<?php
    require_once __DIR__ . '../../../Cek_Autentikasi.php';
    require_once __DIR__ . '../../../Koneksi/KoneksiSasa.php';

    $conn = get_pg_connection();

    // ---------- STATUS UNTUK NOTIF ----------
    $status      = '';
    $message     = '';
    $redirectTo  = 'IndexPublikasi.php';

    // ---------- MODE EDIT / TAMBAH ----------
    $id       = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    $editMode = $id > 0;

    // ---------- NILAI DEFAULT ----------
    $id_publikasi   = "";
    $judul          = "";
    $jenis          = "Buku";
    $abstrak        = "";
    $link           = "";
    $tanggal_terbit = "";
    $author         = "";
    $id_riset       = "";
    $statusPublik   = "Aktif";   // label untuk UI
    $error_message  = "";

    // ---------- OPTIONS BIDANG RISET ----------
    $riset_options = [];
    $res_riset = pg_query($conn, "SELECT id_riset, nama_bidang_riset FROM bidangriset ORDER BY nama_bidang_riset");

    if ($res_riset) {
        while ($row = pg_fetch_assoc($res_riset)) {
            $riset_options[$row['id_riset']] = $row['nama_bidang_riset'];
        }
    }

    // ---------- LOAD DATA SAAT EDIT ----------
    if ($editMode) {
        $sql = "
            SELECT judul, jenis, abstrak, link, tanggal_terbit, author, id_riset, status 
            FROM publikasi 
            WHERE id_publikasi = $1
        ";
        $res = pg_query_params($conn, $sql, [$id]);

        if ($res && pg_num_rows($res) > 0) {
            $row = pg_fetch_assoc($res);

            $id_publikasi   = $id;
            $judul          = $row['judul'];
            $jenis          = $row['jenis'];
            $abstrak        = $row['abstrak'];
            $link           = $row['link'];
            $tanggal_terbit = $row['tanggal_terbit'];

            // author dalam DB disimpan JSON
            $author_raw = $row['author'];
            if ($author_raw) {
                $decoded = json_decode($author_raw, true);
                $author  = is_array($decoded) ? implode(', ', $decoded) : $author_raw;
            }

            $id_riset     = $row['id_riset'];
            $statusPublik = ($row['status'] === 't' ? "Aktif" : "Draft");

        } else {
            $error_message = "Data publikasi tidak ditemukan.";
        }
    }

    // ---------- HANDLE SUBMIT ----------
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        $judul_input    = trim($_POST['judul'] ?? '');
        $jenis_input    = trim($_POST['jenis'] ?? '');
        $abstrak_input  = trim($_POST['abstrak'] ?? '');
        $link_input     = trim($_POST['link'] ?? '');
        $tanggal_input  = $_POST['tanggal_terbit'] ?? null;
        $author_input   = trim($_POST['author'] ?? '');
        $id_riset_input = (int)($_POST['id_riset'] ?? 0);
        $status_input   = "Aktif";   // sementara status dikunci Aktif

        // simpan balik ke variabel form (biar sticky)
        $judul          = $judul_input;
        $jenis          = $jenis_input;
        $abstrak        = $abstrak_input;
        $link           = $link_input;
        $tanggal_terbit = $tanggal_input;
        $author         = $author_input;
        $id_riset       = $id_riset_input;
        $statusPublik   = $status_input;

        // -------- VALIDASI WAJIB DIISI --------
        $missing = [];

        if ($judul_input === "")      $missing[] = "Judul";
        if ($jenis_input === "")      $missing[] = "Jenis Publikasi";
        if ($author_input === "")     $missing[] = "Penulis";
        if ($id_riset_input <= 0)     $missing[] = "Bidang Riset";

        if (!empty($missing)) {
            $error_message = "Kolom wajib diisi: " . implode(', ', $missing);
            $status        = 'error';
            $message       = $error_message;

        } else {
            // ubah author ke JSON array
            $author_json = json_encode(array_map("trim", explode(",", $author_input)));

            // mapping status ke boolean
            $status_db = ($status_input === "Aktif" ? 't' : 'f');

            if ($editMode) {
                // -------- UPDATE --------
                $sql = "
                    UPDATE publikasi SET
                        judul          = $1,
                        jenis          = $2,
                        abstrak        = $3,
                        link           = $4,
                        tanggal_terbit = $5,
                        author         = $6,
                        id_riset       = $7,
                        status         = $8
                    WHERE id_publikasi = $9
                ";

                $params = [
                    $judul_input,
                    $jenis_input,
                    $abstrak_input,
                    $link_input,
                    $tanggal_input,
                    $author_json,
                    $id_riset_input,
                    $status_db,
                    $id_publikasi
                ];

                $run = pg_query_params($conn, $sql, $params);

                if ($run) {
                    $status  = 'success';
                    $message = 'Publikasi berhasil diperbarui.';
                } else {
                    $error_message = "QUERY ERROR: " . pg_last_error($conn);
                    $status        = 'error';
                    $message       = 'Gagal menyimpan perubahan publikasi.';
                }

            } else {
                // -------- INSERT --------
                $sql = "
                    INSERT INTO publikasi 
                        (judul, jenis, abstrak, link, tanggal_terbit, author, id_riset, status)
                    VALUES ($1,$2,$3,$4,$5,$6,$7,$8)
                ";

                $params = [
                    $judul_input,
                    $jenis_input,
                    $abstrak_input,
                    $link_input,
                    $tanggal_input,
                    $author_json,
                    $id_riset_input,
                    $status_db
                ];

                $run = pg_query_params($conn, $sql, $params);

                if ($run) {
                    $status  = 'success';
                    $message = 'Publikasi baru berhasil ditambahkan.';
                    // kalau mau clear form setelah sukses:
                    // $judul = $jenis = $abstrak = $link = $author = "";
                    // $tanggal_terbit = "";
                    // $id_riset = "";
                } else {
                    $error_message = "QUERY ERROR: " . pg_last_error($conn);
                    $status        = 'error';
                    $message       = 'Gagal menyimpan publikasi baru.';
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
    <title><?= $editMode ? "Edit Publikasi" : "Tambah Publikasi"; ?></title>

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
            <a href="IndexPublikasi.php">
                <button type="button" class="btn-back">
                    <i class="fa-solid fa-chevron-left"></i>
                    Kembali
                </button>
            </a>

            <h1 class="page-title"><?= $editMode ? "Edit Publikasi" : "Tambah Publikasi"; ?></h1>
            <div></div>
        </div>

        <section class="profile-layout">

            <form class="profile-form" method="POST" action="">
                <?php if ($editMode): ?>
                    <input type="hidden" name="id_publikasi_hidden" value="<?= (int)$id_publikasi; ?>">
                <?php endif; ?>

                <div class="form-card">
                    <h2 class="form-title">Data Publikasi</h2>
                    <p class="form-subtitle">
                        Lengkapi informasi mengenai publikasi berikut.
                    </p>

                    <div class="form-grid">
                        <!-- JUDUL -->
                        <div class="field-group">
                            <label for="judul">Judul <span class="required">*</span></label>
                            <input
                                type="text"
                                id="judul"
                                name="judul"
                                class="field-input"
                                value="<?= htmlspecialchars($judul) ?>"
                                placeholder="Masukkan judul publikasi">
                        </div>

                        <!-- JENIS PUBLIKASI -->
                        <div class="field-group">
                            <label for="jenis">Jenis Publikasi <span class="required">*</span></label>

                            <div class="field-select" data-placeholder="Pilih Jenis Publikasi">
                                <button type="button" class="dropdown-toggle" aria-haspopup="listbox" aria-expanded="false">
                                    <span class="dropdown-label">
                                        <?= $jenis ? htmlspecialchars($jenis) : 'Pilih Jenis Publikasi'; ?>
                                    </span>
                                    <i class="fa-solid fa-chevron-down caret"></i>
                                </button>

                                <div class="dropdown-menu" role="listbox">
                                    <button type="button" class="dropdown-item" data-value="Buku">Buku</button>
                                    <button type="button" class="dropdown-item" data-value="Jurnal">Jurnal</button>
                                    <button type="button" class="dropdown-item" data-value="Prosiding">Prosiding</button>
                                    <button type="button" class="dropdown-item" data-value="Artikel Ilmiah">Artikel Ilmiah</button>
                                    <button type="button" class="dropdown-item" data-value="HKI">HKI</button>
                                </div>

                                <input type="hidden" name="jenis" id="jenis_hidden" value="<?= htmlspecialchars($jenis) ?>">
                            </div>
                        </div>

                        <!-- PENULIS -->
                        <div class="field-group">
                            <label for="author">Penulis <span class="required">*</span></label>
                            <input
                                type="text"
                                id="author"
                                name="author"
                                class="field-input"
                                value="<?= htmlspecialchars($author) ?>"
                                placeholder="Masukkan nama penulis">
                            <small class="helper-text">Pisahkan dengan koma jika lebih dari satu penulis</small>
                        </div>

                        <!-- ABSTRAK -->
                        <div class="field-group">
                            <label for="abstrak">Abstrak</label>
                            <textarea
                                name="abstrak"
                                class="field-input"
                                rows="4"
                                placeholder="Masukkan abstrak publikasi"><?= htmlspecialchars($abstrak) ?></textarea>
                        </div>

                        <!-- LINK PUBLIKASI -->
                        <div class="field-group">
                            <label for="link">Link Publikasi</label>
                            <input
                                type="url"
                                name="link"
                                class="field-input"
                                placeholder="https://contoh.com/publikasi"
                                value="<?= htmlspecialchars($link) ?>">
                        </div>

                        <!-- TANGGAL TERBIT -->
                        <div class="field-group">
                            <label for="tanggal_terbit">Tanggal Terbit</label>
                            <input
                                type="date"
                                name="tanggal_terbit"
                                class="field-input"
                                value="<?= htmlspecialchars($tanggal_terbit) ?>">
                        </div>

                        <!-- BIDANG RISET -->
                        <div class="field-group">
                            <label for="id_riset">Bidang Riset <span class="required">*</span></label>

                            <div class="field-select" data-placeholder="Pilih Bidang Riset">
                                <button type="button" class="dropdown-toggle" aria-haspopup="listbox" aria-expanded="false">
                                    <span class="dropdown-label">
                                        <?php
                                            if ($id_riset && isset($riset_options[$id_riset])) {
                                                echo htmlspecialchars($riset_options[$id_riset]);
                                            } else {
                                                echo "Pilih Bidang Riset";
                                            }
                                        ?>
                                    </span>
                                    <i class="fa-solid fa-chevron-down caret"></i>
                                </button>

                                <div class="dropdown-menu" role="listbox">
                                    <?php foreach ($riset_options as $id_r => $nama_r): ?>
                                        <button
                                            type="button"
                                            class="dropdown-item"
                                            data-value="<?= $id_r; ?>">
                                            <?= htmlspecialchars($nama_r); ?>
                                        </button>
                                    <?php endforeach; ?>
                                </div>

                                <input type="hidden" name="id_riset" id="id_riset_hidden" value="<?= htmlspecialchars($id_riset); ?>">
                            </div>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn-primary btn-save">
                            Simpan Publikasi
                            <i class="fa-solid fa-check"></i>
                        </button>
                    </div>
                </div>
            </form>
        </section>
    </main>

    <!-- NOTIFIKASI POPUP (SAMA SEPERTI FORM RISET/PROFILE) -->
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
    <script src="../../../Assets/Javascript/Admin/FormPublikasi.js"></script>

    <script>
        window.profileStatus       = <?= json_encode($status  ?? '') ?>;
        window.profileMessage      = <?= json_encode($message ?? '') ?>;
        window.profileRedirectUrl  = <?= json_encode($redirectTo) ?>;
    </script>
    <script src="../../../Assets/Javascript/Admin/Profile.js"></script>
</body>
</html>
