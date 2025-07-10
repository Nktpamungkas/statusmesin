<?php
// Database connection
require_once "koneksi.php";

try {
    // Prepare the query
    $sql = "SELECT
                p.LONGDESCRIPTION,
                FLOOR(i.SAFETYSTOCK) AS QTY_MIN,
	            FLOOR(b.BASEPRIMARYQUANTITYUNIT) AS QTY_AVAILABLE,
                p.BASEPRIMARYUNITCODE AS SATUAN,
                CASE
                    WHEN b.BASEPRIMARYQUANTITYUNIT < i.SAFETYSTOCK THEN '1'
                    WHEN b.BASEPRIMARYQUANTITYUNIT < (i.SAFETYSTOCK * 2) THEN '2'
                    ELSE ''
                END AS STOCK_STATUS
            FROM
                PRODUCT p 
            LEFT JOIN ITEMWAREHOUSELINK i ON i.ITEMTYPECODE = p.ITEMTYPECODE 
                                        AND i.SUBCODE01 = p.SUBCODE01 
                                        AND i.SUBCODE02 = p.SUBCODE02 
                                        AND i.SUBCODE03 = p.SUBCODE03 
                                        AND i.SUBCODE04 = p.SUBCODE04 
                                        AND i.SUBCODE05 = p.SUBCODE05 
                                        AND i.SUBCODE06 = p.SUBCODE06 
                                        AND i.SUBCODE07 = p.SUBCODE07 
                                        AND i.SUBCODE08 = p.SUBCODE08 
                                        AND i.SUBCODE09 = p.SUBCODE09 
                                        AND i.SUBCODE10 = p.SUBCODE10
            LEFT JOIN BALANCE b ON b.ITEMTYPECODE = p.ITEMTYPECODE 
                                AND b.DECOSUBCODE01 = p.SUBCODE01 
                                AND b.DECOSUBCODE02 = p.SUBCODE02 
                                AND b.DECOSUBCODE03 = p.SUBCODE03 
                                AND b.DECOSUBCODE04 = p.SUBCODE04 
                                AND b.DECOSUBCODE05 = p.SUBCODE05 
                                AND b.DECOSUBCODE06 = p.SUBCODE06 
                                AND b.DECOSUBCODE07 = p.SUBCODE07 
                                AND b.DECOSUBCODE08 = p.SUBCODE08 
                                AND b.DECOSUBCODE09 = p.SUBCODE09 
                                AND b.DECOSUBCODE10 = p.SUBCODE10
            WHERE
                p.ITEMTYPECODE = 'SPR' 
                AND p.SUBCODE01 = 'MTC'
                AND i.LOGICALWAREHOUSECODE = 'M201'
                AND (
                    b.BASEPRIMARYQUANTITYUNIT < i.SAFETYSTOCK
                    OR b.BASEPRIMARYQUANTITYUNIT < (i.SAFETYSTOCK * 2)
                )";

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
