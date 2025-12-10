<?php
    require_once __DIR__ . '../../../Cek_Autentikasi.php';
    require __DIR__ . '../../../Koneksi/KoneksiSasa.php';

    $status  = '';
    $message = '';

    $id_anggota = isset($_GET['id']) ? (int) $_GET['id'] : 0;
    if ($id_anggota <= 0) {
        http_response_code(400);
        exit('ID anggota tidak valid.');
    }

    $nama            = '';
    $username        = '';
    $jabatan         = '';
    $deskripsi       = '';
    $keahlianDipilih = [];
    $links           = [];
    $foto_lama       = '';

    try {
        $resAnggota = qparams(
            'SELECT id_anggota, nama, username, jabatan, deskripsi, foto, link
            FROM public.anggotalab
            WHERE id_anggota = $1',
            [$id_anggota]
        );
        $rowA = pg_fetch_assoc($resAnggota);

        if (!$rowA) {
            http_response_code(404);
            exit('Data anggota tidak ditemukan.');
        }

        $nama      = $rowA['nama'] ?? '';
        $username  = $rowA['username'] ?? '';
        $jabatan   = $rowA['jabatan'] ?? '';
        $deskripsi = $rowA['deskripsi'] ?? '';
        $foto_lama = $rowA['foto'] ?? '';

        $links = json_decode($rowA['link'] ?? '[]', true);
        if (!is_array($links)) {
            $links = [];
        }

        $resKA = qparams(
            'SELECT id_keahlian
            FROM public.anggota_keahlian
            WHERE id_anggota = $1',
            [$id_anggota]
        );
        while ($r = pg_fetch_assoc($resKA)) {
            $keahlianDipilih[] = (int) $r['id_keahlian'];
        }
    } catch (Throwable $e) {
        http_response_code(500);
        exit('Gagal mengambil data: ' . htmlspecialchars($e->getMessage()));
    }

    try {
        $resKeahlian = q('SELECT id_keahlian, nama_bidang_keahlian
                        FROM public.bidang_keahlian
                        ORDER BY nama_bidang_keahlian ASC');
        $daftarKeahlian = pg_fetch_all($resKeahlian) ?: [];
    } catch (Throwable $e) {
        $daftarKeahlian = [];
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $err        = '';
        $nama       = trim($_POST['nama'] ?? '');
        $username   = trim($_POST['username'] ?? '');
        $jabatan    = trim($_POST['jabatan'] ?? '');
        $password   = $_POST['password'] ?? '';
        $konfirmasi = $_POST['konfirmasi_password'] ?? '';
        $deskripsi  = trim($_POST['deskripsi'] ?? '');
        $keahlianDipilih = $_POST['keahlian'] ?? [];

        $linkLabel = $_POST['link_label'] ?? [];
        $linkUrl   = $_POST['link_url']   ?? [];
        $links     = [];

        foreach ($linkUrl as $idx => $urlRaw) {
            $url   = trim($urlRaw);
            $label = trim($linkLabel[$idx] ?? '');

            if ($url === '') {
                continue;
            }

            $links[] = [
                'label' => $label !== '' ? $label : 'Link',
                'url'   => $url,
            ];
        }

        if ($nama === '') {
            $err = 'Nama wajib diisi.';
        } elseif ($username === '') {
            $err = 'Username wajib diisi.';
        } elseif ($jabatan === '') {
            $err = 'Jabatan wajib dipilih.';
        } elseif ($deskripsi === '') {
            $err = 'Deskripsi wajib diisi.';
        } elseif (empty($keahlianDipilih)) {
            $err = 'Minimal pilih satu keahlian.';
        } elseif ($password !== '' && $password !== $konfirmasi) {
            $err = 'Konfirmasi password harus sama.';
        }

        $folderUrl = '/PBL-SMT-3/Assets/Image/AnggotaLab/';
        $folderFs = $_SERVER['DOCUMENT_ROOT'] . $folderUrl;
        $namaFileFinal = $foto_lama; 

        if ($err === '' && !empty($_FILES['foto']['name'])) {
            if ($_FILES['foto']['error'] !== UPLOAD_ERR_OK) {
                $err = 'Upload foto gagal (error code: ' . $_FILES['foto']['error'] . ')';
            } else {
                $ext     = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
                $allowed = ['jpg', 'jpeg', 'png'];

                if (!in_array($ext, $allowed)) {
                    $err = 'Format foto harus JPG, JPEG atau PNG.';
                } else {
                    if (!is_dir($folderFs)) {
                        @mkdir($folderFs, 0777, true);
                    }

                    $timestamp = time();
                    $namaFile  = 'anggota_' . $id_anggota . '_' . $timestamp . '.' . $ext;
                    $tujuan    = $folderFs . $namaFile;

                    if (!move_uploaded_file($_FILES['foto']['tmp_name'], $tujuan)) {
                        $err = 'Gagal menyimpan file foto di server.';
                    } else {
                        if (!empty($foto_lama)) {
                            $oldPath = $folderFs . $foto_lama;
                            if (file_exists($oldPath) && $foto_lama !== 'No-Profile.png') {
                                @unlink($oldPath);
                            }
                        }
                        $namaFileFinal = $namaFile;
                        $foto_lama = $namaFile; 
                    }
                }
            }
        }

        if ($err === '') {
            $jsonLinks = json_encode($links, JSON_UNESCAPED_SLASHES);

            $fields = [];
            $params = [];
            $i = 1;

            $fields[] = 'nama = $' . $i;
            $params[] = $nama;
            $i++;

            $fields[] = 'username = $' . $i;
            $params[] = $username;
            $i++;

            $fields[] = 'jabatan = $' . $i;
            $params[] = $jabatan;
            $i++;

            $fields[] = 'deskripsi = $' . $i;
            $params[] = $deskripsi;
            $i++;

            $fields[] = 'foto = $' . $i;
            $params[] = $namaFileFinal;
            $i++;

            $fields[] = 'link = $' . $i . '::jsonb';
            $params[] = $jsonLinks;
            $i++;

            if ($password !== '') {
                $passwordHash = password_hash($password, PASSWORD_DEFAULT);
                $fields[] = 'password_hash = $' . $i;
                $params[] = $passwordHash;
                $i++;
            }

            $params[] = $id_anggota;
            $sql = 'UPDATE public.anggotalab
                    SET ' . implode(', ', $fields) . '
                    WHERE id_anggota = $' . $i;

            try {
                qparams($sql, $params);

                qparams(
                    'DELETE FROM public.anggota_keahlian WHERE id_anggota = $1',
                    [$id_anggota]
                );

                foreach ($keahlianDipilih as $idK) {
                    if ($idK === '') continue;
                    qparams(
                        'INSERT INTO public.anggota_keahlian (id_anggota, id_keahlian)
                        VALUES ($1, $2)',
                        [$id_anggota, (int)$idK]
                    );
                }

                q('REFRESH MATERIALIZED VIEW mv_anggota_keahlian;');

                $status = 'success';
                $message = 'Data anggota berhasil diperbarui.';

            } catch (Throwable $e) {
                $status = 'error';
                $message = 'Gagal menyimpan: ' . $e->getMessage();
            }
        } else {
            $status = 'error';
            $message = $err;
        }
    }

    $folderUrl = '/PBL-SMT-3/Assets/Image/AnggotaLab/';
    $folderFs = $_SERVER['DOCUMENT_ROOT'] . $folderUrl;

    if (!empty($foto_lama) && file_exists($folderFs . $foto_lama)) {
        $src = $folderUrl . $foto_lama;
    } else {
        $src = $folderUrl . 'No-Profile.png';
    }
?>
<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Edit Anggota</title>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link
            href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
            rel="stylesheet">
        <link rel="icon" type="images/x-icon"
            href="../../../Assets/Image/Logo/Logo Without Text.png" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
        <link rel="stylesheet" href="../../../Assets/Css/Admin/FormAnggotaLab.css">
    </head>

    <body>
        <div id="sidebar"></div>

        <main class="content" id="content">
            <div id="header"></div>

            <div class="content-header page-header">
                <a href="IndexAnggota.php">
                    <button type="button" class="btn-back">
                        <i class="fa-solid fa-chevron-left"></i>
                        Kembali
                    </button>
                </a>

                <h1 class="page-title">Edit Anggota</h1>
                <div></div>
            </div>

            <section class="profile-layout">
                <form class="profile-form" method="post" action="" enctype="multipart/form-data">
                    <input type="hidden" name="id_anggota" value="<?= htmlspecialchars($id_anggota) ?>">

                    <div class="profile-card">
                        <div class="avatar-wrapper">
                            <div class="avatar-circle">
                                <img
                                    src="<?= htmlspecialchars($src) ?>"
                                    alt="Foto Anggota"
                                    id="avatarPreview">
                            </div>

                            <label class="avatar-upload" title="Upload foto anggota">
                                <i class="fa-solid fa-camera"></i>
                                <input type="file" name="foto" id="avatarInput" accept="image/*">
                            </label>
                        </div>

                        <div class="content-profile-info">
                            <div class="content-profile-name">
                                <?= htmlspecialchars($nama ?: 'Nama Anggota') ?>
                            </div>
                            <div class="content-profile-role">
                                <?= htmlspecialchars($jabatan ?: 'Jabatan') ?>
                            </div>
                        </div>
                    </div>

                    <div class="form-card">
                        <h2 class="form-title">Data Anggota</h2>
                        <p class="form-subtitle">
                            Perbarui informasi anggota laboratorium dengan data yang valid.
                        </p>

                        <div class="form-grid">
                            <div class="field-group">
                                <label for="nama">Nama Lengkap</label>
                                <input
                                    id="nama"
                                    name="nama"
                                    type="text"
                                    class="field-input"
                                    placeholder="Masukkan nama lengkap"
                                    value="<?= htmlspecialchars($nama) ?>">
                            </div>

                            <div class="field-group">
                                <label for="username">Username</label>
                                <input
                                    id="username"
                                    name="username"
                                    type="text"
                                    class="field-input"
                                    placeholder="Masukkan username"
                                    value="<?= htmlspecialchars($username) ?>">
                            </div>

                            <div class="field-group">
                                <label for="jabatan_hidden">Jabatan</label>

                                <div class="field-select dropdown-jabatan" data-dropdown="jabatan">
                                    <button type="button" class="dropdown-toggle" aria-haspopup="listbox" aria-expanded="false">
                                        <span class="dropdown-label">
                                            <?= $jabatan !== '' ? htmlspecialchars($jabatan) : 'Pilih jabatan' ?>
                                        </span>
                                        <i class="fa-solid fa-chevron-down caret"></i>
                                    </button>

                                    <div class="dropdown-menu" role="listbox">
                                        <button type="button" class="dropdown-item" data-value="Kepala Lab">Kepala Lab</button>
                                        <button type="button" class="dropdown-item" data-value="Peneliti">Peneliti</button>
                                        <button type="button" class="dropdown-item" data-value="Staff">Staff</button>
                                    </div>

                                    <input type="hidden" name="jabatan" id="jabatan_hidden"
                                        value="<?= htmlspecialchars($jabatan) ?>">
                                </div>
                            </div>

                            <div class="field-group">
                                <label for="password">Password (opsional)</label>
                                <input
                                    id="password"
                                    name="password"
                                    type="password"
                                    class="field-input"
                                    placeholder="Biarkan kosong jika tidak diganti">
                            </div>

                            <div class="field-group">
                                <label for="konfirmasi-password">Konfirmasi Password</label>
                                <input
                                    id="konfirmasi-password"
                                    name="konfirmasi_password"
                                    type="password"
                                    class="field-input"
                                    placeholder="Ulangi password baru">
                            </div>

                            <div class="field-group">
                                <label>Keahlian (minimal 1)</label>
                                <div class="chips-wrapper">
                                    <?php if (!$daftarKeahlian): ?>
                                        <small style="color:#ef4444;">Data bidang_keahlian kosong.</small>
                                    <?php else: ?>
                                        <?php foreach ($daftarKeahlian as $k): ?>
                                            <?php
                                            $idK   = (int)$k['id_keahlian'];
                                            $namaK = $k['nama_bidang_keahlian'];
                                            ?>
                                            <label class="chip-option">
                                                <input
                                                    type="checkbox"
                                                    name="keahlian[]"
                                                    value="<?= $idK ?>"
                                                    <?= in_array($idK, array_map('intval', $keahlianDipilih)) ? 'checked' : '' ?>>
                                                <span><?= htmlspecialchars($namaK) ?></span>
                                            </label>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </div>
                                <small>Pilih satu atau lebih bidang keahlian yang dimiliki anggota.</small>
                            </div>

                            <div class="field-group">
                                <label for="deskripsi">Deskripsi</label>
                                <textarea
                                    id="deskripsi"
                                    name="deskripsi"
                                    rows="4"
                                    class="field-input"
                                    placeholder="Tuliskan deskripsi singkat tentang anggota"><?= htmlspecialchars($deskripsi) ?></textarea>
                            </div>

                            <div class="field-group">
                                <label>Link Profil (opsional)</label>
                                <small class="field-hint">
                                    Tambahkan tautan profil seperti Sinta, Google Scholar, Scopus, dsb.
                                </small>

                                <div id="links-wrapper" class="links-wrapper">
                                    <?php if (!empty($links)): ?>
                                        <?php foreach ($links as $l): ?>
                                            <div class="link-row">
                                                <input
                                                    type="text"
                                                    name="link_label[]"
                                                    class="field-input link-label"
                                                    placeholder="Nama platform (mis. Sinta, Scholar)"
                                                    value="<?= htmlspecialchars($l['label'] ?? '') ?>"
                                                >
                                                <input
                                                    type="url"
                                                    name="link_url[]"
                                                    class="field-input link-url"
                                                    placeholder="https://contoh.com/profil-anda"
                                                    value="<?= htmlspecialchars($l['url'] ?? '') ?>"
                                                >
                                                <button type="button" class="btn-remove-link">
                                                    <i class="fa-solid fa-xmark"></i>
                                                </button>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <div class="link-row">
                                            <input
                                                type="text"
                                                name="link_label[]"
                                                class="field-input link-label"
                                                placeholder="Nama platform (mis. Sinta, Scholar)"
                                            >
                                            <input
                                                type="url"
                                                name="link_url[]"
                                                class="field-input link-url"
                                                placeholder="https://contoh.com/profil-anda"
                                            >
                                            <button type="button" class="btn-remove-link">
                                                <i class="fa-solid fa-xmark"></i>
                                            </button>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <button type="button" class="btn-add-link" id="btnTambahLink">
                                    + Tambah link
                                </button>
                            </div>
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="btn-primary">
                                Simpan Perubahan
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
            window.profileStatus  = <?= json_encode($status  ?? '') ?>;
            window.profileMessage = <?= json_encode($message ?? '') ?>;
        </script>
        <script src="../../../Assets/Javascript/Admin/Profile.js"></script>
        <script src="../../../Assets/Javascript/Admin/FormAnggotaLab.js"></script>
    </body>
</html>
