<?php
session_start();
include 'koneksi.php';

// Periksa apakah pengguna sudah login
if (!isset($_SESSION['username'])) {
    // Jika belum, redirect ke halaman login
    header("Location: login.php");
    exit();
}

$id_pelanggan = isset($_SESSION['id_pelanggan']) ? $_SESSION['id_pelanggan'] : null;

// Ambil data riwayat pembelian dari view
$sql = "SELECT * FROM Penjualan_View WHERE id_pelanggan = ?";
$stmt = $koneksi->prepare($sql);
$stmt->bind_param("s", $id_pelanggan); // Menggunakan $id_pelanggan yang telah diambil dari session
$stmt->execute();

// Periksa apakah ada error saat menjalankan query
if ($stmt->error) {
    die("Failed to execute query: " . $stmt->error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["delete_id"])) {
    $delete_id = $_POST["delete_id"];

    // Close the statement associated with the first query
    $stmt->close();

    // Prepare the statement for delete operation
    $stmt_delete = $koneksi->prepare("CALL delete_penjualan(?)");
    $stmt_delete->bind_param("i", $delete_id);
    if ($stmt_delete->execute()) {
        // Redirect to the same page to refresh the table
        header("Location: riwayat.php");
        exit();
    } else {
        // Handle delete failure
        echo "Failed to delete record.";
    }
}

$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Pembelian</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Tambahkan CSS sesuai kebutuhan */
    </style>
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center mb-4">Riwayat Pembelian</h1>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Tanggal Pembelian</th>
                    <th>Nama Pelanggan</th>
                    <th>Alamat Pelanggan</th>
                    <th>Telepon Pelanggan</th>
                    <th>Nama Pegawai</th>
                    <th>Jabatan Pegawai</th>
                    <th>Telepon Pegawai</th>
                    <th>Merk Mobil</th>
                    <th>Model Mobil</th>
                    <th>Tahun Mobil</th>
                    <th>Warna Mobil</th>
                    <th>Harga Mobil</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    $no = 1;
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $no++ . "</td>";
                        echo "<td>" . $row["tanggal_penjualan"] . "</td>";
                        echo "<td>" . $row["nama_pelanggan"] . "</td>";
                        echo "<td>" . $row["alamat_pelanggan"] . "</td>";
                        echo "<td>" . $row["telepon_pelanggan"] . "</td>";
                        echo "<td>" . $row["nama_pegawai"] . "</td>";
                        echo "<td>" . $row["jabatan_pegawai"] . "</td>";
                        echo "<td>" . $row["telepon_pegawai"] . "</td>";
                        echo "<td>" . $row["merk_mobil"] . "</td>";
                        echo "<td>" . $row["model_mobil"] . "</td>";
                        echo "<td>" . $row["tahun_mobil"] . "</td>";
                        echo "<td>" . $row["warna_mobil"] . "</td>";
                        echo "<td>" . $row["harga_mobil"] . "</td>";
                        echo "<td>";
                        echo '<form method="post" onsubmit="return confirm(\'Are you sure you want to delete this record?\')">';
                        echo '<input type="hidden" name="delete_id" value="' . $row["id_penjualan"] . '">';
                        echo '<button type="submit" class="btn btn-danger">Delete</button>';
                        echo '</form>';
                        echo "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='13'>Tidak ada riwayat pembelian.</td></tr>";
                }
                ?>
            </tbody>
        </table>
        <div class="text-center">
            <a href="index.php" class="btn btn-primary">Kembali</a>
        </div>
    </div>
</body>
</html>
