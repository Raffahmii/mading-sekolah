<?php
require "../config/db.php";

// Cek login user
if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'user') {
    header("Location: ../auth/login.php");
    exit;
}

// Get mading data with pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 12;
$offset = ($page - 1) * $per_page;

// Sorting
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'date';
$order_by = ($sort == 'alpha') ? 'judul ASC' : 'tanggal DESC';

// Get total count
$total_stmt = $db->query("SELECT COUNT(*) as total FROM mading");
$total = $total_stmt->fetch(PDO::FETCH_ASSOC)['total'];
$total_pages = ceil($total / $per_page);

// Get mading data with sorting
$data = $db->query("SELECT * FROM mading ORDER BY $order_by LIMIT $per_page OFFSET $offset")->fetchAll(PDO::FETCH_ASSOC);

// Get statistics
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

// ========== CHART 7 HARI TERAKHIR ==========
$last_7_days = [];
$daily_counts = [];

for ($i = 6; $i >= 0; $i--) {
    $date = date('Y-m-d', strtotime("-$i days"));
    $last_7_days[] = date('d M', strtotime($date));
    $stmt = $db->prepare("SELECT COUNT(*) as count FROM mading WHERE DATE(tanggal) = ?");
    $stmt->execute([$date]);
    $daily_counts[] = (int)$stmt->fetch(PDO::FETCH_ASSOC)['count'];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=yes">
    <title>SMK NEGERI 1 BANJAR | Mading Digital</title>
    
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Fredoka+One&display=swap" rel="stylesheet">
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <!-- Main CSS & User CSS -->
    <link rel="stylesheet" href="../assets/css/main.css">
    <link rel="stylesheet" href="../assets/css/user.css">
    
    <style>
        /* Custom Styles untuk User Dashboard */
        :root {
            --user-primary: #4361ee;
            --user-secondary: #3f37c9;
            --user-accent: #4895ef;
            --user-gradient: linear-gradient(135deg, #4361ee, #3a0ca3);
        }
        
        * {
            box-sizing: border-box;
        }
        
        body {
            background: #f5f7fb;
            font-family: 'Poppins', sans-serif;
            overflow-x: hidden;
            width: 100%;
            position: relative;
        }
        
        /* Navbar Styling - RESPONSIF */
        .navbar-user {
            background: var(--user-gradient) !important;
            box-shadow: 0 4px 20px rgba(67, 97, 238, 0.2);
            padding: 0.7rem 0;
        }
        
        .navbar-user .navbar-brand {
            font-family: 'Fredoka One', cursive;
            font-size: 1.2rem;
            color: white;
            display: flex;
            align-items: center;
        }
        
        .navbar-user .navbar-brand i {
            font-size: 1.5rem;
            margin-right: 0.5rem;
        }
        
        .navbar-user .nav-link {
            color: rgba(255,255,255,0.9) !important;
            font-weight: 500;
            padding: 0.5rem 0.8rem !important;
            border-radius: 8px;
            transition: all 0.3s ease;
            font-size: 0.95rem;
        }
        
        /* Tombol logout */
        .btn-logout {
            color: rgba(255,255,255,0.9) !important;
            font-weight: 500;
            padding: 0.5rem 1rem !important;
            border-radius: 8px;
            transition: all 0.3s ease;
            font-size: 0.95rem;
            background: transparent;
            border: none;
            text-decoration: none;
            display: inline-block;
        }
        
        .btn-logout:hover {
            background: rgba(255,255,255,0.1);
            color: white !important;
        }
        
        /* Navbar toggler */
        .navbar-toggler {
            border: none;
            padding: 0.5rem;
        }
        
        .navbar-toggler:focus {
            box-shadow: none;
        }
        
        .navbar-toggler-icon {
            filter: brightness(0) invert(1);
        }
        
        /* Sidebar Styling - RESPONSIF */
        .sidebar-user {
            background: white;
            border-radius: 20px;
            padding: 1.2rem;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            position: sticky;
            top: 90px;
            transition: all 0.3s ease;
            width: 100%;
        }
        
        .sidebar-user .profile-section {
            text-align: center;
            padding-bottom: 1rem;
            border-bottom: 2px solid #f0f2f5;
            margin-bottom: 1rem;
        }
        
        .sidebar-user .avatar {
            width: 70px;
            height: 70px;
            background: var(--user-gradient);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 0.8rem;
            color: white;
            font-size: 1.8rem;
            box-shadow: 0 10px 20px rgba(67, 97, 238, 0.3);
        }
        
        .sidebar-user .menu-item {
            display: flex;
            align-items: center;
            padding: 0.6rem 0.8rem;
            color: #495057;
            border-radius: 12px;
            transition: all 0.3s ease;
            margin-bottom: 0.3rem;
            text-decoration: none;
            font-size: 0.95rem;
        }
        
        .sidebar-user .menu-item i {
            width: 22px;
            margin-right: 10px;
            font-size: 1rem;
        }
        
        /* Welcome Card - RESPONSIF */
        .welcome-card {
            background: linear-gradient(135deg, #4361ee, #3a0ca3);
            border-radius: 20px;
            padding: 1.5rem;
            color: white;
            margin-bottom: 1.2rem;
            box-shadow: 0 10px 30px rgba(67, 97, 238, 0.2);
            width: 100%;
        }
        
        .welcome-card h2 {
            color: white;
            font-size: 1.5rem;
            margin-bottom: 0.5rem;
        }
        
        .welcome-card p {
            color: rgba(255,255,255,0.9);
            font-size: 0.9rem;
            margin-bottom: 0;
        }
        
        .welcome-card .fa-school {
            font-size: 3rem;
            opacity: 0.5;
        }
        
        /* Stat Cards - RESPONSIF */
        .stat-card {
            background: white;
            border-radius: 16px;
            padding: 1.2rem;
            box-shadow: 0 5px 20px rgba(0,0,0,0.03);
            transition: all 0.3s ease;
            border-left: 4px solid var(--user-primary);
            height: 100%;
            width: 100%;
        }
        
        .stat-icon {
            width: 45px;
            height: 45px;
            background: rgba(67, 97, 238, 0.1);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--user-primary);
            font-size: 1.3rem;
            flex-shrink: 0;
        }
        
        .stat-card h3 {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 0.1rem;
            color: #212529;
            line-height: 1.2;
        }
        
        .stat-card small {
            color: #6c757d;
            font-size: 0.75rem;
            font-weight: 500;
            display: block;
        }
        
        .stat-card.success {
            border-left-color: #38b000;
        }
        .stat-card.warning {
            border-left-color: #f48c06;
        }
        
        /* Cards Mading - RESPONSIF */
        .mading-card {
            background: white;
            border-radius: 18px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.03);
            transition: all 0.3s ease;
            height: 100%;
            border: 1px solid rgba(0,0,0,0.03);
            cursor: pointer;
            width: 100%;
        }
        
        .mading-card .card-img-wrapper {
            height: 160px;
            overflow: hidden;
            position: relative;
        }
        
        .mading-card img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: all 0.5s ease;
        }
        
        .mading-card .card-date {
            position: absolute;
            top: 8px;
            right: 8px;
            background: rgba(255,255,255,0.95);
            padding: 0.25rem 0.6rem;
            border-radius: 30px;
            font-size: 0.7rem;
            font-weight: 600;
            color: var(--user-primary);
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            white-space: nowrap;
        }
        
        .mading-card .card-content {
            padding: 1rem;
        }
        
        .mading-card .card-title {
            font-weight: 600;
            font-size: 0.95rem;
            margin-bottom: 0.4rem;
            color: #212529;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            line-height: 1.4;
            height: 2.7rem;
        }
        
        .mading-card .card-text {
            color: #6c757d;
            font-size: 0.8rem;
            margin-bottom: 0.6rem;
            line-height: 1.4;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            height: 2.3rem;
        }
        
        .read-more {
            color: var(--user-primary);
            font-weight: 600;
            font-size: 0.8rem;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }
        
        /* Carousel - RESPONSIF */
        .carousel-user {
            border-radius: 18px;
            overflow: hidden;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }
        
        .carousel-user .carousel-item {
            height: 250px;
        }
        
        .carousel-user .carousel-item img {
            object-fit: cover;
            height: 100%;
            width: 100%;
        }
        
        .carousel-user .carousel-caption {
            background: linear-gradient(to top, rgba(0,0,0,0.8), transparent);
            left: 0;
            right: 0;
            bottom: 0;
            padding: 1.2rem;
            text-align: left;
        }
        
        .carousel-user .carousel-caption h3 {
            font-size: 1.2rem;
            margin-bottom: 0.2rem;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        
        .carousel-user .carousel-caption p {
            font-size: 0.8rem;
            opacity: 0.9;
            margin-bottom: 0;
        }
        
        .carousel-indicators {
            margin-bottom: 0.5rem;
        }
        
        .carousel-indicators button {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            margin: 0 4px;
        }
        
        /* Chart Container - RESPONSIF */
        .chart-container {
            background: white;
            border-radius: 18px;
            padding: 1.2rem;
            box-shadow: 0 5px 15px rgba(0,0,0,0.03);
            margin-bottom: 1.2rem;
            width: 100%;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }
        
        .chart-container::-webkit-scrollbar {
            height: 4px;
        }
        
        .chart-container::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 10px;
        }
        
        #activityChart {
            min-width: 350px;
            height: 120px !important;
        }
        
        /* Filter Buttons - RESPONSIF */
        .filter-btn {
            border: 2px solid #e0e0e0;
            background: white;
            color: #6c757d;
            padding: 0.35rem 0.9rem;
            border-radius: 30px;
            font-weight: 500;
            transition: all 0.3s ease;
            text-decoration: none;
            font-size: 0.8rem;
            white-space: nowrap;
        }
        
        /* Modal Detail - RESPONSIF */
        .modal-detail .modal-content {
            border-radius: 24px;
            overflow: hidden;
            border: none;
            margin: 1rem;
        }
        
        .modal-detail .modal-header {
            background: var(--user-gradient);
            color: white;
            border: none;
            padding: 1rem 1.2rem;
        }
        
        .modal-detail .modal-body {
            padding: 1.2rem;
        }
        
        .modal-detail .modal-img {
            width: 100%;
            max-height: 250px;
            object-fit: cover;
            border-radius: 16px;
            margin-bottom: 1rem;
        }
        
        /* Pagination - RESPONSIF */
        .pagination-user .page-link {
            border: none;
            background: white;
            color: #6c757d;
            border-radius: 10px;
            margin: 0 2px;
            padding: 0.4rem 0.7rem;
            font-weight: 500;
            font-size: 0.85rem;
        }
        
        /* Custom Scrollbar */
        .custom-scrollbar::-webkit-scrollbar {
            width: 4px;
        }
        
        .custom-scrollbar::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }
        
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 10px;
        }
        
        /* Animations */
        .animate-fade-up {
            opacity: 0;
            animation: fadeInUp 0.4s ease-out forwards;
        }
        
        @keyframes fadeInUp {
            0% { opacity: 0; transform: translateY(15px); }
            100% { opacity: 1; transform: translateY(0); }
        }
        
        /* ========== RESPONSIVE BREAKPOINTS ========== */
        
        /* HP kecil (<350px) */
        @media (max-width: 350px) {
            .navbar-user .navbar-brand {
                font-size: 1rem;
            }
            
            .navbar-user .navbar-brand i {
                font-size: 1.2rem;
            }
            
            .btn-logout {
                padding: 0.4rem 0.6rem !important;
                font-size: 0.85rem;
            }
            
            .sidebar-user {
                padding: 1rem;
            }
            
            .sidebar-user .avatar {
                width: 60px;
                height: 60px;
                font-size: 1.5rem;
            }
            
            .sidebar-user .menu-item {
                padding: 0.5rem 0.6rem;
                font-size: 0.85rem;
            }
            
            .welcome-card {
                padding: 1.2rem;
            }
            
            .welcome-card h2 {
                font-size: 1.2rem;
            }
            
            .welcome-card p {
                font-size: 0.8rem;
            }
            
            .stat-card {
                padding: 1rem;
            }
            
            .stat-icon {
                width: 40px;
                height: 40px;
                font-size: 1.1rem;
            }
            
            .stat-card h3 {
                font-size: 1.2rem;
            }
            
            .stat-card small {
                font-size: 0.65rem;
            }
            
            .mading-card .card-img-wrapper {
                height: 140px;
            }
            
            .mading-card .card-content {
                padding: 0.8rem;
            }
            
            .mading-card .card-title {
                font-size: 0.85rem;
                height: 2.4rem;
            }
            
            .mading-card .card-text {
                font-size: 0.75rem;
                height: 2.1rem;
            }
            
            .read-more {
                font-size: 0.75rem;
            }
            
            .carousel-user .carousel-item {
                height: 200px;
            }
            
            .carousel-user .carousel-caption h3 {
                font-size: 1rem;
            }
            
            .filter-btn {
                padding: 0.3rem 0.7rem;
                font-size: 0.75rem;
            }
            
            h4.fw-bold {
                font-size: 1.1rem;
            }
            
            .pagination-user .page-link {
                padding: 0.3rem 0.6rem;
                font-size: 0.8rem;
            }
            
            .footer-user {
                padding: 1rem 0;
                font-size: 0.8rem;
            }
        }
        
        /* HP sedang (350px - 400px) */
        @media (min-width: 351px) and (max-width: 400px) {
            .navbar-user .navbar-brand {
                font-size: 1.1rem;
            }
            
            .welcome-card h2 {
                font-size: 1.3rem;
            }
            
            .stat-card h3 {
                font-size: 1.3rem;
            }
            
            .mading-card .card-img-wrapper {
                height: 150px;
            }
        }
        
        /* Tablet */
        @media (min-width: 768px) and (max-width: 991px) {
            .col-md-4 {
                flex: 0 0 50%;
                max-width: 50%;
            }
        }
        
        /* Landscape HP */
        @media (max-height: 500px) and (orientation: landscape) {
            .sidebar-user {
                position: relative;
                top: 0;
                margin-bottom: 1rem;
            }
            
            .carousel-user .carousel-item {
                height: 200px;
            }
        }
    </style>
