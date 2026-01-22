<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../koneksi.php';

// Proses hapus item dari keranjang
if (isset($_GET['hapus'])) {
    $menu_id = (int)$_GET['hapus'];
    if (isset($_SESSION['keranjang'][$menu_id])) {
        unset($_SESSION['keranjang'][$menu_id]);
    }
    header('Location: keranjang.php');
    exit;
}

// Proses update jumlah
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_jumlah'])) {
    $menu_id = (int)$_POST['menu_id'];
    $jumlah = (int)$_POST['jumlah'];

    if ($jumlah <= 0) {
        unset($_SESSION['keranjang'][$menu_id]);
    } else {
        $_SESSION['keranjang'][$menu_id]['jumlah'] = $jumlah;
    }
    header('Location: keranjang.php');
    exit;
}

// Hitung total
$total = 0;
$item_count = 0;
if (isset($_SESSION['keranjang']) && is_array($_SESSION['keranjang'])) {
    foreach ($_SESSION['keranjang'] as $item) {
        $total += $item['harga'] * $item['jumlah'];
        $item_count += $item['jumlah'];
    }
}

require_once __DIR__ . '/../partials/navbar.php';
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Keranjang - Moorthy Shop</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>

<body class="bg-gray-50">

    <!-- HEADER -->
    <section class="bg-green-500 text-white py-12">
        <div class="max-w-6xl mx-auto px-4">
            <h1 class="text-4xl font-bold">Keranjang Belanja</h1>
            <p class="mt-2">Periksa pesanan Anda sebelum checkout</p>
        </div>
    </section>

    <!-- CONTENT -->
    <section class="py-12">
        <div class="max-w-6xl mx-auto px-4">
            <div class="grid md:grid-cols-3 gap-8">
                <!-- TABEL KERANJANG -->
                <div class="md:col-span-2">
                    <?php if (isset($_SESSION['keranjang']) && count($_SESSION['keranjang']) > 0): ?>
                        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                            <table class="w-full">
                                <thead class="bg-green-500 text-white">
                                    <tr>
                                        <th class="px-6 py-4 text-left">Menu</th>
                                        <th class="px-6 py-4 text-center">Harga</th>
                                        <th class="px-6 py-4 text-center">Jumlah</th>
                                        <th class="px-6 py-4 text-right">Subtotal</th>
                                        <th class="px-6 py-4 text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($_SESSION['keranjang'] as $menu_id => $item): ?>
                                        <tr class="border-b hover:bg-gray-50">
                                            <td class="px-6 py-4">
                                                <div class="font-semibold text-gray-800"><?= htmlspecialchars($item['nama_menu']); ?></div>
                                            </td>
                                            <td class="px-6 py-4 text-center text-gray-600">
                                                Rp <?= number_format($item['harga']); ?>
                                            </td>
                                            <td class="px-6 py-4 text-center">
                                                <form method="POST" class="flex justify-center gap-2">
                                                    <input type="hidden" name="menu_id" value="<?= $menu_id; ?>">
                                                    <input type="hidden" name="update_jumlah" value="1">
                                                    <input type="number" name="jumlah" value="<?= $item['jumlah']; ?>" min="1" max="99"
                                                        class="w-16 px-2 py-1 border border-gray-300 rounded text-center" onchange="this.form.submit()">
                                                </form>
                                            </td>
                                            <td class="px-6 py-4 text-right text-gray-800 font-semibold">
                                                Rp <?= number_format($item['harga'] * $item['jumlah']); ?>
                                            </td>
                                            <td class="px-6 py-4 text-center">
                                                <a href="keranjang.php?hapus=<?= $menu_id; ?>"
                                                    onclick="return confirm('Hapus item ini?')"
                                                    class="text-red-600 hover:text-red-800 font-semibold">Hapus</a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- LANJUT BELANJA & KOSONGKAN -->
                        <div class="mt-6 flex gap-3">
                            <a href="menu.php" class="flex-1 bg-gray-500 text-white py-3 rounded-lg font-semibold hover:bg-gray-600 text-center">
                                ← Lanjut Belanja
                            </a>
                            <a href="keranjang.php?kosongkan=1"
                                onclick="return confirm('Kosongkan semua item?')"
                                class="flex-1 bg-red-500 text-white py-3 rounded-lg font-semibold hover:bg-red-600 text-center">
                                Kosongkan Keranjang
                            </a>
                        </div>

                    <?php else: ?>
                        <div class="bg-white rounded-xl shadow-lg p-12 text-center">
                            <div class="text-6xl mb-4">🛒</div>
                            <h2 class="text-2xl font-bold text-gray-800 mb-2">Keranjang Kosong</h2>
                            <p class="text-gray-600 mb-6">Mulai belanja sekarang untuk menambahkan item ke keranjang</p>
                            <a href="menu.php" class="inline-block bg-green-600 text-white px-8 py-3 rounded-lg font-semibold hover:bg-green-700">
                                Lihat Menu
                            </a>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- RINGKASAN PESANAN -->
                <div>
                    <div class="bg-white rounded-xl shadow-lg p-6 sticky top-4">
                        <h3 class="text-xl font-bold text-gray-800 mb-6">Ringkasan Pesanan</h3>

                        <div class="space-y-4 border-b pb-4">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Item (<?= $item_count; ?>)</span>
                                <span class="font-semibold">Rp <?= number_format($total); ?></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Ongkir</span>
                                <span class="font-semibold text-green-600">Gratis</span>
                            </div>
                        </div>

                        <div class="mt-4 mb-6">
                            <div class="flex justify-between text-lg font-bold">
                                <span>Total</span>
                                <span class="text-green-600">Rp <?= number_format($total); ?></span>
                            </div>
                        </div>

                        <?php if (isset($_SESSION['keranjang']) && count($_SESSION['keranjang']) > 0): ?>
                            <?php if (isset($_SESSION['user'])): ?>
                                <a href="checkout.php" class="block w-full bg-green-600 text-white py-3 rounded-lg font-semibold hover:bg-green-700 text-center">
                                    Lanjut ke Checkout
                                </a>
                            <?php else: ?>
                                <a href="../login.php" class="block w-full bg-green-600 text-white py-3 rounded-lg font-semibold hover:bg-green-700 text-center">
                                    Login untuk Checkout
                                </a>
                            <?php endif; ?>
                        <?php endif; ?>

                        <p class="text-xs text-gray-500 text-center mt-4">
                            Dengan melanjutkan, Anda setuju dengan <br> syarat dan ketentuan kami
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>

<?php
// Proses kosongkan keranjang
if (isset($_GET['kosongkan']) && $_GET['kosongkan'] == 1) {
    $_SESSION['keranjang'] = [];
    header('Location: keranjang.php');
    exit;
}
?>