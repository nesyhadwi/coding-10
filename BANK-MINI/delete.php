<?php
include 'db_connection.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $sql = "DELETE FROM data_diri WHERE id=$id";

    if ($conn->query($sql) === TRUE) {
        echo "Data berhasil dihapus";
    } else {
        echo "Error menghapus data: " . $conn->error;
    }

    $conn->close();
    header("Location: index.php");
    exit();
}
?>
