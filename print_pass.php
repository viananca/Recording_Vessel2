<?php 
include ('dbconn.php');
session_start();


// check if the session variable is set
if (!isset($_SESSION['email'])) {
  // redirect the user to the login page
  header("Location: index.php");
  exit(); // stop further execution
}
if (isset($_POST['logout'])) {
  // unset session variables
  unset($_SESSION['loggedin']);
  unset($_SESSION['firstname']);
  unset($_SESSION['lastname']);
  unset($_SESSION['user_label']);
  
  // destroy the session
  session_destroy();
  
  // redirect user to login page
  header("Location: index.php");
  exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php include("includes/header.php"); ?>
    <style>
        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
            position: relative;
        }
        .index::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: url('https://upload.wikimedia.org/wikipedia/commons/a/a9/Aleson_MV_Antonia.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            opacity: 0.3;
            z-index: -1;
        }
        .content {
            position: relative;
            z-index: 1;
        }
        .card {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
        }
        .table-responsive {
            flex-grow: 1;
            overflow: auto;
        }
        table {
            width: 100%;
        }

        @media print {
            @page {
                size: landscape;
            }
            #printPageButton, #backButton {
                display: none;
            }
        }
    </style>
    <script>
        window.onload = function() {
            setTimeout(function() {
                window.print();
            }, 500);
        };
    </script>
</head>
<body class="index">
<div class="container-fluid">

<!-- Page Heading -->
<button type="button" class="btn btn-success mt-3" id="printPageButton" 
    onclick="window.print();">
    <span class="fas fa fa-print"></span> Print
</button>
<br><br>
<!-- Content Row -->
<div class="row">

    <!-- Grow In Utility -->
    <div class="col-lg-12">

        <div class="card position-relative">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover text-center" id="dataTable" width="100%" cellspacing="0">
                        <colgroup>
                            <col width="5%">
                            <col width="10%">
                            <col width="10%">
                            <col width="10%">
                            <col width="10%">
                            <col width="10%">
                            <col width="15%">
                            <col width="10%">
                            <col width="15%">
                        </colgroup>
                        <thead>
                            <tr>
                                <th >#</th>
                                <th >Vessel Code</th>
                                <th >Vessel Name</th>
                                <th >Date of Drydock</th>
                                <th >Date of In-Water DryDock</th>
                                <th >Expiration Date of Loadline</th>
                                <th >Place of Last Drydock</th>
                                <th >Estimated Date of Next DryDock</th>
                                <th >Remarks</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Add more rows as needed -->
                            <?php
                            $i = 1;
                            $selectAll = $con->query("SELECT * FROM tbl_passenger_record");
                                while ($passvess = $selectAll->fetch_assoc()) :
                                    $formattedDD = (new DateTime($passvess['DateDD']))->format('F d, Y');
                                    $formattedDateInWater = (new DateTime($passvess['DateInWaterDD']))->format('F d, Y');
                                    $formattedExpDate = (new DateTime($passvess['ExpDateLoadline']))->format('F d, Y');
                                    $formattedEstDate = (new DateTime($passvess['EstDateNextDD']))->format('F d, Y');
                                ?>
                            <tr>
                                <th scope="row" ><?php echo $i++; ?></th>
                                <td ><?php echo $passvess['VesselCode']; ?></td>
                                <td ><?php echo $passvess['Vesselname']; ?></td>
                                <td ><?php echo $formattedDD; ?></td>
                                <td ><?php echo $formattedDateInWater; ?></td>
                                <td ><?php echo $formattedExpDate; ?></td>
                                <td ><?php echo $passvess['PlaceLastDD']; ?></td>
                                <td ><?php echo $formattedEstDate; ?></td>
                                <td ><?php echo $passvess['Remarks']; ?></td>
                            </tr>
                            <?php endwhile; ?>
                        
                        </tbody>
                    </table>
                    <div class="mt-3 text-center">
                        <a href="PassVessTable.php">
                            <button id="backButton" class="btn btn-primary">Back</button>
                        </a>
                    </div>
                </div>
            </div>
        </div>
<br><br>
    </div>

    

</div>

</div>
</body>
</html>