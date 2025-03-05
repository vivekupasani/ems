<?php
require_once 'auth_middleware.php';
check_auth();

// Now you can safely display the dashboard content
echo "Welcome " . $_SESSION['name'] . "! You are logged in as " . $_SESSION['role'];
?>