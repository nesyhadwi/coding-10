<?php
require_once 'db_connection.php';
if (isset($_GET['id'])) {
    $id = $_GET['id'];
// perintah hapus data berdasarkan id yang dikirimkan
    $q = $conn->query("DELETE FROM riwayat_transaksi WHERE id = '$id'");
// cek perintah
    if ($q) {
    // pesan apabila hapus berhasil
    echo "<script>alert('Data berhasil dihapus'); window.location.href='riwayat-transaksi.php'</script>";
    } else {
    // pesan apabila hapus gagal
    echo "<script>alert('Data Gagal dihapus'); window.location.href='form-transaction.php'</script>";
    }
} else {
  // jika mencoba akses langsung ke file ini akan diredirect ke halaman index
    header('Location:form-transaction.php');
}