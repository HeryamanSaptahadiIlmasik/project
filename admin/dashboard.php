<?php
require_once '../config/init.php';

if (!isLoggedIn() || !isAdmin()) {
    redirect('../login.php');
}

// Get statistics
$db->query("SELECT COUNT(*) as total_users FROM users WHERE role = 'user'");
$total_users = $db->single()->total_users;

$db->query("SELECT COUNT(*) as total_coffees FROM coffee_types");
$total_coffees = $db->single()->total_coffees;

// Get recent users
$db->query("SELECT username, email, created_at FROM users WHERE role = 'user' ORDER BY created_at DESC LIMIT 5");
$recent_users = $db->resultset();

// Get popular coffees (most rated)
$db->query("SELECT ct.name, ct.origin, ct.price, COUNT(r.id) as review_count, AVG(r.rating) as avg_rating
           FROM coffee_types ct 
           LEFT JOIN recommendations r ON ct.id = r.coffee_id 
           GROUP BY ct.id 
           ORDER BY review_count DESC, avg_rating DESC 
           LIMIT 5");
$popular_coffees = $db->resultset();

// Query distribusi roast level dari riwayat rekomendasi (tabel recommendation_history, kolom recommended_roast)
$db->query("SELECT recommended_roast AS roast_level, COUNT(*) as total FROM recommendation_history GROUP BY recommended_roast");
$roast_stats = $db->resultset();
$roast_labels = [];
$roast_counts = [];
foreach ($roast_stats as $row) {
    $roast_labels[] = ucfirst($row->roast_level);
    $roast_counts[] = (int)$row->total;
}
?>

<?php
$page_title = 'Dashboard Admin';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>
    <link rel="stylesheet" href="/project/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        * {
            box-sizing: border-box;
        }
        
        body {
            margin: 0;
            padding: 0;
            font-family: 'Inter', Arial, sans-serif;
            background: #faf8f6;
            color: #23190f;
            overflow-x: hidden;
        }
        
        .admin-layout {
            display: flex;
            min-height: 100vh;
            width: 100%;
        }
        
        
        
        .admin-content {
            margin-left: 240px;
            padding: 32px;
            width: calc(100% - 240px);
            min-height: 100vh;
            background: #faf8f6;
        }
        
        .admin-dashboard-header {
            background: linear-gradient(135deg, #a86b3c, #7b4a1e);
            color: #fff;
            border-radius: 16px;
            padding: 40px 36px 28px 36px;
            margin-bottom: 36px;
            box-shadow: 0 2px 12px rgba(47,27,20,0.08);
            width: 100%;
        }
        
        .admin-dashboard-header h1, .admin-dashboard-header h2 {
            color: #fff;
            margin: 0 0 8px 0;
        }
        
        .admin-dashboard-header p {
            color: #f7f7f7;
            margin: 0;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 24px;
            margin-bottom: 36px;
            width: 100%;
        }
        
        .stats-card {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 2px 12px rgba(47,27,20,0.08);
            padding: 36px 40px;
            display: flex;
            flex-direction: column;
            align-items: flex-start;
        }
        
        .dashboard-card {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 2px 12px rgba(47,27,20,0.08);
            padding: 36px 40px;
            margin-bottom: 36px;
            width: 100%;
        }
        
        .stats-card .stats-title {
            font-size: 1.1rem;
            color: #a86b3c;
            font-weight: 600;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .stats-card .stats-value {
            font-size: 2.2rem;
            font-weight: bold;
            color: #23190f;
            margin-bottom: 4px;
        }
        
        .stats-card .stats-desc {
            color: #7b4a1e;
            font-size: 0.98rem;
        }
        
        .admin-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 12px;
            font-size: 15px;
        }
        
        .admin-table th, .admin-table td {
            border: 1px solid #eee;
            padding: 12px 16px;
            text-align: left;
        }
        
        .admin-table th {
            background: #f7f7f7;
            font-weight: bold;
            color: #a86b3c;
        }
        
        .admin-table tr:nth-child(even) {
            background: #fcf8f4;
        }
        
        .admin-table tr:hover {
            background: #f3e7de;
        }
        
        .rating-display {
            display: flex;
            align-items: center;
            gap: 4px;
        }
        
        .rating-display .fa-star.active {
            color: #f7b731;
        }
        
        .rating-display .fa-star {
            color: #e0e0e0;
        }
        
        .admin-dashboard-chart {
            width: 100%;
            margin-bottom: 36px;
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 2px 12px rgba(47,27,20,0.08);
            padding: 32px 24px 18px 24px;
        }
        
        .admin-dashboard-chart h3 {
            color: #a86b3c;
            font-size: 1.15rem;
            margin-bottom: 18px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .chart-container {
            position: relative;
            height: 300px;
            width: 100%;
        }
        
        .quick-actions {
            display: flex;
            gap: 16px;
            flex-wrap: wrap;
            margin-top: 20px;
        }
        
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 24px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.2s;
            border: none;
            cursor: pointer;
        }
        
        .btn-primary {
            background: #a86b3c;
            color: #fff;
        }
        
        .btn-primary:hover {
            background: #7b4a1e;
        }
        
        .btn-secondary {
            background: #f3e7de;
            color: #7b4a1e;
        }
        
        .btn-secondary:hover {
            background: #e8d5c4;
        }
        
        .filter-select {
            margin-bottom: 16px;
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 6px;
            background: #fff;
            font-size: 14px;
        }
        
        .table-responsive {
            overflow-x: auto;
            width: 100%;
        }
        
        @media (max-width: 1200px) {
            .stats-grid {
                grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            }
        }
        
        @media (max-width: 900px) {
            .admin-layout {
                flex-direction: column;
            }
            
            .sidebar {
                width: 100%;
                flex-direction: row;
                height: auto;
                position: static;
                border-bottom: 1px solid #4e342e;
            }
            
            .sidebar .sidebar-nav {
                flex-direction: row;
                padding: 0 8px;
            }
            
            .sidebar .sidebar-link {
                padding: 12px 10px;
                font-size: 1rem;
            }
            
            .admin-content {
                margin-left: 0;
                width: 100%;
                padding: 24px 16px;
            }
            
            .stats-grid {
                grid-template-columns: 1fr;
            }
            
            .quick-actions {
                flex-direction: column;
            }
            
            .btn {
                justify-content: center;
            }
        }
        
        @media (max-width: 600px) {
            .admin-content {
                padding: 16px 12px;
            }
            
            .admin-dashboard-header,
            .dashboard-card,
            .admin-dashboard-chart {
                padding: 24px 20px;
            }
            
            .stats-card {
                padding: 24px 20px;
            }
        }
    </style>
</head>

<body>
<div class="admin-layout">
    <?php include '../includes/admin_sidebar.php'; ?>
            <div class="admin-content">
                <div class="admin-dashboard-header">
                    <h1>Dashboard Admin</h1>
                    <p>Sistem Rekomendasi Tingkat Kematangan Kopi Triak Coffee & Roaster</p>
                </div>
        
                <div class="admin-dashboard-chart">
                    <h3><i class="fas fa-chart-bar"></i> Distribusi Tingkat Kematangan Kopi</h3>
            <div class="chart-container">
                <canvas id="roast-distribution"></canvas>
            </div>
                </div>
        
                <div class="stats-grid">
                    <div class="stats-card">
                        <div class="stats-title"><i class="fas fa-users"></i> Total Pengguna</div>
                        <div class="stats-value"><?= $total_users ?></div>
                        <div class="stats-desc">Pengguna terdaftar</div>
                    </div>
                    <div class="stats-card">
                        <div class="stats-title"><i class="fas fa-mug-hot"></i> Total Kopi</div>
                        <div class="stats-value"><?= $total_coffees ?></div>
                        <div class="stats-desc">Varietas kopi</div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="dashboard-card">
                    <h3><i class="fas fa-bolt"></i> Aksi Cepat</h3>
                    <div class="quick-actions">
                        <a href="add_coffee.php" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Tambah Kopi Baru
                        </a>
                        <a href="manage_coffees.php" class="btn btn-secondary">
                            <i class="fas fa-edit"></i> Kelola Kopi
                        </a>
                        <a href="manage_users.php" class="btn btn-secondary">
                            <i class="fas fa-users-cog"></i> Kelola Pengguna
                        </a>
                        <a href="../catalog.php" class="btn btn-secondary">
                            <i class="fas fa-eye"></i> Lihat Katalog
                        </a>
                    </div>
                </div>

                <!-- Recent Users -->
                <div class="dashboard-card">
                    <h3><i class="fas fa-user-plus"></i> Pengguna Terbaru</h3>
                    <?php if (!empty($recent_users)): ?>
                        <div class="table-responsive">
                            <table class="admin-table">
                                <thead>
                                    <tr>
                                        <th>Username</th>
                                        <th>Email</th>
                                        <th>Bergabung</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($recent_users as $user): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($user->username); ?></td>
                                            <td><?php echo htmlspecialchars($user->email); ?></td>
                                            <td><?php echo date('M j, Y', strtotime($user->created_at)); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <p>Belum ada pengguna terdaftar.</p>
                    <?php endif; ?>
                </div>

                <!-- Popular Coffees -->
                <div class="dashboard-card">
                    <h3><i class="fas fa-trophy"></i> Kopi Populer</h3>
                    <?php if (!empty($popular_coffees)): ?>
                        <div class="table-responsive">
                            <table class="admin-table">
                                <thead>
                                    <tr>
                                        <th>Nama Kopi</th>
                                        <th>Asal</th>
                                        <th>Harga</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($popular_coffees as $coffee): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($coffee->name); ?></td>
                                            <td><?php echo htmlspecialchars($coffee->origin); ?></td>
                                            <td>
    <?php
    if (!function_exists('formatRupiahDashboard')) {
        function formatRupiahDashboard($angka) {
            return 'Rp' . number_format($angka, 2, ',', '.');
        }
    }
    echo formatRupiahDashboard($coffee->price);
    ?>
</td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <p>Tidak ada data kopi tersedia.</p>
                    <?php endif; ?>
                </div>

        <!-- Grafik Riwayat Rekomendasi -->
                <div class="dashboard-card">
                    <h3><i class="fas fa-chart-line"></i> Grafik Riwayat Rekomendasi (Semua User)</h3>
            <select id="filter-grafik" class="filter-select">
                        <option value="day">Per Hari</option>
                        <option value="week">Per Minggu</option>
                        <option value="month">Per Bulan</option>
                    </select>
            <div class="chart-container">
                <canvas id="grafik-rekomendasi"></canvas>
            </div>
        </div>
    </div>
                </div>

                <script>
// Grafik Distribusi Roast Level
const roastLabels = <?= json_encode($roast_labels) ?>;
const roastCounts = <?= json_encode($roast_counts) ?>;
const roastColors = ["#a86b3c", "#f7b731", "#7b4a1e", "#23190f", "#e67e22", "#b9770e"];

new Chart(document.getElementById('roast-distribution'), {
    type: 'bar',
    data: {
        labels: roastLabels,
        datasets: [{
            data: roastCounts,
            backgroundColor: roastColors.slice(0, roastLabels.length),
            borderColor: roastColors.slice(0, roastLabels.length),
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { 
            legend: { display: false } 
        },
        scales: {
            x: { 
                grid: { display: false },
                ticks: {
                    color: '#7b4a1e',
                    font: {
                        size: 12
                    }
                }
            },
            y: { 
                beginAtZero: true, 
                ticks: { 
                    stepSize: 1,
                    color: '#7b4a1e',
                    font: {
                        size: 12
                    }
                },
                grid: {
                    color: '#f0f0f0'
                }
            }
        }
    }
});

// Grafik Riwayat Rekomendasi
const dataPerDay = { 
    labels: ["2025-07-01","2025-07-02","2025-07-03","2025-07-04","2025-07-05","2025-07-06","2025-07-07"], 
    data: [5,7,3,8,4,2,6] 
};
const dataPerWeek = { 
    labels: ["Minggu 26","Minggu 27","Minggu 28"], 
    data: [21, 32, 27] 
};
const dataPerMonth = { 
    labels: ["Mei","Juni","Juli"], 
    data: [60, 92, 45] 
};

let recommendationChart;

                function updateChart(type) {
                    let chartData;
    if(type === "day") chartData = dataPerDay;
    else if(type === "week") chartData = dataPerWeek;
                    else chartData = dataPerMonth;
    
    if(recommendationChart) recommendationChart.destroy();
    
    recommendationChart = new Chart(document.getElementById('grafik-rekomendasi'), {
                        type: 'line',
                        data: {
                            labels: chartData.labels,
                            datasets: [{
                                label: 'Jumlah Rekomendasi',
                                data: chartData.data,
                                borderColor: '#a86b3c',
                                backgroundColor: 'rgba(168,107,60,0.1)',
                borderWidth: 3,
                                tension: 0.3,
                fill: true,
                pointBackgroundColor: '#a86b3c',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointRadius: 6
                            }]
                        },
                        options: {
                            responsive: true,
            maintainAspectRatio: false,
            plugins: { 
                legend: { display: false } 
            },
            scales: {
                x: {
                    grid: {
                        color: '#f0f0f0'
                    },
                    ticks: {
                        color: '#7b4a1e',
                        font: {
                            size: 12
                        }
                    }
                },
                y: {
                    beginAtZero: true,
                    grid: {
                        color: '#f0f0f0'
                    },
                    ticks: {
                        color: '#7b4a1e',
                        font: {
                            size: 12
                        }
                    }
                }
            }
        }
    });
}

                document.getElementById('filter-grafik').addEventListener('change', function() {
                    updateChart(this.value);
                });

// Initialize chart
                updateChart('day');
                </script>

</body>
</html>