<?php
session_start();
include 'koneksi.php';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Moorthy</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

    <h1>Selamat datang di Katering Moorthy</h1>

    <a href="pages/menu.php">Lihat Menu</a> |
    <?php if (!isset($_SESSION['user'])): ?>
        <a href="login.php">Login</a> |
        <a href="register.php">Register</a>
    <?php else: ?>
        <a href="pages/keranjang.php">Keranjang</a> |
        <a href="pages/pesanan_saya.php">Pesanan Saya</a> |
        <a href="logout.php">Logout</a>
    <?php endif; ?>

</body>
</html>
