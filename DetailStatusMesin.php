<?php 
    ini_set("error_reporting", 1);
    session_start();
    require_once "koneksi.php";
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>DETAIL - STATUS MESIN</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="#">
    <meta name="keywords"
        content="Admin , Responsive, Landing, Bootstrap, App, Template, Mobile, iOS, Android, apple, creative app">
    <meta name="author" content="#">
    <link rel="icon" href="files\assets\images\favicon.ico" type="image/x-icon">
    <link rel="stylesheet" type="text/css" href="files\bower_components\bootstrap\css\bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="files\assets\icon\themify-icons\themify-icons.css">
    <link rel="stylesheet" type="text/css" href="files\assets\icon\icofont\css\icofont.css">
    <link rel="stylesheet" type="text/css" href="files\assets\icon\feather\css\feather.css">
    <link rel="stylesheet" type="text/css" href="files\assets\pages\prism\prism.css">
    <link rel="stylesheet" type="text/css" href="files\assets\css\style.css">
    <link rel="stylesheet" type="text/css" href="files\assets\css\jquery.mCustomScrollbar.css">
    <link rel="stylesheet" type="text/css" href="files\assets\css\pcoded-horizontal.min.css">
    <link rel="stylesheet" type="text/css" href="files\bower_components\datatables.net-bs4\css\dataTables.bootstrap4.min.css">
    <link rel="stylesheet" type="text/css" href="files\assets\pages\data-table\css\buttons.dataTables.min.css">
    <link rel="stylesheet" type="text/css" href="files\bower_components\datatables.net-responsive-bs4\css\responsive.bootstrap4.min.css">
    <link rel="stylesheet" type="text/css" href="files\assets\pages\data-table\extensions\buttons\css\buttons.dataTables.min.css">
    <link rel="stylesheet" type="text/css" href="files\assets\css\jquery.mCustomScrollbar.css">
