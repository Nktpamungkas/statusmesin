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
      padding: 2px;
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

    #notification {
      display: none;
      position: fixed;
      top: 10px;
      right: 10px;
      background:rgb(241, 203, 76);
      color: #000;
      padding: 10px;
      border: 1px solid #ffecb5;
      border-radius: 5px;
      z-index: 1000;
      transition: opacity 0.5s ease-in-out;
    }
  </style>
</head>
<body>
  <div id="notification"></div>
  <div class="container">
    <!-- Mesin akan dirender di sini -->
  </div>

  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script>
    $(document).ready(function () {
        // const depts = ['BRS', 'DYE', 'FIN', 'GKG', 'LAB', 'QAI', 'QCF', 'YND']; // List DEPT
        const depts = ['FIN']; // List DEPT
        let currentIndex = 0; // Indeks untuk melacak DEPT yang sedang dikirim

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
            // toggleLoading(true);
            $.ajax({
                url: 'fetch_data.php', // URL ke file PHP
                method: 'GET',
                dataType: 'json',
                data: { DEPT: currentDept }, // Kirim DEPT saat ini
                success: function (response) {
                  console.log(response);
                    // toggleLoading(false);
                    if (response.status === 'new_data') {
                        renderNotifications(response.new_data); // Tampilkan notifikasi
                    }
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

            // Perbarui indeks untuk siklus berikutnya
            currentIndex = (currentIndex + 1) % depts.length; // Kembali ke awal jika sudah mencapai akhir array
        }

        function renderNotifications(newData) {
          let notificationContent = '';

          // Cek apakah browser mendukung Web Speech API
          if (!('speechSynthesis' in window)) {
              console.error('Browser does not support text-to-speech');
              alert('Text-to-speech is not supported in this browser.');
              return;
          }

          const synth = window.speechSynthesis; // Inisialisasi SpeechSynthesis
          let index = 0; // Indeks untuk menelusuri array `newData`

          newData.forEach(data => {
              notificationContent += `
                  <div class="notification-item">
                      <strong>New Ticket:</strong><br>
                      Ticket: ${data.ticket_code}<br>
                      Machine: ${data.machine}<br>
                      Description: ${data.description}
                  </div><br>
              `;
          });

          $('#notification').html(notificationContent).fadeIn();

          // Fungsi untuk membaca teks berikutnya
          function speakNext() {
              if (index < newData.length) {
                  const data = newData[index];
                  const utterance = new SpeechSynthesisUtterance(
                      `TIKET BARU, MESIN ${data.machine}. ${data.description}.`
                  );

                  // Set properti tambahan untuk pengucapan
                  utterance.lang = 'id-ID'; // Bahasa Indonesia
                  utterance.rate = 1;       // Kecepatan bicara (0.1 - 10)
                  utterance.pitch = 1;      // Nada bicara (0 - 2)
                  utterance.volume = 1;     // Volume (0 - 1)

                  // Speak hanya jika speechSynthesis tidak sedang sibuk
                  if (!synth.speaking) {
                      synth.speak(utterance);
                      index++; // Pindahkan ke teks berikutnya setelah selesai
                  } else {
                      console.warn('SpeechSynthesis is busy. Skipping...');
                  }
              }
          }

          // Panggil speakNext setiap kali ada event end
          synth.onend = speakNext;

          // Mulai membaca teks pertama
          speakNext();

          // Hilangkan notifikasi setelah 10 detik
          setTimeout(() => {
              $('#notification').fadeOut();
          }, 10000);
        }

        function renderMachines(machineData) {
            let machineHTML = '';
            machineData.forEach(data => {
                machineHTML += data.machine_html; // Asumsi server mengirimkan HTML untuk setiap mesin
            });

            $('.container').html(machineHTML);
        }

        // Panggil `loadData()` sekali saat halaman dimuat
        loadData();

        setInterval(loadData, 5000); // Set interval untuk memperbarui data setiap 5 detik
    });
  </script>
</body>
</html>
