<?php
    require_once 'config.php';

    // Semak hak akses Admin
    if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== "admin") {
        header("Location: admin_login.php");
        exit();
    }

    // Ambil semua permohonan pengecualian daripada database
    try {
        $stmt = $pdo->query("SELECT * FROM permohonan_pengecualian ORDER BY created_at DESC");
        $list_pengecualian = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $list_pengecualian = [];
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
    <title>Senarai Permohonan Pengecualian Bayaran</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .navbar-psis { 
            background-color: #420d66;
        }
    </style>
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
            <h3 class="fw-bold">Senarai Permohonan Pengecualian Bayaran</h3>
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
                                <th>Sebab Kerosakan/Kehilangan</th>
                                <th>Dokumen Sokongan</th>
                                <th class="text-center">Tindakan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($list_pengecualian)): ?>
                                <tr>
                                    <td colspan="8" class="text-center py-4 text-muted">Tiada permohonan pengecualian bayaran ditemui.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($list_pengecualian as $index => $row): ?>
                                    <tr>
                                        <td><?= $index + 1 ?></td>
                                        <td class="fw-bold"><?= htmlspecialchars($row['applicant_name']) ?></td>
                                        <td><?= htmlspecialchars($row['reg_no']) ?></td>
                                        <td><?= htmlspecialchars($row['ic_no']) ?></td>
                                        <td><?= htmlspecialchars($row['phone_no']) ?></td>
                                        <td><?= htmlspecialchars($row['cause_of_damage']) ?></td>
                                        <td>
                                            <?php if (!empty($row['support_document_path'])): ?>
                                                <a href="<?= htmlspecialchars($row['support_document_path']) ?>" target="_blank" class="btn btn-sm btn-outline-info">
                                                    Lihat Dokumen
                                                </a>
                                            <?php else: ?>
                                                <span class="text-muted">Tiada</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <!-- Butang untuk melihat perincian atau borang asal -->
                                            <a href="detail_pengecualian.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-primary">
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