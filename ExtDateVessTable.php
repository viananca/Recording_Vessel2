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
        body {
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
            background-size: cover; /* Adjust as needed */
            background-position: center;
            background-repeat: no-repeat;
            opacity: 0.3; /* Adjust opacity as needed */
            z-index: auto;
        }
    </style>
</head>

<body id="page-top" class="index">
    <!-- Page Wrapper -->
    <div id="wrapper">
        <!-- Sidebar -->
        <?php
            function getPP() {
                // Replace the following line with your logic to determine the appropriate link based on user_status
                $user_status = isset($_SESSION['user_status']) ? $_SESSION['user_status'] : '';

                // Example logic:
                switch ($user_status) {
                    case '1':
                        return 'includes/sidebar-admin.php';
                    case '0':
                        return 'includes/sidebar-emp.php';
                    default:
                        return ''; // Handle other cases or provide a default value
                }
            }
            // Include the sidebar based on the user's status
            $sidebarPath = getPP();
            if (!empty($sidebarPath)) {
                include $sidebarPath;
            }
        ?>
        <!-- End of Sidebar -->

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">
            <!-- Main Content -->
            <div id="content">
                <!-- Navbar -->
                <?php include("includes/navbar.php"); ?>
                <!-- End of Navbar -->

                <!-- Begin Page Content -->
                <div class="container-fluid">
                    <!-- Page Heading -->
                    <h1 class="h3 mb-2 text-gray-800">Extension Date Vessel</h1>

                    <!-- DataTables Example -->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3 d-flex align-items-center">
                            <h6 class="m-0 font-weight-bold text-primary">ASLI DRYDOCK TABLE 2024</h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered text-center" id="dataTable" width="100%" cellspacing="0">
                                <colgroup>
                                        <col width="2%">
                                        <col width="8%">
                                        <col width="8%">
                                        <col width="15%">
                                        <col width="15%">
                                        <col width="15%">
                                        <col width="25%">
                                        <col width="15%">
                                    </colgroup>
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Type</th>
                                            <th>Vessel Code</th>
                                            <th>Vessel Name</th>
                                            <th>Expiration Date of Loadline</th>
                                            <th>Estimated Date of Next DryDock</th>
                                            <th>Place of Last Drydock</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- Add more rows as needed -->
                                        <?php
                                        $i = 1;
                                        $currentDate = date('Y-m-d');
                                        $threeMonthsBefore = date('Y-m-d', strtotime('-3 months'));
                                    
                                        // Combined query using UNION with date condition
                                        $selectAll = $con->query("
                                            SELECT rid AS id, VesselCode, Vesselname, DateDD, EstDateNextDD, ExpDateLoadline, PlaceLastDD, 'cargo' AS type FROM tbl_cargo_record
                                            WHERE Exntd = '1'
                                            UNION
                                            SELECT id, VesselCode, Vesselname, DateDD, EstDateNextDD, ExpDateLoadline, PlaceLastDD, 'passenger' AS type FROM tbl_passenger_record
                                            WHERE Exntd = '1'
                                        ");
                                            while ($vessel = $selectAll->fetch_assoc()) :
                                                $formattedExpDate = (new DateTime($vessel['ExpDateLoadline']))->format('F d, Y');
                                                $formattedDate = (new DateTime($vessel['EstDateNextDD']))->format('F d, Y');
                                            ?>
                                        <tr>
                                            <th scope="row"><?php echo $i++; ?></th>
                                            <td><?php echo $vessel['type'] == 'cargo' ? 'Cargo' : 'Passenger'; ?></td>
                                            <td><?php echo $vessel['VesselCode']; ?></td>
                                            <td><?php echo $vessel['Vesselname']; ?></td>
                                            <td><?php echo $formattedExpDate; ?></td>
                                            <td><?php echo $formattedDate; ?></td>
                                            <td><?php echo $vessel['PlaceLastDD']; ?></td>
                                            <td>
                                            <button type="button" class="btn btn-success btn-sm" data-toggle="modal"  data-target="#DDModal-<?php echo $vessel['id']; ?>">
                                                DryDock
                                            </button>
                                            </td>
                                        </tr>
                                        <div class="modal fade" id="DDModal-<?php echo $vessel['id']; ?>" tabindex="-1" role="dialog" aria-labelledby="DDModalLabel-<?php echo $vessel['id']; ?>" aria-hidden="true">
                                            <div class="modal-dialog modal-lg" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="DDModalLabel-<?php echo $vessel['id']; ?>">Extend <span class="font-weight-bold text-primary"><?php echo $vessel['Vesselname']; ?> - <?php echo $vessel['VesselCode']; ?></span></h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="row">
                                                            <div class="col-md-12 ">
                                                            <!-- Add your form fields and content here -->
                                                            <form action="ExtDateVessTable.php" method="POST">
                                                                <?php
                                                                if (isset($_POST['extendbtn'])) {
                                                                    $id = htmlspecialchars($_POST['id']);
                                                                    $type = htmlspecialchars($_POST['type']);
                                                                    $DateDD = htmlspecialchars($_POST['DateDD']);
                                                                    $newPlaceLastDD = htmlspecialchars($_POST['PlaceLastDD']);
                                                                    $DateInWaterDD = htmlspecialchars($_POST['DateInWaterDD']);
                                                                    $EstDateNextDD = htmlspecialchars($_POST['EstDateNextDD']);
                                                                    $Exntd = $_POST['Exntd'];

                                                                    // Update query based on the type of vessel record
                                                                    if ($type == 'cargo') {
                                                                        $update_query = "UPDATE tbl_cargo_record SET DateDD = '$DateDD', PlaceLastDD = '$newPlaceLastDD', Exntd = '0', DateInWaterDD = '0000-00-00', EstDateNextDD = '0000-00-00'  WHERE rid = '$id'";
                                                                        $insert_query = "INSERT INTO cargo_list (rid, DateDD, DateInWaterDD, PlaceLastDD, EstDateNextDD) VALUES ('$id', '$DateDD', '0000-00-00', '$newPlaceLastDD', '0000-00-00')";
                                                                    } elseif ($type == 'passenger') {
                                                                        $update_query = "UPDATE tbl_passenger_record SET DateDD = '$DateDD', PlaceLastDD = '$newPlaceLastDD', Exntd = '0', DateInWaterDD = '0000-00-00', EstDateNextDD = '0000-00-00' WHERE id = '$id'";
                                                                        $insert_query = "INSERT INTO passenger_list (id, DateDD, DateInWaterDD, PlaceLastDD, EstDateNextDD) VALUES ('$id', '$DateDD', '0000-00-00', '$newPlaceLastDD', '0000-00-00')";
                                                                    }

                                                                    // Execute the update query
                                                                    if ($con->query($update_query) === TRUE) {
                                                                        // Execute the insert query for passenger_list table
                                                                        if ($con->query($insert_query) === TRUE) {
                                                                            echo '<script>window.location.href = "' . $_SERVER['PHP_SELF'] . '";</script>';
                                                                            exit();
                                                                        } else {
                                                                            echo "Error inserting record: " . $con->error;
                                                                        }
                                                                    } else {
                                                                        echo "Error updating record: " . $con->error;
                                                                    }
                                                                }
                                                                ?>
                                                                <!-- Hidden input fields to pass id and type -->
                                                                <input type="hidden" name="id" value="<?php echo htmlspecialchars($vessel['id']); ?>">
                                                                <input type="hidden" name="type" value="<?php echo htmlspecialchars($vessel['type']); ?>">

                                                                <div class="row">
                                                                    <div class="col-md-12">
                                                                        <div class="form-group">
                                                                            <label for="EstDateNextDD" class="control-label">Extend Estimated Date of Next DryDock</label>
                                                                            <input type="date" id="EstDateNextDD" name="DateDD" class="form-control form-control-sm" value="<?php echo isset($vessel['EstDateNextDD']) ? htmlspecialchars($vessel['EstDateNextDD']) : ''; ?>">
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <label for="PlaceLastDD" class="control-label">Place of Last DryDock</label>
                                                                            <textarea name="PlaceLastDD" id="PlaceLastDD" cols="30" rows="2" class="form-control"><?php echo htmlspecialchars($vessel['PlaceLastDD']); ?></textarea>
                                                                        </div>
                                                                        <!-- Additional fields for passenger_list table -->
                                                                        <div class="form-group">
                                                                            <input type="hidden" id="DateInWaterDD" name="DateInWaterDD" class="form-control form-control-sm">
                                                                            <input type="hidden" id="EstDateNextDD" name="EstDateNextDD" class="form-control form-control-sm">
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-12 text-right">
                                                                    <button type="submit" class="btn btn-success" name="extendbtn">DryDock</button>
                                                                </div>
                                                            </form>

                                                            </div>
                                                        </div><br>
                                                        <div class="row">
                                                            <div class="col-md-12 border-top py-3">
                                                            <h4 class="font-weight-bold text-secondary py-1">Renew Date of Loadline</h4>
                                                            <!-- Add your form fields and content here -->
                                                                <form action="ExtDateVessTable.php" method="POST">
                                                                    <?php
                                                                    if (isset($_POST['btnrenew'])) {
                                                                        $id = $_POST['id'];
                                                                        $rid = $_POST['rid'];
                                                                        $type = $_POST['type'];
                                                                        $ExpDateLoadline = $_POST['ExpDateLoadline'];
                                                
                                                                        // Update query based on the type of vessel record
                                                                        if ($type == 'cargo') {
                                                                            $update_query = "UPDATE tbl_cargo_record SET ExpDateLoadline='$ExpDateLoadline' WHERE rid = '$id'";
                                                                        } elseif ($type == 'passenger') {
                                                                            $update_query = "UPDATE tbl_passenger_record SET ExpDateLoadline='$ExpDateLoadline' WHERE id = '$id'";
                                                                        }
                                                
                                                                        // Execute the update query
                                                                        if ($con->query($update_query) === TRUE) {
                                                                            echo '<script>window.location.href = "' . $_SERVER['PHP_SELF'] . '";</script>';
                                                                            exit();
                                                                        } else {
                                                                            echo "Error updating record: " . $con->error;
                                                                        }
                                                                    }
                                                                    ?>
                                                                    <input type="hidden" name="id" value="<?php echo $vessel['id']; ?>">
                                                                    <input type="hidden" name="type" value="<?php echo $vessel['type']; ?>">
                                                                    <div class="form-group">
                                                                        <label for="" class="control-label">Expiration Date of Loadline</label>
                                                                        <input type="date"  value="<?php echo $vessel['ExpDateLoadline']; ?>" class="form-control form-control-sm" disabled >
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label for="" class="control-label">Renew Date of Loadline</label>
                                                                        <input type="date" name="ExpDateLoadline" class="form-control form-control-sm" required>
                                                                    </div>
                                                                    <div class="col-md-12 text-right">
                                                                        <button type="submit" name="btnrenew" class="btn btn-success">Renew</button>
                                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal" aria-label="Close">Cancel</button>
                                                                    </div>
                                                                </form>
                                                            </div>
                                                        </div>

                                                        <!-- end row  -->
                                                    </div>
                                                </div>
                                            </div>
                                        </div>


                                    <?php endwhile; ?>
                                    
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /.container-fluid -->
            </div>
            <!-- End of Main Content -->

            <!-- Footer -->
            <footer class="sticky-footer bg-white">
                
            </footer>
            <!-- End of Footer -->
        </div>
        <!-- End of Content Wrapper -->
    </div>
    <!-- End of Page Wrapper -->

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <!-- Logout Modal-->
    <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Ready to Leave?</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">Select "Logout" below if you are ready to end your current session.</div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                    <a class="btn btn-primary" href="logout.php">Logout</a>
                </div>
            </div>
        </div>
    </div>


    <!-- Bootstrap core JavaScript-->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- Core plugin JavaScript-->
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>
    <!-- Custom scripts for all pages-->
    <script src="js/sb-admin-2.min.js"></script>
    <!-- Page level plugins -->
    <script src="vendor/datatables/jquery.dataTables.min.js"></script>
    <script src="vendor/datatables/dataTables.bootstrap4.min.js"></script>
    <!-- Page level custom scripts -->
    <script src="js/demo/datatables-demo.js"></script>

    <!-- Custom script to handle contenteditable and saving data -->
    
</body>

</html>
