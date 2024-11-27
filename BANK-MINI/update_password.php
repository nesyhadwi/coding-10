<?php
session_start();
include 'db_connection.php'; // Koneksi ke database

// Cek apakah pengguna sudah login
if (!isset($_SESSION['nis'])) {
    header("Location: login.php");
    exit();
}

$nis = $_SESSION['nis'];

// Jika formulir disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password_baru = $_POST['password'];

    // Update password baru tanpa validasi password lama
    $updatePassword = "UPDATE data_diri SET password = ? WHERE nis = ?";
    $stmtUpdate = $conn->prepare($updatePassword);
    if ($stmtUpdate) {
        $stmtUpdate->bind_param("ss", $password_baru, $nis);
        $stmtUpdate->execute();
        $stmtUpdate->close();
        header("Location: profile.php");
    } else {
        echo "Error updating password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Password</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet" />
    <style>
        *{
            font-family: 'montserrat';
        }
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {

            width: 100%;
            max-width: 30%;
            margin: 20px 0px 400px 450px;
            padding: 20px;
            border-radius: 4px;
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
        input[type="password"] {
            width: 100%;
            padding: 8px;
            border-radius: 4px;
            box-sizing: border-box;
        }
        input[type="submit"] {
            width: 100%;
            padding: 10px;
            background-color: #365486;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        input[type="submit"]:hover {
            background-color:black;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Update Password</h2>
    <form method="POST">
        <div class="form-group">
            <label for="password">Password Baru:</label>
            <input type="password" name="password" id="password" required>
        </div>
        <input type="submit" value="Update Password">
    </form>
</div>

</body>
</html>

