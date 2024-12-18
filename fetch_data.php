<?php
require_once "koneksi.php";

// Ambil parameter DEPT dari request
$dept = isset($_GET['DEPT']) ? $_GET['DEPT'] : 'BRS';

$resultMain = "SELECT
                TRIM(p.CODE) AS CODE,
                COALESCE(TRIM(p.RESOURCECODE), '???') AS NO_MESIN,
                TRIM(p.HALLNOCODE) AS DEPT,
                SUBSTR(p.SEARCHDESCRIPTION, 1, 20) AS SEARCHDESCRIPTION
              FROM
                PMBOM p
              LEFT JOIN ADSTORAGE a ON a.UNIQUEID = p.ABSUNIQUEID
              WHERE 
                a.FIELDNAME = 'StatusMesin' 
                AND a.NAMEENTITYNAME = 'PMBoM' 
                AND VALUEBOOLEAN = 1
                AND p.HALLNOCODE = '$dept'
              ORDER BY 
                p.RESOURCECODE ASC, p.HALLNOCODE ASC";
$queryMain = db2_exec($conn1, $resultMain);

$newData = []; // Data untuk menyimpan informasi baru
$machineData = []; // Data untuk menyimpan informasi mesin (HTML generator)

while ($dataMain = db2_fetch_assoc($queryMain)) {
  
    // Cek tiket dengan STATUS '1' (notifikasi baru)
    $tiketNotification        = "SELECT
                                    p.CODE,
                                    p.PMBOMCODE,
                                    p.SYMPTOM,
                                    p2.SEARCHDESCRIPTION
                                  FROM
                                    PMBREAKDOWNENTRY p
                                  LEFT JOIN PMBOM p2 ON p2.CODE = p.PMBOMCODE 
                                  WHERE p.PMBOMCODE = '{$dataMain['CODE']}' AND p.STATUS = '1'";
    $resultTiketNotification  = db2_exec($conn1, $tiketNotification);

    if ($dataTiketNotification = db2_fetch_assoc($resultTiketNotification)) {
        $newData[] = [
            'ticket_code' => $dataTiketNotification['CODE'],
            'machine' => $dataTiketNotification['SEARCHDESCRIPTION'],
            'description' => $dataTiketNotification['SYMPTOM']
        ];
    }

    // Cek tiket dengan STATUS '2' (maintenance atau running)
    $queryTiket = "SELECT * FROM PMBREAKDOWNENTRY WHERE PMBOMCODE = '{$dataMain['CODE']}' AND STATUS = '2'";
    $resultTiket = db2_exec($conn1, $queryTiket);
    $dataTiket = db2_fetch_assoc($resultTiket);

    if ($dataTiket && $dataTiket['STATUS'] == '2') {
        $icon   = 'icons8-machine-mtc.png'; 
        $status = 'maintenance';
        $blink  = 'class = "blink_me"';
        $no_tiket = $dataTiket['CODE'];
    } else {
        $icon   = 'icons8-machine-run.png'; 
        $status = 'running';
        $blink  = '';
        $no_tiket = isset($dataTiket['CODE']) ? $dataTiket['CODE'] : '';
    }

    // Simpan data untuk tampilan HTML
    $machineData[] = [
        'machine_html' => "
            <div class='machine'>
              <a href='DetailStatusMesin.php?pmmachine={$dataMain['CODE']}' target='_blank'><img {$blink} src='img/{$icon}' alt='Mesin Running'></a>
              <h4><center>{$dataMain['DEPT']}</center></h4>
              <h3><center>{$dataMain['SEARCHDESCRIPTION']}</center></h3>
              <div class='status {$status}'><span style='text-transform:uppercase'>{$status}</span></div>
            </div>
        "
    ];
}

// Kirimkan data ke front-end
$response = [
    'status' => !empty($newData) ? 'new_data' : 'no_data', // Ada data baru atau tidak
    'new_data' => $newData,                               // Data baru (notifikasi tiket)
    'machine_data' => $machineData ?? ['machine_html'=> "no"]                        // HTML untuk mesin
];

// Pastikan header JSON sebelum output
header('Content-Type: application/json');
echo json_encode($response);
?>
