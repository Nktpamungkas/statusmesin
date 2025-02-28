<?php
// Database connection
require_once "koneksi.php";

try {
    // Prepare the query
    $sql = "SELECT
                ACTIVITYCODE,
                ASSIGNEDTOUSERID,
                CAST(STARTDATE AS DATE)|| ' ' || CAST(STARTDATE AS TIME) AS STARTDATE,
                CAST(ENDDATE AS DATE)|| ' ' || CAST(ENDDATE AS TIME) AS ENDDATE,
                LPAD(HOUR(ENDDATE - STARTDATE), 2, '0') || ' Jam ' || 
                LPAD(MINUTE(ENDDATE - STARTDATE), 2, '0') || ' Menit' AS DURASI
            FROM
                PMWORKORDERDETAIL p
            WHERE 
                PMWORKORDERCODE = '$_GET[code]'";

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
