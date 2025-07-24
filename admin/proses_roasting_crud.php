<?php
require_once '../config/init.php';
if (!isLoggedIn() || !isAdmin()) redirect('../login.php');

// Handle aksi CRUD
$action = $_GET['action'] ?? '';
$id = $_GET['id'] ?? '';

// Tambah/Edit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $roast_level = $_POST['roast_level'];
    $density_level = $_POST['density_level'];
    $apparent_density = $_POST['apparent_density'];
    $initial_temp = $_POST['initial_temp'];
    $final_temp = $_POST['final_temp'];
    $agtron = $_POST['agtron'];
    if ($action === 'edit' && $id) {
        $db->query("UPDATE proses_roasting SET density_level=:density, apparent_density=:ad, initial_temp=:init, final_temp=:final, agtron=:agtron WHERE roast_level=:roast");
        $db->bind(':density', $density_level);
        $db->bind(':ad', $apparent_density);
        $db->bind(':init', $initial_temp);
        $db->bind(':final', $final_temp);
        $db->bind(':agtron', $agtron);
        $db->bind(':roast', $roast_level);
        $db->execute();
        header('Location: proses_roasting_crud.php'); exit;
    } else {
        $db->query("INSERT INTO proses_roasting (roast_level, density_level, apparent_density, initial_temp, final_temp, agtron) VALUES (:roast, :density, :ad, :init, :final, :agtron)");
        $db->bind(':roast', $roast_level);
        $db->bind(':density', $density_level);
        $db->bind(':ad', $apparent_density);
        $db->bind(':init', $initial_temp);
        $db->bind(':final', $final_temp);
        $db->bind(':agtron', $agtron);
        $db->execute();
        header('Location: proses_roasting_crud.php'); exit;
    }
}
// Hapus
if ($action === 'delete' && $id) {
    $db->query("DELETE FROM proses_roasting WHERE roast_level=:roast");
    $db->bind(':roast', $id);
    $db->execute();
    header('Location: proses_roasting_crud.php'); exit;
}
// Ambil data untuk edit
$edit_data = null;
if ($action === 'edit' && $id) {
    $db->query("SELECT * FROM proses_roasting WHERE roast_level=:roast");
    $db->bind(':roast', $id);
    $edit_data = $db->single();
}
// Ambil semua data
$db->query("SELECT * FROM proses_roasting ORDER BY roast_level");
$all = $db->resultset();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Proses Roasting</title>
    <link rel="stylesheet" href="/project/css/style.css">
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
        <h2> Proses Roasting</h2>
        <a href="proses_roasting_crud.php?action=add" class="btn btn-brown"><i class="fas fa-plus"></i> Tambah Proses Roasting</a>
        <div class="history-card">
            <table class="history-table">
            <thead>
                <tr>
                    <th>Roast Level</th>
                    <th>Density Level</th>
                    <th>Apparent Density</th>
                    <th>Suhu Awal</th>
                    <th>Suhu Akhir</th>
                    <th>Agtron</th>
                <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($all as $row): ?>
            <?php if ($action === 'edit' && $edit_data && $row->roast_level === $edit_data->roast_level): ?>
            <tr>
                <form method="post">
                    <td><input type="text" name="roast_level" value="<?= htmlspecialchars($edit_data->roast_level) ?>" readonly style="width:120px"></td>
                    <td><input type="text" name="density_level" value="<?= htmlspecialchars($edit_data->density_level) ?>" style="width:100px"></td>
                    <td><input type="text" name="apparent_density" value="<?= htmlspecialchars($edit_data->apparent_density) ?>" style="width:110px"></td>
                <td><input type="text" name="initial_temp" value="<?= htmlspecialchars($edit_data->initial_temp) ?>" style="width:70px"></td>
                <td><input type="text" name="final_temp" value="<?= htmlspecialchars($edit_data->final_temp) ?>" style="width:90px"></td>
                <td><input type="text" name="agtron" value="<?= htmlspecialchars($edit_data->agtron) ?>" style="width:80px"></td>
                <td>
                            <button type="submit" class="btn btn-brown">Simpan</button>
                    <a href="proses_roasting_crud.php" class="btn btn-secondary">Batal</a>
                    </td>
                </form>
            </tr>
            <?php else: ?>
            <tr>
                <td><?= htmlspecialchars($row->roast_level) ?></td>
                <td><?= htmlspecialchars($row->density_level) ?></td>
                <td><?= htmlspecialchars($row->apparent_density) ?></td>
            <td><?= htmlspecialchars($row->initial_temp) ?></td>
            <td><?= htmlspecialchars($row->final_temp) ?></td>
            <td><?= htmlspecialchars($row->agtron) ?></td>
            <td>
                        <a href="?action=edit&id=<?= urlencode($row->roast_level) ?>" class="btn btn-brown btn-sm">Edit</a>
                        <a href="?action=delete&id=<?= urlencode($row->roast_level) ?>" class="btn btn-danger btn-sm" onclick="return confirm('Hapus data ini?')">Hapus</a>
                </td>
            </tr>
            <?php endif; ?>
            <?php endforeach; ?>
            </tbody>
        </table>
        </div>
        <?php if ($action === 'add'): ?>
    <h3 style="color:#a86b3c;">Tambah Proses Roasting</h3>
    <form method="post" style="max-width:400px;">
            <label>Roast Level</label>
            <input name="roast_level" required>
            <label>Density Level</label>
            <input name="density_level" required>
            <label>Apparent Density</label>
            <input name="apparent_density" required>
            <label>Suhu Awal</label>
            <input name="initial_temp" required>
            <label>Suhu Akhir</label>
            <input name="final_temp" required>
            <label>Agtron</label>
            <input name="agtron" required>
        <button type="submit" class="btn btn-brown">Simpan</button>
        <a href="proses_roasting_crud.php" class="btn btn-secondary">Batal</a>
        </form>
        <?php endif; ?>
    </div>
</div>
</body>
</html> 