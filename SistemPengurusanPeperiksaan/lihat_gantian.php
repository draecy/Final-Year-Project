<?php
    require_once 'config.php';

    // 1. Semak sesi log masuk
    if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== "student") {
        header("Location: user_login.php");
        exit();
    }

    $id = $_GET['id'] ?? null;
    $session_user_id = $_SESSION['user_id'];

    // 2. Dapatkan maklumat akaun pelajar
    $user_stmt = $pdo->prepare("SELECT email, full_name FROM users WHERE id = :uid LIMIT 1");
    $user_stmt->execute([':uid' => $session_user_id]);
    $user = $user_stmt->fetch(PDO::FETCH_ASSOC);

    $user_email = $user['email'] ?? ($_SESSION['email'] ?? '');
    $user_name  = trim($user['full_name'] ?? '');


    // 3. SEMAKAN 1: Cuba cari guna ID direct
    $stmt = $pdo->prepare("SELECT * FROM permohonan_gantian WHERE id = :id LIMIT 1");
    $stmt->execute([':id' => $id]);
    $data = $stmt->fetch(PDO::FETCH_ASSOC);

    // 4. SEMAKAN 2 (FALLBACK): Jika ID dari dashboard.php tak sama dengan ID borang spesifik,
    // ambil rekod paling terkini kepunyaan pelajar ini (berdasarkan email, reg_no, atau nama)
    if (!$data) {
        $fallback_stmt = $pdo->prepare("SELECT * FROM permohonan_gantian WHERE $session_user_id = :sid OR student_name LIKE :aname ORDER BY id DESC LIMIT 1");
        $fallback_stmt->execute([
            ':sid'   => $session_user_id,
            ':aname' => '%' . $user_name . '%'
        ]);
        $data = $fallback_stmt->fetch(PDO::FETCH_ASSOC);
    }

    // 5. Jika masih tiada rekod langsung
    if (!$data) {
        die("<div class='container my-5 alert alert-danger'>Tiada rekod permohonan transkrip dijumpai untuk akaun anda.</div>");
    }

    $app_types_arr = array_map('trim', explode(',', $data['app_types'] ?? ''));
?>

