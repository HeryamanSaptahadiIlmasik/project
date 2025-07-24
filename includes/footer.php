<footer class="footer">
        <div class="footer-container">
            <div class="footer-content">
                <div class="footer-brand">
                    <h3><i class="fas fa-coffee"></i> Triak Coffee & Roaster</h3>
                    <p>Membuat secangkir kopi sempurna, satu roasting pada satu waktu.</p>
                </div>
                <div class="footer-links">
                    <div class="footer-section">
                        <h4>Akun</h4>
                        <?php if (isLoggedIn()): ?>
                            <a href="<?php echo isAdmin() ? 'admin/dashboard.php' : 'user/dashboard.php'; ?>">Dashboard</a>
                        <?php else: ?>
                            <a href="login.php">Masuk</a>
                        <?php endif; ?>
                    </div>
                    <div class="footer-section">
                        <h4>Kontak</h4>
                        <p><i class="fas fa-envelope"></i> info@triakcoffee.com</p>
                        <p><i class="fas fa-phone"></i> (555) 123-4567</p>
                    </div>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2025 Triak Coffee & Roaster. Hak cipta dilindungi.</p>
            </div>
        </div>
    </footer>

    <script src="js/script.js"></script>
</body>
</html>