<?php
require_once '../koneksi.php';
session_start();

if (!isset($_GET['order'])) {
    header('Location: index.php');
    exit;
}

$order_id = (int) $_GET['order'];

$pesanan = mysqli_fetch_assoc(mysqli_query(
    $koneksi,
    "SELECT * FROM pesanan WHERE id = $order_id"
));

if (!$pesanan) {
    die('Pesanan tidak ditemukan');
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Pembayaran</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>
<body class="bg-gray-100 p-8">

<div class="max-w-xl mx-auto bg-white p-6 rounded-xl shadow">
    <h1 class="text-2xl font-bold mb-4">Pembayaran Pesanan</h1>

    <p><strong>Order ID:</strong> #<?= $pesanan['id']; ?></p>
    <p><strong>Total:</strong> Rp <?= number_format($pesanan['total_harga']); ?></p>
    <p><strong>Metode:</strong> <?= strtoupper($pesanan['metode_pembayaran']); ?></p>

    <hr class="my-4">

    <?php if ($pesanan['metode_pembayaran'] == 'cash'): ?>
        <p class="text-green-600 font-semibold">
            Pembayaran dilakukan saat pesanan diterima (COD)
        </p>

        <a href="../proses/konfirmasi_bayar.php?order=<?= $order_id; ?>"
           class="block mt-4 bg-green-600 text-white py-2 rounded text-center">
           Konfirmasi Pesanan
        </a>

    <?php else: ?>
        <p class="text-gray-700">
            Silakan transfer ke rekening berikut:
        </p>
        <ul class="mt-2 text-sm">
            <li>DANA: 081225662637</li>
            <li>a.n Satria Yusuf Saputra</li>
        </ul>

        <a href="../proses/konfirmasi_bayar.php?order=<?= $order_id; ?>"
           class="block mt-4 bg-blue-600 text-white py-2 rounded text-center">
           Saya Sudah Bayar
        </a>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>
