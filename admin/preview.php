<?php
require "../config/db.php";

if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

$id = $_GET['id'];

$stmt = $db->prepare("SELECT * FROM mading WHERE id = :id");
$stmt->execute([':id' => $id]);
$mading = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$mading) {
    header("Location: dashboard.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Preview Mading | RAF Mading</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f8f9fa; }
        .preview-container { max-width: 800px; margin: 2rem auto; }
        .back-btn { position: fixed; top: 20px; left: 20px; }
    </style>
</head>
<body>
    <a href="dashboard.php" class="btn btn-outline-primary back-btn">
        <i class="fas fa-arrow-left"></i>
    </a>
    
    <div class="preview-container">
        <div class="card shadow-lg">
            <?php if ($mading['foto']): ?>
            <img src="../uploads/<?= $mading['foto'] ?>" class="card-img-top" alt="<?= $mading['judul'] ?>">
            <?php endif; ?>
            <div class="card-body p-5">
                <h1 class="card-title mb-4"><?= htmlspecialchars($mading['judul']) ?></h1>
                <div class="text-muted mb-4">
                    <i class="fas fa-calendar me-1"></i>
                    <?= date('d F Y H:i', strtotime($mading['tanggal'])) ?>
                </div>
                <div class="card-text">
                    <?= nl2br(htmlspecialchars($mading['isi'])) ?>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        // Print functionality
        function printPreview() {
            window.print();
        }
        
        // Close on ESC
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                window.history.back();
            }
        });
    </script>
</body>
</html>