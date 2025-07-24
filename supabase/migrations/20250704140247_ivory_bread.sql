-- =====================================================
-- TRIAK COFFEE & ROASTER DATABASE SETUP
-- Rule-Based Coffee Recommendation System
-- =====================================================

-- Create database
CREATE DATABASE IF NOT EXISTS `triak_coffee` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `triak_coffee`;

-- =====================================================
-- TABLE: users
-- Stores user accounts (admin and regular users)
-- =====================================================
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('user','admin') DEFAULT 'user',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLE: coffee_types
-- Stores coffee varieties with rule-based attributes
-- =====================================================
CREATE TABLE IF NOT EXISTS `coffee_types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `description` text,
  `jenis_kopi` enum('Arabika','Robusta') NOT NULL,
  `roast_level` enum('light','medium','medium-dark','dark') NOT NULL,
  `flavor_notes` text,
  `profil_rasa` enum('Fruity','Chocolate','Nutty','Earthy','Floral','Citrus','Caramel','Bitter Chocolate','Honey','Spicy','Bitter','Sweet') NOT NULL,
  `proses` enum('Washed','Honey','Natural') NOT NULL,
  `brewing_method` text,
  `origin` varchar(100) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLE: user_preferences
-- Stores user preferences for rule-based recommendations
-- =====================================================
CREATE TABLE IF NOT EXISTS `user_preferences` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `jenis_kopi` enum('Arabika','Robusta') DEFAULT NULL,
  `metode_penyajian` enum('V60','French Press','Espresso','Tubruk','Cold Brew','Moka Pot') DEFAULT NULL,
  `profil_rasa` enum('Fruity','Chocolate','Nutty','Earthy','Floral','Citrus','Caramel','Bitter Chocolate','Honey','Spicy','Bitter','Sweet') DEFAULT NULL,
  `proses` enum('Washed','Honey','Natural') DEFAULT NULL,
  `preferred_roast` enum('light','medium','medium-dark','dark') DEFAULT NULL,
  `preferred_brewing` varchar(100) DEFAULT NULL,
  `flavor_preference` text,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `user_preferences_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLE: recommendations
-- Stores user ratings, reviews, and rule-based recommendations
-- =====================================================
CREATE TABLE IF NOT EXISTS `recommendations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `coffee_id` int(11) DEFAULT NULL,
  `rating` int(11) DEFAULT '0',
  `review` text,
  `recommended_roast` enum('light','medium','medium-dark','dark') DEFAULT NULL,
  `rule_applied` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `coffee_id` (`coffee_id`),
  CONSTRAINT `recommendations_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `recommendations_ibfk_2` FOREIGN KEY (`coffee_id`) REFERENCES `coffee_types` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- INSERT DEFAULT ADMIN USER
