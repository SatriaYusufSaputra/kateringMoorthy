<nav class="bg-white shadow">
    <div class="max-w-6xl mx-auto px-4 py-4 flex justify-between items-center">
        <h1 class="text-2xl font-bold text-green-600">Katering Moorthy</h1>
        <div class="space-x-4">
            <a href="<?= BASE_URL ?>/pages/menu.php">Lihat Menu</a>

            <?php if (!isset($_SESSION['user'])): ?>
                <a href="<?= BASE_URL ?>/login.php">Login</a>
                <a href="<?= BASE_URL ?>/register.php">Register</a>
            <?php else: ?>
                <a href="<?= BASE_URL ?>/pages/keranjang.php">Keranjang</a>
                <a href="<?= BASE_URL ?>/pages/pesanan_saya.php">Pesanan Saya</a>
                <a href="<?= BASE_URL ?>/logout.php">Logout</a>
            <?php endif; ?>
        </div>
    </div>
</nav>
