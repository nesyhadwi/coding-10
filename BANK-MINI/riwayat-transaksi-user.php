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
$queryNasabah = "SELECT data_diri.name, data_diri.nis, data_diri.password, data_diri.account_number, class.class_name, 
               major.major_name, data_diri.gender, data_diri.creation_date, data_diri.balance, data_diri.status 
        FROM data_diri 
        JOIN class ON data_diri.id_class = class.id 
        JOIN major ON data_diri.id_major = major.id
        WHERE data_diri.nis = ?";

$stmtNasabah = $conn->prepare($queryNasabah);
if ($stmtNasabah) {
    $stmtNasabah->bind_param("s", $nis);
    $stmtNasabah->execute();
    $stmtNasabah->bind_result($nama, $nis, $password, $no_rekening, $kelas, $jurusan, $jenis_kelamin, $tanggal_pembuatan, $saldo, $status);
    $stmtNasabah->fetch();
    $stmtNasabah->close();
} else {
    echo "Error preparing statement.";
}

// Fungsi untuk mengambil riwayat transaksi berdasarkan no_rekening
function getRiwayatTransaksi($conn, $no_rekening) {
    $query = "SELECT tanggal_transaksi, jenis_transaksi, nominal, saldo_awal, saldo_akhir 
                FROM riwayat_transaksi
                WHERE account_number = ? 
                ORDER BY tanggal_transaksi DESC";
    if ($stmt = $conn->prepare($query)) {
        $stmt->bind_param("s", $no_rekening);
        $stmt->execute();
        $result = $stmt->get_result();
        $transaksi = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $transaksi;
    } else {
        return false;
    }
}

$riwayatTransaksi = getRiwayatTransaksi($conn, $no_rekening);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Transaksi Nasabah</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet" />
    <style>
        * {
        font-family: 'montserrat';
        }
        body {
            background-color: #fff;
            margin: 0;
            padding: 0;
        }
        .container {
        width: 100%;
        max-width: 90%;
        justify-content: center;
        margin: 10px;
        padding: 20px;
        border-radius: 8px;
        border: 1px solid #ddd;
    }

        .profile-header {
            background-color: #365486;
            color: white;
            padding: 10px 0;
            text-align: center;
        }
        .profile-header h2 {
            margin: 0;
        }
        .profile-content {
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        table {

            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 12px;
            text-align: left;
        }
        th {
            background-color: #365486;
            color: white;
        }

        button {
            background-color: #365486;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-align: center;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="profile-header">
        <h2>Riwayat Transaksi</h2>
    </div>
    <div class="profile-content">
        <?php if ($riwayatTransaksi): ?>
            <table>
                <thead>
                    <tr>
                        <th>Tanggal Transaksi</th>
                        <th>Jenis Transaksi</th>
                        <th>Nominal</th>
                        <th>Saldo Awal</th>
                        <th>Saldo Akhir</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($riwayatTransaksi as $transaksi): ?>
                        <tr>
                            <td><?php echo $transaksi['tanggal_transaksi']; ?></td>
                            <td><?php echo $transaksi['jenis_transaksi']; ?></td>
                            <td>Rp. <?php echo number_format($transaksi['nominal'], 2); ?></td>
                            <td>Rp. <?php echo number_format($transaksi['saldo_awal'], 2); ?></td>
                            <td>Rp. <?php echo number_format($transaksi['saldo_akhir'], 2); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>Tidak ada riwayat transaksi.</p>
        <?php endif; ?>
    </div>
      <button onclick="history.back()">Kembali</button>
</div>

</body>
</html>
