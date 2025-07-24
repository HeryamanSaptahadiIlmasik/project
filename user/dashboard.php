<?php
require_once '../config/init.php';

if (!isLoggedIn()) {
    redirect('../login.php');
}

if (isAdmin()) {
    redirect('../admin/dashboard.php');
}

$page_title = 'Beranda';

$user_id = $_SESSION['user_id'];

// Get user preferences
$db->query("SELECT * FROM user_preferences WHERE user_id = :user_id");
$db->bind(':user_id', $user_id);
$preferences = $db->single();

// Get user's favorite coffees (recommendations with rating > 3)
$db->query("SELECT ct.*, r.rating, r.review, r.created_at as review_date, r.recommended_roast, r.rule_applied
           FROM recommendations r 
           JOIN coffee_types ct ON r.coffee_id = ct.id 
           WHERE r.user_id = :user_id AND r.rating >= 4 
           ORDER BY r.rating DESC, r.created_at DESC");
$db->bind(':user_id', $user_id);
$favorites = $db->resultset();

// Get coffee recommendations based on rule-based system
$recommended_coffees = [];
$rule_recommendation = null;

if ($preferences && $preferences->jenis_kopi && $preferences->metode_penyajian && 
    $preferences->profil_rasa && $preferences->proses) {
    
    // Get rule-based recommendation
    $rule_recommendation = getRecommendedRoast(
        $preferences->jenis_kopi,
        $preferences->metode_penyajian,
        $preferences->proses,
        $preferences->profil_rasa
    );
    
    // Find coffees that match the recommended roast level and user preferences
    $db->query("SELECT ct.*, 
                CASE 
                    WHEN ct.roast_level = :recommended_roast AND ct.jenis_kopi = :jenis_kopi THEN 100
                    WHEN ct.roast_level = :recommended_roast THEN 90
                    WHEN ct.jenis_kopi = :jenis_kopi THEN 80
                    WHEN ct.profil_rasa = :profil_rasa THEN 70
                    WHEN ct.proses = :proses THEN 60
                    ELSE 50
                END as match_score
               FROM coffee_types ct 
               WHERE ct.id NOT IN (
                   SELECT COALESCE(r.coffee_id, 0) 
                   FROM recommendations r 
                   WHERE r.user_id = :user_id
               )
               ORDER BY match_score DESC, ct.price ASC 
               LIMIT 6");
    $db->bind(':recommended_roast', $rule_recommendation['roast']);
    $db->bind(':jenis_kopi', $preferences->jenis_kopi);
    $db->bind(':profil_rasa', $preferences->profil_rasa);
    $db->bind(':proses', $preferences->proses);
    $db->bind(':user_id', $user_id);
    $recommended_coffees = $db->resultset();
}

// Hapus query dan variabel terkait favorite, review, rating, avg_rating, recent_reviews, dsb.
// Hapus tampilan recent reviews, rating modal, dan script terkait.
// Pastikan hanya ada statistik, riwayat rekomendasi, dan info staff.

// Get statistics
$db->query("SELECT COUNT(*) as total_reviews FROM recommendations WHERE user_id = :user_id");
$db->bind(':user_id', $user_id);
$total_reviews = $db->single()->total_reviews;

// Ambil riwayat rekomendasi
$db->query("SELECT * FROM recommendation_history WHERE user_id = :user_id ORDER BY created_at DESC LIMIT 20");
$db->bind(':user_id', $user_id);
$history = $db->resultset();

// Query distribusi roast level
$db->query("SELECT recommended_roast, COUNT(*) as total FROM recommendation_history WHERE user_id = :user_id GROUP BY recommended_roast");
$db->bind(':user_id', $user_id);
$roast_stats = $db->resultset();
$roast_labels = [];
$roast_counts = [];
foreach ($roast_stats as $row) {
    $roast_labels[] = ucfirst($row->recommended_roast);
    $roast_counts[] = (int)$row->total;
}

$nama_staff = isset($_SESSION['nama']) ? $_SESSION['nama'] : 'Staff';

// Total rekomendasi hari ini
$db->query("SELECT COUNT(*) as total FROM recommendation_history WHERE user_id = :user_id AND DATE(created_at) = CURDATE()");
$db->bind(':user_id', $user_id);
$total_today = $db->single()->total;

// Total kopi
$db->query("SELECT COUNT(*) as total FROM coffee_types");
$total_kopi = $db->single()->total;

// Roast level paling populer
$db->query("SELECT recommended_roast, COUNT(*) as cnt FROM recommendation_history WHERE user_id = :user_id GROUP BY recommended_roast ORDER BY cnt DESC LIMIT 1");
$db->bind(':user_id', $user_id);
$row_popular = $db->single();
$popular_roast = $row_popular ? ucfirst($row_popular->recommended_roast) : '-';

// Ambil gambar kopi untuk tiap roast level
function getRoastImage($db, $roast) {
    $db->query("SELECT image_url FROM coffee_types WHERE roast_level = :roast LIMIT 1");
    $db->bind(':roast', $roast);
    $row = $db->single();
    return $row && $row->image_url ? $row->image_url : '/project/assets/roast_default.png';
}
$img_light = getRoastImage($db, 'light');
$img_medium = getRoastImage($db, 'medium');
$img_dark = getRoastImage($db, 'dark');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>
    <link rel="stylesheet" href="/project/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            margin: 0;
            font-family: 'Inter', Arial, sans-serif;
            background: #faf8f6;
            color: #23190f;
        }
        .user-layout {
            display: flex;
            min-height: 100vh;
        }
        .sidebar {
            width: 220px;
            background: #2f1b14;
            color: #fff;
            display: flex;
            flex-direction: column;
            padding: 0;
            position: fixed;
            top: 0;
            left: 0;
            bottom: 0;
            z-index: 100;
        }
        .sidebar .sidebar-brand {
            font-size: 1.4rem;
            font-weight: bold;
            padding: 28px 20px 20px 20px;
            display: flex;
            align-items: center;
            gap: 10px;
            border-bottom: 1px solid #4e342e;
        }
        .sidebar .sidebar-nav {
            flex: 1;
            display: flex;
            flex-direction: column;
            padding: 20px 0;
        }
        .sidebar .sidebar-link {
            color: #fff;
            text-decoration: none;
            padding: 12px 28px;
            font-size: 1.08rem;
            display: flex;
            align-items: center;
            gap: 12px;
            transition: background 0.2s;
        }
        .sidebar .sidebar-link.active, .sidebar .sidebar-link:hover {
            background: #4e342e;
        }
        .sidebar .sidebar-footer {
            padding: 14px 20px;
            font-size: 0.95rem;
            border-top: 1px solid #4e342e;
            color: #bdbdbd;
        }
        .user-content {
            margin-left: 220px;
            padding: 36px 24px 24px 24px;
            width: 100%;
            min-height: 100vh;
            box-sizing: border-box;
        }
        @media (max-width: 900px) {
            .user-layout {
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
                padding: 10px 8px;
                font-size: 1rem;
            }
            .user-content {
                margin-left: 0;
                padding: 18px 4px;
            }
        }
        .dashboard-nav {
            display: flex;
            gap: 20px;
            margin-bottom: 28px;
            justify-content: flex-start;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
        }
        .dashboard-card-nav {
            flex: 1 1 0;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 1px 6px rgba(47,27,20,0.07);
            padding: 22px 0 16px 0;
            text-align: center;
            color: #a86b3c;
            font-size: 1.08rem;
            font-weight: 600;
            text-decoration: none;
            transition: box-shadow 0.18s, background 0.18s, transform 0.18s;
            margin-bottom: 0;
            border: 1.5px solid #f7e6d1;
            min-width: 120px;
            max-width: 220px;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 8px;
        }
        .dashboard-card-nav i {
            font-size: 1.7rem;
            margin-bottom: 6px;
            display: block;
        }
        .dashboard-card-nav:hover {
            background: #f7f7f7;
            box-shadow: 0 4px 16px rgba(47,27,20,0.10);
            transform: translateY(-2px) scale(1.03);
            color: #7b4a1e;
        }
        .dashboard-card {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 2px 12px rgba(47,27,20,0.08);
            padding: 40px 44px;
            margin-bottom: 40px;
            transition: box-shadow 0.2s;
        }
        .dashboard-card h3 {
            font-size: 1.3rem;
            color: #a86b3c;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .dashboard-card i {
            font-size: 1.5em;
        }
        .dashboard-header {
            margin-bottom: 32px;
            background: linear-gradient(120deg,#a86b3c 60%,#7b4a1e 100%);
            border-radius: 14px;
            padding: 32px 36px 22px 36px;
            color: #fff;
            box-shadow: 0 2px 10px rgba(47,27,20,0.07);
            display: flex;
            align-items: center;
            gap: 18px;
        }
        .dashboard-header i {
            font-size: 2.2rem;
            margin-right: 10px;
        }
        .dashboard-header h2, .dashboard-header div {
            color: #fff !important;
            text-shadow: 0 1px 4px rgba(47,27,20,0.10);
            margin: 0;
        }
        .dashboard-stats {
            display: flex;
            gap: 24px;
            margin-bottom: 36px;
        }
        .stat-card {
            flex:1;
            background: linear-gradient(120deg,#fff7f0 60%,#f7e6d1 100%);
            border-radius: 14px;
            box-shadow: 0 2px 10px rgba(47,27,20,0.07);
            padding: 28px 0 18px 0;
            text-align: center;
            color: #7b4a1e;
            font-weight: 600;
            min-width: 160px;
        }
        .stat-value {
            font-size: 2.1rem;
            font-weight: 700;
            margin-bottom: 6px;
            color: #a86b3c;
            text-shadow: 0 1px 4px rgba(255,255,255,0.10);
        }
        .stat-label {
            font-size: 1.05rem;
            color: #7b4a1e;
            letter-spacing: 0.5px;
        }
        .dashboard-funfact {
            margin: 0 auto 24px auto;
            max-width: 520px;
            background: #fff7f0;
            border-radius: 12px;
            padding: 18px 28px;
            color: #a86b3c;
            font-size:1.08rem;
            box-shadow:0 1px 6px rgba(47,27,20,0.06);
            text-align: center;
        }
        .dashboard-funfact b {
            color: #7b4a1e;
        }
        .dashboard-info-roast-cards {
            display: flex;
            gap: 24px;
            justify-content: center;
            margin: 0 auto 32px auto;
            max-width: 1100px;
            flex-wrap: wrap;
        }
        .roast-card {
            background: #fffdfa;
            border-radius: 14px;
            box-shadow: 0 2px 10px rgba(47,27,20,0.08);
            border: 2px solid #f7e6d1;
            padding: 24px 20px 18px 20px;
            color: #7b4a1e;
            font-size: 1.04rem;
            width: 320px;
            min-width: 260px;
            max-width: 340px;
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-bottom: 18px;
            transition: box-shadow 0.2s, border 0.2s;
        }
        .roast-card:hover {
            box-shadow: 0 6px 24px rgba(47,27,20,0.13);
            border-color: #f7b731;
        }
        .roast-img {
            width: 70px;
            height: 70px;
            object-fit: contain;
            margin-bottom: 12px;
        }
        .roast-title {
            font-size: 1.18rem;
            font-weight: 700;
            margin-bottom: 10px;
            color: #a86b3c;
        }
        .roast-light { background: #fffdfa; }
        .roast-medium { background: #fdf6ed; }
        .roast-dark { background: #f7f3ef; }
        @media (max-width: 1100px) {
            .dashboard-info-roast-cards { flex-direction: column; align-items: center; }
        }
        .dashboard-header {
            background: linear-gradient(90deg, #a86b3c 60%, #d2a86b 100%);
            color: #fff;
            border-radius: 12px;
            padding: 28px 32px 18px 32px;
            margin-bottom: 24px;
            box-shadow: 0 2px 10px rgba(47,27,20,0.08);
        }
        .dashboard-header h2 { margin: 0 0 8px 0; }
        .dashboard-header p { margin: 0; font-size: 1.1em; }
        .dashboard-cards {
            display: flex;
            gap: 18px;
            margin-bottom: 32px;
        }
        .dashboard-card {
            background: linear-gradient(120deg,#fff7f0 60%,#f7e6d1 100%);
            border-radius: 14px;
            box-shadow: 0 2px 10px rgba(47,27,20,0.07);
            padding: 28px 0 18px 0;
            text-align: center;
            color: #7b4a1e;
            font-weight: 600;
            min-width: 160px;
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 8px;
            position: relative;
        }
        .dashboard-card i {
            font-size: 1.7rem;
            margin-bottom: 8px;
            color: #a86b3c;
        }
        .card-value {
            font-size: 2.2em;
            font-weight: bold;
            color: #a86b3c;
        }
        .card-label {
            color: #7b4a1e;
            font-size: 1.1em;
            margin-top: 6px;
        }
        .dashboard-section {
            margin-top: 36px;
            background: #fff;
            border-radius: 12px;
            padding: 24px 28px;
            box-shadow: 0 2px 8px rgba(47,27,20,0.06);
        }
        .dashboard-section h3 {
            color: #a86b3c;
            font-size: 1.18rem;
            margin-bottom: 18px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .dashboard-section h3 i {
            font-size: 1.2em;
        }
        .history-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 12px;
            font-size: 15px;
        }
        .history-table th, .history-table td {
            border: 1px solid #eee;
            padding: 10px 14px;
            text-align: left;
        }
        .history-table th {
            background: #f7f7f7;
            font-weight: bold;
            color: #a86b3c;
            position: sticky;
            top: 0;
            z-index: 1;
        }
        .history-table tr:nth-child(even) {
            background: #fcf8f4;
        }
        .history-table tr:hover {
            background: #f3e7de;
        }
        @media (max-width: 900px) {
            .dashboard-cards { flex-direction: column; gap: 12px; }
            .dashboard-card { padding: 18px 12px; }
            .dashboard-section { padding: 14px 8px; }
            .dashboard-header { flex-direction: column; align-items: flex-start; padding: 24px 12px 14px 12px; }
        }
    </style>
</head>
<body>
<div class="user-layout">
    <?php include '../includes/sidebar.php'; ?>
    <div class="user-content">
        <div class="dashboard-header">
            <i class="fas fa-mug-hot"></i>
            <div>
            <h2>Selamat datang, <?php echo htmlspecialchars($nama_staff); ?>!</h2>
            <p>Semoga harimu menyenangkan ☕</p>
            </div>
        </div>
        <div class="dashboard-cards">
            <div class="dashboard-card">
                <i class="fas fa-calendar-day"></i>
                <div class="card-value"><?php echo $total_today; ?></div>
                <div class="card-label">Rekomendasi Hari Ini</div>
            </div>
            <div class="dashboard-card">
                <i class="fas fa-mug-hot"></i>
                <div class="card-value"><?php echo $total_kopi; ?></div>
                <div class="card-label">Kopi Terdaftar</div>
            </div>
            <div class="dashboard-card">
                <i class="fas fa-fire"></i>
                <div class="card-value"><?php echo htmlspecialchars($popular_roast); ?></div>
                <div class="card-label">Roast Level Favorit</div>
            </div>
        </div>
        <div class="dashboard-section">
            <h3><i class="fas fa-history"></i> Riwayat Rekomendasi Terbaru</h3>
            <table class="history-table" style="width:100%;margin-top:12px;">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Jenis Kopi</th>
                        <th>Metode</th>
                        <th>Profil Rasa</th>
                        <th>Proses</th>
                        <th>Rekomendasi</th>
                        <th>Aturan</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($history as $item): ?>
                <tr>
                    <td><?= htmlspecialchars($item->created_at) ?></td>
                    <td><?= htmlspecialchars($item->jenis_kopi) ?></td>
                    <td><?= htmlspecialchars($item->metode_penyajian) ?></td>
                    <td><?= htmlspecialchars($item->profil_rasa) ?></td>
                    <td><?= htmlspecialchars($item->proses) ?></td>
                    <td><?= htmlspecialchars($item->recommended_roast) ?></td>
                    <td><?= htmlspecialchars($item->rule_applied) ?></td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
// Contoh data statis, nanti bisa diganti AJAX
const dataPerDay = { labels: ["2025-07-01","2025-07-02","2025-07-03","2025-07-04","2025-07-05","2025-07-06","2025-07-07"], data: [2,3,1,4,2,0,3] };
const dataPerWeek = { labels: ["Minggu 26","Minggu 27","Minggu 28"], data: [7, 12, 9] };
const dataPerMonth = { labels: ["Mei","Juni","Juli"], data: [20, 32, 15] };
let chart;
function updateChart(type) {
    let chartData;
    if(type==="day") chartData = dataPerDay;
    else if(type==="week") chartData = dataPerWeek;
    else chartData = dataPerMonth;
    if(chart) chart.destroy();
    chart = new Chart(document.getElementById('grafik-rekomendasi').getContext('2d'), {
        type: 'line',
        data: {
            labels: chartData.labels,
            datasets: [{
                label: 'Jumlah Rekomendasi',
                data: chartData.data,
                borderColor: '#a86b3c',
                backgroundColor: 'rgba(168,107,60,0.1)',
                tension: 0.3,
                fill: true
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } }
        }
    });
}
document.getElementById('filter-grafik').addEventListener('change', function() {
    updateChart(this.value);
});
updateChart('day');
</script>
</body>
</html>