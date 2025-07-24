<?php
session_start();
require '../koneksi.php';
$id = $_SESSION['user_id'];
$nama_lengkap = $_POST['nama_lengkap'];
$alamat = $_POST['alamat'];
$no_hp = $_POST['no_hp'];

$sql = "UPDATE users SET 
            nama_lengkap='$nama_lengkap', 
            alamat='$alamat', 
            no_hp='$no_hp'
        WHERE id='$id'";
mysqli_query($conn, $sql);

header("Location: profil.php?update=success"); 