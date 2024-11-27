<?php
session_start();
include('db_connection.php');

// Memeriksa apakah pengguna sudah login
if (!isset($_SESSION['nis'])) {
    header("Location: login.php");
    exit();
}

$nis = $_SESSION['nis'];

// Query untuk mendapatkan data user dari tabel data_diri
$sql = "SELECT data_diri.id, data_diri.nis, data_diri.password, data_diri.account_number, data_diri.name, 
               class.class_name, major.major_name, data_diri.gender, 
               data_diri.creation_date, data_diri.balance, data_diri.status 
        FROM data_diri 
        JOIN class ON data_diri.id_class = class.id 
        JOIN major ON data_diri.id_major = major.id
        WHERE data_diri.nis = ?";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}

$stmt->bind_param("i", $nis);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 1) {
    $user = $result->fetch_assoc();
} else {
    echo "User tidak ditemukan.";
    exit();
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Pengguna</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet" />
    <style>
        * {
            font-family: 'montserrat';
        }
        body {
            background-color: #f4f4f4;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .profile-container {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            width: 400px;
            margin-bottom: 20px; /* Beri jarak untuk footer */
        }
        .profile-container h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        .profile-container p {
            margin: 10px 0;
            font-size: 18px;
        }
        .profile-container label {
            font-weight: bold;
        }

        .update-password-container {
            margin-top: 20px;
            text-align: center;
        }
        .update-password-container button {
            padding: 10px 20px;
            background-color: #365486;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }
        .update-password-container button:hover {
            background-color: black;
        }

        .footer {
            display: flex;
            justify-content: space-between;
            width: 400px; /* Sesuaikan lebar dengan container */
            background-color: #fff;
            padding: 10px;
            border-radius: 8px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        }
        .footer span {
            display: flex;
            justify-content: center;
            align-items: center;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: #f4f4f4;
        }
        .footer .user {
            background-color: #365486; /* Warna background untuk user */
            color: white; /* Warna icon user */
        }
        .footer span svg {
            width: 20px;
            height: 20px;
        }
    </style>
</head>
<body>

    <div class="profile-container">
        <h2>Profil Pengguna</h2>
        <p><label>NIS:</label> <?php echo htmlspecialchars($user['nis']); ?></p>
        <p><label>Password:</label> <?php echo htmlspecialchars($user['password']); ?></p>
        <p><label>No Rekening:</label> <?php echo htmlspecialchars($user['account_number']); ?></p>
        <p><label>Nama:</label> <?php echo htmlspecialchars($user['name']); ?></p>
        <p><label>Kelas:</label> <?php echo htmlspecialchars($user['class_name']); ?></p>
        <p><label>Jurusan:</label> <?php echo htmlspecialchars($user['major_name']); ?></p>
        <p><label>Jenis Kelamin:</label> <?php echo htmlspecialchars($user['gender']); ?></p>
        <p><label>Status:</label> <?php echo htmlspecialchars($user['status']); ?></p>
        <p><label>Saldo: Rp. </label> <?php echo number_format($user['balance']), 0, ',', '.'; ?></p>

       <div class="update-password-container">
        <button onclick="window.location.href='update_password.php'">Ganti Password</button>
    </form>
</div>

    </div>

    <div class="footer">
        <span class="user"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 16 16"><path fill="currentColor" fill-rule="evenodd" d="M10 4.5a2 2 0 1 1-4 0a2 2 0 0 1 4 0m1.5 0a3.5 3.5 0 1 1-7 0a3.5 3.5 0 0 1 7 0m-9 8c0-.204.22-.809 1.32-1.459C4.838 10.44 6.32 10 8 10s3.162.44 4.18 1.041c1.1.65 1.32 1.255 1.32 1.459a1 1 0 0 1-1 1h-9a1 1 0 0 1-1-1m5.5-4c-3.85 0-7 2-7 4A2.5 2.5 0 0 0 3.5 15h9a2.5 2.5 0 0 0 2.5-2.5c0-2-3.15-4-7-4" clip-rule="evenodd"/></svg></span>
        <span onclick="window.location.href='riwayat-transaksi-user.php'"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 16 16"><path fill="currentColor" fill-rule="evenodd" d="M4.5 3h7A1.5 1.5 0 0 1 13 4.5v7a1.5 1.5 0 0 1-1.5 1.5h-7A1.5 1.5 0 0 1 3 11.5v-7A1.5 1.5 0 0 1 4.5 3m-3 1.5a3 3 0 0 1 3-3h7a3 3 0 0 1 3 3v7a3 3 0 0 1-3 3h-7a3 3 0 0 1-3-3zm4.75.75a1 1 0 1 1-2 0a1 1 0 0 1 2 0m1 0A.75.75 0 0 1 8 4.5h2.75a.75.75 0 0 1 0 1.5H8a.75.75 0 0 1-.75-.75M5.25 9a1 1 0 1 0 0-2a1 1 0 0 0 0 2m1 1.75a1 1 0 1 1-2 0a1 1 0 0 1 2 0M8 7.25a.75.75 0 0 0 0 1.5h2.75a.75.75 0 0 0 0-1.5zm-.75 3.5A.75.75 0 0 1 8 10h2.75a.75.75 0 0 1 0 1.5H8a.75.75 0 0 1-.75-.75" clip-rule="evenodd"/></svg></span>
    </div>

</body>
</html>




