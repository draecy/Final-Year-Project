<?php
    require_once 'config.php';

    if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== "admin") {
        header("Location: admin_dashboard.php");
        exit();
    }

    // Gunakan 'id' mengikut jadual permohonan_pengecualian
    $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

    if (!$id) {
        header("Location: admin_dashboard.php?status=error&msg=" . urlencode("ID Permohonan tidak sah."));
        exit();
    }

    $message = '';
    $message_type = '';

    // Fetch Data Permohonan Pengecualian dahulu
    try {
        $stmt = $pdo->prepare("SELECT * FROM permohonan_pengecualian WHERE id = ?");
        $stmt->execute([$id]);
        $application = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$application) {
            die("Permohonan Pengecualian Bayaran Tidak Dijumpai.");
        }
    } catch (PDOException $e) {
        error_log("Database Error in view_pengecualian.php: " . $e->getMessage());
        die("Ralat Pangkalan Data: " . $e->getMessage());
    }

    // Proses Kemaskini Status & Catatan Admin
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
        $new_status   = trim($_POST['status'] ?? '');
        $admin_remarks = trim($_POST['admin_remarks'] ?? '');

        if (!empty($new_status)) {
            try {
                $pdo->beginTransaction();

                $stmt_update = $pdo->prepare("UPDATE permohonan_pengecualian SET status = :status, admin_remarks = :admin_remarks WHERE id = :id");
                $stmt_update->execute([
                    ':status'        => $new_status,
                    ':admin_remarks' => $admin_remarks,
                    ':id'            => $id
                ]);

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

                $target_user_id = $application['target_user_id'] ?? null;
                $student_email  = $application['student_email'] ?? ($application['email'] ?? '');

                if (!empty($student_email)) {
                    $stmt_user = $pdo->prepare("SELECT id FROM users WHERE LOWER(email) = LOWER(?) LIMIT 1");
                    $stmt_user->execute([$student_email]);
                    $user_data = $stmt_user->fetch(PDO::FETCH_ASSOC);
                    
                    if ($user_data) {
                        $target_user_id = $user_data['id'];
                    }
                } 

                $app_type_name  = "Pengecualian Bayaran Sijil / Transkrip";

                if (!empty($admin_remarks)) {
                    $notif_message = "Permohonan anda bagi {$app_type_name} {$app_no} telah dikemaskini kepada: {$new_status}.\nCatatan Admin: {$admin_remarks}";
                } else {
                    $notif_message = "Permohonan anda bagi {$app_type_name} {$app_no} telah dikemaskini kepada: {$new_status}.";
                }

                if ($target_user_id && function_exists('sendNotification')) {
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

                header("Location: admin_dashboard.php?status=success&msg=" . urlencode("Status permohonan Pengecualian #" . ($application['application_no'] ?? $id) . " berjaya dikemaskini!"));
                exit();
            } catch (PDOException $e) {
                if ($pdo->inTransaction()) {
                    $pdo->rollBack();
                }
                error_log("Update Status Error in view_pengecualian.php: " . $e->getMessage());
                $message = "Ralat sistem: " . $e->getMessage();
                $message_type = "danger";
            }
        } else {
            $message = "Gagal mengemaskini permohonan.";
            $message_type = "danger";
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Permohonan Pengecualian #PEN-<?= htmlspecialchars($application['id']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <?php include 'admin_header.php' ?>
    <div class="container my-5">
        <div class="d-flex justify-content-between align-items-center mb-3 no-print">
            <a href="admin_dashboard.php" type="submit" class="btn btn-primary" name="back">Back</a>
        </div>
        <!-- Borang Kemaskini Status Admin -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-dark text-white">
                <h5 class="mb-0">Kemaskini Status Permohonan Pengecualian</h5>
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

        <!-- Paparan Maklumat Permohonan -->
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Maklumat Permohonan #PEN-<?= htmlspecialchars($application['id']) ?></h5>
                <span class="badge bg-light text-dark"><?= htmlspecialchars($application['status'] ?? 'Pending') ?></span>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped mb-0">
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
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php include 'footer.php' ?>