<?php
    require_once 'config.php';

    if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== "admin") {
        header("Location: admin_dashboard.php");
        exit();
    }

    $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

    if (!$id) {
        header("Location: admin_dashboard.php?status=error&msg=" . urlencode("ID Permohonan tidak sah."));
        exit();
    }

    $message = '';
    $message_type = '';

    // Fetch Data Permohonan Pengecualian dahulu
    try {
        $stmt = $pdo->prepare("SELECT * FROM permohonan_gantian WHERE id = ?");
        $stmt->execute([$id]);
        $application = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$application) {
            header("Location: admin_dashboard.php?status=error&msg=" . urlencode("Permohonan Gantian Sijil tidak dijumpai."));
            exit();
        }
    } catch (PDOException $e) {
        error_log("Database Error in view_gantian.php: " . $e->getMessage());
        die("Ralat Pangkalan Data: " . $e->getMessage());
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
        $new_status   = trim($_POST['status'] ?? '');
        $admin_remarks = trim($_POST['admin_remarks'] ?? '');

        if (!empty($new_status)) {
            try {
                $pdo->beginTransaction();
                
                $stmt_update = $pdo->prepare("UPDATE permohonan_gantian SET status = :status, admin_remarks = :admin_remarks WHERE id = :id");
                $stmt_update->execute([
                    ':status'        => $new_status,
                    ':admin_remarks' => $admin_remarks,
                    ':id'            => $id
                ]);

                $app_no = trim($application['application_no'] ?? $application['reference_id'] ?? '');
                $stmt_update_main = $pdo->prepare("UPDATE applications SET status = :status, admin_remarks = :admin_remarks WHERE reference_id = :app_no");
                $stmt_update_main->execute([
                    ':status'        => $new_status,
                    ':admin_remarks' => $admin_remarks,
                    ':app_no'        => $app_no
                ]);

                $pdo->commit();

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

                // Fallback target_user_id daripada email jika masih null
                if (!$target_user_id && !empty($student_email)) {
                    $stmt_user = $pdo->prepare("SELECT id FROM users WHERE LOWER(email) = LOWER(?) LIMIT 1");
                    $stmt_user->execute([$student_email]);
                    $user_data = $stmt_user->fetch(PDO::FETCH_ASSOC);
                    if ($user_data) {
                        $target_user_id = $user_data['id'];
                    }
                } 
                 
                $app_type_name = "Gantian Sijil Diploma / Transkrip Akademik";

                if (!empty($admin_remarks)) {
                    $notif_message = "Permohonan anda bagi {$app_type_name} {$app_no} telah dikemaskini kepada: {$new_status}.\nCatatan Admin: {$admin_remarks}";
                } else {
                    $notif_message = "Permohonan anda bagi {$app_type_name} {$app_no} telah dikemaskini kepada: {$new_status}.";
                }
                    
                // Notification Message
                if (!empty($student_email) && function_exists('sendNotification')) {
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

                header("Location: admin_dashboard.php?status=success&msg=" . urlencode("Status permohonan Gantian #" . ($application['application_no'] ?? $id) . " berjaya dikemaskini!"));
                exit();
            } catch (PDOException $e) {
                if ($pdo->inTransaction()) {
                    $pdo->rollBack();
                }
                error_log("Update Status Error in view_gantian.php: " . $e->getMessage());
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
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Permohonan Gantian #<?= htmlspecialchars($replacement['application_id']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container my-5">
        <div class="d-flex justify-content-between align-items-center mb-3 no-print">
            <a href="admin_dashboard.php" type="submit" class="btn btn-primary" name="back">Back</a>
        </div>

         <!-- Borang Kemaskini Status Admin -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-dark text-white">
                <h5 class="mb-0">Kemaskini Status Permohonan Gantian</h5>
            </div>
            <div class="card-body">
                <form method="POST">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">Status Permohonan</label>
                            <select name="status" class="form-select" required>
                                <option value="In Review" <?= ($application['status'] ?? '') == 'In Review' ? 'selected' : '' ?>>In Review</option>
                                <option value="Completed" <?= ($application['status'] ?? '') == 'Completed' ? 'selected' : '' ?>>Completed</option>
                                <option value="Rejected" <?= ($application['status'] ?? '') == 'Rejected' ? 'selected' : '' ?>>Rejected</option>
                            </select>
                        </div>
                        <div class="col-md-8 mb-3">
                            <label class="form-label fw-bold">Catatan Admin</label>
                            <input type="text" name="admin_remarks" class="form-control" value="<?= htmlspecialchars($application['admin_remarks'] ?? '') ?>" placeholder="Masukkan alasan atau catatan...">
                        </div>
                    </div>
                    <button type="submit" name="update_status" class="btn btn-success px-4">Kemaskini</button>
                </form>
            </div>
        </div>
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Maklumat Permohonan #APP-<?= htmlspecialchars($application['id']) ?></h5>
                <span class="badge bg-light text-dark"><?= htmlspecialchars($application['status'] ?? 'Pending') ?></span>
            </div>
            <div class="card-body">
                <table class="table table-bordered">
                    <tbody>
                        <?php foreach ($application as $key => $value): ?>
                            <tr>
                                <th class="w-30 text-capitalize"><?= htmlspecialchars(str_replace('_', ' ', $key)) ?></th>
                                <td><?= htmlspecialchars($value ?? '-') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
<?php include 'footer.php' ?>