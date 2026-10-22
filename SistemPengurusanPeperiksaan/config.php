<?php
     $cookieLifetime = 7200;
     // Tetapkan jangka hayat cookie session
     session_set_cookie_params([
          'lifetime' => $cookieLifetime,
          'path'     => '/',
          'domain'   => '',      // Biarkan kosong untuk domain semasa
          'secure'   => false,   // Tukar ke true jika menggunakan HTTPS
          'httponly' => true,    // Mencegah akses JavaScript (keselamatan XSS)
          'samesite' => 'Lax'
     ]);

     if (session_status() === PHP_SESSION_NONE) {
          session_start();
     }

     if (!class_exists('PHPMailer\PHPMailer\Exception')) {
          require_once __DIR__ . '/phpmailer/src/Exception.php';
          require_once __DIR__ . '/phpmailer/src/PHPMailer.php';
          require_once __DIR__ . '/phpmailer/src/SMTP.php';
     }

     // Load PHPMailer classes
     use PHPMailer\PHPMailer\PHPMailer;
     use PHPMailer\PHPMailer\Exception;
     use PHPMailer\PHPMailer\SMTP;

     $host = '127.0.0.1';
     $port = '3307';
     $db   = 'spp_psis';
     $user = 'root';
     $pass = ''; 
     $charset = 'utf8mb4';

     $dsn = "mysql:host=$host;port=$port;dbname=$db;charset=$charset";
     $options = [
          PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
          PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
          PDO::ATTR_EMULATE_PREPARES   => false,
          PDO::ATTR_TIMEOUT            => 5
     ];

     try {
          $pdo = new PDO($dsn, $user, $pass, $options);
     } catch (PDOException $e) {
          http_response_code(500);
          exit("Database connection failed: " . $e->getMessage());
     }

     /* Universal Notification Function (Database + Email) */
     function sendNotification($pdo, $recipient_user_id, $recipient_email, $title, $message, $role) {
          // Save In-System Notification to Database
          $stmt = $pdo->prepare("INSERT INTO notifications (user_id, title, message, role) VALUES (?, ?, ?, ?)");
          $stmt->execute([$recipient_user_id, $title, $message, $role]);

          // Send Email Notification via PHPMailer
          if (!empty($recipient_email)) {
               $mail = new PHPMailer(true);
               try {
                    // Environment Configuration with Localhost Fallbacks
                    $smtpHost = getenv('SMTP_HOST')?: 'smtp.gmail.com';
                    $smtpUser = getenv('SMTP_USER') ?: 'upep@staff.psis.edu.my';
                    $smtpPass = getenv('SMTP_PASS') ?: 'qerteqjizfvmqepq';
                    $smtpPort = getenv('SMTP_PORT')?: 587;
                    $fromName = getenv('SMTP_FROM_NAME') ?: 'SPP PSIS Notification';
                    
                    // Server settings
                    $mail->isSMTP();
                    $mail->Host       = $smtpHost;
                    $mail->SMTPAuth   = true;
                    $mail->Username   = $smtpUser; 
                    $mail->Password   = $smtpPass;   
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                    $mail->Port       = $smtpPort;
                    $mail->Timeout    = 10;
                    $mail->CharSet    = 'UTF-8';

                    $mail->SMTPOptions = [
                         'ssl' => [
                              'verify_peer'       => false,
                              'verify_peer_name'  => false,
                              'allow_self_signed' => true
                         ]
                    ];

                    // Recipients
                    $mail->setFrom($smtpUser, $fromName);
                    $mail->addAddress($recipient_email);

                   // Logik Penentuan CC yang diperkukuh
                    $email_to_cc = null;

                    // Priority 1: Guna $cc_email yang dihantar ke fungsi
                    if (!empty($cc_email) && filter_var($cc_email, FILTER_VALIDATE_EMAIL)) {
                         $email_to_cc = trim($cc_email);
                    } 
                    // Priority 2: Fallback ke session jika $cc_email kosong
                    elseif (!empty($_SESSION['session_email']) && filter_var($_SESSION['session_email'], FILTER_VALIDATE_EMAIL)) {
                         $email_to_cc = trim($_SESSION['session_email']);
                    } elseif (!empty($_SESSION['email']) && filter_var($_SESSION['email'], FILTER_VALIDATE_EMAIL)) {
                         $email_to_cc = trim($_SESSION['email']);
                    }

                    // Tambah CC jika e-mel CC sah dan tak sama dengan penerima utama
                    if ($email_to_cc && strtolower($email_to_cc) !== strtolower(trim($recipient_email))) {
                         $mail->addCC($email_to_cc);
                    }

                    // Content
                    $mail->isHTML(true);
                    $mail->Subject = $title;
                    $mail->Body    = "<div style='font-family: Arial, sans-serif; padding: 20px; border: 1px solid #ddd; border-radius: 5px;'>
                                        <h3 style='color: #0d3b66; margin-top: 0;'>".htmlspecialchars($title)."</h3>
                                        <p style='color: #333; line-height: 1.5;'>".nl2br(htmlspecialchars($message))."</p>
                                        <hr style='border: none; border-top: 1px solid #eee; margin: 20px 0;'>
                                        <small style='color: #777;'>Sistem Pengurusan Peperiksaan PSIS (SPP PSIS)</small>
                                   </div>";
                    $mail->send();
               } catch (Exception $e) {
                    // Log email error if needed, but allow database execution to proceed
                    error_log("Email sending failed to {$recipient_email}: " . $mail->ErrorInfo);
               }
          }
     }
?>