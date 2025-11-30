<?php
    function get_pg_connection() {
<<<<<<< HEAD
        $connStr = "host=localhost port=5432 dbname=lab_ba user=postgres password=123 options='--client_encoding=UTF8'";
=======
        $connStr = "host=localhost port=5432 dbname=lab_ba user=postgres password=29082006 options='--client_encoding=UTF8'";
>>>>>>> 89e2413e1d6ef326f56158d86625528e27a90c23
        $conn = @pg_connect($connStr);

        if (!$conn) {
            throw new RuntimeException("Koneksi PostgreSQL gagal. Periksa host/port/db/user/pass & ekstensi pgsql.");
        }
        return $conn;
    }

    function qparams(string $sql, array $params) {
        $conn = get_pg_connection();
        $res = @pg_query_params($conn, $sql, $params);
        if ($res === false) {
            throw new RuntimeException("Query gagal: " . pg_last_error($conn));
        }
        return $res;
    }

    function q(string $sql) {
        $conn = get_pg_connection();
        $res = @pg_query($conn, $sql);
        if ($res === false) {
            throw new RuntimeException("Query gagal: " . pg_last_error($conn));
        }
        return $res;
    }
?>