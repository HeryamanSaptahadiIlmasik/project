<?php
require_once 'config/init.php';
$page_title = 'Home';
include 'includes/header.php';
?>

<main>
    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-content">
            <h1>Sistem Manajemen & Rekomendasi Kopi Internal</h1>
            <p>Aplikasi ini digunakan oleh staff Triak Coffee & Roaster untuk mengelola data kopi, proses roasting, dan riwayat rekomendasi secara efisien.</p>
            <?php if (!isLoggedIn()): ?>
                <div class="hero-buttons">
                    <a href="login.php" class="btn btn-primary">Login Staff</a>
                </div>
            <?php else: ?>
                <div class="hero-buttons">
                    <a href="<?php echo isAdmin() ? 'admin/dashboard.php' : 'user/dashboard.php'; ?>" class="btn btn-primary">
                        <i class="fas fa-tachometer-alt"></i> Ke Dashboard
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <?php if (isLoggedIn()): ?>
    <!-- Quick Access Panel -->
    <section class="quick-access">
        <div class="container">
            <h2>Akses Cepat Fitur Utama</h2>
            <div class="quick-grid">
                <?php if (isAdmin()): ?>
                    <a href="admin/manage_users.php" class="quick-card">
                        <i class="fas fa-users"></i>
                        <span>Manajemen User</span>
                    </a>
                    <a href="admin/manage_coffees.php" class="quick-card">
                        <i class="fas fa-mug-hot"></i>
                        <span>Manajemen Kopi</span>
                    </a>
                    <a href="admin/manage_rules.php" class="quick-card">
                        <i class="fas fa-cogs"></i>
                        <span>Manajemen Aturan</span>
                    </a>
                    <a href="admin/proses_roasting_crud.php" class="quick-card">
                        <i class="fas fa-fire"></i>
                        <span>Proses Roasting</span>
                    </a>
                    <a href="admin/riwayat_roasting_admin.php" class="quick-card">
                        <i class="fas fa-history"></i>
                        <span>Riwayat Rekomendasi</span>
                    </a>
                <?php else: ?>
                    <a href="user/recommendation.php" class="quick-card">
                        <i class="fas fa-magic"></i>
                        <span>Buat Rekomendasi</span>
                    </a>
                    <a href="user/riwayat.php" class="quick-card">
                        <i class="fas fa-history"></i>
                        <span>Riwayat Rekomendasi</span>
                    </a>
                    <a href="user/update_preferences.php" class="quick-card">
                        <i class="fas fa-user-cog"></i>
                        <span>Preferensi Staff</span>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- Info Section -->
    <section class="info-section">
        <div class="container">
            <h2>Tentang Sistem</h2>
            <p style="max-width:700px;margin:0 auto;">Sistem ini dirancang khusus untuk staff Triak Coffee & Roaster. Semua fitur difokuskan untuk mendukung operasional internal, mulai dari pengelolaan data kopi, proses roasting, hingga pelaporan riwayat rekomendasi. Tidak ada fitur penjualan atau promosi ke customer.</p>
        </div>
    </section>

    <!-- Roast Level Info Section -->
    <section class="roast-info">
        <div class="container">
            <h2>Karakteristik Tingkat Kematangan Kopi</h2>
            <div class="roast-grid">
                <div class="roast-card">
                    <div class="roast-icon" style="background:linear-gradient(135deg,#f7b731,#fffbe7);"><i class="fas fa-seedling"></i></div>
                    <h3>Light Roast</h3>
                    <p>Asam lebih menonjol, aroma floral/fruit, warna biji coklat muda, rasa kopi lebih “origin”.</p>
                </div>
                <div class="roast-card">
                    <div class="roast-icon" style="background:linear-gradient(135deg,#e67e22,#f7b731);"><i class="fas fa-mug-hot"></i></div>
                    <h3>Medium Roast</h3>
                    <p>Keseimbangan asam dan pahit, rasa manis, aroma kacang/coklat, body sedang.</p>
                </div>
                <div class="roast-card">
                    <div class="roast-icon" style="background:linear-gradient(135deg,#23190f,#7b4a1e);"><i class="fas fa-fire"></i></div>
                    <h3>Dark Roast</h3>
                    <p>Pahit kuat, asam hampir hilang, aroma smoky/burnt, warna biji coklat tua, body berat.</p>
                </div>
            </div>
        </div>
    </section>
</main>

