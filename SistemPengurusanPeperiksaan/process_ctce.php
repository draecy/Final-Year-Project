<?php
    require_once 'config.php';

    // Semak sesi pengguna (Pastikan pengguna telah log masuk)
    if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'student') {
        header("Location: index.php");
        exit();
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        // Ambil ID utama pengguna dari session (sokong pelbagai nama session)
        $user_primary_id = $_SESSION['user_id'] ?? $_SESSION['id'] ?? null;

        // 1. STUDENT INFORMATION
        $student_name = $_POST['student_name'] ?? '';
        $ic_number = $_POST['ic_number'] ?? '';
        $student_id = $_POST['student_id'] ?? '';
        $programme = $_POST['programme'] ?? '';
        $class = $_POST['class'] ?? '';
        $phone = $_POST['phone'] ?? '';

        $declaration_1 = isset($_POST['declaration_1']) ? 1 : 0;
        $signature = $_POST['signature'] ?? '';
        $declaration_date = $_POST['declaration_date'] ?? '';

        // 2. GENERATE APPLICATION NUMBER
        $stmtCount = $pdo->query("SELECT COUNT(*) AS total FROM permohonan_ctce");
        $row = $stmtCount->fetch(PDO::FETCH_ASSOC);
        $number = ($row['total'] ?? 0) + 1;
        $application_no = "CTCE-" . str_pad($number, 3, "0", STR_PAD_LEFT);

        // 3. UPLOAD FOLDER
        $upload_dir = "uploads/ctce/";
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $uploadedFiles = [];

        // 4. UPLOAD DOCUMENT FUNCTION
        function uploadDocument($file, $upload_dir, $application_no, $field_name, &$uploadedFiles) {
            if (!isset($file) || $file['error'] === UPLOAD_ERR_NO_FILE) {
                return null;
            }

            if ($file['error'] !== UPLOAD_ERR_OK) {
                return null;
            }

            $maxSize = 5 * 1024 * 1024;
            if ($file['size'] > $maxSize) {
                header("Location: borang_ctce.php?error=exceedsize");
                exit();
            }

            $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $allowed_extensions = ['pdf', 'jpg', 'jpeg', 'png'];

            if (!in_array($extension, $allowed_extensions)) {
                header("Location: borang_ctce.php?error=invalidformat");
                exit();
            }

            $filename = $application_no . "_" . $field_name . "_" . time() . "." . $extension;
            $destination = $upload_dir . $filename;

            if (move_uploaded_file($file['tmp_name'], $destination)) {
                $uploadedFiles[] = $destination;
                return $destination;
            }
            return null;
        }

        // 5. UPLOAD ALL DOCUMENTS
        $document_transcript  = uploadDocument($_FILES['document_transcript'] ?? null, $upload_dir, $application_no, 'transcript', $uploadedFiles);
        $document_certificate = uploadDocument($_FILES['document_certificate'] ?? null, $upload_dir, $application_no, 'certificate', $uploadedFiles);
        $document_officer     = uploadDocument($_FILES['document_officer'] ?? null, $upload_dir, $application_no, 'officer', $uploadedFiles);
        $document_other       = uploadDocument($_FILES['document_other'] ?? null, $upload_dir, $application_no, 'other', $uploadedFiles);

        // 6. CT/CE COURSES
        $ctce_courses = [];

        if (isset($_POST['ctce_course_code']) && is_array($_POST['ctce_course_code'])) {

            for ($i = 0; $i < count($_POST['ctce_course_code']); $i++) {

                $course_code = $_POST['ctce_course_code'][$i] ?? '';
                $course_name = $_POST['ctce_course_name'][$i] ?? '';
                $credit_hour = $_POST['ctce_credit_hour'][$i] ?? '';

                // Skip empty rows
                if (empty($course_code) && empty($course_name) && empty($credit_hour)) {
                    continue;
                }

                $ctce_courses[] = [
                    'course_code' => $course_code,
                    'course_name' => $course_name,
                    'credit_hour' => $credit_hour
                ];
            }
        }

        $ctce_courses_json = json_encode($ctce_courses, JSON_UNESCAPED_UNICODE);

        // 7. EQUIVALENT COURSES
        $equivalent_courses = [];
        if (isset($_POST['equivalent_course_code']) && is_array($_POST['equivalent_course_code'])) {

            for ($i = 0; $i < count($_POST['equivalent_course_code']); $i++) {

                $course_code = $_POST['equivalent_course_code'][$i] ?? '';
                $course_name = $_POST['equivalent_course_name'][$i] ?? '';
                $credit_hour = $_POST['equivalent_credit_hour'][$i] ?? '';
                $grade = $_POST['equivalent_grade'][$i] ?? '';

                // Skip empty rows
                if (empty($course_code) && empty($course_name) && empty($credit_hour) && empty($grade)) {
                    continue;
                }

                $equivalent_courses[] = [
                    'course_code' => $course_code,
                    'course_name' => $course_name,
                    'credit_hour' => $credit_hour,
                    'grade'       => $grade
                ];
            }
        }

        $equivalent_courses_json = json_encode($equivalent_courses, JSON_UNESCAPED_UNICODE);

        // 8. INSERT INTO DATABASE
        try {
            $pdo->beginTransaction();
            $sql1 = "INSERT INTO permohonan_ctce (
                    application_no,
                    student_name,
                    ic_number,
                    student_id,
                    programme,
                    class,
                    phone,
                    document_transcript,
                    document_certificate,
                    document_officer,
                    document_other,
                    ctce_courses,
                    equivalent_courses,
                    declaration_1,
                    signature,
                    declaration_date,
                    status
                ) VALUES (
                ?, ?, ?, ?, ?, ?, ?,
                ?, ?, ?, ?, ?, ?,
                ?, ?, ?, 'In Review'
            )";

            $stmt1 = $pdo->prepare($sql1);

            // BIND PARAMETERS
            $stmt1->execute([
                $application_no,
                $student_name,
                $ic_number,
                $student_id,
                $programme,
                $class,
                $phone,
                $document_transcript,
                $document_certificate,
                $document_officer,
                $document_other,
                $ctce_courses_json,
                $equivalent_courses_json,
                $declaration_1,
                $signature,
                $declaration_date
            ]);

            // Insert ke jadual main applications menggunakan Foreign Key ID yang sah
            $main_app_type = "Pindahan Kredit / Pengecualian Kursus";
            $sql2 = "INSERT INTO applications (student_id, reference_id, application_type, status) VALUES (?, ?, ?,'In Review')";
            $stmt2 = $pdo->prepare($sql2);
            $stmt2->execute([$user_primary_id, $application_no, $main_app_type]);

            $pdo->commit();

        } catch (PDOException $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }

            foreach ($uploadedFiles as $filePath) {
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
            }

            error_log("CTCE Submission Error: " . $e->getMessage());
            header("Location: borang_ctce.php?error=systemerror");
            exit();
        }

            $email_cc = $_SESSION['email'] ?? '';
            $admin_id    = 1;
            $admin_email = getenv('ADMIN_NOTIFICATION_EMAIL') ?: 'upep@staff.psis.edu.my'; 
            $notif_title = "Permohonan Baru Diterima";
            $notif_msg   = "Pelajar {$student_name} {$student_id} telah menghantar permohonan Pindahan Kredit / Pengecualian Kursus.";

            if (function_exists('sendNotification')) {
                sendNotification($pdo, $admin_id, $admin_email, $notif_title, $notif_msg, 'admin', $email_cc);
            }

        header("Location: user_dashboard.php?status=success&type=ctce");
        exit();
    }  else {
        header("Location: user_dashboard.php");
        exit();
    }
?>