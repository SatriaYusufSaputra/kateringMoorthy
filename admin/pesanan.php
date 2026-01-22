<?php
session_start();
include '../koneksi.php';

// Cek apakah user adalah admin
if (!isset($_SESSION['user']) || $_SESSION['role'] != 'admin') {
    header('Location: ../login.php');
    exit;
}

// Proses ubah status pesanan
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['pesanan_id'])) {
    $pesanan_id = $_POST['pesanan_id'];
    $status = $_POST['status'];

    $query = "UPDATE pesanan SET status='$status' WHERE id='$pesanan_id'";

    if (mysqli_query($koneksi, $query)) {
        $success = "Status pesanan berhasil diubah";
    } else {
        $error = "Gagal mengubah status pesanan";
    }
}

// Ambil semua pesanan
$pesanan = mysqli_query($koneksi, "SELECT p.*, u.nama FROM pesanan p 
                                    JOIN users u ON p.user_id = u.id 
                                    ORDER BY p.id DESC");
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Pesanan - Admin</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>

<body class="bg-gray-50">
    <!-- NAVBAR -->
    <nav class="bg-white shadow">
        <div class="max-w-7xl mx-auto px-4 py-4 flex justify-between items-center">
            <h1 class="text-2xl font-bold text-green-600">Admin Katering Moorthy</h1>
            <div class="space-x-4">
                <span class="text-gray-700">Hi, <?= $_SESSION['nama']; ?></span>
                <a href="../logout.php" class="text-red-600 hover:text-red-700 font-semibold">Logout</a>
            </div>
        </div>
    </nav>

    <!-- SIDEBAR & CONTENT -->
    <div class="flex">
        <!-- SIDEBAR -->
        <aside class="w-64 bg-white shadow-lg min-h-screen">
            <div class="p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-6">Menu Admin</h2>
                <ul class="space-y-4">
                    <li>
                        <a href="dashboard.php" class="block px-4 py-2 text-gray-700 hover:bg-gray-100 rounded-lg font-semibold">
                            Dashboard
                        </a>
                    </li>
                    <li>
                        <a href="menu.php" class="block px-4 py-2 text-gray-700 hover:bg-gray-100 rounded-lg font-semibold">
                            Kelola Menu
                        </a>
                    </li>
                    <li>
                        <a href="pesanan.php" class="block px-4 py-2 bg-green-600 text-white rounded-lg font-semibold">
                            Pesanan
                        </a>
                    </li>
                    <li>
                        <a href="users.php" class="block px-4 py-2 text-gray-700 hover:bg-gray-100 rounded-lg font-semibold">
                            Pengguna
                        </a>
                    </li>
                </ul>
            </div>
        </aside>

        <!-- MAIN CONTENT -->
        <main class="flex-1 p-8">
            <h2 class="text-3xl font-bold text-gray-800 mb-8">Pesanan</h2>

            <!-- ALERT -->
            <?php if (isset($success)): ?>
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    <?= $success; ?>
                </div>
            <?php endif; ?>

            <?php if (isset($error)): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    <?= $error; ?>
                </div>
            <?php endif; ?>

            <!-- TABEL PESANAN -->
            <div class="bg-white p-6 rounded-xl shadow-lg">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b bg-gray-100">
                                <th class="text-left py-3 px-4 font-semibold text-gray-700">ID</th>
                                <th class="text-left py-3 px-4 font-semibold text-gray-700">Pelanggan</th>
                                <th class="text-left py-3 px-4 font-semibold text-gray-700">Total</th>
                                <th class="text-left py-3 px-4 font-semibold text-gray-700">Status</th>
                                <th class="text-left py-3 px-4 font-semibold text-gray-700">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = mysqli_fetch_assoc($pesanan)) : ?>
                                <tr class="border-b hover:bg-gray-50">
                                    <td class="py-4 px-4 font-semibold">#<?= $row['id']; ?></td>
                                    <td class="py-4 px-4"><?= $row['nama']; ?></td>
                                    <td class="py-4 px-4 font-semibold">Rp <?= number_format($row['total_harga']); ?></td>
                                    <td class="py-4 px-4">
                                        <form method="POST" class="flex gap-2">
                                            <input type="hidden" name="pesanan_id" value="<?= $row['id']; ?>">
                                            <select name="status" class="px-2 py-1 border border-gray-300 rounded text-sm focus:outline-none focus:border-green-500"
                                                onchange="this.form.submit()">
                                                <option value="pending" <?= ($row['status'] == 'pending') ? 'selected' : ''; ?>>Pending</option>
                                                <option value="diproses" <?= ($row['status'] == 'diproses') ? 'selected' : ''; ?>>Proses</option>
                                                <option value="selesai" <?= ($row['status'] == 'selesai') ? 'selected' : ''; ?>>Selesai</option>
                                                <option value="dibatalkan" <?= ($row['status'] == 'dibatalkan') ? 'selected' : ''; ?>>Batal</option>
                                            </select>
                                        </form>
                                    </td>
                                    <td class="py-4 px-4">
                                        <a href="?lihat=<?= $row['id']; ?>" class="text-blue-600 hover:text-blue-700 font-semibold">
                                            Lihat Detail
                                        </a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>

                <?php if (mysqli_num_rows($pesanan) == 0): ?>
                    <div class="text-center py-12">
                        <p class="text-gray-500 text-lg">Belum ada pesanan</p>
                    </div>
                <?php endif; ?>
            </div>

            <!-- DETAIL PESANAN -->
            <?php if (isset($_GET['lihat'])):
                $detail_pesanan_id = $_GET['lihat'];
                $detail = mysqli_query($koneksi, "SELECT pd.*, m.nama_menu FROM detail_pesanan pd 
                                                   JOIN menu m ON pd.menu_id = m.id 
                                                   WHERE pd.pesanan_id='$detail_pesanan_id'");
                $pesanan_info = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM pesanan WHERE id='$detail_pesanan_id'"));
            ?>
                <div class="bg-white p-6 rounded-xl shadow-lg mt-8">
                    <h3 class="text-2xl font-bold text-gray-800 mb-4">Detail Pesanan #<?= $detail_pesanan_id; ?></h3>

                    <div class="mb-6">
                        <table class="w-full">
                            <thead>
                                <tr class="border-b">
                                    <th class="text-left py-3 px-4 font-semibold text-gray-700">Menu</th>
                                    <th class="text-left py-3 px-4 font-semibold text-gray-700">Jumlah</th>
                                    <th class="text-left py-3 px-4 font-semibold text-gray-700">Harga</th>
                                    <th class="text-left py-3 px-4 font-semibold text-gray-700">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($item = mysqli_fetch_assoc($detail)) : ?>
                                    <tr class="border-b">
                                        <td class="py-3 px-4"><?= $item['nama_menu']; ?></td>
                                        <td class="py-3 px-4"><?= $item['qty']; ?></td>
                                        <td class="py-3 px-4">Rp <?= number_format($item['harga']); ?></td>
                                        <td class="py-3 px-4 font-semibold">Rp <?= number_format($item['qty'] * $item['harga']); ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="text-right">
                        <p class="text-2xl font-bold text-green-600">
                            Total: Rp <?= number_format($pesanan_info['total_harga']); ?>
                        </p>
                    </div>

                    <div class="mt-6">
                        <a href="pesanan.php" class="px-6 py-2 bg-gray-400 text-white rounded-lg font-semibold hover:bg-gray-500">
                            Kembali
                        </a>
                    </div>
                </div>
            <?php endif; ?>
        </main>
    </div>
<?php require_once __DIR__ . '/../partials/footer.php'; ?>