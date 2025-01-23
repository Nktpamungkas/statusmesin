<?php
// Database connection
require_once "koneksi.php";

try {
    // Prepare the query
    $sql = "SELECT DISTINCT * FROM (SELECT  
                p.PMBOMCODE || ' ' || p2.LONGDESCRIPTION AS NO_MESIN, 
                CASE
                    WHEN p.PRIORITYLEVEL = 0 THEN 'Very High'
                    WHEN p.PRIORITYLEVEL = 1 THEN 'High'
                    WHEN p.PRIORITYLEVEL = 2 THEN 'Medium'
                    WHEN p.PRIORITYLEVEL = 3 THEN 'Normal'
                    WHEN p.PRIORITYLEVEL = 4 THEN 'Low'
                    ELSE ''
                END AS PRIORITYLEVEL,
                p.SYMPTOM,
                p.CODE,
                SUBSTR(a2.VALUETIMESTAMP, 1,10) || ' ' || SUBSTR(a2.VALUETIMESTAMP, 12, 8) AS JAM_TLP,
                a3.VALUESTRING AS USER_TLP,
                CASE
                    WHEN a4.VALUESTRING = 1 THEN 'HENDRO'
                    WHEN a4.VALUESTRING = 2 THEN 'IWAN'
                    WHEN a4.VALUESTRING = 3 THEN 'FIRMAN'
                    ELSE ''
                END AS PENERIMA_TLP,
                SUBSTR(p3.STARTDATE, 1,10) || ' ' || SUBSTR(p3.STARTDATE, 12, 8) AS MULAI,
                SUBSTR(p3.ENDDATE, 1,10) || ' ' || SUBSTR(p3.ENDDATE, 12, 8) AS SELESAI,
                p3.ASSIGNEDTOUSERID AS PERSONEL_MEKANIK,
                LPAD(HOUR(p3.STARTDATE - a2.VALUETIMESTAMP), 2, '0') || ' Jam ' || 
                LPAD(MINUTE(p3.STARTDATE - a2.VALUETIMESTAMP), 2, '0') || ' Menit' AS DURASI_FOLLOWUP,
                LPAD(HOUR(CURRENT_TIMESTAMP - p3.STARTDATE), 2, '0') || ' Jam ' || 
                LPAD(MINUTE(CURRENT_TIMESTAMP - p3.STARTDATE), 2, '0') || ' Menit' AS ONGOING_PROSES_TIME,
                LPAD(HOUR(p3.STARTDATE - a2.VALUETIMESTAMP), 2, '0') + LPAD(HOUR(CURRENT_TIMESTAMP - p3.STARTDATE), 2, '0') AS TOTAL_DURASI_JAM,
                LPAD(MINUTE(p3.STARTDATE - a2.VALUETIMESTAMP), 2, '0') + LPAD(MINUTE(CURRENT_TIMESTAMP - p3.STARTDATE), 2, '0') AS TOTAL_DURASI_MENIT,
                p.STATUS
            FROM PMBREAKDOWNENTRY p 
            LEFT JOIN ADSTORAGE a ON a.UNIQUEID = p.ABSUNIQUEID AND a.FIELDNAME = 'PrioritasTicket'
            LEFT JOIN PMBOM p2 ON p2.CODE = p.PMBOMCODE 
            LEFT JOIN ADSTORAGE a2 ON a2.UNIQUEID = p.ABSUNIQUEID AND a2.FIELDNAME = 'JamTelp'
            LEFT JOIN ADSTORAGE a3 ON a3.UNIQUEID = p.ABSUNIQUEID AND a3.FIELDNAME = 'UserTelp'
            LEFT JOIN ADSTORAGE a4 ON a4.UNIQUEID = p.ABSUNIQUEID AND a4.FIELDNAME = 'Penerimatelp'
            LEFT JOIN PMWORKORDER p3 ON p3.PMBREAKDOWNENTRYCODE = p.CODE 
            WHERE p.COUNTERCODE = 'PBD001' AND p.STATUS IN ('1', '2') 
            ORDER BY p.CREATIONDATETIME DESC)";

    // Execute the query
    $stmt  = db2_exec($conn1, $sql);
    while ($row = db2_fetch_assoc($stmt)) {
        // Loop melalui setiap kolom dalam row dan cek apakah ada nilai null
        foreach ($row as $key => &$value) {
            if ($value === null) {
                $value = ''; // Ganti nilai null dengan string kosong
            }else {
                $value = htmlspecialchars($value, ENT_QUOTES, 'UTF-8'); // Escape karakter khusus
            }
        }

        // Menambahkan row yang sudah diperbarui ke array $data
        $data[] = $row;
    }
    
    // Return the result as JSON
    echo json_encode($data);

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
