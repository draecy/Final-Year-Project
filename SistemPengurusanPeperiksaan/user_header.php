<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
        .navbar-psis { 
            background-color: #0d3b66;
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
            <a class="navbar-brand fw-bold" href="user_dashboard.php">SISTEM PENGURUSAN PEPERIKSAAN PSIS</a>
            <div class="navbar-nav ms-auto">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="notifDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-bell"></i> Notifikasi 
                        <?php if (!empty($user_notifications)): ?>
                            <span class="badge bg-danger rounded-pill"><?php echo count($user_notifications); ?></span>
                        <?php endif; ?>
                    </a>
                    
                    <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="notifDropdown" style="width: 320px; max-height: 400px; overflow-y: auto;">
                        <li><h6 class="dropdown-header">Notifikasi Terkini</h6></li>
                        <li><hr class="dropdown-divider"></li>

                        <?php if (!empty($user_notifications)): ?>
                            <?php foreach ($user_notifications as $notif): ?>
                                <li class="dropdown-item text-wrap">
                                    <strong class="d-block text-dark" style="font-size: 0.9rem;">
                                        <?php echo htmlspecialchars($notif['title']); ?>
                                    </strong>
                                    <span class="text-muted d-block" style="font-size: 0.85rem;">
                                        <?php echo htmlspecialchars($notif['message']); ?>
                                    </span>
                                    <small class="text-secondary" style="font-size: 0.75rem;">
                                        <?php echo date('d M Y, h:i A', strtotime($notif['created_at'])); ?>
                                    </small>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <li class="dropdown-item text-center text-muted">Tiada notifikasi baru</li>
                        <?php endif; ?>
                    </ul>
                </li>
                <a class="nav-link" href="user_dashboard.php">Dashboard</a>
                <a class="nav-link" href="papers_dashboard.php">Kertas Peperiksaan Lepas</a>
                <a class="btn btn-outline-light btn-sm ms-2 px-3 align-self-center" href="logout.php">Log keluar</a>
            </div>
        </div>
    </nav>
</body>
</html>