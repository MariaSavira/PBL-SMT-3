<?php
    require_once __DIR__ . '../../../Cek_Autentikasi.php';
    require __DIR__ . '../../../Koneksi/KoneksiSasa.php';

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        exit('Method not allowed');
    }

    $id_anggota = trim($_POST['id_anggota'] ?? '');

    if ($id_anggota === '' || !ctype_digit($id_anggota)) {
        http_response_code(400);
        exit('ID Anggota tidak valid.');
    }

    try {
        q('BEGIN');

        qparams(
            'DELETE FROM public.anggota_keahlian WHERE id_anggota = $1',
            [$id_anggota]
        );

        qparams(
            'DELETE FROM public.anggotalab WHERE id_anggota = $1',
            [$id_anggota]
        );

        q('REFRESH MATERIALIZED VIEW mv_anggota_keahlian;');

        q('COMMIT');

        $status  = 'success';
        $message = 'Anggota berhasil dihapus.';

    } catch (Throwable $e) {
        q('ROLLBACK');

        $status  = 'error';
        $message = 'Gagal menghapus: ' . $e->getMessage();
    }

    $redirect = $_SERVER['HTTP_REFERER'] ?? '/PBL-SMT-3/Admin/Dashboard.php';
    $sep = (strpos($redirect, '?') !== false) ? '&' : '?';

    header('Location: ' . $redirect . $sep . 'status=' . urlencode($status) . '&message=' . urlencode($message));
    exit;
?>