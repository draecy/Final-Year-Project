<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BORANG PERMOHONAN PENGECUALIAN BAYARAN</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.1.7/dist/signature_pad.umd.min.js"></script>

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

        .required {
            color: #dc3545;
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
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #7B3FB3;
            box-shadow: 0 0 0 0.2rem rgba(91, 42, 134, 0.20);
        }

        .form-check-input:checked {
            background-color: #5B2A86;
            border-color: #5B2A86;
        }

        .info-box {
            background-color: #EEE3FF;
            border-left: 5px solid #5B2A86;
            color: #3D155F;
            padding: 18px;
            border-radius: 10px;
        }

        input[type="file"] {
            background-color: #FAF7FF;
            border: 1px dashed #7B3FB3;
            padding: 12px;
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
            min-width: 160px;
            height: 45px;
            font-weight: 500;
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

        @media print {
            @page {
                size: A4 portrait;
                margin: 12mm 15mm;
            }

            body {
                background-color: #fff !important;
                color: #000 !important;
                font-family: 'Times New Roman', Times, serif, Arial !important;
                font-size: 10pt !important;
                line-height: 1.2 !important;
                padding: 0 !important;
                margin: 0 !important;
            }

            .no-print {
                display: none !important;
            }

            .form-card {
                box-shadow: none !important;
                border: none !important;
                border-radius: 0 !important;
                padding: 0 !important;
                margin-bottom: 12px !important;
                background-color: transparent !important;
            }

            .header-logos {
                margin-bottom: 5px !important;
            }

            .header-logos img {
                height: 40px !important;
            }

            h2 {
                color: #000 !important;
                font-size: 11pt !important;
                text-align: center !important;
                margin-top: 5px !important;
                margin-bottom: 5px !important;
            }

            hr {
                border-top: 1px solid #000 !important;
                opacity: 1 !important;
                margin: 5px 0 !important;
            }

            .section-title {
                background-color: transparent !important;
                color: #000 !important;
                font-size: 10pt !important;
                font-weight: bold !important;
                padding: 0 !important;
                margin-bottom: 4px !important;
                border: none !important;
            }

            .section-title i {
                display: none !important;
            }

            .sub-label {
                color: #000 !important;
                font-style: italic !important;
            }

            .info-box {
                background-color: transparent !important;
                border: none !important;
                padding: 0 !important;
                color: #000 !important;
            }

            .info-box ol {
                padding-left: 20px !important;
            }

            .info-box li {
                margin-bottom: 3px !important;
            }

            .print-table {
                width: 100% !important;
                border-collapse: collapse !important;
                margin-bottom: 10px !important;
            }

            .print-table td, 
            .print-table th {
                border: 1px solid #000 !important;
                padding: 4px 8px !important;
                vertical-align: top !important;
                background: transparent !important;
                color: #000 !important;
            }

            .print-table-header {
                font-weight: bold !important;
                background-color: #f0f0f0 !important;
            }

            .web-section-b,
            .web-section-cd,
            .file-upload-wrapper,
            .sig-container-web {
                display: none !important;
            }

            .print-section-b,
            .print-section-cd {
                display: table !important;
            }

            .sig-container-print {
                display: block !important;
            }

            .official-table {
                border: 1px solid #000 !important;
            }

            .official-table td {
                border: 1px solid #000 !important;
                background-color: transparent !important;
                color: #000 !important;
                padding: 6px !important;
            }
        }

        .print-only {
            display: none;
        }
    </style>
</head>
<body>
    <?php include 'user_header.php' ?>
    <div class="container my-4">
        <div class="d-flex justify-content-between align-items-center mb-3 no-print">
            <a href="user_dashboard.php" type="submit" class="btn btn-primary" name="back">Back</a>
            <span class="badge bg-secondary p-2">Lampiran</span>
        </div>

        <!-- Page Header Card -->
        <div class="form-card mb-4">
            <div class="header-logos">
                <img src="img/psis.png" alt="Politeknik Malaysia" class="img-fluid">
                <img src="img/kpt.png" alt="Kementerian Pengajian Tinggi" class="img-fluid">
            </div>
            <div class="text-end fw-bold mb-1 print-only" style="font-size: 11pt;">Lampiran</div>
            <hr class="text-muted">
            <h2 class="fw-bold text-center mt-2">
                BORANG PERMOHONAN PENGECUALIAN BAYARAN BAGI SALINAN SIJIL DIPLOMA / TRANSKRIP AKADEMIK
            </h2>
        </div>

        <form action="process_pengecualian.php" method="POST" enctype="multipart/form-data">
            
            <!-- SECTION A: INFORMATION -->
            <div class="form-card">
                <h4 class="section-title">
                    <i class="bi bi-info-circle"></i>
                    (A) UNTUK MAKLUMAN PEMOHON
                </h4>

                <div class="info-box">
                    <ol class="mb-0 ps-3">
                        <li>Permohonan berdasarkan kepada <b>ARAHAN-ARAHAN PEPERIKSAAN DAN KAEDAH PENILAIAN</b> yang sedang berkuatkuasa.</li>
                        <li>Hanya lulusan Politeknik dan Kolej Komuniti sendiri yang boleh memohon pengecualian bayaran salinan sijil dan transkrip akademik sekiranya terkesan akibat bencana alam seperti banjir dan kebakaran.</li>
                        <li>Pengecualian bayaran ini perlu mendapatkan kelulusan Pengarah di institusi beserta dokumen sokongan yang disahkan oleh pihak yang berkepentingan seperti pengesahan daripada Ketua Kampung atau laporan atau aduan kehilangan.</li>
                        <li>Kelulusan pengecualian akan dimaklumkan kepada pemohon dalam <b>tempoh lima (5) hari bekerja</b> dari tarikh pemohon mengemukakan permohonan.</li>
                    </ol>
                </div>
            </div>

            <!-- SECTION B: APPLICANT DETAILS (WEB DISPLAY) -->
            <div class="form-card web-section-b">
                <h4 class="section-title">
                    <i class="bi bi-person-vcard"></i>
                    (B) MAKLUMAT PEMOHON
                </h4>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">
                            Nama Penuh <span class="required">*</span>
                        </label>
                        <input type="text" class="form-control" name="applicant_name" id="web_applicant_name" placeholder="Contoh: ALI BIN ABU" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">
                            No. Pendaftaran <span class="required">*</span>
                        </label>
                        <input type="text" class="form-control" name="reg_no" id="web_reg_no" placeholder="Contoh: 17DIT24FXXXX" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">
                            No. Kad Pengenalan <span class="required">*</span>
                        </label>
                        <input type="text" class="form-control" name="ic_no" id="web_ic_no" placeholder="Contoh: 001245-10-XXXX" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">
                            No. Telefon <span class="required">*</span>
                        </label>
                        <input type="text" class="form-control" name="phone_no" id="web_phone_no" placeholder="Contoh: 010-XXX9863" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">
                        Alamat Penuh <span class="required">*</span>
                    </label>
                    <textarea class="form-control" name="address" id="web_address" rows="3" required></textarea>
                </div>


                <div class="mb-3">
                    <label class="form-label">
                        Punca Kerosakan / Kehilangan <span class="required">*</span>
                    </label>
                    <textarea class="form-control" name="cause_of_damage" id="web_cause" rows="2" placeholder="Nyatakan sebab (contoh: Kebakaran / Banjir)" required></textarea>
                </div>
            </div>

            <!-- PRINTABLE SECTION B -->
            <div class="print-only">
                <div class="fw-bold mb-1">(B) MAKLUMAT PEMOHON</div>
                <table class="print-table print-section-b">
                    <tr class="print-table-header">
                        <td colspan="2">B. Butir-butir Diri Pemohon</td>
                    </tr>
                    <tr>
                        <td style="width: 35%;">Nama Penuh</td>
                        <td style="width: 65%;" id="print_applicant_name"></td>
                    </tr>
                    <tr>
                        <td>No. Pendaftaran</td>
                        <td id="print_reg_no"></td>
                    </tr>
                    <tr>
                        <td>No. Kad Pengenalan</td>
                        <td id="print_ic_no"></td>
                    </tr>
                    <tr>
                        <td>No. Telefon </td>
                        <td id="print_phone_no"></td>
                    </tr>
                    <tr>
                        <td style="height: 40px;">Alamat</td>
                        <td id="print_address"></td>
                    </tr>
                    <tr>
                        <td>Emel</td>
                        <td id="print_email"></td>
                    </tr>
                    <tr>
                        <td style="height: 35px;">Punca Kerosakan/Kehilangan</td>
                        <td id="print_cause"></td>
                    </tr>
                </table>
            </div>

            <!-- SECTION C & D (WEB DISPLAY) -->
            <div class="row web-section-cd">
                <!-- SECTION C -->
                <div class="col-md-6 mb-3">
                    <div class="form-card h-100 mb-0">
                        <h4 class="section-title">
                            <i class="bi bi-file-earmark-check"></i>
                            (C) PERMOHONAN BAGI
                        </h4>
                        <p class="sub-label mb-3">Sila tandakan pada kotak yang berkaitan: <span class="required">*</span></p>

                        <div class="form-check mb-3">
                            <input class="form-check-input chk-app" type="checkbox" name="app_type[]" value="sijil_bm" id="sijil_bm">
                            <label class="form-check-label fw-medium" for="sijil_bm">
                                Salinan Sijil Diploma (Bahasa Melayu)
                            </label>
                        </div>

                        <div class="form-check mb-3">
                            <input class="form-check-input chk-app" type="checkbox" name="app_type[]" value="sijil_bi" id="sijil_bi">
                            <label class="form-check-label fw-medium" for="sijil_bi">
                                Salinan Sijil Diploma (Bahasa Inggeris)
                            </label>
                        </div>

                        <div class="form-check mb-3">
                            <input class="form-check-input chk-app" type="checkbox" name="app_type[]" value="transkrip_sahaja" id="transkrip_sahaja">
                            <label class="form-check-label fw-medium" for="transkrip_sahaja">
                                Salinan Transkrip Akademik sahaja
                            </label>
                        </div>

                        <input type="text" id="app_type_validator" style="opacity: 0; height: 0; width: 0; position: absolute;" required>
                    </div>
                </div>

                <!-- SECTION D -->
                <div class="col-md-6 mb-3">
                    <div class="form-card h-100 mb-0">
                        <h4 class="section-title">
                            <i class="bi bi-truck"></i>
                            (D) PENGHANTARAN
                        </h4>
                        <p class="sub-label mb-3">Sila tandakan kaedah penghantaran: <span class="required">*</span></p>

                        <div class="form-check mb-3">
                            <input class="form-check-input chk-del" type="radio" name="delivery_type" value="pos" id="pos" required>
                            <label class="form-check-label fw-medium" for="pos">
                                Secara Pos
                            </label>
                        </div>

                        <div class="form-check mb-3">
                            <input class="form-check-input chk-del" type="radio" name="delivery_type" value="ambil_sendiri" id="ambil_sendiri" required>
                            <label class="form-check-label fw-medium" for="ambil_sendiri">
                                Ambil Sendiri
                            </label>
                        </div>

                        <!-- Dynamic Address Input for Pos Option -->
                        <div id="postal_address_container" class="mt-3 d-none">
                            <label class="form-label">
                                Alamat Penghantaran Pos <span class="required">*</span>
                            </label>
                            <textarea class="form-control" name="postal_address" id="postal_address" rows="3" placeholder="Masukkan alamat lengkap untuk penghantaran pos"></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- PRINTABLE SECTION C & D -->
            <div class="print-only">
                <table class="print-table print-section-cd">
                    <tr>
                        <th style="width: 65%;">(C) PERMOHONAN BAGI</th>
                        <th style="width: 35%;">(D) PENGHANTARAN</th>
                    </tr>
                    <tr>
                        <td>
                            <div>C. Sila tandakan pada (✓) pada kotak yang berkaitan:</i></div>
                            <div style="margin-top: 5px; margin-left: 15px;">
                                <span id="print_chk_bm">[ &nbsp; ]</span> Salinan Sijil Diploma (Bahasa Melayu)<br>
                                <span id="print_chk_bi">[ &nbsp; ]</span> Salinan Sijil Diploma (Bahasa Inggeris)<br>
                                <span id="print_chk_trans">[ &nbsp; ]</span> Salinan Transkrip Akademik sahaja
                            </div>
                        </td>
                        <td style="vertical-align: top;">
                            <div style="margin-top: 15px; margin-left: 10px;">
                                <span id="print_chk_pos">[ &nbsp; ]</span> Secara Pos<br>
                                <div id="print_pos_address_display" style="font-size: 8.5pt; margin-left: 20px; font-style: italic;"></div>
                                <br>
                                <span id="print_chk_ambil">[ &nbsp; ]</span> Ambil Sendiri
                            </div>
                        </td>
                    </tr>
                </table>
            </div>

            <!-- SECTION E: SUPPORTING DOCUMENTS -->
            <div class="form-card mt-3">
                <div class="no-print">
                    <h4 class="section-title">
                        <i class="bi bi-paperclip"></i>
                        (E) DOKUMEN SOKONGAN
                    </h4>

                    <p class="text-muted mb-3">Sila muat naik dokumen yang diperlukan untuk melengkapkan permohonan.</p>

                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="dokumen_sokongan" required>
                        <label class="form-check-label fw-medium" for="dokumen_sokongan">
                            Laporan / aduan kehilangan yang telah disahkan oleh pihak yang berkepentingan seperti Ketua Kampung atau Pegawai Kerajaan Kumpulan A dan disahkan oleh Pengarah Institusi <span class="required">*</span>
                        </label>
                    </div>

                    <div class="mb-3 file-upload-wrapper">
                        <label class="form-label fw-semibold">Muat Naik Salinan Laporan / Aduan Kehilangan <span class="required">*</span></label>
                        <input type="file" class="form-control" name="support_document_file" accept=".pdf,.jpg,.jpeg,.png" required>
                        <small class="text-muted">Format yang dibenarkan: PDF, JPG, PNG</small>
                    </div>
                </div>

                <div class="print-only mb-2">
                    <div class="fw-bold">(E) DOKUMEN SOKONGAN</div>
                    <div style="margin-top: 3px;">
                        E. Sila tandakan pada (✓) pada kotak yang berkaitan:</i>
                        <div style="margin-top: 3px; margin-left: 15px;">
                            [ ✓ ] Laporan / aduan kehilangan yang telah disahkan oleh pihak yang berkepentingan seperti Ketua Kampung atau Pegawai Kerajaan Kumpulan A dan disahkan oleh Pengarah Institusi
                        </div>
                    </div>
                </div>

                <!-- SIGNATURE SECTION -->
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

                <div class="print-only text-end mt-2 sig-container-print">
                    <div style="display: inline-block; text-align: center;">
                        <img id="print_sig_img" src="" style="max-height: 50px; display: none;">
                        <div style="border-top: 1px solid #000; width: 200px; margin-top: 5px;"></div>
                        <div style="font-size: 9pt;">Tandatangan Pemohon</div>
                    </div>
                </div>
            </div>

            <!-- BUTTON SECTION -->
            <div class="d-flex justify-content-end gap-3 my-4 no-print">
                <button type="submit" class="btn btn-primary action-btn">
                    <i class="bi bi-send me-1"></i> Hantar
                </button>
            </div>
        </form>
    </div>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            // Delivery Option Event Listener
            const deliveryRadios = document.querySelectorAll('input[name="delivery_type"]');
            const postalContainer = document.getElementById('postal_address_container');
            const postalInput = document.getElementById('postal_address');

            deliveryRadios.forEach(radio => {
                radio.addEventListener('change', function () {
                    if (document.getElementById('pos').checked) {
                        postalContainer.classList.remove('d-none');
                        postalInput.setAttribute('required', 'required');
                    } else {
                        postalContainer.classList.add('d-none');
                        postalInput.removeAttribute('required');
                        postalInput.value = '';
                    }
                });
            });
 
            // Signature Pad Setup
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
                    printSigImg.src = dataUrl;
                    previewImg.style.display = 'block';
                    printSigImg.style.display = 'inline-block';
                    previewText.style.display = 'none';
                }
            });

            clearBtn.addEventListener('click', () => {
                signaturePad.clear();
                hiddenInput.value = '';
                previewImg.src = '';
                previewImg.style.display = 'none';
                previewText.style.display = 'block';
            });

            function resetPreview() {
                signatureInput.value = '';
                previewImg.src = '';
                printSigImg.src = '';
                previewImg.style.display = 'none';
                printSigImg.style.display = 'none';
                previewText.style.display = 'block';
            }

            // Print Sync Handler
            window.onbeforeprint = function() {
                document.getElementById('print_applicant_name').innerText = document.getElementById('web_applicant_name').value;
                document.getElementById('print_reg_no').innerText = document.getElementById('web_reg_no').value;
                document.getElementById('print_ic_no').innerText = document.getElementById('web_ic_no').value;
                document.getElementById('print_phone_no').innerText = document.getElementById('web_phone_no').value;
                document.getElementById('print_address').innerText = document.getElementById('web_address').value;
                document.getElementById('print_email').innerText = document.getElementById('web_email').value;
                document.getElementById('print_cause').innerText = document.getElementById('web_cause').value;

                document.getElementById('print_chk_bm').innerText = document.getElementById('sijil_bm').checked ? '[ ✓ ]' : '[ &nbsp; ]';
                document.getElementById('print_chk_bi').innerText = document.getElementById('sijil_bi').checked ? '[ ✓ ]' : '[ &nbsp; ]';
                document.getElementById('print_chk_trans').innerText = document.getElementById('transkrip_sahaja').checked ? '[ ✓ ]' : '[ &nbsp; ]';

                const isPos = document.getElementById('pos').checked;
                document.getElementById('print_chk_pos').innerText = isPos ? '[ ✓ ]' : '[ &nbsp; ]';
                document.getElementById('print_chk_ambil').innerText = document.getElementById('ambil_sendiri').checked ? '[ ✓ ]' : '[ &nbsp; ]';

                const printPosAddr = document.getElementById('print_pos_address_display');
                if (isPos && postalInput.value.trim() !== '') {
                    printPosAddr.innerText = 'Alamat: ' + postalInput.value;
                } else {
                    printPosAddr.innerText = '';
                }
            };

            const sectionCCheckboxes = document.querySelectorAll(".chk-app");
            const sectionCValidator = document.getElementById("app_type_validator");

            sectionCCheckboxes.forEach(function (checkbox) {
                checkbox.addEventListener("change", validateSectionC);
            });

            function validateSectionC() {
                // Semak samada sekurang-kurangnya satu checkbox dalam Section C ditanda
                const isChecked = Array.from(sectionCCheckboxes).some(cb => cb.checked);

                if (isChecked) {
                    sectionCValidator.value = "valid";
                } else {
                    sectionCValidator.value = ""; // Kosongkan nilai supaya 'required' menghalang submit
                }
            }
        });
    </script>
</body>
</html>
<?php include 'footer.php' ?>