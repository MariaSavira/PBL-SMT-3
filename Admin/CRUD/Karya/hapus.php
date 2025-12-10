<?php
require 'koneksi.php';

// hapus banyak
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ids'])) {
    $ids = array_map('intval', $_POST['ids']);
    $idString = implode(',', $ids);

    pg_query($conn, "DELETE FROM karya WHERE id_karya IN ($idString)");

    header("Location:index.php");
    exit;
}

// hapus satu
if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    pg_query($conn, "DELETE FROM karya WHERE id_karya = $id");

    header("Location:index.php");
    exit;
}

header("Location:index.php");
exit;
?>