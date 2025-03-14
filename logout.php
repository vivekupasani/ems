<?php
// logout.php
session_start();

// Destroy the session immediately
session_unset();
session_destroy();

// Redirect to default.php
header("Location: default.php");
exit();
?>