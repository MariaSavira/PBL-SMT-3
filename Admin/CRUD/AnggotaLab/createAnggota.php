<?php
    require __DIR__ . '../../koneksi.php';

    $err = '';
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $nama = trim($_POST['nama'] ?? '');
        $jabatan = trim($_POST['jabatan'] ?? '');
        $password = trim($_POST['password'] ?? '');
        $password_verify = trim($_POST['password_verify'] ?? '');
        $deskripsi = trim($_POST['deskripsi'] ?? '');
        $foto = trim($_POST['foto'] ?? '');
        $username = trim($_POST['username'] ?? '');
        $bidang_keahlian = $_POST['bidang_keahlian'] ?? []; 
        
        $link = $_POST['link'] ?? [];
        $link = array_filter($link);
        $json_link = json_encode($link);

        if ($nama === '') {
            $err = "Nama wajib diisi!";
        } else if ($password === '') {
            $err = "Password wajib diisi!";
        } else if ($password !== $password_verify) {
            $err = "Password harus sama!";
        } else if ($username === '') {
            $err = "Username wajib diisi!";
        } else if (empty($bidang_keahlian)) {
            $err = "Minimal pilih 1 bidang keahlian!";
        } else {

            $password_hash = hash('sha256', $password);

            try {   
                $result = qparams(
                    "INSERT INTO anggota_lab
                    (nama, jabatan, password_hash, deskripsi, foto, link, username)
                    VALUES ($1, $2, $3, $4, $5, $6::jsonb, $7)
                    RETURNING id",
                    [
                        $nama,
                        $jabatan,
                        $password_hash,
                        $deskripsi,
                        $foto,
                        $json_link,
                        $username
                    ]
                );

                $anggota = pg_fetch_assoc($result);
                $anggota_id = $anggota['id'];

                foreach ($bidang_keahlian as $keahlian_id) {
                    qparams(
                        "INSERT INTO anggota_bidang_keahlian (anggota_id, bidang_keahlian_id)
                        VALUES ($1, $2)",
                        [$anggota_id, $keahlian_id]
                    );
                }
                echo "Data berhasil disimpan!";
                
            } catch (Exception $e) {
                $err = "Gagal menyimpan: " . $e->getMessage();
            }
        }
    }
?>