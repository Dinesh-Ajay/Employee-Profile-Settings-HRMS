<?php
/**
 * logout.php
 * ------------------------------------------------------------
 * Destroys the current session and sends the user back to the
 * login page.
 * ------------------------------------------------------------
 */

session_start();

// Clear all session variables.
$_SESSION = [];

// Destroy the session itself.
session_destroy();

// Send the user back to the login page.
header("Location: login.php");
exit;
