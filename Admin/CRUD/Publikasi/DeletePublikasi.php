<?php
require_once __DIR__ . '../../../Cek_Autentikasi.php';
require __DIR__ . '../../../Koneksi/KoneksiSasa.php';

// =======================
// HAPUS BANYAK (bulk) -> ids[]
// =======================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['ids'])) {
    $ids = array_map('intval', $_POST['ids']);
    $ids = array_values(array_filter($ids, fn($v) => $v > 0));

    if ($ids) {
        $placeholders = [];
        $params = [];
        $i = 1;
        foreach ($ids as $id) {
            $placeholders[] = '$' . $i;
            $params[] = $id;
            $i++;
        }
        qparams("DELETE FROM publikasi WHERE id_publikasi IN (" . implode(',', $placeholders) . ")", $params);
    }

    header("Location: IndexPublikasi.php");
    exit;
}

// =======================
// HAPUS SATU (single) -> POST id_publikasi
// =======================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['id_publikasi'])) {
    $id = (int)$_POST['id_publikasi'];

    if ($id > 0) {
        qparams("DELETE FROM publikasi WHERE id_publikasi = $1", [$id]);
    }

    header("Location: IndexPublikasi.php");
    exit;
}

// =======================
// HAPUS SATU (opsional) -> GET id
// =======================
if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];

    if ($id > 0) {
        qparams("DELETE FROM publikasi WHERE id_publikasi = $1", [$id]);
    }

    header("Location: IndexPublikasi.php");
    exit;
}

// fallback
header("Location: IndexPublikasi.php");
exit;
