<?php
    require_once 'config.php';

    if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'student') {
        header("Location: index.php");
        exit();
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        // Ambil ID utama pengguna dari session (sokong pelbagai nama session)
        $user_primary_id = $_SESSION['user_id'] ?? $_SESSION['id'] ?? null;

        $student_name       = $_POST['student_name'] ?? '';
        $ic_number          = $_POST['ic_number'] ?? '';
        $student_id         = $_POST['student_id'] ?? '';
        $address            = $_POST['address'] ?? '';
        $phone              = $_POST['phone'] ?? '';
        $programme          = $_POST['programme'] ?? '';
        $study_year         = $_POST['study_year'] ?? '';
        $graduation_session = $_POST['graduation_session'] ?? '';

        $collection_method  = $_POST['collection_method'] ?? '';
        $postal_address     = $_POST['postal_address'] ?? '';

        $application_date   = $_POST['application_date'] ?? date('Y-m-d');

        if (isset($_POST['application_type']) && is_array($_POST['application_type'])) {
            $application_type = implode(", ", $_POST['application_type']);
        } else {
            $application_type = "";
        }

        $year = date("Y");

        // Fetch total count using PDO
        $stmtCount = $pdo->query("SELECT COUNT(*) AS total FROM permohonan_gantian");
        $row = $stmtCount->fetch();
        $number = $row['total'] + 1;

        $application_no = "GSTA-" . str_pad($number, 3, "0", STR_PAD_LEFT);

        $documentFolder  = "../uploads/documents/";
        $signatureFolder = "../uploads/signatures/";

        // Cipta folder automatik jika belum wujud
        if (!is_dir($documentFolder)) {
            mkdir($documentFolder, 0777, true);
        }
        if (!is_dir($signatureFolder)) {
            mkdir($signatureFolder, 0777, true);
        }

        $uploadedFiles = [];

        function uploadFile($fileKey, $folder, $prefix, &$uploadedFiles) {
            if (!isset($_FILES[$fileKey]) || $_FILES[$fileKey]['error'] !== UPLOAD_ERR_OK) {
                return "";
            }

            $file = $_FILES[$fileKey];

            $allowedTypes = ['jpg', 'jpeg', 'png', 'pdf'];
            $extension    = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

            if (!in_array($extension, $allowedTypes)) {
                header("Location: borang_gantian.php?error=invalidformat");
                exit();
            } 

            $newName     = $prefix . "_" . time() . "_" . substr(md5(uniqid()), 0, 5) . "." . $extension;
            $destination = $folder . $newName;

            if (move_uploaded_file($file['tmp_name'], $destination)) {
                $uploadedFiles[] = $destination;
                return $newName;
            }

            return "";
        }

        $police_report   = uploadFile("police_report", $documentFolder, "police", $uploadedFiles);
        $ic_copy         = uploadFile("ic_copy", $documentFolder, "ic", $uploadedFiles);
        $payment_receipt = uploadFile("payment_receipt", $documentFolder, "payment", $uploadedFiles);
        $diploma_copy    = uploadFile("diploma_copy", $documentFolder, "diploma", $uploadedFiles);
        $exam_result     = uploadFile("exam_result", $documentFolder, "result", $uploadedFiles);
        $transcript_copy = uploadFile("transcript_copy", $documentFolder, "transcript", $uploadedFiles);

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

                $stmtCount = $pdo->query("SELECT COUNT(*) AS total FROM permohonan_gantian");
                $row = $stmtCount->fetch(PDO::FETCH_ASSOC);
                $number = ($row['total'] ?? 0) + 1;
                $application_no = "GSTA-" . str_pad($number, 3, "0", STR_PAD_LEFT);
            // Corrected SQL string (duplicates removed)
                $sql = "INSERT INTO permohonan_gantian (
                    application_no, application_type, student_name, ic_number, student_id,
                    address, phone, programme, study_year, graduation_session,
                    signature, application_date, police_report, ic_copy, payment_receipt,
                    diploma_copy, exam_result, transcript_copy, collection_method, postal_address, status
                ) VALUES (
                    ?, ?, ?, ?, ?,
                    ?, ?, ?, ?, ?,
                    ?, ?, ?, ?, ?,
                    ?, ?, ?, ?, ?, 'In Review'
                )";

            // Prepare and execute using PDO
            $stmt1 = $pdo->prepare($sql);
            $stmt1->execute([
                $application_no,
                $application_type,
                $student_name,
                $ic_number,
                $student_id,
                $address,
                $phone,
                $programme,
                $study_year,
                $graduation_session,
                $signature,
                $application_date,
                $police_report,
                $ic_copy,
                $payment_receipt,
                $diploma_copy,
                $exam_result,
                $transcript_copy,
                $collection_method,
                $postal_address
            ]);

            $main_app_type = "Gantian Sijil Diploma / Transkrip Akademik";
            $sql2 = "INSERT INTO applications (student_id, reference_id, application_type, status) VALUES (?, ?, ?, 'In Review')";
            $stmt2 = $pdo->prepare($sql2);
            $stmt2->execute([$user_primary_id, $application_no, $main_app_type]);

            $pdo->commit();

        } catch (PDOException $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }

            // Clean up uploaded files & signature if DB process failed
            foreach ($uploadedFiles as $filePath) {
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
            }

            error_log("Gantian Submission Error: " . $e->getMessage());
            header("Location: borang_gantian.php?error=systemerror");
            exit();
        }

        // Dynamic Admin Email Notification (Deployment-Ready)
        $email_cc = $_SESSION['email'] ?? '';
        $admin_id    = 1;
        $admin_email = getenv('ADMIN_NOTIFICATION_EMAIL') ?: 'upep@staff.psis.edu.my'; 
        $notif_title = "Permohonan Baru Diterima";
        $notif_msg   = "Pelajar {$student_name} ({$student_id}) telah menghantar permohonan Gantian Sijil Diploma / Transkrip Akademik.";

        if (function_exists('sendNotification')) {
            sendNotification($pdo, $admin_id, $admin_email, $notif_title, $notif_msg, 'admin', $email_cc);
        }

        header("Location: user_dashboard.php?status=success&type=gantian");
        exit();

    } else {
        header("Location: user_dashboard.php");
        exit();
    }
?>