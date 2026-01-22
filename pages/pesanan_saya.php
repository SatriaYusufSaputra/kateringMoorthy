<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/koneksi.php';


// Cek login
if (!isset($_SESSION['user'])) {
    header('Location: ../login.php');
    exit;
}

$user_id = $_SESSION['user'];

// Ambil semua pesanan user
$pesanan = mysqli_query(
    $koneksi,
    "SELECT * FROM pesanan 
     WHERE user_id = $user_id 
     ORDER BY created_at DESC"
);
require_once __DIR__ . '/partials/navbar.php';
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Pesanan Saya - Katering Moorthy</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>

<body class="bg-gray-50">
    <!-- HEADER -->
    <section class="bg-green-500 text-white py-10">
        <div class="max-w-6xl mx-auto px-4">
            <h1 class="text-3xl font-bold">Pesanan Saya</h1>
            <p class="mt-1">Riwayat pemesanan katering Anda</p>
        </div>
    </section>

    <!-- CONTENT -->
    <section class="py-10">
        <div class="max-w-6xl mx-auto px-4">

            <!-- ALERT SUKSES -->
            <?php if (isset($_GET['sukses'])): ?>
                <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg">
                    Pesanan berhasil dibuat. ID Pesanan: <b>#<?= htmlspecialchars($_GET['sukses']); ?></b>
                </div>
            <?php endif; ?>

            <?php if (mysqli_num_rows($pesanan) == 0): ?>
                <div class="bg-white p-8 rounded-xl shadow text-center">
                    <p class="text-gray-600">Belum ada pesanan.</p>
                    <a href="../index.php" class="inline-block mt-4 bg-green-600 text-white px-6 py-2 rounded-lg font-semibold hover:bg-green-700">
                        Mulai Pesan
                    </a>
                </div>
            <?php else: ?>
                <div class="space-y-6">
                    <?php while ($row = mysqli_fetch_assoc($pesanan)): ?>
                        <div class="bg-white rounded-xl shadow-lg p-6">
                            <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-4">
                                <div>
                                    <h3 class="text-lg font-bold text-gray-800">
                                        Pesanan #<?= $row['id']; ?>
                                    </h3>
                                    <p class="text-sm text-gray-500">
                                        Tanggal Pesan: <?= date('d M Y H:i', strtotime($row['created_at'])); ?>
                                    </p>
                                    <p class="text-sm text-gray-500">
                                        Tanggal Pengiriman: 
                                        <span class="font-semibold">
                                            <?= date('d M Y', strtotime($row['tanggal_pengiriman'])); ?>
                                        </span>
                                    </p>
                                </div>

                                <div class="text-right">
                                    <p class="text-lg font-bold text-green-600">
                                        Rp <?= number_format($row['total_harga']); ?>
                                    </p>
                                    <span class="inline-block mt-1 px-3 py-1 text-sm rounded-full
                                        <?php
                                        switch ($row['status']) {
                                            case 'pending':
                                                echo 'bg-yellow-100 text-yellow-700';
                                                break;
                                            case 'diproses':
                                                echo 'bg-blue-100 text-blue-700';
                                                break;
                                            case 'dikirim':
                                                echo 'bg-indigo-100 text-indigo-700';
                                                break;
                                            case 'selesai':
                                                echo 'bg-green-100 text-green-700';
                                                break;
                                            case 'batal':
                                                echo 'bg-red-100 text-red-700';
                                                break;
                                        }
                                        ?>">
                                        <?= ucfirst($row['status']); ?>
                                    </span>
                                </div>
                            </div>

                            <!-- DETAIL MENU -->
                            <div class="mt-4 border-t pt-4">
                                <h4 class="font-semibold text-gray-700 mb-2">Detail Pesanan</h4>
                                <div class="space-y-2 text-sm">
                                    <?php
                                    $detail = mysqli_query(
                                        $koneksi,
                                        "SELECT d.*, m.nama_menu 
                                         FROM detail_pesanan d
                                         JOIN menu m ON d.menu_id = m.id
                                         WHERE d.pesanan_id = {$row['id']}"
                                    );
                                    while ($d = mysqli_fetch_assoc($detail)):
                                    ?>
                                        <div class="flex justify-between">
                                            <span><?= htmlspecialchars($d['nama_menu']); ?> x<?= $d['qty']; ?></span>
                                            <span>
                                                Rp <?= number_format($d['qty'] * $d['harga']); ?>
                                            </span>
                                        </div>
                                    <?php endwhile; ?>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php endif; ?>
        </div>
    </section>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>
