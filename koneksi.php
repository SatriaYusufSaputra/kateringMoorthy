<?php
$host = "localhost";
$user = "root"; 
$pass = ""; 
$db   = "katering_db";

$koneksi = mysqli_connect($host, $user, $pass, $db);

if (!$koneksi) {
    die("Gagal konek database: " . mysqli_connect_error());
}
?>
