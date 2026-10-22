<?php
    require_once 'config.php';
    // Determines if user is admin or student. Defaults to 'student'.
    if (!isset($_SESSION['user_id']) && !isset($_SESSION['id'])) {
        header("Location: index.php");
        exit();
    }
?>
<!DOCTYPE html>
<html lang="ms">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BORANG PSIS/UPEP/03 - PERMOHONAN TRANSKRIP AKADEMIK RASMI</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.1.7/dist/signature_pad.umd.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        body {
            background-color: #F5F1FA;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
        }

        .form-code {
            text-align: right;
            font-weight: 700;
            font-size: 0.9rem;
            color: #5B2A86;
            text-decoration: underline;
        }

        .header-logos {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
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
            margin-bottom: 25px;
            box-shadow: 0 4px 15px rgba(75, 31, 111, 0.12);
        }

        h2, h3 {
            color: #3D155F;
            font-weight: 700;
        }

        .section-title {
            background-color: #4B1F6F;
            color: white;
            font-size: 1.05rem;
            font-weight: 600;
            padding: 12px 18px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 20px;
        }

        .official-use-header {
            background-color: #3D155F;
            color: #FFFFFF;
            text-align: center;
            font-weight: 700;
            letter-spacing: 1px;
            padding: 10px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .required {
            color: #dc3545;
        }

        .form-label {
            font-weight: 600;
            color: #3D155F;
            margin-bottom: 4px;
        }

        .sub-label {
            font-size: 0.85rem;
            color: #6c757d;
            font-style: italic;
        }

        .form-control, .form-select {
            border-radius: 8px;
            border: 1px solid #D8C3EE;
            padding: 10px 14px;
        }

        .form-control:focus, .form-select:focus {
            border-color: #7B3FB3;
            box-shadow: 0 0 0 0.2rem rgba(91, 42, 134, 0.20);
        }

        .info-box {
            background-color: #EEE3FF;
            border-left: 5px solid #5B2A86;
            color: #3D155F;
            padding: 16px;
            border-radius: 8px;
        }

        .alert-warning-custom {
            background-color: #FFF3CD;
            border-left: 5px solid #FFC107;
            color: #664D03;
            padding: 15px;
            border-radius: 8px;
        }

        input[type="file"] {
            background-color: #FAF7FF;
            border: 1px dashed #7B3FB3;
            padding: 10px;
        }

        input[type="file"]:hover {
            background-color: #F1E7FF;
        }

        #preview {
            max-height: 120px;
            max-width: 250px;
        }

        .btn-primary {
            background-color: #5B2A86;
            border-color: #5B2A86;
            border-radius: 8px;
        }

        .btn-primary:hover {
            background-color: #3D155F;
            border-color: #3D155F;
        }

        .action-btn {
            min-width: 150px;
            height: 45px;
            font-weight: 600;
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

        .print-only {
            display: none;
        }

        /* Print Media Styles */
        @media print {
            body {
                background-color: #FFFFFF !important;
                padding: 0 !important;
                margin: 0 !important;
                font-size: 11pt !important;
            }

            .no-print, .sig-container-web {
                display: none !important;
            }

            .sig-container-print {
                display: block !important;
            }

            .form-card {
                box-shadow: none !important;
                border: 1px solid #000 !important;
                border-radius: 0 !important;
                padding: 15px !important;
                margin-bottom: 15px !important;
            }

            .section-title {
                background-color: #eee !important;
                color: #000 !important;
                border: 1px solid #000 !important;
                border-radius: 0 !important;
                font-size: 11pt !important;
            }

            .official-use-header {
                background-color: #eee !important;
                color: #000 !important;
                border: 1px solid #000 !important;
            }

            .official-table, .official-table td {
                border-color: #000 !important;
                background-color: transparent !important;
            }

            .form-control {
                border: none !important;
                border-bottom: 1px solid #000 !important;
                border-radius: 0 !important;
                padding: 2px !important;
            }
        }
    </style>
</head>
<body>
    <?php include 'user_header.php' ?>
    <div class="container my-5" style="max-width: 900px;">
        <div class="d-flex justify-content-between align-items-center mb-3 no-print">
            <a href="user_dashboard.php" type="submit" class="btn btn-primary" name="back">Back</a>
            <span class="badge bg-secondary p-2 fs-6">Form Code: PSIS/UPEP/03</span>
        </div>

        <form action="process_transkrip.php" method="POST" enctype="multipart/form-data">
            
            <!-- PAGE HEADER CARD -->
            <div class="form-card">
                
                <div class="header-logos">
                    <img src="img/psis.png" alt="Politeknik Malaysia" class="img-fluid">
                    <img src="img/kpt.png" alt="Kementerian Pengajian Tinggi" class="img-fluid">
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

                <p class="text-muted mb-3">Saya memohon untuk mengambil transkrip akademik seperti butiran berikut:</p>

                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label class="form-label">
                            Nama Pemohon <span class="required">*</span>
                        </label>
                        <input type="text" class="form-control" name="applicant_name" placeholder="Cth: NUR ALIAH BINTI MAHMUD" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">
                            No. Kad Pengenalan <span class="required">*</span>
                        </label>
                        <input type="text" class="form-control" name="ic_no" placeholder="Cth: 010101-01-1234" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">
                            No. Pendaftaran
                        </label>
                        <input type="text" class="form-control" name="reg_no" placeholder="Cth: 17DIT22F1072">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">
                            No. Telefon <span class="required">*</span>
                        </label>
                        <input type="text" class="form-control" name="phone_no" placeholder="Cth: 01X-XXXXXXXX" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">
                            Sesi Tamat Pengajian 
                        </label>
                        <input type="text" class="form-control" name="graduation_session" placeholder="Cth: Sesi II 2023/2024">
                    </div>
                </div>
            </div>

            <!-- SECTION 2: PERAKUAN PEMOHON & WAKIL -->
            <div class="form-card">
                <h4 class="section-title">
                    <i class="bi bi-shield-check"></i>
                    PERAKUAN PEMOHON
                </h4>

                <p class="mb-3">
                    Saya membuat tuntutan transkrip akademik ini dengan 
                    <strong>mengambil sendiri</strong> atau <strong>membenarkan wakil saya</strong> seperti butiran berikut:
                </p>

                <div class="p-3 mb-3 border rounded bg-light">
                    <h6 class="fw-bold text-dark mb-3">
                        <i class="bi bi-people me-1"></i> Maklumat Wakil <span class="sub-label">(Isi sekiranya menggunakan wakil)</span>
                    </h6>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nama Wakil</label>
                            <input type="text" class="form-control" name="proxy_name" placeholder="Nama Penuh Wakil">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">No. Kad Pengenalan Wakil</label>
                            <input type="text" class="form-control" name="proxy_ic" placeholder="No. K/P Wakil">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Hubungan</label>
                            <input type="text" class="form-control" name="proxy_relationship" placeholder="Cth: Ibu / Bapa / Abang / Rakan">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">No. Telefon Wakil</label>
                            <input type="text" class="form-control" name="proxy_phone" placeholder="No. Telefon Wakil">
                        </div>
                    </div>
                </div>
                
                <div class="alert-warning-custom mb-4">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="agreeDeclaration" required>
                        <label class="form-check-label" for="agreeDeclaration">
                                                 <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                                Saya faham bahawa pihak politeknik hanya mengeluarkan transkrip akademik <strong>SEKALI</strong> sahaja dan pihak politeknik tidak akan bertanggungjawab sekiranya berlaku kerosakan atau kehilangan di atas tindakan saya membenarkan wakil saya mengambil transkrip akademik rasmi ini.
                                                 <span class="required">*</span>
                        </label>
                    </div>
                </div>

                <!-- Web Interactive Signature Pad -->
                <div class="row mt-3 sig-container-web">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">
                            Tandatangan Pemohon <span class="text-danger">*</span>
                        </label>
                        <div class="border rounded bg-white p-2">
                            <canvas id="signature-pad" class="w-100" height="150" style="border: 1px dashed #ced4da; cursor: crosshair;"></canvas>
                            <button type="button" id="clear-btn" class="btn btn-sm btn-outline-danger mt-2">
                                <i class="bi bi-trash"></i> Padam & Lukis Semula
                            </button>
                        </div>
                        <input type="hidden" id="signature" name="signature" required>
                        <small class="text-muted">Sila tandatangan di dalam kotak di atas.</small>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Hasil Tandatangan</label>
                        <div class="border rounded p-3 bg-light text-center d-flex align-items-center justify-content-center" style="min-height: 195px;">
                            <img id="preview" src="" alt="Signature Preview" style="display:none; max-height:120px;" class="img-fluid">
                            <p id="previewText" class="text-muted mb-0">Tiada tandatangan dihasilkan.</p>
                        </div>
                    </div>
                </div>

                <!-- Print-Only Signature Display Section -->
                <div class="print-only text-end mt-2 sig-container-print">
                    <div style="display: inline-block; text-align: center;">
                        <img id="print_sig_img" src="" style="max-height: 50px; display: none;">
                        <div style="border-top: 1px solid #000; width: 200px; margin-top: 5px;"></div>
                        <div style="font-size: 9pt;">Tandatangan Pemohon</div>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Tarikh Permohonan</label>
                        <input type="date" class="form-control" name="application_date" value="<?php echo date('Y-m-d'); ?>" readonly>
                    </div>
                </div>
            </div>

            <!-- SECTION 3: DOKUMEN SOKONGAN (STUDENT INPUT) -->
            <div class="form-card">
                <h4 class="section-title">
                    <i class="bi bi-file-earmark-arrow-up"></i>
                    MUAT NAIK DOKUMEN SOKONGAN
                </h4>

                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label fw-semibold">
                            1. Salinan Kad Pengenalan Pemohon (Lulusan) <span class="required">*</span>
                        </label>
                        <input type="file" id="file1" name="doc_ic_applicant_path" class="form-control" accept=".pdf,.png,.jpg,.jpeg" required>
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-semibold">
                            2. Salinan Kad Pengenalan Wakil <span class="sub-label">(jika wakil yang menuntut)</span>
                        </label>
                        <input type="file" id="file2" name="doc_ic_proxy_path" class="form-control" accept=".pdf,.png,.jpg,.jpeg">
                    </div>
                </div>
            </div>

            <!-- SECTION 4: UNTUK KEGUNAAN UNIT PEPERIKSAAN (ADMIN ONLY) -->
            <?php if ($_SESSION['role'] === 'admin'): ?>
                <div class="form-card">
                    <div class="official-use-header">
                        UNTUK KEGUNAAN UNIT PEPERIKSAAN
                    </div>

                    <!-- Official Verification Table -->
                    <h6 class="fw-bold mt-2 mb-2" style="color: #3D155F;">PENGESAHAN UNIT PEPERIKSAAN</h6>
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
            <?php endif; ?>

            <!-- BUTTON SECTION -->
            <div class="d-flex justify-content-end gap-3 mb-5 no-print">
                <button type="submit" class="btn btn-primary action-btn">
                    <i class="bi bi-send me-1"></i> Hantar
                </button>
            </div>
        </form>
    </div>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const canvas = document.getElementById('signature-pad');
            const signatureInput = document.getElementById('signature');
            const previewImg = document.getElementById('preview');
            const printSigImg = document.getElementById('print_sig_img');
            const previewText = document.getElementById('previewText');
            const clearBtn = document.getElementById('clear-btn');

            const signaturePad = new SignaturePad(canvas, {
                backgroundColor: 'rgba(255, 255, 255, 1)',
                penColor: 'rgb(0, 0, 0)'
            });

            function resizeCanvas() {
                const ratio = Math.max(window.devicePixelRatio || 1, 1);
                canvas.width = canvas.offsetWidth * ratio;
                canvas.height = canvas.offsetHeight * ratio;
                canvas.getContext("2d").scale(ratio, ratio);
                signaturePad.clear();
                resetPreview();
            }

            window.addEventListener("resize", resizeCanvas);
            resizeCanvas();

            signaturePad.addEventListener("endStroke", () => {
                if (!signaturePad.isEmpty()) {
                    const dataUrl = signaturePad.toDataURL('image/png');
                    signatureInput.value = dataUrl;
                    previewImg.src = dataUrl;
                    if (printSigImg) printSigImg.src = dataUrl;
                    previewImg.style.display = 'block';
                    if (printSigImg) printSigImg.style.display = 'inline-block';
                    previewText.style.display = 'none';
                }
            });

            clearBtn.addEventListener('click', function () {
                signaturePad.clear();
                resetPreview();
            });

            function resetPreview() {
                signatureInput.value = '';
                previewImg.src = '';
                if (printSigImg) printSigImg.src = '';
                previewImg.style.display = 'none';
                if (printSigImg) printSigImg.style.display = 'none';
                previewText.style.display = 'block';
            }
        });
    </script>
</body>
</html>
<?php include 'footer.php' ?>