-- Username: admin, Password: admin123
-- =====================================================
INSERT INTO `users` (`username`, `email`, `password`, `role`) VALUES
('admin', 'admin@triakcoffee.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

-- =====================================================
-- INSERT SAMPLE COFFEE DATA
-- Coffee varieties with rule-based attributes
-- =====================================================
INSERT INTO `coffee_types` (`name`, `description`, `jenis_kopi`, `roast_level`, `flavor_notes`, `profil_rasa`, `proses`, `brewing_method`, `origin`, `price`, `image_url`) VALUES
('Ethiopian Yirgacheffe', 'Bright and floral with citrus notes', 'Arabika', 'light', 'Citrus, Floral, Tea-like', 'Floral', 'Washed', 'V60, Pour-over, Aeropress', 'Ethiopia', 18.50, 'https://images.pexels.com/photos/894695/pexels-photo-894695.jpeg'),
('Colombian Supremo', 'Well-balanced with chocolate and caramel notes', 'Arabika', 'medium', 'Chocolate, Caramel, Nuts', 'Chocolate', 'Honey', 'French Press, Drip', 'Colombia', 16.00, 'https://images.pexels.com/photos/1695052/pexels-photo-1695052.jpeg'),
('Guatemalan Antigua', 'Full-bodied with smoky and spicy notes', 'Arabika', 'medium-dark', 'Smoky, Spicy, Dark Chocolate', 'Spicy', 'Honey', 'Espresso, French Press', 'Guatemala', 17.25, 'https://images.pexels.com/photos/1695052/pexels-photo-1695052.jpeg'),
('Sumatra Mandheling', 'Bold and earthy with herbal notes', 'Robusta', 'dark', 'Earthy, Herbal, Bold', 'Earthy', 'Natural', 'Tubruk, French Press', 'Indonesia', 15.75, 'https://images.pexels.com/photos/894695/pexels-photo-894695.jpeg'),
('Brazilian Santos', 'Nutty and sweet with caramel undertones', 'Arabika', 'medium', 'Nutty, Sweet, Caramel', 'Nutty', 'Honey', 'Moka Pot, Espresso', 'Brazil', 14.50, 'https://images.pexels.com/photos/1695052/pexels-photo-1695052.jpeg'),
('Java Robusta', 'Strong and bitter with earthy finish', 'Robusta', 'dark', 'Bitter, Strong, Earthy', 'Bitter', 'Natural', 'Espresso, Tubruk', 'Indonesia', 13.00, 'https://images.pexels.com/photos/894695/pexels-photo-894695.jpeg'),
('Kenya AA', 'Wine-like acidity with fruity notes', 'Arabika', 'light', 'Wine, Fruity, Bright', 'Fruity', 'Washed', 'V60, Pour-over', 'Kenya', 19.00, 'https://images.pexels.com/photos/894695/pexels-photo-894695.jpeg'),
('Costa Rica Tarrazú', 'Citrusy and bright with honey sweetness', 'Arabika', 'medium', 'Citrus, Honey, Bright', 'Citrus', 'Honey', 'V60, Aeropress', 'Costa Rica', 16.75, 'https://images.pexels.com/photos/1695052/pexels-photo-1695052.jpeg'),
('Panama Geisha', 'Floral and tea-like with honey notes', 'Arabika', 'light', 'Floral, Tea, Honey', 'Honey', 'Washed', 'V60, Pour-over', 'Panama', 25.00, 'https://images.pexels.com/photos/894695/pexels-photo-894695.jpeg'),
('Vietnam Robusta', 'Strong and earthy with sweet undertones', 'Robusta', 'medium', 'Strong, Earthy, Sweet', 'Sweet', 'Honey', 'Cold Brew, French Press', 'Vietnam', 12.50, 'https://images.pexels.com/photos/1695052/pexels-photo-1695052.jpeg'),
('Jamaica Blue Mountain', 'Mild and smooth with caramel notes', 'Arabika', 'medium', 'Mild, Smooth, Caramel', 'Caramel', 'Honey', 'Cold Brew, Drip', 'Jamaica', 35.00, 'https://images.pexels.com/photos/894695/pexels-photo-894695.jpeg'),
('Yemen Mocha', 'Wine-like with fruity and earthy notes', 'Arabika', 'medium', 'Wine, Fruity, Earthy', 'Fruity', 'Washed', 'Cold Brew, French Press', 'Yemen', 22.00, 'https://images.pexels.com/photos/1695052/pexels-photo-1695052.jpeg');

-- =====================================================
-- INSERT SAMPLE USER PREFERENCES
-- Example preferences for testing rule-based system
-- =====================================================
INSERT INTO `user_preferences` (`user_id`, `jenis_kopi`, `metode_penyajian`, `profil_rasa`, `proses`, `preferred_roast`, `flavor_preference`) VALUES
(1, 'Arabika', 'V60', 'Fruity', 'Washed', 'light', 'I prefer bright and acidic coffees with floral notes');

-- =====================================================
-- INSERT SAMPLE RECOMMENDATIONS
-- Example reviews and ratings with rule applications
-- =====================================================
INSERT INTO `recommendations` (`user_id`, `coffee_id`, `rating`, `review`, `recommended_roast`, `rule_applied`) VALUES
(1, 1, 5, 'Excellent coffee with bright citrus notes. Perfect for morning brewing with V60.', 'light', 'Rule 17'),
(1, 2, 4, 'Great balance of chocolate and caramel. Works well with French Press.', 'medium', 'Rule 2'),
(1, 4, 3, 'Too strong for my taste, but good quality Robusta.', 'dark', 'Rule 4');

-- =====================================================
-- CREATE INDEXES FOR BETTER PERFORMANCE
-- =====================================================
CREATE INDEX idx_coffee_jenis_profil ON coffee_types(jenis_kopi, profil_rasa);
CREATE INDEX idx_coffee_roast_level ON coffee_types(roast_level);
CREATE INDEX idx_preferences_user ON user_preferences(user_id);
CREATE INDEX idx_recommendations_user_coffee ON recommendations(user_id, coffee_id);
CREATE INDEX idx_recommendations_rating ON recommendations(rating);

-- =====================================================
-- CREATE VIEW FOR COFFEE STATISTICS
-- Useful for admin dashboard analytics
-- =====================================================
CREATE VIEW coffee_statistics AS
SELECT 
    ct.id,
    ct.name,
    ct.jenis_kopi,
    ct.roast_level,
    ct.profil_rasa,
    ct.proses,
    ct.price,
    COUNT(r.id) as total_reviews,
    AVG(r.rating) as average_rating,
    SUM(CASE WHEN r.rating >= 4 THEN 1 ELSE 0 END) as favorites_count
FROM coffee_types ct
LEFT JOIN recommendations r ON ct.id = r.coffee_id
GROUP BY ct.id;

-- =====================================================
-- CREATE VIEW FOR USER STATISTICS
-- Useful for admin user management
-- =====================================================
CREATE VIEW user_statistics AS
SELECT 
    u.id,
    u.username,
    u.email,
    u.created_at,
    COUNT(r.id) as total_reviews,
    AVG(r.rating) as average_rating,
    MAX(r.created_at) as last_review_date,
    up.jenis_kopi,
    up.metode_penyajian,
    up.profil_rasa,
    up.proses,
    up.preferred_roast
FROM users u
LEFT JOIN recommendations r ON u.id = r.user_id
LEFT JOIN user_preferences up ON u.id = up.user_id
WHERE u.role = 'user'
GROUP BY u.id;

-- =====================================================
-- CREATE STORED PROCEDURE FOR RULE-BASED RECOMMENDATION
-- =====================================================
DELIMITER //
CREATE PROCEDURE GetRuleBasedRecommendation(
    IN p_jenis_kopi ENUM('Arabika','Robusta'),
    IN p_metode_penyajian ENUM('V60','French Press','Espresso','Tubruk','Cold Brew','Moka Pot'),
    IN p_profil_rasa ENUM('Fruity','Chocolate','Nutty','Earthy','Floral','Citrus','Caramel','Bitter Chocolate','Honey','Spicy','Bitter','Sweet'),
    IN p_proses ENUM('Washed','Honey','Natural'),
    OUT p_recommended_roast ENUM('light','medium','medium-dark','dark'),
    OUT p_rule_applied VARCHAR(255)
)
BEGIN
    -- Rule 1: Arabika + V60 + Fruity + Washed → Light Roast
    IF p_jenis_kopi = 'Arabika' AND p_metode_penyajian = 'V60' AND p_profil_rasa = 'Fruity' AND p_proses = 'Washed' THEN
        SET p_recommended_roast = 'light';
        SET p_rule_applied = 'Rule 1';
    -- Rule 2: Arabika + French Press + Chocolate + Honey → Medium Roast
    ELSEIF p_jenis_kopi = 'Arabika' AND p_metode_penyajian = 'French Press' AND p_profil_rasa = 'Chocolate' AND p_proses = 'Honey' THEN
        SET p_recommended_roast = 'medium';
        SET p_rule_applied = 'Rule 2';
    -- Rule 3: Arabika + Espresso + Nutty + Natural → Medium Roast
    ELSEIF p_jenis_kopi = 'Arabika' AND p_metode_penyajian = 'Espresso' AND p_profil_rasa = 'Nutty' AND p_proses = 'Natural' THEN
        SET p_recommended_roast = 'medium';
        SET p_rule_applied = 'Rule 3';
    -- Rule 4: Robusta + Tubruk + Earthy + Natural → Dark Roast
    ELSEIF p_jenis_kopi = 'Robusta' AND p_metode_penyajian = 'Tubruk' AND p_profil_rasa = 'Earthy' AND p_proses = 'Natural' THEN
        SET p_recommended_roast = 'dark';
        SET p_rule_applied = 'Rule 4';
    -- Rule 5: Robusta + Cold Brew + Fruity + Honey → Medium Roast
    ELSEIF p_jenis_kopi = 'Robusta' AND p_metode_penyajian = 'Cold Brew' AND p_profil_rasa = 'Fruity' AND p_proses = 'Honey' THEN
        SET p_recommended_roast = 'medium';
        SET p_rule_applied = 'Rule 5';
    -- Add more rules as needed...
    -- Rule 17: Arabika + V60 + Floral + Washed → Light Roast
    ELSEIF p_jenis_kopi = 'Arabika' AND p_metode_penyajian = 'V60' AND p_profil_rasa = 'Floral' AND p_proses = 'Washed' THEN
        SET p_recommended_roast = 'light';
        SET p_rule_applied = 'Rule 17';
    -- Rule 19: Arabika + Espresso + Spicy + Honey → Medium-Dark Roast
    ELSEIF p_jenis_kopi = 'Arabika' AND p_metode_penyajian = 'Espresso' AND p_profil_rasa = 'Spicy' AND p_proses = 'Honey' THEN
        SET p_recommended_roast = 'medium-dark';
        SET p_rule_applied = 'Rule 19';
    -- Rule 20: Robusta + Cold Brew + Sweet + Honey → Medium Roast
    ELSEIF p_jenis_kopi = 'Robusta' AND p_metode_penyajian = 'Cold Brew' AND p_profil_rasa = 'Sweet' AND p_proses = 'Honey' THEN
        SET p_recommended_roast = 'medium';
        SET p_rule_applied = 'Rule 20';
    -- Default rule if no match
    ELSE
        SET p_recommended_roast = 'medium';
        SET p_rule_applied = 'Default Rule';
    END IF;
END //
DELIMITER ;

-- =====================================================
-- CREATE FUNCTION TO GET COFFEE MATCH SCORE
-- =====================================================
DELIMITER //
CREATE FUNCTION GetCoffeeMatchScore(
    coffee_jenis VARCHAR(10),
    coffee_profil VARCHAR(20),
    coffee_proses VARCHAR(10),
    coffee_roast VARCHAR(15),
    user_jenis VARCHAR(10),
    user_profil VARCHAR(20),
    user_proses VARCHAR(10),
    recommended_roast VARCHAR(15)
) RETURNS INT
READS SQL DATA
DETERMINISTIC
BEGIN
    DECLARE score INT DEFAULT 0;
    
    -- Perfect match for recommended roast and jenis
    IF coffee_roast = recommended_roast AND coffee_jenis = user_jenis THEN
        SET score = 100;
    -- Match recommended roast
    ELSEIF coffee_roast = recommended_roast THEN
        SET score = 90;
    -- Match jenis kopi
    ELSEIF coffee_jenis = user_jenis THEN
        SET score = 80;
    -- Match profil rasa
    ELSEIF coffee_profil = user_profil THEN
        SET score = 70;
    -- Match proses
    ELSEIF coffee_proses = user_proses THEN
        SET score = 60;
    -- Default score
    ELSE
        SET score = 50;
    END IF;
    
    RETURN score;
END //
DELIMITER ;

-- =====================================================
-- GRANT PERMISSIONS (Optional - for specific user)
-- Uncomment and modify if you want to create a specific database user
-- =====================================================
-- CREATE USER 'triak_user'@'localhost' IDENTIFIED BY 'triak_password';
-- GRANT ALL PRIVILEGES ON triak_coffee.* TO 'triak_user'@'localhost';
-- FLUSH PRIVILEGES;

-- =====================================================
-- SHOW TABLES AND SAMPLE DATA
-- =====================================================
SHOW TABLES;

SELECT 'Database setup completed successfully!' as Status;
SELECT COUNT(*) as 'Total Users' FROM users;
SELECT COUNT(*) as 'Total Coffee Types' FROM coffee_types;
SELECT COUNT(*) as 'Total Preferences' FROM user_preferences;
SELECT COUNT(*) as 'Total Recommendations' FROM recommendations;

-- =====================================================
-- END OF DATABASE SETUP
-- =====================================================