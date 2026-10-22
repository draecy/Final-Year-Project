<?php
    require_once 'config.php';

    if (!isset($_SESSION['user_id']) || !isset($_SESSION['email'])) {
        header("Location: index.php");
        exit();
    }

    $query = "SELECT * FROM past_papers WHERE 1";
    $params = [];
    $search = isset($_GET['search']) ? trim($_GET['search']) : "";
    $department = isset($_GET['department']) ? trim($_GET['department']) : "";
    $session = isset($_GET['session']) ? trim($_GET['session']) : "";

    if (!empty($search)) {
        $query .= " AND (course_code LIKE :search_code OR course_name LIKE :search_name)";
        $params['search_code'] = "%".$search."%";
        $params['search_name'] = "%".$search."%";
    }

    if (!empty($department)) {
        $query .= " AND department = :department";
        $params['department'] = $department;
    }

    if (!empty($session)) {
        $query .= " AND session = :session";
        $params['session'] = $session;
    }

    try {
        $stmt = $pdo->prepare($query);
        $stmt->execute($params);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        die("Database query error: ".$e->getMessage());
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Past Papers Repository</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .page-header {
            background-color: #0d6efd;
            color: white;
            padding: 30px;
            border-radius: 15px;
            margin-bottom: 30px;
        }
        .filter-box {
            background: white;
            padding: 20px;
            border-radius: 15px;
            height: fit-content;
        }
        .paper-card {
            background: white;
            border-radius: 15px;
            padding: 20px;
            transition: 0.3s;
            height: 100%;
        }
        .paper-card:hover {
            transform: translateY(-5px);
            box-shadow: 0px 5px 15px rgba(0,0,0,0.1);
        }
        .pdf-icon {
            font-size: 40px;
            color: #dc3545;
        }
        .badge-session {
            background-color: #71a5ee;
            color: #0d6efd;
        }
    </style>
</head>
<body>
    <?php include 'user_header.php' ?>
    <div class="container mt-5">
        <div class="page-header">
            <h2>
                <i class="bi bi-file-earmark-text"></i>
                Past Papers Repository
            </h2>
            <p class="mb-0">
                Access previous examination papers easily in one place.
            </p>
        </div>
        <form>
            <div class="row">
                <div class="col-md-3">
                    <div class="filter-box">
                        <h5>
                            <i class="bi bi-funnel"></i>
                            Filter
                        </h5>
                        <hr>
                        <label class="form-label">Search</label>

                        <input type="text" name="search" class="form-control mb-3" placeholder="Course code or name" value="<?php echo $search; ?>" >

                        <label class="form-label">Department</label>
                        <select name="department" class="form-select mb-3">
                            <option value="">All Department</option>
                            <option value="JTMK"
                                <?php if($programme=="JTMK") echo "selected"; ?>>
                                    JTMK
                            </option>
                            <option value="JP" 
                                <?php if($programme=="JP") echo "selected"; ?>>
                                    JP
                            </option>
                            <option value="JPH"
                                <?php if($programme=="JPH") echo "selected"; ?>>
                                    JPH
                            </option>
                            <option value="JKA"
                                <?php if($programme=="JKA") echo "selected"; ?>>
                                    JKA
                            </option>
                            <option value="JKE"
                                <?php if($programme=="JKE") echo "selected"; ?>>
                                    JKE
                            </option>
                            <option value="JMSK"
                                <?php if($programme=="JMSK") echo "selected"; ?>>
                                    JMSK
                            </option>
                        </select>

                        <label class="form-label">
                            Academic Session
                        </label>
                        <select name="session" class="form-select">
                            <option value="">
                                All Sessions
                            </option>
                            <option value="Sesi I 2025/2026"
                            <?php if($session=="Sesi I 2025/2026") echo "selected"; ?>>
                                Sesi I 2025/2026
                            </option>
                            <option value="Sesi II 2025/2026"
                            <?php if($session=="Sesi II 2025/2026") echo "selected"; ?>>
                                Sesi II 2025/2026
                            </option>
                            <option value="Sesi I 2024/2025"
                            <?php if($session=="Sesi I 2024/2025") echo "selected"; ?>>
                                Sesi I 2024/2025
                            </option>
                            <option value="Sesi II 2024/2025"
                            <?php if($session=="Sesi II 2024/2025") echo "selected"; ?>>
                                Sesi II 2024/2025
                            </option>
                        </select>

                        <button class="btn btn-primary w-100 mt-3">
                            <i class="bi bi-search"></i>
                            Search
                        </button>
                    </div>
                </div>
        </form>
        <div class="col-md-9">
            <div class="row g-4">
                <?php if (!empty($results)): ?>
                    <?php foreach ($results as $row): ?>
                        <?php $clean_download_name = preg_replace('/^\d+_/', '', $row['pdf_file']); ?>
                        <div class="col-md-6">
                            <div class="paper-card">
                                <div class="text-center">
                                    <i class="bi bi-file-earmark-pdf pdf-icon"></i>
                                </div>

                                <h5 class="mt-3">
                                    <?php echo htmlspecialchars($row['course_name']); ?>
                                </h5>

                                <p class="mb-1">
                                    <strong>Course Code:</strong>
                                    <?php echo htmlspecialchars($row['course_code']); ?>
                                </p>

                                <p class="mb-1">
                                    <strong>Department:</strong>
                                    <?php echo htmlspecialchars($row['department']); ?>
                                </p>

                                <span class="badge badge-session">
                                    <?php echo htmlspecialchars($row['session']); ?>
                                </span>

                                <div class="mt-3">
                                    <a href="uploads/papers/<?php echo rawurlencode($row['pdf_file']); ?>" class="btn btn-primary w-100" target="_blank" download="<?php echo htmlspecialchars($clean_download_name); ?>">
                                        <i class="bi bi-download"></i>
                                        Download PDF
                                    </a>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="alert alert-danger">No past papers match your search criteria.</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>
<?php include 'footer.php' ?>