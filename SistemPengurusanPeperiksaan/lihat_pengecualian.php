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
    $stmt = $pdo->prepare("SELECT * FROM permohonan_pengecualian WHERE id = :id LIMIT 1");
    $stmt->execute([':id' => $id]);
    $data = $stmt->fetch(PDO::FETCH_ASSOC);

    // 4. SEMAKAN 2 (FALLBACK): Jika ID dari dashboard.php tak sama dengan ID borang spesifik,
    // ambil rekod paling terkini kepunyaan pelajar ini (berdasarkan email, reg_no, atau nama)
    if (!$data) {
        $fallback_stmt = $pdo->prepare("SELECT * FROM permohonan_pengecualian WHERE $session_user_id = :sid OR applicant_name LIKE :aname ORDER BY id DESC LIMIT 1");
        $fallback_stmt->execute([
            ':sid'   => $session_user_id,
            ':aname' => '%' . $user_name . '%'
        ]);
        $data = $fallback_stmt->fetch(PDO::FETCH_ASSOC);
    }

    // 5. Jika masih tiada rekod langsung
    if (!$data) {
        die("<div class='container my-5 alert alert-danger'>Tiada rekod permohonan pengecualian bayaran dijumpai untuk akaun anda.</div>");
    }

    $app_types_arr = array_map('trim', explode(',', $data['app_types'] ?? ''));
?>
<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BUTIRAN PERMOHONAN PENGECUALIAN BAYARAN</title>
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
        .official-table { width: 100%; border: 1px solid #D8C3EE; border-collapse: collapse; }
        .official-table td { border: 1px solid #D8C3EE; padding: 15px; vertical-align: top; background-color: #FAF7FF; }
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
                BORANG PERMOHONAN PENGECUALIAN BAYARAN BAGI SALINAN SIJIL DIPLOMA / TRANSKRIP AKADEMIK
            </h2>
        </div>

        <!-- SECTION A: MAKLUMAN -->
        <div class="form-card">
            <h4 class="section-title">
                <i class="bi bi-info-circle"></i>
                (A) MAKLUMAN PEMOHON
            </h4>
            <div class="info-box">
                <ol class="mb-0 ps-3">
                    <li>Permohonan berdasarkan kepada <b>ARAHAN-ARAHAN PEPERIKSAAN DAN KAEDAH PENILAIAN</b> yang sedang berkuatkuasa.</li>
                    <li>Hanya lulusan Politeknik dan Kolej Komuniti sendiri yang boleh memohon pengecualian bayaran salinan sijil dan transkrip akademik sekiranya terkesan akibat bencana alam.</li>
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
                    <input type="text" class="form-control" value="<?= htmlspecialchars($data['applicant_name'] ?? '') ?>" readonly>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">No. Pendaftaran</label>
                    <input type="text" class="form-control" value="<?= htmlspecialchars($data['reg_no'] ?? '') ?>" readonly>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">No. K/P</label>
                    <input type="text" class="form-control" value="<?= htmlspecialchars($data['ic_no'] ?? '') ?>" readonly>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">No. Telefon</label>
                    <input type="text" class="form-control" value="<?= htmlspecialchars($data['phone_no'] ?? '') ?>" readonly>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Alamat</label>
                <textarea class="form-control" rows="3" readonly><?= htmlspecialchars($data['address'] ?? '') ?></textarea>
            </div>

            <div class="mb-3">
                <label class="form-label">Punca Kerosakan / Kehilangan</label>
                <textarea class="form-control" rows="2" readonly><?= htmlspecialchars($data['cause_of_damage'] ?? '') ?></textarea>
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
                        <input class="form-check-input" type="checkbox" <?= in_array('sijil_bm', $app_types_arr) ? 'checked' : '' ?> disabled>
                        <label class="form-check-label fw-medium">Salinan Sijil Diploma (Bahasa Melayu)</label>
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" <?= in_array('sijil_bi', $app_types_arr) ? 'checked' : '' ?> disabled>
                        <label class="form-check-label fw-medium">Salinan Sijil Diploma (Bahasa Inggeris)</label>
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" <?= in_array('transkrip_sahaja', $app_types_arr) ? 'checked' : '' ?> disabled>
                        <label class="form-check-label fw-medium">Salinan Transkrip Akademik sahaja</label>
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
                        <input class="form-check-input" type="radio" <?= ($data['delivery_type'] ?? '') === 'pos' ? 'checked' : '' ?> disabled>
                        <label class="form-check-label fw-medium">Secara Pos</label>
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="radio" <?= ($data['delivery_type'] ?? '') === 'ambil_sendiri' ? 'checked' : '' ?> disabled>
                        <label class="form-check-label fw-medium">Ambil Sendiri</label>
                    </div>

                    <?php if (($data['delivery_type'] ?? '') === 'pos'): ?>
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
                    <?php if (!empty($data['support_document_path'])): ?>
                        <a href="<?= htmlspecialchars($data['support_document_path']) ?>" target="_blank" class="btn btn-outline-primary">
                            <i class="bi bi-file-earmark-pdf"></i> Lihat Dokumen Sokongan
                        </a>
                    <?php else: ?>
                        <p class="text-muted">Tiada dokumen dimuat naik.</p>
                    <?php endif; ?>
                </div>

                <div class="col-md-6 mb-3 text-center">
                    <label class="form-label fw-semibold d-block">Tandatangan Pemohon</label>
                    <?php if (!empty($data['signature_data'])): ?>
                        <img src="<?= htmlspecialchars($data['signature_data']) ?>" alt="Tandatangan Pelajar" class="border rounded p-2" style="max-height: 120px; background-color: white;">
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
                (F) STATUS KELULUSAN INSTITUSI
            </h4>
            <div class="p-3 bg-light rounded border">
                <p class="fw-bold mb-1">Status Permohonan: 
                    <span class="badge bg-<?= ($data['status'] ?? '') === 'Completed' ? 'success' : (($data['status'] ?? '') === 'Rejected' ? 'danger' : 'warning') ?> fs-6">
                        <?= htmlspecialchars($data['status'] ?? 'In Review') ?>
                    </span>
                </p>
                <p class="mb-0 text-muted">Catatan Pengarah / Pentadbir: <?= htmlspecialchars($data['admin_remarks'] ?? 'Tiada ulasan lagi.') ?></p>
            </div>
        </div>
    </div>
</body>
</html>
<?php include 'footer.php' ?>