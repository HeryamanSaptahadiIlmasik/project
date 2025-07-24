<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . ' - ' : ''; ?>Triak Coffee & Roaster</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <div class="nav-brand">
                <a href="index.php">
                    <i class="fas fa-coffee"></i>
                    <span>Triak Coffee</span>
                </a>
            </div>
            <div class="nav-menu">
                <a href="index.php" class="nav-link">Beranda</a>
                <a href="catalog.php" class="nav-link">Katalog Kopi</a>
                <?php if (isLoggedIn()): ?>
                    <?php if (isAdmin()): ?>
                        <a href="admin/dashboard.php" class="nav-link">Dashboard Admin</a>
                    <?php else: ?>
                        <a href="user/dashboard.php" class="nav-link">Dashboard Saya</a>
                    <?php endif; ?>
                    <a href="logout.php" class="nav-link logout">Keluar</a>
                <?php endif; ?>
            </div>
            <div class="nav-toggle">
                <i class="fas fa-bars"></i>
            </div>
        </div>
    </nav>