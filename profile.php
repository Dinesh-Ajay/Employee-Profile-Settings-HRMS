<?php
/**
 * profile.php
 * ------------------------------------------------------------
 * Shows the logged-in employee's profile form, pre-filled with
 * their current data from the database. Submitting the form
 * sends a POST request to update_profile.php.
 * ------------------------------------------------------------
 */

session_start();

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';

// Block anyone who is not logged in.
requireLogin();

// $_SESSION['user_id'] is the ONLY source we trust for "who is this?".
// We never take the employee ID from $_GET or $_POST for this page.
$userId = $_SESSION['user_id'];

// Fetch this employee's current data.
$employee = getEmployeeById($conn, $userId);

// If, for some reason, the session points to an employee that
// no longer exists, don't continue as though everything is fine.
if (!$employee) {
    session_destroy();
    die('Your employee record could not be found. Please contact HR. <a href="login.php">Back to login</a>');
}

// Grab any one-time flash message left by update_profile.php.
$flash = getFlashMessage();

// If the last submission failed validation, update_profile.php
// stored the field errors and the old input here so we can
// show them again instead of losing what the user typed.
$validationErrors = $_SESSION['validation_errors'] ?? [];
$oldInput = $_SESSION['old_input'] ?? [];
unset($_SESSION['validation_errors'], $_SESSION['old_input']);

