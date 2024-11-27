<?php
require_once 'db_connection.php';

if (isset($_POST['submit'])) {
    $name = $_POST['name'];
    $account_number = $_POST['account_number'];
    $nominal = $_POST['nominal'];
    $jenis_transaksi = $_POST['jenis_transaksi'];
    $saldo_awal = $_POST['saldo_awal'];

    // Validasi Data Tidak Boleh Kosong
    if (empty($name) || empty($account_number) || empty($nominal) || empty($jenis_transaksi)) {
        echo "<script>alert('Semua Kolom Harus Di Isi'); window.location.href='form-transaction.php'</script>";
        exit(); // Menghentikan Koneksi
    }

    // Validasi Nominal Minimal
    if ($nominal < 10000) {
        echo "<script>alert('Nominal transaksi minimal adalah 10.000'); window.location.href='form-transaction.php'</script>";
        exit(); // Menghentikan Koneksi
    }

    // Cek status nasabah
    $status_query = $conn->query("SELECT status FROM data_diri WHERE account_number = '$account_number'");
    if ($status_query->num_rows > 0) {
        $row = $status_query->fetch_assoc();
        $status = $row['status'];

        if ($status != 'Aktif') {
            echo "<script>alert('Gagal melakukan transaksi, karena nomor rekening yang digunakan tidak aktif.'); window.location.href='form-transaction.php'</script>";
            exit(); // Menghentikan Koneksi
        }
    } else {
        echo "<script>alert('Nomor rekening tidak ditemukan'); window.location.href='form-transaction.php'</script>";
        exit(); // Menghentikan Koneksi
    }

    // Hitung Saldo Baru
    if ($jenis_transaksi == 'setoran') {
        $saldo_akhir = $saldo_awal + $nominal;
    } elseif ($jenis_transaksi == 'penarikan') {
        if ($nominal > $saldo_awal) {
            echo "<script>alert('Saldo tidak mencukupi untuk penarikan'); window.location.href='form-transaction.php'</script>";
            exit(); // Menghentikan Koneksi
        }
        $saldo_akhir = $saldo_awal - $nominal;
    }

    // Perbarui saldo di database
    $q = $conn->query("UPDATE data_diri SET balance = '$saldo_akhir' WHERE account_number = '$account_number'");
    if ($q) {
        // Catat riwayat transaksi
        $insert_query = "INSERT INTO riwayat_transaksi (name, account_number, tanggal_transaksi, jenis_transaksi, nominal, saldo_awal, saldo_akhir) VALUES ('$name', '$account_number', CURRENT_TIMESTAMP, '$jenis_transaksi', '$nominal', '$saldo_awal', '$saldo_akhir')";
        $result = $conn->query($insert_query);

        if ($result) {
            echo "<script>alert('Transaksi berhasil ditambahkan'); window.location.href='riwayat-transaksi.php'</script>";
        } else {
            echo "<script>alert('Transaksi gagal ditambahkan ke riwayat'); window.location.href='form-transaksi.php'</script>";
            error_log("Error inserting transaction: " . $conn->error); // Log error to server logs
        }
    } else {
        echo "<script>alert('Saldo gagal diperbarui'); window.location.href='form-transaksi.php'</script>";
        error_log("Error updating balance: " . $conn->error); // Log error to server logs
    }
} else {
    // Jika coba akses langsung halaman ini akan diredirect ke halaman transaksi
    header('Location: form-transaksi.php');
}
?>
