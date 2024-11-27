<?php
include 'db_connection.php';

$id = $_GET['id'];

// Periksa apakah ada nasabah yang terkait dengan major ini
$check_sql = "SELECT COUNT(*) as count FROM data_diri WHERE id_major=$id";
$check_result = $conn->query($check_sql);
$check_row = $check_result->fetch_assoc();

if ($check_row['count'] > 0) {
    // Ada data nasabah yang terkait, tampilkan peringatan
    echo "<script>
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('modalMessage').innerText = 'Tidak bisa menghapus Jurusan ini karena ada nasabah yang terkait.';
            document.getElementById('confirmationModal').style.display = 'block';
        });
    </script>";
} else {
    // Tidak ada data nasabah yang terkait, lakukan penghapusan
    $sql = "DELETE FROM major WHERE id=$id";
    if ($conn->query($sql) === TRUE) {
        echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
                document.getElementById('modalMessage').innerText = 'Major deleted successfully';
                document.getElementById('confirmationModal').style.display = 'block';
                document.getElementById('confirmButton').onclick = function() {
                    window.location.href = 'view_class_major.php';
                };
            });
        </script>";
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
    <title>Delete Major</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet" />
    <style>
        body {
        font-family: 'montserrat';
        }
        
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.4);
            justify-content: center;
            align-items: center;
        }

        .modal-content {
            background-color: #fefefe;
            margin: auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            max-width: 400px;
            border-radius: 8px;
            text-align: center;
        }

        .modal-buttons {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-top: 20px;
        }

        .modal-buttons button {
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            background-color: #27b0da;
            color: white;
        }

        .modal-buttons button:hover {
            background-color: #1a8cbd;
        }
    </style>
</head>

<body>
    <!-- Modal Dialog -->
    <div id="confirmationModal" class="modal">
        <div class="modal-content">
            <p id="modalMessage"></p>
            <div class="modal-buttons">
                <button id="confirmButton" onclick="closeModal()">OK</button>
            </div>
        </div>
    </div>
    <script>
        function closeModal() {
            document.getElementById('confirmationModal').style.display = 'none';
            window.location.href = 'view_class_major.php';
        }
    </script>
</body>

</html>
