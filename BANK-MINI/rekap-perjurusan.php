<?php
include 'db_connection.php';

// Tentukan jumlah data per halaman
$limit = 10;

// Tentukan halaman saat ini, jika tidak ada maka halaman = 1
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page > 1) ? ($page * $limit) - $limit : 0;

// Filter status akun
$status_filter = isset($_GET['status']) ? $conn->real_escape_string($_GET['status']) : '';
$status_query = $status_filter !== '' ? "status = '$status_filter'" : '1=1';

// Filter pencarian nama
$search_query = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
$name_query = $search_query !== '' ? "name LIKE '%$search_query%'" : '1=1';

// Filter jurusan
$major_filter = isset($_GET['major']) ? $conn->real_escape_string($_GET['major']) : '';
$major_query = $major_filter !== '' ? "id_major = '$major_filter'" : '1=1';

// Kombinasikan filter status, pencarian nama, dan jurusan
$where_query = "WHERE $status_query AND $name_query AND $major_query";

// Ambil jumlah total data dengan filter status, pencarian nama, dan jurusan
$total = $conn->query("SELECT COUNT(*) AS total FROM data_diri $where_query")->fetch_assoc()['total'];
$pages = ceil($total / $limit);

// Tampilkan data dengan limit, offset, filter status, pencarian nama, dan jurusan
$sql = "SELECT data_diri.id, data_diri.name, class.class_name, major.major_name, data_diri.gender, 
                data_diri.creation_date, data_diri.account_number, data_diri.balance, data_diri.status 
        FROM data_diri 
        JOIN class ON data_diri.id_class = class.id 
        JOIN major ON data_diri.id_major = major.id
        $where_query
        LIMIT $start, $limit";

$result = $conn->query($sql);

// Ambil daftar jurusan untuk dropdown
$majors = $conn->query("SELECT * FROM major")->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE ht<html lang="en">

    <head>
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
            background-color: #dcf2f1;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 100%;
            box-sizing: border-box;
        }

        h2 {
            margin-top: 5px !important;
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
            color: white;
        }

        button {
            background-color: #27b0da;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-align: center;
        }

        button:hover {
            background-color: #1a8cbd;
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
        }

        .modal-content {
            background-color: #fefefe;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            max-width: 400px;
            border-radius: 8px;
        }

        .modal-buttons {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }

        .pagination {
            text-align: center;
            margin-top: 20px;
        }

        .pagination a {
            color: #0275d8;
            padding: 8px 16px;
            text-decoration: none;
            border: 1px solid #ddd;
            border-radius: 4px;
            margin: 0 4px;
        }

        .pagination a:hover {
            background-color: #ddd;
        }

        .pagination a.active {
            background-color: #333e50;
            color: white;
            border: 1px solid #333e50;
        }
    </style>
    <script>
        function showModal(action, id) {
            const modal = document.getElementById("confirmationModal");
            const confirmButton = document.getElementById("confirmButton");

            if (action === 'delete') {
                modal.style.display = "block";
                confirmButton.onclick = function () {
                    window.location.href = 'delete.php?id=' + id;
                };
            } else if (action === 'edit') {
                window.location.href = 'edit.php?id=' + id;
            }
        }

        function closeModal() {
            document.getElementById("confirmationModal").style.display = "none";
        }
    </script>
</head>

<body>

    <div class="container">
        <h2>Data Nasabah</h2>

        <form action="rekap-perjurusan.php" method="GET">
            <label for="major">Jurusan:</label>
            <select id="major" name="major">
                <option value="">Semua Jurusan</option>
                <?php foreach ($majors as $major): ?>
                    <option value="<?php echo $major['id']; ?>" <?php if ($major_filter == $major['id']) echo 'selected'; ?>><?php echo $major['major_name']; ?></option>
                <?php endforeach; ?>
            </select>
            <input type="hidden" name="page" value="<?php echo $page; ?>">
            <button type="submit">Filter</button>
        </form>

        <?php
        if ($result->num_rows > 0) {
            echo '<table>';
            echo '<tr>
            <th>No</th>
            <th>Nomor Rekening</th>
            <th>Nama</th>
            <th>Kelas</th>
            <th>Jurusan</th>
            <th>Saldo</th>
            </tr>';
            $no = $start + 1; // Nomor urut mulai dari offset + 1
            while ($row = $result->fetch_assoc()) {
                echo '<tr>';
                echo '<td>' . $no++ . '</td>';
                echo '<td>' . htmlspecialchars($row["account_number"]) . '</td>';
                echo '<td>' . htmlspecialchars($row["name"]) . '</td>';
                echo '<td>' . htmlspecialchars($row["class_name"]) . '</td>';
                echo '<td>' . htmlspecialchars($row["major_name"]) . '</td>';
                echo '<td>' . htmlspecialchars($row["balance"]) . '</td>';
                echo '</tr>';
            }
            echo '</table>';
        } else {
            echo "Tidak Ada Data";
        }
        $conn->close();
        ?>

        <!-- Pagination and buttons are the same -->
        <div class="pagination">
    <!-- Tombol 'Sebelumnya' -->
    <?php if ($page > 1): ?>
        <a href="?page=<?= $page - 1 ?>&status=<?= $status_filter ?>&search=<?= $search_query ?>&major=<?= $major_filter ?>">Sebelumnya</a>
    <?php endif; ?>

    <!-- Tampilkan nomor halaman -->
    <?php for ($i = 1; $i <= $pages; $i++): ?>
        <?php if ($i == $page): ?>
            <a href="?page=<?= $i ?>&status=<?= $status_filter ?>&search=<?= $search_query ?>&major=<?= $major_filter ?>" class="active"><?= $i ?></a>
        <?php else: ?>
            <a href="?page=<?= $i ?>&status=<?= $status_filter ?>&search=<?= $search_query ?>&major=<?= $major_filter ?>"><?= $i ?></a>
        <?php endif; ?>
    <?php endfor; ?>

    <!-- Tombol 'Selanjutnya' -->
    <?php if ($page < $pages): ?>
        <a href="?page=<?= $page + 1 ?>&status=<?= $status_filter ?>&search=<?= $search_query ?>&major=<?= $major_filter ?>">Selanjutnya</a>
    <?php endif; ?>
</div>


        <div class="button-group">
            <form action="index.php" method="GET">
                <button type="submit">Kembali</button>
            </form>
        </div>
    </div>

    
    </div>

    <!-- Modal Dialog is the same -->
    <div id="confirmationModal" class="modal">
        <div class="modal-content">
            <p>Apakah Anda yakin ingin menghapus data ini?</p>
            <div class="modal-buttons">
                <button id="confirmButton">Ya</button>
                <button type="button" onclick="closeModal()">Tidak</button>
            </div>
        </div>
    </div>

</body>

</html>



