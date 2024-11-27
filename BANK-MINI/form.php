<?php
$conn = new mysqli("localhost", "root", "", "bank-mini");

// Get maximum account number and increment it
        $result = $conn->query("SELECT IFNULL(MAX(account_number), 100000) AS account_number FROM data_diri");
        $row = $result->fetch_assoc();
        $new_account_number = $row['account_number'] + 1;


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nis = $_POST['nis'];

    // Check if NIS already exists
    $nisQuery = "SELECT * FROM data_diri WHERE nis = ?";
    $stmt = $conn->prepare($nisQuery);
    $stmt->bind_param("s", $nis);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // NIS already exists, display error message
        $nisError = "NIS sudah digunakan. Masukkan NIS yang berbeda.";
    } else {

        // Insert new record
        $insertQuery = "INSERT INTO data_diri (nis, name, class, major, gender, creation_date, account_number, balance, password, status) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmtInsert = $conn->prepare($insertQuery);
        $stmtInsert->bind_param("ssssssssis", $nis, $_POST['name'], $_POST['class'], $_POST['major'], $_POST['gender'], $_POST['creation_date'], $new_account_number, $_POST['balance'], $_POST['password'], $_POST['status']);
        
        if ($stmtInsert->execute()) {
            echo "Data berhasil disimpan.";
            // Redirect to data index page
            header("Location: index.php");
            exit;
        } else {
            echo "Gagal menyimpan data.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Data Diri</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet" />
    <style>
        body {
            font-family: 'montserrat';
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: #dcf2f1;
        }
        .container {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 600px;
            margin-top: 100px;
            margin-bottom: 100px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        input[type="text"],
        input[type="password"],
        input[type="number"],
        select,
        input[type="date"] {
            width: 100%;
            padding: 8px;
            margin: 4px 0 10px 0;
            display: inline-block;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }
        button {
            background-color: #365486;
            color: white;
            padding: 15px 15px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            width: 80%;
            margin-top: 10px;
            font-size: large;
            font-weight: 600;
            margin-left: 60px;
        }
        button:hover {
            background-color: #0F1035;
        }
    </style>
</head>
<body>

    <div class="container">
        <h2 style="text-align: center;">Input Data Nasabah</h2>

    <form id="dataForm" action="" method="POST">
            <div class="form-group">
                <label for="name">Nama:</label>
                <input type="text" id="name" name="name" required>
            </div>

            <div class="form-group">
                <label for="class">Kelas:</label>
                <select id="class" name="class" required>
                    <option value="" disabled selected>Pilih Kelas</option>
                    <?php
                    // Menghubungkan ke database
                    $result = $conn->query("SELECT id, class_name FROM class");
                    if ($result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) {
                            echo "<option value='" . $row['id'] . "'>" . $row['class_name'] . "</option>";
                        }
                    }
                    ?>
                </select>
            </div>

            <div class="form-group">
                <label for="major">Jurusan:</label>
                <select id="major" name="major" required>
                    <option value="" disabled selected>Pilih Jurusan</option>
                    <?php
                    // Menghubungkan ke database
                    $result = $conn->query("SELECT id, major_name FROM major");
                    if ($result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) {
                            echo "<option value='" . $row['id'] . "'>" . $row['major_name'] . "</option>";
                        }
                    }
                    ?>
                </select>
            </div>

            <div class="form-group">
                <label>Jenis Kelamin:</label>
                <div class="radio-group">
                    <input type="radio" id="male" name="gender" value="Laki-Laki" required>
                    <label for="Laki-Laki">Laki-laki</label>
                    <input type="radio" id="female" name="gender" value="Perempuan">
                    <label for="Perempuan">Perempuan</label>
                </div>
            </div>

            <div class="form-group">
                <label for="creation_date">Tanggal Pembuatan:</label>
                <input type="date" id="creation_date" name="creation_date" required>
            </div>

    <div class="form-group">
        <label for="account_number">Nomor Rekening:</label>
        <input type="number" id="account_number" name="account_number" value="<?php echo $new_account_number; ?>" readonly>
    </div>
    <div class="form-group">
                <label for="balance">Saldo:</label>
                <input type="number" id="balance" name="balance" required>
            </div>

            <div class="form-group">
                <label for="nis">Username:</label>
                <input type="text" id="nis" name="nis" required>
            </div>
            
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>

            <div class="form-group">
                <label for="status">Status:</label>
                <select id="status" name="status" required>
                    <option value="" disabled selected>Pilih Status</option>
                    <option value="Aktif">Aktif</option>
                    <option value="Tidak Aktif">Tidak Aktif</option>
                </select>
            </div>


    <button type="submit">Submit</button>
</form>

        <button onclick="history.back()">Kembali</button>

    </div>

</body>
</html>
