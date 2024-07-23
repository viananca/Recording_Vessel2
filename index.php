
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recording Vessel</title>
    <style>
        /* Loading screen styles */
        #loading {
            position: fixed;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            background-color: #f2f2f2;
            z-index: 9999;
            display: flex;
            justify-content: center;
            align-items: center;
            overflow: hidden;
        }
        #loading video {
            position: absolute;
            top: 50%;
            left: 50%;
            width: 100%;
            height: 130%;
            object-fit: cover;
            transform: translate(-50%, -50%);
        }
    </style>
</head>
<body>

<!-- Loading Screen -->
<div id="loading">
    <video autoplay muted>
        <source src="loadingprocess.mp4" type="video/mp4">
        Your browser does not support the video tag.
    </video>
</div>

<!-- Main Content -->
<div id="content" style="display:none;">
    <?php
        // Your existing PHP content here
        // Include other PHP content or files as needed
    ?>
</div>

<script>
    // JavaScript to hide the loading screen after a specified delay and show the content
    window.addEventListener("load", function() {
        setTimeout(function() {
            document.getElementById("loading").style.display = "none";
            document.getElementById("content").style.display = "block";
            window.location.href = "login.php"; // Redirect to Login.php after 4 seconds
        }, 4000); // 4000 milliseconds = 4 seconds
    });
</script>

</body>
</html>


