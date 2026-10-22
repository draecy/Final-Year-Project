<?php
    require_once 'config.php';

    // Verify admin authorization
    if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== "admin") {
        header("Location: admin_dashboard.php");
        exit();
    }

    // Validate ID from GET query parameter
    $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

    if (!$id) {
        header("Location: admin_dashboard.php?status=error&msg=" . urlencode("ID Permohonan tidak sah."));
        exit();
    }

    $message = '';
    $message_type = '';

    // Fetch existing CTCE application data
    try {
        $stmt = $pdo->prepare("SELECT * FROM permohonan_ctce WHERE id = ?");
        $stmt->execute([$id]);
        $application = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$application) {
            header("Location: admin_dashboard.php?status=error&msg=" . urlencode("Permohonan Pindah Kredit dan Pengecualian Kursus tidak dijumpai."));
            exit();
        }
    } catch (PDOException $e) {
        header("Location: admin_dashboard.php?status=error&msg=" . urlencode("Permohonan Pindah Kredit dan Pengecualian Kursus tidak dijumpai."));
        exit();
    }

    // Handle Form Submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
        $new_status   = trim($_POST['status'] ?? '');
        $admin_remarks = trim($_POST['admin_remarks'] ?? '');

        if (!empty($new_status)) {
            try {
                $pdo->beginTransaction();
                
                // 1. Update ctce_applications table
                $stmt_update = $pdo->prepare("UPDATE permohonan_ctce SET status = :status, admin_remarks = :admin_remarks WHERE id = :id");
                $stmt_update->execute([
                    ':status'        => $new_status,
                    ':admin_remarks' => $admin_remarks,
                    ':id'            => $id
                ]);

                // 2. Update main applications table if reference/application number exists
                $app_no = trim($application['application_no'] ?? $application['reference_id'] ?? '');
                if (!empty($app_no)) {
                    $stmt_update_main = $pdo->prepare("UPDATE applications SET status = :status, admin_remarks = :admin_remarks WHERE reference_id = :app_no");
                    $stmt_update_main->execute([
                        ':status'        => $new_status,
                        ':admin_remarks' => $admin_remarks,
                        ':app_no'        => $app_no
                    ]); 
                }

                $pdo->commit();

                // 3. Find recipient user account for notification
                $target_user_id = null;
                $student_email  = '';

                if (!empty($app_no)) {
                    $stmt_find = $pdo->prepare("SELECT a.student_id, u.email FROM applications a LEFT JOIN users u ON a.student_id = u.id WHERE a.reference_id = ? LIMIT 1");
                    $stmt_find->execute([$app_no]);
                    $find_data = $stmt_find->fetch(PDO::FETCH_ASSOC);

                    if ($find_data) {
                        $target_user_id = $find_data['student_id'];
                        $student_email  = $find_data['email'];
                    }
                }
                
                if (empty($student_email)) {
                    $student_email = $application['email'] ?? ($application['student_email'] ?? '');
                }
                 
                // 4. Send Notification
                $app_type_name = "Pindahan Kredit / Pengecualian Kursus";
                if (!empty($admin_remarks)) {
                    $notif_message = "Permohonan anda {$app_type_name} telah dikemaskini kepada: {$new_status}.\nCatatan Admin: {$admin_remarks}";
                } else {
                    $notif_message = "Permohonan anda {$app_type_name} telah dikemaskini kepada: {$new_status}.";
                }
         
                if (function_exists('sendNotification')) {
                    $custom_cc = $_SESSION['session_email'];
                    sendNotification(
                        $pdo, 
                        $target_user_id, 
                        $student_email, 
                        "Status Permohonan Dikemaskini", 
                        $notif_message, 
                        'student',
                        $custom_cc
                    );
                }

                // Redirect back to admin dashboard
                $display_id = !empty($app_no) ? $app_no : $id;
                header("Location: admin_dashboard.php?status=success&msg=" . urlencode("Status permohonan CTCE #" . $display_id . " berjaya dikemaskini!"));
                exit();

            } catch (PDOException $e) {
                if ($pdo->inTransaction()) {
                    $pdo->rollBack();
                }
                error_log("Update Status Error in view_ctce.php: " . $e->getMessage());
                $message = "Ralat sistem: " . $e->getMessage();
                $message_type = "danger";
            }
        } else {
            $message = "Gagal mengemaskini permohonan.";
            $message_type = "warning";
        }
    }
?>
<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Permohonan Pindahan Kredit / Pengecualian Kursus #<?= htmlspecialchars($application['application_no'] ?? $application['id']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <?php include 'admin_header.php' ?>
    <div class="container my-5">

        <div class="d-flex justify-content-between align-items-center mb-3">
            <a href="admin_dashboard.php" class="btn btn-primary px-4">Back</a>
        </div>

        <?php if (!empty($message)): ?>
            <div class="alert alert-<?= $message_type ?> alert-dismissible fade show shadow-sm" role="alert">
                <?= htmlspecialchars($message) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <!-- Borang Kemaskini Status Admin -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-dark text-white">
                <h5 class="mb-0 fw-bold">Kemaskini Status Permohonan Pindahan Kredit</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">Status Permohonan</label>
                            <select name="status" class="form-select" required>
                                <option value="In Review" <?= ($application['status'] ?? '') === 'In Review' ? 'selected' : '' ?>>In Review</option>
                                <option value="Completed" <?= ($application['status'] ?? '') === 'Completed' ? 'selected' : '' ?>>Completed</option>
                                <option value="Rejected" <?= ($application['status'] ?? '') === 'Rejected' ? 'selected' : '' ?>>Rejected</option>
                            </select>
                        </div>
                        <div class="col-md-8 mb-3">
                            <label class="form-label fw-bold">Catatan Admin</label>
                            <input type="text" name="admin_remarks" class="form-control" value="<?= htmlspecialchars($application['admin_remarks'] ?? '') ?>" placeholder="Masukkan alasan atau catatan...">
                        </div>
                    </div>
                    <button type="submit" name="update_status" class="btn btn-success px-4 fw-semibold">Kemaskini</button>
                </form>
            </div>
        </div>

        <!-- Maklumat Permohonan -->
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold">Maklumat Permohonan #APP-<?= htmlspecialchars($application['id']) ?></h5>
                <span class="badge bg-light text-dark fw-bold"><?= htmlspecialchars($application['status'] ?? 'In Review') ?></span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped mb-0">
                        <tbody>
                            <?php foreach ($application as $key => $value): ?>
                                <tr>
                                    <th class="w-25 text-capitalize bg-light"><?= htmlspecialchars(str_replace('_', ' ', $key)) ?></th>
                                    <td><?= htmlspecialchars($value ?? '-') ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php include 'footer.php' ?>