<?php
session_start();

try {
    $db = new PDO(
        "pgsql:host=localhost;port=5432;dbname=raf_mading",
        "postgres",
        "postgres"
    );
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Koneksi gagal: " . $e->getMessage());
}