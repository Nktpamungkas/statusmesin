<?php require_once "koneksi.php"; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Status Mesin MTC</title>
  <link rel="icon" href="img\favicon.ico" type="image/x-icon">
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
      grid-template-columns: repeat(10, 1fr); /* 5 kolom per baris */
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
  <div class="container">
    <!-- <div class="loading">Menyiapkan data...</div> -->
    <?php require_once "fetch_data.php"; ?>
  </div>

  <!-- jQuery for AJAX -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script>
    $(document).ready(function() {
        const depts = ['BRS', 'DYE', 'FIN', 'GKG', 'LAB', 'QAI', 'QCF', 'YND']; // List DEPT
        let currentIndex = 0; // Indeks untuk melacak DEPT yang sedang dikirim

        function loadData() {
            const currentDept = depts[currentIndex]; // Ambil DEPT saat ini
            $.ajax({
                url: 'fetch_data.php', // URL ke file PHP
                method: 'GET',
                data: { DEPT: currentDept }, // Kirim DEPT saat ini
                success: function(data) {
                    $('.container').html(data); // Update konten dengan data terbaru
                },
                error: function() {
                    alert('Error fetching data.');
                }
            });

            // Perbarui indeks untuk siklus berikutnya
            currentIndex = (currentIndex + 1) % depts.length; // Kembali ke awal jika sudah mencapai akhir array
        }

        setInterval(loadData, 120000); // Set interval untuk memperbarui data setiap 120 detik
    });
</script>

</body>
</html>