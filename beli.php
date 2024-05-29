<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$id_pegawai_err = "";

$sql_pegawai = "SELECT id_pegawai, nama FROM Pegawai";
$result_pegawai = $koneksi->query($sql_pegawai);
$pegawai_options = "";
$valid_pegawai_ids = array(); // Inisialisasi variabel $valid_pegawai_ids
if ($result_pegawai->num_rows > 0) {
    while ($row = $result_pegawai->fetch_assoc()) {
        $pegawai_options .= '<option value="' . $row["id_pegawai"] . '">' . $row["nama"] . '</option>';
        $valid_pegawai_ids[] = $row["id_pegawai"]; // Tambahkan id_pegawai ke dalam array $valid_pegawai_ids
    }
}

if (isset($_POST["id_pegawai"]) && in_array($_POST["id_pegawai"], $valid_pegawai_ids)) {
    $id_pegawai = $_POST["id_pegawai"];
} else {
    $id_pegawai_err = "Invalid employee ID.";
}

function generate_penjualan_id($koneksi)
{
    $sql = "SELECT generate_penjualan_id() AS id";
    $result = $koneksi->query($sql);
    $row = $result->fetch_assoc();
    return $row['id'];
}

// Ambil data dari session
$id_pelanggan = isset($_SESSION['id_pelanggan']) ? $_SESSION['id_pelanggan'] : null;
$id_mobil = isset($_SESSION['id_mobil']) ? $_SESSION['id_mobil'] : null;
$tanggal_penjualan = date('Y-m-d');
$harga_penjualan = isset($_SESSION['harga_penjualan']) ? $_SESSION['harga_penjualan'] : 0;
$merk = isset($_SESSION['merk']) ? $_SESSION['merk'] : '';
$model = isset($_SESSION['model']) ? $_SESSION['model'] : '';
$tahun = isset($_SESSION['tahun']) ? $_SESSION['tahun'] : '';
$warna = isset($_SESSION['warna']) ? $_SESSION['warna'] : '';
$harga = isset($_SESSION['harga']) ? $_SESSION['harga'] : 0;
$id_pegawai = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_POST["id_pegawai"]) || trim($_POST["id_pegawai"]) === '') {
        $id_pegawai_err = "Please select employee.";
    } else {
        $id_pegawai = trim($_POST["id_pegawai"]);
    }
}

// Cek apakah id_mobil ada di tabel mobil
$sql_check = "SELECT id_mobil FROM mobil WHERE id_mobil = ?";
$stmt_check = $koneksi->prepare($sql_check);
$stmt_check->bind_param("i", $id_mobil);
$stmt_check->execute();
$stmt_check->store_result();

// Check if id_pelanggan exists in the pelanggan table
// Check if the id_pegawai exists in the pegawai table
$sql_check_pegawai = "SELECT id_pegawai FROM pegawai WHERE id_pegawai = ?";
$stmt_check_pegawai = $koneksi->prepare($sql_check_pegawai);
$stmt_check_pegawai->bind_param("i", $id_pegawai);
$stmt_check_pegawai->execute();
$stmt_check_pegawai->store_result();

if ($stmt_check_pegawai->num_rows > 0) {
    // Generate the penjualan ID
    $id_penjualan = generate_penjualan_id($koneksi);
    // Call the stored procedure create_penjualan
    $sql_create_penjualan = "CALL create_penjualan(?, ?, ?, CURDATE(), ?)";
    $stmt_create_penjualan = $koneksi->prepare($sql_create_penjualan);
    $stmt_create_penjualan->bind_param("iiid", $id_pelanggan, $id_pegawai, $id_mobil, $harga_penjualan);
    // Execute the prepared statement
    if ($stmt_create_penjualan->execute()) {
        // Redirect to riwayat.php on successful purchase
        header("location: riwayat.php");
        exit; // Exit the script
    } else {
        // Handle any errors during execution
        echo "Error executing the prepared statement: " . $stmt_create_penjualan->error;
    }
} else {
    echo "Invalid employee ID. Please select a valid employee.";
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beli Mobil</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center mb-4">Beli Mobil</h1>
        <div class="row justify-content-center">
            <div class="col-md-6">
                <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                    <div class="mb-3">
                        <label for="pegawai" class="form-label">Pilih Pegawai</label>
                        <select class="form-select" id="pegawai" name="id_pegawai" required>
                            <?php echo $pegawai_options; ?>
                        </select>
                        <span class="text-danger"><?php echo $id_pegawai_err; ?></span>
                    </div>
                    <div class="text-center">
                        <button type="submit" class="btn btn-primary">Beli</button>
                        <a href="index.php" class="btn btn-secondary">Batalkan</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>