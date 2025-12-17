<?php
require_once __DIR__ . '../../../Cek_Autentikasi.php';
require_once __DIR__ . '/config.php';
$conn = getDBConnection();

$ids = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ids = $_POST['selected_ids'] ?? [];
    if (empty($ids) && isset($_POST['id_pengumuman'])) {
        $ids = [$_POST['id_pengumuman']];
    }
}

$ids = array_values(array_filter($ids, fn($v) => ctype_digit((string)$v)));

if (empty($ids)) {
    header('Location: pengumuman.php');
    exit;
}

$placeholders = implode(',', array_fill(0, count($ids), '?'));
$sql = "DELETE FROM pengumuman WHERE id_pengumuman IN ($placeholders)";
$stmt = $conn->prepare($sql);
$stmt->execute(array_map('intval', $ids));

header('Location: pengumuman.php');
exit;