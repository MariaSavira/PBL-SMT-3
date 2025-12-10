<?php
require __DIR__ . '/../koneksi.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: IndexRiset.php');
    exit;
}

$ids = $_POST['selected_ids'] ?? [];

if (empty($ids)) {
    header('Location: IndexRiset.php');
    exit;
}

$placeholders = [];
$params = [];
foreach ($ids as $i => $id) {
    $placeholders[] = '$' . ($i + 1);
    $params[] = $id;
}

$sql = 'DELETE FROM bidangriset WHERE id_riset IN (' . implode(',', $placeholders) . ')';
qparams($sql, $params);

header('Location: IndexRiset.php');
exit;