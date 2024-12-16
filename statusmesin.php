<?php require_once "koneksi.php" ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Status Mesin</title>
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      /* display: flex; */
      justify-content: center;
      align-items: center;
      min-height: 100vh;
      background-color: #f0f0f0;
      font-family: Arial, sans-serif;
    }

    .container {
      display: grid;
      grid-template-columns: repeat(15, 1fr); /* 5 kolom per baris */
      grid-gap: 1px; /* Jarak antar kotak */
      padding: 2px;
      /* background-color: #ffffff; */
      /* border-radius: 10px; */
      /* box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); */
    }

    .machine {
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      padding: 8px;
      border: 1px solid #333;
      border-radius: 8px;
      /* background-color: #e6e6e6; */
      /* text-align: center; */
    }

    .machine h3 {
      margin-bottom: 1px;
      color: #333;
      font-size: 1.2rem;
    }

    .status {
      padding: 2px 10px;
      border-radius: 5px;
      font-size: 0.7rem;
      color: #fff;
      font-weight: bold;
    }

    .running {
      background-color: #4caf50; /* Hijau */
    }

    .stopped {
      background-color: #f44336; /* Merah */
    }

    .maintenance {
      background-color: #ff9800; /* Orange */
    }

    .machine img {
      width: 60px;
      height: auto;
      margin-bottom: 5px;
    }

    .blink_me {
      animation: blinker 6s linear infinite;
    }

    @keyframes blinker {
      50% {
        opacity: 30%;
      }
    }

    .loading {
      display: flex;
      justify-content: center;
      align-items: center;
      width: 100%;
      height: 100%;
      position: absolute;
      background: rgba(0, 0, 0, 0.5);
      color: #fff;
      font-size: 1.5rem;
      z-index: 10;
    }
  </style>
</head>
<body>
  <?php
    $resultMain = "SELECT
                    TRIM(p.CODE) AS CODE,
                    COALESCE(TRIM(p.RESOURCECODE), '???') AS NAMA_MESIN,
                    TRIM(p.HALLNOCODE) AS DEPT
                  FROM
                    PMBOM p
                  LEFT JOIN ADSTORAGE a ON a.UNIQUEID = p.ABSUNIQUEID
                  WHERE 
                    a.FIELDNAME = 'StatusMesin' 
                    AND a.NAMEENTITYNAME = 'PMBoM' 
                    AND VALUEBOOLEAN = 1
                    AND p.HALLNOCODE = 'FIN'
                  ORDER BY 
                    p.RESOURCECODE ASC, p.HALLNOCODE ASC";
    $queryMain  = db2_exec($conn1, $resultMain);
  ?>
  <div class="container">
    <div class="loading">Menyiapkan data...</div>
  </div>

  <!-- jQuery for AJAX -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script>
    $(document).ready(function(){
      function loadData() {
        $.ajax({
          url: 'fetch_data.php', // URL ke file PHP yang mengambil data dari database
          method: 'GET',
          success: function(data) {
            $('.container').html(data); // Update konten dengan data terbaru
          },
          error: function() {
            alert('Error fetching data.');
          }
        });
      }

      setInterval(loadData, 2000); // Set interval untuk memperbarui data setiap 5 detik
    });
  </script>
</body>
</html>