<?php
session_start();

// Include database
require_once 'database.php';

// Initialize database
$db = new Database();

// Create tables if they don't exist
$db->query("CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('user', 'admin') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");
$db->execute();

$db->query("CREATE TABLE IF NOT EXISTS coffee_types (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    jenis_kopi ENUM('Arabika', 'Robusta') NOT NULL,
    roast_level ENUM('light', 'medium', 'medium-dark', 'dark') NOT NULL,
    flavor_notes TEXT,
    profil_rasa ENUM('Fruity', 'Chocolate', 'Nutty', 'Earthy', 'Floral', 'Citrus', 'Caramel', 'Bitter Chocolate', 'Honey', 'Spicy', 'Bitter', 'Sweet') NOT NULL,
    proses ENUM('Washed', 'Honey', 'Natural') NOT NULL,
    brewing_method TEXT,
    origin VARCHAR(100),
    price DECIMAL(10,2),
    image_url VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");
$db->execute();

$db->query("CREATE TABLE IF NOT EXISTS user_preferences (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    jenis_kopi ENUM('Arabika', 'Robusta'),
    metode_penyajian ENUM('V60', 'French Press', 'Espresso', 'Tubruk', 'Cold Brew', 'Moka Pot'),
    profil_rasa ENUM('Fruity', 'Chocolate', 'Nutty', 'Earthy', 'Floral', 'Citrus', 'Caramel', 'Bitter Chocolate', 'Honey', 'Spicy', 'Bitter', 'Sweet'),
    proses ENUM('Washed', 'Honey', 'Natural'),
    preferred_roast ENUM('light', 'medium', 'medium-dark', 'dark'),
    preferred_brewing VARCHAR(100),
    flavor_preference TEXT,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
)");
$db->execute();

$db->query("CREATE TABLE IF NOT EXISTS recommendations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    coffee_id INT,
    rating INT DEFAULT 0,
    review TEXT,
    recommended_roast ENUM('light', 'medium', 'medium-dark', 'dark'),
    rule_applied VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (coffee_id) REFERENCES coffee_types(id) ON DELETE CASCADE
)");
$db->execute();

// Insert default admin user if not exists
$db->query("SELECT * FROM users WHERE username = :username");
$db->bind(':username', 'admin');
$admin = $db->single();

if (!$admin) {
    $db->query("INSERT INTO users (username, email, password, role) VALUES (:username, :email, :password, :role)");
    $db->bind(':username', 'admin');
    $db->bind(':email', 'admin@triakcoffee.com');
    $db->bind(':password', password_hash('admin123', PASSWORD_DEFAULT));
    $db->bind(':role', 'admin');
    $db->execute();
}

// Insert sample coffee data if table is empty
$db->query("SELECT COUNT(*) as count FROM coffee_types");
$count = $db->single();

if ($count->count == 0) {
    $coffees = [
        [
            'name' => 'Ethiopian Yirgacheffe',
            'description' => 'Bright and floral with citrus notes',
            'jenis_kopi' => 'Arabika',
            'roast_level' => 'light',
            'flavor_notes' => 'Citrus, Floral, Tea-like',
            'profil_rasa' => 'Floral',
            'proses' => 'Washed',
            'brewing_method' => 'V60, Pour-over, Aeropress',
            'origin' => 'Ethiopia',
            'price' => 18.50,
            'image_url' => 'https://images.pexels.com/photos/894695/pexels-photo-894695.jpeg'
        ],
        [
            'name' => 'Colombian Supremo',
            'description' => 'Well-balanced with chocolate and caramel notes',
            'jenis_kopi' => 'Arabika',
            'roast_level' => 'medium',
            'flavor_notes' => 'Chocolate, Caramel, Nuts',
            'profil_rasa' => 'Chocolate',
            'proses' => 'Honey',
            'brewing_method' => 'French Press, Drip',
            'origin' => 'Colombia',
            'price' => 16.00,
            'image_url' => 'https://images.pexels.com/photos/1695052/pexels-photo-1695052.jpeg'
        ],
        [
            'name' => 'Guatemalan Antigua',
            'description' => 'Full-bodied with smoky and spicy notes',
            'jenis_kopi' => 'Arabika',
            'roast_level' => 'medium',
            'flavor_notes' => 'Smoky, Spicy, Dark Chocolate',
            'profil_rasa' => 'Spicy',
            'proses' => 'Honey',
            'brewing_method' => 'Espresso, French Press',
            'origin' => 'Guatemala',
            'price' => 17.25,
            'image_url' => 'https://images.pexels.com/photos/1695052/pexels-photo-1695052.jpeg'
        ],
        [
            'name' => 'Sumatra Mandheling',
            'description' => 'Bold and earthy with herbal notes',
            'jenis_kopi' => 'Robusta',
            'roast_level' => 'dark',
            'flavor_notes' => 'Earthy, Herbal, Bold',
            'profil_rasa' => 'Earthy',
            'proses' => 'Natural',
            'brewing_method' => 'Tubruk, French Press',
            'origin' => 'Indonesia',
            'price' => 15.75,
            'image_url' => 'https://images.pexels.com/photos/894695/pexels-photo-894695.jpeg'
        ],
        [
            'name' => 'Brazilian Santos',
            'description' => 'Nutty and sweet with caramel undertones',
            'jenis_kopi' => 'Arabika',
            'roast_level' => 'medium',
            'flavor_notes' => 'Nutty, Sweet, Caramel',
            'profil_rasa' => 'Nutty',
            'proses' => 'Honey',
            'brewing_method' => 'Moka Pot, Espresso',
            'origin' => 'Brazil',
            'price' => 14.50,
            'image_url' => 'https://images.pexels.com/photos/1695052/pexels-photo-1695052.jpeg'
        ],
        [
            'name' => 'Java Robusta',
            'description' => 'Strong and bitter with earthy finish',
            'jenis_kopi' => 'Robusta',
            'roast_level' => 'dark',
            'flavor_notes' => 'Bitter, Strong, Earthy',
            'profil_rasa' => 'Bitter',
            'proses' => 'Natural',
            'brewing_method' => 'Espresso, Tubruk',
            'origin' => 'Indonesia',
            'price' => 13.00,
            'image_url' => 'https://images.pexels.com/photos/894695/pexels-photo-894695.jpeg'
        ]
    ];

    foreach ($coffees as $coffee) {
        $db->query("INSERT INTO coffee_types (name, description, jenis_kopi, roast_level, flavor_notes, profil_rasa, proses, brewing_method, origin, price, image_url) 
                   VALUES (:name, :description, :jenis_kopi, :roast_level, :flavor_notes, :profil_rasa, :proses, :brewing_method, :origin, :price, :image_url)");
        $db->bind(':name', $coffee['name']);
        $db->bind(':description', $coffee['description']);
        $db->bind(':jenis_kopi', $coffee['jenis_kopi']);
        $db->bind(':roast_level', $coffee['roast_level']);
        $db->bind(':flavor_notes', $coffee['flavor_notes']);
        $db->bind(':profil_rasa', $coffee['profil_rasa']);
        $db->bind(':proses', $coffee['proses']);
        $db->bind(':brewing_method', $coffee['brewing_method']);
        $db->bind(':origin', $coffee['origin']);
        $db->bind(':price', $coffee['price']);
        $db->bind(':image_url', $coffee['image_url']);
        $db->execute();
    }
}

// Helper functions
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

function redirect($url) {
    header("Location: $url");
    exit();
}

// Rule-based recommendation system
function getRecommendedRoast($jenis_kopi, $metode_penyajian, $proses, $profil_rasa) {
    global $db;
    // Panggil stored procedure dengan urutan parameter baru
    $db->query("CALL GetRuleBasedRecommendation(:jenis_kopi, :metode_penyajian, :proses, :profil_rasa, @recommended_roast, @rule_applied)");
    $db->bind(':jenis_kopi', $jenis_kopi);
    $db->bind(':metode_penyajian', $metode_penyajian);
    $db->bind(':proses', $proses);
    $db->bind(':profil_rasa', $profil_rasa);
    $db->execute();

    // Ambil hasil output parameter
    $db->query("SELECT @recommended_roast AS roast, @rule_applied AS rule");
    $result = $db->single();

    if (!$result || !$result->roast) {
        return ['roast' => 'medium', 'rule' => 'Default Rule'];
    }
    return ['roast' => $result->roast, 'rule' => $result->rule];
}
?>