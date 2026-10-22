<?php
    require_once 'config.php';

    // Semak hak akses Admin
    if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== "admin") {
        header("Location: admin_login.php");
        exit();
    }

    // Ambil semua permohonan transkrip menggunakan PDO
    try {
        $stmt = $pdo->query("SELECT * FROM permohonan_ctce ORDER BY declaration_date DESC");
        $list_ctce = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $list_ctce = [];
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
    <title>Senarai Permohonan Transfer Kredit / Pengecualian Kursus</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
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
            <h3 class="fw-bold">Senarai Permohonan Transfer Kredit / Pengecualian Kursus</h3>
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
                                <th>Cut Course</th>
                                <th>Equivalent Course</th>
                                <th>Program</th>
                                <th>Tarikh Mohon</th>
                                <th class="text-center">Tindakan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($list_ctce)): ?>
                                <tr>
                                    <td colspan="8" class="text-center py-4 text-muted">Tiada permohonan transfer kredit / pengecualian kursus ditemui.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($list_ctce as $index => $row): ?>
                                    <?php 
                                        // Decode JSON String kepada Array PHP
                                        $cut_courses = json_decode($row['ctce_courses'] ?? '[]', true);
                                        $eq_courses  = json_decode($row['equivalent_courses'] ?? '[]', true);
                                    ?>
                                    <tr>
                                        <td><?= $index + 1 ?></td>
                                        <td class="fw-bold"><?= htmlspecialchars($row['student_name']) ?></td>
                                        <td><?= htmlspecialchars($row['student_id'] ?? '-') ?></td>
                                        <td><?= htmlspecialchars($row['ic_number']) ?></td>
                                        <!-- Paparan Cut Course secara kemas -->
                                        <td>
                                            <?php if (is_array($cut_courses) && !empty($cut_courses)): ?>
                                                <ul class="list-unstyled mb-0">
                                                    <?php foreach ($cut_courses as $c): ?>
                                                        <li class="mb-1">
                                                            <span class="badge bg-secondary"><?= htmlspecialchars($c['course_code'] ?? '') ?></span>
                                                            <small><?= htmlspecialchars($c['course_name'] ?? '') ?></small> 
                                                            <span class="text-muted small">(<?= htmlspecialchars($c['credit_hour'] ?? '0') ?> Jam)</span>
                                                        </li>
                                                    <?php endforeach; ?>
                                                </ul>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>

                                        <!-- Paparan Equivalent Course secara kemas -->
                                        <td>
                                            <?php if (is_array($eq_courses) && !empty($eq_courses)): ?>
                                                <ul class="list-unstyled mb-0">
                                                    <?php foreach ($eq_courses as $e): ?>
                                                        <li class="mb-1">
                                                            <span class="badge bg-info text-dark"><?= htmlspecialchars($e['course_code'] ?? '') ?></span>
                                                            <small><?= htmlspecialchars($e['course_name'] ?? '') ?></small> 
                                                            <span class="text-muted small">(<?= htmlspecialchars($e['credit_hour'] ?? '0') ?> Jam)</span>
                                                        </li>
                                                    <?php endforeach; ?>
                                                </ul>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= htmlspecialchars($row['programme'] ?? '-') ?></td>
                                        <td><?= htmlspecialchars($row['declaration_date']) ?></td>
                                        <td class="text-center">
                                            <a href="detail_ctce.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-primary">
                                                <i class="bi bi-eye"></i> Papar Detail
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