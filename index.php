<?php
/**
 * index.php
 * ------------------------------------------------------------
 * Entry point of the application. It doesn't display anything
 * itself - it just sends the visitor to the right place.
 * ------------------------------------------------------------
 */

session_start();

require_once __DIR__ . '/includes/functions.php';

if (isLoggedIn()) {
    redirect('profile.php');
} else {
    redirect('login.php');
}
