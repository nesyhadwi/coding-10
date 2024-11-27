<?php
session_start();
include 'db_connection.php'; // Koneksi ke database

// Cek apakah pengguna sudah login
if (!isset($_SESSION['nis'])) {
    header("Location: login.php");
    exit();
}

$nis = $_SESSION['nis'];

// Ambil data nasabah berdasarkan NIS
$queryNasabah = "SELECT account_number, balance FROM data_diri WHERE nis = ?";
$stmtNasabah = $conn->prepare($queryNasabah);
if ($stmtNasabah) {
    $stmtNasabah->bind_param("s", $nis);
    $stmtNasabah->execute();
    $stmtNasabah->bind_result($no_rekening, $saldo);
    $stmtNasabah->fetch();
    $stmtNasabah->close();
} else {
    echo "Error preparing statement.";
}

// Jika formulir disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $jenis_transaksi = $_POST['jenis_transaksi'];
    $nominal = $_POST['nominal'];
    $saldo_awal = $saldo;

    if ($jenis_transaksi === 'penarikan' && $nominal > $saldo) {
        echo "Saldo tidak mencukupi untuk melakukan penarikan.";
    } else {
        if ($jenis_transaksi === 'setoran') {
            $saldo_akhir = $saldo + $nominal;
        } elseif ($jenis_transaksi === 'penarikan') {
            $saldo_akhir = $saldo - $nominal;
        }

        // Update saldo pengguna di database
        $updateSaldo = "UPDATE data_diri SET balance = ? WHERE account_number = ?";
        $stmtUpdateSaldo = $conn->prepare($updateSaldo);
        if ($stmtUpdateSaldo) {
            $stmtUpdateSaldo->bind_param("ds", $saldo_akhir, $no_rekening);
            $stmtUpdateSaldo->execute();
            $stmtUpdateSaldo->close();
        }

        // Simpan transaksi ke dalam riwayat_transaksi
        $insertTransaksi = "INSERT INTO riwayat_transaksi (account_number, tanggal_transaksi, jenis_transaksi, nominal, saldo_awal, saldo_akhir) 
                            VALUES (?, NOW(), ?, ?, ?, ?)";
        $stmtInsertTransaksi = $conn->prepare($insertTransaksi);
        if ($stmtInsertTransaksi) {
            $stmtInsertTransaksi->bind_param("ssddd", $no_rekening, $jenis_transaksi, $nominal, $saldo_awal, $saldo_akhir);
            $stmtInsertTransaksi->execute();
            $stmtInsertTransaksi->close();
            header("Location: riwayat-transaksi-user.php");
        } else {
            echo "Error preparing statement.";
        }

        // Update saldo session
        $saldo = $saldo_akhir;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulir Transaksi</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 50%;
            margin: auto;
            padding: 20px;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h2 {
            text-align: center;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
        }
        input[type="number"], select {
            width: 100%;
            padding: 8px;
            box-sizing: border-box;
        }
        input[type="submit"] {
            width: 100%;
            padding: 10px;
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
        }
        input[type="submit"]:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Formulir Transaksi</h2>
    <form method="POST">
        <div class="form-group">
            <label for="jenis_transaksi">Jenis Transaksi:</label>
            <select name="jenis_transaksi" id="jenis_transaksi" required>
                <option value="setoran">setoran</option>
                <option value="penarikan">penarikan</option>
            </select>
        </div>
        <div class="form-group">
            <label for="nominal">Nominal:</label>
            <input type="number" name="nominal" id="nominal" required min="1">
        </div>
        <input type="submit" value="Submit">
    </form>
</div>

</body>
</html>