<?php include 'includes/footer.php'; ?>
<style>
.header {
    position: sticky;
    top: 0;
    z-index: 100;
    background: #7b4a1e;
    box-shadow: 0 2px 8px rgba(47,27,20,0.08);
    padding: 0;
}
.header .container {
    display: flex;
    align-items: center;
    justify-content: space-between;
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 24px;
    height: 64px;
}
.header .logo {
    font-size: 1.3rem;
    font-weight: bold;
    color: #f7b731;
    display: flex;
    align-items: center;
    gap: 8px;
}
.header nav a {
    color: #fff;
    text-decoration: none;
    margin-left: 24px;
    font-size: 1.08rem;
    transition: color 0.2s;
    padding: 8px 12px;
    border-radius: 6px;
}
.header nav a:hover, .header nav a.active {
    background: #a86b3c;
    color: #fffbe7;
}
.hero {
    position: relative;
    background: linear-gradient(rgba(139, 69, 19, 0.7), rgba(101, 67, 33, 0.7)),
                url('https://images.pexels.com/photos/894695/pexels-photo-894695.jpeg');
    background-size: cover;
    background-position: center;
    min-height: 60vh;
    display: flex;
    align-items: center;
    color: #fff;
    text-align: center;
    padding: 80px 0 64px 0;
    overflow: hidden;
}
.hero-content {
    max-width: 700px;
    margin: 0 auto;
    padding: 0 16px;
    animation: fadeIn 1.2s;
}
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(30px);}
    to { opacity: 1; transform: none;}
}
.hero h1 {
    font-size: clamp(2.2rem, 5vw, 3.2rem);
    font-weight: 700;
    margin-bottom: 22px;
    text-shadow: 2px 2px 8px rgba(0,0,0,0.4);
    line-height: 1.15;
    letter-spacing: 1px;
}
.hero p {
    font-size: 1.15rem;
    margin-bottom: 36px;
    opacity: 0.97;
    line-height: 1.6;
}
.hero-buttons {
    display: flex;
    gap: 18px;
    justify-content: center;
    flex-wrap: wrap;
    margin-bottom: 10px;
}
.hero-buttons .btn-primary {
    background: #f7b731;
    color: #23190f;
    border-radius: 8px;
    font-weight: 600;
    font-size: 1.08rem;
    padding: 12px 32px;
    box-shadow: 0 2px 8px rgba(47,27,20,0.10);
    transition: background 0.2s, color 0.2s, box-shadow 0.2s;
}
.hero-buttons .btn-primary:hover {
    background: #a86b3c;
    color: #fff;
    box-shadow: 0 4px 16px rgba(47,27,20,0.18);
}
.info-section {
    padding: 48px 0 24px 0;
    background: #faf8f6;
    margin-top: -32px;
    border-radius: 32px 32px 0 0;
    box-shadow: 0 -2px 8px rgba(47,27,20,0.04);
}
.info-section h2 {
    text-align: center;
    color: #a86b3c;
    margin-bottom: 18px;
    font-size: 1.2rem;
    margin-top: 18px;
}
.info-section p {
    text-align: center;
    color: #23190f;
    font-size: 1.08rem;
    line-height: 1.7;
    margin-top: 12px;
}
.roast-info {
    padding: 48px 0 36px 0;
    background: #fff;
}
.roast-info h2 {
    text-align: center;
    color: #a86b3c;
    margin-bottom: 36px;
    font-size: 1.3rem;
    letter-spacing: 0.5px;
}
.roast-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
    gap: 36px;
    max-width: 1100px;
    margin: 0 auto;
}
.roast-card {
    background: #fcf8f4;
    border-radius: 16px;
    box-shadow: 0 2px 8px rgba(47,27,20,0.07);
    padding: 36px 18px 28px 18px;
    text-align: center;
    color: #7b4a1e;
    font-size: 1.08rem;
    transition: box-shadow 0.2s, transform 0.2s, background 0.2s;
    display: flex;
    flex-direction: column;
    align-items: center;
}
.roast-icon {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 16px auto;
    color: #fff;
    font-size: 2rem;
    box-shadow: 0 2px 8px rgba(47,27,20,0.10);
}
.roast-card h3 {
    color: #a86b3c;
    margin-bottom: 12px;
    font-size: 1.13rem;
    font-weight: 600;
}
.roast-card:hover {
    background: #f3e7de;
    box-shadow: 0 4px 16px rgba(47,27,20,0.13);
    transform: translateY(-4px);
}
@media (max-width: 900px) {
    .roast-grid { grid-template-columns: 1fr 1fr; }
}
@media (max-width: 600px) {
    .roast-grid { grid-template-columns: 1fr; }
    .hero h1 { font-size: 1.3rem; }
    .hero { padding: 32px 0 24px 0; }
    .info-section { padding: 32px 0 16px 0; }
}
</style>