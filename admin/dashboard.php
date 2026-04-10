<?php
require "../config/db.php";

if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

// Get mading data with pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 12;
$offset = ($page - 1) * $per_page;

// Get total count
$total_stmt = $db->query("SELECT COUNT(*) as total FROM mading");
$total = $total_stmt->fetch(PDO::FETCH_ASSOC)['total'];
$total_pages = ceil($total / $per_page);

// Get mading data
$data = $db->query("SELECT * FROM mading ORDER BY tanggal DESC LIMIT $per_page OFFSET $offset")->fetchAll(PDO::FETCH_ASSOC);

// Get statistics - UNIVERSAL VERSION (works with any database)
$today = date('Y-m-d');

// Total Mading
$total_mading_stmt = $db->query("SELECT COUNT(*) as total_mading FROM mading");
$total_mading = $total_mading_stmt->fetch(PDO::FETCH_ASSOC)['total_mading'];

// Active Days
$active_days_stmt = $db->query("SELECT COUNT(DISTINCT DATE(tanggal)) as hari_aktif FROM mading");
$hari_aktif = $active_days_stmt->fetch(PDO::FETCH_ASSOC)['hari_aktif'];

// Today's mading
$today_stmt = $db->prepare("SELECT COUNT(*) as mading_hari_ini FROM mading WHERE DATE(tanggal) = ?");
$today_stmt->execute([$today]);
$mading_hari_ini = $today_stmt->fetch(PDO::FETCH_ASSOC)['mading_hari_ini'];

// Get latest mading for carousel
$latest_mading = $db->query("SELECT * FROM mading ORDER BY tanggal DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);

// Get statistics for chart (last 7 days)
$last_7_days = [];
for ($i = 6; $i >= 0; $i--) {
    $date = date('Y-m-d', strtotime("-$i days"));
    $last_7_days[] = $date;
}

$daily_counts = [];
foreach ($last_7_days as $day) {
    $stmt = $db->prepare("SELECT COUNT(*) as count FROM mading WHERE DATE(tanggal) = ?");
    $stmt->execute([$day]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $daily_counts[] = $result['count'];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | R Mading</title>
    
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Fredoka+One&display=swap" rel="stylesheet">
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <!-- Admin CSS -->
    <link rel="stylesheet" href="../assets/css/admin.css">
    
    <style>
        /* Custom Animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        @keyframes slideInLeft {
            from {
                opacity: 0;
                transform: translateX(-20px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }
        
        .animate-fade-up {
            animation: fadeInUp 0.6s ease-out;
        }
        
        .animate-slide-left {
            animation: slideInLeft 0.5s ease-out;
        }
        
        /* Glow Effect */
        .card-glow:hover {
            box-shadow: 0 0 20px rgba(13, 110, 253, 0.3);
            transform: translateY(-5px);
            transition: all 0.3s ease;
        }
        
        /* Gradient Background */
        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        .gradient-primary {
            background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%);
        }
        
        /* Glass Effect */
        .glass-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        /* Custom Scrollbar */
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
        }
        
        .custom-scrollbar::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }
        
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 10px;
        }
        
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #555;
        }
        
        /* Carousel */
        .carousel-item img {
            height: 300px;
            object-fit: cover;
            border-radius: 12px;
        }
        
        .carousel-caption {
            background: rgba(0, 0, 0, 0.6);
            border-radius: 8px;
            padding: 1rem;
        }
        
        /* Stats Cards */
        .stat-card {
            border-radius: 12px;
            overflow: hidden;
            transition: all 0.3s ease;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
        }
        
        /* Progress Bars */
        .progress {
            height: 8px;
            border-radius: 4px;
        }
        
        /* Badge */
        .badge-pulse {
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.1); }
            100% { transform: scale(1); }
        }
    </style>
