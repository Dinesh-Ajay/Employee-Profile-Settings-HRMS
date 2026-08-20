-- ============================================================
-- EMPLOYEE PROFILE SETTINGS PAGE - DATABASE SETUP
-- ============================================================
-- This script creates the database, the employees table,
-- and inserts sample employee records for testing.
--
-- HOW TO USE:
-- 1. Open phpMyAdmin (or the MySQL command line).
-- 2. Import this file, or copy/paste its contents and run it.
-- ============================================================

-- 1. Create the database (only if it does not already exist)
CREATE DATABASE IF NOT EXISTS employee_hrms;

-- 2. Select the database so the statements below run inside it
USE employee_hrms;

-- 3. Create the employees table
CREATE TABLE IF NOT EXISTS employees (
    id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    phone VARCHAR(20) NULL,
    emergency_contact_name VARCHAR(100) NULL,
    emergency_contact_phone VARCHAR(20) NULL,
    bio TEXT NULL,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- 4. Insert sample employees so the demo login has real records to use
INSERT INTO employees
    (first_name, last_name, phone, emergency_contact_name, emergency_contact_phone, bio)
VALUES
    (
        'Ajay',
        'Sharma',
        '9876543210',
        'Sunita Sharma',
        '9876500000',
        'Backend developer who enjoys solving database performance problems.'
    ),
    (
        'Rahul',
        'Verma',
        '9123456780',
        'Meena Verma',
        '9123400000',
        'Frontend developer focused on building clean, accessible interfaces.'
    ),
    (
        'Priya',
        'Nair',
        '9988776655',
        'Ravi Nair',
        '9988700000',
        'HR coordinator who manages onboarding and employee records.'
    );
