<?php
require 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ids'])) {
    $ids = array_map('intval', $_POST['ids']);
    $idString = implode(',', $ids);

    pg_query($conn, "DELETE FROM karya WHERE id_karya IN ($idString)");

    header("Location: IndexKarya.php");
    exit;
}

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    pg_query($conn, "DELETE FROM karya WHERE id_karya = $id");

    header("Location: IndexKarya.php");
    exit;
}

header("Location: IndexKarya.php");
exit;
?>