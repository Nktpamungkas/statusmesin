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
    </style>
</head>
<body>
<h2>Outstanding Breakdown Report</h2>
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
            <th>Total Durasi </th>
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
                    // Ekstrak angka jam dari TOTAL_DURASI_JAM
                    let jamMatch = row.DURASI_FOLLOWUP.match(/^(\d+)\sJam/);
                    let jam = jamMatch ? parseInt(jamMatch[1], 10) : 0;
                    let status = row.STATUS === '1' ? 'Open' : row.STATUS === '2' ? 'In Progress' : 'Closed';
                    
                    // Menyusun teks durasi hanya jika tidak null
                    let durasiText = row.TOTAL_DURASI_BULAN !== '' && row.TOTAL_DURASI_HARI !== '' && row.TOTAL_DURASI_JAM !== '' && row.TOTAL_DURASI_MENIT !== ''  
                                    ?   `${row.TOTAL_DURASI_BULAN} Bulan <br> 
                                        ${row.TOTAL_DURASI_HARI} Hari <br>
                                        ${row.TOTAL_DURASI_JAM} Jam <br>
                                        ${row.TOTAL_DURASI_MENIT} Menit` 
                                    : '';
                    
                    if(durasiText === ''){
                        durasiText = `${row.TOTAL_DURASI_MS}`;
                    }else{
                        durasiText = durasiText;
                    }
                    
                    if(status === 'Closed'){
                        durasiText = `${row.TOTAL_DURASI_TS}`;
                    }else{
                        durasiText = durasiText;
                    }
                    // Tentukan warna latar belakang berdasarkan kondisi jam dan status
                    let backgroundColor = '';
                    let textColor = '';
                    let fontWeight = '';
                    
                    if (jam >= 1) {
                        backgroundColor = '#f24b4b'; // Merah
                        textColor = '#FFF';
                        fontWeight = 'bold';
                    } else if (status === 'Closed' || row.STATUS === '3') {
                        backgroundColor = '#a9d08e'; // Hijau
                        textColor = '#FFF';
                        fontWeight = 'bold';
                    } else if (status === 'In Progress' || row.STATUS === '2') {
                        backgroundColor = '#ffcc66'; // Kuning
                        textColor = '#FFF';
                        fontWeight = 'bold';
                    }
                    tableBody.append(`
                        <tr style="background-color: ${backgroundColor}; color: ${textColor}; font-weight: ${fontWeight}">
                            <td>${row.NO_MESIN}</td>
                            <td>${row.PRIORITYLEVEL}</td>
                            <td>${row.SYMPTOM}</td>
                            <td class="code-cell" data-code="${row.WO}" style="cursor: pointer;" title="Detail Activity ${row.WO}">${row.CODE}</td>
                            <td>${row.JAM_TLP}</td>
                            <td>${row.USER_TLP}</td>
                            <td>${row.PENERIMA_TLP}</td>
                            <td>${row.MULAI}</td>
                            <td>${row.SELESAI}</td>
                            <td>${row.PERSONEL_MEKANIK}</td>
                            <td>${row.DURASI_FOLLOWUP}</td>
                            <td>${row.ONGOING_PROSES_TIME}</td>
                            <td>${durasiText}</td>
                        </tr>
                    `);
                });
                // Tambahkan event listener untuk klik pada kolom CODE
                $('.code-cell').on('click', function() {
                    let code = $(this).data('code');
                    window.open(`detail_activity.php?code=${code}`, '_blank');
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
