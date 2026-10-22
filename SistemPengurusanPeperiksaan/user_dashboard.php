<?php
    require_once 'config.php';

    if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'student') {
        header("Location: index.php");
        exit();
    }

    $student_id   = $_SESSION['user_id'];
    $student_name = $_SESSION['full_name'] ?? 'Student';
    $role         = $_SESSION['role'];

    try {    
        // Fetch student applications for dashboard display
        $stmt = $pdo->prepare("SELECT * FROM applications WHERE student_id = ? ORDER BY id DESC");
        $stmt->execute([$student_id]);
        $applications = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Fetch announcement (ID = 1)
        $stmt_ann = $pdo->prepare("SELECT title, content, updated_at FROM announcements WHERE id = 1");
        $stmt_ann->execute();
        $announcement = $stmt_ann->fetch(PDO::FETCH_ASSOC);

        // Fetch unread notifications
        $stmt_notif = $pdo->prepare("SELECT * FROM notifications WHERE user_id = :student_id AND role = 'student' ORDER BY created_at DESC LIMIT 5");
        $stmt_notif->execute([':student_id' => $student_id]);
        $user_notifications = $stmt_notif->fetchAll(PDO::FETCH_ASSOC);

    } catch (PDOException $e) {
        error_log("Dashboard Error: " . $e->getMessage());
        // Fallback query to load records even if update fails
        $stmt = $pdo->prepare("SELECT * FROM applications WHERE student_id = ? ORDER BY id DESC");
        $stmt->execute([$student_id]);
        $applications = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $user_notifications = [];
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"> 
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SPP PSIS - Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include 'user_header.php' ?>
    <div class="container my-4">
        <!-- when user successfully submitted application -->
        <?php if (isset($_GET['status']) && $_GET['status'] === 'success'): ?>
        <?php
            $type = $_GET['type'] ?? '';
            
            switch ($type) {
                case 'transkrip':
                    $message = "Permohonan Transkrip Akademik Rasmi anda berjaya dihantar!";
                    break;
                case 'pengecualian':
                    $message = "Permohonan Pengecualian Bayaran Sijil/Transkrip anda berjaya dihantar!";
                    break;
                case 'gantian':
                    $message = "Permohonan Gantian Sijil/Transkrip Akademik anda berjaya dihantar!";
                    break;
                case 'ctce':
                    $message = "Permohonan Pindahan Kredit / Pengecualian Kuliah anda berjaya dihantar!";
                    break;
                default:
                    $message = "Permohonan anda berjaya dihantar!";
                    break;
            }
        ?>   
            <div class="alert alert-success alert-dismissible fade show text-center fw-bold mb-4" role="alert">
                <?= htmlspecialchars($message) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="p-4 mb-4 hero-section rounded shadow-sm">
            <h4 class="fw-bold mb-1">Selamat kembali, <?= htmlspecialchars($student_name) ?>!</h4>
        </div>

        <div class="alert alert-warning shadow-sm position-relative" role="alert">
            <div class="card-body">
                <h5 class="alert-heading fw-bold"><?= htmlspecialchars($announcement['title'] ?? 'Pengumuman Penting !') ?></h5>
                    <p class="mb-0"><?= nl2br(htmlspecialchars($announcement['content'] ?? 'Tiada pengumuman terkini.')) ?></p>
            </div>
        </div>

        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 fw-bold text-secondary">Status Permohonan Aktif</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3">No</th>
                                <th>Jenis Permohonan</th>
                                <th>Tarikh Hantar</th>
                                <th>Status</th>
                                <th>Catatan Admin</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($applications)): ?>
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">Tiada permohonan aktif ditemui.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($applications as $app): ?>
                                <?php 
                                    // Tentukan fail borang mengikut jenis permohonan
                                    $view_page = '#';
                                    if ($app['application_type'] == 'Pengecualian Bayaran Sijil / Transkrip') {
                                        $view_page = 'lihat_pengecualian.php';
                                    } elseif ($app['application_type'] == 'Transkrip Akademik Rasmi') {
                                        $view_page = 'lihat_transkrip.php';
                                    } elseif ($app['application_type'] == 'Gantian Sijil Diploma / Transkrip Akademik') {
                                        $view_page = 'lihat_gantian.php';
                                    } else {
                                        $view_page = 'lihat_ctce.php';
                                    }
                                ?>
                                <tr>
                                    <td class="ps-3"><?= htmlspecialchars($app['id']) ?></td>
                                    <td><?= htmlspecialchars($app['application_type']) ?></td>
                                    <td>
                                            <?php 
                                                $date_str = $app['date_submitted'] ?? null; 
                                                
                                                if (!empty($date_str) && $date_str !== '0000-00-00 00:00:00') {
                                                    echo date('d M Y', strtotime($date_str));
                                                } else {
                                                    echo '<span class="text-muted">-</span>'; // Jika tiada tarikh
                                                }
                                            ?>
                                    </td>
                                    <td>
                                        <?php 
                                            $st = (!empty($app['status'])) ? $app['status'] : 'In Review';
                                            $bdg = ($st == 'Completed') ? 'bg-success' : (($st == 'Rejected') ? 'bg-danger' : 'bg-warning text-dark');
                                        ?>
                                        <span class="badge <?= $bdg ?>"><?= htmlspecialchars($st) ?></span>
                                    </td>
                                    <td><?= htmlspecialchars($app['admin_remarks'] ?? '') ?></td></td>
                                    <td class="text-center">
                                        <a href="<?= $view_page ?>?id=<?= $app['id'] ?>&action=view" class="btn btn-sm btn-outline-primary px-3">
                                            Lihat Butiran
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

        <h5 class="fw-bold text-secondary mb-3">Permohonan Baru</h5>
        <div class="row g-3">
            <div class="col-md-3">
                <div class="card h-100 shadow-sm">
                    <div class="card-body">
                        <h6 class="card-title fw-bold">BORANG PERMOHONAN PENGECUALIAN BAYARAN BAGI SALINAN SIJIL DIPLOMA/TRANSKRIP AKADEMIK</h6>
                        <p class="card-text small text-muted">Permohonan pengecualian bayaran salinan sijil dan transkrip akademik khusus bagi lulusan yang terkesan akibat bencana alam.</p>
                        <a href="borang_pengecualian.php" class="btn btn-sm btn-primary w-100 mt-2">Mohon Disini</a>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card h-100 shadow-sm">
                    <div class="card-body">
                        <h6 class="card-title fw-bold">BORANG PERMOHONAN GANTIAN SIJIL DIPLOMA / TRANSKRIP AKADEMIK</h6>
                        <p class="card-text small text-muted">Permohonan penggantian salinan sijil diploma dan transkrip akademik bagi graduan yang mengalami kehilangan atau kerosakan dokumen asal.</p>
                        <a href="borang_gantian.php" class="btn btn-sm btn-primary w-100 mt-2">Mohon Disini</a>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card h-100 shadow-sm">
                    <div class="card-body">
                        <h6 class="card-title fw-bold">BORANG PERMOHONAN TRANSKRIP AKADEMIK RASMI</h6>
                        <p class="card-text small text-muted">Permohonan dan tuntutan Transkrip Akademik Rasmi bagi graduan yang telah berjaya menamatkan pengajian.</p>
                        <a href="borang_transkrip.php" class="btn btn-sm btn-primary w-100 mt-2">Mohon Disini</a>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card h-100 shadow-sm">
                    <div class="card-body">
                        <h6 class="card-title fw-bold">BORANG PERMOHONAN PINDAHAN KREDIT / PENGECUALIAN KURSUS</h6>
                        <p class="card-text small text-muted">Permohonan pemindahan kredit dan pengecualian kursus rasmi bagi pelajar yang memenuhi syarat-syarat kelayakan.</p>
                        <a href="borang_ctce.php" class="btn btn-sm btn-primary w-100 mt-2">Mohon Disini</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php include 'footer.php' ?>