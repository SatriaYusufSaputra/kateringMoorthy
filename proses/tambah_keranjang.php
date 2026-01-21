<?php
session_start();

// Validasi input
if (!isset($_POST['menu_id']) || !isset($_POST['nama_menu']) || !isset($_POST['harga']) || !isset($_POST['jumlah'])) {
    die('Data tidak lengkap');
}

$menu_id = (int)$_POST['menu_id'];
$nama_menu = htmlspecialchars($_POST['nama_menu']);
$harga = (int)$_POST['harga'];
$jumlah = (int)$_POST['jumlah'];

// Validasi nilai
if ($jumlah <= 0) {
    die('Jumlah harus lebih dari 0');
}

// Inisialisasi keranjang jika belum ada
if (!isset($_SESSION['keranjang'])) {
    $_SESSION['keranjang'] = [];
}

// Jika item sudah ada di keranjang, tambah jumlahnya
if (isset($_SESSION['keranjang'][$menu_id])) {
    $_SESSION['keranjang'][$menu_id]['jumlah'] += $jumlah;
} else {
    // Tambah item baru
    $_SESSION['keranjang'][$menu_id] = [
        'nama_menu' => $nama_menu,
        'harga' => $harga,
        'jumlah' => $jumlah
    ];
}

// Redirect ke halaman keranjang
header('Location: ../pages/keranjang.php');
exit;
