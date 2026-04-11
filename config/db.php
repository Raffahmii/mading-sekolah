<?php
session_start();

$db = pg_connect(
    "host=" . getenv("PGHOST") .
    " dbname=" . getenv("PGDATABASE") .
    " user=" . getenv("PGUSER") .
    " password=" . getenv("PGPASSWORD") .
    " port=" . getenv("PGPORT")
);

if (!$db) {
    echo "Koneksi gagal";
}
?>