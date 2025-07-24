<?php
require_once '../config/init.php';

if (!isLoggedIn() || isAdmin()) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Akses tidak diizinkan']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Metode tidak diizinkan']);
    exit();
}

$user_id = $_SESSION['user_id'];
$jenis_kopi = $_POST['jenis_kopi'] ?? '';
$metode_penyajian = $_POST['metode_penyajian'] ?? '';
$profil_rasa = $_POST['profil_rasa'] ?? '';
$proses = $_POST['proses'] ?? '';
$flavor_preference = $_POST['flavor_preference'] ?? '';

// Validate required fields
if (empty($jenis_kopi) || empty($metode_penyajian) || empty($profil_rasa) || empty($proses)) {
    echo json_encode(['success' => false, 'message' => 'Semua field preferensi harus diisi']);
    exit();
}

try {
    // Get rule-based recommendation
    $rule_recommendation = getRecommendedRoast($jenis_kopi, $metode_penyajian, $proses, $profil_rasa);
    
    // Check if preferences already exist
    $db->query("SELECT id FROM user_preferences WHERE user_id = :user_id");
    $db->bind(':user_id', $user_id);
    $existing = $db->single();
    
    if ($existing) {
        // Update existing preferences
        $db->query("UPDATE user_preferences 
                   SET jenis_kopi = :jenis_kopi,
                       metode_penyajian = :metode_penyajian,
                       profil_rasa = :profil_rasa,
                       proses = :proses,
                       preferred_roast = :preferred_roast,
                       flavor_preference = :flavor_preference 
                   WHERE user_id = :user_id");
    } else {
        // Insert new preferences
        $db->query("INSERT INTO user_preferences (user_id, jenis_kopi, metode_penyajian, profil_rasa, proses, preferred_roast, flavor_preference) 
                   VALUES (:user_id, :jenis_kopi, :metode_penyajian, :profil_rasa, :proses, :preferred_roast, :flavor_preference)");
    }
    
    $db->bind(':user_id', $user_id);
    $db->bind(':jenis_kopi', $jenis_kopi);
    $db->bind(':metode_penyajian', $metode_penyajian);
    $db->bind(':profil_rasa', $profil_rasa);
    $db->bind(':proses', $proses);
    $db->bind(':preferred_roast', $rule_recommendation['roast']);
    $db->bind(':flavor_preference', $flavor_preference);
    
    if ($db->execute()) {
        // INSERT ke recommendation_history (PASTIKAN DILAKUKAN SEBELUM REDIRECT/EXIT)
        $db->query("INSERT INTO recommendation_history 
            (user_id, jenis_kopi, metode_penyajian, profil_rasa, proses, recommended_roast, rule_applied) 
            VALUES (:user_id, :jenis_kopi, :metode_penyajian, :profil_rasa, :proses, :recommended_roast, :rule_applied)");
        $db->bind(':user_id', $user_id);
        $db->bind(':jenis_kopi', $jenis_kopi);
        $db->bind(':metode_penyajian', $metode_penyajian);
        $db->bind(':profil_rasa', $profil_rasa);
        $db->bind(':proses', $proses);
        $db->bind(':recommended_roast', $rule_recommendation['roast']);
        $db->bind(':rule_applied', $rule_recommendation['rule']);
        $db->execute();
        
        // Baru setelah itu lakukan redirect/response
        if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) || $_SERVER['HTTP_X_REQUESTED_WITH'] !== 'XMLHttpRequest') {
            $_SESSION['success_message'] = 'Preferensi berhasil diperbarui! Rekomendasi roast: ' . 
                ucfirst(str_replace('-', ' ', $rule_recommendation['roast'])) . ' (' . $rule_recommendation['rule'] . ')';
            $_SESSION['show_recommendations'] = true;
            $_SESSION['last_recommendation'] = $rule_recommendation;
            header('Location: recommendation.php');
            exit();
        }
        
        echo json_encode([
            'success' => true, 
            'message' => 'Preferensi berhasil diperbarui',
            'recommendation' => $rule_recommendation
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Gagal memperbarui preferensi']);
    }
} catch (Exception $e) {
    error_log("Error updating preferences: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Terjadi kesalahan database']);
}
?>