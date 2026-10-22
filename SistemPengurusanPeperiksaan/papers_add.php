<?php
    require_once 'config.php';

    if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== "admin") {
        header("Location: admin_login.php");
        exit();
    }

    $staf_id   = $_SESSION['user_id'];
    $student_name = $_SESSION['full_name'];
    $role         = $_SESSION['role'];

    $stmt = $pdo->prepare("SELECT * FROM applications WHERE student_id = ?");
    $stmt->execute([$staf_id]);
    $applications = $stmt->fetchAll();

    if(isset($_POST['submit'])){
        $department = trim($_POST['department']);
        $course_code = trim($_POST['course_code']);
        $course_name = trim($_POST['course_name']);
        $session = $_POST['session'];
        $exam_type = $_POST['exam_type'];

        if(isset($_FILES['pdf_file']) && $_FILES['pdf_file']['error'] === UPLOAD_ERR_OK) {
            $pdf_original_name = $_FILES['pdf_file']['name'];
            $pdf_name = time().'_'.str_replace('', '_', $pdf_original_name);
            $pdf_tmp = $_FILES['pdf_file']['tmp_name'];
            $target_dir = "uploads/papers/";

            if(!file_exists($target_dir)) {
                mkdir($target_dir, 0777, true);
            }

            $target_file = $target_dir.$pdf_name;

            if(move_uploaded_file($pdf_tmp, $target_file)) {
                try {
                    $sql = "INSERT INTO past_papers (department, course_code, course_name, session, exam_type, pdf_file) VALUES (?, ?, ?, ?, ?, ?)";
                
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute([$department, $course_code, $course_name, $session, $exam_type, $pdf_name]);

                    header("Location: papers_manage.php?status=paper_added");
                    exit();
                } catch (PDOException $e) {
                    die("Database Error: ".$e->getMessage());
                }
            } else {
                echo "<script>alert('Failed to move uploaded file.');</script>";
            }
        } else {
            echo "<script>alert('Please select a valid PDF file.');</script>";
        }
    }

    $staf_id   = $_SESSION['user_id'];
    // Admin notifications
    try {
        $stmt_notif = $pdo->prepare("SELECT * FROM notifications WHERE user_id = :admin_id AND role = 'admin' ORDER BY created_at DESC LIMIT 5");
        $stmt_notif->execute([':admin_id' => $staf_id]);
        $admin_notifications = $stmt_notif->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $admin_notifications = [];
    }
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Upload Past Paper</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    </head>
    <body>
        <?php include 'admin_header.php' ?>
        <div class="container mt-5">
            <a href="papers_manage.php" class="btn btn-primary" name="back">← Back</a><br><br>
            <div class="card shadow-sm bg-white p-4">
                <h2 class="mb-4 align-self-center">Upload Past Paper</h2>
                <form method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label class="form-label">Course Code</label>
                        <input type="text" name="course_code" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Course Name</label>
                        <input type="text" name="course_name" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Department</label>
                        <select name="department" class="form-select">
                            <option value="">All Department</option>
                            <option value="JTMK">JTMK</option>
                            <option value="JP">JP</option>
                            <option value="JPH">JPH</option>
                            <option value="JKA">JKA</option>
                            <option value="JKE">JKE</option>
                            <option value="JMSK">JMSK</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Academic Session</label>
                        <select name="session" class="form-select">
                            <option value="">Select Session</option>
                            <option>Sesi II 2025/2026</option>
                            <option>Sesi I 2025/2026</option>
                            <option>Sesi II 2024/2025</option>
                            <option>Sesi I 2024/2025</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Exam Type</label>
                        <select name="exam_type" class="form-select">
                            <option>Final Examination</option>
                            <option>Mid Semester Examination</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Upload PDF</label>
                        <input type="file" name="pdf_file" class="form-control" accept=".pdf" required>
                    </div>

                    <button type="submit" class="btn btn-primary" name="submit">Upload Paper</button>
                </form>
            </div>
        </div>
    </body>
</html>
<br><?php include 'footer.php' ?>