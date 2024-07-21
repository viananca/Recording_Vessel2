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
                                                            SELECT cr.rid AS id, cr.VesselCode, cr.Vesselname, cr.DateDD, cr.DateInWaterDD, cr.ExpDateLoadline, cr.PlaceLastDD, cr.EstDateNextDD, cr.Remarks, 'cargo' AS type 
                                                            FROM tbl_cargo_record cr
                                                            INNER JOIN cargo_list cl ON cr.rid = cl.rid
                                                            WHERE cr.DateInWaterDD = '0000-00-00' AND cl.DateInWaterDD = '0000-00-00'
                                                            UNION
                                                            SELECT pr.id AS id, pr.VesselCode, pr.Vesselname, pr.DateDD, pr.DateInWaterDD, pr.ExpDateLoadline, pr.PlaceLastDD, pr.EstDateNextDD, pr.Remarks, 'passenger' AS type 
                                                            FROM tbl_passenger_record pr
                                                            INNER JOIN passenger_list pl ON pr.id = pl.id
                                                            WHERE pr.DateInWaterDD = '0000-00-00' AND pl.DateInWaterDD = '0000-00-00'
                                                        ");

                                                        while ($vessel = $selectAll->fetch_assoc()) :
                                                        ?>
                                                    <tr>
                                                        <th scope="row"><?php echo $i++; ?></th>
                                                        <td><?php echo $vessel['type'] == 'cargo' ? 'Cargo' : 'Passenger'; ?></td>
                                                        <td><?php echo $vessel['Vesselname']; ?></td>
                                                        <td><?php echo $vessel['DateDD']; ?></td>
                                                        <td><?php echo $vessel['PlaceLastDD']; ?></td>
                                                        <td><?php echo $vessel['Remarks']; ?></td>
                                                        <td>
                                                            <button type="button" class="btn btn-primary btn-sm" data-toggle="modal"  data-target="#editModal-<?php echo $vessel['id']; ?>">
                                                                Edit
                                                            </button>
                                                        </td>
                                                    </tr>
                                                    <div class="modal fade" id="editModal-<?php echo $vessel['id']; ?>" tabindex="-1" role="dialog" aria-labelledby="editModalLabel-<?php echo $vessel['id']; ?>" aria-hidden="true">
                                                        <div class="modal-dialog modal-xl" role="document">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                            <h5 class="modal-title" id="editModalLabel-<?php echo $vessel['id']; ?>">Review Information</h5>
                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                <span aria-hidden="true">&times;</span>
                                                            </button>
                                                            </div>
                                                            <div class="modal-body">
                                                            <!-- Add your form fields and content here -->
                                                            <form action="question_list.php?title_id=<?php //echo $vessel['rid']; ?>" method="post">
                                                                
                                                                <div class="row">
                                                                    <div class="col-md-6 border-right">
                                                                        <div class="form-group">
                                                                            <label for="" class="control-label">Vessel Code</label>
                                                                            <input type="text" name="VesselCode" class="form-control form-control-sm" value="<?php echo $vessel['VesselCode']; ?>" disabled>
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <label for="" class="control-label">Date of DryDock</label>
                                                                            <input type="date" name="DateDD" class="form-control form-control-sm" value="<?php echo $vessel['DateDD'] ? $vessel['DateDD']: ''; ?>" disabled>
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <label for="" class="control-label">Expiration Date of Loadline</label>
                                                                            <input type="date" name="ExpDateLoadline" class="form-control form-control-sm" value="<?php echo $vessel['ExpDateLoadline'] ? $vessel['ExpDateLoadline']: ''; ?>" disabled>
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <label for="" class="control-label">Estimated Date of Next DryDock</label>
                                                                            <input type="date" name="EstDateNextDD" class="form-control form-control-sm" value="<?php echo $vessel['EstDateNextDD'] ? $vessel['EstDateNextDD']: ''; ?>" disabled>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <div class="form-group">
                                                                            <label for="" class="control-label">Vessel Name</label>
                                                                            <input type="text" name="Vesselname" class="form-control form-control-sm" value="<?php echo $vessel['Vesselname']; ?>" disabled>
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <label for="" class="control-label">Date of In-Water DryDock</label>
                                                                            <input type="date" name="DateInWaterDD" class="form-control form-control-sm" value="<?php echo $vessel['DateInWaterDD'] ? $vessel['DateInWaterDD']: ''; ?>" disabled>
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <label class="control-label">Place of Last DryDock</label>
                                                                            <textarea name="PlaceLastDD" id="" cols="30" rows="4" class="form-control" disabled><?php echo $vessel['PlaceLastDD']; ?></textarea>
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <label for="remarks" class="control-label">Remarks</label>
                                                                            <select class="form-control form-control-sm" id="Remarks" name="Remarks" disabled>
                                                                                <option value="" disabled selected>Select Options</option>
                                                                                <option value="Currently at Sangali" <?php echo ($vessel['Remarks'] == 'Currently at Sangali') ? 'selected' : ''; ?>>Currently at Sangali</option>
                                                                                <option value="On Voyage" <?php echo ($vessel['Remarks'] == 'On Voyage') ? 'selected' : ''; ?>>On Voyage</option>
                                                                                <option value="Waiting for the requirements" <?php echo ($vessel['Remarks'] == 'Waiting for the requirements') ? 'selected' : ''; ?>>Waiting for the requirements</option>
                                                                                <option value="Annual DryDock" <?php echo ($vessel['Remarks'] == 'Annual DryDock') ? 'selected' : ''; ?>>Annual DryDock</option>
                                                                                <option value="Last Extension" <?php echo ($vessel['Remarks'] == 'Last Extension') ? 'selected' : ''; ?>>Last Extension</option>
                                                                                <option value="On Drydock" <?php echo ($vessel['Remarks'] == 'On Drydock') ? 'selected' : ''; ?>>On Dry dock</option>
                                                                                <option value="No Operation" <?php echo ($vessel['Remarks'] == 'No Operation') ? 'selected' : ''; ?>>No Operation</option>
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                
                                                                </div>
                                                                <hr>
                                                                <div class="col-lg-12 text-center">
                                                                <button type="button" class="btn btn-secondary" data-dismiss="modal" aria-label="Close">
                                                                    Cancel
                                                                </button>
                                                                </div>
                                                            </form>
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
            <form action="vesselTable.php" method="POST">
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
                            <textarea name="PlaceLastDD" id="" cols="30" rows="4" class="form-control" required><?php echo $vessel['PlaceLastDD']; ?></textarea>
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
