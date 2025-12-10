<?php
require 'koneksi.php';

// tampilkan error tapi sembunyikan DEPRECATED
error_reporting(E_ALL & ~E_DEPRECATED);
ini_set('display_errors', 1);

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$editMode = $id > 0;

// nilai default
$id_publikasi = "";
$judul = "";
$jenis = "Buku";
$author = "";
$id_riset = ""; // PERBAIKAN: Menggunakan id_riset, bukan id_kategori
$status = "Aktif";
$error_message = "";

// PERBAIKAN: Ambil daftar BidangRiset (bukan kategori_karya)
$riset_options = [];
$query = "SELECT id_riset, nama_bidang_riset 
          FROM bidangriset 
          ORDER BY nama_bidang_riset";

$res_riset = pg_query($conn, $query);

if ($res_riset) {
    while ($row = pg_fetch_assoc($res_riset)) {
        $riset_options[$row['id_riset']] = $row['nama_bidang_riset'];
    }
}


if ($editMode) {
    // PERBAIKAN: Query disesuaikan dengan struktur tabel publikasi yang sebenarnya
    $res = pg_query($conn, "SELECT judul, jenis, author, id_riset, status FROM publikasi WHERE id_publikasi = $id");

    if ($res && pg_num_rows($res) > 0) {
        $row = pg_fetch_assoc($res);
        $id_publikasi      = $id;
        $judul             = $row['judul'];
        $jenis             = $row['jenis'];
        
        // PERBAIKAN: Author adalah JSONB, decode jika perlu
        $author_raw = $row['author'];
        if (!empty($author_raw)) {
            $author_decoded = json_decode($author_raw, true);
            $author = is_array($author_decoded) ? implode(", ", $author_decoded) : $author_raw;
        }
        
        $id_riset          = $row['id_riset'];
        $status            = ($row['status'] === 't' || $row['status'] === 'true' || $row['status'] === true) ? 'Aktif' : 'Draft';
    } else {
        if (!$res) {
            $error_message = "Error query: " . pg_last_error($conn);
        } else {
            $error_message = "Data Publikasi tidak ditemukan.";
        }
    }
}

