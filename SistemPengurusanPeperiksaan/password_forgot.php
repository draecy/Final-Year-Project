<?php

require_once 'config.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$error_msg = '';
$success_msg = '';

if (isset($_POST['submit_forgot'])) {
    $email = trim($_POST['email']);

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_msg = "Please enter a valid email address.";
    } else {
        try {
            // 1. Check if email exists in 'admin' or 'users'
            $stmt = $pdo->prepare("SELECT id, 'admin' AS role FROM admin WHERE email = ? UNION SELECT id, 'user' AS role FROM users WHERE email = ?");
            $stmt->execute([$email, $email]);
            $account = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($account) {
                $table = ($account['role'] === 'admin') ? 'admin' : 'users';

                // 2. Generate secure token & 1-hour expiration
                $token = bin2hex(random_bytes(32));
                $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));

                // 3. Save token in the appropriate table
                $update = $pdo->prepare("UPDATE {$table} SET reset_password = ?, reset_password_expires = ? WHERE email = ?");
                $update->execute([$token, $expires, $email]);

                // 4. Construct Dynamic Domain Link (Localhost & Production Auto-detect)
                $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
                $host = $_SERVER['HTTP_HOST'];

                // Detection for folder structure vs standalone domain
                $app_folder = (strpos($host, 'localhost') !== false) ? '/SPP(PSIS)' : '';
                $domain = $protocol . $host . $app_folder;
                $reset_link = "{$domain}/password_reset.php?token={$token}&type={$account['role']}";

                // 5. Configuration with Localhost Fallbacks (Deployment-Ready)
                $fromName = getenv('SMTP_FROM_NAME') ?: 'SPP PSIS System';

                // 5. Send Email via PHPMailer
                $mail = new PHPMailer(true);

                $mail->isSMTP();
                $mail->Host       = $smtpHost;
                $mail->SMTPAuth   = true;
                $mail->Username   = $smtpUser; 
                $mail->Password   = $smtpPass;
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port       = (int)$smtpPort;
                $mail->CharSet    = 'UTF-8';

                $mail->SMTPOptions = [
                    'ssl' => [
                        'verify_peer'       => false,
                        'verify_peer_name'  => false,
                        'allow_self_signed' => true
                    ]
                ];

                $mail->setFrom($smtpUser, $fromName);
                $mail->addAddress($email);

                $mail->isHTML(true);
                $mail->Subject = 'Reset Your Password - SPP PSIS';
                $mail->Body    = "<div style='font-family: Arial, sans-serif; padding: 20px; border: 1px solid #ddd; border-radius: 5px; max-width: 500px;'>
                                    <h3 style='color: #0d3b66; margin-top: 0;'>Password Reset Request</h3>
                                    <p style='color: #333;'>Click the button below to set a new password. This link expires in 1 hour:</p>
                                    <p style='margin: 20px 0;'>
                                        <a href='{$reset_link}' style='padding: 10px 18px; background-color: #0d3b66; color: #ffffff; text-decoration: none; border-radius: 5px; display: inline-block; font-weight: bold;'>Reset Password</a>
                                    </p>
                                    <p style='color: #777; font-size: 12px;'>If you did not request this, please ignore this email.</p>
                                    <hr style='border: none; border-top: 1px solid #eee; margin: 20px 0;'>
                                    <small style='color: #777;'>Sistem Pengurusan Peperiksaan PSIS</small>
                                </div>";

                $mail->send();
                $success_msg = "A password reset link has been sent to your email.";
            } else {
                // Security practice: Don't reveal whether an email exists or not
                $success_msg = "If that email is registered, a password reset link has been sent.";
            }
        } catch (Exception $e) {
            $error_msg = "Mailer Error: " . $mail->ErrorInfo;
        } catch (PDOException $e) {
            $error_msg = "Database Error: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Forgot Password</title>
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
    <div class="container mt-5" style="max-width: 450px;">
        <div class="card shadow-sm" login-card bg-white p-4>
            <h3 class="card-title text-center mb-4">Forgot Password</h3>
                
            <?php if (!empty($error_msg)): ?>
                <div class="alert alert-danger text-center small py-2">
                    <?php echo htmlspecialchars($error_msg); ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($success_msg)): ?>
                <div class="alert alert-success text-center small py-2">
                    <?php echo $success_msg; ?>
                </div>
            <?php endif; ?>

            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">Enter your email address</label>
                    <input type="email" name="email" class="form-control" placeholder="example@gmail.com" required>
                </div>
                <button type="submit" name="submit_forgot" class="btn btn-primary w-100">Send Reset Link</button>
                <div class="text-center mt-3">
                    <a href="index.php" class="text-decoration-none">Back to Login</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>