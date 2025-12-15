<?php
// admin/proses_berita.php
require_once __DIR__ . '../../../Cek_Autentikasi.php';
require_once 'config.php';

// =======================
// Helper redirect
// =======================
function go($to) {
    header('Location: ' . $to);
    exit;
}

// =======================
// Flash notifier (untuk Profile.js)
// =======================
function flash($status, $msg, $redirectAfter = '') {
    $_SESSION['flash_status']   = $status;              // 'success' | 'error'
    $_SESSION['flash_message']  = $msg;                 // pesan
    $_SESSION['flash_redirect'] = $redirectAfter;       // redirect setelah notif (opsional)
}

function success($msg, $showOnPage, $redirectAfter) {
    flash('success', $msg, $redirectAfter);
    go($showOnPage); // balik ke halaman form dulu biar notif tampil
}

function errorFlash($msg, $showOnPage) {
    flash('error', $msg, '');
    go($showOnPage); // balik ke halaman form biar notif tampil (tanpa redirect lanjut)
}

// =======================
// AMBIL ACTION
// =======================
$action = $_POST['action'] ?? '';

// =======================
// Ambil uploader ID dari session (SERVER-SIDE)
// =======================
$uploaded_by = (int)($_SESSION['id_anggota'] ?? 0);
if ($uploaded_by <= 0) {
    // kalau session invalid, notif muncul di berita.php aja
    flash('error', 'Uploader tidak valid (silakan login ulang).', '');
    go('berita.php');
}

// nama author untuk DISPLAY (bukan untuk DB kalau kolom kamu pakai uploaded_by id)
$author_name = $_SESSION['user_name'] ?? ($_SESSION['username'] ?? 'Admin');

