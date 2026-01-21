<?php
session_start();
include '../koneksi.php';

// Cek apakah user adalah admin
if (!isset($_SESSION['user']) || $_SESSION['role'] != 'admin') {
    header('Location: ../login.php');
    exit;
}

$message = '';
$message_type = '';
$admin_id = $_SESSION['user'];

// Proses ubah role
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];

    if ($action == 'ubah_role') {
        $user_id = (int)$_POST['user_id'];
        $role_baru = $_POST['role'];

        if ($user_id == $admin_id) {
            $message = 'Anda tidak bisa mengubah role akun sendiri!';
            $message_type = 'error';
        } else {
            $query = "UPDATE users SET role='$role_baru' WHERE id=$user_id";
            if (mysqli_query($koneksi, $query)) {
                $message = 'Role berhasil diubah';
                $message_type = 'success';
            }
        }
    }

    if ($action == 'hapus') {
        $user_id = (int)$_POST['user_id'];

        if ($user_id == $admin_id) {
            $message = 'Anda tidak bisa menghapus akun sendiri!';
            $message_type = 'error';
        } else {
            $query = "DELETE FROM users WHERE id=$user_id";
            if (mysqli_query($koneksi, $query)) {
                $message = 'User berhasil dihapus';
                $message_type = 'success';
            }
        }
    }
}

// Ambil semua users
$users = mysqli_query($koneksi, "SELECT * FROM users ORDER BY id DESC");
$user_data = mysqli_fetch_all($users, MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Kelola Pengguna - Admin</title>
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
                        <a href="users.php" class="block px-4 py-2 bg-green-600 text-white rounded-lg font-semibold">
                            Pengguna
                        </a>
                    </li>
                </ul>
            </div>
        </aside>

        <!-- MAIN CONTENT -->
        <main class="flex-1 p-8">
            <h2 class="text-3xl font-bold text-gray-800 mb-8">Kelola Pengguna</h2>

            <!-- Alert Message -->
            <?php if ($message): ?>
                <div class="mb-4 p-4 rounded-lg <?php echo $message_type == 'success' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'; ?>">
                    <?= $message; ?>
                </div>
            <?php endif; ?>

            <!-- Tabel Users -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <table class="w-full">
                    <thead class="bg-gray-100 border-b">
                        <tr>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">No</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Nama</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Email</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Role</th>
                            <th class="px-6 py-3 text-center text-sm font-semibold text-gray-700">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($user_data) > 0): ?>
                            <?php foreach ($user_data as $no => $user): ?>
                                <tr class="border-b hover:bg-gray-50">
                                    <td class="px-6 py-4 text-sm text-gray-600"><?= $no + 1; ?></td>
                                    <td class="px-6 py-4 text-sm text-gray-800 font-medium"><?= htmlspecialchars($user['nama']); ?></td>
                                    <td class="px-6 py-4 text-sm text-gray-600"><?= htmlspecialchars($user['email']); ?></td>
                                    <td class="px-6 py-4 text-sm">
                                        <span class="px-3 py-1 rounded-full text-white text-xs font-semibold <?php echo $user['role'] == 'admin' ? 'bg-red-500' : 'bg-blue-500'; ?>">
                                            <?= ucfirst($user['role']); ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-center space-x-2">
                                        <?php if ($user['id'] != $admin_id): ?>
                                            <!-- Ubah Role -->
                                            <form method="POST" style="display: inline;">
                                                <input type="hidden" name="action" value="ubah_role">
                                                <input type="hidden" name="user_id" value="<?= $user['id']; ?>">
                                                <select name="role" class="px-2 py-1 border rounded text-xs" onchange="this.form.submit()">
                                                    <option value="">-- Ubah Role --</option>
                                                    <option value="customer" <?php echo $user['role'] == 'customer' ? 'selected' : ''; ?>>Customer</option>
                                                    <option value="admin" <?php echo $user['role'] == 'admin' ? 'selected' : ''; ?>>Admin</option>
                                                </select>
                                            </form>

                                            <!-- Hapus -->
                                            <button onclick="if(confirm('Hapus user ini?')) { document.getElementById('form_hapus_<?= $user['id']; ?>').submit(); }" class="bg-red-500 text-white px-3 py-1 rounded text-xs hover:bg-red-600">
                                                Hapus
                                            </button>
                                            <form method="POST" id="form_hapus_<?= $user['id']; ?>" style="display: none;">
                                                <input type="hidden" name="action" value="hapus">
                                                <input type="hidden" name="user_id" value="<?= $user['id']; ?>">
                                            </form>
                                        <?php else: ?>
                                            <span class="px-3 py-1 bg-gray-300 text-gray-700 rounded text-xs">Akun Anda</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="px-6 py-8 text-center text-gray-500">Tidak ada user</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <p class="text-gray-600 mt-4">Total: <strong><?= count($user_data); ?></strong> user</p>
        </main>
    </div>
</body>

</html>