<?php
require 'koneksi.php';

// tampilkan error tapi sembunyikan DEPRECATED
error_reporting(E_ALL & ~E_DEPRECATED);
ini_set('display_errors', 1);

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$editMode = $id > 0;

// nilai default
$id_karya = "";
$judul = "";
$jenis_publikasi = "Buku"; // Default untuk select Jenis Publikasi
$penulis = ""; // Menggantikan uploaded_by
$id_kategori = ""; // Menggantikan id_kategori
$status = "Aktif"; // Default untuk select Status
$error_message = "";

// Ambil daftar kategori_riset (menggantikan kategori_karya)
$kategori_options = [];
// Asumsi nama tabel/kolom yang digunakan untuk 'Kategori Riset' di DB
$query = "SELECT id_kategori, nama_kategori 
          FROM kategori_karya 
          ORDER BY nama_kategori";

$res_kategori = pg_query($conn, $query);

if ($res_kategori) {
    while ($row = pg_fetch_assoc($res_kategori)) {
        $kategori_options[$row['id_kategori']] = $row['nama_kategori'];
    }
}


if ($editMode) {
    // ASUMSI: Nama kolom di DB karya sudah disesuaikan
    $res = pg_query($conn, "SELECT judul, jenis_publikasi, penulis, id_kategori, status FROM karya WHERE id_karya = $id");

    if ($row = pg_fetch_assoc($res)) {
        $judul             = $row['judul'];
        $jenis_publikasi   = $row['jenis_publikasi'];
        $penulis           = $row['penulis'];
        $id_kategori = $row['id_kategori'];
        $status            = ($row['status'] === 't' || $row['status'] === 'Aktif') ? 'Aktif' : 'Draft';
    } else {
        $error_message = "Data Karya tidak ditemukan.";
    }
}

