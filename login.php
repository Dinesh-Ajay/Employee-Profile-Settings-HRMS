<?php
/**
 * login.php
 * ------------------------------------------------------------
 * DEMONSTRATION LOGIN ONLY.
 *
 * This is NOT a real authentication system. There are no
 * passwords and no password hashing. Its only purpose is to
 * show how $_SESSION['user_id'] is set, so the rest of the
 * project (profile.php, update_profile.php) has something to
 * check against.
 *
 * In a real HRMS you would verify a username + hashed password
 * (e.g. with password_verify()) before ever setting the session.
 * ------------------------------------------------------------
 */

session_start();

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';

// If the employee is already logged in, there's no need to
// show the login form again.
if (isLoggedIn()) {
    redirect('profile.php');
}

$errorMessage = '';

// Only process the form when it has actually been submitted.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $employeeIdInput = trim($_POST['employee_id'] ?? '');

    // Validate that the ID is numeric before touching the database.
    if ($employeeIdInput === '' || !filter_var($employeeIdInput, FILTER_VALIDATE_INT)) {
        $errorMessage = 'Please enter a valid numeric Employee ID.';
    } else {
        $employeeId = (int) $employeeIdInput;

        // Check whether an employee with this ID actually exists.
        $employee = getEmployeeById($conn, $employeeId);

        if ($employee) {
            // Success: store the employee's ID in the session.
            // Every other page trusts this value to know "who is logged in".
            $_SESSION['user_id'] = $employee['id'];
            redirect('profile.php');
        } else {
            $errorMessage = 'Employee not found.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Employee HRMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="login-page">

    <div class="container">
        <div class="row justify-content-center align-items-center min-vh-100">
            <div class="col-12 col-sm-8 col-md-5 col-lg-4">

                <div class="card shadow-sm">
                    <div class="card-body p-4">

                        <h1 class="h4 mb-1 text-center">HRMS Employee Login</h1>
                        <p class="text-muted text-center small mb-4">
                            Demo login &mdash; enter a sample Employee ID (1, 2, or 3).
                        </p>

                        <?php if (!empty($errorMessage)): ?>
                            <div class="alert alert-danger" role="alert">
                                <?= e($errorMessage) ?>
                            </div>
                        <?php endif; ?>

                        <form method="POST" action="login.php" novalidate>
                            <div class="mb-3">
                                <label for="employee_id" class="form-label">Employee ID</label>
                                <input
                                    type="number"
                                    class="form-control"
                                    id="employee_id"
                                    name="employee_id"
                                    placeholder="e.g. 1"
                                    min="1"
                                    required
                                    autofocus
                                >
                            </div>

                            <button type="submit" class="btn btn-primary w-100">Login</button>
                        </form>

                        <div class="alert alert-warning mt-4 mb-0 small" role="alert">
                            <strong>Note:</strong> This is a demonstration login for learning
                            purposes only. It does not use passwords and should never be used
                            as-is in a real production system.
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </div>

</body>
</html>
