<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SPP PSIS - Staff Sign Up</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4f6f9;
            height: 100vh;
        }
        .register-card {
            border-top: 5px solid #420d66;
            border-bottom: 5px solid #f4d35e;
            border-radius: 8px;
        }
        .btn-psis {
            background-color: #460d6b;
            color: white;
        }
        .btn-psis:hover {
            background-color: #350a52;
            color: white;
        }
        .brand-title {
            color: #420d66;
            font-weight: 800;
            letter-spacing: 0.5px;
        }
    </style>
</head>
<body class="d-flex align-items-center justify-content-center">

    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                
                <div class="text-center mb-4">
                    <h2 class="brand-title mb-1">SPP PSIS</h2>
                    <p class="text-muted small text-uppercase fw-bold">Sistem Pengurusan Peperiksaan PSIS</p>
                </div>

                <div class="card shadow-sm register-card bg-white p-4">
                    <h5 class="fw-bold text-secondary mb-3 text-center">Pendaftaran Staff</h5>
                 
                    <?php if (isset($_GET['error'])): ?>
                        <div class="alert alert-danger text-center small py-2" role="alert">
                            <?php 
                                if ($_GET['error'] == 'emptyfields') echo "Sila isi semua ruangan!";
                                elseif ($_GET['error'] == 'passwordmismatch') echo "Kata laluan dan pengesahan kata laluan tidak sepadan!";
                                elseif ($_GET['error'] == 'invalidemail') echo "Sila masukkan akaun email yang sah!";
                                elseif ($_GET['error'] == 'userexists') echo "Email ini telah pun didaftarkan!";
                                elseif ($_GET['error'] == 'systemerror') echo "Ralat sistem! Sila cuba lagi.";
                            ?>
                        </div>
                    <?php endif; ?>

                    <form action="admin_register_process.php" method="POST">

                        <div class="mb-3">
                            <label for="email" class="form-label small fw-bold text-muted">Akaun Email</label>
                            <input type="text" class="form-control" id="email" name="email" placeholder="e.g., nama@staff.psis.edu.my" pattern="^[a-zA-Z0-9._%+-]+@staff\.psis\.edu\.my$" value="<?php echo isset($_GET['email']) ? htmlspecialchars($_GET['email']) : ''; ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="full_name" class="form-label small fw-bold text-muted">Nama Penuh</label>
                            <input type="text" class="form-control" id="full_name" name="full_name" placeholder="Nama Penuh" value="<?php echo isset($_GET['full_name']) ? htmlspecialchars($_GET['full_name']) : ''; ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label small fw-bold text-muted">Kata Laluan</label>
                            <input type="password" class="form-control" id="password" name="password" placeholder="Minimum 6 aksara" required>
                        </div>

                        <div class="mb-4">
                            <label for="confirm_password" class="form-label small fw-bold text-muted">Sahkan Kata Laluan</label>
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="Masukkan semula kata laluan" required>
                        </div>

                        <button type="submit" class="btn btn-psis w-100 fw-bold py-2 shadow-sm mb-3">
                            Daftar Akaun
                        </button>
                        
                        <div class="text-center">
                            <span class="small text-muted">Sudah mempunyai akaun? </span>
                            <a href="admin_login.php" class="text-decoration-none small fw-bold" style="color: #0d3b66;">Log Masuk disini</a>
                        </div>
                        
                    </form>
                </div>
            </div>
        </div>
        <br><p class="footer-text text-center">&copy; 2026 Sistem Pengurusan Peperiksaan PSIS. Hak Cipta Terpelihara.</p>
    </div>

    <script>
        const password = document.getElementById('password');
        const confirmPassword = document.getElementById('confirm_password');

        function validatePassword(){
            const passValue = password.value;
            const confirmValue = confirmPassword.value;

            if (passValue === '' && confirmValue === '') {
                password.classList.remove('is-valid', 'is-invalid');
                confirmPassword.classList.remove('is-valid', 'is-invalid');
                return;
            }
            if (passValue.length >= 6 && passValue === confirmValue) {
                password.classList.add('is-valid');
                password.classList.remove('is-invalid');
                
                confirmPassword.classList.add('is-valid');
                confirmPassword.classList.remove('is-invalid');
            } else {
                if (confirmValue.length > 0 || passValue.length < 6) {
                    password.classList.add('is-invalid');
                    password.classList.remove('is-valid');

                    confirmPassword.classList.add('is-invalid');
                    confirmPassword.classList.remove('is-valid');
                }
            }
        }

        password.addEventListener('input', validatePassword);
        confirmPassword.addEventListener('input', validatePassword);

        /*document.getElementById('authForm').addEventListener('submit', function(e) {
            const emailInput = document.getElementById('email').value.trim();
            const domain = '@staff.psis.edu.my';

            if (!emailInput.toLowerCase().endsWith(domain)) {
                e.preventDefault();
                alert('Access restricted! You must use a valid @staff.psis.edu.my email address.');
            }
        });*/
    </script>
</body>
</html>