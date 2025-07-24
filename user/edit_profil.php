<?php
require_once '../config/init.php';
if (!isLoggedIn()) { redirect('../login.php'); }
if (isAdmin()) { redirect('../admin/dashboard.php'); }
$id = $_SESSION['user_id'];
$db->query("SELECT * FROM users WHERE id = :id");
$db->bind(':id', $id);
$user = $db->single();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Profil</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/project/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
      body { font-family: 'Poppins', sans-serif; background: linear-gradient(to right, #f8f3ef, #fffaf5); margin: 0; padding: 0; }
      .user-layout { display: flex; min-height: 100vh; }
      .user-content { margin-left: 220px; width: 100%; min-height: 100vh; box-sizing: border-box; display: flex; flex-direction: column; align-items: center; justify-content: flex-start; }
      .profile-card { width: 400px; margin: 50px auto 0 auto; background: white; border-radius: 16px; box-shadow: 0 8px 16px rgba(0,0,0,0.1); padding: 30px; text-align: center; }
      .profile-title { color: #a86b3c; font-size: 2.2rem; font-weight: bold; margin-bottom: 36px; }
      .profile-form { width: 100%; text-align: left; }
      .profile-form label { font-weight: 600; color: #a86b3c; margin-top: 18px; display: block; }
      .profile-form input, .profile-form textarea { width: 100%; padding: 12px; border-radius: 8px; border: 1.5px solid #e0c9b2; margin-top: 6px; font-size: 1.08em; margin-bottom: 10px; }
      .profile-actions { display: flex; gap: 18px; margin-top: 24px; width: 100%; justify-content: flex-end; }
      .btn-edit { background: #28a745; color: white; border: none; border-radius: 6px; padding: 10px 16px; font-weight: bold; transition: 0.3s; }
      .btn-edit:hover { background: #218838; }
      .btn-cancel { background: #e0c9b2; color: #a86b3c; border: none; border-radius: 6px; padding: 10px 16px; font-weight: bold; transition: 0.3s; }
      .btn-cancel:hover { background: #d2a86b; color: #7b4a1e; }
      @media (max-width: 900px) { .user-content { margin-left: 0; } .profile-card { width: 98vw; padding: 10px; } }
    </style>
</head>
<body>
<div class="user-layout">
    <?php include '../includes/sidebar.php'; ?>
    <div class="user-content">
        <div class="profile-card">
            <div class="profile-title">Edit Profil</div>
            <form class="profile-form" action="update_profile.php" method="POST">
                <label>Nama Lengkap</label>
                <input type="text" name="nama_lengkap" value="<?= htmlspecialchars($user->nama_lengkap ?? '') ?>" required>
                <label>Alamat</label>
                <textarea name="alamat"><?= htmlspecialchars($user->alamat ?? '') ?></textarea>
                <label>No HP</label>
                <input type="text" name="no_hp" value="<?= htmlspecialchars($user->no_hp ?? '') ?>">
                <div class="profile-actions">
                    <button type="submit" class="btn-edit"><i class="fas fa-save"></i> Simpan</button>
                    <a href="profil.php" class="btn-cancel"><i class="fas fa-arrow-left"></i> Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>
</body>
</html> 