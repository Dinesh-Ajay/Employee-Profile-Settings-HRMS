# Employee Profile Settings Page

A beginner-to-intermediate PHP and MySQL project that implements a
secure Employee Profile Settings page for an HRMS (Human Resource
Management System).

The application allows a logged-in employee to view and update personal
contact information, emergency contact information, and a short bio. It
demonstrates core PHP form handling, sessions, MySQL database
operations, validation, basic security practices, Bootstrap UI,
JavaScript/jQuery validation, and dynamic character counting.

## Features

-   Employee login using a session-based authentication flow
-   Fetches the logged-in employee using `$_SESSION['user_id']`
-   Displays existing employee information from MySQL
-   Allows employees to update:
    -   First name
    -   Last name
    -   Phone number
    -   Emergency contact name
    -   Emergency contact phone
    -   Bio
-   Uses MySQL `SELECT` and `UPDATE` operations
-   Server-side form handling with PHP
-   Input sanitization and validation using PHP functions
-   Client-side phone number validation
-   Digits-only phone validation
-   Dynamic bio character counter with a 500-character limit
-   Bootstrap-based responsive interface
-   Dismissible success messages
-   Session-based page protection
-   Logout functionality
-   Prepared database queries
-   Sample employee records for testing

## Technologies Used

### Backend

-   PHP 8.2
-   MySQL 9.1
-   MySQLi
-   PHP Sessions

### Frontend

-   HTML5
-   CSS3
-   Bootstrap
-   JavaScript
-   jQuery

### Development Environment

-   XAMPP Apache
-   phpMyAdmin
-   Visual Studio Code

## Project Structure

``` text
employee-profile/
│
├── assets/
│   ├── css/
│   │   └── style.css
│   └── js/
│       └── profile.js
│
├── config/
│   └── database.php
│
├── includes/
│   └── functions.php
│
├── database.sql
├── index.php
├── login.php
├── logout.php
├── profile.php
└── update_profile.php
```

## Database

The project uses a database named:

``` text
employee_hrms
```

The main table is:

``` text
employees
```

### Employees Table

  Column                      Type           Description
  --------------------------- -------------- -----------------------------
  `id`                        INT            Primary key, auto increment
  `first_name`                VARCHAR(50)    Employee first name
  `last_name`                 VARCHAR(50)    Employee last name
  `phone`                     VARCHAR(20)    Employee phone number
  `emergency_contact_name`    VARCHAR(100)   Emergency contact name
  `emergency_contact_phone`   VARCHAR(20)    Emergency contact phone
  `bio`                       TEXT           Short employee biography
  `updated_at`                TIMESTAMP      Last profile update time

The `database.sql` file creates the database, creates the `employees`
table, and inserts sample employee records for testing.

## Application Flow

``` text
User opens application
        ↓
index.php
        ↓
Check login session
        ↓
 ┌──────┴──────┐
 │             │
Not logged in  Logged in
 │             │
 ↓             ↓
login.php    profile.php
               ↓
       Fetch employee data
               ↓
        Display profile form
               ↓
        User edits details
               ↓
       update_profile.php
               ↓
       Validate and sanitize
               ↓
          MySQL UPDATE
               ↓
       Success message
               ↓
        profile.php
```

## Setup Instructions

### 1. Install and start XAMPP

Install XAMPP and start **Apache** from the XAMPP Control Panel.

The project can use an existing MySQL installation as long as MySQL is
running and accessible to PHP.

### 2. Place the project inside `htdocs`

Copy the project into the XAMPP web root:

``` text
XAMPP/htdocs/employee-profile
```

For example:

``` text
C:\xampp\htdocs\employee-profile
```

### 3. Create the database

Open phpMyAdmin:

``` text
http://localhost/phpmyadmin
```

Import:

``` text
database.sql
```

The SQL file creates:

``` text
employee_hrms
```

and the:

``` text
employees
```

table with sample records.

### 4. Configure the database connection

Open:

``` text
config/database.php
```

Set the MySQL connection details for your local environment:

``` php
$host = "localhost";
$username = "root";
$password = "YOUR_MYSQL_PASSWORD";
$database = "employee_hrms";
```

**Do not upload your real database password to GitHub.**

### 5. Run the application

Start Apache and open:

``` text
http://localhost/employee-profile/
```

The application redirects unauthenticated users to the login page.

## Demo Login

The project contains sample employee records for demonstration.

Use an employee ID such as:

``` text
1
```

Other sample records are available in the `employees` table.

This demo login is intended for local learning/testing and is not a
production authentication system.

## Validation

The project performs validation on employee profile inputs.

### Phone Number

Phone numbers:

-   Are optional
-   Must contain digits only when provided
-   Must contain exactly 10 digits when provided

The project includes client-side validation using JavaScript/jQuery and
server-side validation in PHP.

### Bio

The bio field:

-   Has a 500-character limit
-   Displays a live character counter
-   Updates the counter while the user types

## Security Practices Demonstrated

This project demonstrates several beginner-to-intermediate PHP security
practices:

-   Session-based authentication
-   Protected profile page
-   Server-side validation
-   Input trimming
-   Input sanitization
-   Output escaping using `htmlspecialchars()`
-   Prepared MySQLi statements
-   Avoiding direct SQL interpolation of user input
-   Controlled redirects after authentication/update operations

> This is an educational HRMS project and should be further hardened
> before being used in a real production environment.

## Testing Performed

The application was tested locally for:

-   Successful login
-   Session-based access
-   Loading employee records
-   Displaying existing profile data
-   Updating profile information
-   Successful update message
-   Invalid phone input rejection
-   Bio character counting
-   Logout
-   Login with multiple sample employees
-   Verification of updated records in MySQL/phpMyAdmin

## Learning Objectives

This project was built to practice:

-   PHP fundamentals
-   PHP sessions
-   Form handling with `$_POST`
-   Database connectivity using MySQLi
-   SQL `SELECT` and `UPDATE`
-   Prepared statements
-   Input validation and sanitization
-   Basic authentication flow
-   JavaScript/jQuery form validation
-   Bootstrap responsive forms
-   PHP project structure and separation of concerns

## Future Improvements

Possible future improvements include:

-   Real password-based authentication
-   Password hashing with `password_hash()`
-   CSRF protection
-   Role-based access control
-   Employee registration
-   Profile picture upload
-   Email/password reset
-   Admin dashboard
-   Employee search and pagination
-   Audit logs
-   Stronger production-level session configuration

## Author

**Dinesh Ajay**

Built as a PHP and MySQL learning project focused on employee profile
management and core backend development concepts.
