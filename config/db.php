<?php
session_start();

try {
    $db = new PDO(
        getenv("postgresql://postgres:uWiOCHGUesdkZeFouoxVSnnuOMoEnIPi@postgres.railway.internal:5432/railway")
    );
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Koneksi gagal: " . $e->getMessage());
}