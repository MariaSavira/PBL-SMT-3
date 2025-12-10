<?php
// admin/proses_berita.php
session_start();
require_once 'config.php';

$action = $_POST['action'] ?? '';

if ($action == 'tambah') {
    // Tambah Berita Baru
    $judul = trim($_POST['judul'] ?? '');
    $isi = trim($_POST['isi'] ?? '');
    $tanggal = $_POST['tanggal'] ?? date('Y-m-d');
    $author = trim($_POST['author'] ?? '');
    $uploaded_by = $author ?: ($_POST['uploaded_by'] ?? 'Admin');
    $status = $_POST['status'] ?? 'publish';
    
    // Simpan form data ke session untuk retain values
    $_SESSION['form_data'] = [
        'judul' => $judul,
        'isi' => $isi,
        'tanggal' => $tanggal,
        'author' => $author,
        'status' => $status
    ];
    
    // Validasi input
    if (empty($judul)) {
        $_SESSION['error'] = 'Judul berita harus diisi!';
        header('Location: tambah_berita.php');
        exit;
    }
    
    if (strlen($judul) < 5) {
        $_SESSION['error'] = 'Judul berita minimal 5 karakter!';
        header('Location: tambah_berita.php');
        exit;
    }
    
    if (empty($isi)) {
        $_SESSION['error'] = 'Isi berita harus diisi!';
        header('Location: tambah_berita.php');
        exit;
    }
    
    if (strlen($isi) < 20) {
        $_SESSION['error'] = 'Isi berita minimal 20 karakter!';
        header('Location: tambah_berita.php');
        exit;
    }
    
    if (empty($tanggal)) {
        $_SESSION['error'] = 'Tanggal terbit harus diisi!';
        header('Location: tambah_berita.php');
        exit;
    }
    
    // TAMBAHKAN VALIDASI GAMBAR WAJIB - CEK APAKAH FILE ADA
    if (!isset($_FILES['gambar']) || $_FILES['gambar']['error'] == 4) {
        $_SESSION['error'] = 'Gambar berita harus diupload!';
        header('Location: tambah_berita.php');
        exit;
    }
    
    // Upload gambar
    $gambar_name = '';
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] == 0) {
        $upload_result = uploadGambar($_FILES['gambar']);
        if ($upload_result['success']) {
            $gambar_name = $upload_result['filename'];
        } else {
            $_SESSION['error'] = $upload_result['message'];
            header('Location: tambah_berita.php');
            exit;
        }
    } else {
        // Handle upload errors
        $error_messages = [
            1 => 'Ukuran file melebihi batas maksimal server',
            2 => 'Ukuran file melebihi batas maksimal form',
            3 => 'File hanya terupload sebagian',
            4 => 'Tidak ada file yang diupload',
            6 => 'Folder temporary tidak ditemukan',
            7 => 'Gagal menulis file ke disk',
            8 => 'Upload dihentikan oleh extension'
        ];
        
        $error_code = $_FILES['gambar']['error'];
        $_SESSION['error'] = $error_messages[$error_code] ?? 'Terjadi kesalahan saat upload gambar';
        header('Location: tambah_berita.php');
        exit;
    }
    
    try {
        $stmt = $pdo->prepare("INSERT INTO berita (judul, isi, gambar, tanggal, uploaded_by, status) 
                               VALUES (?, ?, ?, ?, ?, ?)");
        $result = $stmt->execute([$judul, $isi, $gambar_name, $tanggal, $uploaded_by, $status]);
        
        if ($result) {
            // Clear form data on success
            unset($_SESSION['form_data']);
            $_SESSION['success'] = 'Berita berhasil ditambahkan!';
            header('Location: berita.php');
        } else {
            $_SESSION['error'] = 'Gagal menambahkan berita ke database.';
            header('Location: tambah_berita.php');
        }
        exit;
    } catch (PDOException $e) {
        $_SESSION['error'] = 'Gagal menambahkan berita: ' . $e->getMessage();
        
        if ($gambar_name) {
            hapusGambar($gambar_name);
        }
        
        header('Location: tambah_berita.php');
        exit;
    }
    
} elseif ($action == 'edit') {
    // Edit Berita
    $id_berita = intval($_POST['id_berita'] ?? 0);
    $judul = trim($_POST['judul'] ?? '');
    $isi = trim($_POST['isi'] ?? '');
    $tanggal = $_POST['tanggal'] ?? date('Y-m-d');
    $author = trim($_POST['author'] ?? '');
    $status = $_POST['status'] ?? 'publish';
    $gambar_lama = $_POST['gambar_lama'] ?? '';
    
    // Validasi input
    if ($id_berita <= 0) {
        $_SESSION['error'] = 'ID berita tidak valid!';
        header('Location: berita.php');
        exit;
    }
    
    if (empty($judul)) {
        $_SESSION['error'] = 'Judul berita harus diisi!';
        header('Location: edit_berita.php?id=' . $id_berita);
        exit;
    }
    
    if (strlen($judul) < 5) {
        $_SESSION['error'] = 'Judul berita minimal 5 karakter!';
        header('Location: edit_berita.php?id=' . $id_berita);
        exit;
    }
    
    if (empty($isi)) {
        $_SESSION['error'] = 'Isi berita harus diisi!';
        header('Location: edit_berita.php?id=' . $id_berita);
        exit;
    }
    
    if (strlen($isi) < 20) {
        $_SESSION['error'] = 'Isi berita minimal 20 karakter!';
        header('Location: edit_berita.php?id=' . $id_berita);
        exit;
    }
    
    if (empty($tanggal)) {
        $_SESSION['error'] = 'Tanggal terbit harus diisi!';
        header('Location: edit_berita.php?id=' . $id_berita);
        exit;
    }
    
    // VALIDASI GAMBAR UNTUK EDIT - Jika tidak ada gambar lama dan tidak upload baru
    if (empty($gambar_lama) && (!isset($_FILES['gambar']) || $_FILES['gambar']['error'] == 4)) {
        $_SESSION['error'] = 'Gambar berita harus diupload!';
        header('Location: edit_berita.php?id=' . $id_berita);
        exit;
    }
    
    // Upload gambar baru jika ada
    $gambar_name = $gambar_lama;
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] == 0) {
        $upload_result = uploadGambar($_FILES['gambar']);
        if ($upload_result['success']) {
            if ($gambar_lama) {
                hapusGambar($gambar_lama);
            }
            $gambar_name = $upload_result['filename'];
        } else {
            $_SESSION['error'] = $upload_result['message'];
            header('Location: edit_berita.php?id=' . $id_berita);
            exit;
        }
    } elseif (isset($_FILES['gambar']) && $_FILES['gambar']['error'] != 4) {
        // Handle upload errors (selain "no file uploaded")
        $error_messages = [
            1 => 'Ukuran file melebihi batas maksimal server',
            2 => 'Ukuran file melebihi batas maksimal form',
            3 => 'File hanya terupload sebagian',
            6 => 'Folder temporary tidak ditemukan',
            7 => 'Gagal menulis file ke disk',
            8 => 'Upload dihentikan oleh extension'
        ];
        
        $error_code = $_FILES['gambar']['error'];
        $_SESSION['error'] = $error_messages[$error_code] ?? 'Terjadi kesalahan saat upload gambar';
        header('Location: edit_berita.php?id=' . $id_berita);
        exit;
    }
    
    try {
        $stmt = $pdo->prepare("UPDATE berita SET 
                               judul = ?, 
                               isi = ?, 
                               gambar = ?, 
                               tanggal = ?, 
                               uploaded_by = ?,
                               status = ?
                               WHERE id_berita = ?");
        $result = $stmt->execute([$judul, $isi, $gambar_name, $tanggal, $author, $status, $id_berita]);
        
        if ($result) {
            $_SESSION['success'] = 'Berita berhasil diperbarui!';
            header('Location: berita.php');
        } else {
            $_SESSION['error'] = 'Gagal memperbarui berita di database.';
            header('Location: edit_berita.php?id=' . $id_berita);
        }
        exit;
    } catch (PDOException $e) {
        $_SESSION['error'] = 'Gagal memperbarui berita: ' . $e->getMessage();
        header('Location: edit_berita.php?id=' . $id_berita);
        exit;
    }
    
} else {
    $_SESSION['error'] = 'Aksi tidak valid!';
    header('Location: berita.php');
    exit;
}
?>