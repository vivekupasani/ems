<?php
session_start();

// Unset all session variables
$_SESSION = array();

// Destroy session
session_destroy();

// Prevent caching of logout response
header("Expires: Tue, 01 Jan 2000 00:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

echo json_encode(['success' => true]);
header("Location: default.php");
exit();
?>