// ==== PROSES SIMPAN (TAMBAH / EDIT) ====
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ambil data dari POST dan bersihkan spasi
    $judul_input       = trim($_POST['judul'] ?? '');
    $jenis_input       = trim($_POST['jenis'] ?? '');
    $author_input      = trim($_POST['author'] ?? '');
    $id_riset_input    = (int)($_POST['id_riset'] ?? 0);
    $status_input      = trim($_POST['status'] ?? '');

    // Tetapkan kembali nilai untuk tampilan form jika terjadi error
    $judul             = $judul_input;
    $jenis             = $jenis_input;
    $author            = $author_input;
    $id_riset          = $id_riset_input;
    $status            = $status_input;

    // --- Validasi Sisi Server ---
    $required_fields = [
        'judul' => $judul_input,
        'jenis' => $jenis_input,
        'author' => $author_input,
        'id_riset' => $id_riset_input,
        'status' => $status_input,
    ];

    $missing_fields = [];
    foreach ($required_fields as $field_name => $field_value) {
        if ($field_name === 'id_riset' && ($field_value <= 0 || !array_key_exists($field_value, $riset_options))) {
            $missing_fields[] = 'Bidang Riset';
            continue;
        }

        if (empty($field_value) && $field_name !== 'id_riset') {
            $display_name = match ($field_name) {
                'judul' => 'Judul',
                'jenis' => 'Jenis Publikasi',
                'author' => 'Penulis',
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
        $judul_db      = pg_escape_string($conn, $judul_input);
        $jenis_db      = pg_escape_string($conn, $jenis_input);
        
        // PERBAIKAN: Author dikonversi ke JSONB array
        $authors_array = array_map('trim', explode(',', $author_input));
        $author_json   = json_encode($authors_array);
        $author_db     = pg_escape_string($conn, $author_json);
        
        $status_db     = ($status_input === 'Aktif') ? 't' : 'f';

        if ($editMode) {
            $id_hidden = (int)$_POST['id_publikasi_hidden'];

            $sql = "
              UPDATE publikasi SET
                judul       = '$judul_db',
                jenis       = '$jenis_db',
                author      = '$author_db',
                id_riset    = $id_riset_input,
                status      = '$status_db'
              WHERE id_publikasi = $id_hidden
            ";
        } else {
            $sql = "
              INSERT INTO publikasi (judul, jenis, author, id_riset, status)
              VALUES (
                '$judul_db',
                '$jenis_db',
                '$author_db',
                $id_riset_input,
                '$status_db'
              )
            ";
        }

        $run = pg_query($conn, $sql);

        if (!$run) {
            $error_message = 'QUERY ERROR: ' . pg_last_error($conn);
        } else {
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

        /* FORM */
        .form-container {
            max-width: 640px;
            width: 100%;
            background: #ffffff;
            border-radius: 18px;
            padding: 30px;
            border: 1px solid #e2e7f3;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }

        .form-container h2 {
            font-size: 22px;
            margin-bottom: 22px;
            text-align: center;
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

        /* Tanda wajib isi */
        .form-group label span {
            color: red;
            margin-left: 5px;
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

        /* Tombol Utama */
        .btn-primary {
            background: #1E5AA8;
            color: #ffffff;
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

        .btn-primary.btn-save {
            margin: 20px auto 0 auto;
            display: block;
            padding: 12px 24px;
            font-weight: 600;
        }

        /* Pesan Error */
        .error-message {
            padding: 10px;
            margin-bottom: 20px;
            background-color: #fdd;
            border: 1px solid #f99;
            color: #a00;
            border-radius: 4px;
            font-weight: bold;
        }

        /* Gaya agar select tidak terlalu lebar */
        .form-select.narrow-select {
            width: 50%;
            max-width: 250px;
        }

        /* Helper text */
        .helper-text {
            font-size: 12px;
            color: #6b7280;
            margin-top: 4px;
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
            <a href="index.php" class="btn-primary">
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
                        <input type="hidden" name="id_publikasi_hidden"
                            value="<?php echo (int)$id_publikasi; ?>">
                    <?php endif; ?>

                    <div class="form-group">
                        <label>Judul <span>*</span></label>
                        <input type="text"
                            name="judul"
                            class="form-control"
                            value="<?php echo htmlspecialchars($judul); ?>"
                            placeholder="Masukkan judul publikasi"
                            required>
                    </div>

                    <div class="form-group">
                        <label>Jenis Publikasi <span>*</span></label>
                        <select name="jenis" class="form-select narrow-select" required>
                            <option value="Buku" <?php if ($jenis == 'Buku') echo "selected"; ?>>Buku</option>
                            <option value="Jurnal" <?php if ($jenis == 'Jurnal') echo "selected"; ?>>Jurnal</option>
                            <option value="Prosiding" <?php if ($jenis == 'Prosiding') echo "selected"; ?>>Prosiding</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Penulis <span>*</span></label>
                        <input type="text"
                            name="author"
                            class="form-control"
                            value="<?php echo htmlspecialchars($author); ?>"
                            placeholder="Masukkan nama penulis"
                            required>
                        <div class="helper-text">Pisahkan dengan koma jika lebih dari satu penulis</div>
                    </div>

                    <div class="form-group">
                        <label>Bidang Riset <span>*</span></label>
                        <select name="id_riset" class="form-select narrow-select" required>
                            <option value="">-- Pilih Bidang Riset --</option>
                            <?php foreach ($riset_options as $id_r => $nama_r): ?>
                                <option value="<?php echo $id_r; ?>"
                                    <?php if ($id_riset == $id_r) echo "selected"; ?>>
                                    <?php echo htmlspecialchars($nama_r); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Status <span>*</span></label>
                        <select name="status" class="form-select narrow-select" required>
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