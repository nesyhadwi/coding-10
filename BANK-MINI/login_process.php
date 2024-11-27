<?php
session_start();
include('db_connection.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Query untuk memeriksa username dan password
    $query = "SELECT * FROM data_diri WHERE username = ? AND password = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['account_number'] = $user['account_number']; // Simpan account_number ke sesi
        $_SESSION['name'] = $user['name']; // Simpan name ke sesi

        // Redirect ke halaman profil setelah login
        header("Location: profile.php");
        exit();
    } else {
        echo "Username atau password salah.";
    }

    $stmt->close();
    $conn->close();
}
?>
