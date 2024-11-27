<?php
include 'db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $class_name = $_POST['class_name'];

    $sql = "INSERT INTO class (class_name) VALUES ('$class_name')";

    if ($conn->query($sql) === TRUE) {
        echo "New class added successfully";
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
    <title>Add Class</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet" />
    <style>
        body {
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: white;
            font-family: 'montserrat';
        }

        .container {
            background-color: #dcf2f1;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 80%;
            max-width: 100%;
            margin-top: 50px;
            margin-bottom: 50px;
            box-sizing: border-box;
            height: 100vh;
        }

        h2 {
            text-align: center;
        }

        .form-group {
            margin-bottom: 15px;
        }

        label {
            display: block;
            margin: 5px 0px 0px 250px;;
        }

        input[type="text"] {
            width: 50%;
            padding: 8px;
            margin: 5px 0px 0px 250px;
            display: inline-block;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }

        button {
            background-color: #365486;
            color: white;
            padding: 10px;
            font-size: 15px;
            font-weight: bold;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            width: 50%;
            margin: 5px 0px 0px 250px;
        }

        button:hover {
            background-color: #0F1035;
        }
    </style>
</head>

<body>

    <div class="container">
        <h2>Tambah Kelas</h2>
        <form method="POST">
            <div class="form-group">
                <label for="class_name">Nama Kelas:</label>
                <input type="text" id="class_name" name="class_name" required>
            </div>
            <button type="submit">Tambah</button>
        </form>
        <div class="button-group">
            <a href="view_class_major.php"><button>Kembali</button></a>
        </div>
    </div>

</body>

</html>
