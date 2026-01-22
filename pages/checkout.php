<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../koneksi.php';

// Cek apakah user sudah login
if (!isset($_SESSION['user'])) {
    header('Location: ../login.php');
    exit;
}

// Cek keranjang ada item
if (!isset($_SESSION['keranjang']) || count($_SESSION['keranjang']) == 0) {
    header('Location: keranjang.php');
    exit;
}
require_once __DIR__ . '/../partials/navbar.php';
$message = '';
$message_type = '';

// Proses checkout
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $user_id = $_SESSION['user'];
    $nama_penerima = mysqli_real_escape_string($koneksi, $_POST['nama_penerima']);
    $email = mysqli_real_escape_string($koneksi, $_POST['email']);
    $no_hp = mysqli_real_escape_string($koneksi, $_POST['no_hp']);
    $alamat = mysqli_real_escape_string($koneksi, $_POST['alamat']);
    $catatan = mysqli_real_escape_string($koneksi, $_POST['catatan']);
    $metode_pembayaran = mysqli_real_escape_string($koneksi, $_POST['metode_pembayaran']);

    // Validasi tanggal pengiriman
    $tanggal_pengiriman = $_POST['tanggal_pengiriman'] ?? null;

    if (!$tanggal_pengiriman) {
        $message = 'Tanggal pengiriman wajib diisi.';
        $message_type = 'error';
    } else {

        $tanggal_pengiriman = mysqli_real_escape_string($koneksi, $tanggal_pengiriman);

        // Hitung total
        $total_harga = 0;
        foreach ($_SESSION['keranjang'] as $item) {
            $total_harga += $item['harga'] * $item['jumlah'];
        }

        // Insert ke tabel pesanan
        $query_pesanan = "
            INSERT INTO pesanan (
                user_id,
                total_harga,
                alamat,
                catatan,
                tanggal_pengiriman,
                status
            ) VALUES (
                $user_id,
                $total_harga,
                '$alamat',
                '$catatan',
                '$tanggal_pengiriman',
                'pending'
            )
        ";

        if (mysqli_query($koneksi, $query_pesanan)) {

            $pesanan_id = mysqli_insert_id($koneksi);

            // Insert detail pesanan
            foreach ($_SESSION['keranjang'] as $menu_id => $item) {
                $subtotal = $item['harga'] * $item['jumlah'];

                $query_detail = "
                    INSERT INTO detail_pesanan (
                        pesanan_id,
                        menu_id,
                        qty,
                        harga
                    ) VALUES (
                        $pesanan_id,
                        $menu_id,
                        {$item['jumlah']},
                        {$item['harga']}
                    )
                ";

                mysqli_query($koneksi, $query_detail);
            }

            // Kosongkan keranjang
            unset($_SESSION['keranjang']);

            // Redirect sukses
            header('Location: pesanan_saya.php?sukses=' . $pesanan_id);
            exit;

        } else {
            $message = 'Gagal melakukan checkout: ' . mysqli_error($koneksi);
            $message_type = 'error';
        }
    }
}

// Hitung total keranjang
$total = 0;
$item_count = 0;
foreach ($_SESSION['keranjang'] as $item) {
    $total += $item['harga'] * $item['jumlah'];
    $item_count += $item['jumlah'];
}

// Ambil data user
$user_id = $_SESSION['user'];
$user_data = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM users WHERE id = $user_id"));


?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Moorthy Shop</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>

