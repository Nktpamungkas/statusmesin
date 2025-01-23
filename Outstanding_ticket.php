<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Auto Update Data</title>
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
            padding: 12px 15px;
            text-align: left;
            border: 1px solid #ddd;
        }

        th {
            background-color: #007BFF;
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
</head>
<body>
<h2>Data Breakdown</h2>
<table id="data-table">
    <thead>
        <tr>
            <th>No Mesin</th>
            <th>Priority Level</th>
            <th>Symptom</th>
            <th>Code</th>
            <th>Jam Telp</th>
            <th>User Telp</th>
            <th>Penerima Tlp</th>
            <th>Mulai</th>
            <th>Selesai</th>
            <th>Personel Mekanik</th>
            <th>Durasi Followup</th>
            <th>Ongoing Process Time</th>
            <th>Total Durasi Jam</th>
            <th>Total Durasi Menit</th>
        </tr>
    </thead>
    <tbody>
        <!-- Data will be inserted here via JavaScript -->
    </tbody>
</table>

<script>
    function fetchData() {
        $.ajax({
            url: 'fetch_data_outstanding.php',
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                console.log("berhasil"); 
                let tableBody = $('#data-table tbody');
                tableBody.empty();  // Clear previous data

                // Loop through the data and append rows
                data.forEach(function(row) {
                    tableBody.append(`
                        <tr>
                            <td>${row.NO_MESIN}</td>
                            <td>${row.PRIORITYLEVEL}</td>
                            <td>${row.SYMPTOM}</td>
                            <td>${row.CODE}</td>
                            <td>${row.JAM_TLP}</td>
                            <td>${row.USER_TLP}</td>
                            <td>${row.PENERIMA_TLP}</td>
                            <td>${row.MULAI}</td>
                            <td>${row.SELESAI}</td>
                            <td>${row.PERSONEL_MEKANIK}</td>
                            <td>${row.DURASI_FOLLOWUP}</td>
                            <td>${row.ONGOING_PROSES_TIME}</td>
                            <td>${row.TOTAL_DURASI_JAM}</td>
                            <td>${row.TOTAL_DURASI_MENIT}</td>
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

    // Fetch data every 10 seconds
    setInterval(fetchData, 2000);
    
    // Initial fetch when the page loads
    fetchData();
</script>

</body>
</html>
