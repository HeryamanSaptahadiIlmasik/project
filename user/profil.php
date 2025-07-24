<?php
require_once '../config/init.php';

if (!isLoggedIn()) { redirect('../login.php'); }
if (isAdmin()) { redirect('../admin/dashboard.php'); }

$id = $_SESSION['user_id'];
$db->query("SELECT * FROM users WHERE id = :id");
$db->bind(':id', $id);
$user = $db->single();
$inisial = strtoupper(substr($user->username, 0, 1));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Saya</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/project/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
      body { font-family: 'Poppins', sans-serif; background: linear-gradient(to right, #f8f3ef, #fffaf5); margin: 0; padding: 0; }
      .user-layout { display: flex; min-height: 100vh; }
      .user-content { margin-left: 220px; width: 100%; min-height: 100vh; box-sizing: border-box; display: flex; flex-direction: column; align-items: stretch; justify-content: flex-start; }
      .profile-title { color: #a86b3c; font-size: 2.5rem; font-weight: bold; margin: 48px 0 36px 0; letter-spacing: 1px; text-align: left; }
      .profile-main { display: flex; gap: 48px; background: #fff; border-radius: 24px; box-shadow: 0 8px 32px rgba(168,107,60,0.13); padding: 56px 64px; max-width: 1100px; width: 100%; margin: 0 auto; align-items: center; }
      .profile-avatar { width: 140px; height: 140px; background: #b57f50; color: #fff; font-size: 4.2rem; font-weight: bold; border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 8px rgba(168,107,60,0.10); margin: 0; }
      .profile-info { flex: 1; text-align: left; }
      .profile-info p { margin: 18px 0; font-size: 1.18em; }
      .profile-info strong { width: 140px; display: inline-block; color: #a86b3c; }
      .profile-actions { display: flex; gap: 18px; margin-top: 32px; }
      .profile-actions a { padding: 12px 28px; border: none; border-radius: 8px; cursor: pointer; font-weight: bold; font-size: 1.08em; text-decoration: none; transition: 0.3s; display: flex; align-items: center; gap: 8px; }
      .btn-edit { background: #28a745; color: white; }
      .btn-edit:hover { background: #218838; }
      .btn-password { background: #c77d41; color: white; }
      .btn-password:hover { background: #a35f28; }
      .btn-logout { background: #dc3545; color: white; }
      .btn-logout:hover { background: #bd2130; }
      @media (max-width: 1100px) {
        .profile-main { flex-direction: column; gap: 24px; padding: 24px 8px; }
        .profile-title { font-size: 2rem; margin: 24px 0 18px 0; }
        .profile-avatar { width: 90px; height: 90px; font-size: 2.2rem; }
      }
      @media (max-width: 700px) {
        .user-content { margin-left: 0; padding: 0 0 24px 0; }
        .profile-main { padding: 12px 2vw; }
      }
    </style>
</head>
<body>
<div class="user-layout">
    <?php include '../includes/sidebar.php'; ?>
    <div class="user-content">
      <div class="profile-title">Profil Saya</div>
      <div class="profile-main">
        <div class="profile-avatar"><?= $inisial ?></div>
        <div class="profile-info">
          <p><strong>Nama Lengkap</strong> : <?= htmlspecialchars($user->nama_lengkap ?? $user->username) ?></p>
          <p><strong>Username</strong> : <?= htmlspecialchars($user->username) ?></p>
          <p><strong>Email</strong> : <?= htmlspecialchars($user->email) ?></p>
          <p><strong>Alamat</strong> : <?= htmlspecialchars($user->alamat ?? '-') ?></p>
          <p><strong>No HP</strong> : <?= htmlspecialchars($user->no_hp ?? '-') ?></p>
          <div class="profile-actions">
            <a href="edit_profil.php" class="btn-edit"><i class="fas fa-edit"></i> Edit Profil</a>
            <a href="reset_password.php" class="btn-password"><i class="fas fa-key"></i> Ubah Password</a>
            <a href="/project/logout.php" class="btn-logout"><i class="fas fa-sign-out-alt"></i> Logout</a>
          </div>
        </div>
      </div>
    </div>
</div>
</body>
</html> 