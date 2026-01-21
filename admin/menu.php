<?php
session_start();
include '../koneksi.php';

// Cek apakah user adalah admin
if (!isset($_SESSION['user']) || $_SESSION['role'] != 'admin') {
    header('Location: ../login.php');
    exit;
}

// Proses tambah menu
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'tambah') {
    $nama_menu = $_POST['nama_menu'];
    $kategori_id = $_POST['kategori_id'];
    $harga = $_POST['harga'];
    $deskripsi = $_POST['deskripsi'];
    $foto_tmp = $_FILES['foto']['tmp_name'];

    // Sanitasi nama file
    $foto = basename($_FILES['foto']['name']);
    $foto = preg_replace('/[^A-Za-z0-9._-]/', '_', $foto);
    $foto = strtolower($foto);

    // Hindari overwrite - tambah suffix jika file sudah ada
    $upload_dir = '../assets/img/';
    $target = $upload_dir . $foto;
    $counter = 1;

    if (file_exists($target)) {
        $ext = pathinfo($foto, PATHINFO_EXTENSION);
        $base = pathinfo($foto, PATHINFO_FILENAME);
        while (file_exists($upload_dir . $base . '_' . $counter . '.' . $ext)) {
            $counter++;
        }
        $foto = $base . '_' . $counter . '.' . $ext;
    }

    if ($foto_tmp) {
        move_uploaded_file($foto_tmp, $upload_dir . $foto);
    }

    $query = "INSERT INTO menu (nama_menu, kategori_id, harga, deskripsi, foto) 
              VALUES ('$nama_menu', '$kategori_id', '$harga', '$deskripsi', '$foto')";

    if (mysqli_query($koneksi, $query)) {
        $success = "Menu berhasil ditambahkan";
    } else {
        $error = "Gagal menambahkan menu";
    }
}

// Proses edit menu
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'edit') {
    $menu_id = $_POST['menu_id'];
    $nama_menu = $_POST['nama_menu'];
    $kategori_id = $_POST['kategori_id'];
    $harga = $_POST['harga'];
    $deskripsi = $_POST['deskripsi'];

    $query = "UPDATE menu SET nama_menu='$nama_menu', kategori_id='$kategori_id', harga='$harga', deskripsi='$deskripsi' 
              WHERE id='$menu_id'";

    if (mysqli_query($koneksi, $query)) {
        $success = "Menu berhasil diperbarui";
    } else {
        $error = "Gagal memperbarui menu";
    }
}

// Proses delete menu
if (isset($_GET['delete'])) {
    $menu_id = $_GET['delete'];
    $query = "DELETE FROM menu WHERE id='$menu_id'";

    if (mysqli_query($koneksi, $query)) {
        $success = "Menu berhasil dihapus";
    } else {
        $error = "Gagal menghapus menu";
    }
}

