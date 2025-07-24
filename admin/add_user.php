<?php
require_once '../config/init.php';

if (!isLoggedIn() || !isAdmin()) {
    redirect('../login.php');
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $email === '' || $password === '') {
        $error = 'Semua field wajib diisi!';
    } else {
        // Cek apakah email sudah terdaftar
        $db->query('SELECT id FROM users WHERE email = :email');
        $db->bind(':email', $email);
        $db->execute();
        if ($db->rowCount() > 0) {
            $error = 'Email sudah terdaftar!';
        } else {
            // Insert user baru
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $db->query('INSERT INTO users (username, email, password, role, created_at) VALUES (:username, :email, :password, "user", NOW())');
            $db->bind(':username', $username);
            $db->bind(':email', $email);
            $db->bind(':password', $hashed);
            if ($db->execute()) {
                $success = 'Pengguna berhasil ditambahkan!';
            } else {
                $error = 'Gagal menambah pengguna.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Pengguna</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
.admin-content {
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 100vh;
    padding: 0;
}
.history-card {
    width: 100%;
    max-width: 480px;
    margin: 0;
}
</style>
</head>
<body>
<div class="admin-layout">
    <?php include '../includes/admin_sidebar.php'; ?>
    <div class="admin-content">
        <div class="history-card">
            <h2>Tambah Pengguna</h2>
            <?php if ($error): ?><div class="alert alert-danger"><?= $error ?></div><?php endif; ?>
            <?php if ($success): ?><div class="alert alert-success"><?= $success ?></div><?php endif; ?>
            <form method="post">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" name="username" id="username" required>
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" name="email" id="email" required>
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" name="password" id="password" required>
                </div>
                <button type="submit" class="btn-brown btn-sm"><i class="fas fa-user-plus"></i> Tambah</button>
                <a href="manage_users.php" class="btn btn-danger btn-sm" style="margin-left: 8px;"><i class="fas fa-arrow-left"></i> Kembali</a>
            </form>
        </div>
    </div>
</div>
</body>
</html> 