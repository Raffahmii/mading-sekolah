<?php
require "../config/db.php";

if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

$id = $_GET['id'];

// Get mading info for deleting image
$stmt = $db->prepare("SELECT foto FROM mading WHERE id = :id");
$stmt->execute([':id' => $id]);
$mading = $stmt->fetch(PDO::FETCH_ASSOC);

// Delete the image file if exists
if ($mading && $mading['foto'] && file_exists("../uploads/" . $mading['foto'])) {
    unlink("../uploads/" . $mading['foto']);
}

// Delete from database
$stmt = $db->prepare("DELETE FROM mading WHERE id = :id");
$stmt->execute([':id' => $id]);

// Set success message
$_SESSION['success'] = "Mading berhasil dihapus!";

// Redirect back
header("Location: dashboard.php");
exit;