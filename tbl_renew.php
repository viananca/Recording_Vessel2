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

$id = isset($_GET['id']) ? $_GET['id'] : null;
$rid = isset($_GET['rid']) ? $_GET['rid'] : null;

if ($id) {
    // Fetch the record based on id
    $selectRecord = $con->prepare("SELECT * FROM tbl_passenger_record WHERE id = ?");
    $selectRecord->bind_param("s", $id);
    $selectRecord->execute();
    $result = $selectRecord->get_result();
    $passvess = $result->fetch_assoc();
    $ExpDateLoadline = $passvess['ExpDateLoadline'];
    $EstDateNextDD = $passvess['EstDateNextDD'];
    $PlaceLastDD = $passvess['PlaceLastDD'];
    $Vesselname = $passvess['Vesselname'];
}
if ($rid) {
    // Fetch the record based on id
    $selectRecord = $con->prepare("SELECT * FROM tbl_cargo_record WHERE rid = ?");
    $selectRecord->bind_param("s", $rid);
    $selectRecord->execute();
    $result = $selectRecord->get_result();
    $cargovess = $result->fetch_assoc();
    $ExpDateLoadline = $cargovess['ExpDateLoadline'];
    $EstDateNextDD = $cargovess['EstDateNextDD'];
    $PlaceLastDD = $cargovess['PlaceLastDD'];
    $Vesselname = $cargovess['Vesselname'];
    
    $formattedExpDateLoadline = (new DateTime($ExpDateLoadline))->format('Y-m-d');
}

 // Format dates
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
                        <h1 class="h3 text-gray-800">Vessel Maintenance Dates</h1>
                    </div>


                    <div class="row mb-4">
                        <div class="col-lg-12">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title"><h3 class="text-primary"><?php echo $Vesselname; ?></h3></h5>

                                    <!-- Table with stripped rows -->
                                        <div class="row">
                                            <div class="col-md-6 border-right">
                                                <form action="tbl_renew.php<?php
                                                        if (isset($_GET['rid'])) {
                                                            echo '?rid=' . htmlspecialchars($_GET['rid']);
                                                        }
                                                        if (isset($_GET['id'])) {
                                                            echo isset($_GET['rid']) ? '&id=' . htmlspecialchars($_GET['id']) : '?id=' . htmlspecialchars($_GET['id']);
                                                        }
                                                    ?>" method="POST">
                                                     <?php
                                                        if (isset($_POST['btnextend'])) {
                                                            // Check if 'btnextend' is clicked and handle update based on parameters
                                                            if (isset($_GET['rid'])) {
                                                                $rid = $_GET['rid'];
                                                                $EstDateNextDD = htmlspecialchars($_POST['EstDateNextDD']);

                                                                // Perform the database update query for tbl_cargo_record
                                                                $update_query = "UPDATE tbl_cargo_record SET EstDateNextDD='$EstDateNextDD' WHERE rid ='$rid'";
                                                            } elseif (isset($_GET['id'])) {
                                                                $id = $_GET['id'];
                                                                $EstDateNextDD = htmlspecialchars($_POST['EstDateNextDD']);

                                                                // Perform the database update query for tbl_passenger_record
                                                                $update_query = "UPDATE tbl_passenger_record SET EstDateNextDD='$EstDateNextDD' WHERE id ='$id'";
                                                            }

                                                            // Execute the update query
                                                            $result = mysqli_query($con, $update_query);

                                                            if (!$result) {
                                                                // Display an error message or handle the error appropriately
                                                                echo "Error: " . mysqli_error($con);
                                                            } else {
                                                                // Redirect to refresh the page after successful update
                                                                $redirect_url = $_SERVER['PHP_SELF'];
                                                                if (isset($_GET['rid'])) {
                                                                    $redirect_url .= '?rid=' . htmlspecialchars($_GET['rid']);
                                                                }
                                                                if (isset($_GET['id'])) {
                                                                    $redirect_url .= isset($_GET['rid']) ? '&id=' . htmlspecialchars($_GET['id']) : '?id=' . htmlspecialchars($_GET['id']);
                                                                }
                                                                echo '<script>window.location.href = "'. $redirect_url .'";</script>';
                                                                exit();
                                                            }
                                                        }
                                                    ?>
                                                    <div class="form-group">
                                                        <label for="" class="control-label">Estimated Date of Next DryDock</label>
                                                        <input type="date" name="EstDateNextDD" value="<?php echo $EstDateNextDD; ?>" class="form-control form-control-sm" disabled readonly>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="" class="control-label">Extend Date of Next DryDock</label>
                                                        <input type="date" name="EstDateNextDD" class="form-control form-control-sm" required>
                                                    </div>
                                                    <div class="form-group justify-content-end d-flex">
                                                        <button type="submit" name="btnextend" class="btn btn-success">Extend</button>
                                                    </div>
                                                </form>
                                            </div>
                                            <div class="col-md-6">
                                            <form action="tbl_renew.php<?php
                                                    if (isset($_GET['rid'])) {
                                                        echo '?rid=' . htmlspecialchars($_GET['rid']);
                                                    }
                                                    if (isset($_GET['id'])) {
                                                        echo isset($_GET['rid']) ? '&id=' . htmlspecialchars($_GET['id']) : '?id=' . htmlspecialchars($_GET['id']);
                                                    }
                                                ?>" method="POST">
                                                    <?php
                                                    if (isset($_POST['btnextend2'])) {
                                                        // Check if 'btnextend2' is clicked and handle update based on parameters
                                                        if (isset($_GET['rid'])) {
                                                            $rid = $_GET['rid'];
                                                            $DateDD = htmlspecialchars($_POST['DateDD']);
                                                            $PlaceLastDD = htmlspecialchars($_POST['PlaceLastDD']);

                                                            // Perform the database update query for tbl_cargo_record
                                                            $update_query = "UPDATE tbl_cargo_record SET DateDD='$DateDD', PlaceLastDD= '$PlaceLastDD' WHERE rid ='$rid'";
                                                        } elseif (isset($_GET['id'])) {
                                                            $id = $_GET['id'];
                                                            $DateDD = htmlspecialchars($_POST['DateDD']);
                                                            $PlaceLastDD = htmlspecialchars($_POST['PlaceLastDD']);

                                                            // Perform the database update query for tbl_passenger_record
                                                            $update_query = "UPDATE tbl_passenger_record SET DateDD='$DateDD', PlaceLastDD= '$PlaceLastDD' WHERE id ='$id'";
                                                        }

                                                        // Execute the update query
                                                        $result = mysqli_query($con, $update_query);

                                                        if (!$result) {
                                                            // Display an error message or handle the error appropriately
                                                            echo "Error: " . mysqli_error($con);
                                                        } else {
                                                            // Redirect to refresh the page after successful update
                                                            $redirect_url = $_SERVER['PHP_SELF'];
                                                            if (isset($_GET['rid'])) {
                                                                $redirect_url .= '?rid=' . htmlspecialchars($_GET['rid']);
                                                            }
                                                            if (isset($_GET['id'])) {
                                                                $redirect_url .= isset($_GET['rid']) ? '&id=' . htmlspecialchars($_GET['id']) : '?id=' . htmlspecialchars($_GET['id']);
                                                            }
                                                            echo '<script>window.location.href = "'. $redirect_url .'";</script>';
                                                            exit();
                                                        }
                                                    }
                                                ?>
                                                <!-- update DD and Place -->
                                                    <div class="form-group">
                                                        <label for="" class="control-label">Date of DryDock</label>
                                                        <input type="date" name="DateDD" value="<?php echo $DateDD; ?>" class="form-control form-control-sm" >
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="control-label">Place of Last DryDock</label>
                                                        <textarea name="PlaceLastDD" id="" cols="30" rows="2" class="form-control" required><?php echo $PlaceLastDD; ?></textarea>
                                                    </div>
                                                    <div class="form-group justify-content-end d-flex">
                                                        <button type="submit" name="btnextend2" class="btn btn-success">DryDock</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                        
                                        <div class="row">
                                            <div class="col-md-12 border-top py-3">
                                            <form action="tbl_renew.php<?php
                                                if (isset($_GET['rid'])) {
                                                    echo '?rid=' . htmlspecialchars($_GET['rid']);
                                                }
                                                if (isset($_GET['id'])) {
                                                    echo isset($_GET['rid']) ? '&id=' . htmlspecialchars($_GET['id']) : '?id=' . htmlspecialchars($_GET['id']);
                                                }
                                            ?>" method="POST">
                                                    <?php
                                                    if (isset($_POST['btnrenew'])) {
                                                        // Check if 'btnrenew' is clicked and handle update based on parameters
                                                        if (isset($_GET['rid'])) {
                                                            $rid = $_GET['rid'];
                                                            $ExpDateLoadline = htmlspecialchars($_POST['ExpDateLoadline']);

                                                            // Perform the database update query for tbl_cargo_record
                                                            $update_query = "UPDATE tbl_cargo_record SET ExpDateLoadline='$ExpDateLoadline' WHERE rid ='$rid'";
                                                        } elseif (isset($_GET['id'])) {
                                                            $id = $_GET['id'];
                                                            $ExpDateLoadline = htmlspecialchars($_POST['ExpDateLoadline']);

                                                            // Perform the database update query for tbl_passenger_record
                                                            $update_query = "UPDATE tbl_passenger_record SET ExpDateLoadline='$ExpDateLoadline' WHERE id ='$id'";
                                                        }

                                                        // Execute the update query
                                                        $result = mysqli_query($con, $update_query);

                                                        if (!$result) {
                                                            // Display an error message or handle the error appropriately
                                                            echo "Error: " . mysqli_error($con);
                                                        } else {
                                                            // Redirect to refresh the page after successful update
                                                            echo '<script>window.location.href = "'.$_SERVER['PHP_SELF'].'";</script>';
                                                            exit();
                                                        }
                                                    }
                                                    ?>
                                                    <div class="form-group">
                                                        <label for="" class="control-label">Expiration Date of Loadline</label>
                                                        <input type="date" name="ExpDateLoadline" value="<?php echo $ExpDateLoadline; ?>" class="form-control form-control-sm" disabled readonly>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="" class="control-label">Renew Date of Loadline</label>
                                                        <input type="date" name="ExpDateLoadline" class="form-control form-control-sm" required>
                                                    </div>
                                                    <div class="form-group justify-content-end d-flex">
                                                        <button type="submit" name="btnrenew" class="btn btn-success">Renew</button>
                                                    </div>
                                                </form>
                                            </div>
                                           
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

    <!-- Page level custom scripts -->
    <script src="js/demo/chart-area-demo.js"></script>
    <script src="js/demo/chart-pie-demo.js"></script>

</body>
</html>