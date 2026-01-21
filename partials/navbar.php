<!-- NAVBAR -->
<nav class="bg-white shadow">
    <div class="max-w-6xl mx-auto px-4 py-4 flex justify-between items-center">
        <h1 class="text-2xl font-bold text-green-600">Katering Moorthy</h1>
        <div class="space-x-4">
            <a href="pages/menu.php" class="text-gray-700 hover:text-green-600">Lihat Menu</a>
            <?php if (!isset($_SESSION['user'])): ?>
                <a href="login.php" class="text-gray-700 hover:text-green-600">Login</a>
                <a href="register.php" class="text-gray-700 hover:text-green-600">Register</a>
            <?php else: ?>
                <a href="pages/keranjang.php" class="text-gray-700 hover:text-green-600">Keranjang</a>
                <a href="pages/pesanan_saya.php" class="text-gray-700 hover:text-green-600">Pesanan Saya</a>
                <a href="logout.php" class="text-gray-700 hover:text-green-600">Logout</a>
            <?php endif; ?>
        </div>
    </div>
</nav>