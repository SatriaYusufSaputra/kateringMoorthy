<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../koneksi.php';


$menu = mysqli_query($koneksi, "SELECT * FROM menu ORDER BY id DESC");
require_once __DIR__ . '/../partials/navbar.php';
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu - Moorthy Shop</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>

<body class="bg-gray-50">

    <!-- MENU HEADER -->
    <section class="bg-green-500 text-white py-12">
        <div class="max-w-6xl mx-auto px-4 text-center">
            <h1 class="text-4xl font-bold">Pilih Menu Favorit Anda</h1>
            <p class="mt-2">Berbagai pilihan menu lezat dan berkualitas</p>
        </div>
    </section>

    <!-- MENU ITEMS -->
    <section class="py-16">
        <div class="max-w-6xl mx-auto px-4">
            <div class="grid md:grid-cols-3 gap-8">
                <?php while ($row = mysqli_fetch_assoc($menu)) : ?>
                    <div class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-2xl transition">
                        <img src="../assets/img/<?= $row['foto']; ?>" class="h-48 w-full object-cover">

                        <div class="p-5">
                            <h3 class="text-xl font-bold text-gray-800 mb-2"><?= $row['nama_menu']; ?></h3>

                            <p class="text-gray-600 text-sm mb-2">
                                <?= substr($row['deskripsi'], 0, 50); ?>...
                            </p>

                            <button
                                onclick="openModal(
                                    '<?= htmlspecialchars($row['nama_menu'], ENT_QUOTES); ?>',
                                    '<?= htmlspecialchars($row['deskripsi'], ENT_QUOTES); ?>',
                                    <?= $row['harga']; ?>,
                                    '<?= $row['foto']; ?>'
                                )"
                                class="text-green-600 font-semibold text-sm hover:underline mb-3">
                                Lihat Detail
                            </button>

                            <div class="flex justify-between items-center mb-4">
                                <span class="text-2xl font-bold text-green-600">
                                    Rp <?= number_format($row['harga']); ?>
                                </span>
                            </div>

                            <form action="../proses/tambah_keranjang.php" method="POST" class="mb-3">
                                <input type="hidden" name="menu_id" value="<?= $row['id']; ?>">
                                <input type="hidden" name="nama_menu" value="<?= $row['nama_menu']; ?>">
                                <input type="hidden" name="harga" value="<?= $row['harga']; ?>">

                                <div class="flex gap-2">
                                    <input type="number" name="jumlah" value="1" min="1" required
                                        class="w-16 px-2 py-2 border border-gray-300 rounded text-center">
                                    <button type="submit" class="flex-1 bg-green-600 text-white py-2 rounded-lg font-semibold hover:bg-green-700">
                                        + Keranjang
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>

            <?php if (mysqli_num_rows($menu) == 0): ?>
                <div class="text-center py-12">
                    <p class="text-gray-500 text-lg">Belum ada menu tersedia</p>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <?php include '../partials/footer.php'; ?>
    
    <!-- MODAL -->
    <div id="menuModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50">
        <div class="bg-white max-w-lg w-full rounded-xl shadow-lg p-6 relative">
            <button onclick="closeModal()" class="absolute top-3 right-3 text-gray-500 hover:text-gray-800">
                ✕
            </button>

            <img id="modalImage" src="" class="w-full h-48 object-cover rounded-lg mb-4">

            <h2 id="modalTitle" class="text-2xl font-bold text-gray-800 mb-2"></h2>

            <p id="modalDesc" class="text-gray-600 mb-4"></p>

            <div class="text-right">
                <span id="modalPrice" class="text-2xl font-bold text-green-600"></span>
            </div>
        </div>
    </div>
    <script>
    function openModal(nama, deskripsi, harga, foto) {
        document.getElementById('modalTitle').innerText = nama;
        document.getElementById('modalDesc').innerText = deskripsi;
        document.getElementById('modalPrice').innerText = 'Rp ' + harga.toLocaleString('id-ID');
        document.getElementById('modalImage').src = '../assets/img/' + foto;

        document.getElementById('menuModal').classList.remove('hidden');
        document.getElementById('menuModal').classList.add('flex');
    }

    function closeModal() {
        document.getElementById('menuModal').classList.add('hidden');
    }
    </script>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>