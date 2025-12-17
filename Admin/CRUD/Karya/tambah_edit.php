<?php
require_once __DIR__ . '../../../Cek_Autentikasi.php';
require 'koneksi.php';

error_reporting(E_ALL & ~E_DEPRECATED);
ini_set('display_errors', 1);

$status      = '';
$message     = '';
$redirectTo  = 'IndexKarya.php';

$id       = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$editMode = $id > 0;

$id_karya       = "";
$judul          = "";
$deskripsi      = "";
$link           = "";
$error_message  = "";

$uploaded_by = $_SESSION['id_anggota']
    ?? $_SESSION['user_id']
    ?? $_SESSION['uploaded_by']
    ?? "4";

if ($editMode) {
    $sql = "
        SELECT id_karya, judul, deskripsi, link, uploaded_by
        FROM karya
        WHERE id_karya = $1
    ";
    $res = pg_query_params($conn, $sql, [$id]);

    if ($res && pg_num_rows($res) > 0) {
        $row = pg_fetch_assoc($res);

        $id_karya     = (int)$row['id_karya'];
        $judul        = $row['judul'] ?? '';
        $deskripsi    = $row['deskripsi'] ?? '';
        $link         = $row['link'] ?? '';
        $uploaded_by  = $row['uploaded_by'] ?? $uploaded_by;

    } else {
        $error_message = "Data karya tidak ditemukan.";
        $status  = 'error';
        $message = $error_message;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $judul_input     = trim($_POST['judul'] ?? '');
    $deskripsi_input = trim($_POST['deskripsi'] ?? '');
    $link_input      = trim($_POST['link'] ?? '');
    $uploaded_input  = trim($_POST['uploaded_by'] ?? $uploaded_by);

    $judul       = $judul_input;
    $deskripsi   = $deskripsi_input;
    $link        = $link_input;
    $uploaded_by = $uploaded_input;

    $missing = [];
    if ($judul_input === "")     $missing[] = "Judul";
    if ($deskripsi_input === "") $missing[] = "Deskripsi";

    if (!empty($missing)) {
        $error_message = "Kolom wajib diisi: " . implode(', ', $missing);
        $status  = 'error';
        $message = $error_message;

    } else {
        $link_db = ($link_input === '') ? null : $link_input;

        if ($editMode) {
            $id_hidden = (int)($_POST['id_karya_hidden'] ?? 0);

            $sql = "
                UPDATE karya SET
                    judul      = $1,
                    deskripsi  = $2,
                    link       = $3,
                    uploaded_by= $4
                WHERE id_karya = $5
            ";

            $params = [
                $judul_input,
                $deskripsi_input,
                $link_db,
                $uploaded_input,
                $id_hidden
            ];

            $run = pg_query_params($conn, $sql, $params);

            if ($run) {
                $status  = 'success';
                $message = 'Karya berhasil diperbarui.';
            } else {
                $error_message = "QUERY ERROR: " . pg_last_error($conn);
                $status  = 'error';
                $message = 'Gagal menyimpan perubahan karya.';
            }

        } else {
            $sql = "
                INSERT INTO karya (judul, deskripsi, link, uploaded_by)
                VALUES ($1, $2, $3, $4)
            ";

            $params = [
                $judul_input,
                $deskripsi_input,
                $link_db,
                $uploaded_input
            ];

            $run = pg_query_params($conn, $sql, $params);

            if ($run) {
                $status  = 'success';
                $message = 'Karya baru berhasil ditambahkan.';
            } else {
                $error_message = "QUERY ERROR: " . pg_last_error($conn);
                $status  = 'error';
                $message = 'Gagal menyimpan karya baru.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $editMode ? "Edit Karya" : "Tambah Karya"; ?></title>
    
    <link rel="stylesheet" href="/PBL-SMT-3/Assets/Css/Admin/FormPublikasi.css">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900&display=swap"
        rel="stylesheet"
    >
    <link rel="icon" type="images/x-icon"
          href="../../../Assets/Image/Logo/Logo Without Text.png" />
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body>
    <div id="sidebar"></div>

    <main class="content" id="content">
        <div id="header"></div>

        <div class="content-header page-header">
            <a href="IndexKarya.php">
                <button type="button" class="btn-back">
                    <i class="fa-solid fa-chevron-left"></i>
                    Kembali
                </button>
            </a>

            <h1 class="page-title"><?= $editMode ? "Edit Karya" : "Tambah Karya"; ?></h1>
            <div></div>
        </div>

        <section class="profile-layout">
            <form class="profile-form" method="POST" action="">
                <?php if ($editMode): ?>
                    <input type="hidden" name="id_karya_hidden" value="<?= (int)$id_karya; ?>">
                <?php endif; ?>

                <input type="hidden" name="uploaded_by" value="<?= htmlspecialchars($uploaded_by); ?>">

                <div class="form-card">
                    <h2 class="form-title">Data Karya</h2>
                    <p class="form-subtitle">
                        Lengkapi informasi mengenai karya berikut.
                    </p>

                    <div class="form-grid">
                        
                        <div class="field-group">
                            <label for="judul">Judul <span class="required">*</span></label>
                            <input
                                type="text"
                                id="judul"
                                name="judul"
                                class="field-input"
                                value="<?= htmlspecialchars($judul) ?>"
                                placeholder="Masukkan judul karya">
                        </div>

                        <div class="field-group">
                            <label for="deskripsi">Deskripsi <span class="required">*</span></label>
                            <textarea
                                id="deskripsi"
                                name="deskripsi"
                                class="field-input"
                                rows="4"
                                placeholder="Masukkan deskripsi karya"><?= htmlspecialchars($deskripsi) ?></textarea>
                        </div>

                        <div class="field-group">
                            <label for="link">Link (Opsional)</label>
                            <input
                                type="url"
                                id="link"
                                name="link"
                                class="field-input"
                                placeholder="https://contoh.com/karya"
                                value="<?= htmlspecialchars($link) ?>">
                            <small class="helper-text">Boleh dikosongi jika tidak ada link.</small>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn-primary btn-save">
                            Simpan Karya
                            <i class="fa-solid fa-check"></i>
                        </button>
                    </div>
                </div>
            </form>
        </section>
    </main>
    
    <div id="notification" class="notification" style="display:none;">
        <div class="notification-content">
            <div class="notification-icon" id="notification-icon"></div>
            <div class="notification-text">
                <div class="notification-title" id="notification-title"></div>
                <div class="notification-message" id="notification-message"></div>
            </div>
            <button id="closeNotification" class="close-btn">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
    </div>
    <div id="overlay" class="overlay" style="display:none;"></div>

    <script src="../../../Assets/Javascript/Admin/Sidebar.js"></script>
    <script src="../../../Assets/Javascript/Admin/Header.js"></script>

    <script>
        window.profileStatus       = <?= json_encode($status  ?? '') ?>;
        window.profileMessage      = <?= json_encode($message ?? '') ?>;
        window.profileRedirectUrl  = <?= json_encode($redirectTo) ?>;
    </script>
    <script src="../../../Assets/Javascript/Admin/Profile.js"></script>
</body>
</html>
