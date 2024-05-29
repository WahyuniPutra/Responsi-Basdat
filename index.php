<?php
session_start();
include 'koneksi.php';

// Deklarasi variabel nama pengguna
$nama = "";

if (isset($_SESSION['username'])) {
    $sql_pelanggan = "SELECT id_pelanggan FROM pelanggan WHERE username = ?";
    $stmt_pelanggan = $koneksi->prepare($sql_pelanggan);
    $stmt_pelanggan->bind_param("s", $_SESSION['username']);
    $stmt_pelanggan->execute();
    $stmt_pelanggan->bind_result($id_pelanggan);
    $stmt_pelanggan->fetch();
    $stmt_pelanggan->close();

    // Simpan id_pelanggan ke dalam session
    $_SESSION['id_pelanggan'] = $id_pelanggan;
}

// Periksa apakah pengguna sudah login
if (isset($_SESSION['roles'])) {
    if ($_SESSION['roles'] == "admin") {
        // Jika pengguna adalah admin, arahkan ke halaman admin
        header("location: assets/admin/index.php");
        exit;
    } elseif ($_SESSION['roles'] == "user") {
        // Jika pengguna adalah user, ambil nama pengguna dari session
        $nama = $_SESSION['username'];
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Mobil Dijual</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Tambahkan CSS sesuai kebutuhan */
    </style>
</head>
<body>
    <div class="container mt-5">
        <!-- Tambahkan bagian untuk menampilkan nama pengguna -->
        <div class="text-start mb-3">
            <?php if (!empty($_SESSION['username'])) : ?>
                <p5>Selamat datang, <?php echo $_SESSION['username']; ?></p5>
            <?php endif; ?>
        </div>
        <h1 class="text-center mb-4">Daftar Mobil Yang Dijual</h1>

        <div class="row">
            <?php
            $sql = "SELECT * FROM mobil";
            $result = $koneksi->query($sql);

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo '<div class="col-md-4 mb-4">';
                    echo '<div class="card">';
                    echo '<img src="data:image/jpeg;base64,' . base64_encode($row["foto"]) . '" class="card-img-top" alt="Mobil">';
                    echo '<div class="card-body">';
                    echo '<h5 class="card-title">' . $row["merk"] . ' ' . $row["model"] . '</h5>';
                    echo '<p class="card-text">Tahun: ' . $row["tahun"] . '</p>';
                    echo '<p class="card-text">Warna: ' . $row["warna"] . '</p>';
                    echo '<p class="card-text">Stok: ' . $row["Stok"] . '</p>';
                    echo '<p class="card-text">Harga: ' . $row["harga"] . '</p>';

                    // Simpan data mobil ke dalam session
                    $_SESSION['id_pelanggan'] = $_SESSION['id_pelanggan'];// Jika Anda sudah memiliki nilai id_pelanggan di session
                    $_SESSION['id_mobil'] = $row['id_mobil'];
                    $_SESSION['harga_penjualan'] = $row['harga'];
                    $_SESSION['merk'] = $row['merk'];
                    $_SESSION['model'] = $row['model'];
                    $_SESSION['tahun'] = $row['tahun'];
                    $_SESSION['warna'] = $row['warna'];
                    $_SESSION['harga'] = $row['harga'];

                    // Tautan Beli dengan redirect ke beli.php
                    echo '<a href="beli.php" class="btn btn-primary">Beli</a>';
                    echo '</div>';
                    echo '</div>';
                    echo '</div>';
                }
            } else {
                echo '<div class="col-md-12">';
                echo '<p>Tidak ada data mobil yang dijual.</p>';
                echo '</div>';
            }
            ?>
        </div>
        <div class="text-center mt-5">
            <a href="riwayat.php" class="btn btn-success me-3">Riwayat Pembelian</a>
            <?php
            if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
            ?>
                <a href="login.php" class="btn btn-primary me-3">Log Out</a>
            <?php
            } else {
            ?>
                <a href="login.php" class="btn btn-primary me-3">Login</a>
            <?php
            }
            ?>
            <a href="tambah.php" class="btn btn-warning">Tambah Mobil</a>
        </div>
    </div>
</body>
</html>