<?php
require_once 'db_connection.php';

if (isset($_GET['term'])) {
    $term = $_GET['term'];
    $query = $conn->query("SELECT account_number, name FROM data_diri WHERE account_number LIKE '%$term%'");
    $result = [];
    while ($row = $query->fetch_assoc()) {
        $result[] = [
            'label' => $row['account_number'] . ' - ' . $row['name'],
            'value' => $row['account_number'],
            'name' => $row['name'],
        ];
    }
    echo json_encode($result);
}
?>
