<?php
session_start();

<?php
$db = pg_connect("host=HOST dbname=DB user=USER password=PASS port=5432");

if (!$db) {
    echo "Koneksi gagal";
}
?>