// Ambil semua menu dengan JOIN kategori_menu
$menu = mysqli_query($koneksi, "
    SELECT menu.*, kategori_menu.nama_kategori 
    FROM menu 
    LEFT JOIN kategori_menu ON menu.kategori_id = kategori_menu.id
    ORDER BY menu.id DESC
");

// Cek menu untuk edit
$edit_menu = null;
if (isset($_GET['edit'])) {
    $edit_id = $_GET['edit'];
    $result = mysqli_query($koneksi, "SELECT * FROM menu WHERE id='$edit_id'");
    $edit_menu = mysqli_fetch_assoc($result);
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Kelola Menu - Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
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
                        <a href="menu.php" class="block px-4 py-2 bg-green-600 text-white rounded-lg font-semibold">
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
            <h2 class="text-3xl font-bold text-gray-800 mb-8">Kelola Menu</h2>

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

            <div class="grid md:grid-cols-3 gap-8">
                <!-- FORM TAMBAH/EDIT -->
                <div class="bg-white p-6 rounded-xl shadow-lg">
                    <h3 class="text-2xl font-bold text-gray-800 mb-4">
                        <?= $edit_menu ? 'Edit Menu' : 'Tambah Menu Baru'; ?>
                    </h3>

                    <form method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="action" value="<?= $edit_menu ? 'edit' : 'tambah'; ?>">
                        <?php if ($edit_menu): ?>
                            <input type="hidden" name="menu_id" value="<?= $edit_menu['id']; ?>">
                        <?php endif; ?>

                        <div class="mb-4">
                            <label class="block text-gray-700 font-semibold mb-2">Nama Menu</label>
                            <input type="text" name="nama_menu" required
                                value="<?= $edit_menu['nama_menu'] ?? ''; ?>"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-green-500">
                        </div>

                        <div class="mb-4">
                            <label class="block text-gray-700 font-semibold mb-2">Kategori</label>
                            <select name="kategori_id" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-green-500">
                                <option value="">Pilih Kategori</option>
                                <?php
                                $kategoris = mysqli_query($koneksi, "SELECT * FROM kategori_menu");
                                while ($kat = mysqli_fetch_assoc($kategoris)) :
                                ?>
                                    <option value="<?= $kat['id']; ?>" <?= ($edit_menu && $edit_menu['kategori_id'] == $kat['id']) ? 'selected' : ''; ?>>
                                        <?= $kat['nama_kategori']; ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label class="block text-gray-700 font-semibold mb-2">Harga</label>
                            <input type="number" name="harga" required
                                value="<?= $edit_menu['harga'] ?? ''; ?>"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-green-500">
                        </div>

                        <div class="mb-4">
                            <label class="block text-gray-700 font-semibold mb-2">Deskripsi</label>
                            <textarea name="deskripsi" required rows="3"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-green-500"><?= $edit_menu['deskripsi'] ?? ''; ?></textarea>
                        </div>

                        <?php if (!$edit_menu): ?>
                            <div class="mb-6">
                                <label class="block text-gray-700 font-semibold mb-2">Foto</label>
                                <input type="file" name="foto" required accept="image/*"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                            </div>
                        <?php endif; ?>

                        <button type="submit" class="w-full bg-green-600 text-white py-2 rounded-lg font-semibold hover:bg-green-700">
                            <?= $edit_menu ? 'Update Menu' : 'Tambah Menu'; ?>
                        </button>

                        <?php if ($edit_menu): ?>
                            <a href="menu.php" class="block w-full mt-2 bg-gray-400 text-white py-2 rounded-lg font-semibold hover:bg-gray-500 text-center">
                                Batal
                            </a>
                        <?php endif; ?>
                    </form>
                </div>

                <!-- DAFTAR MENU -->
                <div class="md:col-span-2 bg-white p-6 rounded-xl shadow-lg">
                    <h3 class="text-2xl font-bold text-gray-800 mb-4">Daftar Menu</h3>

                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b">
                                    <th class="text-left py-3 px-2 font-semibold text-gray-700">Nama</th>
                                    <th class="text-left py-3 px-2 font-semibold text-gray-700">Harga</th>
                                    <th class="text-left py-3 px-2 font-semibold text-gray-700">Kategori</th>
                                    <th class="text-left py-3 px-2 font-semibold text-gray-700">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = mysqli_fetch_assoc($menu)) : ?>
                                    <tr class="border-b hover:bg-gray-50">
                                        <td class="py-3 px-2"><?= $row['nama_menu']; ?></td>
                                        <td class="py-3 px-2">Rp <?= number_format($row['harga']); ?></td>
                                        <td class="py-3 px-2"><?= $row['nama_kategori'] ?? '-'; ?></td>
                                        <td class="py-3 px-2 space-x-2">
                                            <a href="menu.php?edit=<?= $row['id']; ?>" class="text-blue-600 hover:text-blue-700 font-semibold">Edit</a>
                                            <a href="menu.php?delete=<?= $row['id']; ?>" onclick="return confirm('Yakin?')" class="text-red-600 hover:text-red-700 font-semibold">Hapus</a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>

</html>