</head>
<body>
    <!-- Top Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark gradient-primary shadow-sm fixed-top">
        <div class="container-fluid">
            <!-- Logo/Title -->
            <a class="navbar-brand d-flex align-items-center" href="dashboard.php">
                <i class="fas fa-school fa-xl me-2"></i>
                <div>
                    <span class="fw-bold fs-5" style="font-family: 'Fredoka One', cursive;">SMKN 1 BANJAR</span>
                    <small class="d-block text-white-50" style="font-size: 0.7rem; line-height: 1;">Admin Panel</small>
                </div>
            </a>
            
            <!-- Mobile Toggle -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <!-- Navigation Items -->
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item">
                        <a class="nav-link active" href="dashboard.php">
                            <i class="fas fa-home me-1"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <button class="btn btn-outline-light btn-sm mx-2" data-bs-toggle="modal" data-bs-target="#addModal">
                            <i class="fas fa-plus-circle me-1"></i> Mading Baru
                        </button>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                            <div class="avatar-circle bg-white text-primary d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px; border-radius: 50%;">
                                <i class="fas fa-user"></i>
                            </div>
                            <span>Admin</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="#"><i class="fas fa-cog me-2"></i> Pengaturan</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="../auth/logout.php"><i class="fas fa-sign-out-alt me-2"></i> Logout</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container-fluid mt-5 pt-4">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-lg-2 col-md-3 d-none d-md-block sidebar-container animate-slide-left">
                <div class="sidebar glass-card shadow-sm rounded-3 p-3">
                    <div class="text-center mb-4">
                        <div class="avatar-circle bg-gradient-primary text-white d-inline-flex align-items-center justify-content-center mb-2" style="width: 60px; height: 60px; border-radius: 50%;">
                            <i class="fas fa-user-shield fa-lg"></i>
                        </div>
                        <h6 class="fw-bold mb-0">Administrator</h6>
                        <small class="text-muted">SMKN 1 BANJAR</small>
                    </div>
                    
                    <div class="list-group list-group-flush">
                        <a href="dashboard.php" class="list-group-item list-group-item-action active">
                            <i class="fas fa-tachometer-alt me-2"></i> Dashboard
                        </a>
                        <button class="list-group-item list-group-item-action" data-bs-toggle="modal" data-bs-target="#addModal">
                            <i class="fas fa-plus-circle me-2"></i> Tambah Mading
                        </button>
                        <a href="#" class="list-group-item list-group-item-action">
                            <i class="fas fa-image me-2"></i> Galeri
                        </a>
                        <a href="#" class="list-group-item list-group-item-action">
                            <i class="fas fa-chart-bar me-2"></i> Analitik
                        </a>
                        <a href="#" class="list-group-item list-group-item-action">
                            <i class="fas fa-users me-2"></i> Pengguna
                        </a>
                        <a href="#" class="list-group-item list-group-item-action">
                            <i class="fas fa-cog me-2"></i> Pengaturan
                        </a>
                    </div>
                    
                    <div class="mt-4">
                        <div class="card bg-light border-0">
                            <div class="card-body p-3">
                                <small class="text-muted d-block mb-2">Statistik Hari Ini</small>
                                <div class="d-flex justify-content-between">
                                    <span>Mading Baru:</span>
                                    <span class="badge bg-success"><?= $mading_hari_ini ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Main Content Area -->
            <div class="col-lg-10 col-md-9 ms-auto">
                <!-- Welcome Header -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card gradient-bg text-white shadow-sm border-0 overflow-hidden">
                            <div class="card-body p-4">
                                <div class="row align-items-center">
                                    <div class="col-md-8">
                                        <h3 class="fw-bold mb-2">Selamat Datang, Admin! 👋</h3>
                                        <p class="mb-0 opacity-75">Kelola semua konten mading sekolah dengan mudah dan cepat.</p>
                                    </div>
                                    <div class="col-md-4 text-end">
                                        <div class="bg-white bg-opacity-25 rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                                            <i class="fas fa-school fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Statistics Cards -->
                <div class="row mb-4">
                    <div class="col-md-4 mb-3">
                        <div class="card border-0 shadow-sm animate-fade-up stat-card" style="animation-delay: 0.1s;">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="bg-primary bg-opacity-10 rounded-circle p-3 me-3">
                                        <i class="fas fa-newspaper fa-lg text-primary"></i>
                                    </div>
                                    <div>
                                        <h5 class="fw-bold mb-0"><?= $total_mading ?></h5>
                                        <small class="text-muted">Total Mading</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <div class="card border-0 shadow-sm animate-fade-up stat-card" style="animation-delay: 0.2s;">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="bg-success bg-opacity-10 rounded-circle p-3 me-3">
                                        <i class="fas fa-calendar-day fa-lg text-success"></i>
                                    </div>
                                    <div>
                                        <h5 class="fw-bold mb-0"><?= $hari_aktif ?></h5>
                                        <small class="text-muted">Hari Aktif</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <div class="card border-0 shadow-sm animate-fade-up stat-card" style="animation-delay: 0.3s;">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="bg-warning bg-opacity-10 rounded-circle p-3 me-3">
                                        <i class="fas fa-fire fa-lg text-warning"></i>
                                    </div>
                                    <div>
                                        <h5 class="fw-bold mb-0"><?= $mading_hari_ini ?></h5>
                                        <small class="text-muted">Hari Ini</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Latest Mading Carousel -->
                <?php if (!empty($latest_mading)): ?>
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card border-0 shadow-sm animate-fade-up" style="animation-delay: 0.4s;">
                            <div class="card-header bg-white border-0">
                                <h5 class="fw-bold mb-0">
                                    <i class="fas fa-images me-2 text-primary"></i>Mading Terbaru
                                </h5>
                            </div>
                            <div class="card-body">
                                <div id="madingCarousel" class="carousel slide" data-bs-ride="carousel">
                                    <div class="carousel-inner">
                                        <?php foreach ($latest_mading as $index => $mading): ?>
                                        <div class="carousel-item <?= $index === 0 ? 'active' : '' ?>">
                                            <?php if ($mading['foto']): ?>
                                                <img src="../uploads/<?= htmlspecialchars($mading['foto']) ?>" class="d-block w-100" alt="<?= htmlspecialchars($mading['judul']) ?>">
                                            <?php else: ?>
                                                <div class="bg-light d-block w-100 d-flex align-items-center justify-content-center" style="height: 300px;">
                                                    <i class="fas fa-newspaper fa-4x text-muted"></i>
                                                </div>
                                            <?php endif; ?>
                                            <div class="carousel-caption d-none d-md-block">
                                                <h5><?= htmlspecialchars($mading['judul']) ?></h5>
                                                <p><?= date('d F Y', strtotime($mading['tanggal'])) ?></p>
                                            </div>
                                        </div>
                                        <?php endforeach; ?>
                                    </div>
                                    <button class="carousel-control-prev" type="button" data-bs-target="#madingCarousel" data-bs-slide="prev">
                                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                        <span class="visually-hidden">Previous</span>
                                    </button>
                                    <button class="carousel-control-next" type="button" data-bs-target="#madingCarousel" data-bs-slide="next">
                                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                        <span class="visually-hidden">Next</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
                
                <!-- Analytics Chart -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card border-0 shadow-sm animate-fade-up" style="animation-delay: 0.5s;">
                            <div class="card-header bg-white border-0">
                                <h5 class="fw-bold mb-0">
                                    <i class="fas fa-chart-line me-2 text-primary"></i>Aktivitas 7 Hari Terakhir
                                </h5>
                            </div>
                            <div class="card-body">
                                <canvas id="activityChart" height="100"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Mading List Header -->
                <div class="row mb-3">
                    <div class="col-12">
                        <div class="d-flex justify-content-between align-items-center">
                            <h4 class="fw-bold mb-0">
                                <i class="fas fa-list me-2 text-primary"></i>Daftar Mading
                            </h4>
                            <div class="btn-group" role="group">
                                <button type="button" class="btn btn-outline-primary btn-sm" id="sortAlpha">
                                    <i class="fas fa-sort-alpha-down"></i>
                                </button>
                                <button type="button" class="btn btn-outline-primary btn-sm" id="sortDate">
                                    <i class="fas fa-calendar-alt"></i>
                                </button>
                                <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addModal">
                                    <i class="fas fa-plus"></i> Baru
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Mading Cards Grid -->
                <div class="row custom-scrollbar" style="max-height: 600px; overflow-y: auto;">
                    <?php if (empty($data)): ?>
                        <div class="col-12 text-center py-5">
                            <div class="card border-dashed border-2">
                                <div class="card-body py-5">
                                    <i class="fas fa-newspaper fa-3x text-muted mb-3"></i>
                                    <h5 class="text-muted">Belum ada mading</h5>
                                    <p class="text-muted mb-0">Mulai dengan membuat mading pertama Anda</p>
                                    <button class="btn btn-primary mt-3" data-bs-toggle="modal" data-bs-target="#addModal">
                                        <i class="fas fa-plus me-2"></i>Buat Mading Pertama
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php else: ?>
                        <?php foreach ($data as $m): ?>
                        <div class="col-xl-3 col-lg-4 col-md-6 mb-4">
                            <div class="card border-0 shadow-sm h-100 card-glow animate-fade-up">
                                <!-- Image with Overlay -->
                                <?php if ($m['foto']): ?>
                                <div class="position-relative">
                                    <img src="../uploads/<?= htmlspecialchars($m['foto']) ?>" 
                                         class="card-img-top" 
                                         style="height: 180px; object-fit: cover;"
                                         alt="<?= htmlspecialchars($m['judul']) ?>">
                                    <div class="position-absolute top-0 end-0 m-2">
                                        <span class="badge bg-primary">
                                            <i class="fas fa-calendar me-1"></i>
                                            <?= date('d M', strtotime($m['tanggal'])) ?>
                                        </span>
                                    </div>
                                </div>
                                <?php else: ?>
                                <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 180px;">
                                    <i class="fas fa-newspaper fa-3x text-muted"></i>
                                </div>
                                <?php endif; ?>
                                
                                <!-- Card Body -->
                                <div class="card-body d-flex flex-column">
                                    <h6 class="card-title fw-bold mb-2 text-truncate">
                                        <?= htmlspecialchars($m['judul']) ?>
                                    </h6>
                                    <p class="card-text small text-muted flex-grow-1">
                                        <?= substr(strip_tags($m['isi']), 0, 80) ?>...
                                    </p>
                                    
                                    <!-- Action Buttons -->
                                    <div class="d-flex justify-content-between align-items-center mt-2">
                                        <small class="text-muted">
                                            <i class="fas fa-clock me-1"></i>
                                            <?= date('H:i', strtotime($m['tanggal'])) ?>
                                        </small>
                                        <div class="btn-group" role="group">
                                            <a href="edit.php?id=<?= $m['id'] ?>" 
                                               class="btn btn-outline-primary btn-sm" 
                                               data-bs-toggle="tooltip" 
                                               title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="preview.php?id=<?= $m['id'] ?>" 
                                               class="btn btn-outline-info btn-sm"
                                               data-bs-toggle="tooltip" 
                                               title="Preview">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <button type="button" 
                                                    class="btn btn-outline-danger btn-sm delete-mading"
                                                    data-id="<?= $m['id'] ?>"
                                                    data-title="<?= htmlspecialchars($m['judul']) ?>"
                                                    data-bs-toggle="tooltip" 
                                                    title="Hapus">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                
                <!-- Pagination -->
                <?php if ($total_pages > 1): ?>
                <div class="row mt-4">
                    <div class="col-12">
                        <nav aria-label="Page navigation">
                            <ul class="pagination justify-content-center">
                                <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                                    <a class="page-link" href="?page=<?= $page - 1 ?>">
                                        <i class="fas fa-chevron-left"></i>
                                    </a>
                                </li>
                                
                                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                                    <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                                </li>
                                <?php endfor; ?>
                                
                                <li class="page-item <?= $page >= $total_pages ? 'disabled' : '' ?>">
                                    <a class="page-link" href="?page=<?= $page + 1 ?>">
                                        <i class="fas fa-chevron-right"></i>
                                    </a>
                                </li>
                            </ul>
                        </nav>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Add Mading Modal -->
    <div class="modal fade" id="addModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header gradient-bg text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-plus-circle me-2"></i>Tambah Mading Baru
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="addMadingForm" action="tambah.php" method="POST" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Judul Mading</label>
                            <input type="text" name="judul" class="form-control" placeholder="Masukkan judul mading" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">Isi Mading</label>
                            <textarea name="isi" class="form-control" rows="4" placeholder="Tulis isi mading di sini..." required></textarea>
                            <small class="text-muted">Gunakan format sederhana atau <a href="#" id="openFullEditor">buka editor lengkap</a></small>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">Gambar Mading (Opsional)</label>
                            <div class="upload-area border-dashed rounded-3 p-4 text-center">
                                <i class="fas fa-cloud-upload-alt fa-2x text-muted mb-3"></i>
                                <h6 class="mb-2">Seret & Lepas Gambar</h6>
                                <p class="text-muted mb-3">atau klik untuk memilih file</p>
                                <input type="file" name="foto" class="form-control" accept="image/*" id="imageUpload">
                                <div class="mt-3" id="imagePreview"></div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">Kategori</label>
                            <div class="d-flex flex-wrap gap-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="kategori[]" value="informasi" id="cat1">
                                    <label class="form-check-label" for="cat1">Informasi</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="kategori[]" value="kegiatan" id="cat2">
                                    <label class="form-check-label" for="cat2">Kegiatan</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="kategori[]" value="pengumuman" id="cat3">
                                    <label class="form-check-label" for="cat3">Pengumuman</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="kategori[]" value="prestasi" id="cat4">
                                    <label class="form-check-label" for="cat4">Prestasi</label>
                                </div>
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-between">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                <i class="fas fa-times me-2"></i>Batal
                            </button>
                            <button type="submit" name="simpan" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Simpan Mading
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-exclamation-triangle me-2"></i>Konfirmasi Hapus
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Apakah Anda yakin ingin menghapus mading:</p>
                    <h6 id="deleteMadingTitle" class="fw-bold"></h6>
                    <p class="text-danger"><small>Tindakan ini tidak dapat dibatalkan!</small></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <a href="#" id="confirmDelete" class="btn btn-danger">
                        <i class="fas fa-trash me-2"></i>Ya, Hapus
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer mt-5 py-3 bg-light border-top">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-school text-primary me-2"></i>
                        <span class="fw-bold" style="font-family: 'Fredoka One', cursive;">M Raffa Izzel H.</span>
                        <span class="text-muted ms-2">© <?= date('Y') ?> All rights reserved</span>
                    </div>
                </div>
                <div class="col-md-6 text-end">
                    <div class="d-flex justify-content-end align-items-center">
                        <small class="text-muted me-3">v2.0.1</small>
                        <div class="btn-group">
                            <a href="#" class="btn btn-sm btn-outline-secondary">
                                <i class="fab fa-github"></i>
                            </a>
                            <a href="#" class="btn btn-sm btn-outline-secondary">
                                <i class="fas fa-question-circle"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap & JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
        // Initialize tooltips
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        const tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
        
        // Delete confirmation
        document.querySelectorAll('.delete-mading').forEach(button => {
            button.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                const title = this.getAttribute('data-title');
                
                document.getElementById('deleteMadingTitle').textContent = title;
                document.getElementById('confirmDelete').href = `hapus.php?id=${id}`;
                
                const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
                deleteModal.show();
            });
        });
        
        // Image upload preview
        document.getElementById('imageUpload').addEventListener('change', function(e) {
            const preview = document.getElementById('imagePreview');
            preview.innerHTML = '';
            
            if (this.files && this.files[0]) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.className = 'img-thumbnail mt-2';
                    img.style.maxWidth = '200px';
                    preview.appendChild(img);
                }
                
                reader.readAsDataURL(this.files[0]);
            }
        });
        
        // Drag and drop for image upload
        const uploadArea = document.querySelector('.upload-area');
        const fileInput = document.getElementById('imageUpload');
        
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            uploadArea.addEventListener(eventName, preventDefaults, false);
        });
        
        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }
        
        ['dragenter', 'dragover'].forEach(eventName => {
            uploadArea.addEventListener(eventName, highlight, false);
        });
        
        ['dragleave', 'drop'].forEach(eventName => {
            uploadArea.addEventListener(eventName, unhighlight, false);
        });
        
        function highlight() {
            uploadArea.classList.add('bg-primary', 'bg-opacity-10');
        }
        
        function unhighlight() {
            uploadArea.classList.remove('bg-primary', 'bg-opacity-10');
        }
        
        uploadArea.addEventListener('drop', handleDrop, false);
        
        function handleDrop(e) {
            const dt = e.dataTransfer;
            const files = dt.files;
            fileInput.files = files;
            
            // Trigger change event
            const event = new Event('change');
            fileInput.dispatchEvent(event);
        }
        
        // Form submission with loading
        document.getElementById('addMadingForm').addEventListener('submit', function(e) {
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Menyimpan...';
            submitBtn.disabled = true;
            
            // You can add AJAX submission here for better UX
        });
        
        // Open full editor
        document.getElementById('openFullEditor').addEventListener('click', function(e) {
            e.preventDefault();
            // Redirect to full editor page
            window.location.href = 'tambah.php?full=1';
        });
        
        // Animation on scroll
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };
        
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animate-fade-up');
                }
            });
        }, observerOptions);
        
        // Observe cards for animation
        document.querySelectorAll('.card').forEach(card => {
            observer.observe(card);
        });
        
        // Sort functionality
        document.getElementById('sortAlpha').addEventListener('click', function() {
            window.location.href = '?sort=alpha&page=1';
        });
        
        document.getElementById('sortDate').addEventListener('click', function() {
            window.location.href = '?sort=date&page=1';
        });
        
        // Initialize Chart
        const ctx = document.getElementById('activityChart').getContext('2d');
        const activityChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: <?= json_encode(array_map(function($date) {
                    return date('d M', strtotime($date));
                }, $last_7_days)) ?>,
                datasets: [{
                    label: 'Jumlah Mading',
                    data: <?= json_encode($daily_counts) ?>,
                    backgroundColor: 'rgba(13, 110, 253, 0.1)',
                    borderColor: 'rgba(13, 110, 253, 1)',
                    borderWidth: 2,
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });
        
        // Auto refresh carousel
        const carousel = document.getElementById('madingCarousel');
        if (carousel) {
            const carouselInstance = new bootstrap.Carousel(carousel, {
                interval: 5000,
                wrap: true
            });
        }
    </script>
</body>
</html>