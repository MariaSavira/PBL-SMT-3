<?php
// sambung ke koneksi PostgreSQL
require __DIR__ . '/../Koneksi.php';

$errors    = [];
$namaRiset = '';   // biar nggak undefined di bagian value input

// kalau form disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $namaRiset = trim($_POST['namaRiset'] ?? '');

    // validasi
    if ($namaRiset === '') {
        $errors[] = 'Nama riset wajib diisi.';
    } else {
        try {
            // pakai helper qparams() dari Koneksi.php
            qparams(
                'INSERT INTO bidangriset (nama_bidang_riset) VALUES ($1)',
                [$namaRiset]
            );

            // kalau sukses, balik ke index dengan status
            header('Location: IndexRiset.php?status=created');
            exit;
        } catch (Throwable $e) {
            // kalau mau debug:
            // echo $e->getMessage();
            $errors[] = 'Gagal menyimpan data ke database.';
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
    <link rel="stylesheet" href="../../../Assets/Css/Admin/Sidebar.css">
    <link rel="stylesheet" href="../../../Assets/Css/Admin/TambahRiset.css">
</head>

<body>

    <!-- sidebar akan di-inject lewat JS -->
    <div id="sidebar-container"></div>

    <main class="content">

        <!-- tombol kembali -->
        <button class="btn-back" onclick="window.location.href='IndexRiset.php'">
            <i class="fa-solid fa-chevron-left"></i>
            <span>Kembali</span>
        </button>

        <!-- judul halaman -->
        <h1 class="page-title">Tambah Riset</h1>

        <!-- pesan error -->
        <?php if (!empty($errors)): ?>
            <div class="alert alert-error">
                <?php foreach ($errors as $err): ?>
                    <p><?= htmlspecialchars($err) ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <!-- card form -->
        <section class="form-card">
            <form method="post" action="">
                <div class="form-group">
                    <label for="namaRiset">Nama Riset</label>
                    <input
                        type="text"
                        id="namaRiset"
                        name="namaRiset"
                        placeholder="Masukkan nama riset"
                        value="<?= htmlspecialchars($namaRiset) ?>">
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn-save">
                        Simpan
                    </button>
                </div>
            </form>
        </section>

    </main>

    <script src="../../../Assets/Javascript/Admin/Sidebar.js"></script>
</body>
</html>
