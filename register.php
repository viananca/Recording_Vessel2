<?php
include('dbconn.php');


if (isset($_POST['register'])) {
    // Retrieve form data
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $user_status = $_POST['user_status'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $conpass = $_POST['conpass'];
  // Define the default image file path
  
    $pass_enc = md5($password);
  
    // Check if the email is already taken
    $check_email = $con->query("SELECT * FROM users WHERE email = '$email'");
  
    if ($check_email->num_rows > 0) {
        echo '<script>alert("The email is already taken")</script>';
    } else {
      if ($password == $conpass) {
  
        $register = $con->query("INSERT INTO users (firstname, lastname, user_status, email, password) VALUES ('$firstname','$lastname','0','$email','$pass_enc')");
  
        if ($register) {
          echo '<script>alert("Registration Successful")</script>';
          header("Location: index.php");
        } else {
          echo '<script>alert("Registration Failed")</script>';
        }
      } else {
        echo '<script>alert("Password Mismatch")</script>';
      }
    }
  }
?>
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>SB Admin 2 - Register</title>

    <!-- Custom fonts for this template-->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="css/sb-admin-2.min.css" rel="stylesheet">

</head>

<body class="bg-gradient-primary">

    <div class="container-md">

        <div class="card o-hidden border-0 shadow-lg my-5">
            <div class="card-body p-0">
                <!-- Nested Row within Card Body -->
                <div class="p-5">
                    <div class="text-center">
                        <h1 class="h4 text-gray-900 mb-4">Create an Account!</h1>
                    </div>
                    <form class="user" method="post" action="register.php" id="manage_user"> 
                        <input type="hidden" name="user_status">
                        <div class="form-group row">
                            <div class="col-sm-6 mb-3 mb-sm-0">
                                <input type="text" class="form-control form-control-user" name="firstname" id="exampleFirstName"
                                    placeholder="First Name" required>
                            </div>
                            <div class="col-sm-6">
                                <input type="text" class="form-control form-control-user" name="lastname" id="exampleLastName"
                                    placeholder="Last Name" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <input type="email" class="form-control form-control-user" name="email" id="exampleInputEmail"
                                placeholder="Email Address" required>
                        </div>
                        <div class="form-group row">
                            <div class="col-sm-6 mb-3 mb-sm-0">
                                <input type="password" class="form-control form-control-user" name="password"
                                    id="exampleInputPassword" placeholder="Password" required>
                            </div>
                            <div class="col-sm-6">
                                <input type="password" class="form-control form-control-user" name="conpass"
                                    id="exampleRepeatPassword" placeholder="Repeat Password" required>
                            </div>
                        </div>
                        <button class="btn btn-primary btn-user btn-block" type="submit" name="register">
                            Register Account
                        </button>
                    </form>
                    <hr>
                    <div class="text-center">
                        <a class="small" href="index.php">Already have an account? Login!</a>
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
    <script>
  document.getElementById('manage_user').addEventListener('submit', function(e) {
    var password = document.getElementById('password').value;
    var conpass = document.getElementById('conpass').value;


    if (password.length < 8 || password.length > 9) {
      e.preventDefault();
      document.getElementById('passwordMsg').innerHTML = 'Password must be 8 to 9 characters long';
    } else {
      document.getElementById('passwordMsg').innerHTML = '';
    }

    if (password !== conpass) {
      e.preventDefault();
      alert('Password and Confirm Password do not match');
    }
  });

  document.getElementById('password').addEventListener('input', function() {
    var password = this.value;
    var passwordMsg = document.getElementById('passwordMsg');

    if (password.length < 8 || password.length > 9) {
      passwordMsg.innerHTML = 'Password must be 8 to 9 characters long';
    } else {
      passwordMsg.innerHTML = '';
    }
  });
</script>
</body>

</html>