<?php
include 'db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $major_name = $_POST['major_name'];

    $sql = "INSERT INTO major (major_name) VALUES ('$major_name')";

    if ($conn->query($sql) === TRUE) {
        echo "New major added successfully";
        header("Location: view_class_major.php");
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Major</title>
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
            max-width: 400px;
            margin-top: 50px;
            margin-bottom: 50px;
            box-sizing: border-box;
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

        input[type="text"] {
            width: 100%;
            padding: 8px;
            margin: 4px 0 10px 0;
            display: inline-block;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
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
    </style>
</head>

<body>

    <div class="container">
        <h2>Tambah Jurusan</h2>
        <form method="POST">
            <div class="form-group">
                <label for="major_name">Nama Jurusan:</label>
                <input type="text" id="major_name" name="major_name" required>
            </div>
            <button type="submit">Tambah</button>
        </form>
        <div class="button-group">
            <a href="view_class_major.php"><button>Kembali</button></a>
        </div>
    </div>

</body>

</html>
