<?php
require_once '../koneksi.php';
session_start();

$order_id = (int) $_GET['order'];

mysqli_query($koneksi, "
    UPDATE pesanan 
    SET status_pembayaran = 'paid',
        status = 'diproses'
    WHERE id = $order_id
");

header('Location: ../pages/pesanan_saya.php');
exit;
