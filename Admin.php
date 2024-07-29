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

ob_start();
if (isset($_POST['update'])) {
    if (isset($_GET['id'])) {
        $id = $_GET['id'];
        $VesselCode = htmlspecialchars($_POST['VesselCode']);
        $DateDD = htmlspecialchars($_POST['DateDD']);
        $ExpDateLoadline = htmlspecialchars($_POST['ExpDateLoadline']);
        $Vesselname = htmlspecialchars($_POST['Vesselname']);
        $DateInWaterDD = htmlspecialchars($_POST['DateInWaterDD']);
        $PlaceLastDD = htmlspecialchars($_POST['PlaceLastDD']);
        $EstDateNextDD = htmlspecialchars($_POST['EstDateNextDD']);
        $Remarks = htmlspecialchars($_POST['Remarks']);

        // Check if the user added a custom remark
        if ($Remarks === 'Others' && !empty($_POST['OtherRemarks'])) {
            $Remarks = htmlspecialchars($_POST['OtherRemarks']);
        }

        // Perform the database update query for tbl_records
        $update_query = "UPDATE tbl_records SET 
            VesselCode = ?, DateDD = ?, ExpDateLoadline = ?, Vesselname = ?, DateInWaterDD = ?, PlaceLastDD = ?, EstDateNextDD = ?, Remarks = ? 
            WHERE id = ?";
        $stmt = $con->prepare($update_query);
        $stmt->bind_param("ssssssssi", $VesselCode, $DateDD, $ExpDateLoadline, $Vesselname, $DateInWaterDD, $PlaceLastDD, $EstDateNextDD, $Remarks, $id);
        
        if ($stmt->execute()) {
            // Check if the Remarks already exist
            $check_remarks = $con->prepare("SELECT * FROM remarks WHERE Remarks = ?");
            $check_remarks->bind_param("s", $Remarks);
            $check_remarks->execute();
            $check_remarks_result = $check_remarks->get_result();

            if ($check_remarks_result->num_rows === 0) {
                // Insert into the remarks table
                $insert_remarks = $con->prepare("INSERT INTO remarks (Remarks, added_by) VALUES (?, ?)");
                $added_by = $_SESSION['user_status']; // Get the user status from the session
                $insert_remarks->bind_param("ss", $Remarks, $added_by);
                $insert_remarks->execute();
            }

            // Redirect to Admin.php after successful update
            header('Location: Admin.php');
            exit(); // Ensure no further code is executed
        } else {
            // Display an error message or handle the error appropriately
            echo "Error: " . mysqli_error($con);
        }
    }
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
                                    <h5 class="card-title">Vessels Management Table</h5>

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
                                                        $remarksQuery = $con->query("SELECT DISTINCT Remarks FROM Remarks");
                                                        $remarksOptions = [];
                                                        while ($row = $remarksQuery->fetch_assoc()) {
                                                            $remarksOptions[] = $row['Remarks'];
                                                        }
                                                        $i = 1;
                                                        // Combined query using UNION with consistent column count and aliases
                                                        $selectAll = $con->query("SELECT * FROM tbl_records");

                                                        while ($vessel = $selectAll->fetch_assoc()) :
                                                            $formattedDate = (new DateTime($vessel['ExpDateLoadline']))->format('F d, Y');
                                                        ?>
                                                    <tr>
                                                        <th scope="row"><?php echo $i++; ?></th>
                                                        <td><?php echo $vessel['type'] == 'c' ? 'Cargo' : 'Passenger'; ?></td>
                                                        <td><?php echo $vessel['Vesselname']; ?></td>
                                                        <td><?php echo $formattedDate; ?></td>
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
                                                            <form action="Admin.php?id=<?php echo $vessel['id']; ?>" method="post">
                                                                
                                                                <div class="row">
                                                                    <div class="col-md-6 border-right">
                                                                        <div class="form-group">
                                                                            <label for="" class="control-label">Vessel Code</label>
                                                                            <input type="text" name="VesselCode" class="form-control form-control-sm" value="<?php echo $vessel['VesselCode']; ?>" >
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <label for="" class="control-label">Date of DryDock</label>
                                                                            <input type="date" name="DateDD" class="form-control form-control-sm" value="<?php echo $vessel['DateDD'] ? $vessel['DateDD']: ''; ?>" >
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <label for="" class="control-label">Expiration Date of Loadline</label>
                                                                            <input type="date" name="ExpDateLoadline" class="form-control form-control-sm" value="<?php echo $vessel['ExpDateLoadline'] ? $vessel['ExpDateLoadline']: ''; ?>" >
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <label for="" class="control-label">Estimated Date of Next DryDock</label>
                                                                            <input type="date" name="EstDateNextDD" class="form-control form-control-sm" value="<?php echo $vessel['EstDateNextDD'] ? $vessel['EstDateNextDD']: ''; ?>" >
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <div class="form-group">
                                                                            <label for="" class="control-label">Vessel Name</label>
                                                                            <input type="text" name="Vesselname" class="form-control form-control-sm" value="<?php echo $vessel['Vesselname']; ?>" >
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <label for="" class="control-label">Date of In-Water DryDock</label>
                                                                            <input type="date" name="DateInWaterDD" class="form-control form-control-sm" value="<?php echo $vessel['DateInWaterDD'] ? $vessel['DateInWaterDD']: ''; ?>" >
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <label class="control-label">Place of Last DryDock</label>
                                                                            <textarea name="PlaceLastDD" id="" cols="30" rows="4" class="form-control" ><?php echo $vessel['PlaceLastDD']; ?></textarea>
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <label for="remarks" class="control-label">Remarks</label>
                                                                            <select class="form-control form-control-sm" id="Remarks" name="Remarks" onchange="toggleRemarksInput(this)">
                                                                                <option value="" disabled selected>Select Options</option>
                                                                                <?php foreach ($remarksOptions as $remark) : ?>
                                                                                    <option value="<?php echo $remark; ?>" <?php echo ($vessel['Remarks'] == $remark) ? 'selected' : ''; ?>>
                                                                                        <?php echo $remark; ?>
                                                                                    </option>
                                                                                <?php endforeach; ?>
                                                                                <option value="Others">Others</option>
                                                                            </select>
                                                                            <input type="text" class="form-control form-control-sm mt-2 other-remarks-input" name="OtherRemarks" placeholder="Please specifys" style="display: none;">
                                                                        </div>
                                                                    </div>
                                                                
                                                                </div>
                                                                <hr>
                                                                <div class="col-lg-12 text-center">
                                                                <button type="button" class="btn btn-secondary" data-dismiss="modal" aria-label="Close">
                                                                    Cancel
                                                                </button>
                                                                <button type="submit" class="btn btn-success" name="update">
                                                                    Update
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

   
    <!-- Bootstrap core JavaScript-->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Core plugin JavaScript-->
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>

    <!-- Custom scripts for all pages-->
    <script src="js/sb-admin-2.min.js"></script>

    <!-- Page level plugins -->
    <script src="vendor/chart.js/Chart.min.js"></script>


    <!-- Page level plugins -->
    <script src="vendor/datatables/jquery.dataTables.min.js"></script>
    <script src="vendor/datatables/dataTables.bootstrap4.min.js"></script>
    <!-- Page level custom scripts -->
    <script src="js/demo/chart-area-demo.js"></script>
    <script src="js/demo/chart-pie-demo.js"></script>
    <script src="js/demo/datatables-demo.js"></script>
    <script>
    function toggleRemarksInput(selectElement) {
        var otherRemarksInput = selectElement.closest('.form-group').querySelector('.other-remarks-input');
        if (selectElement.value === 'Others') {
            otherRemarksInput.style.display = 'block';
        } else {
            otherRemarksInput.style.display = 'none';
        }
    }
</script>
</body>
</html>
