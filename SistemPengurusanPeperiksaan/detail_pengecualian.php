<?php
    require_once 'config.php';

    if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== "admin") {
        header("Location: admin_login.php");
        exit();
    }

    $id = $_GET['id'] ?? null;

    if (!$id) {
        header("Location: senarai_pengecualian.php");
        exit();
    }

    // Ambil data permohonan spesifik
    $stmt = $pdo->prepare("SELECT * FROM permohonan_pengecualian WHERE id = ?");
    $stmt->execute([$id]);
    $data = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$data) {
        echo "Permohonan tidak dijumpai.";
        exit();
    }

    // Tukar app_types (string) kepada array untuk semakan checkbox
    $app_types_arr = array_map('trim', explode(',', $data['app_types'] ?? ''));
?>
<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PAPAR PERMOHONAN PENGECUALIAN BAYARAN</title>
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

        .info-box {
            background-color: #EEE3FF;
            border-left: 5px solid #5B2A86;
            color: #3D155F;
            padding: 18px;
            border-radius: 10px;
        }

        .btn-primary {
            background-color: #5B2A86;
            border-color: #5B2A86;
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
                margin-header: 0;
                margin-footer: 0;
                @page :left {
                margin: 8mm 12mm;
            }
            @page :right {
                margin: 8mm 12mm;
                 }
            }

            body {
                background-color: #fff !important;
                color: #000 !important;
                font-family: Arial, Helvetica, sans-serif !important;
                font-size: 8.5pt !important;
                line-height: 1.15 !important;
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
                padding: 3px 6px !important;
                font-size: 8.5pt !important;
                color: #000 !important;
                vertical-align: top;
            }

            .print-table th {
                font-weight: bold !important;
                background-color: #f0f0f0 !important;
                text-align: left !important;
            }

            .table-sub-header td {
                font-weight: bold !important;
                background-color: #f8f8f8 !important;
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
            <a href="senarai_pengecualian.php" class="btn btn-secondary">
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
                BORANG PERMOHONAN PENGECUALIAN BAYARAN BAGI SALINAN SIJIL DIPLOMA / TRANSKRIP AKADEMIK
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
                    <input type="text" class="form-control" value="<?= htmlspecialchars($data['applicant_name']) ?>" readonly>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">No. Pendaftaran</label>
                    <input type="text" class="form-control" value="<?= htmlspecialchars($data['reg_no']) ?>" readonly>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">No. K/P</label>
                    <input type="text" class="form-control" value="<?= htmlspecialchars($data['ic_no']) ?>" readonly>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">No. Telefon</label>
                    <input type="text" class="form-control" value="<?= htmlspecialchars($data['phone_no']) ?>" readonly>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Alamat</label>
                <textarea class="form-control" rows="3" readonly><?= htmlspecialchars($data['address']) ?></textarea>
            </div>

            <div class="mb-3">
                <label class="form-label">Punca Kerosakan / Kehilangan</label>
                <textarea class="form-control" rows="2" readonly><?= htmlspecialchars($data['cause_of_damage']) ?></textarea>
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
                        <input class="form-check-input" type="radio" <?= $data['delivery_type'] === 'pos' ? 'checked' : '' ?> disabled>
                        <label class="form-check-label fw-medium">Secara Pos</label>
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="radio" <?= $data['delivery_type'] === 'ambil_sendiri' ? 'checked' : '' ?> disabled>
                        <label class="form-check-label fw-medium">Ambil Sendiri</label>
                    </div>

                    <?php if ($data['delivery_type'] === 'pos'): ?>
                        <div class="mt-3">
                            <label class="form-label">Alamat Penghantaran Pos</label>
                            <textarea class="form-control" rows="2" readonly><?= htmlspecialchars($data['postal_address']) ?></textarea>
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
                            <i class="bi bi-file-earmark-pdf"></i> Buka / Muat Turun Dokumen Sokongan
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

        <!-- SECTION F: KELULUSAN PENGARAH -->
        <div class="form-card mt-3">
            <h4 class="section-title">
                <i class="bi bi-pen"></i>
                (F) KELULUSAN PENGARAH INSTITUSI
            </h4>
            <table class="official-table">
                <tr>
                    <td style="width: 60%; height: 80px;">
                        <p class="fw-bold mb-1">STATUS KELULUSAN:</p>
                        <p class="mb-0 text-muted">Ulasan / Comment : ___________________________</p>
                    </td>
                    <td style="width: 40%; vertical-align: bottom;">
                        <p class="mb-0">Tandatangan Pengarah / Stamp :</p>
                    </td>
                </tr>
            </table>
        </div>
    </div>


    <!-- PRINTABLE STRUCTURE ONLY (Triggered on window.print()) -->
    <div class="print-only p-2">
        <!-- Document Header -->
        <div class="d-flex justify-content-between align-items-center mb-1">
            <div class="d-flex align-items-center gap-2">
                <img src="img/psis.png" alt="Politeknik Malaysia" style="height: 38px;">
                <span style="font-size: 8pt; font-weight: bold;">KEMENTERIAN PENGAJIAN TINGGI</span>
            </div>
            <div class="text-end fw-bold" style="font-size: 10pt;">Lampiran</div>
        </div>

        <div class="text-center fw-bold my-2" style="font-size: 9.5pt; line-height: 1.2;">
            BORANG PERMOHONAN PENGECUALIAN BAYARAN BAGI SALINAN<br>
            SIJIL / TRANSKRIP AKADEMIK
        </div>

        <!-- Section A -->
        <div style="font-size: 8pt;" class="mb-2">
            <div class="fw-bold mb-1">(A) UNTUK MAKLUMAN PEMOHON / <i>Information To Applicants</i></div>
            <ol class="ps-3 mb-0" style="line-height: 1.15;">
                <li>Permohonan berdasarkan kepada <b>ARAHAN-ARAHAN PEPERIKSAAN DAN KAEDAH PENILAIAN</b> yang telah ditetapkan dalam Perkara 27.2.</li>
                <li>Hanya lulusan Politeknik dan Kolej Komuniti sendiri yang boleh memohon pengecualian bayaran salinan sijil dan transkrip akademik sekiranya terkesan akibat bencana alam seperti banjir dan kebakaran.</li>
                <li>Pengecualian bayaran ini perlu mendapatkan kelulusan Pengarah di institusi beserta dokumen sokongan yang disahkan oleh pihak yang berkepentingan seperti pengesahan daripada Ketua Kampung atau laporan atau aduan kehilangan.</li>
                <li>Kelulusan pengecualian akan dimaklumkan kepada pemohon dalam tempoh <b>lima (5) hari bekerja</b> dari tarikh pemohon mengemukakan permohonan.</li>
            </ol>
        </div>

        <!-- Section B -->
        <div class="fw-bold" style="font-size: 8.5pt;">(B) MAKLUMAT PEMOHON / <i>Applicants Information</i></div>
        <table class="print-table w-100 mb-2">
            <tr class="table-sub-header">
                <td colspan="2">B. Butir-butir Diri Pemohon / <i>Applicant Details</i></td>
            </tr>
            <tr>
                <td style="width: 38%;">Nama Lulusan / <i>Graduate Name</i></td>
                <td style="width: 62%;"><?= htmlspecialchars($data['applicant_name']) ?></td>
            </tr>
            <tr>
                <td>No. Pendaftaran / <i>Registration No.</i></td>
                <td><?= htmlspecialchars($data['reg_no']) ?></td>
            </tr>
            <tr>
                <td>No. K/P / <i>I/C No.</i></td>
                <td><?= htmlspecialchars($data['ic_no']) ?></td>
            </tr>
            <tr>
                <td>No. Telefon / <i>Phone No.</i></td>
                <td><?= htmlspecialchars($data['phone_no']) ?></td>
            </tr>
            <tr>
                <td style="height: 38px;">Alamat / <i>Address</i></td>
                <td><?= htmlspecialchars($data['address']) ?></td>
            </tr>
            <tr>
                <td>Emel / <i>Email</i></td>
                <td><?= htmlspecialchars($data['email']) ?></td>
            </tr>
            <tr>
                <td style="height: 30px;">Punca Kerosakan/Kehilangan /<br><i>Course of damage/missing</i></td>
                <td><?= htmlspecialchars($data['cause_of_damage']) ?></td>
            </tr>
        </table>

        <!-- Section C & D -->
        <table class="print-table w-100 mb-2">
            <tr>
                <th style="width: 62%;">(C) PERMOHONAN BAGI / <i>Application for</i></th>
                <th style="width: 38%;">(D) PENGHANTARAN / <i>Delivery</i></th>
            </tr>
            <tr>
                <td style="height: 90px; vertical-align: top;">
                    <div>C. Sila tandakan pada (✓) pada kotak yang berkaitan / <i>Please tick (✓)</i>:</div>
                    <div class="mt-2 ms-3">
                        [ <?= in_array('sijil_bm', $app_types_arr) ? '✓' : '&nbsp;' ?> ] Salinan Sijil Diploma (Bahasa Melayu)<br>
                        [ <?= in_array('sijil_bi', $app_types_arr) ? '✓' : '&nbsp;' ?> ] Salinan Sijil Diploma (Bahasa Inggeris)<br>
                        [ <?= in_array('transkrip_sahaja', $app_types_arr) ? '✓' : '&nbsp;' ?> ] Salinan Transkrip Akademik sahaja
                    </div>
                </td>
                <td style="vertical-align: top;">
                    <div class="mt-3 ms-2">
                        [ <?= $data['delivery_type'] === 'pos' ? '✓' : '&nbsp;' ?> ] Secara Pos<br>
                        <?php if ($data['delivery_type'] === 'pos' && !empty($data['postal_address'])): ?>
                            <div style="font-size: 7.5pt; margin-left: 15px; font-style: italic;">
                                Alamat: <?= htmlspecialchars($data['postal_address']) ?>
                            </div>
                        <?php endif; ?>
                        <br>
                        [ <?= $data['delivery_type'] === 'ambil_sendiri' ? '✓' : '&nbsp;' ?> ] Ambil Sendiri
                    </div>
                </td>
            </tr>
        </table>

        <!-- Section E -->
        <div class="fw-bold mb-1">(E) DOKUMEN SOKONGAN / <i>Supporting document</i></div>
        <div class="mb-2" style="font-size: 8pt;">
            E. Sila tandakan pada (✓) pada kotak yang berkaitan / <i>Please tick (✓)</i>:
            <div class="d-flex align-items-start mt-1 ms-3">
                <span class="me-2">[ ✓ ]</span>
                <span>Laporan / aduan kehilangan yang telah disahkan oleh pihak yang berkepentingan seperti Ketua Kampung atau Pegawai Kerajaan Kumpulan A dan disahkan oleh Pengarah Institusi</span>
            </div>
        </div>

        <!-- Applicant Signature Display -->
        <?php if (!empty($data['signature_data'])): ?>
            <div class="text-end mb-2" style="margin-right: 30px;">
                <img src="<?= htmlspecialchars($data['signature_data']) ?>" style="max-height: 40px;"><br>
                <span style="font-size: 7.5pt; border-top: 1px dotted #000; padding-top: 2px;">Tandatangan Pemohon</span>
            </div>
        <?php endif; ?>

        <!-- Section F -->
        <table class="print-table w-100 mb-2">
            <tr>
                <th colspan="2">(F) KELULUSAN PENGARAH INSTITUSI / <i>Approved by Director</i></th>
            </tr>
            <tr>
                <td style="width: 62%; height: 80px; vertical-align: top;">
                    <b>LULUS / TIDAK LULUS</b><br>
                    <i>Approved/Not Approved</i><br><br>
                    Ulasan / <i>Comment</i> :
                </td>
                <td style="width: 38%; vertical-align: bottom; text-align: center;">
                    <div style="border-bottom: 1px dotted #000; width: 80%; margin: 0 auto 5px auto;"></div>
                    Tandatangan Pengarah /<br><i>Director Signature :</i>
                </td>
            </tr>
        </table>

        <!-- Section G -->
        <table class="print-table w-100">
            <tr>
                <th colspan="3">(G) UNTUK DIISI OLEH UNIT PEPERIKSAAN / <i>To be completed by Examination Unit</i></th>
            </tr>
            <tr style="height: 50px; text-align: center; vertical-align: top;">
                <td style="width: 33%;">Tarikh Terima / <i>Received Date :</i></td>
                <td style="width: 33%;">Tarikh Proses / <i>Processed Date :</i></td>
                <td style="width: 34%;">Cop Unit Peperiksaan /<br><i>Examination Unit Stamp :</i></td>
            </tr>
        </table>
    </div>

    <div class="no-print">
        <?php include 'footer.php' ?>
    </div>
</body>
</html>