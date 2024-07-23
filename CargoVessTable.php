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
  unset($_SESSION['user_status']);
  
  // destroy the session
  session_destroy();
  
  // redirect user to login page
  header("Location: index.php");
  exit();
}
if (isset($_POST['inputcargo'])) {
    // Retrieve form data
    $VesselCode = $_POST['VesselCode'];
    $Vesselname = $_POST['Vesselname'];
    $DateDD = $_POST['DateDD'];
    $DateInWaterDD = $_POST['DateInWaterDD'];
    $ExpDateLoadline = $_POST['ExpDateLoadline'];
    $PlaceLastDD = $_POST['PlaceLastDD'];
    $EstDateNextDD = $_POST['EstDateNextDD'];
    $Remarks = $_POST['Remarks'];
    // Check if the DateInWaterDD is already taken
    $check_VesselCode = $con->query("SELECT * FROM tbl_cargo_record WHERE VesselCode = '$VesselCode'");
  
    if ($check_VesselCode->num_rows > 0) {
        echo '<script>alert("The Vessel Code is already taken")</script>';
    } else {
      
        $register = $con->prepare("INSERT INTO tbl_cargo_record (VesselCode, Vesselname, DateDD, DateInWaterDD, ExpDateLoadline, PlaceLastDD, EstDateNextDD, Remarks) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $register->bind_param("ssssssss", $VesselCode, $Vesselname, $DateDD, $DateInWaterDD, $ExpDateLoadline, $PlaceLastDD, $EstDateNextDD, $Remarks);
        $active = 0; // User is not yet active
        $register->execute();
  
        if ($register) {
            // Generate the activation link
        
            echo '<script>
                document.addEventListener("DOMContentLoaded", function() {
                    Swal.fire({
                        position: "top-end",
                        icon: "success",
                        title: "Registration Successful",
                        showConfirmButton: false,
                        timer: 1500
                    }).then(function() {
                        window.location.href = "admin.php";
                    });
                });
            </script>';
        } else {
            if (empty($department)) {
                echo '<script>
                    document.addEventListener("DOMContentLoaded", function() {
                        Swal.fire({
                            icon: "error",
                            title: "Registration Failed",
                            text: "Please try again."
                        });
                    });
                </script>';
            }
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
                    <h1 class="h3 mb-2 text-gray-800">Cargo Vessels Tables</h1>

                    <!-- DataTables Example -->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3 d-flex align-items-center">
                            <h6 class="m-0 font-weight-bold text-primary">ASLI DRYDOCK TABLE 2024</h6>
                            <button class="btn btn-sm btn-default btn-flat border-success ml-auto mr-2" type="button" data-toggle="modal" data-target="#customCSVModal">
                                <i class="fa fa-plus"></i> Add Vessel
                            </button>
                            <a href="print_cargo.php">
                                <button class="btn btn-sm btn-success" type="button" onclick="printPage()">
                                    <div class="d-flex justify-content-center align-items-center">
                                        <i class="fa fa-print"></i> Print
                                    </div>
                                </button>
                            </a>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered text-center" id="dataTable" width="100%" cellspacing="0">
                                <colgroup>
                                        <col width="2%">
                                        <col width="8%">
                                        <col width="15%">
                                        <col width="10%">
                                        <col width="25%">
                                        <col width="10%">
                                        <col width="10%">
                                    </colgroup>
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Vessel Code</th>
                                            <th>Vessel Name</th>
                                            <th>Expiration Date of Loadline</th>
                                            <th>Place of Last Drydock</th>
                                            <th>Remarks</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- Add more rows as needed -->
                                        <?php
                                        $i = 1;
                                        $selectAll = $con->query("SELECT * FROM tbl_records WHERE type ='c'");
                                            while ($vessel = $selectAll->fetch_assoc()) :
                                                $formattedDate = (new DateTime($vessel['ExpDateLoadline']))->format('F d, Y');
                                            ?>
                                        <tr>
                                            <th scope="row"><?php echo $i++; ?></th>
                                            <td><?php echo $vessel['VesselCode']; ?></td>
                                            <td><?php echo $vessel['Vesselname']; ?></td>
                                            <td><?php echo $formattedDate; ?></td>
                                            <td><?php echo $vessel['PlaceLastDD']; ?></td>
                                            <td><?php echo $vessel['Remarks']; ?></td>
                                            <td>
                                            <button type="button" class="btn btn-success btn-sm" data-toggle="modal"  data-target="#editModal-<?php echo $vessel['id']; ?>">
                                                View
                                            </button>
                                            <a href="C_record.php?id=<?php echo $vessel['id']; ?>">
                                                <button type="button" class="btn btn-primary btn-sm" >
                                                    Manage
                                                </button>
                                            </a>
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
                                                <form action="question_list.php?title_id=<?php //echo $vessel['id']; ?>" method="post">
                                                    <?php

                                                    ?>
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

    <!-- Modal -->
    <div class="modal fade" id="customCSVModal" tabindex="-1" role="dialog" aria-labelledby="csvModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="csvModalLabel">Add New Vessel</h5>
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
                                <label for="" class="control-label">Vessel Code</label>
                                <input type="text" name="VesselCode" class="form-control form-control-sm" required>
                            </div>
                            <div class="form-group">
                                <label for="" class="control-label">Date of DryDock</label>
                                <input type="date" name="DateDD" class="form-control form-control-sm" required>
                            </div>
                            <div class="form-group">
                                <label for="" class="control-label">Expiration Date of Loadline</label>
                                <input type="date" name="ExpDateLoadline" class="form-control form-control-sm" required>
                            </div>
                            <div class="form-group">
                                <label for="" class="control-label">Estimated Date of Next DryDock</label>
                                <input type="date" name="EstDateNextDD" class="form-control form-control-sm" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="" class="control-label">Vessel Name</label>
                                <input type="text" name="Vesselname" class="form-control form-control-sm" required>
                            </div>
                            <div class="form-group">
                                <label for="" class="control-label">Date of In-Water DryDock</label>
                                <input type="date" name="DateInWaterDD" class="form-control form-control-sm" required>
                            </div>
                            <div class="form-group">
                                <label class="control-label">Place of Last DryDock</label>
                                <textarea name="PlaceLastDD" id="" cols="30" rows="4" class="form-control" required></textarea>
                            </div>
                            <div class="form-group">
                                <label for="remarks" class="control-label">Remarks</label>
                                <select class="form-control form-control-sm" id="Remarks" name="Remarks" required>
                                    <option value="" disabled selected>Select Options</option>
                                    <option value="Currently at Sangali" >Currently at Sangali</option>
                                    <option value="On Voyage" >On Voyage</option>
                                    <option value="Waiting for the requirements">Waiting for the requirements</option>
                                    <option value="Annual DryDock">Annual DryDock</option>
                                    <option value="Last Extension">Last Extension</option>
                                    <option value="On Drydock">On Dry dock</option>
                                    <option value="No Operation">No Operation</option>
                                </select>
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
    <script src="vendor/datatables/jquery.dataTables.min.js"></script>
    <script src="vendor/datatables/dataTables.bootstrap4.min.js"></script>
    <!-- Page level custom scripts -->
    <script src="js/demo/datatables-demo.js"></script>

    <!-- Custom script to handle contenteditable and saving data -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const table = document.getElementById('dataTable');

            // Function to save the table data
            function saveTableData() {
                const rows = table.querySelectorAll('tbody tr');
                const dataToSave = [];
                rows.forEach(row => {
                    const rowData = Array.from(row.children).map(cell => cell.textContent.trim());
                    dataToSave.push(rowData);
                });

                // Example fetch call to send the data to the server
                fetch('/save', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ data: dataToSave })
                })
                .then(response => response.json())
                .then(data => {
                    console.log('Server response:', data);
                })
                .catch(error => {
                    console.error('Error:', error);
                });
            }

            // Save button click event
            const saveButton = document.getElementById('saveButton');
            saveButton.addEventListener('click', function () {
                saveTableData();
            });

            // Event listener for contenteditable cells to save data immediately on input change
            table.addEventListener('input', function (event) {
                if (event.target.tagName === 'TD' && event.target.isContentEditable) {
                    const row = event.target.parentNode;
                    const rowData = Array.from(row.children).map(cell => cell.textContent.trim());

                    // Example fetch call to save the individual row data
                    fetch('/saveRow', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({ data: rowData })
                    })
                    .then(response => response.json())
                    .then(data => {
                        console.log('Server response:', data);
                    })
                    .catch(error => {
                        console.error('Error:', error);
                    });
                }
            }, true);
        });
    </script>
</body>

</html>
