<?php
/**
 * update_profile.php
 * ------------------------------------------------------------
 * Processes the profile form submitted from profile.php.
 * Follows the Post/Redirect/Get (PRG) pattern: after handling
 * the POST request, it always redirects back to profile.php
 * so refreshing the page never resubmits the form.
 * ------------------------------------------------------------
 */

session_start();

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';

// 1. Must be logged in.
requireLogin();

// 2. Must be a POST request. A GET request to this file should
//    never update anything.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('profile.php');
}

// 3. The employee ID comes ONLY from the session, never from
//    the form. This stops a logged-in user from editing
//    someone else's profile by tampering with hidden fields.
$userId = $_SESSION['user_id'];

// 4. Read the raw submitted values. We default to '' with the
//    null coalescing operator (??) in case a field is missing.
$rawFirstName       = $_POST['first_name'] ?? '';
$rawLastName        = $_POST['last_name'] ?? '';
$rawPhone           = $_POST['phone'] ?? '';
$rawEmergencyName   = $_POST['emergency_contact_name'] ?? '';
$rawEmergencyPhone  = $_POST['emergency_contact_phone'] ?? '';
$rawBio             = $_POST['bio'] ?? '';

// 5. Trim and sanitize every field before validating it.
$data = [
    'first_name'              => sanitizeInput($rawFirstName),
    'last_name'                => sanitizeInput($rawLastName),
    'phone'                    => sanitizePhone($rawPhone),
    'emergency_contact_name'   => sanitizeInput($rawEmergencyName),
    'emergency_contact_phone'  => sanitizePhone($rawEmergencyPhone),
    'bio'                      => sanitizeInput($rawBio),
];

// 6. Validate the cleaned data.
$errors = validateProfileData($data);

// 7. If validation failed, save the errors + the submitted
//    values in the session and send the user back to the form.
if (!empty($errors)) {
    $_SESSION['validation_errors'] = $errors;
    $_SESSION['old_input'] = $data;
    setFlashMessage('danger', 'Profile update failed. Please check the form for errors.');
    redirect('profile.php');
}

// 8. Validation passed - attempt the database update using a
//    prepared statement (see updateEmployeeProfile()).
$updateSucceeded = updateEmployeeProfile($conn, $userId, $data);

if ($updateSucceeded) {
    setFlashMessage('success', 'Profile updated successfully.');
} else {
    // We never show the raw SQL error to the user - just a
    // generic, friendly message. The real error was already
    // logged inside updateEmployeeProfile().
    setFlashMessage('danger', 'Something went wrong while updating your profile. Please try again.');
}

// 9. Always redirect back to profile.php (Post/Redirect/Get).
redirect('profile.php');
