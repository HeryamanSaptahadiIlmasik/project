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
$coffee_id = (int)($input['coffee_id'] ?? 0);

if ($coffee_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid coffee ID']);
    exit();
}

try {
    // First delete related recommendations
    $db->query("DELETE FROM recommendations WHERE coffee_id = :coffee_id");
    $db->bind(':coffee_id', $coffee_id);
    $db->execute();
    
    // Then delete the coffee
    $db->query("DELETE FROM coffee_types WHERE id = :coffee_id");
    $db->bind(':coffee_id', $coffee_id);
    
    if ($db->execute()) {
        echo json_encode(['success' => true, 'message' => 'Coffee deleted successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error deleting coffee']);
    }
} catch (Exception $e) {
    error_log("Error deleting coffee: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Database error occurred']);
}
?>