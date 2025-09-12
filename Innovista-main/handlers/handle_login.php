<?php
// C:\xampp1\htdocs\Innovista-final\Innovista-main\handlers\handle_login.php

require_once '../public/session.php'; // For session_start() and helper functions
require_once '../config/Database.php';
require_once '../classes/User.php';
require_once '../handlers/flash_message.php'; // For setting flash messages

// If user is already logged in, redirect to their dashboard
if (isUserLoggedIn()) {
    $userRole = getUserRole();
    if ($userRole === 'admin') {
        header("Location: ../admin/admin_dashboard.php");
    } elseif ($userRole === 'provider') {
        // Corrected path for already logged-in provider
        header("Location: ../provider/provider_dashboard.php");
    } else { // customer or unknown
        // Corrected path for already logged-in customer
        header("Location: ../customer/customer_dashboard.php");
    }
    exit();
}

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../public/login.php');
    exit();
}

$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

// Store email in session for re-filling form on error
$_SESSION['login_data']['email'] = $email;

// Basic validation
if (empty($email) || empty($password)) {
    set_flash_message('error', 'Please enter both email and password.');
    header('Location: ../public/login.php');
    exit();
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    set_flash_message('error', 'Invalid email format.');
    header('Location: ../public/login.php');
    exit();
}

// Database connection and user authentication
$database = new Database();
$conn = $database->getConnection();
$userClass = new User($conn);

$loggedInUser = $userClass->login($email, $password);

if ($loggedInUser) {
    // Authentication successful
    // Set session variables
    $_SESSION['user_id'] = $loggedInUser['id'];
    $_SESSION['user_name'] = $loggedInUser['name'];
    $_SESSION['user_role'] = $loggedInUser['role'];
    
    // Clear login data from session
    unset($_SESSION['login_data']);

    set_flash_message('success', 'Welcome back, ' . htmlspecialchars($loggedInUser['name']) . '!');

    // Redirect based on user role
    if ($loggedInUser['role'] === 'admin') {
        header('Location: ../admin/admin_dashboard.php');
    } elseif ($loggedInUser['role'] === 'provider') {
        // This is the CRITICAL block to check
        if ($loggedInUser['provider_status'] === 'approved') {
            // THE PROBLEM IS HERE! THIS PATH IS LIKELY STILL WRONG.
            // It should be relative from /handlers/ to /provider/
            header('Location: ../provider/provider_dashboard.php'); // Ensure this is '../provider/' NOT '../public/provider/'
        } elseif ($loggedInUser['provider_status'] === 'pending') {
            set_flash_message('info', 'Your provider account is pending approval. Please wait for an administrator to review it.');
            header('Location: ../public/index.php'); // This path is correct
        } else { // rejected or inactive
            set_flash_message('error', 'Your provider account is currently inactive or rejected. Please contact support.');
            header('Location: ../public/login.php'); // This path is correct
        }
    } else { // Default to customer dashboard for 'customer' role
        // This is also a CRITICAL block to check
        // It should be relative from /handlers/ to /customer/
        header('Location: ../customer/customer_dashboard.php'); // Ensure this is '../customer/' NOT '../public/customer/'
    }
    exit();

} else {
    // Authentication failed
    set_flash_message('error', 'Invalid email or password.');
    header('Location: ../public/login.php');
    exit();
}