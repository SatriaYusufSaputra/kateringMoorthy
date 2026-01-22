<?php
session_start();
include '../koneksi.php';

// Cek apakah user adalah admin
if (!isset($_SESSION['user']) || $_SESSION['role'] != 'admin') {
    header('Location: ../login.php');
    exit;
}

// Hitung statistik
$total_pesanan = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as total FROM pesanan"))['total'];
$total_menu = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as total FROM menu"))['total'];
$total_users = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as total FROM users WHERE role='customer'"))['total'];
$total_revenue = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT SUM(total_harga) as total FROM pesanan WHERE status='selesai'"))['total'] ?? 0;
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Dashboard Admin - Moorthy Shop</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>

<body class="bg-gray-50">
    <!-- NAVBAR ADMIN -->
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
                        <a href="dashboard.php" class="block px-4 py-2 bg-green-600 text-white rounded-lg font-semibold">
                            Dashboard
                        </a>
                    </li>
                    <li>
                        <a href="menu.php" class="block px-4 py-2 text-gray-700 hover:bg-gray-100 rounded-lg font-semibold">
                            Kelola Menu
                        </a>
                    </li>
                    <li>
                        <a href="pesanan.php" class="block px-4 py-2 text-gray-700 hover:bg-gray-100 rounded-lg font-semibold">
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
            <h2 class="text-3xl font-bold text-gray-800 mb-8">Dashboard</h2>

            <!-- STATISTICS -->
            <div class="grid md:grid-cols-4 gap-6 mb-12">
                <!-- Total Pesanan -->
                <div class="bg-blue-500 text-white p-6 rounded-xl shadow-lg">
                    <h3 class="text-lg font-semibold mb-2">Total Pesanan</h3>
                    <p class="text-4xl font-bold"><?= $total_pesanan; ?></p>
                </div>

                <!-- Total Menu -->
                <div class="bg-green-500 text-white p-6 rounded-xl shadow-lg">
                    <h3 class="text-lg font-semibold mb-2">Total Menu</h3>
                    <p class="text-4xl font-bold"><?= $total_menu; ?></p>
                </div>

                <!-- Total Users -->
                <div class="bg-purple-500 text-white p-6 rounded-xl shadow-lg">
                    <h3 class="text-lg font-semibold mb-2">Total Pelanggan</h3>
                    <p class="text-4xl font-bold"><?= $total_users; ?></p>
                </div>

                <!-- Total Revenue -->
                <div class="bg-orange-500 text-white p-6 rounded-xl shadow-lg">
                    <h3 class="text-lg font-semibold mb-2">Total Revenue</h3>
                    <p class="text-2xl font-bold">Rp <?= number_format($total_revenue); ?></p>
                </div>
            </div>

            <!-- RECENT ORDERS -->
            <div class="bg-white p-6 rounded-xl shadow-lg">
                <h3 class="text-2xl font-bold text-gray-800 mb-4">Pesanan Terbaru</h3>
                <table class="w-full">
                    <thead>
                        <tr class="border-b">
                            <th class="text-left py-3 px-4 font-semibold text-gray-700">ID</th>
                            <th class="text-left py-3 px-4 font-semibold text-gray-700">Pelanggan</th>
                            <th class="text-left py-3 px-4 font-semibold text-gray-700">Total</th>
                            <th class="text-left py-3 px-4 font-semibold text-gray-700">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $pesanan = mysqli_query($koneksi, "SELECT p.*, u.nama FROM pesanan p 
                                                         JOIN users u ON p.user_id = u.id 
                                                         ORDER BY p.id DESC LIMIT 5");
                        while ($row = mysqli_fetch_assoc($pesanan)) :
                        ?>
                            <tr class="border-b hover:bg-gray-50">
                                <td class="py-3 px-4">#<?= $row['id']; ?></td>
                                <td class="py-3 px-4"><?= $row['nama']; ?></td>
                                <td class="py-3 px-4 font-semibold">Rp <?= number_format($row['total_harga']); ?></td>
                                <td class="py-3 px-4">
                                    <span class="px-3 py-1 rounded-full text-sm font-semibold
                                        <?php
                                        if ($row['status'] == 'pending') echo 'bg-yellow-100 text-yellow-700';
                                        elseif ($row['status'] == 'proses') echo 'bg-blue-100 text-blue-700';
                                        elseif ($row['status'] == 'selesai') echo 'bg-green-100 text-green-700';
                                        else echo 'bg-red-100 text-red-700';
                                        ?>
                                    ">
                                        <?= ucfirst($row['status']); ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
<?php require_once __DIR__ . '/../partials/footer.php'; ?>