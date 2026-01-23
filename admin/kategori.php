<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../koneksi.php';

// Cek admin
if (!isset($_SESSION['user']) || $_SESSION['role'] != 'admin') {
    header('Location: ../login.php');
    exit;
}

/* =========================
   TAMBAH / EDIT KATEGORI
========================= */
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama_kategori']);
    $minimal = (int) $_POST['minimal_pembelian'];

    if ($_POST['action'] == 'tambah') {
        mysqli_query($koneksi,
            "INSERT INTO kategori_menu (nama_kategori, minimal_pembelian)
             VALUES ('$nama', '$minimal')"
        );
        $success = "Kategori berhasil ditambahkan";
    }

    if ($_POST['action'] == 'edit') {
        $id = $_POST['id'];
        mysqli_query($koneksi,
            "UPDATE kategori_menu
             SET nama_kategori='$nama', minimal_pembelian='$minimal'
             WHERE id='$id'"
        );
        $success = "Kategori berhasil diperbarui";
    }
}

/* =========================
   HAPUS KATEGORI
========================= */
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    mysqli_query($koneksi, "DELETE FROM kategori_menu WHERE id='$id'");
    $success = "Kategori berhasil dihapus";
}

/* =========================
   AMBIL DATA
========================= */
$kategori = mysqli_query($koneksi, "SELECT * FROM kategori_menu ORDER BY id DESC");

$edit = null;
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $edit = mysqli_fetch_assoc(
        mysqli_query($koneksi, "SELECT * FROM kategori_menu WHERE id='$id'")
    );
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kelola Kategori</title>
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
    <div class="flex min-h-screen">
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
                        <a href="pesanan.php" class="block px-4 py-2 text-gray-700 hover:bg-gray-100 rounded-lg font-semibold">
                            Pesanan
                        </a>
                    </li>
                    <li>
                        <a href="kategori.php" class="block px-4 py-2 bg-green-600 text-white rounded-lg font-semibold">
                            Kelola Kategori
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

    <!-- CONTENT -->
    <main class="flex-1 p-8">

        <h2 class="text-3xl font-bold text-gray-800 mb-6">Kelola Kategori</h2>

        <?php if (isset($success)): ?>
            <div class="mb-6 p-4 rounded-lg bg-green-100 text-green-700 font-medium">
                <?= $success ?>
            </div>
        <?php endif; ?>

        <div class="grid md:grid-cols-3 gap-6">

            <!-- FORM -->
            <div class="bg-white p-6 rounded-xl shadow">
                <h3 class="text-xl font-semibold mb-4">
                    <?= $edit ? 'Edit Kategori' : 'Tambah Kategori' ?>
                </h3>

                <form method="POST" class="space-y-4">
                    <input type="hidden" name="action" value="<?= $edit ? 'edit' : 'tambah' ?>">
                    <?php if ($edit): ?>
                        <input type="hidden" name="id" value="<?= $edit['id'] ?>">
                    <?php endif; ?>

                    <div>
                        <label class="block mb-1 text-sm font-medium">Nama Kategori</label>
                        <input type="text" name="nama_kategori" required
                            value="<?= $edit['nama_kategori'] ?? '' ?>"
                            class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-green-500">
                    </div>

                    <div>
                        <label class="block mb-1 text-sm font-medium">Minimal Pembelian</label>
                        <input type="number" name="minimal_pembelian" min="1" required
                            value="<?= $edit['minimal_pembelian'] ?? 1 ?>"
                            class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-green-500">
                    </div>

                    <button
                        class="w-full bg-green-600 hover:bg-green-700 text-white py-2 rounded-lg font-semibold transition">
                        <?= $edit ? 'Update' : 'Tambah' ?>
                    </button>

                    <?php if ($edit): ?>
                        <a href="kategori.php" class="block text-center text-sm text-gray-500 hover:underline">
                            Batal
                        </a>
                    <?php endif; ?>
                </form>
            </div>

            <!-- TABLE -->
            <div class="md:col-span-2 bg-white p-6 rounded-xl shadow">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b text-gray-600">
                            <th class="text-left py-3">Kategori</th>
                            <th class="text-left py-3">Minimal</th>
                            <th class="text-left py-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = mysqli_fetch_assoc($kategori)): ?>
                            <tr class="border-b hover:bg-gray-50">
                                <td class="py-3"><?= $row['nama_kategori'] ?></td>
                                <td class="py-3"><?= $row['minimal_pembelian'] ?></td>
                                <td class="py-3 space-x-2">
                                    <a href="?edit=<?= $row['id'] ?>" class="text-blue-600 hover:underline">
                                        Edit
                                    </a>
                                    <a href="?delete=<?= $row['id'] ?>"
                                       onclick="return confirm('Hapus kategori?')"
                                       class="text-red-600 hover:underline">
                                        Hapus
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>

        </div>
    </main>
</div>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>
