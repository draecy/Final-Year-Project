<?php
    require_once 'config.php';

    if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'student') {
        header("Location: index.php");
        exit();
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        $user_primary_id = $_SESSION['user_id'] ?? $_SESSION['id'] ?? null;
        // Sanitize & Retrieve Text Inputs
        $applicant_name     = trim($_POST['applicant_name'] ?? '');
        $ic_no              = trim($_POST['ic_no'] ?? '');
        $reg_no             = !empty($_POST['reg_no']) ? trim($_POST['reg_no']) : NULL;
        $phone_no           = trim($_POST['phone_no'] ?? '');
        $graduation_session = !empty($_POST['graduation_session']) ? trim($_POST['graduation_session']) : NULL;
        // Proxy Details
        $proxy_name         = !empty($_POST['proxy_name']) ? trim($_POST['proxy_name']) : NULL;
        $proxy_ic           = !empty($_POST['proxy_ic']) ? trim($_POST['proxy_ic']) : NULL;
        $proxy_relationship = !empty($_POST['proxy_relationship']) ? trim($_POST['proxy_relationship']) : NULL;
        $proxy_phone        = !empty($_POST['proxy_phone']) ? trim($_POST['proxy_phone']) : NULL;

        $application_date   = $_POST['application_date'] ?? date('Y-m-d');
        
        $uploadedFiles = [];
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        if (!is_dir($signatureFolder)) {
            mkdir($signatureFolder, 0777, true);
        }

        // 2. File Upload Function
        function uploadDoc($fileInputName, $isMandatory = false) {
            if (!isset($_FILES[$fileInputName]) || $_FILES[$fileInputName]['error'] === UPLOAD_ERR_NO_FILE) {
                if ($isMandatory) {
                    header("Location: borang_transkrip.php?error=missingfile");
                    exit();
                }
                return NULL;
            }

            $file = $_FILES[$fileInputName];

            if ($file['error'] === UPLOAD_ERR_OK) {
                header("Location: borang_transkrip.php?error=uploaderror");
                exit();
            }
            
            // Allowed Extensions
            $allowedTypes = ['jpg', 'jpeg', 'png', 'pdf'];
            $fileExt = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

            if (!in_array($fileExt, $allowedTypes)) {
                header("Location: borang_transkrip.php?error=invalidformat");
                exit();
            }

            $newFileName = strtoupper($fileInputName) . "_" . time() . "_" . substr(md5(uniqid()), 0, 5) . "." . $fileExt;
            $destination = $uploadDir . $newFileName;

            if (move_uploaded_file($file['tmp_name'], $destination)) {
                $uploadedFiles[] = $destination;
                return $destination;
            } else {
                header("Location: borang_transkrip.php?error=savefailed");
                exit();
            }
        }

        // Process Document Uploads
        $doc_ic_applicant_path  = uploadDoc('doc_ic_applicant_path', true, $uploadedFiles);  // Mandatory
        $doc_ic_proxy_path      = uploadDoc('doc_ic_proxy_path', false, $uploadedFiles);      // Optional

        $signatureFolder = "../uploads/signatures/";
        $signature = "";
        if (!empty($_POST['signature'])) {
            $signatureData = $_POST['signature'];
            $signatureData = str_replace('data:image/png;base64,', '', $signatureData);
            $signatureData = str_replace(' ', '+', $signatureData);
            $signatureFile = "signature_" . uniqid() . ".png";

            file_put_contents($signatureFolder . $signatureFile, base64_decode($signatureData));
            $signature = $signatureFile;
        }

        try {
            $pdo->beginTransaction();

            // Fetch total count using PDO
            $stmtCount = $pdo->query("SELECT COUNT(*) AS total FROM permohonan_transkrip");
            $row = $stmtCount->fetch(PDO::FETCH_ASSOC);
            $number = ($row['total'] ?? 0) + 1;
            $reference_id = "TAR-" . str_pad($number, 3, "0", STR_PAD_LEFT);

            // Database Insertion using MySQLi Prepared Statements
            $sql = "INSERT INTO permohonan_transkrip 
                    (application_no, applicant_name, ic_no, reg_no, phone_no, graduation_session, 
                    proxy_name, proxy_ic, proxy_relationship, proxy_phone, 
                    signature_data, application_date, doc_ic_applicant_path, doc_ic_proxy_path, status) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'In Review')";

            // Guna objek $pdo dari config.php
            $stmt1 = $pdo->prepare($sql);
            $stmt1->execute([
                $reference_id,
                $applicant_name, 
                $ic_no, 
                $reg_no, 
                $phone_no, 
                $graduation_session, 
                $proxy_name, 
                $proxy_ic, 
                $proxy_relationship, 
                $proxy_phone, 
                $signature_filename, 
                $application_date, 
                $doc_ic_applicant_path, 
                $doc_ic_proxy_path
            ]);

            $main_app_type = "Transkrip Akademik Rasmi";
            $sql2 = "INSERT INTO applications (student_id, reference_id, application_type, status) VALUES (?, ?, ?, 'In Review')";
            $stmt2 = $pdo->prepare($sql2);
            $stmt2->execute([$student_id, $reference_id, $main_app_type]);

            $pdo->commit();
        } catch (PDOException $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }

            // Clean up uploaded files if DB process failed
            foreach ($uploadedFiles as $filePath) {
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
            }

            error_log("Transkrip Submission Error: " . $e->getMessage());
            header("Location: borang_transkrip.php?error=systemerror");
            exit();
        }

        // Dynamic Admin Email Notification (Deployment-Ready)
        $email_cc = $_SESSION['email'] ?? '';
        $admin_id    = 1;
        $admin_email = getenv('ADMIN_NOTIFICATION_EMAIL') ?: 'upep@staff.psis.edu.my'; 
        $notif_title = "Permohonan Baru Diterima";
        $notif_msg   = "Pelajar {$applicant_name} {$reg_no} telah menghantar permohonan Transkrip Akademik Rasmi.";

        if (function_exists('sendNotification')) {
            sendNotification($pdo, $admin_id, $admin_email, $notif_title, $notif_msg, 'admin', $email_cc);
        }

        header("Location: user_dashboard.php?status=success&type=transkrip&id=" . $insert_id);
        exit();
    } else {
        header("Location: user_dashboard.php");
        exit();
    }
?>