<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="dist\img\ITTI_Logo index.ico" type="image/x-icon">
    <title>Outstanding Ticket MTC</title>
    <script type="text/javascript" src="files\bower_components\jquery\js\jquery.min.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f5f5f5;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            background-color: #fff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border: 1px solid #ddd;
        }

        th, td {
            padding: 8px 10px; /* Diperkecil padding */
            text-align: left;
            border: 1px solid #ddd;
            color: #1a0808; /* Warna teks hitam */
        }

        th {
            background-color: #2196F3; /* Biru */
            text-transform: uppercase;
            font-size: 14px;
            color: #1a0808; /* Warna teks hitam */
        }

        th.rotate {
            white-space: nowrap;
            transform: rotate(-90deg);
            transform-origin: bottom left;
            padding: 10px 5px; /* Penyesuaian padding untuk teks yang terputar */
        }

        tr:hover {
            background-color: #f1f1f1;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        td {
            font-size: 14px;
        }

        td.mulai {
            font-size: 12px; /* Ukuran font lebih kecil untuk kolom Mulai */
        }

        caption {
            caption-side: top;
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 10px;
        }

        @media (max-width: 600px) {
            table {
                width: 100%;
                display: block;
                overflow-x: auto;
                white-space: nowrap;
            }

            th, td {
                padding: 6px 8px; /* Diperkecil padding untuk layar kecil */
                font-size: 12px;
            }
        }

        .status-danger {
            background-color: #e74c3c; /* Merah */
            color: white;
            padding: 4px 8px;
            border-radius: 4px;
            font-weight: bold;
        }

        .status-warning {
            background-color: #f39c12; /* Oranye */
            color: white;
            padding: 4px 8px;
            border-radius: 4px;
            font-weight: bold;
        }

        .text-center {
            text-align: center;
        }
    </style>
</head>
<body>
<h2>SAFETY STOCK MTC</h2>
<table id="data-table">
    <thead>
        <tr>
            <th class="text-center">No</th>
            <th>Nama Barang</th>
            <th class="text-center">Stock Minimum</th>
            <th class="text-center">Satuan</th>
            <th class="text-center">Balance</th>
            <th class="text-center">Satuan</th>
            <th class="text-center">Status</th>
        </tr>
    </thead>
    <tbody>
        <!-- Data will be inserted here via JavaScript -->
    </tbody>
</table>

<script>
    function fetchData() {
        $.ajax({
            url: 'fetch_data_safetystock.php',
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                console.log("berhasil"); 
                let tableBody = $('#data-table tbody');
                tableBody.empty();  // Clear previous data

                // Loop through the data and append rows
                data.forEach(function(row, index) {
                    let statusText = '';
                    let statusClass = '';
                    let statusIcon = '⚠️'; // Bisa ganti pakai icon lain / <i> FontAwesome

                    if (row.STOCK_STATUS == 1) {
                        statusText = 'Harus Ditambah';
                        statusClass = 'status-danger'; // Kelas CSS merah
                    } else if (row.STOCK_STATUS == 2) {
                        statusText = 'Stok Hampir Habis';
                        statusClass = 'status-warning'; // Kelas CSS oranye
                    } else {
                        statusText = '';
                    }

                    tableBody.append(`
                        <tr>
                            <td class="text-center">${index + 1}</td>
                            <td>${row.LONGDESCRIPTION}</td>
                            <td class="text-center">${row.QTY_MIN}</td>
                            <td class="text-center">${row.SATUAN}</td>
                            <td class="text-center">${row.QTY_AVAILABLE}</td>
                            <td class="text-center">${row.SATUAN}</td>
                            <td>${statusIcon}<span class="${statusClass}"> ${statusText}</span></td>
                        </tr>
                    `);
                });
            },
            error: function(xhr, status, error) {
                console.log("Error: " + error);  // Log error if AJAX fails
                console.log("Response Text:", xhr.responseText);  // The full response (HTML)
            }
        });
    }

    // Fetch data every 2 seconds
    setInterval(fetchData, 2000);
    
    // Initial fetch when the page loads
    fetchData();
</script>

</body>
</html>
