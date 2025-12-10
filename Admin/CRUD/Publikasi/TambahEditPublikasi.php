<?php
    require_once __DIR__ . '../../../Cek_Autentikasi.php';
    require_once __DIR__ . '../../../Koneksi/KoneksiSasa.php';

    $conn = get_pg_connection();

    $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    $editMode = $id > 0;

    $id_publikasi = "";
    $judul = "";
    $jenis = "Buku";
    $author = "";
    $id_riset = "";
    $status = "Aktif";
    $error_message = "";

    // ================== AMBIL BIDANG RISET ==================
    $riset_options = [];
    $query = "
        SELECT id_riset, nama_bidang_riset 
        FROM bidangriset 
        ORDER BY nama_bidang_riset
    ";

    $res_riset = pg_query($conn, $query);

    if ($res_riset) {
        while ($row = pg_fetch_assoc($res_riset)) {
            $riset_options[$row['id_riset']] = $row['nama_bidang_riset'];
        }
    } else {
        $error_message = "Gagal mengambil data Bidang Riset: " . pg_last_error($conn);
    }

    // ================== MODE EDIT ==================
    if ($editMode) {
        $res = pg_query_params(
            $conn,
            "SELECT judul, jenis, author, id_riset, status FROM publikasi WHERE id_publikasi = $1",
            [$id]
        );

        if ($res && pg_num_rows($res) > 0) {
            $row = pg_fetch_assoc($res);
            $id_publikasi = $id;
            $judul        = $row['judul'];
            $jenis        = $row['jenis'];

            // author JSONB -> string "a, b, c"
            $author_raw = $row['author'];
            if (!empty($author_raw)) {
                $author_decoded = json_decode($author_raw, true);
                $author = is_array($author_decoded) ? implode(", ", $author_decoded) : $author_raw;
            }

            $id_riset = $row['id_riset'];

            // status boolean t/f -> Aktif/Draft
            $status = ($row['status'] === 't' || $row['status'] === 'true' || $row['status'] === true)
                ? 'Aktif'
                : 'Draft';
        } else {
            if (!$res) {
                $error_message = "Error query publikasi: " . pg_last_error($conn);
            } else {
                $error_message = "Data Publikasi tidak ditemukan.";
            }
        }
    }

    // ================== PROSES SUBMIT ==================
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $judul_input    = trim($_POST['judul'] ?? '');
        $jenis_input    = trim($_POST['jenis'] ?? '');
        $author_input   = trim($_POST['author'] ?? '');
        $id_riset_input = (int)($_POST['id_riset'] ?? 0);
        $status_input   = trim($_POST['status'] ?? '');

        // set ulang ke variabel tampilan
        $judul    = $judul_input;
        $jenis    = $jenis_input;
        $author   = $author_input;
        $id_riset = $id_riset_input;
        $status   = $status_input;

        // VALIDASI
        $required_fields = [
            'judul'    => $judul_input,
            'jenis'    => $jenis_input,
            'author'   => $author_input,
            'id_riset' => $id_riset_input,
            'status'   => $status_input,
        ];

        $missing_fields = [];
        foreach ($required_fields as $field_name => $field_value) {
            if ($field_name === 'id_riset') {
                if ($field_value <= 0 || !array_key_exists($field_value, $riset_options)) {
                    $missing_fields[] = 'Bidang Riset';
                }
                continue;
            }

            if (empty($field_value)) {
                $display_name = match ($field_name) {
                    'judul'  => 'Judul',
                    'jenis'  => 'Jenis Publikasi',
                    'author' => 'Penulis',
                    'status' => 'Status',
                    default  => $field_name,
                };
                $missing_fields[] = $display_name;
            }
        }

        $final_error_message = '';
        if (!empty($missing_fields)) {
            $final_error_message .= "Kolom berikut wajib diisi: " . implode(', ', $missing_fields) . ".";
        }

        if (!empty($final_error_message)) {
            $error_message = $final_error_message;
        } else {
            // KONVERSI AUTHOR KE JSON ARRAY
            $authors_array = array_filter(
                array_map('trim', explode(',', $author_input)),
                fn($v) => $v !== ''
            );
            $author_json   = json_encode($authors_array);

            $status_db = ($status_input === 'Aktif') ? 't' : 'f';

            if ($editMode) {
                $id_hidden = (int)($_POST['id_publikasi_hidden'] ?? 0);

                $sql = "
                    UPDATE publikasi SET
                        judul    = $1,
                        jenis    = $2,
                        author   = $3,
                        id_riset = $4,
                        status   = $5
                    WHERE id_publikasi = $6
                ";

                $params = [
                    $judul_input,
                    $jenis_input,
                    $author_json,
                    $id_riset_input,
                    $status_db,
                    $id_hidden
                ];

                $run = pg_query_params($conn, $sql, $params);
            } else {
                $sql = "
                    INSERT INTO publikasi (judul, jenis, author, id_riset, status)
                    VALUES ($1, $2, $3, $4, $5)
                ";

                $params = [
                    $judul_input,
                    $jenis_input,
                    $author_json,
                    $id_riset_input,
                    $status_db
                ];

                $run = pg_query_params($conn, $sql, $params);
            }

            if (!$run) {
                $error_message = 'QUERY ERROR: ' . pg_last_error($conn);
            } else {
                header("Location: IndexPublikasi.php");
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
    <link rel="stylesheet" href="/PBL-SMT-3/Assets/Css/Admin/FormPublikasi.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
        rel="stylesheet">
    <link rel="icon" type="images/x-icon"
          href="../../../Assets/Image/Logo/Logo Without Text.png" />
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

            <h1 class="page-title"><?php echo $editMode ? "Edit Publikasi" : "Tambah Publikasi"; ?></h1>
            <div></div>
        </div>

        <section class="profile-layout">
            <?php if (!empty($error_message)): ?>
                <div class="error-message">
                    <?php echo nl2br(htmlspecialchars($error_message)); ?>
                </div>
            <?php endif; ?>

            <form class="profile-form" method="POST" action="">
                <?php if ($editMode): ?>
                    <input type="hidden" name="id_publikasi_hidden" value="<?php echo (int)$id_publikasi; ?>">
                <?php endif; ?>

                <!-- Kartu kanan: form -->
                <div class="form-card">
                    <h2 class="form-title">Data Publikasi</h2>
                    <p class="form-subtitle">
                        Lengkapi informasi mengenai publikasi berikut.
                    </p>

                    <div class="form-grid">
                        <!-- JUDUL -->
                        <div class="field-group">
                            <label for="judul">Judul</label>
                            <input
                                type="text"
                                id="judul"
                                name="judul"
                                class="field-input"
                                value="<?= htmlspecialchars($judul) ?>"
                                placeholder="Masukkan judul publikasi">
                        </div>

                        <!-- JENIS PUBLIKASI (DROPDOWN CUSTOM) -->
                        <div class="field-group">
                            <label for="jenis">Jenis Publikasi</label>

                            <div class="field-select" data-placeholder="Pilih Jenis Publikasi">
                                <button type="button" class="dropdown-toggle" aria-haspopup="listbox" aria-expanded="false">
                                    <span class="dropdown-label">
                                        <?php echo $jenis ? htmlspecialchars($jenis) : 'Pilih Jenis Publikasi'; ?>
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
                            <label for="author">Penulis</label>
                            <input
                                type="text"
                                id="author"
                                name="author"
                                class="field-input"
                                value="<?= htmlspecialchars($author) ?>"
                                placeholder="Masukkan nama penulis">
                            <small class="helper-text">Pisahkan dengan koma jika lebih dari satu penulis</small>
                        </div>

                        <!-- BIDANG RISET (DROPDOWN CUSTOM) -->
                        <div class="field-group">
                            <label for="id_riset">Bidang Riset</label>

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
                                            data-value="<?php echo $id_r; ?>">
                                            <?php echo htmlspecialchars($nama_r); ?>
                                        </button>
                                    <?php endforeach; ?>
                                </div>

                                <input type="hidden" name="id_riset" id="id_riset_hidden" value="<?php echo htmlspecialchars($id_riset); ?>">
                            </div>
                        </div>

                        <!-- STATUS (DROPDOWN CUSTOM) -->
                        <!-- <div class="field-group">
                            <label for="status">Status</label>

                            <div class="field-select" data-placeholder="Pilih Status">
                                <button type="button" class="dropdown-toggle" aria-haspopup="listbox" aria-expanded="false">
                                    <span class="dropdown-label">
                                        <?php echo $status ? htmlspecialchars($status) : 'Pilih Status'; ?>
                                    </span>
                                    <i class="fa-solid fa-chevron-down caret"></i>
                                </button>

                                <div class="dropdown-menu" role="listbox" disabled>
                                    <button type="button" class="dropdown-item" data-value="Aktif">Aktif</button>
                                    <button type="button" class="dropdown-item" data-value="Draft">Draft</button>
                                </div>

                                <input type="hidden" name="status" id="status_hidden" value="<?php echo htmlspecialchars($status); ?>">
                            </div>
                        </div> -->
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

    <script src="../../../Assets/Javascript/Admin/Sidebar.js"></script>
    <script src="../../../Assets/Javascript/Admin/Header.js"></script>
    <script src="../../../Assets/Javascript/Admin/FormPublikasi.js"></script>
</body>
</html>
