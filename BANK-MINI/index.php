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

// Kombinasikan filter status dan pencarian nama
$where_query = "WHERE $status_query AND $name_query";

// Filter status akun
$status_filter = isset($_GET['status']) ? $conn->real_escape_string($_GET['status']) : '';
$status_query = $status_filter !== '' ? "status = '$status_filter'" : '1=1';

// Ambil jumlah total data dengan filter status dan pencarian nama
$total = $conn->query("SELECT COUNT(*) AS total FROM data_diri $where_query")->fetch_assoc()['total'];
$pages = ceil($total / $limit);

// Tampilkan data dengan limit, offset, filter status, dan pencarian nama
$sql = "SELECT data_diri.id, data_diri.name, class.class_name, major.major_name, data_diri.gender, 
                data_diri.creation_date, data_diri.account_number, data_diri.balance, data_diri.nis, data_diri.password, data_diri.status 
        FROM data_diri 
        JOIN class ON data_diri.id_class = class.id 
        JOIN major ON data_diri.id_major = major.id
        $where_query
        LIMIT $start, $limit";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Nasabah</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet" />
    <style>
        * {
            font-family: 'montserrat';
        }
        body {
            margin: 0;
            padding: 0;
    }

    .container {
        width: 100%;
        padding: 20px;
        border-radius: 8px;
        background-color: #fff;
        border: 1px solid #ddd;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }

    h2 {
        margin-top: 0;
        text-align: center;
        font-weight: bold;
        font-size: 24px;
    }

    table {
        width: 98%;
        border-collapse: collapse;
        margin-top: 20px;
        font-size: 12px;
    }

    td {
        padding: 7px 7px 7px 0px;
        text-align: start;
        border-bottom: 1px solid #ddd;
    }

    th {
        text-align: start;
        background-color: #365486;
        padding: 7px 7px 7px 0px;
        color: white;
    }

    .button-group {
        display: flex;
        justify-content: flex-end;
        margin-top: 20px;
    }

    button {
        background-color: #365486;
        color: #fff;
        padding: 5px 10px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
    }

    button:hover {
        background-color: black;
    }

    .pagination {
        text-align: center;
        margin-top: 20px;
    }

    .pagination a {
        color: #337ab7;
        padding: 5px 10px;
        text-decoration: none;
        border: 1px solid #ddd;
        border-radius: 4px;
        margin: 0 4px;
    }

    .pagination a:hover {
        background-color: #f5f5f5;
    }

    .pagination a.active {
        background-color: #337ab7;
        color: #fff;
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

    button.edit {
        background-color: #4CAF50;
    }

    button.hapus {
        background-color: #f44336;
    }

    button.edit:hover {
        background-color: green;
    }

    button.hapus:hover {
        background-color: red;
    }

    input[type="text"] {
        padding: 8px;
        border-radius: 4px;
        width: 300px;
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
        

    <button class="buat-akun-btn" onclick="window.location.href='form.php'">Buat Akun Nasabah</button>
    <button class="tambah-transaksi-btn" onclick="window.location.href='form-transaction.php'">Tambahkan Transaksi</button>
    <button class="riwayat-transaksi-btn" onclick="window.location.href='riwayat-transaksi.php'">Lihat Riwayat Transaksi</button>
    <button class="aktivitas-transaksi-btn" onclick="window.location.href='riwayat-account.php'">Lihat Aktivitas Transaksi</button>
    <button class="rekap-jurusan-btn" onclick="window.location.href='rekap-perjurusan.php'">Lihat Data Rekapan Jurusan</button>
    <button class="data-jurusan-btn" onclick="window.location.href='view_class_major.php'">Lihat Data Jurusan Kelas</button><br><br>
    
    <form method="GET" action="">
        <input type="text" name="search" placeholder="Cari Nama..." value="<?= $search_query ?>">
    </form>

        <?php
        if ($result->num_rows > 0) {
            echo '<table>';
            echo '<tr><th>No</th><th>Nama</th><th>Kelas</th><th>Jurusan</th><th>Jenis Kelamin</th><th>Tgl Pembuatan</th><th>No Rekening</th><th>Saldo</th><th>Username</th><th>Password</th><th>Status</th><th colspan="2">Aksi</th></tr>';
            $no = $start + 1; // Nomor urut mulai dari offset + 1
            while ($row = $result->fetch_assoc()) {
                echo '<tr>';
                echo '<td>' . $no++ . '</td>';
                echo '<td>' . htmlspecialchars($row["name"]) . '</td>';
                echo '<td>' . htmlspecialchars($row["class_name"]) . '</td>';
                echo '<td>' . htmlspecialchars($row["major_name"]) . '</td>';
                echo '<td>' . htmlspecialchars($row["gender"]) . '</td>';
                echo '<td>' . htmlspecialchars($row["creation_date"]) . '</td>';
                echo '<td>' . htmlspecialchars($row["account_number"]) . '</td>';
                echo '<td>'."Rp. " . number_format($row["balance"]), 0, ',', '.'. '</td>';
                echo '<td>' . htmlspecialchars($row["nis"]) . '</td>';
                echo '<td>' . htmlspecialchars($row["password"]) . '</td>';
                echo '<td>' . htmlspecialchars($row["status"]) . '</td>';
                echo '<td class="action-buttons">
                    <button class="edit" onclick="showModal(\'edit\', ' . htmlspecialchars($row["id"]) . ')">Edit</button>
                    <button class="hapus" onclick="showModal(\'delete\', ' . htmlspecialchars($row["id"]) . ')">Hapus</button>  
                </td>';
                echo '</tr>';
            }
            echo '</table>';
        } else {
            echo "Tidak Ada Data";
        }
        $conn->close();
        ?>

        <div class="pagination">
            <!-- Tombol 'Sebelumnya' -->
            <?php if ($page > 1): ?>
                <a href="?page=<?= $page - 1 ?>&status=<?= $status_filter ?>&search=<?= $search_query ?>">Sebelumnya</a>
            <?php endif; ?>

            <!-- Tampilkan nomor halaman -->
            <?php for ($i = 1; $i <= $pages; $i++): ?>
                <?php if ($i == $page): ?>
                    <a href="?page=<?= $i ?>&status=<?= $status_filter ?>&search=<?= $search_query ?>" class="active"><?= $i ?></a>
                <?php else: ?>
                    <a href="?page=<?= $i ?>&status=<?= $status_filter ?>&search=<?= $search_query ?>"><?= $i ?></a>
                <?php endif; ?>
            <?php endfor; ?>

            <!-- Tombol 'Selanjutnya' -->
            <?php if ($page < $pages): ?>
                <a href="?page=<?= $page + 1 ?>&status=<?= $status_filter ?>&search=<?= $search_query ?>">Selanjutnya</a>
            <?php endif; ?>
        </div>

    </div>

    <!-- Modal Dialog -->
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



