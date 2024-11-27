<?php
include 'db_connection.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Query untuk mendapatkan data nasabah beserta nama class dan major
    $sql = "SELECT data_diri.id, data_diri.name, data_diri.id_class, data_diri.id_major, 
                   data_diri.gender, data_diri.creation_date, data_diri.account_number, 
                   data_diri.balance, data_diri.status, class.class_name, major.major_name 
            FROM data_diri 
            JOIN class ON data_diri.id_class = class.id 
            JOIN major ON data_diri.id_major = major.id 
            WHERE data_diri.id=$id";

    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
    } else {
        echo "Data tidak ditemukan";
        exit();
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $class = $_POST['class'];
    $major = $_POST['major'];
    $gender = $_POST['gender'];
    $creation_date = $_POST['creation_date'];
    $account_number = $_POST['account_number'];
    $balance = $_POST['balance'];
    $status = $_POST['status'];

    // Update data_diri dengan ID class dan major
   $sql = "UPDATE data_diri SET 
             name='$name', 
            id_class='$class', 
            id_major='$major', 
            gender='$gender', 
            creation_date='$creation_date', 
            account_number='$account_number', 
            balance='$balance', 
            status='$status' 
            WHERE id=$id";

    if ($conn->query($sql) === TRUE) {
        header("Location: data_nasabah.php");
        exit();
    } else {
        echo "Error mengupdate data: " . $conn->error;
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Data Nasabah</title>
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
            background-color: #f2f2f2;
        }
        .container {
            background-color: #fff;
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
        }
        input[type="text"],
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
        .radio-group {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .radio-group input[type="radio"] {
            margin-top: 0;
        }
        button {
            background-color: #27b0da;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            width: 100%;
            margin-top: 10px;
        }
        button:hover {
            background-color: #1a8cbd;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table,
        th,
        td {
            border: 1px solid #ddd;
        }
        th,
        td {
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>

<body>

    <div class="container">
        <h2 style="text-align: center;">Edit Data Nasabah</h2>
        <form action="edit.php" method="POST">
            <input type="hidden" name="id" value="<?php echo htmlspecialchars($row['id']); ?>">
            
            <div class="form-group">
                <label for="name">Nama:</label>
                <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($row['name']); ?>" required>
            </div>

            <div class="form-group">
                <label for="class">Kelas:</label>
                <select id="class" name="class" required>
                    <?php
                    // Fetching classes from the database
                    $class_sql = "SELECT id, class_name FROM class";
                    $class_result = $conn->query($class_sql);
                    
                    while ($class_row = $class_result->fetch_assoc()) {
                        $selected = ($class_row['id'] == $row['id_class']) ? 'selected' : '';
                        echo '<option value="' . htmlspecialchars($class_row['id']) . '" ' . $selected . '>' . htmlspecialchars($class_row['class_name']) . '</option>';
                    }
                    ?>
                </select>
            </div>

            <div class="form-group">
                <label for="major">Jurusan:</label>
                <select id="major" name="major" required>
                    <?php
                    // Fetching majors from the database
                    $major_sql = "SELECT id, major_name FROM major";
                    $major_result = $conn->query($major_sql);
                    
                    while ($major_row = $major_result->fetch_assoc()) {
                        $selected = ($major_row['id'] == $row['id_major']) ? 'selected' : '';
                        echo '<option value="' . htmlspecialchars($major_row['id']) . '" ' . $selected . '>' . htmlspecialchars($major_row['major_name']) . '</option>';
                    }
                    ?>
                </select>
            </div>

            <div class="form-group">
                <label>Jenis Kelamin:</label>
                <div class="radio-group">
                    <input type="radio" id="male" name="gender" value="Laki-Laki" <?php if ($row['gender'] == 'male') echo 'checked'; ?> required>
                    <label for="male">Laki-laki</label>
                    <input type="radio" id="female" name="gender" value="Perempuan" <?php if ($row['gender'] == 'female') echo 'checked'; ?>>
                    <label for="female">Perempuan</label>
                </div>
            </div>

            <div class="form-group">
                <label for="creation_date">Tanggal Pembuatan:</label>
                <input type="date" id="creation_date" name="creation_date" value="<?php echo htmlspecialchars($row['creation_date']); ?>" required >
            </div>

            <div class="form-group">
                <label for="account_number">Nomor Rekening:</label>
                <input type="number" id="account_number" name="account_number" value="<?php echo htmlspecialchars($row['account_number']); ?>" readonly >
            </div>



            <div class="form-group">
                <label for="balance">Saldo:</label>
                <input type="text" id="balance" name="balance" value="<?php echo htmlspecialchars($row['balance']); ?>" required>
            </div>

            <div class="form-group">
                <label for="status">Status:</label>
                <select id="status" name="status" required>
                    <option value="active" <?php if ($row['status'] == 'active') echo 'selected'; ?>>Aktif</option>
                    <option value="inactive" <?php if ($row['status'] == 'inactive') echo 'selected'; ?>>Tidak Aktif</option>
                </select>
            </div>

            <button type="submit">UPDATE</button>
        </form>
        <form action="index.php" method="GET">
            <button type="submit">Kembali</button>
        </form>
    </div>

</body>

</html>
