<?php
// Database connection
require_once "koneksi.php";

try {
    // Prepare the query
    $sql = "SELECT DISTINCT * FROM (SELECT  
                p.PMBOMCODE || ' ' || p2.LONGDESCRIPTION AS NO_MESIN, 
                CASE
                    WHEN a.VALUESTRING = 1 THEN 'Normal'
                    WHEN a.VALUESTRING = 2 THEN 'High'
                    WHEN a.VALUESTRING = 3 THEN 'Low'
                    ELSE ''
                END AS PRIORITYLEVEL,
                p.SYMPTOM,
                p.CODE,
                p3.CODE AS WO,
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
                -- Tgl mulai atau Tgl oday - Tgl tlp
				LPAD(MONTH(COALESCE(p3.STARTDATE, CURRENT_TIMESTAMP) - a2.VALUETIMESTAMP), 2, '0') || ' Bulan ' || 
    			LPAD(DAY(COALESCE(p3.STARTDATE, CURRENT_TIMESTAMP) - a2.VALUETIMESTAMP), 2, '0') || ' Hari ' ||
				LPAD(HOUR(COALESCE(p3.STARTDATE, CURRENT_TIMESTAMP) - a2.VALUETIMESTAMP), 2, '0') || ' Jam ' || 
				LPAD(MINUTE(COALESCE(p3.STARTDATE, CURRENT_TIMESTAMP) - a2.VALUETIMESTAMP), 2, '0') || ' Menit' AS DURASI_FOLLOWUP,
				-- Tgl today - Tgl mulai
				LPAD(MONTH(CURRENT_TIMESTAMP - p3.STARTDATE), 2, '0') || ' Bulan ' ||
				LPAD(DAY(CURRENT_TIMESTAMP - p3.STARTDATE), 2, '0') || ' Hari ' ||
				LPAD(HOUR(CURRENT_TIMESTAMP - p3.STARTDATE), 2, '0') || ' Jam ' || 
				LPAD(MINUTE(CURRENT_TIMESTAMP - p3.STARTDATE), 2, '0') || ' Menit' AS ONGOING_PROSES_TIME,
				-- Hitungan dari durasi followup + on going process time
				LPAD(MONTH(p3.STARTDATE - a2.VALUETIMESTAMP), 2, '0') + LPAD(MONTH(CURRENT_TIMESTAMP - p3.STARTDATE), 2, '0') AS TOTAL_DURASI_BULAN,
    			LPAD(DAY(p3.STARTDATE - a2.VALUETIMESTAMP), 2, '0') + LPAD(DAY(CURRENT_TIMESTAMP - p3.STARTDATE), 2, '0') AS TOTAL_DURASI_HARI,
                LPAD(HOUR(p3.STARTDATE - a2.VALUETIMESTAMP), 2, '0') + LPAD(HOUR(CURRENT_TIMESTAMP - p3.STARTDATE), 2, '0') AS TOTAL_DURASI_JAM,
                LPAD(MINUTE(p3.STARTDATE - a2.VALUETIMESTAMP), 2, '0') + LPAD(MINUTE(CURRENT_TIMESTAMP - p3.STARTDATE), 2, '0') AS TOTAL_DURASI_MENIT,
				-- Hitungan dari tgl mulai - selesai
				LPAD(MONTH(p3.ENDDATE - p3.STARTDATE), 2, '0') || ' Bulan ' || 
				LPAD(DAY(p3.ENDDATE - p3.STARTDATE), 2, '0') || ' Hari ' || 
				LPAD(HOUR(p3.ENDDATE - p3.STARTDATE), 2, '0') || ' Jam ' || 
				LPAD(MINUTE(p3.ENDDATE - p3.STARTDATE), 2, '0') || ' Menit' AS TOTAL_DURASI_MS,
                -- Hitungan dari tgl tlp - selesai
                LPAD(MONTH(p3.ENDDATE - a2.VALUETIMESTAMP), 2, '0') || ' Bulan ' || 
                LPAD(DAY(p3.ENDDATE - a2.VALUETIMESTAMP), 2, '0') || ' Hari ' || 
                LPAD(HOUR(p3.ENDDATE - a2.VALUETIMESTAMP), 2, '0') || ' Jam ' || 
                LPAD(MINUTE(p3.ENDDATE - a2.VALUETIMESTAMP), 2, '0') || ' Menit' AS TOTAL_DURASI_TS,
                p.STATUS,
                p.CREATIONDATETIME
            FROM PMBREAKDOWNENTRY p 
            LEFT JOIN ADSTORAGE a ON a.UNIQUEID = p.ABSUNIQUEID AND a.FIELDNAME = 'PrioritasTicketMTC'
            LEFT JOIN PMBOM p2 ON p2.CODE = p.PMBOMCODE 
            LEFT JOIN ADSTORAGE a2 ON a2.UNIQUEID = p.ABSUNIQUEID AND a2.FIELDNAME = 'JamTelp'
            LEFT JOIN ADSTORAGE a3 ON a3.UNIQUEID = p.ABSUNIQUEID AND a3.FIELDNAME = 'UserTelp'
            LEFT JOIN ADSTORAGE a4 ON a4.UNIQUEID = p.ABSUNIQUEID AND a4.FIELDNAME = 'Penerimatelp'
            LEFT JOIN PMWORKORDER p3 ON p3.PMBREAKDOWNENTRYCODE = p.CODE 
            WHERE 
                p.COUNTERCODE = 'PBD001' AND p.STATUS IN ('1', '2', '3') 
                AND CAST(p.CREATIONDATETIME AS DATE) BETWEEN (CURRENT_TIMESTAMP - 2 MONTH) AND CURRENT_TIMESTAMP
            ORDER BY p.STATUS ASC, p.CREATIONDATETIME DESC)";

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
