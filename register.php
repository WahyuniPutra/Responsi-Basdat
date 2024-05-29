<?php
session_start();
include 'koneksi.php';

function generate_pelanggan_id($koneksi)
{
    $sql = "SELECT generate_pelanggan_id() AS id";
    $result = $koneksi->query($sql);
    $row = $result->fetch_assoc();
    return $row['id'];
}
$pesan_error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil nilai dari form
    $username = $_POST["username"];
    $password = $_POST["password"];
    $nama = $_POST["nama"];
    $alamat = $_POST["alamat"];
    $telepon = $_POST["telepon"];

    // Generate pelanggan ID
    $id_pelanggan = generate_pelanggan_id($koneksi);

    // Panggil stored procedure create_pelanggan untuk menambahkan pelanggan baru
    $stmt = $koneksi->prepare("CALL create_pelanggan(?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("isssss", $id_pelanggan, $username, $password, $nama, $alamat, $telepon); // Adjusted to match the number of parameters
    if ($stmt->execute()) {
        // Redirect ke halaman login setelah berhasil mendaftar
        header("Location: login.php");
        exit();
    } else {
        $pesan_error = "Gagal mendaftar. Silakan coba lagi.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Tambahkan CSS sesuai kebutuhan */
    </style>
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center mb-4">Register</h1>
        <div class="row justify-content-center">
            <div class="col-md-6">
                <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" class="form-control" id="username" name="username" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <div class="mb-3">
                        <label for="nama" class="form-label">Nama</label>
                        <input type="text" class="form-control" id="nama" name="nama" required>
                    </div>
                    <div class="mb-3">
                        <label for="alamat" class="form-label">Alamat</label>
                        <input type="text" class="form-control" id="alamat" name="alamat" required>
                    </div>
                    <div class="mb-3">
                        <label for="telepon" class="form-label">Telepon</label>
                        <input type="text" class="form-control" id="telepon" name="telepon" required>
                    </div>
                    <div class="text-center">
                        <button type="submit" class="btn btn-primary">Register</button>
                        <a href="login.php" class="btn btn-secondary">Login</a>
                    </div>
                    <?php
                    // Tampilkan pesan error jika ada
                    if (!empty($pesan_error)) {
                        echo '<div class="alert alert-danger mt-3" role="alert">' . $pesan_error . '</div>';
                    }
                    ?>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
