<?php
session_start();
include 'koneksi.php';

// Inisialisasi variabel
$username = $password = "";
$username_err = $password_err = "";

// Memproses data yang di-submit ketika form disubmit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validasi input username
    if (empty(trim($_POST["username"]))) {
        $username_err = "Please enter your username.";
    } else {
        $username = trim($_POST["username"]);
    }

    // Validasi input password
    if (empty(trim($_POST["password"]))) {
        $password_err = "Please enter your password.";
    } else {
        $password = trim($_POST["password"]);
    }

    // Jika tidak ada error validasi
    if (empty($username_err) && empty($password_err)) {
        // Siapkan statement SQL
        $sql = "SELECT id_pelanggan, username, password FROM pelanggan WHERE username = ?";

        if ($stmt = $koneksi->prepare($sql)) {
            // Bind variabel ke pernyataan persiapan sebagai parameter
            $stmt->bind_param("s", $param_username);

            // Set parameter
            $param_username = $username;

            // Mencoba untuk mengeksekusi pernyataan yang telah dipersiapkan
            if ($stmt->execute()) {
                // Simpan hasilnya
                $stmt->store_result();

                // Periksa apakah username ada, jika ya, maka verifikasi password
                if ($stmt->num_rows == 1) {
                    // Bind hasil dari kolom password
                    // Bind hasil dari kolom password
                    $stmt->bind_result($id, $username, $db_password);
                    if ($stmt->fetch()) {
                        if ($password == $db_password) {
                            // Jika password cocok, inisialisasi sesi baru
                            session_start();

                            // Simpan data sesi
                            $_SESSION["loggedin"] = true;
                            $_SESSION["id"] = $id;
                            $_SESSION["username"] = $username;

                            // Redirect ke halaman selamat datang
                            header("location: index.php");
                        } else {
                            // Jika password tidak cocok, tampilkan pesan kesalahan
                            $password_err = "Invalid username or password.";
                        }
                    }
                } else {
                    // Jika username tidak ada, tampilkan pesan kesalahan
                    $username_err = "Invalid username or password.";
                }
            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }

            // Tutup pernyataan
            $stmt->close();
        }
    }

    // Tutup koneksi
    $koneksi->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Tambahkan CSS sesuai kebutuhan */
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <h2 class="text-center mb-4">Login</h2>
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" name="username" class="form-control <?php echo (!empty($username_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $username; ?>">
                        <span class="invalid-feedback"><?php echo $username_err; ?></span>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" name="password" class="form-control <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>">
                        <span class="invalid-feedback"><?php echo $password_err; ?></span>
                    </div>
                    <div class="mb-3">
                        <input type="submit" class="btn btn-primary" value="Login">
                    </div>
                    <p>Belum Punya Akun? <a href="register.php">Sign up now</a>.</p>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
