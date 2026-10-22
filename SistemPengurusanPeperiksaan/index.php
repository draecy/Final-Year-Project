<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SPP PSIS - Sign In</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4f6f9;
            height: 100vh;
        }
        .login-card {
            border-top: 5px solid #0d3b66;
            border-bottom: 5px solid #f4d35e;
            border-radius: 8px;
        }
        .btn-psis {
            background-color: #0d3b66;
            color: white;
        }
        .btn-psis:hover {
            background-color: #0b3052;
            color: white;
        }
        .brand-title {
            color: #0d3b66;
            font-weight: 800;
            letter-spacing: 0.5px;
        }
    </style>
</head>
<body class="d-flex align-items-center justify-content-center">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="text-center mb-4">
                    <h2 class="brand-title mb-1">SPP PSIS</h2>
                    <p class="text-muted small text-uppercase fw-bold">Sistem Pengurusan Peperiksaan PSIS</p>
                </div>

                <?php if (isset($_GET['signup']) && $_GET['signup'] == 'success'): ?>
                        <div class="alert alert-success text-center small py-2" role="alert">
                            Pendaftaran berjaya! Sila log masuk.
                        </div>
                <?php endif; ?>

                <?php if (isset($_GET['error'])): ?>
                        <div class="alert alert-danger text-center small py-2" role="alert">
                            <?php 
                                if ($_GET['error'] == 'emptyfields') echo "Sila isi semua ruangan!";
                                elseif ($_GET['error'] == 'invalidcredentials') echo "E-mel atau kata laluan salah!";
                                elseif ($_GET['error'] == 'systemerror') echo "Ralat sistem! Sila cuba lagi.";
                            ?>
                        </div>
                <?php endif; ?>

                <div class="card shadow-sm login-card bg-white p-4">
                    <h5 class="fw-bold text-secondary mb-3 text-center">Log Masuk Pelajar</h5>
                    <form action="user_login_process.php" method="POST" class="needs-validation" novalidate>
                        
                        <div class="mb-3">
                            <label for="email" class="form-label small fw-bold text-muted">Akaun E-mel</label>
                            <input type="email" class="form-control" id="email" name="email" placeholder="e.g., pelajar@gmail.com" value="<?php echo isset($_GET['email']) ? htmlspecialchars($_GET['email']) : ''; ?>" required>
                            <div class="invalid-feedback">Sila masukkan e-mel yang sah.</div>
                        </div>

                        <div class="mb-4">
                            <label for="password" class="form-label small fw-bold text-muted">Kata Laluan</label>
                            <input type="password" class="form-control" id="password" name="password" placeholder="Minimum 6 aksara" required>
                            <div class="invalid-feedback" id="password-feedback">Sila masukkan kata laluan.</div>
                        </div>

                        <button type="submit" class="btn btn-psis w-100 fw-bold py-2 shadow-sm mb-3">
                            Log Masuk
                        </button>
                        
                        <div class="text-center">
                            <a href="password_forgot.php" class="text-decoration-none small text-muted">Lupa Kata Laluan?</a><br>
                            <span class="small text-muted">Tiada akaun? </span>
                            <a href="user_register.php" class="text-decoration-none small fw-bold" style="color: #0d3b66;">Daftar Akaun disini</a><br>
                            <a href="admin_login.php" class="text-decoration-none small fw-bold" style="color: #0d3b66;">Log Masuk Staf disini</a>
                        </div>
                        
                    </form>
                </div>
            </div>
        </div>
        <br><p class="footer-text text-center">&copy; 2026 Sistem Pengurusan Peperiksaan PSIS. Hak Cipta Terpelihara.</p>
    </div>
    <script>
    (() => {
        'use strict';

        const forms = document.querySelectorAll('.needs-validation');

        Array.from(forms).forEach(form => {
            const passwordInput = form.querySelector('#password');
            const passwordFeedback = form.querySelector('#password-feedback');

            if (passwordInput) {
                passwordInput.addEventListener('input', () => {
                    const val = passwordInput.value;

                    if (val.length >= 6) {
                        passwordInput.setCustomValidity(''); 
                        passwordInput.classList.remove('is-invalid');
                        passwordInput.classList.add('is-valid');
                    } else if (val.length > 0) {
                        passwordInput.setCustomValidity('Minimum 6 characters required.');
                        passwordInput.classList.remove('is-valid');
                        passwordInput.classList.add('is-invalid');
                        if (passwordFeedback) {
                            passwordFeedback.textContent = "Kata laluan mestilah sekurang-kurangnya 6 aksara.";
                        }
                    } else {
                        passwordInput.setCustomValidity('Password is required.');
                        passwordInput.classList.remove('is-valid', 'is-invalid');
                        if (passwordFeedback) {
                            passwordFeedback.textContent = "Sila masukkan kata laluan.";
                        }
                    }
                });
            }

            form.addEventListener('submit', event => {
                if (passwordInput && passwordInput.value.length < 6) {
                    passwordInput.setCustomValidity('Minimum 6 characters required.');
                    passwordInput.classList.add('is-invalid');
                }

                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                }

                form.classList.add('was-validated');
            }, false);
        });
    })();
    </script>
</body>
</html>