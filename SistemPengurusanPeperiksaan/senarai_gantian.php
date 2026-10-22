<?php
    require_once 'config.php';

    // Semak hak akses Admin
    if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== "admin") {
        header("Location: admin_login.php");
        exit();
    }

    // Ambil semua permohonan gantian sijil/transkrip daripada database
    try {
        $stmt = $pdo->query("SELECT * FROM permohonan_gantian ORDER BY application_date DESC");
        $list_gantian = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $list_gantian = [];
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
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <title>Senarai Permohonan Gantian Sijil / Transkrip</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <?php include 'admin_header.php' ?>
    <div class="container my-5">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <a href="admin_dashboard.php" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Kembali ke Dashboard
            </a>
        </div>
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="fw-bold">Senarai Permohonan Gantian Sijil / Transkrip Akademik</h3>
        </div>
        <div class="card shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th>#</th>
                                <th>Nama Pemohon</th>
                                <th>No. Pendaftaran</th>
                                <th>No. K/P</th>
                                <th>No. Telefon</th>
                                <th>Program</th>
                                <th>Tarikh Permohonan</th>
                                <th class="text-center">Tindakan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($list_gantian)): ?>
                                <tr>
                                    <td colspan="8" class="text-center py-4 text-muted">Tiada permohonan gantian sijil/transkrip ditemui.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($list_gantian as $index => $row): ?>
                                    <tr>
                                        <td><?= $index + 1 ?></td>
                                        <td class="fw-bold"><?= htmlspecialchars($row['student_name'] ?? '') ?></td>
                                        <td><?= htmlspecialchars($row['student_id'] ?? '') ?></td>
                                        <td><?= htmlspecialchars($row['ic_number'] ?? '') ?></td>
                                        <td><?= htmlspecialchars($row['phone'] ?? '') ?></td>
                                        <td><?= htmlspecialchars($row['programme'] ?? '') ?></td>
                                        <td><?= htmlspecialchars($row['application_date'] ?? '') ?></td>
                                        <td class="text-center">
                                            <!-- Butang untuk melihat perincian borang asal -->
                                            <a href="detail_gantian.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-primary">
                                                Papar Detail
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
<?php include 'footer.php' ?>