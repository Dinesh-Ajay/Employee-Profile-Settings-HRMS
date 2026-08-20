/**
 * assets/js/profile.js
 * ------------------------------------------------------------
 * Client-side validation for the Employee Profile form.
 *
 * IMPORTANT: This is a convenience layer only. It gives the
 * user instant feedback so they don't have to wait for a page
 * reload to find a typo. It is NOT a substitute for the
 * server-side validation in update_profile.php - a user could
 * disable JavaScript entirely and bypass all of this, which is
 * exactly why PHP validates everything again on the server.
 * ------------------------------------------------------------
 */

$(function () {

    var PHONE_REGEX = /^[0-9]{10}$/;
    var MAX_BIO_LENGTH = 500;

    var $form = $('#profileForm');
    var $bio = $('#bio');
    var $bioCounter = $('#bioCounter');

    /**
     * updateBioCounter()
     * Updates the "x / 500 characters" label under the bio box
     * and turns it red once the limit is reached.
     */
    function updateBioCounter() {
        var length = $bio.val().length;
        $bioCounter.text(length + ' / ' + MAX_BIO_LENGTH + ' characters');

        if (length >= MAX_BIO_LENGTH) {
            $bioCounter.addClass('text-danger');
        } else {
            $bioCounter.removeClass('text-danger');
        }
    }

    // Run once on page load (in case the field is pre-filled),
    // then again every time the user types.
    updateBioCounter();
    $bio.on('input', updateBioCounter);

    /**
     * validatePhoneField($field)
     * Validates a single phone-style input (used for both the
     * main phone field and the emergency contact phone field).
     * Phone fields are OPTIONAL, so an empty value is valid.
     * Returns true if the field is valid, false otherwise.
     */
    function validatePhoneField($field) {
        var value = $field.val().trim();
        var $feedback = $field.siblings('.invalid-feedback');

        // Empty is allowed because the database column is nullable.
        if (value === '') {
            $field.removeClass('is-invalid');
            return true;
        }

        if (/[^0-9]/.test(value)) {
            $feedback.text('Phone number must contain only digits.');
            $field.addClass('is-invalid');
            return false;
        }

        if (!PHONE_REGEX.test(value)) {
            $feedback.text('Phone number must contain exactly 10 digits.');
            $field.addClass('is-invalid');
            return false;
        }

        $field.removeClass('is-invalid');
        return true;
    }

    /**
     * validateRequiredField($field, message)
     * Validates that a required text field is not empty.
     */
    function validateRequiredField($field, message) {
        var value = $field.val().trim();

        if (value === '') {
            $field.siblings('.invalid-feedback').text(message);
            $field.addClass('is-invalid');
            return false;
        }

        $field.removeClass('is-invalid');
        return true;
    }

    /**
     * validateBioField()
     * Validates that the bio does not exceed the max length.
     * (The "maxlength" HTML attribute already prevents typing
     * past 500 characters, but we double-check here too.)
     */
    function validateBioField() {
        var length = $bio.val().length;

        if (length > MAX_BIO_LENGTH) {
            $bio.addClass('is-invalid');
            return false;
        }

        $bio.removeClass('is-invalid');
        return true;
    }

    // Validate phone fields as the user types, so they get
    // immediate feedback instead of waiting until submit.
    $('#phone').on('input', function () {
        validatePhoneField($(this));
    });

    $('#emergency_contact_phone').on('input', function () {
        validatePhoneField($(this));
    });

    /**
     * Form submit handler.
     * Runs every validation rule and blocks submission if
     * anything fails.
     */
    $form.on('submit', function (event) {
        var isValid = true;

        if (!validateRequiredField($('#first_name'), 'First name is required.')) {
            isValid = false;
        }

        if (!validateRequiredField($('#last_name'), 'Last name is required.')) {
            isValid = false;
        }

        if (!validatePhoneField($('#phone'))) {
            isValid = false;
        }

        if (!validatePhoneField($('#emergency_contact_phone'))) {
            isValid = false;
        }

        if (!validateBioField()) {
            isValid = false;
        }

        if (!isValid) {
            event.preventDefault();
        }
    });

});
