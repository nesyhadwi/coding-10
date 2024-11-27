<?php
require_once 'db_connection.php';

if (isset($_POST['account_number'])) {
    $account_number = $_POST['account_number'];

    $query = $conn->query("SELECT name, balance, status FROM data_diri WHERE account_number = '$account_number'");
    if ($query->num_rows > 0) {
        $data_diri = $query->fetch_assoc();
        echo json_encode($data_diri);
    } else {
        echo json_encode(['name' => '', 'balance' => 0, 'status' => '']);
    }
}
?>
