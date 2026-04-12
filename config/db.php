<?php
try {
    $conn = new PDO(
        "pgsql:host=" . getenv("PGHOST") . 
        ";port=" . getenv("PGPORT") . 
        ";dbname=" . getenv("PGDATABASE"),
        getenv("PGUSER"),
        getenv("PGPASSWORD")
    );

    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch (PDOException $e) {
    echo "Koneksi gagal: " . $e->getMessage();
}