// ==== PROSES SIMPAN (TAMBAH / EDIT) ====
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ambil data dari POST dan bersihkan spasi
    $judul_input          = trim($_POST['judul'] ?? '');
    $jenis_publikasi_input = trim($_POST['jenis_publikasi'] ?? '');
    $penulis_input        = trim($_POST['penulis'] ?? '');
    $id_kategori_input = (int)($_POST['id_kategori'] ?? 0);
    $status_input         = trim($_POST['status'] ?? '');

    // Tetapkan kembali nilai untuk tampilan form jika terjadi error
    $judul             = $judul_input;
    $jenis_publikasi   = $jenis_publikasi_input;
    $penulis           = $penulis_input;
    $id_kategori = $id_kategori_input;
    $status            = $status_input;

    // --- Validasi Sisi Server ---
    $required_fields = [
        'judul' => $judul_input,
        'jenis_publikasi' => $jenis_publikasi_input,
        'penulis' => $penulis_input,
        'id_kategori' => $id_kategori_input,
        'status' => $status_input,
    ];

    $missing_fields = [];
    foreach ($required_fields as $field_name => $field_value) {
        if ($field_name === 'id_kategori' && ($field_value <= 0 || !array_key_exists($field_value, $kategori_options))) {
            $missing_fields[] = 'Kategori Riset';
            continue;
        }

        if (empty($field_value) && $field_name !== 'id_kategori') {
            $display_name = match ($field_name) {
                'judul' => 'Judul',
                'jenis_publikasi' => 'Jenis Publikasi',
                'penulis' => 'Penulis',
                'status' => 'Status',
                default => $field_name,
            };
            $missing_fields[] = $display_name;
        }
    }

    $final_error_message = '';
    if (!empty($missing_fields)) {
        $final_error_message .= "Kolom berikut wajib diisi: **" . implode(', ', $missing_fields) . "**.";
    }

    if (!empty($final_error_message)) {
        $error_message = $final_error_message;
    } else {
        // --- Lanjutkan Proses Penyimpanan ke DB ---
        $judul_db             = pg_escape_string($conn, $judul_input);
        $jenis_publikasi_db   = pg_escape_string($conn, $jenis_publikasi_input);
        $penulis_db           = pg_escape_string($conn, $penulis_input);
        $status_db            = ($status_input === 'Aktif') ? 't' : 'f';

        if ($editMode) {
            $id_hidden = (int)$_POST['id_karya_hidden'];

            $sql = "
              UPDATE karya SET
                judul               = '$judul_db',
                jenis_publikasi     = '$jenis_publikasi_db',
                penulis             = '$penulis_db',
                id_kategori   = $id_kategori_input,
                status              = '$status_db'
              WHERE id_karya = $id_hidden
            ";
        } else {
            $sql = "
              INSERT INTO karya (judul, jenis_publikasi, penulis, id_kategori, status)
              VALUES (
                '$judul_db',
                '$jenis_publikasi_db',
                '$penulis_db',
                $id_kategori_input,
                '$status_db'
              )
            ";
        }

        $run = pg_query($conn, $sql);

        if (!$run) {
            $error_message = 'QUERY ERROR: ' . pg_last_error($conn);
        } else {
            // Ubah redirect ke index.php
            header("Location: index.php");
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title><?php echo $editMode ? "Edit Publikasi" : "Tambah Publikasi"; ?></title>
    <link rel="stylesheet" href="../../../../Assets/Css/Admin/PublikasiAdmin.css">
    <style>
        .error-message {
            padding: 10px;
            margin-bottom: 20px;
            background-color: #fdd;
            border: 1px solid #f99;
            color: #a00;
            border-radius: 4px;
            font-weight: bold;
        }

        .form-group label span {
            color: red;
            margin-left: 5px;
        }

        /* Tambahan CSS khusus untuk form publikasi baru agar select lebih pendek */
        .form-select.narrow-select {
            width: 50%;
            /* Membuat select lebih sempit seperti di gambar */
            max-width: 250px;
        }

        /* Perubahan di sini agar tombol kembali tidak terlihat seperti tombol primer */
        .btn-back-custom {
            margin-bottom: 20px;
            align-self: flex-start;
            border: 1px solid #dde2f2;
            padding: 8px 14px;
            text-decoration: none;
            display: inline-flex;
            font-weight: 500;
        }

        /* RESET & FONT */
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: "Poppins", Arial, sans-serif;
        }

        body {
            background: #F7F9FB;
            display: flex;
            min-height: 100vh;
            color: #111827;
        }

        /* SIDEBAR */
        .sidebar {
            width: 260px;
            background: #e5f0ff;
            min-height: 100vh;
            padding: 24px 18px;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 32px;
        }

        .brand-logo {
            width: 44px;
            height: 44px;
            border-radius: 16px;
            background: linear-gradient(135deg, #0059ff, #00b4ff);
        }

        .brand-text {
            font-weight: 600;
            line-height: 1.3;
            font-size: 14px;
        }

        .brand-text span {
            display: block;
            font-size: 12px;
            color: #657287;
        }

        /* MAIN LAYOUT */
        .title-publikasi {
            font-size: 26px;
            font-weight: 600;
            margin-bottom: 20px;
            text-align: center;
            color: #1E1E1E;
        }

        .main {
            flex: 1;
            padding: 24px 40px 40px 32px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        .centered-content {
            max-width: 640px;
            width: 100%;
        }

        /* header atas */
        .main-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .main-header h1 {
            font-size: 26px;
            font-weight: 600;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 14px;
        }

        .user-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: #d9d9d9;
        }

        /* TOOLBAR / NAVBAR ATAS */

        .toolbar {
            margin-top: 24px;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 32px;
        }

        /* kiri */
        .toolbar-left {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .search-wrapper {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .search-box {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 18px;
            border-radius: 10px;
            background: #f5f5f7;
            min-width: 280px;
            border: 1px solid #e5e7eb;
        }

        .search-icon {
            font-size: 18px;
            opacity: 0.65;
        }

        .search-input {
            border: none;
            outline: none;
            font-size: 14px;
            background: transparent;
            width: 220px;
            color: #111827;
        }

        .search-input::placeholder {
            color: #9ca3af;
        }

        .search-count {
            font-size: 13px;
            color: #6b7280;
        }

        /* filter row */
        .filter-line {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-top: 4px;
        }

        .filter-btn {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            border: 1px solid #e5e7eb;
            background: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-size: 16px;
            color: #4b5563;
        }

        .chip {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 7px 14px;
            border-radius: 10px;
            font-size: 13px;
            border: 1px solid #e5e7eb;
            background: #f9fafb;
        }

        .chip-active {
            background: #f3f4ff;
        }

        .chip-close {
            text-decoration: none;
            font-size: 14px;
            color: #9ca3af;
        }

        .filter-reset {
            font-size: 13px;
            color: #9ca3af;
            text-decoration: none;
        }

        /* kanan */
        .toolbar-right {
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            gap: 6px;
        }

        .toolbar-right-top {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        /* tombol umum */
        .btn {
            border: none;
            border-radius: 10px;
            padding: 9px 16px;
            font-size: 14px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            text-decoration: none;
        }

        .btn-primary {
            background: #1E5AA8;
            color: #ffffff;
        }

        .btn-add {
            padding-inline: 24px;
        }

        .btn-add .icon {
            margin-right: 6px;
        }


        /* export */
        .btn-export {
            border: none;
            background: transparent;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            cursor: pointer;
            font-size: 13px;
            color: #1E5AA8;
        }

        .export-icon {
            width: 27px;
            height: 25px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
        }

        /* sort pill */
        .btn-sort {
            border: none;
            background: #ebebef;
            border-radius: 10px;
            padding: 9px 18px;
            font-size: 13px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            color: #4b5563;
        }

        .sort-icon {
            font-size: 10px;
            opacity: 0.7;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 12px;
            height: 12px;
        }

        .sort-label {
            font-weight: 500;
        }

        /* mini pagination */
        .pagination-mini {
            font-size: 12px;
            color: #6b7280;
            display: flex;
            align-items: center;
            gap: 6px;
            margin-top: 2px;
        }

        .page-arrow {
            border: none;
            background: transparent;
            cursor: pointer;
            font-size: 14px;
            padding: 2px 4px;
            color: #4b5563;
            border-radius: 4px;
        }

        .page-arrow.disabled {
            opacity: 0.35;
            cursor: default;
        }

        /* TABEL */

        .table-wrapper {
            margin-top: 20px;
            background: #ffffff;
            border-radius: 18px;
            border: 1px solid #e2e7f3;
            overflow: hidden;
        }

        /* header row */
        .pub-table {
            width: 100%;
            border-collapse: collapse;
        }

        .pub-table thead {
            background: #e7efff;
        }

        .pub-table th,
        .pub-table td {
            padding: 14px 18px;
            font-size: 13px;
            text-align: left;
            border-bottom: 1px solid #f0f2fa;
            vertical-align: top;
        }

        .pub-table th {
            font-weight: 500;
            color: #4b5563;
        }

        .pub-table tbody tr:hover td {
            background: #f9fbff;
        }

        /* kolom khusus */
        .col-check {
            width: 40px;
            text-align: center;
        }

        .col-judul {
            width: 200px;
        }

        .col-deskripsi {
            width: 280px;
        }

        .col-kategori {
            width: 150px;
        }

        .col-author {
            width: 120px;
        }

        .col-aksi {
            width: 90px;
            text-align: right;
            padding-right: 25px;
        }

        /* Teks no data */
        .no-data {
            text-align: center;
            padding: 30px 0;
            color: #9ca3af;
        }

        /* BADGE JENIS / RISET / STATUS */

        .badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 10px;
            font-size: 11px;
        }

        /* jenis */
        .badge-jenis {
            border: 1px solid #93c5fd;
            color: #1d4ed8;
            background: #eff6ff;
        }

        /* riset */
        .badge-riset {
            border: 1px solid #fdba74;
            color: #c2410c;
            background: #fff7ed;
        }

        /* status */
        .badge-status-aktif {
            border: 1px solid #4ade80;
            color: #15803d;
            background: #ecfdf3;
        }

        .badge-status-draft {
            border: 1px solid #facc15;
            color: #92400e;
            background: #fefce8;
        }

        /* link aksi - tidak terpakai jika menggunakan dropdown */
        .link-aksi {
            font-size: 13px;
            color: #2563eb;
            text-decoration: none;
        }

        .link-aksi:hover {
            text-decoration: underline;
        }

        .aksi-separator {
            margin: 0 3px;
            color: #9ca3af;
        }

        /* FOOTER TABEL */

        .table-footer {
            padding: 12px 18px 16px 18px;
            display: flex;
            justify-content: flex-end;
        }

        .btn-delete {
            color: #4b5563;
            background-color: transparent !important;
            border-radius: 10px;
            padding: 10px 22px;
            font-size: 14px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        /* FORM (untuk tambah_edit.php) */

        .form-container {
            max-width: 640px;
            width: 100%;
            background: #F7F9FB;
            border-radius: 18px;
            padding: 30px;
            box-shadow: 0 12px 10px rgba(0, 0, 0, 0.12),
                0 6px 5px rgba(0, 0, 0, 0.08);
        }

        .form-container h2 {
            font-size: 22px;
            margin-bottom: 22px;
            text-align: center;
            /* Pusat Judul Form */
        }

        .form-group {
            margin-bottom: 16px;
        }

        .form-group label {
            display: block;
            margin-bottom: 6px;
            font-size: 14px;
            font-weight: 500;
        }

        .form-control,
        .form-select {
            width: 100%;
            padding: 10px 14px;
            border-radius: 10px;
            border: 1px solid #d1d5db;
            font-size: 14px;
            outline: none;
            box-shadow:
                0 4px 6px -1px rgba(0, 0, 0, 0.05),
                0 2px 4px -2px rgba(0, 0, 0, 0.03);
            transition: all 0.2s;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #1E5AA8;
            box-shadow: 0 0 0 3px rgba(30, 90, 168, 0.2);
        }

        .btn-primary.btn-save {
            margin: 20px auto 0 auto;
            display: block;
            padding: 12px 24px;
            font-weight: 600;
        }

        /* Style tombol Kembali */
        .btn-back-custom {
            margin-bottom: 20px;
            align-self: flex-start;
            color: #1E5AA8;
            /* Mengganti warna teks agar terlihat seperti tombol navigasi */
            background: #e5f0ff;
            /* Background biru muda */
            border: none;
            padding: 8px 14px;
            text-decoration: none;
            display: inline-flex;
            font-weight: 500;
            border-radius: 10px;
            gap: 4px;
        }

        /* Style Select yang lebih sempit */
        .form-select.narrow-select {
            width: 50%;
            max-width: 250px;
        }

        /* Gaya untuk pesan error bawaan dari PHP */
        .error-message {
            padding: 10px;
            margin-bottom: 20px;
            background-color: #fdd;
            border: 1px solid #f99;
            color: #a00;
            border-radius: 4px;
            font-weight: bold;
        }

        .form-group label span {
            color: red;
            margin-left: 5px;
        }
    </style>
</head>

<body>

    <div class="sidebar">
        <div class="brand">
            <div class="brand-logo"></div>
            <div class="brand-text">
                Laboratorium<br><span>Business Analytics</span>
            </div>
        </div>
    </div>

    <div class="main">
        <div class="centered-content">
            <a href="index.php" class="btn btn-primary">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M15 18l-6-6 6-6" />
                </svg>
                Kembali
            </a>

            <h2 class="title-publikasi"><?php echo $editMode ? "Edit Publikasi" : "Tambah Publikasi"; ?></h2>

            <div class="form-container">

                <?php if (!empty($error_message)): ?>
                    <div class="error-message">
                        <?php echo nl2br(htmlspecialchars($error_message)); ?>
                    </div>
                <?php endif; ?>

                <form method="POST">

                    <?php if ($editMode): ?>
                        <input type="hidden" name="id_karya_hidden"
                            value="<?php echo (int)$id_karya; ?>">
                    <?php endif; ?>

                    <div class="form-group">
                        <label>Judul</label>
                        <input type="text"
                            name="judul"
                            class="form-control"
                            value="<?php echo htmlspecialchars($judul); ?>"
                            placeholder="Masukkan nama judul">
                    </div>

                    <div class="form-group">
                        <label>Jenis Publikasi</label>
                        <select name="jenis_publikasi" class="form-select narrow-select">
                            <option value="Buku" <?php if ($jenis_publikasi == 'Buku') echo "selected"; ?>>Buku</option>
                            <option value="Jurnal" <?php if ($jenis_publikasi == 'Jurnal') echo "selected"; ?>>Jurnal</option>
                            <option value="Prosiding" <?php if ($jenis_publikasi == 'Prosiding') echo "selected"; ?>>Prosiding</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Penulis</label>
                        <input type="text"
                            name="penulis"
                            class="form-control"
                            value="<?php echo htmlspecialchars($penulis); ?>"
                            placeholder="Masukkan nama penulis">
                    </div>

                    <div class="form-group">
                        <label>Kategori Riset</label>
                        <select name="id_kategori" class="form-select narrow-select">
                            <option value="">Fraud ...</option>
                            <?php foreach ($kategori_options as $id_kat => $nama_kat): ?>
                                <option value="<?php echo $id_kat; ?>"
                                    <?php if ($id_kategori == $id_kat) echo "selected"; ?>>
                                    <?php echo htmlspecialchars($nama_kat); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Status</label>
                        <select name="status" class="form-select narrow-select">
                            <option value="Aktif" <?php if ($status == 'Aktif') echo "selected"; ?>>Aktif</option>
                            <option value="Draft" <?php if ($status == 'Draft') echo "selected"; ?>>Draft</option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary btn-save">Simpan</button>
                </form>
            </div>
        </div>
    </div>

</body>

</html>