<?php
/**
 * includes/functions.php
 * ------------------------------------------------------------
 * Reusable helper functions used across the project.
 * Keeping these in one place means we write each piece of
 * logic (login check, sanitizing, validation, etc.) only once
 * and reuse it everywhere it's needed.
 * ------------------------------------------------------------
 */

/**
 * isLoggedIn()
 * Checks whether $_SESSION['user_id'] has been set.
 * Returns true if the user is logged in, false otherwise.
 */
function isLoggedIn()
{
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * requireLogin()
 * If the user is NOT logged in, send them to login.php and
 * stop the script with exit so no further code runs.
 */
function requireLogin()
{
    if (!isLoggedIn()) {
        redirect('login.php');
    }
}

/**
 * redirect($url)
 * A small wrapper around header("Location: ...") so we don't
 * repeat the same two lines everywhere.
 */
function redirect($url)
{
    header("Location: $url");
    exit;
}

/**
 * e($value)
 * Short helper around htmlspecialchars().
 * Use this every time a database value is printed inside HTML,
 * so special characters like < > " ' can't break the page or
 * be used for a cross-site scripting (XSS) attack.
 */
function e($value)
{
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

/**
 * sanitizeInput($value)
 * General-purpose text cleanup for simple text fields.
 * - trim() removes extra spaces from the start/end.
 * - filter_var() with FILTER_UNSAFE_RAW + FILTER_FLAG_STRIP_LOW
 *   strips control characters (like stray line breaks) while
 *   leaving normal punctuation and letters alone.
 * We do NOT use FILTER_SANITIZE_STRING because it is deprecated
 * as of PHP 8.1.
 */
function sanitizeInput($value)
{
    $value = trim($value ?? '');
    $value = filter_var($value, FILTER_UNSAFE_RAW, FILTER_FLAG_STRIP_LOW);
    return $value;
}

/**
 * sanitizePhone($phone)
 * Cleans a phone number so only digits remain.
 * Example: " 98765-43210 " becomes "9876543210".
 * This does NOT validate length - validateProfileData() does
 * that. This function only cleans the value.
 */
function sanitizePhone($phone)
{
    $phone = trim($phone ?? '');
    // Keep digits only using a regular expression.
    $phone = preg_replace('/[^0-9]/', '', $phone);
    return $phone;
}

/**
 * validateProfileData($data)
 * Validates the profile form fields and returns an associative
 * array of error messages. An EMPTY array means "no errors".
 *
 * $data is expected to be an associative array with keys:
 * first_name, last_name, phone, emergency_contact_name,
 * emergency_contact_phone, bio
 */
function validateProfileData($data)
{
    $errors = [];

    // ---- First name: required, max 50 chars, letters/spaces only ----
    $firstName = $data['first_name'] ?? '';
    if (empty($firstName)) {
        $errors['first_name'] = 'First name is required.';
    } elseif (strlen($firstName) > 50) {
        $errors['first_name'] = 'First name must be 50 characters or fewer.';
    } elseif (!preg_match('/^[a-zA-Z ]+$/', $firstName)) {
        $errors['first_name'] = 'First name can only contain letters and spaces.';
    }

    // ---- Last name: required, max 50 chars, letters/spaces only ----
    $lastName = $data['last_name'] ?? '';
    if (empty($lastName)) {
        $errors['last_name'] = 'Last name is required.';
    } elseif (strlen($lastName) > 50) {
        $errors['last_name'] = 'Last name must be 50 characters or fewer.';
    } elseif (!preg_match('/^[a-zA-Z ]+$/', $lastName)) {
        $errors['last_name'] = 'Last name can only contain letters and spaces.';
    }

    // ---- Phone: optional, but if entered must be exactly 10 digits ----
    $phone = $data['phone'] ?? '';
    if (!empty($phone)) {
        if (!filter_var($phone, FILTER_VALIDATE_REGEXP, [
            'options' => ['regexp' => '/^[0-9]{10}$/']
        ])) {
            $errors['phone'] = 'Phone number must contain exactly 10 digits.';
        }
    }

    // ---- Emergency contact name: optional, max 100 chars ----
    $emergencyName = $data['emergency_contact_name'] ?? '';
    if (!empty($emergencyName) && strlen($emergencyName) > 100) {
        $errors['emergency_contact_name'] = 'Emergency contact name must be 100 characters or fewer.';
    }

    // ---- Emergency contact phone: optional, if entered must be 10 digits ----
    $emergencyPhone = $data['emergency_contact_phone'] ?? '';
    if (!empty($emergencyPhone)) {
        if (!filter_var($emergencyPhone, FILTER_VALIDATE_REGEXP, [
            'options' => ['regexp' => '/^[0-9]{10}$/']
        ])) {
            $errors['emergency_contact_phone'] = 'Emergency contact phone must contain exactly 10 digits.';
        }
    }

    // ---- Bio: optional, max 500 chars ----
    $bio = $data['bio'] ?? '';
    if (!empty($bio) && strlen($bio) > 500) {
        $errors['bio'] = 'Bio must be 500 characters or fewer.';
    }

    return $errors;
}

/**
 * getEmployeeById($conn, $employeeId)
 * Fetches a single employee row from the database using a
 * prepared statement (so the employee ID can never be used
 * for SQL injection).
 *
 * Returns an associative array of the employee's data, or
 * null if no employee with that ID exists.
 */
function getEmployeeById($conn, $employeeId)
{
    // The "?" is a placeholder. The real value is bound safely
    // below with bind_param(), never concatenated into the string.
    $sql = "SELECT * FROM employees WHERE id = ?";

    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        error_log("Prepare failed (getEmployeeById): " . $conn->error);
        return null;
    }

    // "i" means the bound value is an integer.
    $stmt->bind_param("i", $employeeId);
    $stmt->execute();

    $result = $stmt->get_result();
    $employee = $result->fetch_assoc(); // returns null automatically if no row found

    $stmt->close();

    return $employee ?: null;
}

/**
 * updateEmployeeProfile($conn, $employeeId, $data)
 * Updates an employee's editable fields using a prepared
 * UPDATE statement. Returns true on success, false on failure.
 */
function updateEmployeeProfile($conn, $employeeId, $data)
{
    $sql = "UPDATE employees
            SET first_name = ?,
                last_name = ?,
                phone = ?,
                emergency_contact_name = ?,
                emergency_contact_phone = ?,
                bio = ?
            WHERE id = ?";

    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        error_log("Prepare failed (updateEmployeeProfile): " . $conn->error);
        return false;
    }

    // "ssssssi" = six strings followed by one integer,
    // matching the six SET columns plus the WHERE id.
    $stmt->bind_param(
        "ssssssi",
        $data['first_name'],
        $data['last_name'],
        $data['phone'],
        $data['emergency_contact_name'],
        $data['emergency_contact_phone'],
        $data['bio'],
        $employeeId
    );

    $success = $stmt->execute();

    if (!$success) {
        error_log("Execute failed (updateEmployeeProfile): " . $stmt->error);
    }

    $stmt->close();

    return $success;
}

/**
 * setFlashMessage($type, $message)
 * Stores a one-time message in the session so it can be shown
 * on the NEXT page load (used after a redirect).
 * $type is usually 'success' or 'danger' (Bootstrap alert types).
 */
function setFlashMessage($type, $message)
{
    $_SESSION['flash'] = [
        'type'    => $type,
        'message' => $message,
    ];
}

/**
 * getFlashMessage()
 * Returns the current flash message (or null if none exists)
 * and removes it from the session so it only appears once.
 */
function getFlashMessage()
{
    if (!empty($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']); // remove it so it doesn't show again
        return $flash;
    }
    return null;
}
