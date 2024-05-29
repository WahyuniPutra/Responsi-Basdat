<?php
session_start();
include 'koneksi.php';

// Periksa apakah pengguna sudah login sebagai admin
if (!isset($_SESSION['username'])) {
    // Jika belum, redirect ke halaman login
    header("Location: login.php");
    exit();
}

// Function untuk menghasilkan ID mobil baru
function generate_mobil_id($koneksi)
{
    $sql = "SELECT generate_mobil_id() AS id";
    $result = $koneksi->query($sql);
    $row = $result->fetch_assoc();
    return $row['id'];
}

// Inisialisasi variabel
$merk = $model = $tahun = $warna = $foto = $stok = $harga = "";
$pesan_error = "";

$foto = "";
if (!isset($_FILES["foto"]) || $_FILES["foto"]["error"] != UPLOAD_ERR_OK) {
    $pesan_error = "Please upload a photo of the car.";
} else {
    // Read the file into a string
    $foto = file_get_contents($_FILES["foto"]["tmp_name"]);
}
// Jika tombol Submit diklik
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil nilai dari form
    $merk = $_POST["merk"];
    $model = $_POST["model"];
    $tahun = $_POST["tahun"];
    $warna = $_POST["warna"];
    $foto = file_get_contents($_FILES["foto"]["tmp_name"]); // Read the file into a string
    $stok = $_POST["stok"];
    $harga = $_POST["harga"];

    // Panggil stored procedure tambah_mobil untuk menambahkan mobil baru
    $stmt = $koneksi->prepare("CALL create_mobil(?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssisi", $merk, $model, $tahun, $warna, $harga, $foto, $stok);
    
    // Check if the execution is successful
    if ($stmt->execute()) {
        $stmt->close();
        // Redirect to index.php after successfully adding the car
        header("Location: index.php");
        exit();
    } else {
        // Handle any errors during execution
        $pesan_error = "Failed to add the car. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Mobil</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Tambahkan CSS sesuai kebutuhan */
    </style>
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center mb-4">Tambah Mobil Baru</h1>
        <div class="row justify-content-center">
            <div class="col-md-6">
                <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" enctype="multipart/form-data"> <!-- Add enctype to this form tag -->
                    <div class="mb-3">
                        <label for="merk" class="form-label">Merk</label>
                        <input type="text" class="form-control" id="merk" name="merk" required>
                    </div>
                    <div class="mb-3">
                        <label for="model" class="form-label">Model</label>
                        <input type="text" class="form-control" id="model" name="model" required>
                    </div>
                    <div class="mb-3">
                        <label for="tahun" class="form-label">Tahun</label>
                        <input type="text" class="form-control" id="tahun" name="tahun" required>
                    </div>
                    <div class="mb-3">
                        <label for="warna" class="form-label">Warna</label>
                        <input type="text" class="form-control" id="warna" name="warna" required>
                    </div>
                    <div class="mb-3">
                        <label for="stok" class="form-label">Stok</label>
                        <input type="number" class="form-control" id="stok" name="stok" required>
                    </div>
                    <div class="mb-3">
                        <label for="foto" class="form-label">Foto</label>
                        <input type="file" class="form-control" id="foto" name="foto" required>
                    </div>
                    <div class="mb-3">
                        <label for="harga" class="form-label">Harga</label>
                        <input type="number" class="form-control" id="harga" name="harga" required>
                    </div>
                    <div class="text-center">
                        <button type="submit" class="btn btn-primary">Submit</button>
                        <a href="index.php" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
                <?php
                // Tampilkan pesan error jika ada
                if (!empty($pesan_error)) {
                    echo '<div class="alert alert-danger mt-3" role="alert">' . $pesan_error . '</div>';
                }
                ?>
            </div>
        </div>
    </div>
</body>
</html>