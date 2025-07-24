<?php
require_once '../config/init.php';

if (!isLoggedIn() || !isAdmin()) {
    redirect('../login.php');
}

$id = $_GET['id'] ?? 0;
if ($id) {
    $db->query("DELETE FROM roasting_rules WHERE id = :id");
    $db->bind(':id', $id);
    $db->execute();
}
redirect('manage_rules.php');
?>
