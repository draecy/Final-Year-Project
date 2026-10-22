<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BORANG PERMOHONAN PINDAHAN KREDIT/PENGECUALIAN KURSUS</title>
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
        .form-control, .form-select {
            border-radius: 8px;
            border: 1px solid #D8C3EE;
            padding: 10px;
        }

        /* Input Focus */
        .form-control:focus, .form-select:focus {
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

        .approval-area {
            border: 1px dashed #bbb;
            border-radius: 10px;
            padding: 30px;
            min-height: 250px;
        }

        .signature-space {
            min-height: 120px;
        }

        .signature-line {
            border-bottom: 1px solid #333;
            width: 250px;
        }

        .print-line {
            border-bottom: 1px solid #333;
            height: 35px;
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

        <div class="form-card mb-4">
            <div class="header-logos">
                <img src="img/psis.png" alt="Politeknik Malaysia" class="img-fluid">
                <img src="img/kpt.png" alt="Kementerian Pengajian Tinggi" class="img-fluid">
            </div>
            <hr class="text-muted">
            <h2 class="fw-bold text-center mt-2">
                BORANG PERMOHONAN PINDAHAN KREDIT / PENGECUALIAN KURSUS
            </h2>
        </div>

        <form action="process_ctce.php" method="POST" enctype="multipart/form-data">

            <div class="form-card">

                <!-- SECTION 1: MAKLUMAT PELAJAR -->
                <h4 class="section-title">
                    <i class="bi bi-person"></i>
                    1. Maklumat Pelajar
                </h4>
                    
                <!-- Nama Pelajar -->
                <div class="mb-3">
                    <label class="form-label">
                        Nama Penuh Pelajar <span class="required">*</span>
                    </label>

                    <input type="text" name="student_name" class="form-control" placeholder="Masukkan nama penuh" required>
                </div>

                <!-- No. Kad Pengenalan -->
                <div class="mb-3">
                    <label class="form-label">
                        No. Kad Pengenalan <span class="required">*</span>
                    </label>

                    <input type="text" name="ic_number" class="form-control" placeholder="Contoh: 010101-01-0101" required>
                </div>

                <!-- No. Pendaftaran -->
                <div class="mb-3">
                    <label class="form-label">
                        No. Pendaftaran <span class="required">*</span>
                    </label>

                    <input type="text" name="student_id" class="form-control" placeholder="Masukkan no. pendaftaran" required>
                </div>
                
                <!-- Program & Kelas -->
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">
                            Program <span class="required">*</span>
                        </label>

                        <input type="text" name="programme" class="form-control" placeholder="Masukkan program" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">
                            Kelas <span class="required">*</span>
                        </label>

                        <input type="text" name="class" class="form-control" placeholder="Masukkan kelas" required>
                    </div>
                </div>

                <!-- No. Telefon -->
                <div class="mb-3">
                    <label class="form-label">
                        No. Telefon <span class="required">*</span>
                    </label>

                    <input type="tel" name="phone" class="form-control" placeholder="Contoh: 012-3456789" required>
                </div>
            </div>

            <!-- SECTION 2: SENARAI DOKUMEN YANG PERLU DIUPLOAD -->
            <div class="form-card">

                <h4 class="section-title">
                    <i class="bi bi-file-earmark-arrow-up"></i>
                    2. Dokumen Sokongan (Yang Disahkan)
                </h4>

                <!-- Transkrip yang Disahkan -->
                <div class="document-upload mb-3">
                    <label class="form-label" for="documentTranscript">
                        Transkrip yang Disahkan <span class="required">*</span>
                    </label>
                    
                    <input type="file" name="document_transcript" id="documentTranscript" class="form-control" accept=".pdf,.jpg,.jpeg,.png" required>
                </div>

                <!-- Sijil Kursus yang Disahkan -->
                <div class="document-upload mb-3">
                    <label class="form-label" for="documentCertificate">
                        Sukatan Kursus yang Disahkan <span class="required">*</span>
                    </label>

                    <input type="file" name="document_certificate" id="documentCertificate" class="form-control" accept=".pdf,.jpg,.jpeg,.png" required>
                </div>

                <!-- Disahkan oleh Pegawai Akademik -->
                <div class="document-upload mb-3">
                    <label class="form-label" for="documentOfficer">
                        Dokumen Pensetaraan (Jika Berkaitan)
                    </label>

                    <input type="file" name="document_officer" id="documentOfficer" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
                </div>
            </div>

            <!-- SECTION 3: SENARAI KURSUS -->
            <div class="form-card">

                <h4 class="section-title">
                    <i class="bi bi-book"></i>
                    3. Senarai Kursus
                </h4>

                <!-- KURSUS YANG DIPOHON CT/CE -->
                <h5 class="fw-bold mt-3 mb-3">
                    A. Kursus Yang Dipohon CT/CE
                </h5>

                <div class="table-responsive">
                    <table class="table table-bordered align-middle" id="ctceCourseTable">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 8%;">Bil.</th>
                                <th>Kod Kursus</th>
                                <th>Nama Kursus</th>
                                <th>Jam Kredit</th>
                                <th style="width: 8%;">Tindakan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="text-center">1</td>
                                <td>
                                    <input type="text" name="ctce_course_code[]" class="form-control">
                                </td>
                                <td>
                                    <input type="text" name="ctce_course_name[]" class="form-control">
                                </td>
                                <td>
                                    <input type="number" name="ctce_credit_hour[]" class="form-control" min="0" step="0.5">
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-sm btn-outline-danger remove-course">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <button type="button" class="btn btn-outline-primary mb-4" id="addCtceCourse">
                    <i class="bi bi-plus-circle"></i>
                    Tambah Kursus
                </button>

                <hr class="my-4">

                <!-- KURSUS TERDAHULU YANG SETARA -->
                <h5 class="fw-bold mt-3 mb-3">
                    B. Kursus Terdahulu Yang Setara
                </h5>

                <div class="table-responsive">
                    <table class="table table-bordered align-middle" id="equivalentCourseTable">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 8%;">Bil.</th>
                                <th>Kod Kursus</th>
                                <th>Nama Kursus</th>
                                <th>Jam Kredit</th>
                                <th>Gred</th>
                                <th style="width: 8%;">Tindakan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="text-center">1</td>
                                <td>
                                    <input type="text" name="equivalent_course_code[]" class="form-control">
                                </td>
                                <td>
                                    <input type="text" name="equivalent_course_name[]" class="form-control">
                                </td>
                                <td>
                                    <input type="number" name="equivalent_credit_hour[]" class="form-control" min="0" step="0.5">
                                    </td>
                                <td>
                                    <input type="text" name="equivalent_grade[]" class="form-control">
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-sm btn-outline-danger remove-course">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <button type="button" class="btn btn-outline-primary" id="addEquivalentCourse">
                    <i class="bi bi-plus-circle"></i>
                    Tambah Kursus
                </button>
            </div>

            <!-- SECTION 4: PERAKUAN PEMOHON -->
            <div class="form-card">
                <h4 class="section-title">
                    <i class="bi bi-check2-square"></i>
                    4. Perakuan Pemohon
                </h4>

                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" name="declaration_1" id="declaration1" required>
                    <label class="form-check-label" for="declaration1">
                        Saya mengaku bahawa keterangan yang diberi dalam borang ini adalah betul dan benar. <span class="required">*</span>
                    </label>
                </div>

                <!-- SIGNATURE -->
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

                <!-- DATE -->
                <div class="mt-4">
                    <label class="form-label">
                        Tarikh
                        <span class="required">*</span>
                    </label>
                    <input type="date" class="form-control" name="application_date" value="<?php echo date('Y-m-d'); ?>" readonly>
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
</html>

<script>
// Add CT/CE Course

document.getElementById("addCtceCourse").addEventListener("click", function () {

    const tableBody = document.querySelector("#ctceCourseTable tbody");
    const rowCount = tableBody.rows.length + 1;
    const row = document.createElement("tr");

    row.innerHTML = `
        <td class="text-center">
            ${rowCount}
        </td>

        <td>
            <input type="text" name="ctce_course_code[]" class="form-control">
        </td>

        <td>
            <input type="text" name="ctce_course_name[]" class="form-control">
        </td>

        <td>
            <input type="number" name="ctce_credit_hour[]" class="form-control" min="0" step="0.5">
        </td>

        <td class="text-center">
            <button type="button" class="btn btn-sm btn-outline-danger remove-course">
                <i class="bi bi-trash"></i>
            </button>
        </td>
    `;

    tableBody.appendChild(row);
});

// Add Equivalent Course
document.getElementById("addEquivalentCourse").addEventListener("click", function () {

    const tableBody = document.querySelector("#equivalentCourseTable tbody");
    const rowCount = tableBody.rows.length + 1;
    const row = document.createElement("tr");

    row.innerHTML = `
        <td class="text-center">
            ${rowCount}
        </td>

        <td>
            <input type="text" name="equivalent_course_code[]" class="form-control">
        </td>

        <td>
            <input type="text" name="equivalent_course_name[]" class="form-control">
        </td>

        <td>
            <input type="number" name="equivalent_credit_hour[]" class="form-control" min="0" step="0.5">
        </td>

        <td>
            <input type="text" name="equivalent_grade[]" class="form-control">
        </td>

        <td class="text-center">
            <button type="button" class="btn btn-sm btn-outline-danger remove-course">
                <i class="bi bi-trash"></i>
            </button>
        </td>
    `;

    tableBody.appendChild(row);
});

// Remove Course
document.addEventListener("click", function (event) {

    if (event.target.closest(".remove-course")) {

        const row = event.target.closest("tr");
        const tableBody = row.parentElement;

        if (tableBody.rows.length > 1) {

            row.remove();

            Array.from(tableBody.rows).forEach(function (row, index) {
                row.cells[0].textContent = index + 1;
            });
        }
    }
});

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
<?php include 'footer.php' ?>