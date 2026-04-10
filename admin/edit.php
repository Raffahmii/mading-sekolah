<?php
require "../config/db.php";

if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

/* =============================
   VALIDASI ID
============================= */
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: dashboard.php");
    exit;
}

$id = (int) $_GET['id'];

/* =============================
   AMBIL DATA MADiNG
============================= */
$stmt = $db->prepare("SELECT * FROM mading WHERE id = :id");
$stmt->execute([':id' => $id]);
$mading = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$mading) {
    header("Location: dashboard.php");
    exit;
}

/* =============================
   UPDATE MADiNG (FIXED TOTAL)
============================= */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $judul = trim($_POST['judul']);
    $isi   = trim($_POST['isi']);
    $foto  = $mading['foto'];

    if ($judul === '' || $isi === '') {
        $error = "Judul dan isi tidak boleh kosong!";
    } else {

        // Hapus foto jika dicentang
        if (isset($_POST['hapus_foto']) && $foto) {
            if (file_exists("../uploads/" . $foto)) {
                unlink("../uploads/" . $foto);
            }
            $foto = null;
        }

        // Upload foto baru
        if (!empty($_FILES['foto']['name'])) {

            $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];

            if (in_array($_FILES['foto']['type'], $allowed_types)) {

                // hapus foto lama
                if ($foto && file_exists("../uploads/" . $foto)) {
                    unlink("../uploads/" . $foto);
                }

                $ext  = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
                $foto = uniqid('mading_', true) . '.' . $ext;

                move_uploaded_file($_FILES['foto']['tmp_name'], "../uploads/" . $foto);
            }
        }

        $stmt = $db->prepare("
            UPDATE mading 
            SET judul = :j,
                isi = :i,
                foto = :f
            WHERE id = :id
        ");

        $stmt->execute([
            ':j'  => $judul,
            ':i'  => $isi,
            ':f'  => $foto,
            ':id' => $id
        ]);

        header("Location: dashboard.php");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Edit Mading | RAF Mading</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<style>
.editor-container { min-height:400px;border-radius:8px;overflow:hidden; }
.image-preview { max-width:300px;border-radius:8px;border:2px dashed #dee2e6; }
.back-btn { position:fixed;top:20px;left:20px;z-index:1000; }
</style>
</head>

<body class="bg-light">

<a href="dashboard.php" class="btn btn-outline-primary back-btn">
<i class="fas fa-arrow-left me-2"></i>Kembali
</a>

<div class="container py-5">
<div class="row justify-content-center">
<div class="col-lg-10">

<div class="text-center mb-5">
<h1 class="fw-bold text-primary mb-3">
<i class="fas fa-edit me-2"></i>Edit Mading
</h1>
<p class="text-muted">Perbarui konten mading sekolah Anda</p>
</div>

<?php if (isset($error)): ?>
<div class="alert alert-danger"><?= $error ?></div>
<?php endif; ?>

<div class="card border-0 shadow-lg">
<div class="card-body p-4">

<form method="POST" enctype="multipart/form-data">

<div class="mb-4">
<label class="form-label fw-bold fs-5">Judul Mading</label>
<input type="text" name="judul" class="form-control form-control-lg"
value="<?= htmlspecialchars($mading['judul']) ?>" required>
</div>

<div class="mb-4">
<label class="form-label fw-bold fs-5">Isi Mading</label>
<textarea name="isi" id="isiMading"
class="form-control" rows="10"
required><?= htmlspecialchars($mading['isi']) ?></textarea>
</div>

<div class="mb-4">
<label class="form-label fw-bold fs-5">Gambar</label>

<?php if ($mading['foto']): ?>
<div class="mb-3 text-center">
<img src="../uploads/<?= $mading['foto'] ?>"
class="img-fluid rounded image-preview mb-2">
<div class="form-check">
<input class="form-check-input" type="checkbox"
name="hapus_foto" id="hapusFoto">
<label class="form-check-label text-danger"
for="hapusFoto">Hapus gambar ini</label>
</div>
</div>
<?php endif; ?>

<input type="file" name="foto"
class="form-control" accept="image/*">
</div>

<div class="d-flex justify-content-between pt-3 border-top">
<a href="dashboard.php"
class="btn btn-outline-secondary">
<i class="fas fa-times me-2"></i>Batal
</a>

<button type="submit"
class="btn btn-primary">
<i class="fas fa-check me-2"></i>Perbarui Mading
</button>
</div>

</form>
</div>
</div>

</div>
</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
const form = document.querySelector('form');
form.addEventListener('submit', function(e){
    const btn = e.submitter;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Memproses...';
    btn.disabled = true;
});
</script>

</body>
</html>