<body class="bg-gray-50">

    <!-- HEADER -->
    <section class="bg-green-500 text-white py-12">
        <div class="max-w-6xl mx-auto px-4">
            <h1 class="text-4xl font-bold">Checkout Pesanan</h1>
            <p class="mt-2">Lengkapi data untuk menyelesaikan pemesanan</p>
        </div>
    </section>

    <!-- CONTENT -->
    <section class="py-12">
        <div class="max-w-6xl mx-auto px-4">
            <?php if ($message): ?>
                <div class="mb-6 p-4 rounded-lg <?php echo $message_type == 'error' ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700'; ?>">
                    <?= $message; ?>
                </div>
            <?php endif; ?>

            <div class="grid md:grid-cols-3 gap-8">
                <!-- FORM CHECKOUT -->
                <div class="md:col-span-2">
                    <div class="bg-white rounded-xl shadow-lg p-8">
                        <h2 class="text-2xl font-bold text-gray-800 mb-6">Informasi Pengiriman</h2>

                        <form method="POST">
                            <div class="space-y-4">
                                <!-- Nama Penerima -->
                                <div>
                                    <label class="block text-gray-700 font-semibold mb-2">Nama Penerima</label>
                                    <input type="text" name="nama_penerima" value="<?= htmlspecialchars($user_data['nama'] ?? ''); ?>" required
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-green-500">
                                </div>

                                <!-- Email -->
                                <div>
                                    <label class="block text-gray-700 font-semibold mb-2">Email</label>
                                    <input type="email" name="email" value="<?= htmlspecialchars($user_data['email'] ?? ''); ?>" required
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-green-500">
                                </div>

                                <!-- No HP -->
                                <div>
                                    <label class="block text-gray-700 font-semibold mb-2">No. HP</label>
                                    <input type="tel" name="no_hp" value="<?= htmlspecialchars($user_data['no_hp'] ?? ''); ?>" required
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-green-500">
                                </div>

                                <!-- Alamat -->
                                <div>
                                    <label class="block text-gray-700 font-semibold mb-2">Alamat Pengiriman</label>
                                    <textarea name="alamat" rows="3" required
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-green-500"><?= htmlspecialchars($user_data['alamat'] ?? ''); ?></textarea>
                                </div>
                                
                                <!-- Tanggal Pesanan -->
                                <div>
                                    <label class="block text-gray-700 font-semibold mb-2">Tanggal Pengiriman</label>
                                    <input type="date" name="tanggal_pengiriman" required
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-green-500"
                                        min="<?= date('Y-m-d'); ?>">
                                </div>
                                
                                <!-- Catatan -->
                                <div>
                                    <label class="block text-gray-700 font-semibold mb-2">Catatan (Opsional)</label>
                                    <textarea name="catatan" rows="3"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-green-500"
                                        placeholder="Contoh: Tidak pedas, jangan telur, dll"></textarea>
                                </div>

                                <!-- Metode Pembayaran -->
                                <div>
                                    <label class="block text-gray-700 font-semibold mb-2">Metode Pembayaran</label>
                                    <select name="metode_pembayaran" required
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-green-500">
                                        <option value="">-- Pilih Metode --</option>
                                        <option value="transfer_bank">Transfer Bank</option>
                                        <option value="gopay">GoPay</option>
                                        <option value="ovo">OVO</option>
                                        <option value="dana">DANA</option>
                                        <option value="cash">Bayar di Tempat</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Tombol -->
                            <div class="mt-8 flex gap-3">
                                <a href="keranjang.php" class="flex-1 bg-gray-500 text-white py-3 rounded-lg font-semibold hover:bg-gray-600 text-center">
                                    Kembali ke Keranjang
                                </a>
                                <button type="submit" class="flex-1 bg-green-600 text-white py-3 rounded-lg font-semibold hover:bg-green-700">
                                    Konfirmasi Pesanan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- RINGKASAN PESANAN -->
                <div>
                    <div class="bg-white rounded-xl shadow-lg p-6 sticky top-4">
                        <h3 class="text-xl font-bold text-gray-800 mb-6">Ringkasan Pesanan</h3>

                        <!-- Item Pesanan -->
                        <div class="space-y-3 mb-6 max-h-80 overflow-y-auto border-b pb-4">
                            <?php foreach ($_SESSION['keranjang'] as $item): ?>
                                <div class="flex justify-between text-sm">
                                    <div>
                                        <div class="font-semibold text-gray-800"><?= htmlspecialchars($item['nama_menu']); ?></div>
                                        <div class="text-gray-600">x<?= $item['jumlah']; ?></div>
                                    </div>
                                    <div class="font-semibold text-gray-800">
                                        Rp <?= number_format($item['harga'] * $item['jumlah']); ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <!-- Total -->
                        <div class="space-y-2 border-t pt-4">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Subtotal</span>
                                <span class="font-semibold">Rp <?= number_format($total); ?></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Ongkir</span>
                                <span class="font-semibold text-green-600">Gratis</span>
                            </div>
                            <div class="flex justify-between text-lg font-bold mt-4 pt-4 border-t">
                                <span>Total</span>
                                <span class="text-green-600">Rp <?= number_format($total); ?></span>
                            </div>
                        </div>

                        <!-- Info -->
                        <p class="text-xs text-gray-500 text-center mt-6">
                            Pastikan semua data sudah benar sebelum melanjutkan
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>
<?php require_once __DIR__ . '/../partials/footer.php'; ?>