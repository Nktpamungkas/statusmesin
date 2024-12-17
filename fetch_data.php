<?php
require_once "koneksi.php";

// Ambil parameter DEPT dari request
$dept = isset($_GET['DEPT']) ? $_GET['DEPT'] : 'BRS';

$resultMain = "SELECT
                TRIM(p.CODE) AS CODE,
                COALESCE(TRIM(p.RESOURCECODE), '???') AS NO_MESIN,
                TRIM(p.HALLNOCODE) AS DEPT,
                p.SEARCHDESCRIPTION
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

while($dataMain = db2_fetch_assoc($queryMain)) :
    $queryTiket = "SELECT * FROM PMBREAKDOWNENTRY WHERE PMBOMCODE = '{$dataMain['CODE']}' AND STATUS = '2'";
    $resultTiket = db2_exec($conn1, $queryTiket);
    $dataTiket = db2_fetch_assoc($resultTiket);
    if($dataTiket['STATUS'] == '2'){
        $icon   = 'icons8-machine-mtc.png'; 
        $status = 'maintenance';
        $blink  = 'class = "blink_me"';
        $no_tiket = $dataTiket['CODE'];
    }else{
        $icon   = 'icons8-machine-run.png'; 
        $status = 'running';
        $blink  = '';
        $no_tiket = $dataTiket['CODE'];
    }
?>
    <div class="machine">
      <a href="DetailStatusMesin.php?pmmachine=<?= $dataMain['CODE']; ?>" target="_blank"><img <?= $blink; ?> src="img/<?= $icon; ?>" alt="Mesin Running"></a>
      <h3><center><?= $dataMain['SEARCHDESCRIPTION']; ?></center></h3>
      <div class="status <?= $status; ?>"><span style="text-transform:uppercase"><?= $status; ?></span></div>
    </div>
<?php endwhile; ?>
