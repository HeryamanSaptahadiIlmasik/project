<?php
require_once '../config/init.php';

if (!isLoggedIn() || isAdmin()) {
    redirect('../login.php');
}

$user_id = $_SESSION['user_id'];
$db->query("SELECT * FROM recommendation_history WHERE user_id = :user_id ORDER BY created_at DESC LIMIT 50");
$db->bind(':user_id', $user_id);
$history = $db->resultset();
$page_title = 'Riwayat Rekomendasi';

function mapRoastLevel($roast) {
    $roast = strtolower(trim($roast));
    if (strpos($roast, 'light') !== false && strpos($roast, 'medium') !== false) {
        return 'Light to medium';
    } elseif (strpos($roast, 'medium') !== false && strpos($roast, 'high') !== false) {
        return 'Medium-light to medium-high';
    } elseif (strpos($roast, 'dark') !== false) {
        return 'Medium-light to dark';
    } elseif (strpos($roast, 'very-light') !== false) {
        return 'Very-light to medium-light';
    } elseif ($roast === 'light') {
        return 'Light to medium';
    } elseif ($roast === 'medium') {
        return 'Medium-light to medium-high';
    } elseif ($roast === 'high') {
        return 'Medium-light to medium-high';
    } elseif ($roast === 'very high') {
        return 'Medium-light to dark';
    }
    return $roast;
}

if (isset($_GET['export']) && $_GET['export'] === 'csv') {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="riwayat_rekomendasi.csv"');
    $output = fopen('php://output', 'w');
    fputcsv($output, ['Waktu', 'Jenis Kopi', 'Metode Penyajian','Proses', 'Profil Rasa', 'Rekomendasi', 'Aturan']);
    foreach ($history as $item) {
        fputcsv($output, [
            $item->created_at,
            $item->jenis_kopi,
            $item->metode_penyajian,
            $item->proses,
            $item->profil_rasa,
            $item->recommended_roast,
            $item->rule_applied
        ]);
    }
    fclose($output);
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title><?= $page_title ?></title>
    <link rel="stylesheet" href="/project/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
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
        .history-card {
            background: #fff;
            border-radius: 10px;
            padding: 24px;
            margin-bottom: 24px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.04);
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
            color: #a86b2d;
        }
        .history-table tr:nth-child(even) {
            background: #faf6f2;
        }
        .history-table tr:hover {
            background: #f1e7db;
            transition: background 0.2s;
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
    </style>
</head>
<body>
<div class="user-layout">
    <?php include '../includes/sidebar.php'; ?>
    <div class="user-content">
        <div class="history-card">
            <h3><i class="fas fa-history"></i> Riwayat Rekomendasi</h3>
            <a href="?export=csv" class="btn btn-brown" style="margin-bottom:16px;"><i class="fas fa-file-csv"></i> Export CSV</a>
            <table class="history-table">
                <thead>
                    <tr>
                        <th>Waktu</th>
                        <th>Jenis Kopi</th>
                        <th>Metode Penyajian</th>
                        <th>Proses</th>
                        <th>Profil Rasa</th>
                        <th>Rekomendasi</th>
                        <th>Aturan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($history as $item): ?>
                    <tr>
                        <td><?= htmlspecialchars($item->created_at) ?></td>
                        <td><?= htmlspecialchars($item->jenis_kopi) ?></td>
                        <td><?= htmlspecialchars($item->metode_penyajian) ?></td>
                        <td><?= htmlspecialchars($item->proses) ?></td>
                        <td><?= htmlspecialchars($item->profil_rasa) ?></td>
                        <td><?= htmlspecialchars($item->recommended_roast) ?></td>
                        <td><?= htmlspecialchars($item->rule_applied) ?></td>
                        <td>
                            <button class="btn btn-brown btn-sm" onclick="showRoastingModal('<?= htmlspecialchars(mapRoastLevel($item->recommended_roast)) ?>')">
                                <i class="fas fa-fire"></i> Lihat Proses Roasting
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <!-- Modal Proses Roasting -->
        <div id="roasting-modal" class="modal" style="display:none;">
            <div class="modal-content">
                <span class="close" onclick="closeRoastingModal()">&times;</span>
                <div id="roasting-modal-body"></div>
            </div>
        </div>
    </div>
</div>
<style>
.btn-brown {
    background: #a86b3c;
    color: #fff;
    border: none;
    border-radius: 5px;
    padding: 8px 16px;
    font-weight: 600;
    cursor: pointer;
    transition: background 0.2s;
}
.btn-brown:hover {
    background: #7b4a1e;
}
.modal {
    position: fixed; left: 0; top: 0; width: 100vw; height: 100vh;
    background: rgba(0,0,0,0.3); display: flex; align-items: center; justify-content: center; z-index: 9999;
}
.modal-content {
    background: #fff; border-radius: 12px; padding: 32px 36px; max-width: 500px; width: 90%; position: relative;
    box-shadow: 0 2px 12px rgba(47,27,20,0.12);
}
.close {
    position: absolute; top: 12px; right: 18px; font-size: 1.8rem; color: #a86b3c; cursor: pointer;
}
</style>
<script>
function showRoastingModal(roast) {
    var modal = document.getElementById('roasting-modal');
    var body = document.getElementById('roasting-modal-body');
    body.innerHTML = '<p>Loading...</p>';
    modal.style.display = 'flex';
    fetch('/project/proses_roasting.php?ajax=1&roast=' + encodeURIComponent(roast))
        .then(res => res.text())
        .then(html => { body.innerHTML = html; });
}
function closeRoastingModal() {
    document.getElementById('roasting-modal').style.display = 'none';
}
</script>
</body>
</html>