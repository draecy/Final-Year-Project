<?php
    require_once 'config.php'; 

    if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'student') {
        header("Location: index.php");
        exit();
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $student_id = $_SESSION['user_id'];
        $applicant_name  = trim($_POST['applicant_name'] ?? '');
        $reg_no          = trim($_POST['reg_no'] ?? '');
        $ic_no           = trim($_POST['ic_no'] ?? '');
        $phone_no        = trim($_POST['phone_no'] ?? '');
        $address         = trim($_POST['address'] ?? '');
        $cause_of_damage = trim($_POST['cause_of_damage'] ?? '');

        $app_type_arr    = $_POST['app_type'] ?? [];
        $app_types_str   = implode(', ', $app_type_arr);

        $delivery_type   = $_POST['delivery_type'] ?? '';
        $postal_address  = ($delivery_type === 'pos') ? trim($_POST['postal_address'] ?? '') : NULL;
    
        $support_doc_path = '';
        if (isset($_FILES['support_document_file']) && $_FILES['support_document_file']['error'] === UPLOAD_ERR_OK) {
            $fileTmpPath   = $_FILES['support_document_file']['tmp_name'];
            $fileName      = $_FILES['support_document_file']['name'];
            $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
            
            $allowedExtensions = ['pdf', 'jpg', 'jpeg', 'png'];
            if (in_array($fileExtension, $allowedExtensions)) {
                $uploadDir = 'uploads/documents/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }
                $newFileName      = 'PENGECUALIAN_' . time() . '_' . substr(md5(uniqid()), 0, 5) . '.' . $fileExtension;
                $support_doc_path = $uploadDir . $newFileName;

                if (!move_uploaded_file($fileTmpPath, $support_doc_path)) {
                    header("Location: borang_pengecualian.php?error=uploadfailed");
                    exit();
                }
            } else {
                header("Location: borang_pengecualian.php?error=invalidfile");
                exit();
            }
        } else {
            header("Location: borang_pengecualian.php?error=missingfile");
            exit();
        }

        $signature_data = $_POST['signature'] ?? '';

        try { 
            $pdo->beginTransaction();

            // Fetch total count using PDO
            $stmtCount = $pdo->query("SELECT COUNT(*) AS total FROM permohonan_pengecualian");
            $row = $stmtCount->fetch(PDO::FETCH_ASSOC);
            $number = ($row['total'] ?? 0) + 1;
            $reference_id = "PBST-" . str_pad($number, 3, "0", STR_PAD_LEFT);

            // Insert into permohonan_pengecualian
            $sql1 = "INSERT INTO permohonan_pengecualian (
                        application_no, applicant_name, reg_no, ic_no, phone_no, address, cause_of_damage,
                        app_types, delivery_type, postal_address,
                        support_document_path, signature_data, status)
                        VALUES ( ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'In Review')";
            $stmt1 = $pdo->prepare($sql1);
            $stmt1->execute([
                $reference_id, $applicant_name, $reg_no, $ic_no, $phone_no, $address, $cause_of_damage,
                $app_types_str, $delivery_type, $postal_address,
                $support_doc_path, $signature_data
            ]);

            // Insert into main applications table
            $application_type = "Pengecualian Bayaran Sijil / Transkrip";
            $sql2 = "INSERT INTO applications (student_id, reference_id, application_type, status) VALUES (?, ?, ?, 'In Review')";
            $stmt2 = $pdo->prepare($sql2);
            $stmt2->execute([$student_id, $reference_id, $application_type]);

            $pdo->commit();

        } catch (PDOException $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }

            // Clean up uploaded file if DB process failed
            if (!empty($support_doc_path) && file_exists($support_doc_path)) {
                unlink($support_doc_path);
            }
            // Temporary debugging: display error details
            echo "<h3>Database Error Details:</h3>";
            echo "<pre>" . $e->getMessage() . "</pre>";
            exit();
        }

        // Send Email Notification (Outside transaction so email failure won't rollback DB submission)
        $email_cc = $_SESSION['email'] ?? '';
        $admin_id    = 1;
        $admin_email = getenv('ADMIN_NOTIFICATION_EMAIL') ?: 'upep@staff.psis.edu.my'; 
        $notif_title = "Permohonan Baru Diterima";
        $notif_msg   = "Pelajar {$applicant_name} {$reg_no} telah menghantar permohonan Pengecualian Bayaran baru.";
        
        if (function_exists('sendNotification')) {
            sendNotification($pdo, $admin_id, $admin_email, $notif_title, $notif_msg, 'admin', $email_cc);
        }

        header("Location: user_dashboard.php?status=success&type=pengecualian");
        exit();
    } else {
        header("Location: user_dashboard.php");
        exit();
    }
?>