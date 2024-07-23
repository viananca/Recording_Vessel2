<?php
session_start();
include ("dbconn.php");

if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $pass_enc = md5($password);

    $check = $con->query("SELECT * FROM users WHERE email = '$email' AND password = '$pass_enc'");

    $selectAll = $con->query("SELECT * FROM users");

    if (mysqli_num_rows($check) != 0) {
        $results = mysqli_fetch_array($check);
 
    
                $_SESSION['loggedin'] = true;
                $_SESSION['email'] = $_POST['email'];
                $_SESSION['user_status'] = $results['user_status'];
                $_SESSION['firstname'] = $results['firstname'];
                $_SESSION['lastname'] = $results['lastname'];

                echo '<script>
                    Swal.fire({
                        icon: "success",
                        title: "Login Successful",
                        showConfirmButton: false,
                        timer: 2000
                    }).then(function() {
                        window.location.href = "dashboard.php";
                    });
                </script>';

                switch ($results['user_status']) {
                    case '1':
                        $_SESSION['user_label'] = "Admin";
                        header("Location: dashboard.php");
                        break;
                    case '0':
                        $_SESSION['user_label'] = "Employee";
                        header("Location: dashboard.php");
                        break;
                    default:
                        $_SESSION['user_label'] = "Unknown User";
                        break;
                }
            }else {
        // No record found
        echo '<script>
            document.addEventListener("DOMContentLoaded", function() {
                Swal.fire({
                    icon: "error",
                    title: "Oops...",
                    text: "No Record Found!"
                });
            });
        </script>';
    }
}
?>
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
     <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@10.16.7/dist/sweetalert2.min.css">
</head>
<body class="bg-gradient-primary">

    <div class="container">

        <!-- Outer Row -->
        <div class="row justify-content-center">

            <div class="col-xl-6">

                <div class="card o-hidden border-0 shadow-lg my-5">
                    <div class="card-body p-0">
                        <!-- Nested Row within Card Body -->
                        <div class="p-5">
                            <div class="text-center">
                                <h1 class="h4 text-gray-900 mb-4">Welcome Back!</h1>
                            </div>
                            <form class="user" action="login.php" method="POST">
                                <div class="form-group">
                                    <input type="email" class="form-control form-control-user"
                                        id="exampleInputEmail" aria-describedby="emailHelp" name="email"
                                        placeholder="Enter Email" required>
                                </div>
                                <div class="form-group">
                                    <input type="password" class="form-control form-control-user" name="password"
                                        id="exampleInputPassword" placeholder="Enter Password" required>
                                </div>
                                <button class="btn btn-primary btn-user btn-block" type="submit" name="login">
                                    Login
                                </button>
                            </form>
                            <hr>
                            <div class="text-center">
                                <a class="small" href="register.php">Create an Account!</a>
                            </div>
                        </div>
                    </div>
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

</body>
</html>