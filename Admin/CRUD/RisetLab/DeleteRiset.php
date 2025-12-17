<?php
    require_once __DIR__ . '../../../Cek_Autentikasi.php';
    require __DIR__ . '../../../Koneksi/KoneksiSasa.php';

    $ids = [];

    // Bulk delete via POST: selected_ids[]
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $ids = $_POST['selected_ids'] ?? [];

    // Delete satuan via GET: ?id=...
    } elseif (isset($_GET['id'])) {
        $ids = [$_GET['id']];
    }

    // Kalau tidak ada ID, balik saja
    if (empty($ids)) {
        header('Location: IndexRiset.php');
        exit;
    }

    // Siapkan placeholder & parameter untuk qparams
    $placeholders = [];
    $params      = [];

    foreach ($ids as $i => $id) {
        $placeholders[] = '$' . ($i + 1);
        $params[]       = $id;
    }

    $sql = 'DELETE FROM bidangriset WHERE id_riset IN (' . implode(',', $placeholders) . ')';
    qparams($sql, $params);

    header('Location: IndexRiset.php');
    exit;
?>