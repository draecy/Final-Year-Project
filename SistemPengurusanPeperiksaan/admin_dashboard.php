<?php
    require_once 'config.php';

    // Verify admin authorization
    if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== "admin") {
        header("Location: admin_login.php");
        exit();
    }

    $staf_id   = $_SESSION['user_id'];
    $staf_name = $_SESSION['full_name'] ?? 'Admin';
    $role      = $_SESSION['role'];

    $message = '';
    $message_type = '';

    // Handle Announcement Update
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_announcement'])) {
        $ann_title   = trim($_POST['ann_title'] ?? '');
        $ann_content = trim($_POST['ann_content'] ?? '');

        if (!empty($ann_title) && !empty($ann_content)) {
            $stmt_ann = $pdo->prepare("INSERT INTO announcements (id, title, content) VALUES (1, :title, :content) ON DUPLICATE KEY UPDATE title = VALUES(title), content = VALUES(content)");
            $stmt_ann->execute([
                ':title'   => $ann_title,
                ':content' => $ann_content
            ]);
            $message = "Pengumuman berjaya dikemaskini!";
            $message_type = "success";
        } else {
            $message = "Gagal mengemaskini pengumuman. Sila isi semua maklumat.";
            $message_type = "danger";
        }
    }

    // Fetch Announcement
    try {
        $ann_query = $pdo->query("SELECT * FROM announcements WHERE id = 1");
        $announcement = $ann_query->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $announcement = false;
    }

    // Fetch Permohonan Pengecualian Bayaran
    try {
        $stmt_pengecualian = $pdo->query("SELECT * FROM permohonan_pengecualian ORDER BY id DESC");
        $pengecualian_apps = $stmt_pengecualian->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $pengecualian_apps = [];
    }

    // Fetch Permohonan Gantian Sijil / Transkrip
    try {
        $stmt_replacement = $pdo->query("SELECT * FROM permohonan_gantian ORDER BY id DESC");
        $replacement_apps = $stmt_replacement->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $replacement_apps = []; // Fixed variable name from $replacement_applications
    }

    // Fetch Permohonan Transkrip Akademik Rasmi
    try {
        $stmt_transkrip = $pdo->query("SELECT * FROM permohonan_transkrip ORDER BY id DESC");
        $transkrip_apps = $stmt_transkrip->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $transkrip_apps = [];
    }

    // Fetch Permohonan Pindahan Kredit / Pengecualian Kursus (CTCE)
    try {
        $stmt_ctce = $pdo->query("SELECT * FROM permohonan_ctce ORDER BY id DESC");
        $ctce_applications = $stmt_ctce->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $ctce_applications = [];
    }

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
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SPP PSIS - Admin Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
    <?php include 'admin_header.php' ?>
    <div class="container mt-4">

        <!-- Alert Notifikasi Kemaskini Admin -->
        <?php if (isset($_GET['status']) && $_GET['status'] === 'success'): ?>
            <div class="alert alert-success alert-dismissible fade show text-center fw-bold mb-4" role="alert">
                <?= htmlspecialchars($_GET['msg'] ?? 'Status permohonan berjaya dikemaskini dan notifikasi telah dihantar.') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php elseif (isset($_GET['status']) && $_GET['status'] === 'error'): ?>
            <div class="alert alert-danger alert-dismissible fade show text-center fw-bold mb-4" role="alert">
                <?= htmlspecialchars($_GET['msg'] ?? 'Berlaku ralat semasa mengemaskini permohonan.') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="p-4 mb-4 hero-section rounded shadow-sm">
            <div class="d-flex justify-content-between align-items-center">
                <h4 class="fw-bold mb-1">Selamat Datang, <?= htmlspecialchars($staf_name) ?>!</h4>
            </div>
        </div>

        <?php if (!empty($message)): ?>
            <div class="alert alert-<?= $message_type ?> alert-dismissible fade show shadow-sm" role="alert">
                <?= htmlspecialchars($message) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <!-- Dynamic Announcement Alert Box with Edit Button -->
        <div class="alert alert-warning shadow-sm position-relative" role="alert">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <h5 class="alert-heading fw-bold"><?= htmlspecialchars($announcement['title'] ?? 'Pengumuman Penting !') ?></h5>
                    <p class="mb-0"><?= nl2br(htmlspecialchars($announcement['content'] ?? 'Tiada pengumuman terkini.')) ?></p>
                </div>
                <button type="button" class="btn btn-sm btn-outline-dark fw-bold ms-3" data-bs-toggle="modal" data-bs-target="#editAnnouncementModal">
                    Kemaskini
                </button>
            </div>
        </div>

        <!-- Edit Announcement Modal -->
        <div class="modal fade" id="editAnnouncementModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form method="POST">
                        <div class="modal-header">
                            <h5 class="modal-title fw-bold">Kemaskini Pengumuman Dashboard</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Tajuk Pengumuman</label>
                                <input type="text" name="ann_title" class="form-control" value="<?= htmlspecialchars($announcement['title'] ?? 'Pengumuman Penting !') ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Kandungan Pengumuman</label>
                                <textarea name="ann_content" class="form-control" rows="4" required><?= htmlspecialchars($announcement['content'] ?? '') ?></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" name="update_announcement" class="btn btn-primary btn-sm px-3">Kemaskini</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- 1. JADUAL PERMOHONAN PENGECUALIAN BAYARAN -->
        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold text-secondary">Permohonan Pengecualian Bayaran Sijil / Transkrip</h5>
                <span class="badge bg-primary rounded-pill"><?= count($pengecualian_apps) ?> Permohonan</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3">No. Permohonan</th>
                                <th>Nama Pemohon</th>
                                <th>Status</th>
                                <th>Catatan Admin</th>
                                <th class="text-center">Tindakan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($pengecualian_apps)): ?>
                                <tr><td colspan="5" class="text-center py-4 text-muted">Tiada permohonan pengecualian bayaran.</td></tr>
                            <?php else: ?>
                                <?php foreach ($pengecualian_apps as $pengecualian): ?>
                                    <tr>
                                        <td class="ps-3 fw-semibold"><?= htmlspecialchars($pengecualian['application_no'] ?? $pengecualian['id']) ?></td>
                                        <td><?= htmlspecialchars($pengecualian['applicant_name'] ?? 'N/A') ?></td>
                                        <td>
                                            <?php 
                                                $st = trim($pengecualian['status'] ?? 'In Review');
                                                $bdg = ($st === 'Completed') ? 'bg-success' : (($st === 'Rejected') ? 'bg-danger' : 'bg-warning text-dark');
                                            ?>
                                            <span class="badge <?= $bdg ?>"><?= htmlspecialchars($st) ?></span>
                                        </td>
                                        <td class="small text-muted"><?= htmlspecialchars($pengecualian['admin_remarks'] ?? '-') ?></td>
                                        <td class="text-center">
                                            <a href="view_pengecualian.php?id=<?= urlencode($pengecualian['id']) ?>" class="btn btn-sm btn-outline-primary px-3 me-1"><i class="bi bi-eye"></i> View</a>
                                            <a href="delete_app.php?id=<?= urlencode($pengecualian['id']) ?>&table=permohonan_pengecualian" class="btn btn-sm btn-outline-danger px-3" onclick="return confirm('Adakah anda pasti ingin memadam rekod ini?');"><i class="bi bi-trash"></i> Delete</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- 2. JADUAL PERMOHONAN GANTIAN SIJIL / TRANSKRIP -->
        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold text-secondary">Permohonan Borang Gantian Sijil / Transkrip Akademik</h5>
                <span class="badge bg-primary rounded-pill"><?= count($replacement_apps) ?> Permohonan</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3">No. Permohonan</th>
                                <th>Nama Pemohon</th>
                                <th>Status</th>
                                <th>Catatan Admin</th>
                                <th class="text-center">Tindakan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($replacement_apps)): ?>
                                <!-- Fixed colspan from 4 to 5 -->
                                <tr><td colspan="5" class="text-center py-4 text-muted">Tiada permohonan gantian sijil atau transkrip.</td></tr>
                            <?php else: ?>
                                <?php foreach ($replacement_apps as $replacement): ?>
                                    <tr>
                                        <td class="ps-3 fw-semibold"><?= htmlspecialchars($replacement['application_no'] ?? $replacement['id']) ?></td>
                                        <td><?= htmlspecialchars($replacement['student_name'] ?? 'N/A') ?></td>
                                        <td>
                                            <?php 
                                                $st = trim($replacement['status'] ?? 'In Review');
                                                $bdg = ($st === 'Completed') ? 'bg-success' : (($st === 'Rejected') ? 'bg-danger' : 'bg-warning text-dark');
                                            ?>
                                            <span class="badge <?= $bdg ?>"><?= htmlspecialchars($st) ?></span>
                                        </td>
                                        <td class="small text-muted"><?= htmlspecialchars($replacement['admin_remarks'] ?? '-') ?></td>
                                        <td class="text-center">
                                            <a href="view_gantian.php?id=<?= urlencode($replacement['id']) ?>" class="btn btn-sm btn-outline-primary px-3 me-1"><i class="bi bi-eye"></i> View</a>
                                            <a href="delete_app.php?id=<?= urlencode($replacement['id']) ?>&table=permohonan_gantian" class="btn btn-sm btn-outline-danger px-3" onclick="return confirm('Adakah anda pasti ingin memadam rekod ini?');"><i class="bi bi-trash"></i> Delete</a>
                                        </td>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- 3. JADUAL PERMOHONAN TRANSKRIP AKADEMIK RASMI -->
        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold text-secondary">Permohonan Transkrip Akademik Rasmi</h5>
                <span class="badge bg-primary rounded-pill"><?= count($transkrip_apps) ?> Permohonan</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3">No. Permohonan</th>
                                <th>Nama Pemohon</th>
                                <th>Status</th>
                                <th>Catatan Admin</th>
                                <th class="text-center">Tindakan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($transkrip_apps)): ?>
                                <!-- Fixed colspan from 6 to 5 -->
                                <tr><td colspan="5" class="text-center py-4 text-muted">Tiada permohonan transkrip akademik rasmi.</td></tr>
                            <?php else: ?>
                                <?php foreach ($transkrip_apps as $trans): ?>
                                    <tr>
                                        <td class="ps-3 fw-semibold"><?= htmlspecialchars($trans['application_no'] ?? $trans['id']) ?></td>
                                        <td><?= htmlspecialchars($trans['applicant_name'] ?? 'N/A') ?></td>
                                        <td>
                                            <?php 
                                                $st = trim($trans['status'] ?? 'In Review');
                                                $bdg = ($st === 'Completed') ? 'bg-success' : (($st === 'Rejected') ? 'bg-danger' : 'bg-warning text-dark');
                                            ?>
                                            <span class="badge <?= $bdg ?>"><?= htmlspecialchars($st) ?></span>
                                        </td>
                                        <td class="small text-muted"><?= htmlspecialchars($trans['admin_remarks'] ?? '-') ?></td>
                                        <td class="text-center">
                                            <a href="view_transkrip.php?id=<?= urlencode($trans['id']) ?>" class="btn btn-sm btn-outline-primary px-3 me-1"><i class="bi bi-eye"></i> View</a>
                                            <a href="delete_app.php?id=<?= urlencode($trans['id']) ?>&table=permohonan_transkrip" class="btn btn-sm btn-outline-danger px-3" onclick="return confirm('Adakah anda pasti ingin memadam rekod ini?');"><i class="bi bi-trash"></i> Delete</a>
                                        </td>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- 4. JADUAL PERMOHONAN PINDAHAN KREDIT / CTCE -->
        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold text-secondary">Permohonan Pindahan Kredit / Pengecualian Kursus (CTCE)</h5>
                <span class="badge bg-primary rounded-pill"><?= count($ctce_applications) ?> Permohonan</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                           <tr>
                                <th class="ps-3">No. Permohonan</th>
                                <th>Nama Pemohon</th>
                                <th>Status</th>
                                <th>Catatan Admin</th>
                                <th class="text-center">Tindakan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($ctce_applications)): ?>
                                <tr><td colspan="5" class="text-center py-4 text-muted">Tiada permohonan pindahan kredit / CTCE.</td></tr>
                            <?php else: ?>
                                <?php foreach ($ctce_applications as $ctce): ?>
                                    <tr>
                                        <td class="ps-3 fw-semibold"><?= htmlspecialchars($ctce['application_no'] ?? $ctce['id']) ?></td>
                                        <td><?= htmlspecialchars($ctce['student_name'] ?? 'N/A') ?></td>
                                        <td>
                                            <?php 
                                                $st = trim($ctce['status'] ?? 'In Review');
                                                $bdg = ($st === 'Completed') ? 'bg-success' : (($st === 'Rejected') ? 'bg-danger' : 'bg-warning text-dark');
                                            ?>
                                            <span class="badge <?= $bdg ?>"><?= htmlspecialchars($st) ?></span>
                                        </td>
                                        <td class="small text-muted"><?= htmlspecialchars($ctce['admin_remarks'] ?? '-') ?></td>
                                        <td class="text-center">
                                            <a href="view_ctce.php?id=<?= urlencode($ctce['id']) ?>" class="btn btn-sm btn-outline-primary px-3 me-1"><i class="bi bi-eye"></i> View</a>
                                            <a href="delete_app.php?id=<?= urlencode($ctce['id']) ?>&table=permohonan_ctce" class="btn btn-sm btn-outline-danger px-3" onclick="return confirm('Adakah anda pasti ingin memadam rekod ini?');"><i class="bi bi-trash"></i> Delete</a>
                                        </td>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <h5 class="fw-bold text-secondary mb-3">Semakan Permohonan Baru</h5>
        <div class="row g-3 mb-5">
            <div class="col-md-3">
                <div class="card h-100 shadow-sm">
                    <div class="card-body">
                        <h6 class="card-title fw-bold">BORANG PERMOHONAN PENGECUALIAN BAYARAN BAGI SALINAN SIJIL DIPLOMA/TRANSKRIP AKADEMIK</h6>
                        <p class="card-text small text-muted">Permohonan pengecualian bayaran salinan sijil dan transkrip akademik bagi lulusan terkesan bencana alam.</p>
                        <a href="senarai_pengecualian.php" class="btn btn-sm btn-primary w-100 mt-2">Semak Permohonan</a>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card h-100 shadow-sm">
                    <div class="card-body">
                        <h6 class="card-title fw-bold">BORANG PERMOHONAN GANTIAN SIJIL DIPLOMA / TRANSKRIP AKADEMIK</h6>
                        <p class="card-text small text-muted">Permohonan penggantian salinan sijil diploma dan transkrip akademik bagi graduan yang kehilangan dokumen asal.</p>
                        <a href="senarai_gantian.php" class="btn btn-sm btn-primary w-100 mt-2">Semak Permohonan</a>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card h-100 shadow-sm">
                    <div class="card-body">
                        <h6 class="card-title fw-bold">TRANSKRIP AKADEMIK RASMI</h6>
                        <p class="card-text small text-muted">Permohonan dan tuntutan Transkrip Akademik Rasmi bagi graduan yang telah berjaya menamatkan pengajian.</p>
                        <a href="senarai_transkrip.php" class="btn btn-sm btn-primary w-100 mt-2">Semak Permohonan</a>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card h-100 shadow-sm">
                    <div class="card-body">
                        <h6 class="card-title fw-bold">BORANG PERMOHONAN PINDAHAN KREDIT / PENGECUALIAN KURSUS</h6>
                        <p class="card-text small text-muted">Permohonan pemindahan kredit dan pengecualian kursus rasmi bagi pelajar yang memenuhi syarat kelayakan.</p>
                        <a href="senarai_ctce.php" class="btn btn-sm btn-primary w-100 mt-2">Semak Permohonan</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php include 'footer.php' ?>