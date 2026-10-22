<?php
require_once 'config.php';

// Semak akses Admin
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== "admin") {
    header("Location: admin_login.php");
    exit();
}

$id = $_GET['id'] ?? null;

if (!$id) {
    header("Location: senarai_ctce.php");
    exit();
}

// Ambil data permohonan spesifik
$stmt = $pdo->prepare("SELECT * FROM permohonan_ctce WHERE id = ?");
$stmt->execute([$id]);
$data = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$data) {
    echo "Permohonan tidak dijumpai.";
    exit();
}

// Tukar JSON dari pangkalan data kepada Array PHP
$ctce_courses = !empty($data['ctce_courses']) ? json_decode($data['ctce_courses'], true) : [];
$equivalent_courses = !empty($data['equivalent_courses']) ? json_decode($data['equivalent_courses'], true) : [];
?>

<!DOCTYPE html>
<html lang="ms">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BORANG PERMOHONAN PINDAHAN KREDIT / PENGECUALIAN KURSUS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        body {
            background-color: #f8f9fa;
            font-family: Arial, Helvetica, sans-serif;
            color: #000;
        }

        /* Landscape A4 Container Dimensions */
        .paper-container {
            background-color: #fff;
            width: 297mm;
            min-height: 210mm;
            margin: 20px auto;
            padding: 10mm 15mm;
            border: 1px solid #d3d3d3;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        /* Form Header Layout */
        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
        }

        .header-table td {
            vertical-align: top;
        }

        .header-title {
            text-align: center;
            font-weight: bold;
            font-size: 11pt;
            line-height: 1.2;
        }

        .header-title span {
            display: block;
            font-size: 9.5pt;
            font-weight: normal;
            margin-top: 2px;
        }

        /* Structured Form Tables */
        .form-grid {
            width: 100%;
            border-collapse: collapse;
            font-size: 9pt;
            margin-top: 2px;
        }

        .form-grid td {
            padding: 2px 4px;
            vertical-align: middle;
        }

        .dotted-line {
            border-bottom: 1px dotted #000;
            display: inline-block;
            min-height: 1.1em;
        }

        .char-box {
            display: inline-block;
            width: 16px;
            height: 18px;
            border: 1px solid #000;
            text-align: center;
            line-height: 16px;
            font-size: 8pt;
            font-weight: bold;
            margin-right: -1px;
            background-color: #fff;
        }

        /* Checkbox Box Top Right */
        .check-table {
            border-collapse: collapse;
            font-size: 8pt;
            width: 100%;
        }

        .check-table td {
            border: 1px solid #000;
            padding: 2px 4px;
        }

        /* Instructions Text */
        .instruction-text {
            font-size: 8pt;
            font-style: italic;
            margin-top: 6px;
            margin-bottom: 6px;
            line-height: 1.2;
        }

        /* Main Data Table */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8.5pt;
            margin-top: 4px;
            text-align: center;
        }

        .data-table th, .data-table td {
            border: 1px solid #000;
            padding: 4px 3px;
        }

        .data-table th {
            font-weight: bold;
            font-size: 8pt;
            background-color: #f2f2f2;
        }

        /* Footer Signatures */
        .sig-section {
            width: 100%;
            margin-top: 12px;
            font-size: 8.5pt;
        }

        .sig-box {
            vertical-align: top;
            padding: 0 8px;
        }

        .btn-glass {
            background: rgba(255, 255, 255, 0.9);
            color: #3D155F;
            border: 1.5px solid #7B3FB3;
            padding: 9px 20px;
            border-radius: 12px;
            font-weight: 600;
            font-size: 14px;
            backdrop-filter: blur(5px);
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-glass:hover {
            background: #7B3FB3;
            color: #ffffff;
            border-color: #7B3FB3;
            transform: translateY(-2px);
            box-shadow: 0 4px 14px rgba(123, 63, 179, 0.25);
        }

        /* PRINT STYLES - Set for Landscape */
        @media print {
            body {
                background-color: #fff !important;
                margin: 0 !important;
                padding: 0 !important;
            }

            .no-print {
                display: none !important;
            }

            .paper-container {
                border: none !important;
                box-shadow: none !important;
                width: 100% !important;
                min-height: auto !important;
                padding: 0 !important;
                margin: 0 !important;
            }

            @page {
                size: A4 landscape;
                margin: 6mm 10mm;
            }
        }
    </style>
</head>
<body>
    <!-- DISOROKKAN SEMASA CETAKAN -->
    <div class="no-print">
        <?php include 'admin_header.php' ?>
        <!-- Action Bar -->
        <div class="container my-3" style="max-width: 297mm;">
            <div class="d-flex justify-content-between align-items-center">
                <a href="senarai_ctce.php" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Kembali ke Senarai
                </a>
                <button type="button" class="btn btn-glass" onclick="window.print()">
                    <i class="bi bi-printer fs-5"></i> Cetak Borang
                </button>
            </div>
        </div>
    </div>

    <!-- Printable Official CTCE Paper Form Layout (Landscape) -->
    <div class="paper-container">

        <!-- Header -->
        <table class="header-table">
            <tr>
                <td style="width: 15%;">
                    <img src="img/kpt.png" alt="Coat of Arms" style="height: 42px;">
                </td>
                <td style="width: 70%;" class="header-title">
                    UNIT PEPERIKSAAN<br>
                    BORANG PERMOHONAN PINDAHAN KREDIT / PENGECUALIAN KURSUS<br>
                    <span>SESI PENGAJIAN: <u><?= htmlspecialchars($data['created_at'] ? date('Y', strtotime($data['created_at'])) : '________________') ?></u></span>
                </td>
                <td style="width: 15%; text-align: right;">
                    <img src="img/psis.png" alt="Politeknik Logo" style="height: 42px;">
                </td>
            </tr>
        </table>

        <!-- Student Details -->
        <table class="form-grid">
            <tr>
                <td style="width: 12%;">Nama Pelajar</td>
                <td style="width: 53%;">: <span class="dotted-line" style="width: 92%;"><?php echo strtoupper(htmlspecialchars($data['student_name'] ?? '')); ?></span></td>
                <td style="width: 35%" rowspan="3">
                    <table class="check-table">
                        <tr>
                            <td colspan="2" style="font-weight: bold; text-align: center;">Sila tandakan (✓)</td>
                        </tr>
                        <tr>
                            <td>Transkrip Yang Disahkan</td>
                            <td style="width: 25px; text-align: center;"><?= !empty($data['document_transcript']) ? '✓' : '' ?></td>
                        </tr>
                        <tr>
                            <td>Sukatan Kursus Yang Disahkan</td>
                            <td style="text-align: center;"><?= !empty($data['document_certificate']) ? '✓' : '' ?></td>
                        </tr>
                        <tr>
                            <td>Dokumen Penyetaraan (jika berkaitan)</td>
                            <td style="text-align: center;"><?= (!empty($data['document_officer']) || !empty($data['document_other'])) ? '✓' : '' ?></td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td>No. K/P</td>
                <td>: 
                    <?php 
                    $ic_chars = str_replace(['-', ' '], '', $data['ic_number'] ?? '');
                    for ($i = 0; $i < strlen($ic_chars); $i++): 
                        if ($i == 6 || $i == 8) echo ' - ';
                    ?>
                        <span class="char-box"><?= htmlspecialchars($ic_chars[$i]) ?></span>
                    <?php endfor; ?>
                </td>
            </tr>
            <tr>
                <td>No. Pendaftaran</td>
                <td>: 
                    <?php 
                    $reg_chars = str_replace(['-', ' '], '', $data['student_id'] ?? '');
                    for ($i = 0; $i < strlen($reg_chars); $i++): 
                    ?>
                        <span class="char-box"><?= htmlspecialchars($reg_chars[$i]) ?></span>
                    <?php endfor; ?>
                </td>
            </tr>
            <tr>
                <td>No. Telefon</td>
                <td>: <span class="dotted-line" style="width: 50%;"><?= htmlspecialchars($data['phone'] ?? '') ?></span></td>
                <td>
                    Program : 
                    <?php 
                    $prog = $data['programme'] ?? '';
                    for ($i = 0; $i < strlen($prog); $i++): 
                    ?>
                        <span class="char-box"><?= htmlspecialchars($prog[$i]) ?></span>
                    <?php endfor; ?>
                    &nbsp; Kelas : <span class="dotted-line" style="width: 60px;"><?= htmlspecialchars($data['class'] ?? '') ?></span>
                </td>
            </tr>
        </table>

        <p class="instruction-text">
            Sila baca Garis Panduan Pindahan Kredit dan Pengecualian Kursus (CTCE) sebelum mengisi borang ini. Sila isikan petak-petak di bawah ini dengan terang dan betul. Pastikan anda menulis kod dan nama kursus yang berkenaan setelah berbincang dengan Penasihat Akademik.
        </p>

        <!-- Course Exemption Table -->
        <table class="data-table">
            <thead>
                <tr>
                    <th rowspan="2" style="width: 3%;">Bil</th>
                    <th colspan="3" style="width: 42%;">Kursus Yang Dipohon CT/CE</th>
                    <th colspan="4" style="width: 43%;">Kursus Terdahulu Yang Setara</th>
                    <th rowspan="2" style="width: 12%;">Pertimbangan Keputusan</th>
                    <th rowspan="2" style="width: 10%;">Tandatangan & Cop Ketua Program</th>
                </tr>
                <tr>
                    <th style="width: 10%;">Kod Kursus</th>
                    <th>Nama Kursus</th>
                    <th style="width: 6%;">Jam Kredit</th>
                    <th style="width: 10%;">Kod Kursus</th>
                    <th>Nama Kursus</th>
                    <th style="width: 6%;">Jam Kredit</th>
                    <th style="width: 8%;">Gred / % Keseluruhan</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                for ($i = 0; $i < 6; $i++): 
                    $ctce = $ctce_courses[$i] ?? [];
                    $equiv = $equivalent_courses[$i] ?? [];
                ?>
                <tr style="height: 24px;">
                    <td><?= $i + 1 ?></td>
                    <!-- Kursus Yang Dipohon CT/CE -->
                    <td><?= htmlspecialchars($ctce['course_code'] ?? '') ?></td>
                    <td style="text-align: left;"><?= htmlspecialchars($ctce['course_name'] ?? '') ?></td>
                    <td><?= htmlspecialchars($ctce['credit_hour'] ?? '') ?></td>

                    <!-- Kursus Terdahulu Yang Setara -->
                    <td><?= htmlspecialchars($equiv['course_code'] ?? '') ?></td>
                    <td style="text-align: left;"><?= htmlspecialchars($equiv['course_name'] ?? '') ?></td>
                    <td><?= htmlspecialchars($equiv['credit_hour'] ?? '') ?></td>
                    <td><?= htmlspecialchars($equiv['grade'] ?? '') ?></td>

                    <td style="font-size: 7.5pt; line-height: 1;">Sokong / Tidak Disokong</td>
                    <td></td>
                </tr>
                <?php endfor; ?>
            </tbody>
        </table>

        <!-- Signatures and Approvals Section -->
        <table class="sig-section">
            <tr>
                <td class="sig-box" style="width: 33%;">
                    <p class="mb-1">Saya mengaku bahawa keterangan yang diberi dalam borang ini adalah betul dan benar :</p>
                    <div style="height: 35px; text-align: left; vertical-align: bottom;">
                        <?php if (!empty($data['signature'])): ?>
                            <img src="<?= htmlspecialchars($data['signature']) ?>" style="max-height: 32px; display: block;">
                        <?php endif; ?>
                    </div>
                    Tandatangan Pelajar<br>
                    Tarikh : <?= htmlspecialchars($data['declaration_date'] ?? '________________') ?>
                </td>
                <td class="sig-box" style="width: 33%;">
                    <p class="mb-1">&nbsp;</p>
                    <div style="height: 35px;"></div>
                    Pengesahan Ketua Jabatan :<br><br>
                    Tandatangan Ketua Jabatan dan Cop Rasmi<br>
                    Tarikh :
                </td>
                <td class="sig-box" style="width: 34%;">
                    <p class="mb-1">Keputusan dibuat oleh Jawatankuasa Pindahan Kredit & Pengecualian Kursus Politeknik Sultan Idris Shah :</p>
                    <p style="font-weight: bold; text-align: center; margin: 2px 0;">DILULUSKAN / TIDAK DILULUSKAN</p>
                    <div style="height: 20px;"></div>
                    Tandatangan Pengerusi dan Cop Rasmi<br>
                    Tarikh :
                </td>
            </tr>
        </table>
        <div style="font-size: 7.5pt; margin-top: 6px;">
            *Potong yang TIDAK berkenaan.
        </div>
    </div>

    <!-- FOOTER DISOROKKAN SEMASA CETAKAN -->
    <div class="no-print">
        <?php include 'footer.php' ?>
    </div>
</body>
</html>