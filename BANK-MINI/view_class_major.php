<?php
include 'db_connection.php';

// Retrieve classes
$sql_classes = "SELECT * FROM class";
$result_classes = $conn->query($sql_classes);

// Retrieve majors
$sql_majors = "SELECT * FROM major";
$result_majors = $conn->query($sql_majors);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Kelas dan Jurusan</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet" />
    <style>
        body {
            font-family: 'montserrat';
            margin: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            background-color: white;
        }

        .container {
            background-color: #dcf2f1;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 1000px;
            margin-top: 50px;
            margin-bottom: 50px;
            box-sizing: border-box;
        }

        h2 {
            text-align: center;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background-color: white;
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
            background-color: #365486;
        }

        button {
            background-color: #365486;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        button:hover {
            background-color: #0F1035;
        }

        .button-group {
            display: flex;
            justify-content: flex-end;
            margin-top: 20px;
        }

        .action-buttons {
            display: flex;
            gap: 10px;
        }
    </style>
</head>

<body>

    <div class="container">
        <h2>Data Kelas dan Jurusan</h2>

        <!-- Classes Section -->
        <h3>Daftar Kelas</h3>
        <table>
            <tr>
                <th>ID</th>
                <th>Nama Kelas</th>
                <th>Aksi</th>
            </tr>
            <?php
            if ($result_classes->num_rows > 0) {
                while ($row = $result_classes->fetch_assoc()) {
                    echo '<tr>';
                    echo '<td>' . htmlspecialchars($row["id"]) . '</td>';
                    echo '<td>' . htmlspecialchars($row["class_name"]) . '</td>';
                    echo '<td class="action-buttons">
                        <a href="edit_class.php?id=' . htmlspecialchars($row["id"]) . '"><button>Edit</button></a>
                        <a href="delete_class.php?id=' . htmlspecialchars($row["id"]) . '"><button>Delete</button></a>
                    </td>';
                    echo '</tr>';
                }
            } else {
                echo '<tr><td colspan="3">No classes found.</td></tr>';
            }
            ?>
        </table>
        <div class="button-group">
            <a href="add_class.php"><button>Tambah Kelas</button></a>
        </div>

        <!-- Majors Section -->
        <h3>Daftar Jurusan</h3>
        <table>
            <tr>
                <th>ID</th>
                <th>Nama Jurusan</th>
                <th>Aksi</th>
            </tr>
            <?php
            if ($result_majors->num_rows > 0) {
                while ($row = $result_majors->fetch_assoc()) {
                    echo '<tr>';
                    echo '<td>' . htmlspecialchars($row["id"]) . '</td>';
                    echo '<td>' . htmlspecialchars($row["major_name"]) . '</td>';
                    echo '<td class="action-buttons">
                        <a href="edit_major.php?id=' . htmlspecialchars($row["id"]) . '"><button>Edit</button></a>
                        <a href="delete_major.php?id=' . htmlspecialchars($row["id"]) . '"><button>Delete</button></a>
                    </td>';
                    echo '</tr>';
                }
            } else {
                echo '<tr><td colspan="3">No majors found.</td></tr>';
            }
            ?>
        </table>
        <div class="button-group">
            <a href="add_major.php"><button>Tambah Jurusan</button></a>
        </div>
        <form action="index.php" method="GET">
            <button type="submit">Kembali</button>
        </form>
    </div>

</body>

</html>