// Helper: use the resubmitted old value if it exists (after a
// failed validation), otherwise fall back to the saved database value.
function oldOrDb($oldInput, $employee, $field)
{
    return $oldInput[$field] ?? $employee[$field] ?? '';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Profile Settings</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

    <!-- ==================== HEADER ==================== -->
    <header class="profile-header">
        <div class="container d-flex justify-content-between align-items-center flex-wrap py-3">
            <div>
                <h1 class="h5 mb-0">HRMS Employee Profile</h1>
                <small class="text-white-50">
                    Logged in as <?= e($employee['first_name'] . ' ' . $employee['last_name']) ?>
                </small>
            </div>
            <a href="logout.php" class="btn btn-outline-light btn-sm">Logout</a>
        </div>
    </header>

    <!-- ==================== MAIN CONTENT ==================== -->
    <main class="container py-4">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-8">

                <?php if ($flash): ?>
                    <div class="alert alert-<?= e($flash['type']) ?> alert-dismissible fade show" role="alert">
                        <?= e($flash['message']) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <?php if (!empty($validationErrors)): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        Please fix the errors below and try again.
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <div class="card shadow-sm">
                    <div class="card-body p-4">

                        <h2 class="h5 card-title mb-1">Employee Profile Settings</h2>
                        <p class="text-muted small mb-4">
                            Update your personal and emergency contact information.
                        </p>

                        <form id="profileForm" method="POST" action="update_profile.php" novalidate>

                            <div class="row g-3">

                                <!-- First Name -->
                                <div class="col-md-6">
                                    <label for="first_name" class="form-label">First Name</label>
                                    <input
                                        type="text"
                                        class="form-control <?= isset($validationErrors['first_name']) ? 'is-invalid' : '' ?>"
                                        id="first_name"
                                        name="first_name"
                                        placeholder="e.g. Ajay"
                                        maxlength="50"
                                        value="<?= e(oldOrDb($oldInput, $employee, 'first_name')) ?>"
                                        required
                                    >
                                    <div class="invalid-feedback">
                                        <?= e($validationErrors['first_name'] ?? 'First name is required.') ?>
                                    </div>
                                </div>

                                <!-- Last Name -->
                                <div class="col-md-6">
                                    <label for="last_name" class="form-label">Last Name</label>
                                    <input
                                        type="text"
                                        class="form-control <?= isset($validationErrors['last_name']) ? 'is-invalid' : '' ?>"
                                        id="last_name"
                                        name="last_name"
                                        placeholder="e.g. Sharma"
                                        maxlength="50"
                                        value="<?= e(oldOrDb($oldInput, $employee, 'last_name')) ?>"
                                        required
                                    >
                                    <div class="invalid-feedback">
                                        <?= e($validationErrors['last_name'] ?? 'Last name is required.') ?>
                                    </div>
                                </div>

                                <!-- Phone Number -->
                                <div class="col-md-6">
                                    <label for="phone" class="form-label">Phone Number</label>
                                    <input
                                        type="text"
                                        class="form-control <?= isset($validationErrors['phone']) ? 'is-invalid' : '' ?>"
                                        id="phone"
                                        name="phone"
                                        placeholder="10-digit phone number"
                                        maxlength="10"
                                        inputmode="numeric"
                                        value="<?= e(oldOrDb($oldInput, $employee, 'phone')) ?>"
                                    >
                                    <div class="form-text">Optional. Digits only, exactly 10 digits if entered.</div>
                                    <div class="invalid-feedback">
                                        <?= e($validationErrors['phone'] ?? 'Phone number must contain exactly 10 digits.') ?>
                                    </div>
                                </div>

                                <!-- Emergency Contact Name -->
                                <div class="col-md-6">
                                    <label for="emergency_contact_name" class="form-label">Emergency Contact Name</label>
                                    <input
                                        type="text"
                                        class="form-control <?= isset($validationErrors['emergency_contact_name']) ? 'is-invalid' : '' ?>"
                                        id="emergency_contact_name"
                                        name="emergency_contact_name"
                                        placeholder="e.g. Sunita Sharma"
                                        maxlength="100"
                                        value="<?= e(oldOrDb($oldInput, $employee, 'emergency_contact_name')) ?>"
                                    >
                                    <div class="invalid-feedback">
                                        <?= e($validationErrors['emergency_contact_name'] ?? 'Emergency contact name is too long.') ?>
                                    </div>
                                </div>

                                <!-- Emergency Contact Phone -->
                                <div class="col-md-6">
                                    <label for="emergency_contact_phone" class="form-label">Emergency Contact Phone</label>
                                    <input
                                        type="text"
                                        class="form-control <?= isset($validationErrors['emergency_contact_phone']) ? 'is-invalid' : '' ?>"
                                        id="emergency_contact_phone"
                                        name="emergency_contact_phone"
                                        placeholder="10-digit phone number"
                                        maxlength="10"
                                        inputmode="numeric"
                                        value="<?= e(oldOrDb($oldInput, $employee, 'emergency_contact_phone')) ?>"
                                    >
                                    <div class="form-text">Optional. Digits only, exactly 10 digits if entered.</div>
                                    <div class="invalid-feedback">
                                        <?= e($validationErrors['emergency_contact_phone'] ?? 'Emergency contact phone must contain exactly 10 digits.') ?>
                                    </div>
                                </div>

                                <!-- Bio -->
                                <div class="col-12">
                                    <label for="bio" class="form-label">Bio</label>
                                    <textarea
                                        class="form-control <?= isset($validationErrors['bio']) ? 'is-invalid' : '' ?>"
                                        id="bio"
                                        name="bio"
                                        rows="4"
                                        maxlength="500"
                                        placeholder="A short description about yourself..."
                                    ><?= e(oldOrDb($oldInput, $employee, 'bio')) ?></textarea>
                                    <div class="d-flex justify-content-between">
                                        <div class="invalid-feedback mb-0">
                                            <?= e($validationErrors['bio'] ?? 'Bio must be 500 characters or fewer.') ?>
                                        </div>
                                        <small id="bioCounter" class="text-muted ms-auto">0 / 500 characters</small>
                                    </div>
                                </div>

                            </div>

                            <div class="mt-4 text-end">
                                <button type="submit" class="btn btn-primary">Save Changes</button>
                            </div>

                        </form>

                    </div>
                </div>

            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="assets/js/profile.js"></script>
</body>
</html>
