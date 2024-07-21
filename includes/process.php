<?php
// process.php

// Simulate a long process (e.g., database query, file processing, etc.)
sleep(5); // Simulating a delay for demonstration purposes

// After processing, redirect to the main content page
header('Location: main.php');
exit();
?>
