<?php
$backgroundImage1 = 'https://wallpapers.com/images/hd/pure-white-1080-x-1920-background-69l155kugkiyejy6.jpg';



$alerts = [];
$selectPassenger = $con->query("SELECT * FROM tbl_passenger_record");

while ($passvess = $selectPassenger->fetch_assoc()) {
    if (strpos($passvess['EstDateNextDD'], '0000') === 0) {
        continue;
    }
    $expDateLoadline = new DateTime($passvess['ExpDateLoadline']);
    $estDateNextDD = new DateTime($passvess['EstDateNextDD']);
    $currentDate = new DateTime();

    
    // Calculate the difference
    $interval = $expDateLoadline->diff($estDateNextDD);
    $years = $interval->y;
    $months = $interval->m;
    $days = $interval->d;

    // Calculate the difference between current date and EstDateNextDD
    $diffToEstDateNextDD = $currentDate->diff($estDateNextDD);
    $daysToEstDateNextDD = $diffToEstDateNextDD->days;

    // Define a flag for closeness to drydock date
    $closeToDD = $daysToEstDateNextDD <= 60; // 2 months close to EstDateNextDD
    $oneYearBeforeDD = $daysToEstDateNextDD >= 365;


    $message = '';
    $href = '';
    $iconColor = '';

    // Check if the difference is close to 2 years, exactly 2 years, or within those 2 years
    if (($years == 2 || $years < 2 && $closeToDD || $years < 2 || $oneYearBeforeDD ) ) {
        $message = "exceed the limit of Expiration Date";
        $href = "tbl_renew.php?id=" . htmlspecialchars($passvess['id']);
        $iconColor = 'bg-danger'; // Red color for urgent notifications
    } elseif ($closeToDD) {
        $message = "needs to Drydock";
        $href = "tbl_extend.php?id=" . htmlspecialchars($passvess['id']);
        $iconColor = 'bg-warning'; // Yellow color for less urgent notifications
    }

    if ($message) {
        $alerts[] = [
            'vesselName' => $passvess['Vesselname'],
            'expDateLoadline' => $expDateLoadline->format('F j, Y'),
            'estDateNextDD' => $estDateNextDD->format('F j, Y'),
            'type' => 'passenger',
            'iconColor' => $iconColor,
            'id' => $passvess['id'],
            'href' => $href,
            'message' => $message
        ];
    }
}

$selectCargo = $con->query("SELECT * FROM tbl_cargo_record");

while ($cargovess = $selectCargo->fetch_assoc()) {
   
    $expDateLoadline = new DateTime($cargovess['ExpDateLoadline']);
    $estDateNextDD = new DateTime($cargovess['EstDateNextDD']);
    $currentDate = new DateTime();

    
    // Calculate the difference
    $interval = $expDateLoadline->diff($estDateNextDD);
    $years = $interval->y;
    $months = $interval->m;
    $days = $interval->d;

    // Calculate the difference between current date and EstDateNextDD
    $diffToEstDateNextDD = $currentDate->diff($estDateNextDD);
    $daysToEstDateNextDD = $diffToEstDateNextDD->days;

    // Define a flag for closeness to drydock date
    $closeToDD = $daysToEstDateNextDD <= 60; // 2 months close to EstDateNextDD
    $oneYearBeforeDD = $daysToEstDateNextDD >= 365;
    if (strpos($cargovess['EstDateNextDD'], '0000') === 0 || $closeToDD) {
        continue;
    }

    $message = '';
    $href = '';
    $iconColor = '';

    // Check if the difference is close to 2 years, exactly 2 years, or within those 2 years
    if (($years == 2  || $years < 2 && $closeToDD || $years < 2  || $oneYearBeforeDD ) ) {
        $message = "exceed the limit of Expiration Date";
        $href = "tbl_renew.php?rid=" . htmlspecialchars($cargovess['rid']);
        $iconColor = 'bg-danger'; // Red color for urgent notifications
    } elseif ($closeToDD) {
        $message = "needs to Drydock";
        $href = "tbl_extend.php?rid=" . htmlspecialchars($cargovess['rid']);
        $iconColor = 'bg-warning'; // Yellow color for less urgent notifications
    }

    if ($message) {
        $alerts[] = [
            'vesselName' => $cargovess['Vesselname'],
            'expDateLoadline' => $expDateLoadline->format('F j, Y'),
            'estDateNextDD' => $estDateNextDD->format('F j, Y'),
            'type' => 'cargo',
            'iconColor' => $iconColor,
            'id' => $cargovess['rid'],
            'href' => $href,
            'message' => $message
        ];
    }
}


// Fetch data from the tbl_cargo_record
// $selectCargo = $con->query("SELECT * FROM tbl_cargo_record");
// while ($cargovess = $selectCargo->fetch_assoc()) {
//     $expDateLoadline = new DateTime($cargovess['ExpDateLoadline']);
//     $estDateNextDD = new DateTime($cargovess['EstDateNextDD']);
    
//     // Calculate the difference
//     $interval = $expDateLoadline->diff($estDateNextDD);
//     $years = $interval->y;
//     $months = $interval->m;
//     $days = $interval->d;

//     // Check if the difference is close to 2 years, exactly 2 years, or within those 2 years
//     if (($years == 2 )|| $years < 2) {
        
//         // Determine icon color based on proximity to current date
//         $iconColor = '';
//         $today = new DateTime();
//         $diffEstToExp = $estDateNextDD->diff($today);

