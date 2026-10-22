<?php
    require_once 'config.php';

    // Semak akses Admin
    if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== "admin") {
        header("Location: admin_login.php");
        exit();
    }

    $id = $_GET['id'] ?? null;

    if (!$id) {
        header("Location: senarai_transkrip.php");
        exit();
    } 

    // Ambil data permohonan spesifik
    $stmt = $pdo->prepare("SELECT * FROM permohonan_transkrip WHERE id = ?");
    $stmt->execute([$id]);
    $data = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$data) {
        echo "Permohonan tidak dijumpai.";
        exit();
    }

    $is_proxy = !empty($data['proxy_name']);
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

        h2, h3 {
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

        .sub-label {
            font-size: 0.85rem;
            color: #6c757d;
            font-style: italic;
        }

        .form-control,
        .form-select {
            border-radius: 8px;
            border: 1px solid #D8C3EE;
            padding: 10px;
            background-color: #f8f9fa;
        }

        .alert-warning-custom {
            background-color: #FFF3CD;
            border-left: 5px solid #FFC107;
            color: #664D03;
            padding: 15px;
            border-radius: 8px;
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
                margin: 8mm 12mm;
            }

            body {
                background-color: #fff !important;
                color: #000 !important;
                font-family: Arial, Helvetica, sans-serif !important;
                font-size: 9pt !important;
                line-height: 1.2 !important;
                padding: 0 !important;
                margin: 0 !important;
            }

            .no-print {
                display: none !important;
            }

            .print-only {
                display: block !important;
            }

            .print-table {
                width: 100% !important;
                border-collapse: collapse !important;
                margin-bottom: 6px !important;
            }

            .print-table td, 
            .print-table th {
                border: 1px solid #000 !important;
                padding: 4px 6px !important;
                font-size: 8.5pt !important;
                color: #000 !important;
                vertical-align: top;
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
    <!-- WEB VIEW HEADER -->
    <div class="no-print">
        <?php include 'admin_header.php' ?>
    </div>

    <!-- WEB DISPLAY (Unchanged) -->
    <div class="container my-4 no-print" style="max-width: 900px;">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <a href="senarai_transkrip.php" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Kembali ke Senarai
            </a>
            <button type="button" class="btn btn-outline-dark" onclick="window.print()">
                <i class="bi bi-printer me-1"></i> Cetak Borang
            </button>
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
                    <input type="text" class="form-control" value="<?= htmlspecialchars($data['applicant_name']) ?>" readonly>
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
                    <input type="text" class="form-control" value="<?= htmlspecialchars($data['phone_no']) ?>" readonly>
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
                    <input type="text" class="form-control" value="<?= htmlspecialchars($data['application_date']) ?>" readonly>
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

        <!-- SECTION 4: UNTUK KEGUNAAN UNIT PEPERIKSAAN -->
        <div class="form-card">
            <h4 class="section-title">
                <i class="bi bi-pen"></i>
                UNTUK KEGUNAAN UNIT PEPERIKSAAN
            </h4>
            <table class="official-table">
                <tr>
                    <td style="width: 50%; height: 100px;">
                        <p class="fw-bold mb-1">Tandatangan Pegawai :</p>
                    </td>
                    <td style="width: 50%; height: 100px;">
                        <p class="fw-bold mb-1">Tarikh Pengesahan :</p>
                    </td>
                </tr>
            </table>
        </div>
    </div>


    <!-- PRINTABLE STRUCTURE ONLY (Matches image_72810d.jpg) -->
    <div class="print-only">
        <!-- Top Reference Code -->
        <div class="text-end fw-bold mb-1" style="font-size: 8.5pt;">BORANG PSIS/UPEP/03</div>

        <!-- Header Section -->
        <div class="d-flex justify-content-between align-items-start mb-2">
            <div class="d-flex align-items-center gap-2">
                <img src="img/kpt.png" alt="Coat of Arms" style="height: 40px;">
                <div class="fw-bold" style="font-size: 8.5pt; line-height: 1.2;">
                    UNIT PEPERIKSAAN<br>
                    BORANG PERMOHONAN<br>
                    TRANSKRIP AKADEMIK RASMI
                </div>
            </div>
            <div>
                <img src="img/psis.png" alt="Politeknik Malaysia" style="height: 38px;">
            </div>
        </div>

        <!-- Maklumat Pemohon Header -->
        <div class="fw-bold mb-1" style="font-size: 8.5pt;">MAKLUMAT PEMOHON :</div>
        <div class="mb-1" style="font-size: 8pt;">Saya memohon untuk mengambil transkrip akademik seperti butiran berikut:</div>

        <!-- Table 1: Applicant Details -->
        <table class="print-table mb-2">
            <tr>
                <td style="width: 32%;">Nama</td>
                <td style="width: 68%;"><?= htmlspecialchars($data['applicant_name']) ?></td>
            </tr>
            <tr>
                <td>No. Kad Pengenalan</td>
                <td><?= htmlspecialchars($data['ic_no']) ?></td>
            </tr>
            <tr>
                <td>No. Pendaftaran</td>
                <td><?= htmlspecialchars($data['reg_no'] ?? '') ?></td>
            </tr>
            <tr>
                <td style="height: 35px;">Alamat</td>
                <td><?= htmlspecialchars($data['address'] ?? '') ?></td>
            </tr>
            <tr>
                <td>No. Telefon</td>
                <td><?= htmlspecialchars($data['phone_no']) ?></td>
            </tr>
            <tr>
                <td>Sesi Tamat Pengajian</td>
                <td><?= htmlspecialchars($data['graduation_session'] ?? '') ?></td>
            </tr>
        </table>

        <!-- Perakuan Pemohon Section -->
        <div class="fw-bold mb-1" style="font-size: 8.5pt;">PERAKUAN PEMOHON :</div>
        <div class="mb-1" style="font-size: 8pt; line-height: 1.2;">
            Saya membuat tuntutan transkrip akademik ini dengan 
            <b><?= !$is_proxy ? '<u>*mengambil sendiri</u>' : 'mengambil sendiri' ?></b> / 
            <b><?= $is_proxy ? '<u>membenarkan wakil saya</u>' : 'membenarkan wakil saya' ?></b> seperti butiran berikut:<br>
            <i style="font-size: 7.5pt;">*potong yang tidak berkenaan</i>
        </div>

        <!-- Table 2: Proxy Details -->
        <table class="print-table mb-2">
            <tr>
                <td style="width: 32%;">Nama Wakil</td>
                <td style="width: 68%;"><?= htmlspecialchars($data['proxy_name'] ?? '') ?></td>
            </tr>
            <tr>
                <td>No. Kad Pengenalan</td>
                <td><?= htmlspecialchars($data['proxy_ic'] ?? '') ?></td>
            </tr>
            <tr>
                <td>Hubungan</td>
                <td><?= htmlspecialchars($data['proxy_relationship'] ?? '') ?></td>
            </tr>
            <tr>
                <td>No. Telefon</td>
                <td><?= htmlspecialchars($data['proxy_phone'] ?? '') ?></td>
            </tr>
        </table>

        <!-- Disclaimer Paragraph -->
        <div class="mb-2" style="font-size: 7.8pt; text-align: justify; line-height: 1.2;">
            Saya faham bahawa pihak politeknik hanya mengeluarkan transkrip akademik <b>SEKALI</b> sahaja dan pihak politeknik tidak akan bertanggungjawab sekiranya berlaku kerosakan atau kehilangan di atas tindakan saya membenarkan wakil saya mengambil transkrip akademik rasmi ini.
        </div>

        <!-- Signature Block -->
        <div class="d-flex justify-content-between align-items-end mb-2" style="font-size: 8.5pt;">
            <table style="border: none; width: 60%;">
                <tr style="border: none;">
                    <td style="border: none; padding: 2px 0; width: 110px;">Tandatangan</td>
                    <td style="border: none; padding: 2px 0;">: 
                        <?php if (!empty($data['signature_data'])): ?>
                            <img src="<?= htmlspecialchars($data['signature_data']) ?>" style="max-height: 35px; vertical-align: middle;">
                        <?php endif; ?>
                    </td>
                </tr>
                <tr style="border: none;">
                    <td style="border: none; padding: 2px 0;">Nama Pemohon</td>
                    <td style="border: none; padding: 2px 0;">: <?= htmlspecialchars($data['applicant_name']) ?></td>
                </tr>
                <tr style="border: none;">
                    <td style="border: none; padding: 2px 0;">Tarikh</td>
                    <td style="border: none; padding: 2px 0;">: <?= htmlspecialchars($data['application_date'] ?? date('d/m/Y')) ?></td>
                </tr>
            </table>
        </div>

        <!-- Table 3: Section Kegunaan Unit Peperiksaan Header -->
        <table class="print-table mb-2">
            <tr style="background-color: #f8f8f8;">
                <td class="text-center fw-bold" style="padding: 4px;">UNTUK KEGUNAAN UNIT PEPERIKSAAN</td>
            </tr>
        </table>

        <!-- Checklist Section -->
        <div class="fw-bold mb-1" style="font-size: 8.5pt;">SENARAI SEMAK DOKUMEN SOKONGAN :</div>
        <div class="d-flex justify-content-between align-items-start mb-2">
            <ol class="ps-3 mb-0" style="font-size: 8.2pt; line-height: 1.3; width: 75%;">
                <li>Salinan Kad Pengenalan Pemohon (Lulusan)</li>
                <li>Salinan Kad Pengenalan Wakil (jika wakil yang menuntut)</li>
            </ol>
            <div style="width: 18%;">
                <table class="checkbox-grid">
                    <tr><td></td></tr>
                    <tr><td></td></tr>
                </table>
            </div>
        </div>

        <!-- Verification Section -->
        <table style="border: none; width: 100%; font-size: 8.5pt;" class="mt-2">
            <tr style="border: none;">
                <td style="border: none; padding: 2px 0; width: 110px;">PENGESAHAN</td>
                <td style="border: none; padding: 2px 0;">:</td>
            </tr>
            <tr style="border: none;">
                <td style="border: none; padding: 2px 0;">Tandatangan</td>
                <td style="border: none; padding: 2px 0;">:</td>
            </tr>
            <tr style="border: none;">
                <td style="border: none; padding: 2px 0;">Tarikh</td>
                <td style="border: none; padding: 2px 0;">:</td>
            </tr>
        </table>
    </div>

    <!-- WEB VIEW FOOTER -->
    <div class="no-print">
        <?php include 'footer.php' ?>
    </div>
</body>
</html>