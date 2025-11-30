<?php
require __DIR__ . '/../koneksi.php';

$id = $_GET['id'] ?? '';
$errors = [];
$data = null;

if ($id === '') {
    die("<h2 style='color:red;'>ID riset tidak ditemukan.</h2>");
}

try {
    $res = qparams("SELECT id_riset, nama_bidang_riset FROM bidangriset WHERE id_riset=$1", [$id]);
    $data = pg_fetch_assoc($res);
} catch (Throwable $e) {
    die("<h2 style='color:red;'>Gagal mengambil data.</h2>");
}

if (!$data) {
    die("<h2 style='color:red;'>Data riset tidak ditemukan.</h2>");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['namaRiset'] ?? '');

    if ($nama === '') {
        $errors[] = "Nama riset wajib diisi.";
    } else {
        try {
            qparams("UPDATE bidangriset SET nama_bidang_riset=$1 WHERE id_riset=$2", [$nama, $id]);

            header("Location: IndexRiset.php?status=updated");
            exit;
        } catch (Throwable $e) {
            $errors[] = "Gagal menyimpan perubahan.";
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

    <link rel="stylesheet" href="../../../Assets/Css/Admin/Sidebar.css">
    <link rel="stylesheet" href="../../../Assets/Css/Admin/EditRiset.css">
</head>

<body>

    <div id="sidebar-container"></div>

    <main class="content">

        <button class="btn-back" onclick="window.location.href='IndexRiset.php'">
            <i class="fa-solid fa-chevron-left"></i>
            <span>Kembali</span>
        </button>

        <h1 class="page-title">Edit Riset</h1>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-error">
                <?php foreach ($errors as $err): ?>
                    <p><?= htmlspecialchars($err) ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <section class="form-card">
            <form method="post">
                <div class="form-group">
                    <label for="namaRiset">Nama Riset</label>
                    <input
                        type="text"
                        id="namaRiset"
                        name="namaRiset"
                        value="<?= htmlspecialchars($data['nama_bidang_riset']) ?>"
                        placeholder="Masukkan nama riset"
                    >
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn-save-change">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </section>

    </main>

    <script src="../../../Assets/Javascript/Admin/Riset.js"></script>
</body>

</html>