<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PAPAR PERMOHONAN GANTIAN SIJIL / TRANSKRIP AKADEMIK</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body { background-color: #F5F1FA; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .header-logos { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        .header-logos img { height: 50px; width: auto; object-fit: contain; }
        .form-card { background-color: #FFFFFF; border-radius: 15px; padding: 30px; margin-bottom: 30px; box-shadow: 0 4px 15px rgba(75, 31, 111, 0.12); }
        h2 { color: #3D155F; font-weight: 700; }
        .section-title { background-color: #4B1F6F; color: white; font-size: 18px; font-weight: 600; padding: 12px 18px; border-radius: 10px; display: flex; align-items: center; gap: 10px; margin-bottom: 25px; }
        .form-label { font-weight: 500; color: #3D155F; }
        .form-control, .form-select { border-radius: 8px; border: 1px solid #D8C3EE; padding: 10px; background-color: #f8f9fa; }
        .info-box { background-color: #EEE3FF; border-left: 5px solid #5B2A86; color: #3D155F; padding: 18px; border-radius: 10px; }
    </style>
</head>
<body>
    <?php include 'user_header.php' ?>
    <div class="container my-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <a href="user_dashboard.php" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Kembali ke Dashboard
            </a>
        </div>

        <!-- Header -->
        <div class="form-card mb-4">
            <div class="header-logos">
                <img src="img/psis.png" alt="Politeknik Malaysia">
                <img src="img/kpt.png" alt="Kementerian Pengajian Tinggi">
            </div>
            <hr class="text-muted">
            <h2 class="fw-bold text-center mt-2">
                PERMOHONAN GANTIAN SIJIL DIPLOMA / TRANSKRIP AKADEMIK
            </h2>
        </div>

        <!-- SECTION A: MAKLUMAN -->
        <div class="form-card">
            <h4 class="section-title">
                <i class="bi bi-info-circle"></i>
                (A) UNTUK MAKLUMAN PEMOHON
            </h4>
            <div class="info-box">
                <ol class="mb-0 ps-3">
                    <li>Kadar Bayaran: Salinan Sijil (RM50.00) | Salinan Transkrip Akademik (RM10.00).</li>
                    <li>Permohonan gantian sijil/transkrip akan diproses dalam tempoh kerja yang ditetapkan selepas dokumen dan bayaran disahkan.</li>
                </ol>
            </div>
        </div>

        <!-- SECTION B: MAKLUMAT PEMOHON -->
        <div class="form-card">
            <h4 class="section-title">
                <i class="bi bi-person-vcard"></i>
                (B) MAKLUMAT PEMOHON
            </h4>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Nama Lulusan</label>
                    <input type="text" class="form-control" value="<?= htmlspecialchars($data['student_name'] ?? '') ?>" readonly>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">No. Pendaftaran</label>
                    <input type="text" class="form-control" value="<?= htmlspecialchars($data['student_id'] ?? '') ?>" readonly>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">No. K/P</label>
                    <input type="text" class="form-control" value="<?= htmlspecialchars($data['ic_number'] ?? '') ?>" readonly>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">No. Telefon</label>
                    <input type="text" class="form-control" value="<?= htmlspecialchars($data['phone'] ?? '') ?>" readonly>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Alamat</label>
                <textarea class="form-control" rows="3" readonly><?= htmlspecialchars($data['address'] ?? '') ?></textarea>
            </div>

            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Program</label>
                    <input type="text" class="form-control" value="<?= htmlspecialchars($data['programme'] ?? '') ?>" readonly>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Tahun Pengajian</label>
                    <input type="text" class="form-control" value="<?= htmlspecialchars($data['study_year'] ?? '') ?>" readonly>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Sesi Tamat Pengajian</label>
                    <input type="text" class="form-control" value="<?= htmlspecialchars($data['graduation_session'] ?? '') ?>" readonly>
                </div>
            </div>
        </div>

        <!-- SECTION C & D -->
        <div class="row">
            <!-- SECTION C -->
            <div class="col-md-6 mb-3">
                <div class="form-card h-100 mb-0">
                    <h4 class="section-title">
                        <i class="bi bi-file-earmark-check"></i>
                        (C) PERMOHONAN BAGI
                    </h4>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" <?= in_array('Sijil Diploma Versi BM', $app_types_arr) ? 'checked' : '' ?> disabled>
                        <label class="form-check-label fw-medium">Sijil Diploma Versi BM (RM50)</label>
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" <?= in_array('Sijil Diploma Versi BI', $app_types_arr) ? 'checked' : '' ?> disabled>
                        <label class="form-check-label fw-medium">Sijil Diploma Versi BI (RM50)</label>
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" <?= in_array('Transkrip Akademik', $app_types_arr) ? 'checked' : '' ?> disabled>
                        <label class="form-check-label fw-medium">Transkrip Akademik (RM10)</label>
                    </div>
                </div>
            </div>

            <!-- SECTION D -->
            <div class="col-md-6 mb-3">
                <div class="form-card h-100 mb-0">
                    <h4 class="section-title">
                        <i class="bi bi-truck"></i>
                        (D) PENGHANTARAN
                    </h4>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="radio" <?= ($data['collection_method'] ?? '') === 'Pos' ? 'checked' : '' ?> disabled>
                        <label class="form-check-label fw-medium">Secara Pos</label>
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="radio" <?= ($data['collection_method'] ?? '') === 'Ambil Sendiri' ? 'checked' : '' ?> disabled>
                        <label class="form-check-label fw-medium">Ambil Sendiri</label>
                    </div>

                    <?php if (($data['collection_method'] ?? '') === 'Pos'): ?>
                        <div class="mt-3">
                            <label class="form-label">Alamat Penghantaran Pos</label>
                            <textarea class="form-control" rows="2" readonly><?= htmlspecialchars($data['postal_address'] ?? '') ?></textarea>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- SECTION E: DOKUMEN SOKONGAN & TANDATANGAN -->
        <div class="form-card mt-3">
            <h4 class="section-title">
                <i class="bi bi-paperclip"></i>
                (E) DOKUMEN SOKONGAN & TANDATANGAN
            </h4>

            <div class="row align-items-center">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-semibold">Dokumen Sokongan Uploaded</label><br>
                    <?php if (!empty($data['payment_receipt'])): ?>
                        <a href="../uploads/documents/<?= htmlspecialchars($data['payment_receipt']) ?>" target="_blank" class="btn btn-outline-primary mb-2 me-2">
                            <i class="bi bi-file-earmark-pdf"></i> Resit Bayaran
                        </a>
                    <?php endif; ?>
                    <?php if (!empty($data['police_report'])): ?>
                        <a href="../uploads/documents/<?= htmlspecialchars($data['police_report']) ?>" target="_blank" class="btn btn-outline-primary mb-2 me-2">
                            <i class="bi bi-file-earmark-pdf"></i> Laporan Polis
                        </a>
                    <?php endif; ?>
                    <?php if (empty($data['payment_receipt']) && empty($data['police_report'])): ?>
                        <p class="text-muted">Tiada dokumen dimuat naik.</p>
                    <?php endif; ?>
                </div>

                <div class="col-md-6 mb-3 text-center">
                    <label class="form-label fw-semibold d-block">Tandatangan Pemohon</label>
                    <?php if (!empty($data['signature'])): ?>
                        <img src="../uploads/signatures/<?= htmlspecialchars($data['signature']) ?>" alt="Tandatangan Pelajar" class="border rounded p-2" style="max-height: 120px; background-color: white;">
                    <?php else: ?>
                        <p class="text-muted">Tiada tandatangan tersedia.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- SECTION F: STATUS KELULUSAN -->
        <div class="form-card mt-3">
            <h4 class="section-title">
                <i class="bi bi-patch-check"></i>
                (F) KELULUSAN UNIT PEPERIKSAAN
            </h4>
            <div class="p-3 bg-light rounded border">
                <?php $status_val = $data['status'] ?? 'In Review'; ?>
                <p class="fw-bold mb-1">Status Permohonan: 
                    <span class="badge bg-<?= $status_val === 'Completed' ? 'success' : ($status_val === 'Rejected' ? 'danger' : 'warning') ?> fs-6">
                        <?= htmlspecialchars($status_val) ?>
                    </span>
                </p>
                <p class="mb-0 text-muted">Catatan: <?= htmlspecialchars($data['remarks'] ?? $data['main_remarks'] ?? 'Tiada ulasan lagi.') ?></p>
            </div>
        </div>
    </div>
</body>
</html>
<?php include 'footer.php' ?>