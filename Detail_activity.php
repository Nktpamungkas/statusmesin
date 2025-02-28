<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="dist\img\ITTI_Logo index.ico" type="image/x-icon">
    <title>Detail Activity</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f5f5f5;
        }

        table {
            width: 50%;
            border-collapse: collapse;
            margin: 20px auto; /* Mengatur margin untuk memusatkan tabel */
            background-color: #fff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border: 1px solid #ddd;
        }

        th, td {
            padding: 12px 15px;
            text-align: left;
            border: 1px solid #ddd;
        }

        th {
            background-color: #2196F3; /* Biru */
            color: #fff;
            text-transform: uppercase;
            font-size: 14px;
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
                padding: 10px;
                font-size: 12px;
            }
        }
    </style>
    <script type="text/javascript" src="files\bower_components\jquery\js\jquery.min.js"></script>
</head>
<body>
    <table id="detail-table" border="1">
        <thead>
            <tr>
                <th>No</th>
                <th>Detail Activity</th>
                <th>Personel</th>
                <th>Start Date</th>
                <th>End Date</th>
                <th>Durasi</th>
            </tr>
        </thead>
        <tbody>
            <!-- Data akan ditambahkan di sini -->
        </tbody>
    </table>

    <script>
        function fetchDetailData(code) {
            $.ajax({
                url: 'fetch_detail_data.php',
                type: 'GET',
                data: { code: code },
                dataType: 'json',
                success: function(data) {
                    console.log("berhasil");
                    let tableBody = $('#detail-table tbody');
                    tableBody.empty();  // Clear previous data

                    // Loop through the data and append rows
                    data.forEach(function(row, index) {
                        tableBody.append(`
                            <tr>
                                <td>${index + 1}</td>
                                <td>${row.ACTIVITYCODE}</td>
                                <td>${row.ASSIGNEDTOUSERID}</td>
                                <td>${row.STARTDATE}</td>
                                <td>${row.ENDDATE}</td>
                                <td>${row.DURASI}</td>
                            </tr>
                        `);
                    });
                },
                error: function() {
                    console.log("gagal");
                }
            });
        }

        $(document).ready(function() {
            const urlParams = new URLSearchParams(window.location.search);
            const code = urlParams.get('code');
            if (code) {
                fetchDetailData(code);
            }
        });
    </script>
</body>
</html>
