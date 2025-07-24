<?php
require_once '../config/init.php';

if (!isLoggedIn() || !isAdmin()) {
    redirect('../login.php');
}

// Query user, silakan sesuaikan jika perlu
$db->query("SELECT u.id, u.username, u.email, u.created_at, 
    (SELECT MAX(created_at) FROM recommendations r WHERE r.user_id = u.id) as last_activity
    FROM users u WHERE u.role = 'user' ORDER BY u.created_at DESC");
$users = $db->resultset();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kelola Pengguna</title>
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
        .admin-table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .admin-table th, .admin-table td { border: 1px solid #eee; padding: 10px 14px; }
        .admin-table th { background: #f7f7f7; }
        .btn { padding: 6px 14px; border: none; border-radius: 4px; cursor: pointer; }
        .btn-view { background: #17a2b8; color: #fff; }
        .btn-delete { background: #dc3545; color: #fff; }
        .btn-view:hover { background: #138496; }
        .btn-delete:hover { background: #b52a37; }
        h2 { margin-top: 0; }
        
    </style>
</head>
<body>
<div class="admin-layout">
    <?php include '../includes/admin_sidebar.php'; ?>
    <div class="admin-content">
        <div class="history-card">
            <h2>Kelola Pengguna</h2>
            <p>Lihat dan kelola pengguna terdaftar</p>
            <a href="add_user.php" class="btn-brown btn-sm" style="margin-bottom: 16px; display: inline-block;"><i class="fas fa-user-plus"></i> Tambah Pengguna</a>
            <table class="history-table">
            <thead>
                <tr>
                        <th>Pengguna</th>
                    <th>Email</th>
                        <th>Bergabung</th>
                        <th>Aktivitas Terakhir</th>
                        <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                    <?php foreach ($users as $user): ?>
                <tr>
                    <td>
                        <?= htmlspecialchars($user->username) ?><br>
                        <small>ID: <?= $user->id ?></small>
                    </td>
                    <td><?= htmlspecialchars($user->email) ?></td>
                    <td><?= date('M j, Y', strtotime($user->created_at)) ?></td>
                    <td><?= $user->last_activity ? date('M j, Y', strtotime($user->last_activity)) : '-' ?></td>
                    <td>
                            <a href="reset_password.php?id=<?= $user->id ?>" class="btn-brown btn-sm"><i class="fas fa-key"></i> Reset Password</a>
                            <a href="delete_user.php?id=<?= $user->id ?>" class="btn-danger btn-sm" onclick="return confirm('Yakin hapus pengguna ini?')"><i class="fas fa-trash"></i> Hapus</a>
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