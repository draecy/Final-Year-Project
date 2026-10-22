<?php
    $valid_token = false;
    $token = $_GET['token'] ?? '';
    $type  = $_GET['type'] ?? 'user'; // Default to 'user' if not specified

    // Validate table name to prevent SQL injection
    $table = ($type === 'admin') ? 'admin' : 'users';

    if (!empty($token)) {
        try {
            // 1. Check if token exists in the designated table and is NOT expired
            $current_time = date('Y-m-d H:i:s');
            $stmt = $pdo->prepare("SELECT id FROM {$table} WHERE reset_password = ? AND reset_password_expires > ?");
            $stmt->execute([$token, $current_time]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                $valid_token = true;
            } else {
                $error_msg = "Invalid or expired password reset link.";
            }
        } catch (PDOException $e) {
            die("Database Error: " . $e->getMessage());
        }
    } else {
        $error_msg = "No reset token provided.";
    }

    // 2. Process new password form submission
    if (isset($_POST['submit_reset']) && $valid_token) {
        $new_password     = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];

        if ($new_password !== $confirm_password) {
            $error_msg = "Passwords do not match!";
        } elseif (strlen($new_password) < 6) {
            $error_msg = "Password must be at least 6 characters long.";
        } else {
            // Hash the new password securely
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

            try {
                // Update password and CLEAR token fields in the correct table
                $update_stmt = $pdo->prepare("UPDATE {$table} SET password = ?, reset_password = NULL, reset_password_expires = NULL WHERE reset_password = ?");
                $update_stmt->execute([$hashed_password, $token]);

                // Redirect to appropriate login page based on account type
                $redirect_page = ($type === 'admin') ? 'admin_login.php' : 'user_login.php';

                $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
                $host = $_SERVER['HTTP_HOST'];
                $app_folder = (strpos($host, 'localhost') !== false) ? '/SPP(PSIS)' : '';
                $target_url = $protocol . $host . $app_folder . '/' . $redirect_page;

                echo "<script>
                        alert('Your password has been successfully reset!');
                        window.location.href='{$target_url}';
                    </script>";
                exit();

            } catch (PDOException $e) {
                die("Database Error: " . $e->getMessage());
            }
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Set New Password - SPP PSIS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .login-card {
            border-top: 5px solid #0d3b66;
            border-bottom: 5px solid #f4d35e;
            border-radius: 8px;
        }
    </style>
</head>
<body class="bg-light d-flex align-items-center justify-content-center" style="min-height: 100vh;">
    <div class="container" style="max-width: 450px;">
        <div class="card shadow-sm border-0">
            <div class="card shadow-sm login-card bg-white p-4">
                <h3 class="card-title fw-bold mb-4" style="color: #0d3b66;">Set New Password</h3>

                <?php if (isset($error_msg)): ?>
                    <div class="alert alert-danger text-center small py-2"><?php echo htmlspecialchars($error_msg); ?></div>
                <?php endif; ?>

                <?php if ($valid_token): ?>
                    <form method="POST" id="resetForm" class="text-start">
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted">New Password</label>
                            <input type="password" name="new_password" id="password" class="form-control" placeholder="Minimum 6 characters" required minlength="6">
                            <div class="invalid-feedback">Password must be at least 6 characters.</div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted">Confirm New Password</label>
                            <input type="password" name="confirm_password" id="confirm_password" class="form-control" placeholder="Re-enter new password" required minlength="6">
                            <div class="invalid-feedback">Passwords do not match!</div>
                        </div>
                        <button type="submit" name="submit_reset" class="btn btn-primary w-100 fw-bold py-2 mt-2" style="background-color: #0d3b66; border: none;">
                            Update Password
                        </button>
                    </form>
                    <script>
                        const password = document.getElementById('password');
                        const confirmPassword = document.getElementById('confirm_password');
                        const form = document.getElementById('resetForm');

                        function validatePassword(){
                            const passValue = password.value;
                            const confirmValue = confirmPassword.value;

                            if (passValue === '' && confirmValue === '') {
                                password.classList.remove('is-valid', 'is-invalid');
                                confirmPassword.classList.remove('is-valid', 'is-invalid');
                                return false;
                            }
                            
                            if (passValue.length >= 6 && passValue === confirmValue) {
                                password.classList.add('is-valid');
                                password.classList.remove('is-invalid');
                                
                                confirmPassword.classList.add('is-valid');
                                confirmPassword.classList.remove('is-invalid');
                                return true;
                            } else {
                                password.classList.add('is-invalid');
                                password.classList.remove('is-valid');

                                confirmPassword.classList.add('is-invalid');
                                confirmPassword.classList.remove('is-valid');
                                return false;
                            }
                        }

                        password.addEventListener('input', validatePassword);
                        confirmPassword.addEventListener('input', validatePassword);

                        form.addEventListener('submit', function(e) {
                            if (!validatePassword()) {
                                e.preventDefault();
                            }
                        });
                    </script>
                <?php else: ?>
                    <div class="mt-3">
                        <a href="password_forgot.php" class="btn btn-primary btn-sm px-4">Request New Link</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>