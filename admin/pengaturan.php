<?php
require "../config/db.php";

if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

$tab = $_GET['tab'] ?? 'umum';
$message = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_settings'])) {
        // Update general settings logic here
        $message = "Pengaturan berhasil diperbarui!";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengaturan | RAF Mading</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        .settings-nav .nav-link {
            border-radius: 8px;
            margin-bottom: 5px;
            padding: 12px 15px;
            color: #495057;
        }
        
        .settings-nav .nav-link.active {
            background: #0d6efd;
            color: white;
        }
        
        .setting-card {
            border-radius: 12px;
            border: 1px solid #e0e0e0;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }
    </style>
</head>
<body>
    <div class="container py-4 mt-4">
        <h2 class="mb-4"><i class="fas fa-cog me-2"></i>Pengaturan</h2>
        
        <?php if ($message): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <?= $message ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>
        
        <div class="row">
            <!-- Settings Navigation -->
            <div class="col-md-3 mb-4">
                <div class="settings-nav">
                    <div class="nav flex-column">
                        <a class="nav-link <?= $tab === 'umum' ? 'active' : '' ?>" href="?tab=umum">
                            <i class="fas fa-sliders-h me-2"></i> Umum
                        </a>
                        <a class="nav-link <?= $tab === 'tampilan' ? 'active' : '' ?>" href="?tab=tampilan">
                            <i class="fas fa-palette me-2"></i> Tampilan
                        </a>
                        <a class="nav-link <?= $tab === 'users' ? 'active' : '' ?>" href="?tab=users">
                            <i class="fas fa-users me-2"></i> Pengguna
                        </a>
                        <a class="nav-link <?= $tab === 'backup' ? 'active' : '' ?>" href="?tab=backup">
                            <i class="fas fa-database me-2"></i> Backup
                        </a>
                        <a class="nav-link <?= $tab === 'jadwal' ? 'active' : '' ?>" href="?tab=jadwal">
                            <i class="fas fa-calendar-alt me-2"></i> Jadwal
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Settings Content -->
            <div class="col-md-9">
                <?php if ($tab === 'umum'): ?>
                <!-- General Settings -->
                <form method="POST">
                    <div class="setting-card">
                        <h5><i class="fas fa-info-circle me-2"></i>Informasi Aplikasi</h5>
                        <div class="mb-3">
                            <label class="form-label">Nama Aplikasi</label>
                            <input type="text" class="form-control" value="RAF Mading" name="app_name">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Deskripsi</label>
                            <textarea class="form-control" rows="3" name="app_desc">Platform Mading Digital Sekolah</textarea>
                        </div>
                    </div>
                    
                    <div class="setting-card">
                        <h5><i class="fas fa-bell me-2"></i>Notifikasi</h5>
                        <div class="form-check form-switch mb-2">
                            <input class="form-check-input" type="checkbox" id="notif_email" checked>
                            <label class="form-check-label" for="notif_email">Email Notifikasi</label>
                        </div>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="notif_browser" checked>
                            <label class="form-check-label" for="notif_browser">Browser Notifikasi</label>
                        </div>
                    </div>
                    
                    <button type="submit" name="update_settings" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Simpan Pengaturan
                    </button>
                </form>
                
                <?php elseif ($tab === 'jadwal'): ?>
                <!-- Schedule Settings -->
                <div class="setting-card">
                    <h5><i class="fas fa-calendar-alt me-2"></i>Jadwal Mading</h5>
                    <p>Atur jadwal publikasi mading otomatis</p>
                    
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Hari</th>
                                    <th>Waktu</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Senin</td>
                                    <td>08:00 - 15:00</td>
                                    <td><span class="badge bg-success">Aktif</span></td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                    </td>
                                </tr>
                                <!-- Add more days... -->
                            </tbody>
                        </table>
                    </div>
                    
                    <button class="btn btn-success mt-2">
                        <i class="fas fa-plus me-2"></i>Tambah Jadwal
                    </button>
                </div>
                
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>