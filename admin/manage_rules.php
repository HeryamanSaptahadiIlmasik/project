<?php
require_once '../config/init.php';

if (!isLoggedIn() || !isAdmin()) {
    redirect('../login.php');
}

// Ambil semua rule
$db->query("SELECT * FROM roasting_rules ORDER BY id ASC");
$rules = $db->resultset();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kelola Aturan Roasting</title>
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
        .dashboard-header h1 {
            margin-top: 0;
            font-size: 2.2rem;
            color: #2f1b14;
        }
        .dashboard-header p {
            color: #6d4c41;
            margin-bottom: 32px;
        }
        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 24px;
            margin-bottom: 32px;
        }
        .dashboard-card {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(47,27,20,0.06);
            padding: 24px;
            margin-bottom: 24px;
        }
        .dashboard-card h3 {
            margin-top: 0;
            color: #8b4513;
            font-size: 1.1rem;
            margin-bottom: 12px;
        }
        .stat-number {
            font-size: 2.1rem;
            font-weight: bold;
            color: #2f1b14;
        }
        .stat-label {
            color: #6d4c41;
            font-size: 0.98rem;
        }
        .quick-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 16px;
            margin-top: 12px;
        }
        .quick-actions .btn {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 10px 18px;
            border-radius: 6px;
            border: none;
            font-size: 1rem;
            cursor: pointer;
            text-decoration: none;
            background: #cd853f;
            color: #fff;
            transition: background 0.2s;
        }
        .quick-actions .btn-secondary {
            background: #8b4513;
        }
        .quick-actions .btn:hover {
            background: #a0522d;
        }

        .admin-table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .admin-table th, .admin-table td { border: 1px solid #eee; padding: 8px 12px; }
        .admin-table th { background: #f7f7f7; }
        .btn { padding: 6px 14px; border: none; border-radius: 4px; cursor: pointer; }
        .btn-edit { background: #ffc107; color: #fff; }
        .btn-delete { background: #dc3545; color: #fff; }
        .btn-add { background: #28a745; color: #fff; margin-bottom: 12px; }

        
                    
    </style>
</head>
<body>
<div class="admin-layout">
    <?php include '../includes/admin_sidebar.php'; ?>
    <div class="admin-content">
        <h2>Kelola Aturan Rekomendasi</h2>
        <a href="add_rule.php" class="btn btn-brown">Tambah Aturan</a>
        <div class="history-card">
            <table class="history-table">
            <thead>
                <tr>
                    <th>ID</th>
                        <th>Nama Aturan</th>
                    <th>Jenis Kopi</th>
                    <th>Metode Penyajian</th>
                    <th>Proses</th>
                    <th>Profil Rasa</th>
                    <th>Rekomendasi</th>
                        <th>Catatan</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($rules as $rule): ?>
                <tr>
                    <td><?= $rule->id ?></td>
                    <td><?= htmlspecialchars($rule->rule_name) ?></td>
                    <td><?= htmlspecialchars($rule->jenis_kopi) ?></td>
                    <td><?= htmlspecialchars($rule->metode_penyajian) ?></td>
                    <td><?= htmlspecialchars($rule->proses) ?></td>
                    <td><?= htmlspecialchars($rule->profil_rasa) ?></td>
                    <td><?= htmlspecialchars($rule->recommended_roast) ?></td>
                    <td><?= htmlspecialchars($rule->roasting_notes) ?></td>
                    <td>
                            <a href="edit_rule.php?id=<?= $rule->id ?>" class="btn btn-brown btn-sm">Edit</a>
                            <a href="delete_rule.php?id=<?= $rule->id ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin hapus aturan ini?')">Hapus</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        </div>
    </div>
</div>
</body>
</html>
