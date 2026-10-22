<?php
    require_once 'config.php';

    if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== "admin") {
        header("Location: index.php");
        exit();
    }

    if(!isset($_GET['id']) || empty($_GET['id'])) {
        header("Location: papers_manage.php");
        exit();
    }

    $paper_id = $_GET['id'];

    try {
        $stmt = $pdo->prepare("SELECT * FROM past_papers WHERE paper_id = ?");
        $stmt->execute([$paper_id]);
        $paper = $stmt->fetch(PDO::FETCH_ASSOC);

        if(!$paper) {
            echo "<script>alert('Paper not found!'); window.location='papers_manage.php'; </script>";
            exit();
        }
    } catch (PDOException $e) {
         die("Database Error: ".$e->getMessage());
    }

    if(isset($_POST['update'])) {
        $department = trim($_POST['department']);
        $course_code = trim($_POST['course_code']);
        $course_name = trim($_POST['course_name']);
        $session = $_POST['session'];
        $exam_type = $_POST['exam_type'];
        $pdf_name = $paper['pdf_file'];

        if(isset($_FILES['pdf_file']) && $_FILES['pdf_file']['error'] === UPLOAD_ERR_OK) {
            $new_pdf_original = $_FILES['pdf_file']['name'];
            $pdf_tmp = $_FILES['pdf_file']['tmp_name'];

            $new_pdf_name = time().'_'.str_replace(' ', '_', $new_pdf_original);
            $target_dir = "uploads/papers/";
            $target_file = $target_dir.$new_pdf_name;

            if(!file_exists($target_dir)) {
                mkdir($target_dir, 0777, true);
            }

            if(move_uploaded_file($pdf_tmp, $target_file)) {
                $old_file_path = $target_dir.$paper['pdf_file'];
                if(!empty($paper['pdf_file']) && file_exists($old_file_path)) {
                    unlink($old_file_path);
                }

                $pdf_name = $new_pdf_name;
            } else {
                echo "<script>alert('Failed to upload new PDF file.'); </script>";
            }
        }

        try {
            $sql = "UPDATE past_papers SET department = ?, course_code = ?, course_name = ?, session = ?, exam_type = ?, pdf_file = ? WHERE paper_id = ?";
            $update_stmt = $pdo->prepare($sql);
            $update_stmt->execute([$department, $course_code, $course_name, $session, $exam_type, $pdf_name, $paper_id]);

            echo "<script>alert('Past paper updated successfully!'); window.location='papers_manage.php';</script>";
            exit();
        } catch (PDOException $e) {
            die("Database Error: ".$e->getMessage());
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
        <title>Edit Past Paper</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    </head>
    <body>
        <?php include 'admin_header.php' ?>

        <div class="container mt-5 mb-5">
        <a href="papers_manage.php" class="btn btn-primary mb-3">← Back</a>
        <div class="card shadow-sm bg-white p-4">
            <h2 class="mb-4 align-self-center">Edit Past Paper</h2>
            <form method="POST" enctype="multipart/form-data">
                <div class="mb-3">
                    <label class="form-label">Course Code</label>
                    <input type="text" name="course_code" class="form-control" value="<?php echo htmlspecialchars($paper['course_code']); ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Course Name</label>
                    <input type="text" name="course_name" class="form-control" value="<?php echo htmlspecialchars($paper['course_name']); ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Department</label>
                    <select name="department" class="form-select">
                        <?php 
                            $department = ["JTMK", "JP", "JPH", "JKA", "JKE", "JMSK"];
                            foreach ($department as $dept): 
                                $selected = ($paper['department'] === $dept) ? 'selected' : '';
                        ?>
                        <option value="<?php echo $dept; ?>" <?php echo $selected; ?>><?php echo $dept; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Academic Session</label>
                    <select name="session" class="form-select">
                        <?php 
                        $sessions = ["Sesi II 2025/2026", "Sesi I 2025/2026", "Sesi II 2024/2025", "Sesi I 2024/2025"];
                        foreach ($sessions as $sess): 
                            $selected = ($paper['session'] === $sess) ? 'selected' : '';
                        ?>
                            <option value="<?php echo $sess; ?>" <?php echo $selected; ?>><?php echo $sess; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Exam Type</label>
                    <select name="exam_type" class="form-select">
                        <option value="Final Examination" <?php echo ($paper['exam_type'] === 'Final Examination') ? 'selected' : ''; ?>>Final Examination</option>
                        <option value="Mid Semester Examination" <?php echo ($paper['exam_type'] === 'Mid Semester Examination') ? 'selected' : ''; ?>>Mid Semester Examination</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Current PDF File</label>
                    <p class="form-text text-muted mb-1">
                        <i class="bi bi-file-earmark-pdf text-danger"></i> 
                        <?php echo htmlspecialchars($paper['pdf_file']); ?>
                    </p>
                    <label class="form-label mt-2">Replace PDF (Optional)</label>
                    <input type="file" name="pdf_file" class="form-control" accept=".pdf">
                </div>

                <button type="submit" class="btn btn-warning" name="update">
                    <i class="bi bi-check-circle"></i> Save Changes
                </button>
            </form>
        </div>
    </div>
</body>
</html>
<?php include 'footer.php' ?>