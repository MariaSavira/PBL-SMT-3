<!DOCTYPE html>
<html lang="id">

<?php
    require_once __DIR__ . '../../../Cek_Autentikasi.php';
    require __DIR__ . '../../../Koneksi/KoneksiSasa.php';

    $status  = '';  
    $message = '';   

    $id_anggota = $_SESSION['id_anggota'] ?? '';

    if ($id_anggota === '') {
        http_response_code(400);
        exit('Session tidak valid.');
    }

    try {
        $res = qparams(
            'SELECT id_anggota, nama, username, foto 
            FROM public.anggotalab 
            WHERE id_anggota = $1',
            [$id_anggota]
        );
        $row = pg_fetch_assoc($res);

        if (!$row) {
            http_response_code(404);
            exit('Data profil tidak ditemukan.');
        }
    } catch (Throwable $e) {
        exit('Error: ' . htmlspecialchars($e->getMessage()));
    }

    $nama      = $row['nama'];
    $username  = $row['username'];
    $foto_lama = $row['foto'] ?? '';
    $jabatan   = $_SESSION['jabatan'] ?? 'Admin Lab';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $err      = '';
        $nama     = trim($_POST['nama'] ?? '');
        $username = trim($_POST['username'] ?? '');
        $passBaru = $_POST['password_baru'] ?? '';
        $konfirm  = $_POST['konfirmasi_password'] ?? '';

        if ($nama === '') {
            $err = 'Nama wajib diisi.';
        } elseif ($username === '') {
            $err = 'Username wajib diisi.';
        } elseif ($passBaru !== '' && $passBaru !== $konfirm) {
            $err = 'Konfirmasi password tidak sama.';
        }

        $fotoBaru = null;
        $folderUrl  = '/PBL-SMT-3/Assets/Image/AnggotaLab/';
        $folderFs   = $_SERVER['DOCUMENT_ROOT'] . $folderUrl;

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
                    $namaFile  = 'profil_' . $id_anggota . '_' . $timestamp . '.' . $ext;
                    $tujuan    = $folderFs . $namaFile;

                    if (!empty($foto_lama)) {
                        $oldPath = $folderFs . $foto_lama;
                        if (file_exists($oldPath)) {
                            @unlink($oldPath);
                        }
                    }

                    if (!move_uploaded_file($_FILES['foto']['tmp_name'], $tujuan)) {
                        $err = 'Gagal menyimpan file foto di server.';
                    } else {
                        $fotoBaru   = $namaFile;
                        $foto_lama  = $namaFile;
                    }
                }
            }
        }

        if ($err === '') {
            $fields = [];
            $params = [];
            $i      = 1;

            $fields[] = "nama = $$i";
            $params[] = $nama;
            $i++;

            $fields[] = "username = $$i";
            $params[] = $username;
            $i++;

            if ($passBaru !== '') {
                $hash = password_hash($passBaru, PASSWORD_DEFAULT);
                $fields[] = "password_hash = $$i";
                $params[] = $hash;
                $i++;
            }

            if ($fotoBaru !== null) {
                $fields[] = "foto = $$i";
                $params[] = $fotoBaru;
                $i++;
            }

            $params[] = $id_anggota;
            $sql = 'UPDATE public.anggotalab
                    SET ' . implode(', ', $fields) . " 
                    WHERE id_anggota = $$i";

            try {
                qparams($sql, $params);

                q('REFRESH MATERIALIZED VIEW mv_anggota_keahlian;');

                $_SESSION['nama']     = $nama;
                $_SESSION['username'] = $username;
                if ($fotoBaru !== null) {
                    $_SESSION['foto'] = $fotoBaru;
                }

                $status  = 'success';
                $message = 'Perubahan profil berhasil disimpan.';
            } catch (Throwable $e) {
                $status  = 'error';
                $message = 'Query gagal: ' . $e->getMessage();
            }
        } else {
            $status  = 'error';
            $message = $err;
        }
    }

    $folderUrl = '/PBL-SMT-3/Assets/Image/AnggotaLab/';
    $folderFs  = $_SERVER['DOCUMENT_ROOT'] . $folderUrl;

    if (!empty($foto_lama) && file_exists($folderFs . $foto_lama)) {
        $src = $folderUrl . $foto_lama;
    } else {
        $src = $folderUrl . 'No-Profile.png';
    }
?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil <?= htmlspecialchars($nama) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="icon" type="images/x-icon"
        href="../../../Assets/Image/Logo/Logo Without Text.png" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="../../../Assets/Css/Admin/ProfilAdmin.css">
</head>

<body>
    <div id="sidebar"></div>
    <main class="content" id="content">
        <?php if (!empty($_GET['error'])): ?>
            <div class="alert error-alert">
                <?= htmlspecialchars($_GET['error']) ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($_GET['success'])): ?>
            <div class="alert success-alert">
                <?= htmlspecialchars($_GET['success']) ?>
            </div>
        <?php endif; ?>

        <div id="header"></div>

        <div class="content-header page-header">
            <a href="../AnggotaLab/IndexAnggota.php">
                <button type="button" class="btn-back">
                    <i class="fa-solid fa-chevron-left"></i>
                    Kembali
                </button>
            </a>

            <h1 class="page-title">Edit Profil</h1>
            <div></div>
        </div>

        <section class="profile-layout">
            <form class="profile-form" method="post" action="" enctype="multipart/form-data">

                <div class="profile-card">
                    <div class="avatar-wrapper">
                        <div class="avatar-circle" id="avatarCircle">
                            <img src="<?= htmlspecialchars($src) ?>" alt="Foto Profil" id="avatarPreview">
                        </div>

                        <label class="avatar-upload" title="Ubah foto profil">
                            <i class="fa-solid fa-camera"></i>
                            <input type="file" accept="image/*" name="foto" id="avatarInput">
                        </label>
                    </div>

                    <div class="content-profile-info">
                        <div class="content-profile-name">
                            <?= htmlspecialchars($nama ?: 'Nama Admin') ?>
                        </div>
                        <div class="content-profile-role">
                            <?= htmlspecialchars($jabatan) ?>
                        </div>
                    </div>
                </div>

                <div class="form-card">
                    <h2 class="form-title">Informasi Diri</h2>
                    <p class="form-subtitle">
                        Perbarui data profil sesuai kebutuhan. Password boleh dikosongkan bila tidak ingin diubah.
                    </p>

                    <div class="form-grid">
                        <div class="field-group">
                            <label for="nama">Nama</label>
                            <input
                                id="nama"
                                name="nama"
                                type="text"
                                class="field-input"
                                placeholder="Nama lengkap kamu"
                                value="<?= htmlspecialchars($nama) ?>">
                        </div>

                        <div class="field-group">
                            <label for="username">Username</label>
                            <input
                                id="username"
                                name="username"
                                type="text"
                                class="field-input"
                                placeholder="Username"
                                value="<?= htmlspecialchars($username) ?>">
                        </div>

                        <div class="field-group">
                            <label for="password-baru">
                                Password Baru
                                <span style="font-weight:400;color:var(--text-muted);">
                                    (Opsional)
                                </span>
                            </label>
                            <input
                                id="password-baru"
                                name="password_baru"
                                type="password"
                                class="field-input"
                                placeholder="••••••••">
                            <small>Biarkan kosong jika tidak ingin mengganti password.</small>
                        </div>

                        <div class="field-group">
                            <label for="konfirmasi-password">Konfirmasi Password</label>
                            <input
                                id="konfirmasi-password"
                                name="konfirmasi_password"
                                type="password"
                                class="field-input"
                                placeholder="••••••••">
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
</body>

</html>