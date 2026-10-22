<?php
    require_once 'config.php';

    // 1. Semak sesi log masuk
    if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== "student") {
        header("Location: index.php");
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
    $stmt = $pdo->prepare("SELECT * FROM permohonan_transkrip WHERE id = :id LIMIT 1");
    $stmt->execute([':id' => $id]);
    $data = $stmt->fetch(PDO::FETCH_ASSOC);

    // 4. SEMAKAN 2 (FALLBACK): Jika ID dari dashboard.php tak sama dengan ID borang spesifik,
    // ambil rekod paling terkini kepunyaan pelajar ini (berdasarkan email, reg_no, atau nama)
    if (!$data) {
        $fallback_stmt = $pdo->prepare("SELECT * FROM permohonan_transkrip WHERE $session_user_id = :sid OR applicant_name LIKE :aname ORDER BY id DESC LIMIT 1");
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

?>
<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PAPAR PERMOHONAN TRANSKRIP AKADEMIK RASMI</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body { background-color: #F5F1FA; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .header-logos { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        .header-logos img { height: 50px; width: auto; object-fit: contain; }
        .form-card { background-color: #FFFFFF; border-radius: 15px; padding: 30px; margin-bottom: 30px; box-shadow: 0 4px 15px rgba(75, 31, 111, 0.12); }
        h2, h3 { color: #3D155F; font-weight: 700; }
        .section-title { background-color: #4B1F6F; color: white; font-size: 18px; font-weight: 600; padding: 12px 18px; border-radius: 10px; display: flex; align-items: center; gap: 10px; margin-bottom: 25px; }
        .form-label { font-weight: 500; color: #3D155F; }
        .form-control, .form-select { border-radius: 8px; border: 1px solid #D8C3EE; padding: 10px; background-color: #f8f9fa; }
        .alert-warning-custom { background-color: #FFF3CD; border-left: 5px solid #FFC107; color: #664D03; padding: 15px; border-radius: 8px; }
        .official-table { width: 100%; border: 1px solid #D8C3EE; border-collapse: collapse; }
        .official-table td { border: 1px solid #D8C3EE; padding: 15px; vertical-align: top; background-color: #FAF7FF; }
    </style>
</head>
<body>
    <?php include 'user_header.php' ?>
    <div class="container my-4" style="max-width: 900px;">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <a href="user_dashboard.php" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Kembali ke Dashboard
            </a>
        </div>

        <!-- Header -->
        <div class="form-card mb-4">
            <div class="text-end fw-bold mb-2" style="color: #5B2A86; text-decoration: underline;">BORANG PSIS/UPEP/03</div>
            <div class="header-logos">
                <img src="img/psis.png" alt="Politeknik Malaysia">
                <img src="img/kpt.png" alt="Kementerian Pengajian Tinggi">
            </div>
            <hr class="text-muted">
            <div class="text-center mt-3">
                <h5 class="fw-bold text-uppercase mb-1" style="color: #4B1F6F;">UNIT PEPERIKSAAN</h5>
                <h3 class="fw-bold text-uppercase mb-0">BORANG PERMOHONAN TRANSKRIP AKADEMIK RASMI</h3>
            </div>
        </div>

        <!-- SECTION 1: MAKLUMAT PEMOHON -->
        <div class="form-card">
            <h4 class="section-title">
                <i class="bi bi-person-vcard"></i>
                MAKLUMAT PEMOHON
            </h4>

            <div class="row">
                <div class="col-md-12 mb-3">
                    <label class="form-label">Nama Pemohon</label>
                    <input type="text" class="form-control" value="<?= htmlspecialchars($data['applicant_name'] ?? '') ?>" readonly>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">No. Kad Pengenalan</label>
                    <input type="text" class="form-control" value="<?= htmlspecialchars($data['ic_no']) ?>" readonly>                          
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">No. Pendaftaran</label>
                    <input type="text" class="form-control" value="<?= htmlspecialchars($data['reg_no'] ?? '-') ?>" readonly>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">No. Telefon</label>
                    <input type="text" class="form-control" value="<?= htmlspecialchars($data['phone_no'] ?? '') ?>" readonly>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Sesi Tamat Pengajian</label>
                    <input type="text" class="form-control" value="<?= htmlspecialchars($data['graduation_session'] ?? '-') ?>" readonly>
                </div>
            </div>
        </div>

        <!-- SECTION 2: PERAKUAN PEMOHON & WAKIL -->
        <div class="form-card">
            <h4 class="section-title">
                <i class="bi bi-shield-check"></i>
                PERAKUAN PEMOHON & MAKLUMAT WAKIL
            </h4>

            <div class="p-3 mb-3 border rounded bg-light">
                <h6 class="fw-bold text-dark mb-3">
                    <i class="bi bi-people me-1"></i> Maklumat Wakil
                </h6>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Nama Wakil</label>
                        <input type="text" class="form-control" value="<?= htmlspecialchars($data['proxy_name'] ?? 'Tiada (Ambil Sendiri)') ?>" readonly>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">No. Kad Pengenalan Wakil</label>
                        <input type="text" class="form-control" value="<?= htmlspecialchars($data['proxy_ic'] ?? '-') ?>" readonly>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Hubungan</label>
                        <input type="text" class="form-control" value="<?= htmlspecialchars($data['proxy_relationship'] ?? '-') ?>" readonly>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">No. Telefon Wakil</label>
                        <input type="text" class="form-control" value="<?= htmlspecialchars($data['proxy_phone'] ?? '-') ?>" readonly>
                    </div>
                </div>
            </div>

            <div class="alert-warning-custom mb-4">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                Pemohon difahamkan bahawa pihak politeknik hanya mengeluarkan transkrip akademik <strong>SEKALI</strong> sahaja dan tidak bertanggungjawab atas kerosakan atau kehilangan.
            </div>

            <div class="row align-items-center">
                <div class="col-md-6 mb-3 text-center">
                    <label class="form-label fw-semibold d-block">Tandatangan Pemohon</label>
                    <?php if (!empty($data['signature_data'])): ?>
                        <img src="<?= htmlspecialchars($data['signature_data']) ?>" alt="Tandatangan Pemohon" class="border rounded p-2" style="max-height: 120px; background-color: white;">
                    <?php else: ?>
                        <p class="text-muted">Tiada tandatangan tersedia.</p>
                    <?php endif; ?>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Tarikh Permohonan</label>
                    <input type="text" class="form-control" value="<?= htmlspecialchars($data['application_date'] ?? '') ?>" readonly>
                </div>
            </div>
        </div>

        <!-- SECTION 3: DOKUMEN SOKONGAN -->
        <div class="form-card">
            <h4 class="section-title">
                <i class="bi bi-paperclip"></i>
                DOKUMEN SOKONGAN DILAMPIRKAN
            </h4>

            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label fw-semibold">1. Salinan K/P Pemohon</label><br>
                    <?php if (!empty($data['doc_ic_applicant_path'])): ?>
                        <a href="<?= htmlspecialchars($data['doc_ic_applicant_path']) ?>" target="_blank" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-file-earmark-pdf"></i> Lihat Dokumen
                        </a>
                    <?php else: ?>
                        <span class="text-muted">Tiada</span>
                    <?php endif; ?>
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold">2. Salinan K/P Wakil</label><br>
                    <?php if (!empty($data['doc_ic_proxy_path'])): ?>
                        <a href="<?= htmlspecialchars($data['doc_ic_proxy_path']) ?>" target="_blank" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-file-earmark-pdf"></i> Lihat Dokumen
                        </a>
                    <?php else: ?>
                        <span class="text-muted">Tiada / Ambil Sendiri</span>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- SECTION 4: STATUS KELULUSAN -->
        <div class="form-card">
            <h4 class="section-title">
                <i class="bi bi-patch-check"></i>
                STATUS KELULUSAN UNIT PEPERIKSAAN
            </h4>
            <div class="p-3 bg-light rounded border">
                <p class="fw-bold mb-1">Status Permohonan: 
                    <span class="badge bg-<?= ($data['status'] ?? '') === 'Completed' ? 'success' : (($data['status'] ?? '') === 'Rejected' ? 'danger' : 'warning') ?> fs-6">
                        <?= htmlspecialchars($data['status'] ?? 'In Review') ?>
                    </span>
                </p>
                <p class="mb-0 text-muted">Catatan: <?= htmlspecialchars($data['admin_remarks'] ?? 'Tiada ulasan lagi.') ?></p>
            </div>
        </div>
    </div>
</body>
</html>
<?php include 'footer.php' ?>