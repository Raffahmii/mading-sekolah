<?php
require "../config/db.php";

$error = "";

if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $db->prepare("SELECT * FROM users WHERE username = :u");
    $stmt->execute([':u'=>$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
    if (
        $user['password'] === $password || 
        password_verify($password, $user['password'])
    ) {

            $_SESSION['login'] = true;
            $_SESSION['role'] = $user['role'];

            if ($user['role'] === 'admin') {
                header("Location: ../admin/dashboard.php");
            } else {
                header("Location: ../user/dashboard.php");
            }
            exit;

        } else {
            $error = "Password salah";
        }
    } else {
        $error = "User tidak ditemukan";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Mading Sekolah</title>
    
    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Fredoka+One&display=swap" rel="stylesheet">
    
    <!-- Auth CSS -->
    <link rel="stylesheet" href="../assets/css/auth.css">
    
    <style>
        /* Tambahan inline style untuk login */
        .floating {
            animation: floating 3s ease-in-out infinite;
        }
        
        @keyframes floating {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
            100% { transform: translateY(0px); }
        }
        
        /* Logo untuk mobile - lebih compact */
        .mobile-logo {
            display: none;
            text-align: center;
            margin-bottom: 0.8rem;
            padding: 0.5rem;
        }
        
        .mobile-logo i {
            color: #4e9cff;
            font-size: 1.8rem;
            display: block;
            margin-bottom: 0.3rem;
        }
        
        .mobile-logo h1 {
            font-family: 'Fredoka One', cursive;
            color: #2962ff;
            font-size: 1.4rem;
            margin-bottom: 0.2rem;
            line-height: 1.2;
        }
        
        .mobile-logo p {
            color: #666;
            font-size: 0.75rem;
            margin-bottom: 0;
        }
        
        /* Form lebih compact di mobile */
        .compact-form .form-group {
            margin-bottom: 0.8rem;
        }
        
        .compact-form .form-label {
            font-size: 0.85rem;
            margin-bottom: 0.2rem;
        }
        
        .compact-form .form-control {
            padding: 0.5rem 0.75rem;
            font-size: 0.9rem;
            height: 40px;
        }
        
        .compact-form .input-group-text {
            padding: 0.5rem 0.75rem;
            height: 40px;
        }
        
        .compact-form .btn {
            padding: 0.5rem;
            font-size: 0.9rem;
        }
        
        .form-header h2 {
            font-size: 1.3rem;
            margin-bottom: 0.3rem;
        }
        
        .form-header p {
            font-size: 0.8rem;
            margin-bottom: 1rem;
        }
        
        /* Responsive untuk mobile */
        @media (max-width: 768px) {
            .mobile-logo {
                display: block;
            }
            
            .compact-form {
                display: block;
            }
            
            .floating {
                animation: floating 4s ease-in-out infinite;
            }
            
            @keyframes floating {
                0% { transform: translateY(0px); }
                50% { transform: translateY(-6px); }
                100% { transform: translateY(0px); }
            }
            
            /* Full height untuk mobile */
            .right-panel {
                height: 100vh;
                justify-content: flex-start;
                padding-top: 0.5rem;
            }
            
            .auth-form-container {
                padding: 1.2rem;
                margin: 0.3rem;
                max-height: 90vh;
                overflow-y: auto;
            }
            
            .auth-form-container::-webkit-scrollbar {
                width: 3px;
            }
            
            .auth-form-container::-webkit-scrollbar-thumb {
                background: #ddd;
                border-radius: 2px;
            }
            
            .container-fluid {
                padding: 0;
            }
        }
        
        /* Untuk layar sangat kecil */
        @media (max-width: 576px) {
            .auth-form-container {
                padding: 1rem;
                border-radius: 15px;
                box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            }
            
            .mobile-logo h1 {
                font-size: 1.2rem;
            }
            
            .mobile-logo i {
                font-size: 1.5rem;
            }
            
            .mobile-logo p {
                font-size: 0.7rem;
            }
            
            .form-header h2 {
                font-size: 1.2rem;
            }
            
            .form-header p {
                font-size: 0.75rem;
            }
            
            body {
                background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
                padding: 0.5rem;
            }
        }
        
        /* Untuk layar ekstra kecil (iPhone SE dll) */
        @media (max-height: 700px) and (max-width: 576px) {
            .mobile-logo {
                margin-bottom: 0.5rem;
                padding: 0.3rem;
            }
            
            .auth-form-container {
                padding: 0.8rem;
            }
            
            .form-header {
                margin-bottom: 0.8rem;
            }
            
            .compact-form .form-group {
                margin-bottom: 0.6rem;
            }
        }
    </style>
</head>
<body>
    <div class="container-fluid vh-100">
        <div class="row h-100">
            <!-- Left Section - Info -->
            <div class="col-lg-6 col-md-5 d-none d-md-flex flex-column justify-content-center align-items-center left-panel">
                <div class="auth-info text-center px-5">
                    <div class="logo-container mb-4 floating">
                        <i class="fas fa-school fa-4x text-white"></i>
                        <h1 class="mt-3 fw-bold" style="font-family: 'Fredoka One', cursive;">SMK NEGERI 1 BANJAR</h1>
                    </div>
                    <h2 class="fw-bold mb-3">Selamat Datang Kembali!</h2>
                    <p class="mb-4">Login untuk melihat mading terbaru dan info penting sekolahmu. Masukkan username dan password untuk masuk ke dalam platform.</p>
                    <p class="mb-4">Belum punya akun?</p>
                    <a href="register.php" class="btn btn-outline-light btn-lg btn-register">
                        <i class="fas fa-user-plus me-2"></i>Daftar Sekarang
                    </a>
                </div>
                
                <!-- Decorative Elements -->
                <div class="decorative-circle circle-1"></div>
                <div class="decorative-circle circle-2"></div>
                <div class="decorative-circle circle-3"></div>
            </div>
            
            <!-- Right Section - Form -->
            <div class="col-lg-6 col-md-7 d-flex flex-column justify-content-center align-items-center right-panel">
                <div class="auth-form-container animated-form" style="max-width: 400px;">
                    <!-- Logo untuk Mobile -->
                    <div class="mobile-logo floating">
                        <i class="fas fa-school"></i>
                        <h1 class="fw-bold">SMK NEGERI 1 BANJAR</h1>
                        <p>Selamat datang kembali!</p>
                    </div>
                    
                    <div class="form-header text-center mb-3">
                        <h2 class="fw-bold text-primary">Masuk ke Akun</h2>
                        <p class="text-muted">Silakan masuk dengan akun yang telah terdaftar</p>
                    </div>
                    
                    <?php if ($error): ?>
                        <div class="alert alert-danger alert-dismissible fade show py-2 mb-3" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            <span style="font-size: 0.85rem;"><?= $error ?></span>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close" style="padding: 0.4rem; font-size: 0.7rem;"></button>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST" class="needs-validation compact-form" novalidate>
                        <div class="form-group">
                            <label for="username" class="form-label fw-semibold">Username</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0">
                                    <i class="fas fa-user text-primary"></i>
                                </span>
                                <input type="text" name="username" id="username" class="form-control border-start-0" placeholder="Masukkan username" required>
                            </div>
                            <div class="invalid-feedback" style="font-size: 0.75rem;">Username wajib diisi</div>
                        </div>
                        
                        <div class="form-group">
                            <label for="password" class="form-label fw-semibold">Password</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0">
                                    <i class="fas fa-lock text-primary"></i>
                                </span>
                                <input type="password" name="password" id="password" class="form-control border-start-0" placeholder="Masukkan password" required>
                                <button class="btn btn-outline-secondary" type="button" id="togglePassword" style="padding: 0.4rem;">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            <div class="invalid-feedback" style="font-size: 0.75rem;">Password wajib diisi</div>
                        </div>
                        
                        <div class="d-grid mt-3">
                            <button name="login" class="btn btn-primary btn-login">
                                <i class="fas fa-sign-in-alt me-2"></i>Login
                            </button>
                        </div>
                        
                        <div class="text-center mt-3">
                            <p class="mb-2" style="font-size: 0.85rem;">
                                Belum punya akun? 
                                <a href="register.php" class="text-decoration-none fw-semibold text-primary">Daftar di sini</a>
                            </p>
                            <p class="mb-0 text-muted" style="font-size: 0.75rem;">MADING By RAFFA</p>
                        </div>
                    </form>
                </div>
                
                <!-- Footer -->
                <div class="mt-2 text-center">
                    <p class="text-muted" style="font-size: 0.7rem;">
                        <i class="fas fa-copyright me-1"></i> <?= date('Y') ?> sekolah smkn 1 banjar
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Form validation
        (function () {
            'use strict'
            var forms = document.querySelectorAll('.needs-validation')
            Array.prototype.slice.call(forms).forEach(function (form) {
                form.addEventListener('submit', function (event) {
                    if (!form.checkValidity()) {
                        event.preventDefault()
                        event.stopPropagation()
                    }
                    form.classList.add('was-validated')
                }, false)
            })
        })()
        
        // Toggle password visibility
        document.getElementById('togglePassword').addEventListener('click', function() {
            const passwordInput = document.getElementById('password');
            const icon = this.querySelector('i');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });
        
        // Animation on load
        document.addEventListener('DOMContentLoaded', function() {
            const formContainer = document.querySelector('.animated-form');
            formContainer.style.opacity = '0';
            formContainer.style.transform = 'translateY(15px)';
            
            setTimeout(() => {
                formContainer.style.transition = 'opacity 0.4s ease, transform 0.4s ease';
                formContainer.style.opacity = '1';
                formContainer.style.transform = 'translateY(0)';
            }, 200);
        });
        
        // Auto resize form container untuk mobile
        function adjustFormHeight() {
            if (window.innerWidth <= 768) {
                const formContainer = document.querySelector('.auth-form-container');
                const windowHeight = window.innerHeight;
                const maxHeight = windowHeight * 0.9;
                formContainer.style.maxHeight = maxHeight + 'px';
            }
        }
        
        window.addEventListener('resize', adjustFormHeight);
        window.addEventListener('load', adjustFormHeight);
    </script>
</body>
</html>