</head>
<body>
    <!-- Top Navigation -->
    <nav class="navbar navbar-expand-lg navbar-user fixed-top">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="dashboard.php">
                <i class="fas fa-school"></i>
                <span>SMKN 1 BANJAR</span>
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarUser">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarUser">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item">
                        <a class="nav-link active" href="dashboard.php">
                            <i class="fas fa-home me-1"></i> Beranda
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">
                            <i class="fas fa-images me-1"></i> Galeri
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">
                            <i class="fas fa-cog me-1"></i> Pengaturan
                        </a>
                    </li>
                    <li class="nav-item ms-2">
                        <a href="../auth/logout.php" class="btn-logout">
                            <i class="fas fa-sign-out-alt me-1"></i> Logout
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container-fluid mt-5 pt-5 px-2 px-sm-3">
        <div class="row g-3">
            <!-- Sidebar -->
            <div class="col-lg-3">
                <div class="sidebar-user animate-fade-up">
                    <div class="profile-section">
                        <div class="avatar">
                            <i class="fas fa-user-graduate"></i>
                        </div>
                        <h5 class="fw-bold mb-1"><?= htmlspecialchars($_SESSION['username'] ?? 'User') ?></h5>
                        <p class="text-muted small mb-0">Member since <?= date('Y') ?></p>
                    </div>
                    
                    <div class="menu-section">
                        <a href="dashboard.php" class="menu-item active">
                            <i class="fas fa-tachometer-alt"></i>
                            <span>Beranda</span>
                        </a>
                        <a href="#" class="menu-item">
                            <i class="fas fa-images"></i>
                            <span>Galeri</span>
                        </a>
                        <a href="#" class="menu-item">
                            <i class="fas fa-bookmark"></i>
                            <span>Mading Tersimpan</span>
                        </a>
                        <a href="#" class="menu-item">
                            <i class="fas fa-cog"></i>
                            <span>Pengaturan</span>
                        </a>
                        <a href="#" class="menu-item">
                            <i class="fas fa-question-circle"></i>
                            <span>Bantuan</span>
                        </a>
                    </div>
                    
                    <div class="mt-3 p-3 bg-light rounded-4">
                        <small class="text-muted d-block mb-2">Info Hari Ini</small>
                        <div class="d-flex align-items-center justify-content-between">
                            <span class="fw-bold small"><?= date('d F Y') ?></span>
                            <span class="badge bg-primary"><?= $mading_hari_ini ?> baru</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Main Content Area -->
            <div class="col-lg-9">
                <!-- Welcome Banner -->
                <div class="welcome-card animate-fade-up">
                    <div class="row align-items-center">
                        <div class="col-8">
                            <h2 class="fw-bold">Halo, <?= explode(' ', $_SESSION['username'] ?? 'User')[0] ?>! 👋</h2>
                            <p class="mb-0">Selamat datang di Mading Digital</p>
                        </div>
                        <div class="col-4 text-end">
                            <i class="fas fa-school"></i>
                        </div>
                    </div>
                </div>
                
                <!-- Statistics Cards -->
                <div class="row g-2 g-sm-3 mb-4">
                    <div class="col-4">
                        <div class="stat-card d-flex align-items-center animate-fade-up">
                            <div class="stat-icon me-2">
                                <i class="fas fa-newspaper"></i>
                            </div>
                            <div>
                                <h3 class="fw-bold mb-0"><?= $total_mading ?></h3>
                                <small>Total Mading</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="stat-card success d-flex align-items-center animate-fade-up">
                            <div class="stat-icon me-2">
                                <i class="fas fa-calendar-check"></i>
                            </div>
                            <div>
                                <h3 class="fw-bold mb-0"><?= $hari_aktif ?></h3>
                                <small>Hari Aktif</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="stat-card warning d-flex align-items-center animate-fade-up">
                            <div class="stat-icon me-2">
                                <i class="fas fa-fire"></i>
                            </div>
                            <div>
                                <h3 class="fw-bold mb-0"><?= $mading_hari_ini ?></h3>
                                <small>Trending</small>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Carousel -->
                <?php if (!empty($latest_mading)): ?>
                <div class="mb-4 animate-fade-up">
                    <div id="madingCarousel" class="carousel slide carousel-user" data-bs-ride="carousel">
                        <div class="carousel-indicators">
                            <?php foreach ($latest_mading as $index => $mading): ?>
                            <button type="button" data-bs-target="#madingCarousel" data-bs-slide-to="<?= $index ?>" class="<?= $index === 0 ? 'active' : '' ?>"></button>
                            <?php endforeach; ?>
                        </div>
                        <div class="carousel-inner">
                            <?php foreach ($latest_mading as $index => $mading): ?>
                            <div class="carousel-item <?= $index === 0 ? 'active' : '' ?>">
                                <?php if ($mading['foto']): ?>
                                    <img src="../uploads/<?= htmlspecialchars($mading['foto']) ?>" class="d-block w-100" alt="<?= htmlspecialchars($mading['judul']) ?>">
                                <?php else: ?>
                                    <div class="d-block w-100 bg-light d-flex align-items-center justify-content-center">
                                        <i class="fas fa-newspaper fa-3x text-muted"></i>
                                    </div>
                                <?php endif; ?>
                                <div class="carousel-caption">
                                    <h3><?= htmlspecialchars($mading['judul']) ?></h3>
                                    <p><?= date('d M Y', strtotime($mading['tanggal'])) ?></p>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <button class="carousel-control-prev" type="button" data-bs-target="#madingCarousel" data-bs-slide="prev">
                            <span class="carousel-control-prev-icon"></span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#madingCarousel" data-bs-slide="next">
                            <span class="carousel-control-next-icon"></span>
                        </button>
                    </div>
                </div>
                <?php endif; ?>
                
                <!-- Chart Container -->
                <div class="chart-container animate-fade-up">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h5 class="fw-bold mb-0 small">
                            <i class="fas fa-chart-line me-1 text-primary"></i>7 Hari Terakhir
                        </h5>
                        <span class="badge bg-light text-dark px-2 py-1 small">Update real-time</span>
                    </div>
                    <canvas id="activityChart"></canvas>
                </div>
                
                <!-- Mading List Header with Filters -->
                <div class="d-flex flex-wrap justify-content-between align-items-center my-3">
                    <h4 class="fw-bold mb-0">
                        <i class="fas fa-stream me-2 text-primary"></i>Jelajahi
                    </h4>
                    <div class="d-flex gap-2 mt-2 mt-sm-0">
                        <a href="?sort=date" class="filter-btn <?= $sort == 'date' ? 'active' : '' ?>">
                            <i class="fas fa-calendar-alt me-1"></i> Terbaru
                        </a>
                        <a href="?sort=alpha" class="filter-btn <?= $sort == 'alpha' ? 'active' : '' ?>">
                            <i class="fas fa-sort-alpha-down me-1"></i> A-Z
                        </a>
                    </div>
                </div>
                
                <!-- Mading Cards Grid - Scroll -->
                <div class="row g-2 g-sm-3 mb-4 custom-scrollbar" style="max-height: 500px; overflow-y: auto; padding-right: 5px;">
                    <?php if (empty($data)): ?>
                        <div class="col-12">
                            <div class="text-center py-4 bg-white rounded-4">
                                <i class="fas fa-newspaper fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted small">Belum ada mading</h5>
                                <p class="text-muted small mb-0">Nantikan informasi terbaru!</p>
                            </div>
                        </div>
                    <?php else: ?>
                        <?php foreach ($data as $index => $m): ?>
                        <div class="col-6 col-md-4 col-lg-3">
                            <div class="mading-card animate-fade-up" onclick="showDetail(<?= htmlspecialchars(json_encode($m)) ?>)">
                                <div class="card-img-wrapper">
                                    <?php if ($m['foto']): ?>
                                        <img src="../uploads/<?= htmlspecialchars($m['foto']) ?>" alt="<?= htmlspecialchars($m['judul']) ?>">
                                    <?php else: ?>
                                        <div class="bg-light d-flex align-items-center justify-content-center h-100">
                                            <i class="fas fa-newspaper fa-2x text-muted"></i>
                                        </div>
                                    <?php endif; ?>
                                    <span class="card-date">
                                        <?= date('d M', strtotime($m['tanggal'])) ?>
                                    </span>
                                </div>
                                <div class="card-content">
                                    <h6 class="card-title"><?= htmlspecialchars($m['judul']) ?></h6>
                                    <p class="card-text"><?= substr(strip_tags($m['isi']), 0, 50) ?>...</p>
                                    <span class="read-more">
                                        Baca <i class="fas fa-arrow-right"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                
                <!-- Pagination -->
                <?php if ($total_pages > 1): ?>
                <nav aria-label="Page navigation" class="mt-2">
                    <ul class="pagination justify-content-center pagination-user">
                        <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                            <a class="page-link" href="?page=<?= $page - 1 ?>&sort=<?= $sort ?>">
                                <i class="fas fa-chevron-left"></i>
                            </a>
                        </li>
                        
                        <?php 
                        $start = max(1, $page - 2);
                        $end = min($total_pages, $page + 2);
                        for ($i = $start; $i <= $end; $i++): 
                        ?>
                        <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                            <a class="page-link" href="?page=<?= $i ?>&sort=<?= $sort ?>"><?= $i ?></a>
                        </li>
                        <?php endfor; ?>
                        
                        <li class="page-item <?= $page >= $total_pages ? 'disabled' : '' ?>">
                            <a class="page-link" href="?page=<?= $page + 1 ?>&sort=<?= $sort ?>">
                                <i class="fas fa-chevron-right"></i>
                            </a>
                        </li>
                    </ul>
                </nav>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Modal Detail Mading -->
    <div class="modal fade modal-detail" id="detailModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-newspaper me-2"></i>Detail Mading
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <img src="" alt="" class="modal-img" id="modalImage" style="display: none;">
                    <h5 class="fw-bold mb-2" id="modalTitle"></h5>
                    <div class="d-flex align-items-center mb-2 small">
                        <i class="far fa-calendar-alt text-primary me-2"></i>
                        <span id="modalDate"></span>
                    </div>
                    <div class="border-top pt-2 small" id="modalContent"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <!-- Footer - RESPONSIVE & KEKINIAN -->
    <footer class="footer-user mt-4">
        <div class="container">
            <!-- Footer Main -->
            <div class="row gy-3 gy-md-4">
                <!-- Brand Section -->
                <div class="col-12 col-md-4">
                    <div class="d-flex flex-column">
                        <div class="d-flex align-items-center mb-2">
                            <div class="rounded-circle bg-primary bg-opacity-10 p-2 me-2">
                                <i class="fas fa-school text-primary"></i>
                            </div>
                            <span class="fw-bold" style="font-family: 'Fredoka One', cursive;">SMKN 1 BANJAR</span>
                        </div>
                        <p class="text-muted small mb-2">
                            Mading Digital - Tempat informasi terbaru seputar kegiatan dan pengumuman sekolah.
                        </p>
                        <div class="d-flex gap-2">
                            <a href="#" class="btn btn-sm btn-outline-primary rounded-circle p-2" style="width: 35px; height: 35px;">
                                <i class="fab fa-instagram"></i>
                            </a>
                            <a href="#" class="btn btn-sm btn-outline-primary rounded-circle p-2" style="width: 35px; height: 35px;">
                                <i class="fab fa-tiktok"></i>
                            </a>
                            <a href="#" class="btn btn-sm btn-outline-primary rounded-circle p-2" style="width: 35px; height: 35px;">
                                <i class="fab fa-youtube"></i>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Quick Links -->
                <div class="col-6 col-md-2">
                    <h6 class="fw-bold mb-2 mb-md-3">Menu</h6>
                    <ul class="list-unstyled small">
                        <li class="mb-2"><a href="dashboard.php" class="text-decoration-none text-muted hover-primary">Beranda</a></li>
                        <li class="mb-2"><a href="#" class="text-decoration-none text-muted hover-primary">Galeri</a></li>
                        <li class="mb-2"><a href="#" class="text-decoration-none text-muted hover-primary">Mading Tersimpan</a></li>
                        <li class="mb-2"><a href="#" class="text-decoration-none text-muted hover-primary">Bantuan</a></li>
                    </ul>
                </div>

                <!-- Information -->
                <div class="col-6 col-md-3">
                    <h6 class="fw-bold mb-2 mb-md-3">Informasi</h6>
                    <ul class="list-unstyled small">
                        <li class="mb-2"><i class="fas fa-map-marker-alt text-primary me-2"></i>Jl. Raya Banjar No. 1</li>
                        <li class="mb-2"><i class="fas fa-phone text-primary me-2"></i>(0265) 123456</li>
                        <li class="mb-2"><i class="fas fa-envelope text-primary me-2"></i>smkn1banjar@sch.id</li>
                    </ul>
                </div>

                <!-- Stats Mini -->
                <div class="col-12 col-md-3">
                    <div class="bg-light rounded-4 p-3">
                        <h6 class="fw-bold mb-2">Statistik Mading</h6>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="small">Total Mading</span>
                            <span class="badge bg-primary"><?= $total_mading ?? '0' ?></span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="small">Hari Aktif</span>
                            <span class="badge bg-success"><?= $hari_aktif ?? '0' ?></span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="small">Hari Ini</span>
                            <span class="badge bg-warning text-dark"><?= $mading_hari_ini ?? '0' ?></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Divider -->
            <hr class="my-3 my-md-4">

            <!-- Bottom Footer -->
            <div class="row align-items-center gy-2">
                <div class="col-12 col-md-6 text-center text-md-start">
                    <small class="text-muted">
                        © <?= date('Y') ?> <span class="fw-bold">SMKN 1 BANJAR</span>. All rights reserved.
                    </small>
                </div>
                <div class="col-12 col-md-6 text-center text-md-end">
                    <small class="text-muted">
                        Dibuat dengan <i class="fas fa-heart text-danger"></i> oleh <span class="fw-bold">M Raffa Izzel H.</span>
                    </small>
                </div>
            </div>

            <!-- Version Mobile (muncul di HP) -->
            <div class="row d-md-none mt-2">
                <div class="col-12 text-center">
                    <small class="text-muted">v2.0.1 | Mading Digital</small>
                </div>
            </div>
        </div>
    </footer>

    
    <style>
    /* Footer Styling - RESPONSIVE */
    .footer-user {
        background: white;
        padding: 2rem 0 1.5rem;
        margin-top: 3rem;
        box-shadow: 0 -5px 20px rgba(0,0,0,0.03);
        border-top: 1px solid rgba(0,0,0,0.05);
    }

    /* Hover effect untuk link */
    .hover-primary {
        transition: color 0.2s ease;
    }
    .hover-primary:hover {
        color: var(--user-primary) !important;
    }

    /* Social media buttons */
    .btn-outline-primary {
        border-color: #dee2e6;
        color: #6c757d;
        transition: all 0.3s ease;
    }
    .btn-outline-primary:hover {
        background: var(--user-gradient);
        border-color: var(--user-primary);
        color: white;
        transform: translateY(-2px);
    }

    /* Responsive breakpoints */

    /* HP kecil (<350px) */
    @media (max-width: 350px) {
        .footer-user {
            padding: 1.5rem 0 1rem;
        }
        
        .footer-user h6 {
            font-size: 0.9rem;
        }
        
        .footer-user .small {
            font-size: 0.7rem;
        }
        
        .btn-outline-primary {
            width: 30px !important;
            height: 30px !important;
            font-size: 0.7rem;
        }
        
        .btn-outline-primary i {
            font-size: 0.8rem;
        }
        
        .bg-light.rounded-4 {
            padding: 0.8rem !important;
        }
    }

    /* HP sedang (351px - 576px) */
    @media (min-width: 351px) and (max-width: 576px) {
        .footer-user {
            padding: 1.5rem 0;
        }
        
        .footer-user h6 {
            font-size: 0.95rem;
        }
        
        .footer-user .small {
            font-size: 0.75rem;
        }
        
        .btn-outline-primary {
            width: 32px !important;
            height: 32px !important;
        }
    }

    /* Tablet (577px - 768px) */
    @media (min-width: 577px) and (max-width: 768px) {
        .footer-user {
            padding: 1.8rem 0;
        }
    }

    /* Landscape mode di HP */
    @media (max-height: 500px) and (orientation: landscape) {
        .footer-user {
            padding: 1rem 0;
        }
        
        .row.gy-3 {
            --bs-gutter-y: 0.5rem;
        }
    }

    /* Dark mode support (optional) */
    @media (prefers-color-scheme: dark) {
        .footer-user {
            background: #1a1a1a;
            border-top-color: #333;
        }
        
        .footer-user .text-muted {
            color: #999 !important;
        }
        
        .footer-user h6 {
            color: #fff;
        }
        
        .bg-light.rounded-4 {
            background: #2d2d2d !important;
        }
        
        .bg-light.rounded-4 .small {
            color: #ccc !important;
        }
        
        .btn-outline-primary {
            border-color: #444;
            color: #999;
        }
    }
    </style>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Chart 7 Hari Terakhir
        const ctx = document.getElementById('activityChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: <?= json_encode($last_7_days) ?>, 
                datasets: [{
                    label: 'Jumlah Mading',
                    data: <?= json_encode($daily_counts) ?>,
                    backgroundColor: 'rgba(67, 97, 238, 0.1)',
                    borderColor: '#4361ee',
                    borderWidth: 2,
                    tension: 0.3,
                    fill: true,
                    pointBackgroundColor: '#4361ee',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 1.5,
                    pointRadius: 3,
                    pointHoverRadius: 5
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: { 
                        mode: 'index', 
                        intersect: false,
                        backgroundColor: '#4361ee',
                        titleColor: '#fff',
                        bodyColor: '#fff',
                        titleFont: { size: 11 },
                        bodyFont: { size: 11 },
                        padding: 8
                    }
                },
                scales: {
                    y: { 
                        beginAtZero: true, 
                        ticks: { 
                            stepSize: 1,
                            font: { size: 9 }
                        },
                        grid: { color: 'rgba(0,0,0,0.03)' }
                    },
                    x: { 
                        grid: { display: false },
                        ticks: { 
                            font: { size: 9 },
                            maxRotation: 45,
                            minRotation: 45
                        }
                    }
                }
            }
        });

        // Function to show detail modal
        function showDetail(mading) {
            document.getElementById('modalTitle').textContent = mading.judul;
            document.getElementById('modalDate').textContent = new Date(mading.tanggal).toLocaleDateString('id-ID', {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });
            document.getElementById('modalContent').innerHTML = mading.isi;
            
            const modalImage = document.getElementById('modalImage');
            if (mading.foto) {
                modalImage.src = '../uploads/' + mading.foto;
                modalImage.style.display = 'block';
            } else {
                modalImage.style.display = 'none';
            }
            
            new bootstrap.Modal(document.getElementById('detailModal')).show();
        }

        // Auto refresh carousel
        const carousel = document.getElementById('madingCarousel');
        if (carousel) {
            new bootstrap.Carousel(carousel, {
                interval: 5000,
                wrap: true
            });
        }
        
        // Fix untuk modal di mobile
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('modal-img')) {
                e.target.style.maxHeight = '400px';
            }
        });
    </script>
</body>
</html>