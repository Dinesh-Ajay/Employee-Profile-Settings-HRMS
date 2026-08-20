<?php

session_start();

require_once __DIR__ . '/includes/functions.php';

if (isLoggedIn()) {
    redirect('profile.php');
} else {
    redirect('login.php');
}
