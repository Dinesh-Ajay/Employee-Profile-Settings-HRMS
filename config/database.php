<?php
/**
 * config/database.php
 * ------------------------------------------------------------
 * This file creates ONE shared MySQLi database connection
 * that every other page in the project can reuse.
 *
 * IMPORTANT: This file only sets up the connection.
 * It does not print anything or start a session, so it is
 * safe to require_once it from any page.
 * ------------------------------------------------------------
 */

// Database credentials.
// For a real production project these would NOT be hard-coded
// in a file like this - they would come from environment
// variables or a config file outside the web root. Since this
// is a learning project, we keep it simple and explicit.
$host     = "localhost";
$username = "root";
$password = "root";
$database = "employee_hrms";

// Create the MySQLi connection using the credentials above.
$conn = new mysqli($host, $username, $password, $database);

// If the connection fails, stop the script immediately.
// We show a generic message to the user and log the real
// technical error for the developer instead of exposing it.
if ($conn->connect_error) {
    error_log("Database connection failed: " . $conn->connect_error);
    die("Database connection failed. Please try again later.");
}

// Force the connection to use utf8mb4 so names, emojis, etc.
// are stored and displayed correctly.
$conn->set_charset("utf8mb4");