</head>
<body>
    <div class="pcoded-content">
        <div class="pcoded-inner-content">
            <div class="main-body">
                <div class="page-wrapper">
                    <div class="page-body">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="card">
                                            <div class="card-header table-card-header">
                                                <h5>DETAIL STATUS MESIN MAINTENANCE</h5>
                                            </div>
                                            <div class="card-block">
                                                <div class="dt-responsive table-responsive">
                                                    <table id="basic-btn"
                                                        class="table compact table-striped table-bordered nowrap">
                                                        <thead>
                                                            <tr>
                                                                <th>NO TIKET</th>
                                                                <th>PM MACHINE</th>
                                                                <th>RESOURCE</th>
                                                                <th>SYMPTOM</th>
                                                                <th>STAF</th>
                                                                <th>REMARKS</th>
                                                                <th>START DATE</th>
                                                                <th>END DATE</th>
                                                                <th>STATUS</th>
                                                                <th>CREATIONDATE BREAKDOWN</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php
                                                                $resultMain     = "SELECT
                                                                                        TRIM(p.CODE) AS CODE,
                                                                                        TRIM(p.PMBOMCODE) AS PMBOMCODE,
                                                                                        COALESCE(TRIM(p3.RESOURCECODE), 'NO RESOURCE') AS NO_MESIN,
                                                                                        TRIM(p.SYMPTOM) AS SYMPTOM,
                                                                                        TRIM(p2.ASSIGNEDTOUSERID) AS ASSIGNEDTOUSERID,
                                                                                        TRIM(p2.REMARKS) AS REMARKS,
                                                                                        p2.STARTDATE,
                                                                                        p2.ENDDATE,
                                                                                        CASE
                                                                                            WHEN p.STATUS  = 1 THEN 'OPEN'
                                                                                            WHEN p.STATUS  = 2 THEN 'IN PROGRESS'
                                                                                            WHEN p.STATUS  = 3 THEN 'CLOSED'
                                                                                            WHEN p.STATUS  = 4 THEN 'SUSPENDED'
                                                                                        END AS STATUS,
                                                                                        p.CREATIONDATETIME
                                                                                    FROM
                                                                                        PMBREAKDOWNENTRY p 
                                                                                    LEFT JOIN PMWORKORDER p2 ON p2.PMBREAKDOWNENTRYCODE = p.CODE 
                                                                                    LEFT JOIN PMBOM p3 ON p3.CODE = p.PMBOMCODE
                                                                                    WHERE
                                                                                        p.PMBOMCODE = '$_GET[pmmachine]'
                                                                                    ORDER BY
	                                                                                    p.CREATIONDATETIME DESC";
                                                                $queryMain      = db2_exec($conn1, $resultMain);
                                                            ?>
                                                            <?php while($dataMain   = db2_fetch_assoc($queryMain)) : ?>
                                                                <tr>
                                                                    <td><?= $dataMain['CODE']; ?></td>
                                                                    <td><?= $dataMain['CODE']; ?></td>
                                                                    <td><?= $dataMain['NO_MESIN']; ?></td>
                                                                    <td><?= $dataMain['SYMPTOM']; ?></td>
                                                                    <td><?= $dataMain['ASSIGNEDTOUSERID']; ?></td>
                                                                    <td><?= $dataMain['REMARKS']; ?></td>
                                                                    <td><?= $dataMain['STARTDATE']; ?></td>
                                                                    <td><?= $dataMain['ENDDATE']; ?></td>
                                                                    <td><?= $dataMain['STATUS']; ?></td>
                                                                    <td><?= $dataMain['CREATIONDATETIME']; ?></td>
                                                                </tr>
                                                            <?php endwhile; ?>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script type="text/javascript" src="files\bower_components\jquery\js\jquery.min.js"></script>
    <script type="text/javascript" src="files\bower_components\jquery-ui\js\jquery-ui.min.js"></script>
    <script type="text/javascript" src="files\bower_components\popper.js\js\popper.min.js"></script>
    <script type="text/javascript" src="files\bower_components\bootstrap\js\bootstrap.min.js"></script>
    <script type="text/javascript" src="files\bower_components\jquery-slimscroll\js\jquery.slimscroll.js"></script>
    <script type="text/javascript" src="files\bower_components\modernizr\js\modernizr.js"></script>
    <script type="text/javascript" src="files\bower_components\modernizr\js\css-scrollbars.js"></script>
    <script src="files\bower_components\datatables.net\js\jquery.dataTables.min.js"></script>
    <script src="files\bower_components\datatables.net-buttons\js\dataTables.buttons.min.js"></script>
    <script src="files\assets\pages\data-table\js\jszip.min.js"></script>
    <script src="files\assets\pages\data-table\js\pdfmake.min.js"></script>
    <script src="files\assets\pages\data-table\js\vfs_fonts.js"></script>
    <script src="files\assets\pages\data-table\extensions\buttons\js\dataTables.buttons.min.js"></script>
    <script src="files\assets\pages\data-table\extensions\buttons\js\buttons.flash.min.js"></script>
    <script src="files\assets\pages\data-table\extensions\buttons\js\jszip.min.js"></script>
    <script src="files\assets\pages\data-table\extensions\buttons\js\vfs_fonts.js"></script>
    <script src="files\assets\pages\data-table\extensions\buttons\js\buttons.colVis.min.js"></script>
    <script src="files\bower_components\datatables.net-buttons\js\buttons.print.min.js"></script>
    <script src="files\bower_components\datatables.net-buttons\js\buttons.html5.min.js"></script>
    <script src="files\bower_components\datatables.net-bs4\js\dataTables.bootstrap4.min.js"></script>
    <script src="files\bower_components\datatables.net-responsive\js\dataTables.responsive.min.js"></script>
    <script src="files\bower_components\datatables.net-responsive-bs4\js\responsive.bootstrap4.min.js"></script>
    <script type="text/javascript" src="files\bower_components\i18next\js\i18next.min.js"></script>
    <script type="text/javascript" src="files\bower_components\i18next-xhr-backend\js\i18nextXHRBackend.min.js">
    </script>
    <script type="text/javascript" src="files\bower_components\i18next-browser-languagedetector\js\i18nextBrowserLanguageDetector.min.js"></script>
    <script type="text/javascript" src="files\bower_components\jquery-i18next\js\jquery-i18next.min.js"></script>
    <script src="files\assets\pages\data-table\extensions\buttons\js\extension-btns-custom.js"></script>
    <script src="files\assets\js\pcoded.min.js"></script>
    <script src="files\assets\js\menu\menu-hori-fixed.js"></script>
    <script src="files\assets\js\jquery.mCustomScrollbar.concat.min.js"></script>
    <script type="text/javascript" src="files\assets\js\script.js"></script>
</body>
</html>