<?php
require_once '../config/init.php';

if (!isLoggedIn() || !isAdmin()) {
    redirect('../login.php');
}

// Ambil user id
$user_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($user_id <= 0) {
    die('User ID tidak valid.');
}

// Ambil data user
$db->query("SELECT * FROM users WHERE id = :id");
$db->bind(':id', $user_id);
$user = $db->single();
if (!$user) {
    die('User tidak ditemukan.');
}

$success = false;
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_password = trim($_POST['new_password'] ?? '');
    $confirm_password = trim($_POST['confirm_password'] ?? '');
    if (strlen($new_password) < 6) {
        $error = 'Password minimal 6 karakter.';
    } elseif ($new_password !== $confirm_password) {
        $error = 'Konfirmasi password tidak cocok.';
    } else {
        $hashed = password_hash($new_password, PASSWORD_DEFAULT);
        $db->query("UPDATE users SET password = :password WHERE id = :id");
        $db->bind(':password', $hashed);
        $db->bind(':id', $user_id);
        if ($db->execute()) {
            $success = true;
        } else {
            $error = 'Gagal reset password.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Reset Password User</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { background: #faf8f6; font-family: 'Inter', Arial, sans-serif; }
        .reset-container { max-width: 400px; margin: 60px auto; background: #fff; border-radius: 12px; box-shadow: 0 2px 12px rgba(47,27,20,0.08); padding: 32px 28px; }
        h2 { margin-top: 0; color: #a86b3c; }
        .form-group { margin-bottom: 18px; }
        label { display: block; margin-bottom: 6px; color: #7b4a1e; }
        input[type="password"] { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px; font-size: 1rem; }
        .btn { background: #a86b3c; color: #fff; border: none; padding: 10px 22px; border-radius: 6px; font-size: 1rem; cursor: pointer; }
        .btn:hover { background: #7b4a1e; }
        .alert-success { background: #eafbe7; color: #2e7d32; padding: 10px 14px; border-radius: 6px; margin-bottom: 16px; }
        .alert-error { background: #fdecea; color: #c0392b; padding: 10px 14px; border-radius: 6px; margin-bottom: 16px; }
        .back-link { display: inline-block; margin-top: 18px; color: #a86b3c; text-decoration: none; }
        .back-link:hover { text-decoration: underline; }
    </style>
</head>
<body>
<div class="reset-container">
    <h2><i class="fas fa-key"></i> Reset Password</h2>
    <p>Reset password untuk user: <strong><?= htmlspecialchars($user->username) ?></strong> (ID: <?= $user->id ?>)</p>
    <?php if ($success): ?>
        <div class="alert-success">Password berhasil direset!</div>
        <a href="manage_users.php" class="back-link"><i class="fas fa-arrow-left"></i> Kembali ke Manage Users</a>
    <?php else: ?>
        <?php if ($error): ?>
            <div class="alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <form method="post">
            <div class="form-group">
                <label>Password Baru</label>
                <input type="password" name="new_password" required minlength="6">
            </div>
            <div class="form-group">
                <label>Konfirmasi Password</label>
                <input type="password" name="confirm_password" required minlength="6">
            </div>
            <button type="submit" class="btn"><i class="fas fa-save"></i> Reset Password</button>
        </form>
        <a href="manage_users.php" class="back-link"><i class="fas fa-arrow-left"></i> Batal</a>
    <?php endif; ?>
</div>
</body>
</html> 