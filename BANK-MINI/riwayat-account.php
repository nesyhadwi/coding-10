<?php
// panggil koneksinya
require_once 'db_connection.php';

// Tentukan jumlah data per halaman
$per_page = 10;

// Cek halaman saat ini
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page > 1) ? ($page * $per_page) - $per_page : 0;

// Filter pencarian nama
$search_query = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
$name_query = $search_query !== '' ? "name LIKE '%$search_query%'" : '1=1';

// Kombinasikan filter status dan pencarian name
$where_query = "WHERE $name_query";

// Ambil jumlah total data dengan filter status dan pencarian name
$total_result = $conn->query("SELECT COUNT(DISTINCT name) AS total FROM riwayat_transaksi $where_query");
if (!$total_result) {
    die("Query failed: " . $conn->error);
}
$total = $total_result->fetch_assoc()['total'];
$pages = ceil($total / $per_page);

// Tampilkan data dengan limit, offset, filter status, dan pencarian name
$q = $conn->query("
    SELECT 
        name,
        SUM(CASE WHEN jenis_transaksi = 'setoran' THEN 1 ELSE 0 END) AS total_setor,
        SUM(CASE WHEN jenis_transaksi = 'penarikan' THEN 1 ELSE 0 END) AS total_tarik,
        COUNT(*) AS total_transaksi
    FROM riwayat_transaksi
    $where_query
    GROUP BY name
    ORDER BY total_transaksi DESC
    LIMIT $start, $per_page
");

if (!$q) {
    die("Query failed: " . $conn->error);
}
?>

<!DOCTYPE html>
<html>
    <head>
    <title>Jumlah Data Akun Yang Telah Melakukan Transaksi</title>
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
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Jumlah Data Akun Yang Telah Melakukan Transaksi</h1>

            <!-- Bungkus dropdown dan pencarian dalam div dengan Flexbox -->
            <div class="dropdown-wrapper">
                <!-- Input pencarian nama -->
                <form method="GET" action="">
                    <input type="text" name="search" placeholder="Cari Nama..." value="<?= htmlspecialchars($search_query) ?>">
                </form>

            </div>
        </div>

        <!-- Read atau menampilkan data dari database -->
        <table>
            <tr>
                <th>No</th>
                <th>Nama</th>
                <th>Total Transaksi (Setor)</th>
                <th>Total Transaksi (Tarik)</th>
                <th>Total Semua Transaksi</th>
            </tr>
            <?php
            $no = $start + 1; // nomor urut sesuai dengan pagination
            if ($q->num_rows == 0) {
                echo "<tr><td colspan='5'>No transactions found.</td></tr>";
            } else {
                while ($dt = $q->fetch_assoc()) :
            ?>
            <tr>  
                <td><?= $no++ ?></td>
                <td><?= htmlspecialchars($dt['name']) ?></td>
                <td><?= htmlspecialchars($dt['total_setor']) ?> kali</td>
                <td><?= htmlspecialchars($dt['total_tarik']) ?> kali</td>
                <td><?= htmlspecialchars($dt['total_transaksi']) ?> kali</td>
            </tr>
            <?php
                endwhile;
            }
            ?>
        </table>

        <!-- Pagination -->
        <div class="pagination">
            <!-- Tombol 'Sebelumnya' -->
            <?php if($page > 1): ?>
                <a href="?page=<?= $page - 1 ?>&search=<?= urlencode($search_query) ?>">Sebelumnya</a>
            <?php endif; ?>

            <!-- Tampilkan nomor halaman -->
            <?php for($i = 1; $i <= $pages; $i++): ?>
                <?php if($i == $page): ?>
                    <!-- Halaman saat ini tanpa tautan -->
                    <a href="?page=<?= $i ?>&search=<?= urlencode($search_query) ?>" style="background-color: #333e50;"><?= $i ?></a>
                <?php else: ?>
                    <a href="?page=<?= $i ?>&search=<?= urlencode($search_query) ?>"><?= $i ?></a>
                <?php endif; ?>
            <?php endfor; ?>

            <!-- Tombol 'Selanjutnya' -->
            <?php if($page < $pages): ?>
                <a href="?page=<?= $page + 1 ?>&search=<?= urlencode($search_query) ?>">Selanjutnya</a>
            <?php endif; ?>
        </div>
        <button onclick="history.back()">Kembali</button>
    </div>

    <script>
        function confirmDeletion(id) {
            if (confirm("Anda yakin akan menghapus data ini?")) {
                window.location.href = 'delete_riwayat_count.php?id=' + encodeURIComponent(id);
                setTimeout(function() {
                    document.getElementById('alert-success').style.display = 'block';
                }, 500);
                setTimeout(function() {
                    document.getElementById('alert-success').style.display = 'none';
                }, 3000);
            }
        }
    </script>
</body>
</html>