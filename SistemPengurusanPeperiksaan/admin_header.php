<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
        .navbar-psis { 
            background-color: #420d66;
        }
        .hero-section {
            background-color: #f8f9fa; 
            border-left: 5px solid #f4d35e; 
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark navbar-psis shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold" href="admin_dashboard.php">DASHBOARD ADMIN SPP PSIS</a>
            <div class="navbar-nav ms-auto flex-row align-items-center">
                <li class="nav-item dropdown me-3">
                    <a class="nav-link dropdown-toggle" href="#" id="notifDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-bell"></i> Notifikasi 
                        <?php if (!empty($admin_notifications)): ?>
                            <span class="badge bg-danger rounded-pill"><?= count($admin_notifications) ?></span>
                        <?php endif; ?>
                    </a>
                    
                    <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="notifDropdown" style="width: 320px; max-height: 400px; overflow-y: auto;">
                        <li><h6 class="dropdown-header">Notifikasi Terkini</h6></li>
                        <li><hr class="dropdown-divider"></li>

                        <?php if (!empty($admin_notifications)): ?>
                            <?php foreach ($admin_notifications as $notif): ?>
                                <li class="dropdown-item text-wrap">
                                    <strong class="d-block text-dark" style="font-size: 0.9rem;">
                                        <?= htmlspecialchars($notif['title']) ?>
                                    </strong>
                                    <span class="text-muted d-block" style="font-size: 0.85rem;">
                                        <?= htmlspecialchars($notif['message']) ?>
                                    </span>
                                    <small class="text-secondary" style="font-size: 0.75rem;">
                                        <?= date('d M Y, h:i A', strtotime($notif['created_at'])) ?>
                                    </small>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <li class="dropdown-item text-center text-muted">Tiada notifikasi baru</li>
                        <?php endif; ?>
                    </ul>
                </li>
                <a class="nav-link active me-2" href="admin_dashboard.php">Dashboard</a>
                <a class="nav-link me-2" href="papers_manage.php">Kertas Peperiksaan Lepas</a>
                <a class="btn btn-outline-light btn-sm px-3 ms-2" href="logout.php">Log keluar</a>
            </div>
        </div>
    </nav>
</body>
</html>