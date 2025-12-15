<?php
    // sambung ke koneksi PostgreSQL
    require_once __DIR__ . '../../../Cek_Autentikasi.php';
    require __DIR__ . '../../../Koneksi/KoneksiSasa.php';

    $status      = '';
    $message     = '';
    $redirectTo  = 'IndexRiset.php';

    $namaRiset   = '';      // biar tetap keisi di form

    // kalau form disubmit
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        $namaRiset = trim($_POST['namaRiset'] ?? '');

        // validasi
        if ($namaRiset === '') {
            $status   = 'error';
            $message  = 'Nama riset tidak boleh kosong.';
        } else {
            try {
                qparams(
                    'INSERT INTO bidangriset (nama_bidang_riset) VALUES ($1)',
                    [$namaRiset]
                );

                // kalau mau langsung redirect hard:
                // header('Location: IndexRiset.php?status=created');
                // exit;

                $status  = 'success';
                $message = 'Riset baru berhasil ditambahkan';
            } catch (Throwable $e) {
                $status  = 'error';
                $message = 'Gagal menyimpan: ' . $e->getMessage();
            }
        }
    }
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Riset</title>

    <!-- Poppins -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap"
        rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <!-- CSS sidebar & form tambah/edit -->
    <link rel="icon" type="images/x-icon"
        href="../../../Assets/Image/Logo/Logo Without Text.png" />
    <link rel="stylesheet" href="../../../Assets/Css/Admin/Sidebar.css">
    <link rel="stylesheet" href="../../../Assets/Css/Admin/FormRiset.css">
</head>

<body>

    <!-- sidebar akan di-inject lewat JS -->
    <div id="sidebar"></div>

    <main class="content" id="content">
        <div id="header"></div>

        <div class="content-header page-header">
            <button class="btn-back" onclick="window.location.href='IndexRiset.php'">
                <i class="fa-solid fa-chevron-left"></i>
                Kembali
            </button>

            <h1 class="page-title">Tambah Riset</h1>
            <div></div>
        </div>

        <section class="profile-layout">

            <form class="profile-form" method="post" action="">
                <div class="form-card">
                    <h2 class="form-title">Data Riset</h2>
                    <p class="form-subtitle">
                        Tambahkan bidang riset baru dengan mengisi informasi berikut.
                    </p>

                    <div class="form-grid">
                        <div class="field-group">
                            <label for="namaRiset">Nama Riset</label>
                            <input
                                type="text"
                                id="namaRiset"
                                name="namaRiset"
                                class="field-input"
                                placeholder="Masukkan nama riset"
                                value="<?= htmlspecialchars($namaRiset) ?>">
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="btn-primary btn-save">
                                Simpan Riset
                                <i class="fa-solid fa-check"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </section>

    </main>

    <!-- NOTIFICATION OVERLAY -->
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

    <script src="../../../Assets/Javascript/Admin/Header.js"></script>
    <script src="../../../Assets/Javascript/Admin/Sidebar.js"></script>
    <script>
        window.profileStatus      = <?= json_encode($status  ?? '') ?>;
        window.profileMessage     = <?= json_encode($message ?? '') ?>;
        window.profileRedirectUrl = <?= json_encode($redirectTo) ?>;
    </script>
    <script src="../../../Assets/Javascript/Admin/Profile.js"></script>
</body>

</html>
