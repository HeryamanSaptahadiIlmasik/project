<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../config/init.php';
$id = $_SESSION['user_id'];
$db->query("SELECT * FROM users WHERE id = :id");
$db->bind(':id', $id);
$user = $db->single();
$inisial = strtoupper(substr($user->username, 0, 1));
?>
<!-- includes/sidebar.php -->
<aside class="sidebar">
    <div class="sidebar-brand">
        <i class="fas fa-coffee"></i> Triak Coffee
    </div>
    <nav class="sidebar-nav">
        <a href="/project/user/dashboard.php" class="sidebar-link<?= basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? ' active' : '' ?>">
            <i class="fas fa-tachometer-alt"></i> Dashboard
        </a>
        <a href="/project/user/recommendation.php" class="sidebar-link<?= basename($_SERVER['PHP_SELF']) == 'recommendation.php' ? ' active' : '' ?>">
            <i class="fas fa-eye"></i> Rekomendasi
        </a>
        <a href="/project/user/riwayat.php" class="sidebar-link<?= basename($_SERVER['PHP_SELF']) == 'riwayat.php' ? ' active' : '' ?>">
            <i class="fas fa-history"></i> Riwayat Rekomendasi
        </a>
        <a href="/project/user/profil.php" class="sidebar-link<?= basename($_SERVER['PHP_SELF']) == 'profil.php' ? ' active' : '' ?>">
            <i class="fas fa-user"></i> Profil
        </a>
    </nav>
    <div class="sidebar-footer">
        &copy; <?= date('Y') ?> Triak Coffee
    </div>
</aside>