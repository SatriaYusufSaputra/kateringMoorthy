<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/koneksi.php';

// Query menu dari database
$query_menu = "SELECT * FROM menu LIMIT 3";
$menu = mysqli_query($koneksi, $query_menu);
?>

<?php 
$page_title = 'Index - Moorthy Shop';
require_once __DIR__ . '/partials/header.php';
require_once __DIR__ . '/partials/navbar.php'; 
?>

    <!-- HERO -->
    <section class="bg-green-600 text-white py-20">
        <div class="max-w-6xl mx-auto px-4 text-center">
            <h2 class="text-4xl font-bold mb-4">
                Solusi Katering Praktis & Lezat
            </h2>
            <p class="mb-6">
                Pesan katering harian, acara, dan nasi box dengan mudah.
            </p>
            <a href="pages/menu.php" class="bg-white text-green-600 px-6 py-3 rounded-lg font-semibold">
                Lihat Menu
            </a>
        </div>
    </section>

    <!-- MENU PREVIEW -->
    <section class="py-14">
        <div class="max-w-6xl mx-auto px-4">
            <h3 class="text-2xl font-bold text-center mb-8">Menu Favorit</h3>

            <div class="grid md:grid-cols-3 gap-6">
                <?php while ($row = mysqli_fetch_assoc($menu)) : ?>
                    <div class="bg-white rounded-xl shadow p-4">
                        <img src="assets/img/<?= $row['foto']; ?>" class="h-40 w-full object-cover rounded-lg">
                        <h4 class="text-lg font-semibold mt-3"><?= $row['nama_menu']; ?></h4>
                        <p class="text-green-600 font-bold">
                            Rp <?= number_format($row['harga']); ?>
                        </p>
                    </div>
                <?php endwhile; ?>
            </div>

            <div class="text-center mt-8">
                <a href="pages/menu.php" class="text-green-600 font-semibold">
                    Lihat semua menu →
                </a>
            </div>
        </div>
    </section>
<?php require_once __DIR__ . '/partials/footer.php'; ?>
