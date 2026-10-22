<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BORANG PERMOHONAN GANTIAN SIJIL DIPLOMA/TRANSKRIP AKADEMIK</title>
    <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.1.7/dist/signature_pad.umd.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body {
            background-color: #F5F1FA;
            font-family: 'Segoe UI', sans-serif;
        }

        /* Main Form Cards */
        .form-card {
            background-color: #FFFFFF;
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 4px 15px rgba(75, 31, 111, 0.12);
        }

        /* Page Title */
        h2 {
            color: #3D155F;
            font-weight: 700;
        }

        /* Subtitle */
        .text-muted {
            color: #6c757d !important;
        }

        /* Section Titles */
        .section-title {
            background-color: #4B1F6F;
            color: white;
            font-size: 20px;
            font-weight: 600;
            padding: 12px 18px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 25px;
        }

        /* Required Symbol */
        .required {
            color: #dc3545;
        }

        /* Labels */
        .form-label {
            font-weight: 500;
            color: #3D155F;
        }

        /* Input Fields */
        .form-control,
        .form-select {
            border-radius: 8px;
            border: 1px solid #D8C3EE;
            padding: 10px;
        }

        /* Input Focus */
        .form-control:focus,
        .form-select:focus {
            border-color: #7B3FB3;
            box-shadow: 0 0 0 0.2rem rgba(91,42,134,0.20);
        }

        /* Dropdown */
        .form-select:hover {
            border-color: #7B3FB3;
        }

        /* Fee Box */
        .fee-box {
            background-color: #EEE3FF;
            border-left: 5px solid #5B2A86;
            color: #3D155F;
            padding: 15px;
            border-radius: 10px;
            margin-top: 15px;
        }

        /* Declaration Box */
        .alert-light {
            background-color: #FAF7FF;
            border: 1px solid #D8C3EE;
        }

        /* File Upload */
        input[type="file"] {
            background-color: #FAF7FF;
            border: 1px dashed #7B3FB3;
            padding: 12px;
        }

        /* Upload Hover */
        input[type="file"]:hover {
            background-color: #F1E7FF;
        }

        /* Optional Badge */
        .badge.bg-secondary {
            background-color: #8064A2 !important;
        }

        /* Buttons */
        .btn-primary {
            background-color: #5B2A86;
            border-color: #5B2A86;
            border-radius: 8px;
            padding: 10px 25px;
        }

        .btn-primary:hover {
            background-color: #3D155F;
            border-color: #3D155F;
        }

        /* White button */
        .btn-secondary {
            border-radius: 8px;
        }

        /* Signature Preview */
        #preview {
            max-height: 120px;
            max-width: 250px;
        }

        .action-btn {
            width: 180px;
            height: 45px;
            font-weight: 500;
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

        .success-icon {
            font-size: 70px;
            color: #198754;
        }

        .application-number-box {
            background: #f8f5ff;
            border: 1px solid #ddd3f5;
            border-radius: 10px;
            padding: 15px;
        }

        .application-number-box h4 {
            color: #4b2c82;
            letter-spacing: 1px;
        }
    </style>
</head>
<body>
    <?php include 'user_header.php' ?>
    <div class="container my-5">
        <div class="d-flex justify-content-between align-items-center mb-3 no-print">
            <a href="user_dashboard.php" type="submit" class="btn btn-primary" name="back">Back</a>
            <span class="badge bg-secondary p-2">Lampiran</span>
        </div>
        <form id="applicationForm" action="process_gantian.php" method="POST" enctype="multipart/form-data">
            <div class="form-card mb-4">
                <div class="header-logos">
                    <img src="img/psis.png" alt="Politeknik Malaysia" class="img-fluid">
                    <img src="img/kpt.png" alt="Kementerian Pengajian Tinggi" class="img-fluid">
                </div>
            
                    <hr class="text-muted">
                    <h2 class="fw-bold text-center mt-2">
                        BORANG PERMOHONAN GANTIAN SIJIL DIPLOMA / TRANSKRIP AKADEMIK
                    </h2>
                </div>

            <div class="form-card" id="applicationTypeSection">
                <h4 class="section-title">
                    <i class="bi bi-file-earmark-text"></i>
                    Jenis Permohonan
                </h4>
                <div class="mb-3">
                    <p class="text-muted">Sila pilih sekurang-kurangnya 1<span class="required">*</span></p>
                    <div class="form-check mb-2">
                        <input class="form-check-input app-checkbox" type="checkbox" name="application_type[]" value="Sijil Diploma Versi BM" data-fee="50" id="bm">
                        <label class="form-check-label" for="bm">
                            Sijil Diploma Versi BM (RM50)
                        </label>
                    </div>

                    <div class="form-check mb-2">
                        <input class="form-check-input app-checkbox" type="checkbox" name="application_type[]" value="Sijil Diploma Versi BI" data-fee="50" id="bi">
                        <label class="form-check-label" for="bi">
                            Sijil Diploma Versi BI (RM50)
                        </label>
                    </div>

                    <div class="form-check mb-2">
                        <input class="form-check-input app-checkbox" type="checkbox" name="application_type[]" value="Transkrip Akademik" data-fee="10" id="transcript">
                        <label class="form-check-label" for="transcript">
                            Transkrip Akademik (RM10)
                        </label>
                    </div>
                </div>

                <div class="fee-box" id="feeBox">
                    <strong>Bayaran Permohonan :</strong>
                    RM <span id="feeAmount"></span>
                </div>
            </div>

            <div class="form-card">
                <h4 class="section-title">
                    <i class="bi bi-person-vcard"></i>
                    Maklumat Lulusan
                </h4>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">
                            Nama Penuh <span class="required">*</span>
                        </label>
                        <input type="text" class="form-control" name="student_name" placeholder="Cth: NUR ALIAH BINTI MAHMUD" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">
                            No. Kad Pengenalan <span class="required">*</span>
                        </label>
                        <input type="text" class="form-control" name ="ic_number" placeholder="Cth: 010101-01-1234" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">
                            No. Pendaftaran <span class="required">*</span>
                        </label>
                        <input type="text" class="form-control" name="student_id" placeholder="Cth: 17DIT22F1072" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">
                            No. Telefon <span class="required">*</span>
                        </label>
                        <input type="text" class="form-control" name = "phone" placeholder="01X-XXXXXXXX" required>
                    </div>
                </div>
 
                
                <div class="mb-3">
                    <label class="form-label">
                        Alamat <span class="required">*</span>
                    </label>
                    <textarea class="form-control" rows="3" name="address" placeholder="Sila berikan alamat penuh" required></textarea>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">
                            Program <span class="required">*</span>
                        </label>
                        <select class="form-select" name = "programme" required>
                            <option selected disabled hidden>-- Pilih Program --</option>
                            <option>DDT-DIPLOMA TEKNOLOGI MAKLUMAT (TEKNOLOGI DIGITAL)</option>
                            <option>DIT-DIPLOMA TEKNOLOGI MAKLUMAT</option>
                            <option>DNS-DIPLOMA TEKNOLOGI MAKLUMAT (RANGKAIAN)</option>
                            <option>DIP-DIPLOMA TEKNOLOGI MAKLUMAT (PENGATURCARAAN)</option>
                            <option>SIT-SIJIL TEKNOLOGI MAKLUMAT</option>
                            <option>DHF-DIPLOMA PERKHIDMATAN MAKANAN AMALAN HALAL</option>
                            <option>DUP-DIPLOMA PENGURUSAN PELANCONGAN</option>
                            <option>DHK-DIPLOMA PENGURUSAN HOTEL DAN KATERING</option>
                            <option>SUP-SIJIL PENGURUSAN PELANCONGAN</option>
                            <option>SHK-SIJIL PENGURUSAN HOTEL DAN KATERING</option>
                            <option>DIF-DIPLOMA KEWANGAN ISLAM</option>
                            <option>DIB-DIPLOMA KEWANGAN DAN PERBANKAN ISLAM</option>
                            <option>DSK-DIPLOMA SAINS KESETIAUSAHAAN</option>
                            <option>SMD-SIJIL PEMPROSESAN DATA</option>
                            <option>DEP-DIPLOMA KEJURUTERAAN ELEKTRONIK (TELEKOMUNIKASI)</option>
                            <option>DEP-DIPLOMA KEJURUTERAAN ELEKTRONIK (KOMUNIKASI)</option>
                            <option>DTK-DIPLOMA KEJURUTERAAN ELEKTRONIK (KOMPUTER)</option>
                            <option>DKE-DIPLOMA KEJURUTERAAN ELEKTRONIK</option>
                            <option>SKE-SIJIL KEJURUTERAAN ELEKTRIK & ELEKTRONIK</option>
                            <option>DSB-DIPLOMA SENI BINA</option>
                            <option>DKA-DIPLOMA KEJURUTERAAN AWAM</option>
                            <option>DAS-DIPLOMA KEJURUTERAAN ALAM SEKITAR</option>
                            <option>SKA-SIJIL KEJURUTERAAN AWAM</option>
                            <option>SSB-SIJIL SENI BINA</option>
                        </select>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">
                            Tahun Pengajian
                        </label>
                        <select class="form-select" name = "study_year">
                            <option selected disabled hidden>-- Pilih Tahun --</option>
                            <option>2005</option>
                            <option>2006</option>
                            <option>2007</option>
                            <option>2008</option>
                            <option>2009</option>
                            <option>2010</option>
                            <option>2011</option>
                            <option>2012</option>
                            <option>2013</option>
                            <option>2014</option>
                            <option>2015</option>
                            <option>2016</option>
                            <option>2017</option>
                            <option>2018</option>
                            <option>2019</option>
                            <option>2020</option>
                            <option>2021</option>
                            <option>2022</option>
                            <option>2023</option>
                            <option>2024</option>
                            <option>2025</option>
                            <option>2026</option>
                        </select>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">
                            Sesi Tamat Pengajian
                        </label>
                        <input type="text" class="form-control" name="graduation_session" list="sessionList" placeholder="Cth: Sesi II 2024/2025">
                        <datalist id="sessionList">
                            <option value="Sesi I 2025/2026">
                            <option value="Sesi II 2025/2026">
                            <option value="Sesi I 2024/2025">
                            <option value="Sesi II 2024/2025">
                            <option value="Sesi I 2023/2024">
                            <option value="Sesi II 2023/2024">
                        </datalist>
                    </div>
                </div>
            </div>

            <!-- SECTION 4 -->
            <div class="form-card">
                <h4 class="section-title">
                    <i class="bi bi-truck"></i>
                    Kaedah Penerimaan Sijil
                </h4>

                <label class="form-label">
                    Kaedah Pengambilan <span class="required">*</span>
                </label>

                <div class="form-check mb-2">
                    <input class="form-check-input" type="radio" name="collection_method" id="collect" value="Ambil Sendiri">
                    <label class="form-check-label" for="collect">
                        Ambil Sendiri di Unit Peperiksaan
                    </label>
                </div>

                <div class="form-check mb-3">
                    <input class="form-check-input" type="radio" name="collection_method" id="post" value="Pos">
                    <label class="form-check-label" for="post">
                        Pos ke Alamat
                    </label>
                </div>

                <!-- Postal Address -->
                <div id="postalAddress" style="display:none;">
                    <label class="form-label">
                        Alamat Penghantaran <span class="required">*</span>
                    </label>
                    <textarea class="form-control" name="postal_address" id="postalAddressInput" rows="4" placeholder="Masukkan alamat lengkap untuk penghantaran sijil"></textarea>
                </div>
            </div>

            <!-- SECTION 2 -->
            <div class="form-card">
                <h4 class="section-title">
                    <i class="bi bi-pencil-square"></i>
                    Perakuan Pemohon
                </h4>

                <div class="alert alert-light border">
                    <p class="mb-2">
                        <strong>Sila baca perakuan berikut:</strong>
                    </p>

                    <ol class="mb-3">
                        <li>Saya mengaku dan mengesahkan semua maklumat di atas adalah benar dan tepat.</li>
                        <li>Saya bersetuju sekiranya terdapat maklumat yang dinyatakan tidak benar atau palsu, pihak Politeknik Sultan Idris Shah berhak menolak permohonan ini.</li>
                        <li>Saya bersetuju permohonan diproses dalam masa 14 hari bekerja.</li>
                    </ol>

                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="agreeDeclaration" required>
                        <label class="form-check-label" for="agreeDeclaration">
                            Saya telah membaca dan bersetuju dengan semua perakuan di atas. <span class="required">*</span>
                        </label>
                    </div>
                </div>

                <div class="row mt-4">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">
                            Nama Penuh <span class="required">*</span>
                        </label>
                        <input type="text" class="form-control" name="declaration_name" placeholder="Nama Penuh" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">
                            No. Kad Pengenalan <span class="required">*</span>
                        </label>
                        <input type="text" class="form-control" name="declaration_ic" placeholder="Cth: 010101-01-1234" required>
                    </div>
                </div>
                
                <div class="row">
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
                        <small class="text-muted">
                            Sila tandatangan di dalam kotak di atas.
                        </small>
                    </div>

                    <!-- Signature Preview -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">
                            Hasil Tandatangan
                        </label>
                        <div class="border rounded p-3 bg-light text-center d-flex align-items-center justify-content-center"
                            style="min-height: 195px;">

                            <img id="preview" src="" alt="Signature Preview" style="display:none; max-height:120px;" class="img-fluid">
                            <p id="previewText" class="text-muted mb-0">
                                Tiada tandatangan dihasilkan.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="row mt-3 sig-container-web">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">
                            Tarikh Permohonan
                        </label>
                        <input type="date" class="form-control" name="application_date" value="<?php echo date('Y-m-d'); ?>" readonly>
                    </div>
                </div>
            </div>

            <div class="form-card">
                <h4 class="section-title">
                    <i class="bi bi-paperclip"></i>
                    Dokumen Sokongan
                </h4>

                <p class="text-muted">
                    Sila muat naik dokumen yang diperlukan untuk melengkapkan permohonan.<br>
                    Format yang dibenarkan: PDF, JPG, PNG
                </p>

                <div class="mb-4">
                    <label class="form-label fw-semibold">
                        1. Salinan Laporan Polis <span class="required">*</span>
                    </label>
                    <input type="file" class="form-control" name="police_report" accept=".pdf,.jpg,.jpeg,.png" required>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-semibold">
                        2. Salinan Kad Pengenalan Lulusan <span class="required">*</span>
                    </label>

                    <input type="file" class="form-control" name="ic_copy" accept=".pdf,.jpg,.jpeg,.png" required>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-semibold">
                        3. Salinan Resit Bayaran iPayment <span class="required">*</span>
                    </label>

                    <input type="file" class="form-control" name="payment_receipt" accept=".pdf,.jpg,.jpeg,.png" required>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-semibold">
                        4. Salinan Sijil Diploma Asal (Jika Ada)
                    </label>
                    <input type="file" class="form-control" name="diploma_copy" accept=".pdf,.jpg,.jpeg,.png">
                </div>

                <div class="mb-4">
                    <label class="form-label fw-semibold">
                        5. Salinan Keputusan Peperiksaan (Jika Ada)
                    </label>
                    <input type="file" class="form-control" name="exam_result" accept=".pdf,.jpg,.jpeg,.png">
                </div>

                <div class="mb-4">
                    <label class="form-label fw-semibold">
                        6. Salinan Transkrip Akademik (Jika Ada)
                    </label>

                    <input type="file" class="form-control" name="transcript_copy" accept=".pdf,.jpg,.jpeg,.png">
                </div>
            </div>

            <!-- BUTTON SECTION -->
            <div class="d-flex justify-content-end gap-3 mb-5">
                <button type="submit" class="btn btn-primary action-btn" name="submit">
                    <i class="bi bi-send"></i>
                    Hantar
                </button>
            </div>
        </form>
    </div>
</body>
    <script>
        document.addEventListener("DOMContentLoaded", function () {

            const canvas = document.getElementById('signature-pad');
            const signatureInput = document.getElementById('signature');
            const previewImg = document.getElementById('preview');
            const printSigImg = document.getElementById('print_sig_img');
            const previewText = document.getElementById('previewText');

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

            function resetPreview() {
                signatureInput.value = '';
                previewImg.src = '';
                if (printSigImg) printSigImg.src = '';
                previewImg.style.display = 'none';
                if (printSigImg) printSigImg.style.display = 'none';
                previewText.style.display = 'block';
            }

            // Application Fee
            const checkboxes = document.querySelectorAll(".app-checkbox");
            const feeBox = document.getElementById("feeBox");
            const feeAmount = document.getElementById("feeAmount");

            checkboxes.forEach(function (checkbox) {
                checkbox.addEventListener("change", calculateFee);
            });

            function calculateFee() {
                let total = 0;
                checkboxes.forEach(function (checkbox) {
                    if (checkbox.checked) {
                        total += parseInt(checkbox.dataset.fee);
                    }
                });

                if (total > 0) {
                    feeBox.style.display = "block";
                    feeAmount.textContent = total;
                } else {
                    feeBox.style.display = "none";
                    feeAmount.textContent = "0";
                }
            }

            // Postal Address
            const collect = document.getElementById("collect");
            const post = document.getElementById("post");
            const postalAddress = document.getElementById("postalAddress");
            const postalAddressInput = document.getElementById("postalAddressInput");

            function toggleAddress() {
                if (!postalAddressInput) return;

                if (post && post.checked) {
                    if (postalAddress) postalAddress.style.display = "block";
                    postalAddressInput.setAttribute("required", "required");
                } else {
                    if (postalAddress) postalAddress.style.display = "none";
                    postalAddressInput.removeAttribute("required");
                    postalAddressInput.value = "";
                }
            }

            if (collect) collect.addEventListener("change", toggleAddress);
            if (post) post.addEventListener("change", toggleAddress);
            toggleAddress();

            const form = document.getElementById('applicationForm');

            if (form) {
                // 1. Semakan semasa butang submit ditekan
                form.addEventListener('submit', function(e) {
                    const checkedBoxes = document.querySelectorAll('input[name="application_type[]"]:checked');

                    // Semak Jenis Permohonan
                    if (checkedBoxes.length === 0) {
        e.preventDefault(); // Hentikan submit
        alert("Sila pilih sekurang-kurangnya satu Jenis Permohonan.");
        
        const typeSection = document.getElementById('applicationTypeSection');
        if (typeSection) {
            typeSection.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
        return false;
    }

                    const typeValidator = document.getElementById('type_validator');
    if (typeValidator) typeValidator.value = "valid";

                    // Semak Tandatangan
                    if (signaturePad.isEmpty()) {
                        e.preventDefault();
                        alert("Sila turunkan tandatangan pemohon sebelum menghantar borang.");
                        canvas.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        return false;
                    }

                    console.log("Form bersedia untuk dihantar!");
                });
            }
        });
    </script>
</body>
</html>
<?php include 'footer.php' ?>