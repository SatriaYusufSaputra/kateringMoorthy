<?php 
session_start();
include '../koneksi.php';

$menu = mysqli_query($koneksi, "SELECT * FROM menu ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu Katering</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>
<body>
    <div>
        <h1>
            Menu Katering
        </h1>
        <div>
            <?php while($row = mysqli_fetch_assoc($menu)) : ?>
                <div>
                    <img src="../assets/img/<?= $row['gambar']; ?>" >           
                    <div>
                        <h2><?= $row['nama_menu']; ?></h2>
                        <p><?= $row['deskripsi']; ?></p>
                        <p>Rp <?= number_format($row['harga']) ?></p>

                        <?php if(isset($_SESSION['user'])) : ?>
                            <a href="../proses/tambah_keranjang.php?id=<?= $row['id']; ?>"> + Tambah ke Keranjang</a>
                        <?php else : ?>
                            <a href="../login.php"> Login Untuk Pesan</a>
                            <?php endif; ?>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
</body>
</html>