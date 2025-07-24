<?php
require_once '../config/init.php';
if (!isLoggedIn() || !isAdmin()) {
    redirect('../login.php');
}

// Ambil data admin dari session atau database
$user_id = $_SESSION['user_id'];
$db->query("SELECT username, email, created_at FROM users WHERE id = :id");
$db->bind(':id', $user_id);
$admin = $db->single();
$page_title = 'Profil Admin';

// Proses ganti password
$pw_success = '';
$pw_error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    $new_password = trim($_POST['new_password'] ?? '');
    $confirm_password = trim($_POST['confirm_password'] ?? '');
    if (strlen($new_password) < 6) {
        $pw_error = 'Password minimal 6 karakter.';
    } elseif ($new_password !== $confirm_password) {
        $pw_error = 'Konfirmasi password tidak cocok.';
    } else {
        $hashed = password_hash($new_password, PASSWORD_DEFAULT);
        $db->query("UPDATE users SET password = :password WHERE id = :id");
        $db->bind(':password', $hashed);
        $db->bind(':id', $user_id);
        if ($db->execute()) {
            $pw_success = 'Password berhasil diganti!';
        } else {
            $pw_error = 'Gagal mengganti password.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title><?= $page_title ?></title>
    <link rel="stylesheet" href="/project/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* Layout modern profile admin langsung di sini */
        .profile-wrapper {
          display: flex;
          flex-direction: column;
          align-items: center;
          justify-content: center;
          min-height: 90vh;
          padding-top: 0;
        }
        .modern-profile-card {
          position: relative;
          background: linear-gradient(135deg, #fff 70%, #f8f5f2 100%);
          border-radius: 28px;
          box-shadow: 0 8px 32px #0002;
          padding: 80px 64px 56px 64px;
          max-width: 480px;
          width: 100%;
          margin-bottom: 32px;
          text-align: center;
          display: flex;
          flex-direction: column;
          align-items: center;
        }
        .modern-avatar {
          position: absolute;
          top: -70px;
          left: 50%;
          transform: translateX(-50%);
          font-size: 8rem;
          width: 150px;
          height: 150px;
          background: linear-gradient(135deg, #a86b3c 60%, #8b4513 100%);
          color: #fff;
          border-radius: 50%;
          display: flex;
          align-items: center;
          justify-content: center;
          box-shadow: 0 2px 12px #0002;
          border: 8px solid #fff;
        }
        .modern-profile-info {
          margin-top: 90px;
          margin-bottom: 32px;
          display: flex;
          flex-direction: column;
          gap: 18px;
          width: 100%;
          align-items: flex-start;
        }
        .profile-row {
          font-size: 1.18em;
          display: flex;
          align-items: center;
          gap: 10px;
          justify-content: flex-start;
        }
        .profile-label {
          font-weight: 600;
          color: #2f1b14;
          min-width: 110px;
        }
        .profile-value {
          color: #654321;
          font-weight: 400;
          text-align: left;
          flex: 1;
        }
        .profile-icon {
          color: #a86b3c;
          font-size: 1.3em;
          width: 22px;
          text-align: center;
        }
        .profile-actions, .modern-actions {
          display: flex;
          justify-content: center;
          gap: 48px;
          margin-top: 40px;
        }
        .btn-pill {
          font-size: 1.18em;
          padding: 18px 48px;
          border: none;
          border-radius: 999px;
          font-weight: 600;
          font-family: 'Poppins', 'Inter', Arial, sans-serif;
          margin: 0 4px;
          cursor: pointer;
          box-shadow: 0 2px 8px #0001;
          transition: background 0.2s, transform 0.15s, box-shadow 0.2s;
          display: inline-flex;
          align-items: center;
          gap: 8px;
        }
        .btn-pill.btn-warning {
          background: #f0ad4e;
          color: #fff;
        }
        .btn-pill.btn-warning:hover {
          background: #e08e00;
        }
        .btn-pill.btn-danger {
          background: #d9534f;
          color: #fff;
        }
        .btn-pill.btn-danger:hover {
          background: #b52a37;
        }
        .btn-brown {
          background: #8b4513;
          color: #fff;
          font-size: 1.18em;
          padding: 18px 48px;
          border: none;
          border-radius: 999px;
          font-weight: 600;
          font-family: 'Poppins', 'Inter', Arial, sans-serif;
          margin: 0 4px;
          cursor: pointer;
          box-shadow: 0 2px 8px #0001;
          transition: background 0.2s, transform 0.15s, box-shadow 0.2s;
          display: inline-flex;
          align-items: center;
          gap: 8px;
        }
        .btn-brown:hover {
          background: #6d340c;
        }
        @media (max-width: 700px) {
          .modern-profile-card {
            max-width: 98vw;
            padding: 32px 4vw 24px 4vw;
          }
          .modern-profile-info {
            margin-top: 70px;
          }
          .modern-avatar {
            font-size: 4rem;
            width: 80px;
            height: 80px;
            top: -40px;
            border-width: 4px;
          }
          .profile-actions, .modern-actions {
            flex-direction: column;
            gap: 16px;
          }
        }
    </style>
</head>
<body>
<div class="admin-layout">
    <?php include '../includes/admin_sidebar.php'; ?>
    <div class="admin-content">
        <div class="profile-wrapper">
            <div class="profile-card modern-profile-card">
                <div class="profile-avatar modern-avatar">
                    <i class="fas fa-user-circle"></i>
                </div>
                <div class="modern-profile-info">
                    <div class="profile-row"><span class="profile-icon"><i class="fas fa-user"></i></span><span class="profile-label">Username</span><span class="profile-sep">:</span><span class="profile-value"><?= htmlspecialchars($admin->username) ?></span></div>
                    <div class="profile-row"><span class="profile-icon"><i class="fas fa-envelope"></i></span><span class="profile-label">Email</span><span class="profile-sep">:</span><span class="profile-value"><?= htmlspecialchars($admin->email) ?></span></div>
                    <div class="profile-row"><span class="profile-icon"><i class="fas fa-clock"></i></span><span class="profile-label">Bergabung</span><span class="profile-sep">:</span><span class="profile-value"><?= date('d M Y', strtotime($admin->created_at)) ?></span></div>
                    <div class="profile-row"><span class="profile-icon"><i class="fas fa-id-badge"></i></span><span class="profile-label">ID Admin</span><span class="profile-sep">:</span><span class="profile-value"><?= $user_id ?></span></div>
                </div>
                <?php if ($pw_success): ?><div class="alert-success"> <?= $pw_success ?> </div><?php endif; ?>
                <?php if ($pw_error): ?><div class="alert-error"> <?= $pw_error ?> </div><?php endif; ?>
                <form method="post" class="pw-form modern-form" id="pw-form" autocomplete="off" style="display:none;">
                    <h3 class="form-title">Ganti Password</h3>
                    <div class="form-group">
                        <label for="new_password">Password Baru</label>
                        <input type="password" id="new_password" name="new_password" minlength="6" required>
                    </div>
                    <div class="form-group">
                        <label for="confirm_password">Konfirmasi Password Baru</label>
                        <input type="password" id="confirm_password" name="confirm_password" minlength="6" required>
                    </div>
                    <div class="form-actions">
                        <button type="submit" name="change_password" class="btn-brown">Ganti Password</button>
                    </div>
                </form>
            </div>
            <div class="profile-actions modern-actions">
                <button id="show-pw-form" class="btn-brown"><i class="fas fa-key"></i> Ganti Password</button>
                <form action="/project/logout.php" method="post" style="display:inline;">
                    <button type="submit" class="btn-pill btn-danger"><i class="fas fa-sign-out-alt"></i> Logout</button>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
document.getElementById('show-pw-form').onclick = function() {
    document.getElementById('pw-form').style.display = 'block';
    this.style.display = 'none';
};
document.getElementById('cancel-pw') && (document.getElementById('cancel-pw').onclick = function() {
    document.getElementById('pw-form').style.display = 'none';
    document.getElementById('show-pw-form').style.display = 'inline-block';
});
</script>
</body>
</html> 