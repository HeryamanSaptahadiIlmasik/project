<style>
.sidebar {
    width: 240px;
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
    overflow-y: auto;
}
.sidebar .sidebar-brand {
    font-size: 1.5rem;
    font-weight: bold;
    padding: 32px 24px 24px 24px;
    display: flex;
    align-items: center;
    gap: 10px;
    border-bottom: 1px solid #4e342e;
}
.sidebar .sidebar-nav {
    flex: 1;
    display: flex;
    flex-direction: column;
    padding: 24px 0;
}
.sidebar .sidebar-link {
    color: #fff;
    text-decoration: none;
    padding: 14px 32px;
    font-size: 1.1rem;
    display: flex;
    align-items: center;
    gap: 12px;
    transition: background 0.2s;
}
.sidebar .sidebar-link.active, .sidebar .sidebar-link:hover {
    background: #4e342e;
}
.sidebar .sidebar-footer {
    padding: 18px 24px;
    font-size: 0.95rem;
    border-top: 1px solid #4e342e;
    color: #bdbdbd;
}
@media (max-width: 900px) {
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
        padding: 12px 10px;
        font-size: 1rem;
    }
}
</style>
<aside class="sidebar">
    <div class="sidebar-brand">
        <i class="fas fa-coffee"></i> Triak Coffee Admin
    </div>
    <nav class="sidebar-nav">
        <a href="/project/admin/dashboard.php" class="sidebar-link<?= basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? ' active' : '' ?>">
            <i class="fas fa-tachometer-alt"></i> Dashboard
        </a>
        <a href="/project/admin/manage_users.php" class="sidebar-link<?= basename($_SERVER['PHP_SELF']) == 'manage_users.php' ? ' active' : '' ?>">
            <i class="fas fa-users"></i> Kelola Pengguna
        </a>
        <a href="/project/admin/manage_coffees.php" class="sidebar-link<?= basename($_SERVER['PHP_SELF']) == 'manage_coffees.php' ? ' active' : '' ?>">
            <i class="fas fa-mug-hot"></i> Kelola Kopi
        </a>
        <a href="/project/admin/manage_rules.php" class="sidebar-link<?= basename($_SERVER['PHP_SELF']) == 'manage_rules.php' ? ' active' : '' ?>">
            <i class="fas fa-cogs"></i> Kelola Aturan Rekomendasi
        </a>
        <a href="/project/admin/proses_roasting_crud.php" class="sidebar-link<?= basename($_SERVER['PHP_SELF']) == 'proses_roasting_crud.php' ? ' active' : '' ?>">
            <i class="fas fa-fire"></i> Proses Roasting
        </a>
        <a href="/project/admin/riwayat_roasting_admin.php" class="sidebar-link<?= basename($_SERVER['PHP_SELF']) == 'riwayat_roasting_admin.php' ? ' active' : '' ?>">
            <i class="fas fa-history"></i> Riwayat Rekomendasi
        </a>
        <a href="/project/admin/profile.php" class="sidebar-link<?= basename($_SERVER['PHP_SELF']) == 'profile.php' ? ' active' : '' ?>">
            <i class="fas fa-user"></i> Profile
        </a>
    </nav>
    <div class="sidebar-footer">
        &copy; <?= date('Y') ?> Triak Coffee Admin
    </div>
</aside>

