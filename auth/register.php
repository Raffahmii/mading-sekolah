<?php
require "../config/db.php";

$error = "";

if (isset($_POST['register'])) {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $confirm  = $_POST['confirm_password'];

    if ($username == "" || $password == "" || $confirm == "") {
        $error = "Semua field wajib diisi";
    } else if ($password !== $confirm) {
        $error = "Password dan Confirm Password tidak sama";
    } else {
        try {
            $stmt = $db->prepare(
                "INSERT INTO users (username, password, role)
                 VALUES (:u, :p, 'user')"
            );
            $stmt->execute([
                ':u' => $username,
                ':p' => md5($password)
            ]);

            header("Location: login.php");
            exit;
        } catch (PDOException $e) {
            $error = "Username sudah terdaftar";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register | Mading Sekolah</title>
    
    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Fredoka+One&display=swap" rel="stylesheet">
    
    <!-- Auth CSS -->
    <link rel="stylesheet" href="../assets/css/auth.css">
    
    <style>
        /* Custom styling untuk register super compact */
        .super-compact .form-group {
            margin-bottom: 0.7rem;
        }
        
        .super-compact .form-label {
            font-size: 0.8rem;
            margin-bottom: 0.2rem;
            font-weight: 500;
        }
        
        .super-compact .form-control {
            padding: 0.4rem 0.6rem;
            font-size: 0.85rem;
            height: 38px;
        }
        
        .super-compact .input-group-text {
            padding: 0.4rem 0.6rem;
            height: 38px;
            font-size: 0.85rem;
        }
        
        .super-compact .btn {
            padding: 0.5rem;
            font-size: 0.9rem;
            height: 42px;
        }
        
        .super-compact .form-check {
            margin-top: 0.4rem;
            margin-bottom: 0.8rem;
        }
        
        .super-compact .form-check-label {
            font-size: 0.75rem;
            line-height: 1.2;
        }
        
        .password-strength {
            margin-top: 0.2rem;
        }
        
        .password-strength .progress {
            height: 3px;
            margin-bottom: 0.1rem;
        }
        
        .password-strength small {
            font-size: 0.7rem;
        }
        
        .password-match-feedback {
            font-size: 0.7rem;
            margin-top: 0.1rem;
        }
        
        .form-header h2 {
            font-size: 1.3rem;
            margin-bottom: 0.2rem;
        }
        
        .form-header p {
            font-size: 0.75rem;
            margin-bottom: 1rem;
            color: #666;
        }
        
        /* Logo untuk mobile - super compact */
        .mobile-logo {
            display: none;
            text-align: center;
            margin-bottom: 0.6rem;
            padding: 0.4rem;
        }
        
        .mobile-logo i {
            color: #4e9cff;
            font-size: 1.6rem;
            display: block;
            margin-bottom: 0.2rem;
        }
        
        .mobile-logo h1 {
            font-family: 'Fredoka One', cursive;
            color: #2962ff;
            font-size: 1.2rem;
            margin-bottom: 0.1rem;
            line-height: 1.1;
        }
        
        .mobile-logo p {
            color: #666;
            font-size: 0.7rem;
            margin-bottom: 0;
        }
        
        .auth-form-container {
            padding: 1.2rem;
            max-width: 400px;
        }
        
        /* Animasi Floating untuk Logo */
        .floating {
            animation: floating 3s ease-in-out infinite;
        }
        
        @keyframes floating {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-6px); }
            100% { transform: translateY(0px); }
        }
        
        /* Animasi untuk tombol register */
        @keyframes subtle-pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.01); }
            100% { transform: scale(1); }
        }
        
        .btn-register {
            animation: subtle-pulse 3s infinite;
        }
        
        /* Checkbox lebih kecil */
        .form-check-input {
            width: 0.8rem;
            height: 0.8rem;
            margin-top: 0.15rem;
        }
        
        /* Responsive untuk mobile */
        @media (max-width: 768px) {
            .mobile-logo {
                display: block;
            }
            
            .super-compact {
                display: block;
            }
            
            /* Full height untuk mobile */
            .left-panel-register {
                height: 100vh;
                justify-content: flex-start;
                padding-top: 0.3rem;
            }
            
            .auth-form-container {
                padding: 1rem;
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
            
            .floating {
                animation: floating 4s ease-in-out infinite;
            }
            
            @keyframes floating {
                0% { transform: translateY(0px); }
                50% { transform: translateY(-5px); }
                100% { transform: translateY(0px); }
            }
        }
        
        /* Untuk layar sangat kecil */
        @media (max-width: 576px) {
            .auth-form-container {
                padding: 0.9rem;
                border-radius: 12px;
                box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            }
            
            .mobile-logo h1 {
                font-size: 1.1rem;
            }
            
            .mobile-logo i {
                font-size: 1.4rem;
            }
            
            .mobile-logo p {
                font-size: 0.65rem;
            }
            
            .form-header h2 {
                font-size: 1.1rem;
            }
            
            .form-header p {
                font-size: 0.7rem;
            }
            
            body {
                background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
                padding: 0.5rem;
            }
            
            .super-compact .form-group {
                margin-bottom: 0.6rem;
            }
        }
        
        /* Untuk layar ekstra kecil (iPhone SE dll) */
        @media (max-height: 700px) and (max-width: 576px) {
            .mobile-logo {
                margin-bottom: 0.4rem;
                padding: 0.2rem;
            }
            
            .auth-form-container {
                padding: 0.7rem;
            }
            
            .form-header {
                margin-bottom: 0.6rem;
            }
            
            .super-compact .form-group {
                margin-bottom: 0.5rem;
            }
            
            .super-compact .form-control {
                height: 36px;
                padding: 0.35rem 0.5rem;
            }
            
            .super-compact .input-group-text {
                height: 36px;
                padding: 0.35rem 0.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="container-fluid vh-100">
        <div class="row h-100">
            <!-- Left Section - Form -->
            <div class="col-lg-6 col-md-7 d-flex flex-column justify-content-center align-items-center left-panel-register">
                <div class="auth-form-container animated-form">
                    <!-- Logo untuk Mobile -->
                    <div class="mobile-logo floating">
                        <i class="fas fa-school"></i>
                        <h1 class="fw-bold">SMK NEGERI 1 BANJAR</h1>
                        <p>Isi data diri Anda untuk membuat akun baru</p>
                    </div>
                    
                    <div class="form-header text-center mb-2">
                        <h2 class="fw-bold text-primary">Buat Akun Baru</h2>
                        <p class="text-muted">Isi data diri Anda untuk membuat akun baru</p>
                    </div>
                    
                    <?php if ($error): ?>
                        <div class="alert alert-danger alert-dismissible fade show py-2 mb-2" role="alert">
                            <i class="fas fa-exclamation-circle me-1" style="font-size: 0.8rem;"></i>
                            <span style="font-size: 0.8rem;"><?= $error ?></span>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close" style="padding: 0.4rem; font-size: 0.7rem;"></button>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST" class="needs-validation super-compact" novalidate>
                        <div class="form-group">
                            <label for="username" class="form-label fw-semibold">Username</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0">
                                    <i class="fas fa-user text-primary"></i>
                                </span>
                                <input type="text" name="username" id="username" class="form-control border-start-0" placeholder="Masukkan username" required>
                            </div>
                            <div class="invalid-feedback" style="font-size: 0.7rem;">Username wajib diisi</div>
                            <small class="text-muted" style="font-size: 0.7rem; display: block; margin-top: 0.1rem;">Username akan digunakan untuk login</small>
                        </div>
                        
                        <div class="form-group">
                            <label for="password" class="form-label fw-semibold">Password</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0">
                                    <i class="fas fa-lock text-primary"></i>
                                </span>
                                <input type="password" name="password" id="password" class="form-control border-start-0" placeholder="Masukkan password" required>
                                <button class="btn btn-outline-secondary" type="button" id="togglePassword1" style="padding: 0.4rem; font-size: 0.8rem;">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            <div class="invalid-feedback" style="font-size: 0.7rem;">Password wajib diisi</div>
                            <div class="password-strength">
                                <div class="progress" style="height: 3px;">
                                    <div class="progress-bar" id="passwordStrength" role="progressbar" style="width: 0%"></div>
                                </div>
                                <small id="passwordStrengthText" class="text-muted">Kekuatan password: lemah</small>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="confirm_password" class="form-label fw-semibold">Konfirmasi Password</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0">
                                    <i class="fas fa-lock text-primary"></i>
                                </span>
                                <input type="password" name="confirm_password" id="confirm_password" class="form-control border-start-0" placeholder="Konfirmasi password" required>
                                <button class="btn btn-outline-secondary" type="button" id="togglePassword2" style="padding: 0.4rem; font-size: 0.8rem;">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            <div class="invalid-feedback" style="font-size: 0.7rem;">Konfirmasi password wajib diisi</div>
                            <div class="password-match-feedback">
                                <small id="passwordMatch" class="text-success d-none"><i class="fas fa-check-circle me-1"></i>Password cocok</small>
                                <small id="passwordNotMatch" class="text-danger d-none"><i class="fas fa-times-circle me-1"></i>Password tidak cocok</small>
                            </div>
                        </div>
                        
                        <div class="form-check mt-1">
                            <input class="form-check-input" type="checkbox" id="terms" required>
                            <label class="form-check-label" for="terms">
                                Saya menyetujui <a href="#" class="text-decoration-none text-primary">Syarat & Ketentuan</a> dan <a href="#" class="text-decoration-none text-primary">Kebijakan Privasi</a>
                            </label>
                            <div class="invalid-feedback" style="font-size: 0.7rem;">Anda harus menyetujui syarat & ketentuan</div>
                        </div>
                        
                        <div class="d-grid mt-2">
                            <button name="register" class="btn btn-primary btn-register">
                                <i class="fas fa-user-plus me-1"></i>Daftar Sekarang
                            </button>
                        </div>
                        
                        <div class="text-center mt-2">
                            <p class="mb-0" style="font-size: 0.75rem;">
                                Sudah punya akun? 
                                <a href="login.php" class="text-decoration-none fw-semibold text-primary">Masuk di sini</a>
                            </p>
                        </div>
                    </form>
                </div>
                
                <!-- Footer -->
                <div class="mt-2 text-center">
                    <p class="text-muted" style="font-size: 0.65rem;">
                        <i class="fas fa-copyright me-1"></i> <?= date('Y') ?> Sekolah smkn 1 banjar
                    </p>
                </div>
            </div>
            
            <!-- Right Section - Info -->
            <div class="col-lg-6 col-md-5 d-none d-md-flex flex-column justify-content-center align-items-center right-panel-register">
                <div class="auth-info text-center px-4">
                    <div class="logo-container mb-3 floating">
                        <i class="fas fa-school fa-3x text-white"></i>
                        <h1 class="mt-2 fw-bold" style="font-family: 'Fredoka One', cursive; font-size: 2rem;">SMK NEGERI 1 BANJAR</h1>
                    </div>
                    <h2 class="fw-bold mb-2" style="font-size: 1.5rem;">Bergabung dengan Komunitas Sekolah!</h2>
                    <p class="mb-3" style="line-height: 1.4;">Daftar sekarang untuk mendapatkan akses melihat mading-mading sekolah dan info terbaru.</p>
                    <p class="mb-3" style="font-size: 0.95rem;">Sudah punya akun?</p>
                    <a href="login.php" class="btn btn-outline-light btn-login">
                        <i class="fas fa-sign-in-alt me-2"></i>Login Sekarang
                    </a>
                </div>
                
                <!-- Decorative Elements - lebih kecil -->
                <div class="decorative-circle circle-1" style="width: 80px; height: 80px;"></div>
                <div class="decorative-circle circle-2" style="width: 60px; height: 60px;"></div>
                <div class="decorative-circle circle-3" style="width: 100px; height: 100px;"></div>
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
        document.getElementById('togglePassword1').addEventListener('click', function() {
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
        
        document.getElementById('togglePassword2').addEventListener('click', function() {
            const passwordInput = document.getElementById('confirm_password');
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
        
        // Password strength checker
        document.getElementById('password').addEventListener('input', function() {
            const password = this.value;
            const strengthBar = document.getElementById('passwordStrength');
            const strengthText = document.getElementById('passwordStrengthText');
            
            let strength = 0;
            
            // Check password length
            if (password.length >= 8) strength += 25;
            if (password.length >= 12) strength += 10;
            
            // Check for lowercase
            if (/[a-z]/.test(password)) strength += 15;
            
            // Check for uppercase
            if (/[A-Z]/.test(password)) strength += 15;
            
            // Check for numbers
            if (/[0-9]/.test(password)) strength += 20;
            
            // Check for special characters
            if (/[^A-Za-z0-9]/.test(password)) strength += 25;
            
            // Update strength bar
            strengthBar.style.width = strength + '%';
            
            // Update strength text and color
            if (strength < 50) {
                strengthBar.className = 'progress-bar bg-danger';
                strengthText.textContent = 'Kekuatan password: lemah';
                strengthText.className = 'text-muted';
            } else if (strength < 75) {
                strengthBar.className = 'progress-bar bg-warning';
                strengthText.textContent = 'Kekuatan password: sedang';
                strengthText.className = 'text-muted';
            } else {
                strengthBar.className = 'progress-bar bg-success';
                strengthText.textContent = 'Kekuatan password: kuat';
                strengthText.className = 'text-muted';
            }
            
            // Check password match
            checkPasswordMatch();
        });
        
        // Password match checker
        document.getElementById('confirm_password').addEventListener('input', checkPasswordMatch);
        
        function checkPasswordMatch() {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            const match = document.getElementById('passwordMatch');
            const notMatch = document.getElementById('passwordNotMatch');
            
            if (confirmPassword === '') {
                match.classList.add('d-none');
                notMatch.classList.add('d-none');
                return;
            }
            
            if (password === confirmPassword) {
                match.classList.remove('d-none');
                notMatch.classList.add('d-none');
            } else {
                match.classList.add('d-none');
                notMatch.classList.remove('d-none');
            }
        }
        
        // Animation on load
        document.addEventListener('DOMContentLoaded', function() {
            const formContainer = document.querySelector('.animated-form');
            formContainer.style.opacity = '0';
            formContainer.style.transform = 'translateY(10px)';
            
            setTimeout(() => {
                formContainer.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
                formContainer.style.opacity = '1';
                formContainer.style.transform = 'translateY(0)';
            }, 200);
            
            // Auto resize untuk mobile
            adjustFormHeight();
        });
        
        // Auto resize form container untuk mobile
        function adjustFormHeight() {
            if (window.innerWidth <= 768) {
                const formContainer = document.querySelector('.auth-form-container');
                const windowHeight = window.innerHeight;
                const maxHeight = windowHeight * 0.92;
                formContainer.style.maxHeight = maxHeight + 'px';
            }
        }
        
        window.addEventListener('resize', adjustFormHeight);
    </script>
</body>
</html>