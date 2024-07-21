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

$rid = isset($_GET['rid']) ? $_GET['rid'] : null;

if ($rid) {
    // Fetch the record based on rid
    $selectRecord = $con->prepare("SELECT * FROM tbl_cargo_record WHERE rid = ?");
    $selectRecord->bind_param("s", $rid);
    $selectRecord->execute();
    $result = $selectRecord->get_result();
    $cargovess = $result->fetch_assoc();
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
            #printPageButton, #backButton, #labelP {
                display: none;
            }
        }
    </style>
</head>
<body class="index">
<div class="container-fluid">

<!-- Page Heading -->
<h1 class="h3 mb-2 text-gray-800" id="labelP">Preview DryDock History</h1>
<button type="button" class="btn btn-success" id="printPageButton" 
    onclick="window.print();">
    <span class="fas fa fa-print"></span> Print
</button>
<br><br>
<!-- Content Row -->
<div class="row">

    <!-- Grow In Utility -->
    <div class="col-lg-12">

        <div class="card position-relative">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary"><b><?php  echo $cargovess['Vesselname']; ?>  - </b><?php  echo $cargovess['VesselCode']; ?></h6>
            </div>
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
                            if ($cargovess) {
                                    $formattedDD = (new DateTime($cargovess['DateDD']))->format('F d, Y');
                                    $formattedDateInWater = (new DateTime($cargovess['DateInWaterDD']))->format('F d, Y');
                                    $formattedExpDate = (new DateTime($cargovess['ExpDateLoadline']))->format('F d, Y');
                                    $formattedEstDate = (new DateTime($cargovess['EstDateNextDD']))->format('F d, Y');
                                ?>
                            <tr>
                                <th scope="row" ><?php echo $i++; ?></th>
                                <td ><?php echo $cargovess['VesselCode']; ?></td>
                                <td ><?php echo $cargovess['Vesselname']; ?></td>
                                <td ><?php echo $formattedDD; ?></td>
                                <td ><?php echo $formattedDateInWater; ?></td>
                                <td ><?php echo $formattedExpDate; ?></td>
                                <td ><?php echo $cargovess['PlaceLastDD']; ?></td>
                                <td ><?php echo $formattedEstDate; ?></td>
                                <td ><?php echo $cargovess['Remarks']; ?></td>
                            </tr>
                            <?php
                            } else {
                                echo "<tr><td colspan='7'>No record found.</td></tr>";
                            }
                            ?>
                        
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