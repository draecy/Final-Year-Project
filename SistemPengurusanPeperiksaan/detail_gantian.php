<?php
    require_once 'config.php';

    // Semak akses Admin
    if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== "admin") {
        header("Location: admin_login.php");
        exit();
    }

    $id = $_GET['id'] ?? null;

    if (!$id) {
        header("Location: senarai_gantian.php");
        exit();
    }

    // Ambil data permohonan spesifik
    $stmt = $pdo->prepare("SELECT * FROM permohonan_gantian WHERE id = ?");
    $stmt->execute([$id]);
    $data = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$data) {
        echo "Permohonan tidak dijumpai.";
        exit();
    }

    // Tukar application_type (string) kepada array untuk semakan checkbox
    $app_types_arr = array_map('trim', explode(',', $data['application_type'] ?? ''));

    $has_bm = false;
    $has_bi = false;
    $has_transkrip = false;

    foreach ($app_types_arr as $type) {
        if (stripos($type, 'BM') !== false) $has_bm = true;
        if (stripos($type, 'BI') !== false) $has_bi = true;
        if (stripos($type, 'Transkrip') !== false) $has_transkrip = true;
    }
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
        body {
            background-color: #F5F1FA;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .header-logos {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .header-logos img {
            height: 50px;
            width: auto;
            object-fit: contain;
        }

        .form-card {
            background-color: #FFFFFF;
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 4px 15px rgba(75, 31, 111, 0.12);
        }

        h2 {
            color: #3D155F;
            font-weight: 700;
        }

        .section-title {
            background-color: #4B1F6F;
            color: white;
            font-size: 18px;
            font-weight: 600;
            padding: 12px 18px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 25px;
        }

        .form-label {
            font-weight: 500;
            color: #3D155F;
        }

        .form-control,
        .form-select {
            border-radius: 8px;
            border: 1px solid #D8C3EE;
            padding: 10px;
            background-color: #f8f9fa;
        }

        .info-box {
            background-color: #EEE3FF;
            border-left: 5px solid #5B2A86;
            color: #3D155F;
            padding: 18px;
            border-radius: 10px;
        }

        .official-table {
            width: 100%;
            border: 1px solid #D8C3EE;
            border-collapse: collapse;
        }

        .official-table td {
            border: 1px solid #D8C3EE;
            padding: 15px;
            vertical-align: top;
            background-color: #FAF7FF;
        }

        /* PRINT STYLING */
        .print-only {
            display: none;
        }

        @media print {
            @page {
                size: A4 portrait;
                margin: 10mm 15mm;
            }

            body {
                background-color: #fff !important;
                color: #000 !important;
                font-family: Arial, Helvetica, sans-serif !important;
                font-size: 9.5pt !important;
                line-height: 1.3 !important;
                padding: 0 !important;
                margin: 0 !important;
            }

            .no-print {
                display: none !important;
            }

            .print-only {
                display: block !important;
            }

            .page-break {
                page-break-before: always;
            }

            .print-table {
                width: 100% !important;
                border-collapse: collapse !important;
            }

            .print-table td, 
            .print-table th {
                border: 1px solid #000 !important;
                padding: 4px 8px !important;
                font-size: 9pt !important;
                color: #000 !important;
                vertical-align: middle;
            }

            .print-table th {
                background-color: #e0e0e0 !important;
                font-weight: bold !important;
            }

            .dotted-leader {
                border-bottom: 1px dotted #000;
                display: inline-block;
                min-height: 16px;
            }

            .checkbox-grid {
                width: 100%;
                border-collapse: collapse;
            }

            .checkbox-grid td {
                border: 1px solid #000;
                height: 18px;
            }
        }
    </style>
