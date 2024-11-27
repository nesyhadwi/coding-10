<?php
session_start();
include('db_connection.php');

// Pastikan user sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Ambil data dari formulir
$user_id = $_SESSION['user_id'];
$jenis_transaksi = $_POST['jenis_transaksi'];
$nominal = $_POST['nominal'];
$saldo_awal = $_POST['saldo_awal'];
$tanggal_transaksi = date('Y-m-d H:i:s');

// Hitung saldo akhir
if ($jenis_transaksi == "setoran") {
    $saldo_akhir = $saldo_awal + $nominal;
} else if ($jenis_transaksi == "penarikan") {
    $saldo_akhir = $saldo_awal - $nominal;
}

// Simpan data transaksi ke database
$query_insert = "INSERT INTO riwayat_transaksi (id, account_number, jenis_transaksi, nominal, saldo_awal, saldo_akhir, tanggal_transaksi) VALUES (?, ?, ?, ?, ?, ?, ?)";
$stmt_insert = $conn->prepare($query_insert);
$stmt_insert->bind_param("issddds", $user_id, $account_number, $jenis_transaksi, $nominal, $saldo_awal, $saldo_akhir, $tanggal_transaksi);

if ($stmt_insert->execute()) {
    // Update saldo nasabah
    $query_update_saldo = "UPDATE data_diri SET balance = ? WHERE id = ?";
    $stmt_update_saldo = $conn->prepare($query_update_saldo);
    $stmt_update_saldo->bind_param("di", $saldo_akhir, $user_id);
    $stmt_update_saldo->execute();

    // Redirect ke halaman riwayat transaksi setelah berhasil transaksi
    header("Location: riwayat-transaksi-user.php");
    exit();
} else {
    echo "Terjadi kesalahan dalam menyimpan transaksi: " . $conn->error;
}
?>
