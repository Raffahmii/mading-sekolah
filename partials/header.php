<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$role = $_SESSION['role'] ?? null;
$page_title = isset($page_title) ? $page_title : 'RAF Mading Sekolah';
$is_admin = ($role === 'admin');
$is_user = ($role === 'user');
?>
<!DOCTYPE html>
<html lang="id" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($page_title) ?> | RAF Mading</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="../assets/images/favicon.ico">
    
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Fredoka+One&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    
    <!-- Main Styles -->
    <link rel="stylesheet" href="../assets/css/main.css">
    
    <!-- Role Specific CSS -->
    <?php if ($is_admin): ?>
        <link rel="stylesheet" href="../assets/css/admin.css">
        <style>
            :root {
                --primary-color: #0d6efd;
                --secondary-color: #6c757d;
                --accent-color: #20c997;
                --danger-color: #dc3545;
                --warning-color: #ffc107;
                --success-color: #198754;
            }
        </style>
    <?php elseif ($is_user): ?>
        <link rel="stylesheet" href="../assets/css/user.css">
        <style>
            :root {
                --primary-color: #4e9cff;
                --secondary-color: #6c757d;
                --accent-color: #ff6b6b;
                --dark-color: #2d3748;
                --light-color: #f8f9fa;
            }
        </style>
    <?php else: ?>
        <link rel="stylesheet" href="../assets/css/auth.css">
        <style>
            :root {
                --primary-color: #4e9cff;
                --secondary-color: #2962ff;
                --accent-color: #ff6b6b;
            }
        </style>
    <?php endif; ?>
    
    <!-- Global Styles -->
    <style>
        :root {
            --font-primary: 'Poppins', sans-serif;
            --font-heading: 'Fredoka One', cursive;
            --font-ui: 'Inter', sans-serif;
        }
        
        body {
            font-family: var(--font-primary);
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            background-color: var(--bs-body-bg, #f8f9fa);
        }
        
        h1, h2, h3, h4, h5, h6 {
            font-family: var(--font-heading);
            font-weight: 600;
        }
        
        .btn {
            font-family: var(--font-ui);
            font-weight: 500;
            border-radius: 8px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }
        
        .card {
            border-radius: 12px;
            border: 1px solid rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
        }
        
        .card:hover {
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }
        
        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 10px;
            height: 10px;
        }
        
        ::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }
        
        ::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 10px;
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: #a8a8a8;
        }
        
        /* Animations */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .animate-fade-in {
            animation: fadeIn 0.5s ease-out;
        }
        
        @keyframes slideInRight {
            from { opacity: 0; transform: translateX(20px); }
            to { opacity: 1; transform: translateX(0); }
        }
        
        .animate-slide-in {
            animation: slideInRight 0.6s ease-out;
        }
        
        /* Loading Spinner */
        .spinner {
            width: 40px;
            height: 40px;
            border: 3px solid #f3f3f3;
            border-top: 3px solid #0d6efd;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        /* Theme Toggle */
        .theme-toggle {
            cursor: pointer;
            padding: 8px;
            border-radius: 50%;
            transition: all 0.3s ease;
        }
        
        .theme-toggle:hover {
            background: rgba(0, 0, 0, 0.05);
        }
        
        /* Accessibility */
        .sr-only {
            position: absolute;
            width: 1px;
            height: 1px;
            padding: 0;
            margin: -1px;
            overflow: hidden;
            clip: rect(0, 0, 0, 0);
            white-space: nowrap;
            border: 0;
        }
        
        /* Focus Styles */
        *:focus {
            outline: 2px solid #0d6efd;
            outline-offset: 2px;
        }
        
        *:focus:not(:focus-visible) {
            outline: none;
        }
    </style>
    
    <!-- Page Specific CSS -->
    <?php if (isset($page_css)): ?>
        <style><?= $page_css ?></style>
    <?php endif; ?>
</head>
<body class="<?= $is_admin ? 'admin-dashboard' : ($is_user ? 'user-dashboard' : 'auth-page') ?>">
    
    <!-- Loading Overlay -->
    <div id="loading-overlay" class="position-fixed top-0 start-0 w-100 h-100 bg-white d-flex flex-column align-items-center justify-content-center" style="z-index: 9999; display: none !important;">
        <div class="spinner mb-3"></div>
        <p class="text-muted">Memuat...</p>
    </div>
    
    <?php if ($is_admin || $is_user): ?>
    <!-- Top Navigation Bar (For Logged-in Users) -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom shadow-sm fixed-top" style="z-index: 1030;">
        <div class="container-fluid">
            <!-- Logo/Brand -->
            <a class="navbar-brand d-flex align-items-center" href="<?= $is_admin ? 'dashboard.php' : '../user/dashboard.php' ?>">
                <div class="logo-icon bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 36px; height: 36px;">
                    <i class="fas fa-school"></i>
                </div>
                <div>
                    <span class="fw-bold" style="font-family: 'Fredoka One', cursive; color: #0d6efd;">RAF</span>
                    <span class="text-dark fw-bold">Mading</span>
                    <small class="d-block text-muted" style="font-size: 0.7rem; line-height: 1;">Sekolah</small>
                </div>
            </a>
            
            <!-- Mobile Toggle -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <!-- Navigation Items -->
            <div class="collapse navbar-collapse" id="mainNavbar">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <?php if ($is_admin): ?>
                    <li class="nav-item">
                        <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : '' ?>" href="dashboard.php">
                            <i class="fas fa-tachometer-alt me-1"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'tambah.php' ? 'active' : '' ?>" href="tambah.php">
                            <i class="fas fa-plus-circle me-1"></i> Tambah Mading
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="adminDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-cog me-1"></i> Pengaturan
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#"><i class="fas fa-users me-2"></i> Kelola Pengguna</a></li>
                            <li><a class="dropdown-item" href="#"><i class="fas fa-tags me-2"></i> Kategori</a></li>
                            <li><a class="dropdown-item" href="#"><i class="fas fa-image me-2"></i> Media</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="#"><i class="fas fa-chart-bar me-2"></i> Analitik</a></li>
                        </ul>
                    </li>
                    <?php elseif ($is_user): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="../user/dashboard.php">
                            <i class="fas fa-home me-1"></i> Beranda
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../user/mading.php">
                            <i class="fas fa-newspaper me-1"></i> Mading
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../user/kategori.php">
                            <i class="fas fa-tags me-1"></i> Kategori
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../user/galeri.php">
                            <i class="fas fa-images me-1"></i> Galeri
                        </a>
                    </li>
                    <?php endif; ?>
                </ul>
                
                <!-- Right Side: Search, Notification, Profile -->
                <div class="d-flex align-items-center">
                    <!-- Search -->
                    <div class="input-group me-3 d-none d-md-flex" style="width: 250px;">
                        <span class="input-group-text bg-light border-end-0">
                            <i class="fas fa-search text-muted"></i>
                        </span>
                        <input type="text" class="form-control border-start-0" placeholder="Cari mading...">
                    </div>
                    
                    <!-- Theme Toggle -->
                    <button class="btn btn-sm btn-outline-secondary me-2 theme-toggle" id="themeToggle">
                        <i class="fas fa-moon"></i>
                    </button>
                    
                    <!-- Notifications -->
                    <div class="dropdown me-3">
                        <button class="btn btn-sm btn-outline-secondary position-relative" data-bs-toggle="dropdown">
                            <i class="fas fa-bell"></i>
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.6rem;">
                                3
                            </span>
                        </button>
                        <div class="dropdown-menu dropdown-menu-end p-2" style="width: 300px;">
                            <h6 class="dropdown-header">Notifikasi</h6>
                            <div class="list-group list-group-flush">
                                <a href="#" class="list-group-item list-group-item-action">
                                    <small class="text-primary">5 menit lalu</small>
                                    <p class="mb-1">Mading baru ditambahkan</p>
                                </a>
                                <a href="#" class="list-group-item list-group-item-action">
                                    <small class="text-primary">1 jam lalu</small>
                                    <p class="mb-1">Ada komentar baru</p>
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <!-- User Profile -->
                    <div class="dropdown">
                        <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle" data-bs-toggle="dropdown">
                            <div class="avatar-circle bg-primary text-white d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px; border-radius: 50%;">
                                <i class="fas fa-user"></i>
                            </div>
                            <div class="d-none d-md-block">
                                <span class="fw-semibold"><?= $_SESSION['username'] ?? 'Pengguna' ?></span>
                                <small class="d-block text-muted" style="font-size: 0.75rem;">
                                    <?= $is_admin ? 'Administrator' : 'Siswa' ?>
                                </small>
                            </div>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow">
                            <li>
                                <a class="dropdown-item" href="#">
                                    <i class="fas fa-user-circle me-2"></i> Profil Saya
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="#">
                                    <i class="fas fa-cog me-2"></i> Pengaturan
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item text-danger" href="../auth/logout.php">
                                    <i class="fas fa-sign-out-alt me-2"></i> Logout
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </nav>
    
    <!-- Main Content Wrapper -->
    <div class="container-fluid mt-5 pt-4">
        <!-- Page Content will be inserted here -->
        
    <?php else: ?>
    <!-- For Non-Authenticated Pages (Login/Register) -->
    <!-- Content will be full page -->
    
    <?php endif; ?>