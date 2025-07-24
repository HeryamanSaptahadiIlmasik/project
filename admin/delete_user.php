<?php
require_once '../config/init.php';

header('Content-Type: application/json');

if (!isLoggedIn() || !isAdmin()) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit();
}

$input = json_decode(file_get_contents('php://input'), true);
$user_id = (int)($input['user_id'] ?? 0);

if ($user_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid user ID']);
    exit();
}

// Prevent deleting admin users
$db->query("SELECT role FROM users WHERE id = :user_id");
$db->bind(':user_id', $user_id);
$user = $db->single();

if (!$user) {
    echo json_encode(['success' => false, 'message' => 'User not found']);
    exit();
}

if ($user->role === 'admin') {
    echo json_encode(['success' => false, 'message' => 'Cannot delete admin users']);
    exit();
}

try {
    // Delete related data (foreign key constraints will handle this automatically)
    // But we'll do it explicitly for better control
    
    // Delete user preferences
    $db->query("DELETE FROM user_preferences WHERE user_id = :user_id");
    $db->bind(':user_id', $user_id);
    $db->execute();
    
    // Delete user recommendations
    $db->query("DELETE FROM recommendations WHERE user_id = :user_id");
    $db->bind(':user_id', $user_id);
    $db->execute();
    
    // Delete the user
    $db->query("DELETE FROM users WHERE id = :user_id");
    $db->bind(':user_id', $user_id);
    
    if ($db->execute()) {
        echo json_encode(['success' => true, 'message' => 'User deleted successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error deleting user']);
    }
} catch (Exception $e) {
    error_log("Error deleting user: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Database error occurred']);
}
?>