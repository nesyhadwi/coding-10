<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "bank-mini";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get form data
$name = $_POST['name'];
$id_class = $_POST['class'];
$id_major = $_POST['major'];
$gender = $_POST['gender'];
$creation_date = $_POST['creation_date'];
$account_number = $_POST['account_number'];
$balance = $_POST['balance'];
$status = $_POST['status'];

// Prepare SQL query
$sql = "INSERT INTO data_diri (name, id_class, id_major, gender, creation_date, account_number, balance, status) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);
if ($stmt === false) {
    die("Prepare failed: " . $conn->error);
}

$stmt->bind_param("ssssssss", $name, $id_class, $id_major, $gender, $creation_date, $account_number, $balance, $status);

// Execute the query
if ($stmt->execute()) {
    echo "<script>
            window.location.href = 'index.php';
          </script>";
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