</head>
<body>
    <div class="no-print">
        <?php include 'admin_header.php' ?>
    </div>

    <!-- WEB VIEW ONLY -->
    <div class="container my-4 no-print">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <a href="senarai_gantian.php" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Kembali ke Senarai
            </a>
            <button type="button" class="btn btn-outline-dark" onclick="window.print()">
                <i class="bi bi-printer me-1"></i> Cetak Borang
            </button>
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
                        <input class="form-check-input" type="checkbox" <?= $has_bm ? 'checked' : '' ?> disabled>
                        <label class="form-check-label fw-medium">Sijil Diploma Versi BM (RM50)</label>
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" <?= $has_bi ? 'checked' : '' ?> disabled>
                        <label class="form-check-label fw-medium">Sijil Diploma Versi BI (RM50)</label>
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" <?= $has_transkrip ? 'checked' : '' ?> disabled>
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

        <!-- SECTION F: KELULUSAN UNIT PEPERIKSAAN -->
        <div class="form-card mt-3">
            <h4 class="section-title">
                <i class="bi bi-pen"></i>
                (F) KELULUSAN UNIT PEPERIKSAAN
            </h4>
            <table class="official-table">
                <tr>
                    <td style="width: 60%; height: 80px;">
                        <p class="fw-bold mb-1">STATUS KELULUSAN:</p>
                        <p class="mb-0 text-muted">Ulasan / Comment : ___________________________</p>
                    </td>
                    <td style="width: 40%; vertical-align: bottom;">
                        <p class="mb-0">Tandatangan Pegawai / Stamp :</p>
                    </td>
                </tr>
            </table>
        </div>
    </div>


    <!-- PRINTABLE STRUCTURE ONLY (Matches image_728889.jpg & image_7288ae.jpg) -->
    <div class="print-only">
        
        <!-- PAGE 1 -->
        <div style="border: 1px solid #000; padding: 15px; min-height: 95vh;">
            <div class="text-end fw-bold mb-1" style="font-size: 8.5pt;">BORANG PSIS/UPEP/02</div>

            <!-- Header Section -->
            <div class="d-flex justify-content-between align-items-start mb-3">
                <div class="d-flex align-items-center gap-2">
                    <img src="img/kpt.png" alt="Coat of Arms" style="height: 42px;">
                    <div class="fw-bold" style="font-size: 8.5pt; line-height: 1.2;">
                        UNIT PEPERIKSAAN<br>
                        BORANG PERMOHONAN SALINAN<br>
                        SIJIL/TRANSKRIP AKADEMIK
                    </div>
                </div>
                <div class="text-end">
                    <img src="img/psis.png" alt="Politeknik Malaysia" style="height: 38px;">
                </div>
            </div>

            <!-- Jenis Permohonan -->
            <div class="fw-bold mb-1" style="font-size: 9pt;">JENIS PERMOHONAN :</div>
            <table class="print-table mb-3">
                <thead>
                    <tr>
                        <th style="width: 8%; text-align: center;">Bil</th>
                        <th style="width: 52%;">Perkara</th>
                        <th style="width: 20%; text-align: center;">Bayaran</th>
                        <th style="width: 20%; text-align: center;">Tandakan</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="text-center">1.</td>
                        <td>Sijil (Versi BM)</td>
                        <td class="text-center">RM 50.00</td>
                        <td class="text-center"><?= $has_bm ? '✓' : '' ?></td>
                    </tr>
                    <tr>
                        <td class="text-center">2.</td>
                        <td>Sijil (Versi BI)</td>
                        <td class="text-center">RM 50.00</td>
                        <td class="text-center"><?= $has_bi ? '✓' : '' ?></td>
                    </tr>
                    <tr>
                        <td class="text-center">3.</td>
                        <td>Transkrip Akademik</td>
                        <td class="text-center">RM10.00</td>
                        <td class="text-center"><?= $has_transkrip ? '✓' : '' ?></td>
                    </tr>
                </tbody>
            </table>

            <!-- Maklumat Lulusan -->
            <div class="fw-bold mb-2" style="font-size: 9pt;">MAKLUMAT LULUSAN :</div>
            
            <table style="width: 100%; font-size: 9pt; border-collapse: separate; border-spacing: 0 6px;" class="mb-3">
                <tr>
                    <td style="width: 130px;">Nama</td>
                    <td style="width: 15px;">:</td>
                    <td><span class="dotted-leader w-100"><?= htmlspecialchars($data['student_name'] ?? '') ?></span></td>
                </tr>
                <tr>
                    <td>No. Kad Pengenalan</td>
                    <td>:</td>
                    <td><span class="dotted-leader w-100"><?= htmlspecialchars($data['ic_number'] ?? '') ?></span></td>
                </tr>
                <tr>
                    <td>No. Pendaftaran</td>
                    <td>:</td>
                    <td><span class="dotted-leader w-100"><?= htmlspecialchars($data['student_id'] ?? '') ?></span></td>
                </tr>
                <tr>
                    <td style="vertical-align: top;">Alamat</td>
                    <td style="vertical-align: top;">:</td>
                    <td>
                        <span class="dotted-leader w-100"><?= htmlspecialchars($data['address'] ?? '') ?></span><br>
                        <span class="dotted-leader w-100 mt-1"></span>
                    </td>
                </tr>
                <tr>
                    <td>No Telefon</td>
                    <td>:</td>
                    <td><span class="dotted-leader w-100"><?= htmlspecialchars($data['phone'] ?? '') ?></span></td>
                </tr>
                <tr>
                    <td>Program</td>
                    <td>:</td>
                    <td><span class="dotted-leader w-100"><?= htmlspecialchars($data['programme'] ?? '') ?></span></td>
                </tr>
                <tr>
                    <td>Tahun Pengajian</td>
                    <td>:</td>
                    <td><span class="dotted-leader w-100"><?= htmlspecialchars($data['study_year'] ?? '') ?></span></td>
                </tr>
                <tr>
                    <td>Sesi Tamat Pengajian</td>
                    <td>:</td>
                    <td><span class="dotted-leader w-100"><?= htmlspecialchars($data['graduation_session'] ?? '') ?></span></td>
                </tr>
            </table>

            <!-- Perakuan Pemohon -->
            <div class="fw-bold mb-2" style="font-size: 9pt;">PERAKUAN PEMOHON :</div>
            <ol class="ps-3 mb-3" style="font-size: 8.5pt; line-height: 1.3;">
                <li class="mb-1">Saya mengaku dan mengesahkan semua maklumat di atas adalah benar dan tepat.</li>
                <li>Saya bersetuju sekiranya terdapat maklumat yang dinyatakan tidak benar atau palsu, maka pihak Politeknik Sultan Idris Shah berhak menolak permohonan ini.</li>
            </ol>

            <!-- Signature Form Section -->
            <table style="width: 100%; font-size: 9pt; border-collapse: separate; border-spacing: 0 5px;" class="mt-2">
                <tr>
                    <td style="width: 130px;">Nama</td>
                    <td style="width: 15px;">:</td>
                    <td><span class="dotted-leader w-75"><?= htmlspecialchars($data['student_name'] ?? '') ?></span></td>
                </tr>
                <tr>
                    <td>No. K/P</td>
                    <td>:</td>
                    <td><span class="dotted-leader w-75"><?= htmlspecialchars($data['ic_number'] ?? '') ?></span></td>
                </tr>
                <tr>
                    <td style="vertical-align: bottom;">Tandatangan</td>
                    <td style="vertical-align: bottom;">:</td>
                    <td>
                        <div class="d-inline-block" style="min-width: 250px;">
                            <?php if (!empty($data['signature'])): ?>
                                <img src="../uploads/signatures/<?= htmlspecialchars($data['signature']) ?>" style="max-height: 35px; vertical-align: middle;">
                            <?php endif; ?>
                            <div class="dotted-leader w-100"></div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>Tarikh</td>
                    <td>:</td>
                    <td><span class="dotted-leader w-75"><?= htmlspecialchars($data['created_at'] ?? date('d/m/Y')) ?></span></td>
                </tr>
            </table>
        </div>

        <!-- PAGE 2 -->
        <div class="page-break"></div>
        <div style="border: 1px solid #000; padding: 15px; min-height: 95vh;">
            <div class="text-end fw-bold mb-2" style="font-size: 8.5pt;">BORANG PSIS/UPEP/02</div>

            <!-- Page 2 Header Table -->
            <table class="print-table mb-3">
                <tr style="background-color: #f0f0f0;">
                    <td class="text-center fw-bold" style="padding: 6px; font-size: 9.5pt;">PENGESAHAN UNIT PEPERIKSAAN</td>
                </tr>
            </table>

            <!-- Checklist Section -->
            <div class="fw-bold mb-2" style="font-size: 9pt;">SENARAI SEMAK DOKUMEN SOKONGAN :</div>
            <div class="d-flex justify-content-between align-items-start mb-4">
                <ol class="ps-3 mb-0" style="font-size: 8.8pt; line-height: 1.5; width: 75%;">
                    <li>Borang Permohonan PSIS/UPEP/02</li>
                    <li>Salinan Laporan Polis</li>
                    <li>Salinan Kad Pengenalan Lulusan</li>
                    <li>Salinan Sijil Asal</li>
                    <li>Salinan Keputusan Peperiksaan</li>
                    <li>Salinan Transkrip Akademik</li>
                </ol>
                <div style="width: 18%;">
                    <table class="checkbox-grid">
                        <tr><td></td></tr>
                        <tr><td></td></tr>
                        <tr><td></td></tr>
                        <tr><td></td></tr>
                        <tr><td></td></tr>
                        <tr><td></td></tr>
                    </table>
                </div>
            </div>

            <!-- Disahkan Block -->
            <div class="fw-bold mb-2" style="font-size: 9pt;">DISAHKAN :</div>
            <table style="width: 100%; font-size: 9pt; border-collapse: separate; border-spacing: 0 8px;" class="mb-4">
                <tr>
                    <td style="width: 160px;">Tarikh Terima</td>
                    <td style="width: 15px;">:</td>
                    <td><span class="dotted-leader w-75"></span></td>
                </tr>
                <tr>
                    <td>Tandatangan Pegawai</td>
                    <td>:</td>
                    <td><span class="dotted-leader w-75"></span></td>
                </tr>
                <tr>
                    <td style="vertical-align: top;">Nama & Cop Pegawai</td>
                    <td style="vertical-align: top;">:</td>
                    <td><span class="dotted-leader w-75"></span></td>
                </tr>
            </table>

            <!-- Pengesahan Penyerahan Block -->
            <div class="fw-bold mb-2" style="font-size: 9pt;">PENGESAHAN PENYERAHAN :</div>
            <table style="width: 100%; font-size: 9pt; border-collapse: separate; border-spacing: 0 8px;">
                <tr>
                    <td style="width: 160px;">Tarikh Serah</td>
                    <td style="width: 15px;">:</td>
                    <td><span class="dotted-leader w-75"></span></td>
                </tr>
                <tr>
                    <td>Tandatangan</td>
                    <td>:</td>
                    <td><span class="dotted-leader w-75"></span></td>
                </tr>
                <tr>
                    <td style="vertical-align: top;">Nama Lulusan / Wakil<br><i style="font-size: 8pt;">(Surat Rasmi Kebenaran)</i></td>
                    <td style="vertical-align: top;">:</td>
                    <td style="vertical-align: top;"><span class="dotted-leader w-75"></span></td>
                </tr>
                <tr>
                    <td>No. Kad Pengenalan<br>Lulusan / Wakil</td>
                    <td>:</td>
                    <td><span class="dotted-leader w-75"></span></td>
                </tr>
                <tr>
                    <td>Tandatangan Pegawai</td>
                    <td>:</td>
                    <td><span class="dotted-leader w-75"></span></td>
                </tr>
                <tr>
                    <td style="vertical-align: top;">Nama & Cop Pegawai</td>
                    <td style="vertical-align: top;">:</td>
                    <td><span class="dotted-leader w-75"></span></td>
                </tr>
            </table>
        </div>
    </div>

    <!-- WEB VIEW FOOTER -->
    <div class="no-print">
        <?php include 'footer.php' ?>
    </div>
</body>
</html>