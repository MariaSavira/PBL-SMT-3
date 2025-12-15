<?php
    require_once __DIR__ . '../../../Cek_Autentikasi.php';
    require __DIR__ . '../../../Koneksi/KoneksiSasa.php';

    $status = '';
    $message = '';
    $redirectTo  = 'IndexRiset.php';

    $id = $_GET['id'] ?? '';

    $redirectTo = !empty($_SERVER['HTTP_REFERER'])
        ? $_SERVER['HTTP_REFERER']
        : 'IndexRiset.php';

    if ($id === '') {
        die("<h2 style='color:red;'>ID riset tidak ditemukan.</h2>");
    }

    // Ambil data awal
    try {
        $res = qparams("SELECT id_riset, nama_bidang_riset FROM bidangriset WHERE id_riset=$1", [$id]);
        $data = pg_fetch_assoc($res);
    } catch (Throwable $e) {
        die("<h2 style='color:red;'>Gagal mengambil data.</h2>");
    }

    if (!$data) {
        die("<h2 style='color:red;'>Data riset tidak ditemukan.</h2>");
    }

    // Jika submit
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $nama = trim($_POST['namaRiset'] ?? '');

        if ($nama === '') {
            $status   = 'error';
            $message  = 'Nama riset tidak boleh kosong.';
        } else {
            try {
                qparams("UPDATE bidangriset SET nama_bidang_riset=$1 WHERE id_riset=$2", [$nama, $id]);

                $status  = 'success';
                $message = 'Perubahan riset berhasil disimpan';
                
            } catch (Throwable $e) {
                $status  = 'error';
                $message = 'Gagal menyimpan perubahan.';
            }
        }
    }
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Riset</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap"
        rel="stylesheet">

    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="icon" type="images/x-icon"
        href="../../../Assets/Image/Logo/Logo Without Text.png" />

    <link rel="stylesheet" href="../../../Assets/Css/Admin/Sidebar.css">
    <link rel="stylesheet" href="../../../Assets/Css/Admin/FormRiset.css">
</head>

<body>

    <div id="sidebar"></div>

    <main class="content" id="content">
        <div id="header"></div>

        <div class="content-header page-header">
            <button class="btn-back" onclick="window.location.href='IndexRiset.php'">
                <i class="fa-solid fa-chevron-left"></i>
                Kembali
            </button>

            <h1 class="page-title">Edit Riset</h1>
            <div></div>
        </div>

        <section class="profile-layout">
            <form class="profile-form" method="post">
                <div class="form-card">
                    <h2 class="form-title">Data Riset</h2>
                    <p class="form-subtitle">Perbarui informasi riset di bawah ini.</p>

                    <div class="form-grid">

                        <div class="field-group">
                            <label for="namaRiset">Nama Riset</label>
                            <input
                                type="text"
                                id="namaRiset"
                                name="namaRiset"
                                class="field-input"
                                value="<?= htmlspecialchars($data['nama_bidang_riset']) ?>"
                                placeholder="Masukkan nama riset">
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="btn-primary btn-save">
                                Simpan Perubahan
                                <i class="fa-solid fa-check"></i>
                            </button>
                        </div>

                    </div>
                </div>
            </form>
        </section>
    </main>

    <!-- NOTIFICATION -->
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
        window.profileStatus = <?= json_encode($status ?? '') ?>;
        window.profileMessage = <?= json_encode($message ?? '') ?>;
        window.profileRedirectUrl = <?= json_encode($redirectTo) ?>;
    </script>

    <script src="../../../Assets/Javascript/Admin/Profile.js"></script>

</body>

</html>