//         if ($diffEstToExp->days <= 60) {
//             $iconColor = 'bg-danger'; // Red for close to or past expiry
//             $href = "tbl_renew.php?rid=" . htmlspecialchars($cargovess['rid']);
//         } elseif ($years == 2 && $months == 0 && $days >= 335) {
//             $iconColor = 'bg-danger'; // Red for exactly 2 years
//             $href = "tbl_renew.php?rid=" . htmlspecialchars($cargovess['rid']);
//         } elseif ($diffEstToExp->days <= 365) {
//             $iconColor = 'bg-primary'; // Blue for within 1 year
//             $href = "tbl_extend.php?rid=" . htmlspecialchars($cargovess['rid']);
//         } else {
//             $iconColor = 'bg-primary'; // Default color if none of the above conditions met
//             $href = "tbl_extend.php?rid=" . htmlspecialchars($cargovess['rid']);
//         }

//         $alerts[] = [
//             'vesselName' => $cargovess['Vesselname'],
//             'placeLastDD' => $cargovess['PlaceLastDD'],
//             'expDateLoadline' => $expDateLoadline->format('F j, Y'), // Changed date format
//             'estDateNextDD' => $estDateNextDD->format('F j, Y'), // Changed date format
//             'type' => 'cargo',
//             'iconColor' => $iconColor,
//             'rid' => $cargovess['rid'],
//             'href' => $href // Add href to the alert
//         ];
//     }
// } 
?>
<!-- Topbar -->
<nav class="navbar navbar-expand navbar-light bg-black topbar mb-4 static-top shadow">
    <style>
        .navbar {
            position: relative;
            padding: 30px;
            z-index: 1;
        }
        .navbar::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: url('<?php echo $backgroundImage1; ?>');
            
            background-size: 110%; /* Adjust the value to zoom out the image */
            background-position: center;
            background-repeat: no-repeat; /* Ensure the image doesn't repeat */
            opacity: 0.7; /* Adjust the opacity value to your preference */
            z-index: -1;
        }
        .navbar img {
            height: 50px; /* Set the height to 50px */
            width: auto; /* Maintains the aspect ratio */
        }
        .navbar-text {
            font-size: 40px;
            text-align: left;
            color: white; /* Adjust text color if needed */
            margin-left: 8px; /* Adds some space between logo and text */
        }
        .dropdown-menu.scrollable-menu {
        max-height: 300px;
        overflow-y: auto;
    }
    </style>
    <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/2/20/Aleson.svg/512px-Aleson.svg.png?20160828195738" alt="Aleson Logo">
    
    <!-- Center Text mx-auto- Aleson Shipping Lines, Inc. -->
    <div class="navbar-text" id="aleson-text" style="font-size: 40px; text-align: center; color: #0000FF;">
        <?php echo "Aleson Shipping Lines, Inc."; ?>
    </div>
    <!-- Topbar Navbar -->
    <ul class="navbar-nav ml-auto">

        <!-- Nav Item - Alerts -->
        <li class="nav-item dropdown no-arrow mx-1">
            <a class="nav-link dropdown-toggle" href="#" id="alertsDropdown" role="button"
                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fas fa-bell fa-fw"></i>
                <!-- Counter - Alerts -->
                <span class="badge badge-danger badge-counter"><?php echo count($alerts); ?></span>
            </a>
            <!-- Dropdown - Alerts -->
            <div class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in scrollable-menu"
                aria-labelledby="alertsDropdown">
                <h6 class="dropdown-header">
                    Notifications
                </h6>
                <?php foreach ($alerts as $alert): ?>
                    <a href="<?php echo htmlspecialchars($alert['href']); ?>" class="dropdown-item d-flex align-items-center">
                        <div class="mr-3">
                            <div class="icon-circle <?php echo htmlspecialchars($alert['iconColor']); ?>">
                                <?php if ($alert['type'] == 'passenger'): ?>
                                    <i class="fas fa-ship text-white"></i>
                                <?php else: ?>
                                    <i class="fas fa-box text-white"></i>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div>
                            <div class="small text-gray-500">
                                <?php echo htmlspecialchars($alert['expDateLoadline']); ?>
                            </div>
                            <span class="font-weight-bold">
                                <b class="text-primary"><?php echo htmlspecialchars($alert['vesselName']); ?></b> <span><?php echo $alert['message']; ?></span> <b><?php echo htmlspecialchars($alert['estDateNextDD']); ?></b>!
                            </span>
                        </div>
                    </a>
                <?php endforeach; ?>
                
            </div>
        </li>

        <!-- Nav Item - Messages -->
        
        <!-- Nav Item - User Information -->
        <li class="nav-item dropdown no-arrow">
            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <img class="img-profile rounded-circle"
                    src="img/undraw_profile.svg">
            </a>
            <!-- Dropdown - User Information -->
            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                aria-labelledby="userDropdown">
                <a class="dropdown-item" href="#">
                    <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                    Profile
                </a>
                <a class="dropdown-item" href="#">
                    <i class="fas fa-cogs fa-sm fa-fw mr-2 text-gray-400"></i>
                    Settings
                </a>
                <a class="dropdown-item" href="#">
                    <i class="fas fa-list fa-sm fa-fw mr-2 text-gray-400"></i>
                    Activity Log
                </a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="#" data-toggle="modal" data-target="#logoutModal">
                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                    Logout
                </a>
            </div>
        </li>
    </ul>
</nav>
