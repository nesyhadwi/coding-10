<!DOCTYPE html>
<html>
<head>
    <title>Transaksi</title>
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
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
            background-color: white;
        }
        .container {
            background-color: #dcf2f1;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 50%;
            margin-top: 50px;
            margin-bottom: 50px;
            box-sizing: border-box;
        }
        h1 {
            text-align: center;
        }
        input[type="text"], input[type="number"], select {
            font-family: Poppins, sans-serif;
            width: 100%;
            padding: 8px;
            margin: 5px 0 10px 0;
            display: inline-block;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }
        .form-row {
            display: flex;
            justify-content: space-between;
        }
        .form-row select {
            width: 48%;
        }
        input[type="submit"], .view-button {
            font-family: Poppins, sans-serif;
            width: 48%;
            background-color: #4158A6;
            color: white;
            padding: 10px 20px;
            margin: 10px 1%;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        input[type="submit"]:hover {
            background-color: #4158A7;
            color: white;
        }
        .view-button {
            background-color: #C63C51;
            text-align: center;
            display: inline-block;
            text-decoration: none;
        }
        .view-button:hover {
            background-color: #D95F59;
        }
        .error-message {
            color: red;
            display: none;
        }
        .ui-autocomplete {
            max-height: 300px; /*batasi tinggi maksimal daftar*/
            overflow-y: auto; /*tambahkan scrollbar vertikan jika hasilnya terlalu banyak*/ 
            overflow-x: hidden; /*Sembunyikan scrollbar horizontal */
            z-index: 1000; /**agar tampil di atas elemn lain*/
        }
        button {
            width: 90px;
            height: 30px;
            display: inline;
            background-color: #4158A6;
            border: none;
            color: white;
            border-radius: 4px;
        }

    </style>
    <script>
        $(function() {
            // Autocomplete for account_number
            $("#account_number").autocomplete({
                source: "search-account-number.php",
                minLength: 1,
                select: function(event, ui) {
                    $("#account_number").val(ui.item.value);
                    $("#name").val(ui.item.name);
                    return false;
                }
            });

            // Fetch data_diri details on account_number input change
            $("#account_number").on("change", function() {
                var account_number = $(this).val();
                if (account_number !== "") {
                    $.ajax({
                        url: "get_nasabah.php",
                        type: "POST",
                        data: { account_number: account_number },
                        success: function(data) {
                            var data_diri = JSON.parse(data);
                            $("#name").val(data_diri.name);
                            $("#saldo_awal").val(data_diri.balance);
                            if (data_diri.status === "Tidak Aktif") {
                                $(".error-message").show();
                                $("input[type='submit']").prop("disabled", true);
                            } else {
                                $(".error-message").hide();
                                $("input[type='submit']").prop("disabled", false);
                            }
                        }
                    });
                }
            });
            $("form").on("submit", function(event) {
                var nominal = parseInt($("input[name='nominal']").val());

                if (nominal < 10000) {
                    alert("nominal transaksi minimal adalah 10.000.");
                    event.preventDefault(); // Mencegah pengiriman form
                }
            });
        });
    </script>
</head>
<body>
    <div class="container">
        <h1>Transaksi</h1>
        <form method="post" action="submit-form-transaction.php">
            <input type="text" id="account_number" name="account_number" placeholder="No Rekening" required>
            <input type="text" id="name" name="name" placeholder="Masukan nama" readonly required>
            <div class="form">
                <select name="jenis_transaksi" required>
                    <option value="" disabled selected>Jenis Transaksi</option>
                    <option value="setoran">Setor</option>
                    <option value="penarikan">Tarik</option>
                </select>
            </div>
            <input type="number" name="nominal" placeholder="nominal" required min="10000">
            <input type="hidden" id="saldo_awal" name="saldo_awal">
            <div class="form-row">
                <input type="submit" name="submit" value="Submit" style="width: 100%;">
            </div>
            <p class="error-message">Transaksi tidak bisa dilakukan. Rekening ini tidak aktif.</p>
        </form>
        <button onclick="history.back()">Kembali</button>
    </div>
</body>
</html>
