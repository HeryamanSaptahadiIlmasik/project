<?php
require_once '../config/init.php';

if (!isLoggedIn()) { redirect('../login.php'); }
if (isAdmin()) { redirect('../admin/dashboard.php'); }

$id = $_SESSION['user_id'];
$password_lama = $_POST['password_lama'];
$password_baru = $_POST['password_baru'];
$konfirmasi = $_POST['konfirmasi_password'];

// Ambil password lama dari database
$db->query("SELECT password FROM users WHERE id = :id");
$db->bind(':id', $id);
$data = $db->single();

// Cek password lama
if (!password_verify($password_lama, $data->password)) {
    header("Location: reset_password.php?error=Password lama salah");
    exit;
}

// Cek konfirmasi password baru
if ($password_baru !== $konfirmasi) {
    header("Location: reset_password.php?error=Konfirmasi password tidak cocok");
    exit;
}

// Hash password baru
$password_baru_hash = password_hash($password_baru, PASSWORD_DEFAULT);

// Update password di database
$db->query("UPDATE users SET password = :password WHERE id = :id");
$db->bind(':password', $password_baru_hash);
$db->bind(':id', $id);
$db->execute();

header("Location: reset_password.php?success=1");
exit; 