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
                    <div class="d-sm-flex align-items-center justify-content-between ">
                        <h1 class="h3 text-gray-800">Dashboard</h1>
                    </div>
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title">DryDock Datatables</h5>

                                    <!-- Table with stripped rows -->
                                        <div class="table-responsive">
                                            <table class="table table-bordered text-center" id="dataTable" width="100%" cellspacing="0">
                                                <colgroup>
                                                    <col width="2%">
                                                    <col width="8%">
                                                    <col width="15%">
                                                    <col width="15%">
                                                    <col width="25%">
                                                    <col width="15%">
                                                    <col width="8%">
                                                </colgroup>
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
                                                        <th>Type</th>
                                                        <th>Vessel Name</th>
                                                        <th>Date of Drydock</th>
                                                        <th>Place of Drydock</th>
                                                        <th>Remarks</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <!-- Add more rows as needed -->
                                                    <?php
                                                        $i = 1;

                                                        // Combined query using UNION with consistent column count and aliases
                                                        $selectAll = $con->query("
                                                            SELECT pr.id AS id, pr.type, pr.VesselCode, pr.Vesselname, pr.DateDD, pr.DateInWaterDD, pr.PlaceLastDD, pr.Remarks
                                                            FROM tbl_records pr
                                                            INNER JOIN tbl_list pl ON pr.id = pl.id
                                                            WHERE pr.DateInWaterDD = '0000-00-00' AND pl.DateInWaterDD = '0000-00-00'
                                                        ");

                                                        while ($vessel = $selectAll->fetch_assoc()) :
                                                        ?>
                                                    <tr>
                                                        <th scope="row"><?php echo $i++; ?></th>
                                                        <td><?php echo $vessel['type'] == 'c' ? 'Cargo' : 'Passenger'; ?></td>
                                                        <td><?php echo $vessel['Vesselname']; ?></td>
                                                        <td><?php echo $vessel['DateDD']; ?></td>
                                                        <td><?php echo $vessel['PlaceLastDD']; ?></td>
                                                        <td><?php echo $vessel['Remarks']; ?></td>
                                                        <td>
                                                            <button type="button" class="btn btn-success btn-sm" data-toggle="modal"  data-target="#editModal-<?php echo $vessel['id']; ?>">
                                                                Done
                                                            </button>
                                                        </td>
                                                    </tr>
                                                    <div class="modal fade" id="editModal-<?php echo $vessel['id']; ?>" tabindex="-1" role="dialog" aria-labelledby="editModalLabel-<?php echo $vessel['id']; ?>" aria-hidden="true">
                                                        <div class="modal-dialog modal-lg" role="document">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title" id="editModalLabel-<?php echo $vessel['id']; ?>">Extend <span class="font-weight-bold text-primary"><?php echo $vessel['Vesselname']; ?> - <?php echo $vessel['VesselCode']; ?></span></h5>
                                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                        <span aria-hidden="true">&times;</span>
                                                                    </button>
                                                                </div>
                                                                <div class="modal-body">
                                                                <!-- Add your form fields and content here -->
                                                                    <form action="dashboard.php" method="post">
                                                                        <?php
                                                                        if (isset($_POST['btnwaterDD'])) {
                                                                            $id = htmlspecialchars($_POST['id']);
                                                                            $DateInWaterDD = htmlspecialchars($_POST['DateInWaterDD']);
                                                                            $PlaceLastDD = htmlspecialchars($_POST['PlaceLastDD']);
                                                                            $EstDateNextDD = htmlspecialchars($_POST['EstDateNextDD']);
                                                                        
                                                                            $dateInWater = new DateTime($DateInWaterDD);
                                                                            $dateInWater->add(new DateInterval('P2Y')); // Add 2 years
                                                                            $EstDateNextDD = $dateInWater->format('Y-m-d');
                                                                        
                                                                            // Update query based on the type of vessel record
                                                                            $update_query = "UPDATE tbl_records SET DateInWaterDD='$DateInWaterDD', PlaceLastDD='$PlaceLastDD', EstDateNextDD='$EstDateNextDD' WHERE id='$id'";
                                                                           
                                                                            // Execute the update query
                                                                            $result = mysqli_query($con, $update_query);
                                                                        
                                                                            if (!$result) {
                                                                                // Display an error message or handle the error appropriately
                                                                                echo "Error: " . mysqli_error($con);
                                                                            } else {
                                                                                // Update the tbl_list table
                                                                                $update_query_list = "UPDATE tbl_list SET DateInWaterDD='$DateInWaterDD', PlaceLastDD='$PlaceLastDD', EstDateNextDD='$EstDateNextDD' WHERE id='$id'";
                                                                                mysqli_query($con, $update_query_list);
                                                                        
                                                                                // Redirect to refresh the page after successful update
                                                                                $redirect_url = $_SERVER['PHP_SELF'];
                                                                                if (isset($_GET['rid'])) {
                                                                                    $redirect_url .= '?rid=' . htmlspecialchars($_GET['rid']);
                                                                                }
                                                                                if (isset($_GET['id'])) {
                                                                                    $redirect_url .= isset($_GET['rid']) ? '&id=' . htmlspecialchars($_GET['id']) : '?id=' . htmlspecialchars($_GET['id']);
                                                                                }
                                                                                
                                                                                echo '<script>window.location.href = "' . $redirect_url . '";</script>';
                                                                                exit();
                                                                            }
                                                                        }
                                                                        
                                                                        ?>
                                                                        <input type="hidden" name="id" value="<?php echo $vessel['id']; ?>">
                                                                        <div class="row">
                                                                            <div class="col-md-6">
                                                                                <div class="form-group">
                                                                                    <label for="" class="control-label">Date of In-Water DryDock</label>
                                                                                    <input type="date" id="DateInWaterDD-<?php echo $vessel['id']; ?>" name="DateInWaterDD" class="form-control form-control-sm" value="<?php echo isset($vessel['DateInWaterDD']) ? $vessel['DateInWaterDD'] : ''; ?>" required>
                                                                                </div>
                                                                                <div class="form-group">
                                                                                    <label for="EstDateNextDD" class="control-label">Estimated Date of Next DryDock</label>
                                                                                    <input type="text" id="EstDateNextDD-<?php echo $vessel['id']; ?>" name="EstDateNextDD" value="EstDateNextDD-<?php echo $vessel['id']; ?>" class="form-control form-control-sm" readonly>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-md-6">
                                                                                <div class="form-group">
                                                                                    <label class="control-label">Place of Last DryDock</label>
                                                                                    <textarea name="PlaceLastDD" cols="30" rows="4" class="form-control"><?php echo isset($vessel['PlaceLastDD']) ? $vessel['PlaceLastDD'] : ''; ?></textarea>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <hr>
                                                                        <div class="col-lg-12 text-center">
                                                                            <button type="button" class="btn btn-secondary" data-dismiss="modal" aria-label="Close">
                                                                                Cancel
                                                                            </button>
                                                                            <button type="submit" class="btn btn-success" name="btnwaterDD">
                                                                                Done
                                                                            </button>
                                                                        </div>
                                                                    </form>
                                                                    <script>
                                                                        // Function to calculate and set EstDateNextDD for this modal
                                                                        document.getElementById('DateInWaterDD-<?php echo $vessel['id']; ?>').addEventListener('change', function() {
                                                                            var dateInWater = new Date(this.value);
                                                                            if (!isNaN(dateInWater.getTime())) {
                                                                                var estDateNext = new Date(dateInWater.getFullYear() + 2, dateInWater.getMonth(), dateInWater.getDate());
                                                                                var estDateNextFormatted = estDateNext.toISOString().slice(0, 10); // Format as yyyy-mm-dd
                                                                                document.getElementById('EstDateNextDD-<?php echo $vessel['id']; ?>').value = estDateNextFormatted;
                                                                            }
                                                                        });
                                                                    </script>

                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                <?php endwhile; ?>
                                                
                                                </tbody>
                                            </table>
                                        </div>
                                    <!-- End Table with stripped rows -->

                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                
                <!-- /.container-fluid -->
                <footer class="sticky-footer bg-white">
            
            </footer>
            </div>
            <!-- End of Main Content -->
        </div>
        
        <!-- End of Content Wrapper -->

    </div>
    <!-- End of Page Wrapper -->

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

 <!-- Logout Modal-->
 <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Ready to Leave?</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
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

    <div class="modal fade" id="notificationModal-<?php echo $alert['id'] . '-' . $alert['id']; ?>" tabindex="-1" role="dialog" aria-labelledby="csvModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="csvModalLabel">Vessel Maintenance Dates</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">
            <!-- Form content goes here -->
            <form action="CargoVessTable.php" method="POST">
                <div class="row">
                    <div class="col-md-6 border-right">
                        <div class="form-group">
                            <label for="" class="control-label">Expiration Date of Loadline</label>
                            <input type="date" name="ExpDateLoadline" class="form-control form-control-sm" required>
                        </div>
                        <div class="form-group">
                            <label for="" class="control-label">Renew Date of Loadline</label>
                            <input type="date" name="EstDateNextDD" class="form-control form-control-sm" disabled>
                        </div>
                        <div class="form-group justify-content-end d-flex">
                            <button type="button" class="btn btn-success" >Renew</button>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="" class="control-label">Estimated Date of Next DryDock</label>
                            <input type="date" name="EstDateNextDD" class="form-control form-control-sm" required>
                        </div>
                        <div class="form-group">
                            <label for="" class="control-label">Extend Date of Next DryDock</label>
                            <input type="date" name="DateInWaterDD" class="form-control form-control-sm" disabled>
                        </div>
                        <div class="form-group justify-content-end d-flex">
                            <button type="button" class="btn btn-success" >Extend</button>
                        </div>
                    </div>
                </div>
                <div class="row">
                <div class="col-md-12 border-top pt-4">
                    <!-- update DD and Place -->
                        <div class="form-group">
                            <label for="" class="control-label">Date of DryDock</label>
                            <input type="date" name="DateDD" class="form-control form-control-sm" required>
                        </div>
                        <div class="form-group">
                            <label class="control-label">Place of Last DryDock</label>
                            <textarea name="PlaceLastDD" id="" cols="30" rows="4" class="form-control" required><?php echo $cargovess['PlaceLastDD']; ?></textarea>
                        </div>
                        <div class="form-group justify-content-end d-flex">
                            <button type="button" class="btn btn-success" >Extend</button>
                        </div>
                    </div>
                </div>
        </div>
        <div class="modal-footer text-right justify-content-center d-flex">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary" name="inputcargo">Save changes</button>
        </div>
        </form>
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
    <script src="vendor/chart.js/Chart.min.js"></script>

    <!-- Page level custom scripts -->
    <script src="js/demo/chart-area-demo.js"></script>
    <script src="js/demo/chart-pie-demo.js"></script>

</body>
</html>
