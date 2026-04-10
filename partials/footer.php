<?php if (!isset($_SESSION['login'])): ?>
    <!-- Footer for Auth Pages -->
    <footer class="auth-footer py-4 mt-5">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <div class="d-flex align-items-center">
                        <div class="logo-icon bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                            <i class="fas fa-school"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-0" style="font-family: 'Fredoka One', cursive;">RAF Mading</h5>
                            <small class="text-muted">Platform mading digital sekolah</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="text-md-end">
                        <small class="text-muted">&copy; <?= date('Y') ?> RAF Mading Sekolah. All rights reserved.</small>
                    </div>
                </div>
            </div>
        </div>
    </footer>
<?php else: ?>
    <!-- Footer for Dashboard Pages -->
    </div> <!-- Close container from header -->
    
    <footer class="dashboard-footer border-top bg-white py-4 mt-5">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-md-4 mb-3 mb-md-0">
                    <div class="d-flex align-items-center">
                        <div class="logo-icon bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 36px; height: 36px;">
                            <i class="fas fa-school"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-0" style="font-family: 'Fredoka One', cursive;">RAF Mading</h6>
                            <small class="text-muted">Sistem Informasi Mading Digital</small>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4 mb-3 mb-md-0">
                    <div class="text-center">
                        <div class="btn-group" role="group">
                            <a href="#" class="btn btn-sm btn-outline-secondary">
                                <i class="fab fa-instagram"></i>
                            </a>
                            <a href="#" class="btn btn-sm btn-outline-secondary">
                                <i class="fab fa-facebook"></i>
                            </a>
                            <a href="#" class="btn btn-sm btn-outline-secondary">
                                <i class="fab fa-twitter"></i>
                            </a>
                            <a href="#" class="btn btn-sm btn-outline-secondary">
                                <i class="fab fa-youtube"></i>
                            </a>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="text-md-end">
                        <div class="d-flex flex-column flex-md-row align-items-center justify-content-md-end">
                            <small class="text-muted me-3 mb-2 mb-md-0">
                                <i class="fas fa-code me-1"></i> v2.1.0
                            </small>
                            <div class="d-flex">
                                <a href="#" class="btn btn-sm btn-link text-decoration-none me-3">
                                    <i class="fas fa-question-circle me-1"></i> Bantuan
                                </a>
                                <a href="#" class="btn btn-sm btn-link text-decoration-none me-3">
                                    <i class="fas fa-file-alt me-1"></i> Kebijakan
                                </a>
                                <a href="#" class="btn btn-sm btn-link text-decoration-none">
                                    <i class="fas fa-envelope me-1"></i> Kontak
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="row mt-3">
                <div class="col-12">
                    <hr class="my-2">
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center">
                        <small class="text-muted">
                            &copy; <?= date('Y') ?> RAF Mading Sekolah. All rights reserved.
                        </small>
                        <small class="text-muted mt-2 mt-md-0">
                            <i class="fas fa-heart text-danger"></i> Dibuat dengan semangat pendidikan
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </footer>
<?php endif; ?>

    <!-- Back to Top Button -->
    <button id="backToTop" class="btn btn-primary btn-floating shadow-lg" style="position: fixed; bottom: 20px; right: 20px; z-index: 1000; display: none; border-radius: 50%; width: 50px; height: 50px;">
        <i class="fas fa-arrow-up"></i>
    </button>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- SweetAlert2 for Beautiful Alerts -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- Chart.js for Statistics (Optional) -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <!-- Custom JavaScript -->
    <script>
        // Wait for DOM to be fully loaded
        document.addEventListener('DOMContentLoaded', function() {
            // Hide loading overlay
            const loadingOverlay = document.getElementById('loading-overlay');
            if (loadingOverlay) {
                setTimeout(() => {
                    loadingOverlay.style.opacity = '0';
                    setTimeout(() => {
                        loadingOverlay.style.display = 'none';
                    }, 300);
                }, 500);
            }
            
            // Theme Toggle
            const themeToggle = document.getElementById('themeToggle');
            const htmlElement = document.documentElement;
            
            // Check for saved theme or prefer-color-scheme
            const savedTheme = localStorage.getItem('theme') || 'light';
            const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            
            // Set initial theme
            if (savedTheme === 'dark' || (!savedTheme && prefersDark)) {
                htmlElement.setAttribute('data-bs-theme', 'dark');
                if (themeToggle) {
                    themeToggle.innerHTML = '<i class="fas fa-sun"></i>';
                }
            }
            
            // Toggle theme
            if (themeToggle) {
                themeToggle.addEventListener('click', function() {
                    const currentTheme = htmlElement.getAttribute('data-bs-theme');
                    const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
                    
                    htmlElement.setAttribute('data-bs-theme', newTheme);
                    localStorage.setItem('theme', newTheme);
                    
                    this.innerHTML = newTheme === 'dark' 
                        ? '<i class="fas fa-sun"></i>' 
                        : '<i class="fas fa-moon"></i>';
                    
                    // Show theme change notification
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: `Mode ${newTheme === 'dark' ? 'Gelap' : 'Terang'} diaktifkan`,
                        showConfirmButton: false,
                        timer: 1500,
                        timerProgressBar: true
                    });
                });
            }
            
            // Back to Top Button
            const backToTopBtn = document.getElementById('backToTop');
            
            window.addEventListener('scroll', function() {
                if (backToTopBtn) {
                    if (window.pageYOffset > 300) {
                        backToTopBtn.style.display = 'flex';
                        backToTopBtn.style.alignItems = 'center';
                        backToTopBtn.style.justifyContent = 'center';
                    } else {
                        backToTopBtn.style.display = 'none';
                    }
                }
            });
            
            if (backToTopBtn) {
                backToTopBtn.addEventListener('click', function() {
                    window.scrollTo({
                        top: 0,
                        behavior: 'smooth'
                    });
                });
            }
            
            // Form Validation Enhancement
            const forms = document.querySelectorAll('.needs-validation');
            forms.forEach(form => {
                form.addEventListener('submit', function(event) {
                    if (!form.checkValidity()) {
                        event.preventDefault();
                        event.stopPropagation();
                        
                        // Highlight invalid fields
                        const invalidFields = form.querySelectorAll(':invalid');
                        invalidFields.forEach(field => {
                            field.classList.add('is-invalid');
                            
                            // Add error message if not exists
                            if (!field.nextElementSibling || !field.nextElementSibling.classList.contains('invalid-feedback')) {
                                const errorDiv = document.createElement('div');
                                errorDiv.className = 'invalid-feedback';
                                errorDiv.textContent = field.validationMessage || 'Field ini wajib diisi';
                                field.parentNode.insertBefore(errorDiv, field.nextSibling);
                            }
                        });
                        
                        // Scroll to first invalid field
                        if (invalidFields.length > 0) {
                            invalidFields[0].scrollIntoView({
                                behavior: 'smooth',
                                block: 'center'
                            });
                        }
                    }
                    
                    form.classList.add('was-validated');
                });
                
                // Real-time validation
                form.querySelectorAll('input, textarea, select').forEach(field => {
                    field.addEventListener('input', function() {
                        if (this.classList.contains('is-invalid')) {
                            this.classList.remove('is-invalid');
                            const errorMsg = this.nextElementSibling;
                            if (errorMsg && errorMsg.classList.contains('invalid-feedback')) {
                                errorMsg.remove();
                            }
                        }
                    });
                });
            });
            
            // Auto-dismiss alerts
            const alerts = document.querySelectorAll('.alert:not(.alert-permanent)');
            alerts.forEach(alert => {
                setTimeout(() => {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                }, 5000);
            });
            
            // Copy to clipboard functionality
            document.querySelectorAll('[data-copy]').forEach(button => {
                button.addEventListener('click', function() {
                    const textToCopy = this.getAttribute('data-copy');
                    navigator.clipboard.writeText(textToCopy).then(() => {
                        const originalHTML = this.innerHTML;
                        this.innerHTML = '<i class="fas fa-check me-1"></i> Tersalin!';
                        this.classList.add('btn-success');
                        this.classList.remove('btn-outline-secondary');
                        
                        setTimeout(() => {
                            this.innerHTML = originalHTML;
                            this.classList.remove('btn-success');
                            this.classList.add('btn-outline-secondary');
                        }, 2000);
                    });
                });
            });
            
            // Initialize tooltips
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            const tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
            
            // Initialize popovers
            const popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
            const popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
                return new bootstrap.Popover(popoverTriggerEl);
            });
            
            // Smooth scroll for anchor links
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function (e) {
                    const href = this.getAttribute('href');
                    if (href !== '#') {
                        e.preventDefault();
                        const target = document.querySelector(href);
                        if (target) {
                            target.scrollIntoView({
                                behavior: 'smooth',
                                block: 'start'
                            });
                        }
                    }
                });
            });
            
            // Session timeout warning
            let timeoutWarning;
            const resetTimer = () => {
                clearTimeout(timeoutWarning);
                timeoutWarning = setTimeout(() => {
                    Swal.fire({
                        title: 'Sesi akan berakhir',
                        text: 'Sesi Anda akan berakhir dalam 1 menit karena tidak ada aktivitas.',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Tetap Login',
                        cancelButtonText: 'Logout'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Reset session
                            fetch('../auth/keepalive.php')
                                .then(() => resetTimer())
                                .catch(() => window.location.href = '../auth/logout.php');
                        } else {
                            window.location.href = '../auth/logout.php';
                        }
                    });
                }, 29 * 60 * 1000); // 29 minutes
            };
            
            // Reset timer on user activity
            ['mousedown', 'mousemove', 'keypress', 'scroll', 'touchstart'].forEach(event => {
                document.addEventListener(event, resetTimer);
            });
            
            resetTimer(); // Start the timer
            
            // Print functionality
            window.printPage = function() {
                window.print();
            };
            
            // Export functionality
            window.exportData = function(format) {
                Swal.fire({
                    title: 'Mengekspor Data',
                    text: `Menyiapkan data dalam format ${format.toUpperCase()}...`,
                    icon: 'info',
                    showConfirmButton: false,
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                        // Simulate export process
                        setTimeout(() => {
                            Swal.fire({
                                title: 'Berhasil!',
                                text: `Data berhasil diekspor dalam format ${format.toUpperCase()}`,
                                icon: 'success',
                                confirmButtonText: 'OK'
                            });
                        }, 1500);
                    }
                });
            };
            
            // Initialize animations on scroll
            const observerOptions = {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            };
            
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('animate-fade-in');
                        observer.unobserve(entry.target);
                    }
                });
            }, observerOptions);
            
            // Observe elements with animation class
            document.querySelectorAll('.animate-on-scroll').forEach(el => {
                observer.observe(el);
            });
        });
        
        // Global error handler
        window.addEventListener('error', function(e) {
            console.error('Global error:', e.error);
            
            // Show user-friendly error message
            if (e.error && e.error.message && !e.error.message.includes('ResizeObserver')) {
                Swal.fire({
                    title: 'Terjadi Kesalahan',
                    text: 'Maaf, terjadi kesalahan tak terduga. Silakan refresh halaman.',
                    icon: 'error',
                    confirmButtonText: 'Refresh',
                    allowOutsideClick: false
                }).then(() => {
                    window.location.reload();
                });
            }
        });
        
        // Handle offline/online status
        window.addEventListener('online', function() {
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: 'Koneksi internet kembali',
                showConfirmButton: false,
                timer: 2000
            });
        });
        
        window.addEventListener('offline', function() {
            Swal.fire({
                title: 'Anda Offline',
                text: 'Koneksi internet terputus. Beberapa fitur mungkin tidak tersedia.',
                icon: 'warning',
                confirmButtonText: 'OK'
            });
        });
    </script>
    
    <!-- Page Specific JavaScript -->
    <?php if (isset($page_js)): ?>
        <script><?= $page_js ?></script>
    <?php endif; ?>
    
    <!-- Google Analytics (Optional) -->
    <?php if (isset($GA_TRACKING_ID)): ?>
    <script async src="https://www.googletagmanager.com/gtag/js?id=<?= $GA_TRACKING_ID ?>"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', '<?= $GA_TRACKING_ID ?>');
    </script>
    <?php endif; ?>
</body>
</html>