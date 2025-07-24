<?php
require_once '../config/init.php';

if (!isLoggedIn() || !isAdmin()) {
    redirect('../login.php');
}

if (!function_exists('formatRupiahAdmin')) {
    function formatRupiahAdmin($angka) {
        return 'Rp' . number_format($angka, 2, ',', '.');
    }
}

// Get all coffees with review statistics
$db->query("SELECT ct.*, 
           COUNT(r.id) as review_count, 
           AVG(r.rating) as avg_rating,
           SUM(CASE WHEN r.rating >= 4 THEN 1 ELSE 0 END) as favorites_count
           FROM coffee_types ct 
           LEFT JOIN recommendations r ON ct.id = r.coffee_id 
           GROUP BY ct.id 
           ORDER BY ct.name ASC");
$coffees = $db->resultset();
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Kelola Kopi</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            margin: 0;
            font-family: 'Inter', Arial, sans-serif;
            background: #faf8f6;
            color: #23190f;
        }

        .admin-layout {
            display: flex;
            min-height: 100vh;
        }

        
        .admin-content {
            margin-left: 240px;
            padding: 40px 32px 32px 32px;
            width: 100%;
            min-height: 100vh;
            box-sizing: border-box;
        }

        .dashboard-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            margin-bottom: 32px;
            flex-wrap: wrap;
            gap: 24px;
        }

        .dashboard-header h1 {
            margin-left: 30px;
            font-size: 2.2rem;
            color: rgb(255, 255, 255);
        }

        .dashboard-header p {
            color: #6d4c41;
            margin-bottom: 0;
        }

        .btn-primary {
            background: #28a745;
            color: #fff;
            border: none;
            border-radius: 6px;
            padding: 10px 20px;
            font-size: 1rem;
            cursor: pointer;
            text-decoration: none;
            transition: background 0.2s;
        }

        .btn-primary:hover {
            background: #218838;
        }

        .dashboard-card {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(47, 27, 20, 0.06);
            padding: 24px;
            margin-bottom: 24px;
        }

        .dashboard-card h3 {
            margin-top: 0;
            color: #8b4513;
            font-size: 1.1rem;
            margin-bottom: 12px;
        }

        .coffee-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(340px, 1fr));
            gap: 24px;
        }

        .coffee-card {
            background: #f7f7f7;
            border-radius: 10px;
            box-shadow: 0 1px 4px rgba(47, 27, 20, 0.04);
            display: flex;
            gap: 18px;
            padding: 18px;
            align-items: flex-start;
        }

        .coffee-image {
            width: 110px;
            height: 110px;
            border-radius: 8px;
            overflow: hidden;
            position: relative;
            flex-shrink: 0;
        }

        .coffee-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .roast-badge {
            position: absolute;
            top: 8px;
            left: 8px;
            background: #cd853f;
            color: #fff;
            padding: 2px 10px;
            border-radius: 6px;
            font-size: 0.95rem;
            font-weight: 500;
        }

        .coffee-stats {
            position: absolute;
            bottom: 8px;
            right: 8px;
            display: flex;
            gap: 8px;
            background: rgba(0, 0, 0, 0.8);
            padding: 4px 8px;
            border-radius: 6px;
        }

        .stat-item {
            color: #fff;
            font-size: 0.98rem;
            display: flex;
            align-items: center;
            gap: 2px;
        }

        .coffee-info {
            flex: 1;
        }

        .coffee-info h3 {
            margin: 0 0 6px 0;
            font-size: 1.2rem;
            color: #2f1b14;
        }

        .coffee-origin {
            color: #8b4513;
            font-size: 0.98rem;
            margin-bottom: 4px;
        }

        .coffee-description {
            font-size: 0.97rem;
            margin-bottom: 8px;
        }

        .coffee-details {
            font-size: 0.95rem;
            margin-bottom: 8px;
        }

        .flavor-notes,
        .brewing-method {
            margin-bottom: 2px;
        }

        .coffee-price {
            font-weight: bold;
            color: #28a745;
            font-size: 1.1rem;
            margin-bottom: 8px;
        }

        .coffee-actions {
            display: flex;
            gap: 10px;
            margin-top: 8px;
        }

        .btn-small {
            padding: 6px 14px;
            font-size: 0.98rem;
            border-radius: 6px;
        }

        .btn-secondary {
            background: #ffc107;
            color: #fff;
            border: none;
        }

        .btn-secondary:hover {
            background: #e0a800;
        }

        .btn-danger {
            background: #dc3545;
            color: #fff;
            border: none;
        }

        .btn-danger:hover {
            background: #b52a37;
        }

        .empty-state {
            text-align: center;
            padding: 48px 0;
            color: #bdbdbd;
        }

        .empty-state i {
            color: #cd853f;
            margin-bottom: 16px;
        }

        .empty-state h3 {
            color: #2f1b14;
            margin-bottom: 8px;
        }

        .empty-state p {
            margin-bottom: 18px;
        }

        @media (max-width: 900px) {
            .admin-layout {
                flex-direction: column;
            }

        

            .admin-content {
                margin-left: 0;
                padding: 24px 8px;
            }

            .coffee-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>
    <div class="admin-layout">
        <?php include '../includes/admin_sidebar.php'; ?>
        <div class="admin-content">
            <div class="dashboard-header">
                <div>
                    <h1>Kelola Data Kopi</h1>
                </div>
                <a href="add_coffee.php" class="btn btn-brown">
                    <i class="fas fa-plus"></i> Tambah Kopi Baru
                </a>
            </div>

            <?php if (isset($_SESSION['success_message'])): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    <?php echo $_SESSION['success_message'];
                    unset($_SESSION['success_message']); ?>
                </div>
            <?php endif; ?>

            <?php if (isset($_SESSION['error_message'])): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i>
                    <?php echo $_SESSION['error_message'];
                    unset($_SESSION['error_message']); ?>
                </div>
            <?php endif; ?>

            <div class="dashboard-card">
                <h3><i class="fas fa-coffee"></i> Inventory Kopi (<?php echo count($coffees); ?> total)</h3>

                <?php if (!empty($coffees)): ?>
                    <div class="coffee-grid">
                        <?php foreach ($coffees as $coffee): ?>
                            <div class="coffee-card admin-coffee-card">
                                <div class="coffee-image">
                                    <img src="<?php echo $coffee->image_url; ?>" alt="<?php echo htmlspecialchars($coffee->name); ?>">
                                    <div class="roast-badge <?php echo $coffee->roast_level; ?>">
                                        <?php echo ucfirst(str_replace('-', ' ', $coffee->roast_level)); ?>
                                    </div>
                                    <div class="coffee-stats">
                                        <span class="stat-item">
                                            <i class="fas fa-star"></i>
                                            <?php echo $coffee->avg_rating ? number_format($coffee->avg_rating, 1) : 'N/A'; ?>
                                        </span>
                                        <span class="stat-item">
                                            <i class="fas fa-comments"></i>
                                            <?php echo $coffee->review_count; ?>
                                        </span>
                                        <span class="stat-item">
                                            <i class="fas fa-heart"></i>
                                            <?php echo $coffee->favorites_count; ?>
                                        </span>
                                    </div>
                                </div>
                                <div class="coffee-info">
                                    <h3><?php echo htmlspecialchars($coffee->name); ?></h3>
                                    <p class="coffee-origin"><?php echo htmlspecialchars($coffee->origin); ?></p>
                                    <p class="coffee-description"><?php echo htmlspecialchars($coffee->description); ?></p>

                                    <div class="coffee-details">
                                        <div class="flavor-notes">
                                            <strong>Catatan Rasa:</strong>
                                            <span><?php echo htmlspecialchars($coffee->flavor_notes); ?></span>
                                        </div>
                                        <div class="brewing-method">
                                            <strong>Cocok untuk:</strong>
                                            <span><?php echo htmlspecialchars($coffee->brewing_method); ?></span>
                                        </div>
                                    </div>

                                    <div class="coffee-price">
                                        <span class="price">
                                            <?php
                                            echo formatRupiahAdmin($coffee->price);
                                            ?>
                                        </span>
                                    </div>

                                    <div class="coffee-actions">
                                        <a href="edit_coffee.php?id=<?php echo $coffee->id; ?>" class="btn-brown btn-sm"><i class="fas fa-edit"></i> Edit</a>
                                        <button class="btn btn-small btn-danger" onclick="if(confirm('Yakin ingin menghapus kopi ini?')) window.location.href='manage_coffees.php?delete=<?= $coffee->id ?>';">
                                            <i class="fas fa-trash"></i> Hapus
                                        </button>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="empty-state">
                        <i class="fas fa-coffee fa-3x"></i>
                        <h3>Belum ada kopi yang ditambahkan</h3>
                        <p>Mulai membangun katalog kopi Anda dengan menambahkan varietas kopi pertama Anda.</p>
                        <a href="add_coffee.php" class="btn btn-brown">
                            <i class="fas fa-plus"></i> Tambah Kopi Pertama
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>

</html>