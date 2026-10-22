<?php
    require_once 'config.php';

    if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== "admin") {
        header("Location: admin_login.php");
        exit();
    }

    $student_id   = $_SESSION['user_id'];
    $student_name = $_SESSION['full_name'];
    $role         = $_SESSION['role'];

    $stmt = $pdo->prepare("SELECT * FROM past_papers ORDER BY paper_id DESC");
    $stmt->execute();
    $past_papers = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $staf_id   = $_SESSION['user_id'];
    // Admin notifications
    try {
        $stmt_notif = $pdo->prepare("SELECT * FROM notifications WHERE user_id = :admin_id AND role = 'admin' ORDER BY created_at DESC LIMIT 5");
        $stmt_notif->execute([':admin_id' => $staf_id]);
        $admin_notifications = $stmt_notif->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $admin_notifications = [];
    }
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Manage Past Papers</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    </head>
    <body>
        <?php include 'admin_header.php' ?>
        <div class="container mt-5">
            <div class="p-3 hero-section rounded shadow-sm mb-4">
                <div class="d-flex justify-content-between align-items-center">
                    <h2>
                        <i class="bi bi-file-earmark-text"></i>
                        Manage Past Papers
                    </h2>

                    <a href="papers_add.php" class="btn btn-primary">
                        <i class="bi bi-plus-circle"></i>
                        Add New Paper
                    </a>
                </div>
            </div>

            <?php if (isset($_GET['status']) && $_GET['status'] === 'paper_added'): ?>
                    <div class="alert alert-success text-center small py-2" role="alert">
                        Past Papers Added Successfully!
                    </div>
            <?php endif; ?>

            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold text-secondary">Past Papers List</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3">Department</th>
                                    <th>Course Code</th>
                                    <th>Course Name</th>
                                    <th>Session</th>
                                    <th>Exam Type</th>
                                    <th class="text-center pe-3">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(!empty($past_papers)):  ?>
                                    <?php foreach ($past_papers as $row): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['department']); ?></td>
                                    <td><?php echo htmlspecialchars($row['course_code']); ?></td>
                                    <td><?php echo htmlspecialchars($row['course_name']); ?></td>
                                    <td><?php echo htmlspecialchars($row['session']); ?></td>
                                    <td><?php echo htmlspecialchars($row['exam_type']); ?></td>
                                    <td class="text-center pe-3">
                                        <a href="papers_edit.php?id=<?php echo $row['paper_id']; ?>" class="btn btn-warning btn-sm">
                                            <i class="bi bi-pencil"></i>            
                                                Edit
                                        </a>
                                        <a href="papers_delete.php?id=<?php echo $row['paper_id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Delete this paper?');">
                                            <i class="bi bi-trash"></i>
                                                Delete
                                        </a>
                                    </td>
                                </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6" class="text-center">No Past papers found.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
<br><?php include 'footer.php' ?>