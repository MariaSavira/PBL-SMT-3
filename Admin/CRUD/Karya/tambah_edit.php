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
$deskripsi = "";
$link = "";
$id_kategori = ""; // ID Kategori
$uploaded_by = "4"; // Nilai default hardcode untuk hidden field
$error_message = "";

// Ambil daftar kategori_karya dari database
$kategori_options = [];
$res_kategori = pg_query($conn, "SELECT id_kategori, nama_kategori FROM kategori_karya ORDER BY nama_kategori");

// Tambahkan pengecekan error untuk res_kategori (pencegahan Fatal Error)
if ($res_kategori) {
    while ($row = pg_fetch_assoc($res_kategori)) {
        $kategori_options[$row['id_kategori']] = $row['nama_kategori'];
    }
}


if ($editMode) {
    $res = pg_query($conn, "SELECT judul, deskripsi, link, id_kategori, uploaded_by FROM karya WHERE id_karya = $id");

    if ($row = pg_fetch_assoc($res)) {
        $id_karya          = $id; // Set ID karya yang sedang diedit
        $judul             = $row['judul'];
        $deskripsi         = $row['deskripsi'];
        $link              = $row['link'];
        $id_kategori       = $row['id_kategori'];
        $uploaded_by       = $row['uploaded_by'];
    } else {
        $error_message = "Data Karya tidak ditemukan.";
    }
}

// ==== PROSES SIMPAN (TAMBAH / EDIT) ====
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ambil data dari POST dan bersihkan spasi
    $judul_input          = trim($_POST['judul'] ?? '');
    $deskripsi_input      = trim($_POST['deskripsi'] ?? '');
    $link_input           = trim($_POST['link'] ?? '');
    $id_kategori_input    = (int)($_POST['id_kategori'] ?? 0);
    $uploaded_by_input    = trim($_POST['uploaded_by'] ?? '4'); // Pastikan nilai default jika kosong

    // Tetapkan kembali nilai untuk tampilan form jika terjadi error
    $judul             = $judul_input;
    $deskripsi         = $deskripsi_input;
    $link              = $link_input;
    $id_kategori       = $id_kategori_input;
    $uploaded_by       = $uploaded_by_input;

    // --- Validasi Sisi Server ---
    $required_fields = [
        'judul' => $judul_input,
        'deskripsi' => $deskripsi_input,
        'id_kategori' => $id_kategori_input,
        // 'uploaded_by' => $uploaded_by_input, // Asumsi ini selalu terisi (hidden)
    ];

    $missing_fields = [];
    foreach ($required_fields as $field_name => $field_value) {
        // Cek ID Kategori khusus, harus lebih dari 0 dan ada di options
        if ($field_name === 'id_kategori' && ($field_value <= 0 || !array_key_exists($field_value, $kategori_options))) {
            $missing_fields[] = 'Kategori';
            continue;
        }

        if (empty($field_value) && $field_name !== 'id_kategori') {
            // Mapping nama field ke label yang lebih mudah dibaca
            $display_name = match ($field_name) {
                'judul' => 'Judul',
                'deskripsi' => 'Deskripsi',
                'uploaded_by' => 'Penulis',
                default => $field_name,
            };
            $missing_fields[] = $display_name;
        }
    }

    // Gabungkan semua pesan error
    $final_error_message = '';
    if (!empty($missing_fields)) {
        $final_error_message .= "Kolom berikut wajib diisi: **" . implode(', ', $missing_fields) . "**.";
    }

    if (!empty($final_error_message)) {
        $error_message = $final_error_message;
    } else {
        // --- Lanjutkan Proses Penyimpanan ke DB (jika validasi lolos) ---

        $judul_db      = pg_escape_string($conn, $judul_input);
        $deskripsi_db  = pg_escape_string($conn, $deskripsi_input);

        // Link bisa NULL, jadi perlu perlakuan khusus
        $link_db       = empty($link_input) ? 'NULL' : "'" . pg_escape_string($conn, $link_input) . "'";

        if ($editMode) {
            $id_hidden = (int)$_POST['id_karya_hidden'];

            $sql = "
              UPDATE karya SET
                judul               = '$judul_db',
                deskripsi           = '$deskripsi_db',
                link                = $link_db,
                id_kategori         = $id_kategori_input,
                uploaded_by         = '$uploaded_by_input' 
              WHERE id_karya = $id_hidden
            ";
        } else {
            // INSERT
            $sql = "
              INSERT INTO karya (judul, deskripsi, link, id_kategori, uploaded_by)
              VALUES (
                '$judul_db',
                '$deskripsi_db',
                $link_db,
                $id_kategori_input,
                '$uploaded_by_input'
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
    <title><?php echo $editMode ? "Edit Karya" : "Tambah Karya"; ?></title>
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

        /* Tombol Kembali (Secondary Style) - Perubahan di sini */
        .btn-back-custom {
            margin-bottom: 20px;
            align-self: flex-start;
            color: #1E5AA8;
            background: #e5f0ff;
            border: none;
            padding: 8px 14px;
            text-decoration: none;
            display: inline-flex;
            font-weight: 500;
            border-radius: 10px;
            gap: 4px;
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

            <h2 class="title-publikasi"><?php echo $editMode ? "Edit Karya" : "Tambah Karya"; ?></h2>

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
                        <label>Judul <span>*</span></label>
                        <input type="text"
                            name="judul"
                            class="form-control"
                            value="<?php echo htmlspecialchars($judul); ?>"
                            placeholder="Masukkan nama judul"
                            required>
                    </div>

                    <div class="form-group">
                        <label>Deskripsi <span>*</span></label>
                        <textarea
                            name="deskripsi"
                            class="form-control"
                            rows="4"
                            placeholder="Masukkan deskripsi"
                            required><?php echo htmlspecialchars($deskripsi); ?></textarea>
                    </div>

                    <div class="form-group">
                        <label>Kategori <span>*</span></label>
                        <select name="id_kategori" class="form-select narrow-select" required>
                            <option value="">-- Pilih Kategori Karya --</option>
                            <?php foreach ($kategori_options as $id_kat => $nama_kat): ?>
                                <option value="<?php echo $id_kat; ?>"
                                    <?php if ($id_kategori == $id_kat) echo "selected"; ?>>
                                    <?php echo htmlspecialchars($nama_kat); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <input type="hidden" name="uploaded_by" value="<?php echo htmlspecialchars($uploaded_by); ?>">

                    <div class="form-group">
                        <label>Link (Opsional)</label>
                        <input type="url"
                            name="link"
                            class="form-control"
                            value="<?php echo htmlspecialchars($link); ?>"
                            placeholder="Masukkan URL jika ada">
                    </div>

                    <button type="submit" class="btn btn-primary btn-save">Simpan</button>
                </form>
            </div>
        </div>
    </div>

</body>

</html>