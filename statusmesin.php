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
      padding: 10px;
    }

    .machine {
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      padding: 8px;
      border: 1px solid #333;
      border-radius: 8px;
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
  <link rel="stylesheet" type="text/css" href="files\assets\pages\notification\notification.css">
  <link rel="stylesheet" type="text/css" href="alert/toastr.css" rel="stylesheet">
</head>
<body>
  <div class="container">
    <!-- Mesin akan dirender di sini -->
  </div>

  <!-- Required Jquery -->
  <script type="text/javascript" src="files\bower_components\jquery\js\jquery.min.js"></script>
  <script type="text/javascript" src="files\bower_components\jquery-ui\js\jquery-ui.min.js"></script>
  <script type="text/javascript" src="files\bower_components\popper.js\js\popper.min.js"></script>
  <script type="text/javascript" src="files\bower_components\bootstrap\js\bootstrap.min.js"></script>
  <!-- notification js -->
  <script type="text/javascript" src="files\assets\js\bootstrap-growl.min.js"></script>
  <script type="text/javascript" src="files\assets\pages\notification\notification.js"></script>
  <script src="alert/toastr.js"></script>
  <script type="text/javascript" src="files\assets\js\script.js"></script>
  <script src="https://code.responsivevoice.org/responsivevoice.js"></script>

  <script>
    $(document).ready(function () {
      const depts = ['BRS', 'DYE', 'FIN', 'GKG', 'LAB', 'QAI', 'QCF', 'YND']; // List DEPT
      // const depts = ['FIN']; // List DEPT
      let currentIndex = 0; // Indeks untuk melacak DEPT yang sedang dikirim

      function showToastInfo(message) {
        toastr.info(message, 'Tiket Baru', {
          closeButton: true,
          progressBar: false,
          timeOut: 10000, // Durasi muncul (dalam milidetik, di sini 5000ms = 5 detik)
          extendedTimeOut: 1000, // Waktu tambahan saat mouse hover (dalam milidetik)
          positionClass: 'toast-bottom-right', // Posisi di pojok kanan bawah
          showEasing: "linear",
          hideEasing: "swing",
          showMethod: "show",
          hideMethod: "hide"
        });
      }
      
      function toggleLoading(show) {
          if (show) {
              if (!$('.loading').length) {
                  $('body').append('<div class="loading">Menyiapkan data...</div>');
              }
              $('.loading').show();
          } else {
              $('.loading').hide();
          }
      }

      function loadData() {
          const currentDept = depts[currentIndex]; // Ambil DEPT saat ini
          console.log(currentDept);

          return new Promise((resolve, reject) => {
            $.ajax({
                url: 'fetch_data.php', // URL ke file PHP
                method: 'GET',
                dataType: 'json',
                data: { DEPT: currentDept }, // Kirim DEPT saat ini
                success: function (response) {
                    // Periksa apakah machine_data ada dan tidak kosong
                    if (response.machine_data && response.machine_data.length > 0) {
                        renderMachines(response.machine_data); // Perbarui konten mesin
                    } else {
                        // Tampilkan placeholder atau pesan bahwa tidak ada data mesin
                        $('.container').html('<div>No machines available for this department.</div>');
                    }
                },
                error: function () {
                    // toggleLoading(false);
                    // console.log(response);
                    alert('Error fetching data.');
                }
            });
          });
      }

      function loadAllDepts() {
        loadData().then(() => {
            currentIndex = (currentIndex + 1) % depts.length;
            if (currentIndex < depts.length) {
                loadAllDepts(); // Recursively call for the next department
            }
        });
      }
      
      function loadDataNotification() {
          const currentDept = depts[currentIndex]; // Ambil DEPT saat ini
          // toggleLoading(true);
          $.ajax({
              url: 'fetch_data.php', // URL ke file PHP
              method: 'GET',
              dataType: 'json',
              data: { DEPT: currentDept }, // Kirim DEPT saat ini
              success: function (response) {
                if (response.machine_data && response.machine_data.length > 0) {
                  if (response.status === 'new_data') {
                    console.log(response.new_data);
                    renderNotifications(response.new_data); // Tampilkan notifikasi
                    renderSpeakNotification(response.new_data); // Tampilkan notifikasi suara
                  }
                }else {
                  // Tampilkan placeholder atau pesan bahwa tidak ada data mesin
                  $('.container').html('<div>No machines available for this department.</div>');
                }
              },
              error: function () {
                  // toggleLoading(false);
                  // console.log(response);
                  alert('Error fetching data.');
              }
          });

          // Perbarui indeks untuk siklus berikutnya
          currentIndex = (currentIndex + 1) % depts.length; // Kembali ke awal jika sudah mencapai akhir array
      }

      function renderNotifications(newData) {
        // Durasi jeda antar notifikasi (ms)
        const delay = 2000;
        
        newData.forEach((data, index) => {
          setTimeout(() => {
              showToastInfo(`Mesin: ${data.machine} <br>
                            Description: ${data.description}`);
          }, index * delay);
        });
      }

      function renderSpeakNotification(newData) {
        if (typeof responsiveVoice === 'undefined') {
            console.error('ResponsiveVoice.js is not loaded or supported.');
            alert('Text-to-speech is not supported in this browser.');
            return;
        }

        let index = 0; // Indeks untuk menelusuri array `newData`

        // Fungsi untuk membaca teks berikutnya
        function speakNext() {
            if (index < newData.length) {
                const data = newData[index];
                const message = `tiket baru, mesin "${data.machine.toLowerCase()}"`;

                // Panggil fungsi speak dari ResponsiveVoice
                responsiveVoice.speak(
                    message,                // Teks yang akan dibacakan
                    'Indonesian Female',    // Suara
                    {
                        rate: 1,            // Kecepatan bicara (0.1 - 2)
                        pitch: 1,           // Nada bicara (0 - 2)
                        volume: 1,          // Volume (0 - 1)
                        onend: () => {      // Callback setelah selesai bicara
                            index++;        // Pindahkan ke notifikasi berikutnya
                            speakNext();    // Panggil ulang fungsi untuk notifikasi berikutnya
                        }
                    }
                );
            }
        }

        // Mulai membaca teks pertama
        speakNext();
      }

      function renderMachines(machineData) {
          let machineHTML = '';
          machineData.forEach(data => {
              machineHTML += data.machine_html; // Asumsi server mengirimkan HTML untuk setiap mesin
          });

          $('.container').html(machineHTML);
      }

      loadAllDepts(); // Start the process
      loadDataNotification();

      setInterval(loadData, 5000); // Set interval untuk memperbarui data setiap 120 detik
      setInterval(loadDataNotification, 11000); // Set interval untuk memperbarui data setiap 5 detik
    });
  </script>
</body>
</html>