// =======================
// TAMBAH BERITA
// =======================
if ($action === 'tambah') {

    $judul   = trim($_POST['judul'] ?? '');
    $isi     = trim($_POST['isi'] ?? '');
    $tanggal = $_POST['tanggal'] ?? date('Y-m-d');
    $status  = $_POST['status'] ?? 'publish';

    // simpan form session biar gak hilang kalau error
    $_SESSION['form_data'] = [
        'judul'   => $judul,
        'isi'     => $isi,
        'tanggal' => $tanggal,
        'status'  => $status,
        'author'  => $author_name,
    ];

    // Validasi
    if ($judul === '' || mb_strlen($judul) < 5) {
        errorFlash('Judul berita minimal 5 karakter!', 'tambah_berita.php');
    }

    if ($isi === '' || mb_strlen($isi) < 20) {
        errorFlash('Isi berita minimal 20 karakter!', 'tambah_berita.php');
    }

    if (!isset($_FILES['gambar']) || $_FILES['gambar']['error'] == UPLOAD_ERR_NO_FILE) {
        errorFlash('Gambar berita harus diupload!', 'tambah_berita.php');
    }

    // Upload gambar
    $gambar_name = '';
    if ($_FILES['gambar']['error'] === UPLOAD_ERR_OK) {
        $upload_result = uploadGambar($_FILES['gambar']); // dari config.php kamu
        if (empty($upload_result['success'])) {
            errorFlash($upload_result['message'] ?? 'Upload gambar gagal.', 'tambah_berita.php');
        }
        $gambar_name = $upload_result['filename'] ?? '';
        if ($gambar_name === '') {
            errorFlash('Upload gambar gagal: nama file kosong.', 'tambah_berita.php');
        }
    } else {
        $error_messages = [
            UPLOAD_ERR_INI_SIZE   => 'Ukuran file melebihi batas maksimal server',
            UPLOAD_ERR_FORM_SIZE  => 'Ukuran file melebihi batas maksimal form',
            UPLOAD_ERR_PARTIAL    => 'File hanya terupload sebagian',
            UPLOAD_ERR_NO_TMP_DIR => 'Folder temporary tidak ditemukan',
            UPLOAD_ERR_CANT_WRITE => 'Gagal menulis file ke disk',
            UPLOAD_ERR_EXTENSION  => 'Upload dihentikan oleh extension'
        ];
        $code = $_FILES['gambar']['error'];
        errorFlash($error_messages[$code] ?? 'Terjadi kesalahan saat upload gambar.', 'tambah_berita.php');
    }

    // Insert DB
    try {
        $stmt = $pdo->prepare("
            INSERT INTO berita (judul, isi, gambar, tanggal, uploaded_by, status)
            VALUES (?, ?, ?, ?, ?, ?)
        ");

        $ok = $stmt->execute([
            $judul,
            $isi,
            $gambar_name,
            $tanggal,
            $uploaded_by,
            $status
        ]);

        if ($ok) {
            unset($_SESSION['form_data']);
            success('Berita berhasil ditambahkan!', 'tambah_berita.php', 'berita.php');
        }

        // kalau execute false tapi ga lempar exception
        if ($gambar_name) {
            hapusGambar($gambar_name);
        }
        errorFlash('Gagal menambahkan berita ke database.', 'tambah_berita.php');

    } catch (Throwable $e) {
        if ($gambar_name) {
            hapusGambar($gambar_name);
        }
        errorFlash('Gagal menambahkan berita: ' . $e->getMessage(), 'tambah_berita.php');
    }
}

// =======================
// EDIT BERITA
// =======================
if ($action === 'edit') {

    $id_berita   = (int)($_POST['id_berita'] ?? 0);
    $judul       = trim($_POST['judul'] ?? '');
    $isi         = trim($_POST['isi'] ?? '');
    $tanggal     = $_POST['tanggal'] ?? date('Y-m-d');
    $status      = $_POST['status'] ?? 'publish';
    $gambar_lama = $_POST['gambar_lama'] ?? '';

    $editPage = 'edit_berita.php?id=' . $id_berita;

    if ($id_berita <= 0) {
        flash('error', 'ID berita tidak valid!', '');
        go('berita.php');
    }

    // simpan form session biar kalau error, pilihan status dll tetap
    $_SESSION['form_data'] = [
        'judul'   => $judul,
        'isi'     => $isi,
        'tanggal' => $tanggal,
        'status'  => $status,
        'author'  => $author_name,
    ];

    // Validasi
    if ($judul === '' || mb_strlen($judul) < 5) {
        errorFlash('Judul berita minimal 5 karakter!', $editPage);
    }

    if ($isi === '' || mb_strlen($isi) < 20) {
        errorFlash('Isi berita minimal 20 karakter!', $editPage);
    }

    if (empty($tanggal)) {
        errorFlash('Tanggal terbit harus diisi!', $editPage);
    }

    // Validasi gambar: kalau tidak ada gambar lama dan tidak upload baru
    $noNewFile = (!isset($_FILES['gambar']) || $_FILES['gambar']['error'] == UPLOAD_ERR_NO_FILE);
    if ($noNewFile && empty($gambar_lama)) {
        errorFlash('Gambar berita harus diupload! (belum ada gambar sebelumnya)', $editPage);
    }

    // Upload gambar baru jika ada
    $gambar_name = $gambar_lama;

    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === UPLOAD_ERR_OK) {
        $upload_result = uploadGambar($_FILES['gambar']);
        if (empty($upload_result['success'])) {
            errorFlash($upload_result['message'] ?? 'Upload gambar gagal.', $editPage);
        }

        // hapus gambar lama setelah upload baru sukses
        if ($gambar_lama) {
            hapusGambar($gambar_lama);
        }

        $gambar_name = $upload_result['filename'] ?? $gambar_lama;

    } elseif (isset($_FILES['gambar']) && $_FILES['gambar']['error'] !== UPLOAD_ERR_NO_FILE) {
        $error_messages = [
            UPLOAD_ERR_INI_SIZE   => 'Ukuran file melebihi batas maksimal server',
            UPLOAD_ERR_FORM_SIZE  => 'Ukuran file melebihi batas maksimal form',
            UPLOAD_ERR_PARTIAL    => 'File hanya terupload sebagian',
            UPLOAD_ERR_NO_TMP_DIR => 'Folder temporary tidak ditemukan',
            UPLOAD_ERR_CANT_WRITE => 'Gagal menulis file ke disk',
            UPLOAD_ERR_EXTENSION  => 'Upload dihentikan oleh extension'
        ];
        $code = $_FILES['gambar']['error'];
        errorFlash($error_messages[$code] ?? 'Terjadi kesalahan saat upload gambar.', $editPage);
    }

    // Update DB
    try {
        $stmt = $pdo->prepare("
            UPDATE berita SET
                judul = ?,
                isi = ?,
                gambar = ?,
                tanggal = ?,
                uploaded_by = ?,
                status = ?
            WHERE id_berita = ?
        ");

        $ok = $stmt->execute([
            $judul,
            $isi,
            $gambar_name,
            $tanggal,
            $uploaded_by,
            $status,
            $id_berita
        ]);

        if ($ok) {
            unset($_SESSION['form_data']);
            success('Berita berhasil diperbarui!', $editPage, 'berita.php');
        }

        errorFlash('Gagal memperbarui berita di database.', $editPage);

    } catch (Throwable $e) {
        errorFlash('Gagal memperbarui berita: ' . $e->getMessage(), $editPage);
    }
}

// =======================
// ACTION TIDAK VALID
// =======================
flash('error', 'Aksi tidak valid!', '');
go('berita.php');
