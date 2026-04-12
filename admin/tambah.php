<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require "../config/db.php";

if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $judul = trim($_POST['judul']);
    $isi   = trim($_POST['isi']);

    // ✅ ambil penulis dari session login
    $penulis = trim($_POST['penulis']);

    // ❌ HAPUS kategori (karena ga ada di DB)

    // Handle file upload
    $foto = null;

    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === 0) {

        $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $file_type = $_FILES['foto']['type'];

        if (in_array($file_type, $allowed_types)) {

            $extension = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
            $foto = uniqid() . '.' . $extension;

            $upload_path = "../uploads/" . $foto;

            move_uploaded_file($_FILES['foto']['tmp_name'], $upload_path);
        }
    }

    try {

        // ✅ TAMBAH penulis ke query
        $stmt = $db->prepare(
            "INSERT INTO mading (judul, isi, penulis, foto, tanggal) 
             VALUES (:j, :i, :p, :f, NOW())"
        );

        $stmt->execute([
            ':j' => $judul,
            ':i' => $isi,
            ':p' => $penulis,
            ':f' => $foto,
        ]);

        $_SESSION['success'] = "Mading berhasil ditambahkan!";
        header("Location: dashboard.php");
        exit;

    } catch (PDOException $e) {

        echo "ERROR DATABASE: " . $e->getMessage();
        exit;
    }
}
?>