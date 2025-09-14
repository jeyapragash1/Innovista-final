<?php
// C:\xampp1\htdocs\Innovista-final\Innovista-main\handlers\handle_update_password.php

require_once '../public/session.php';
require_once '../handlers/flash_message.php';
require_once '../config/Database.php';
require_once '../classes/User.php'; // Needed for User class to verify old password

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../public/index.php');
    exit();
}

// Ensure user is logged in
if (!isUserLoggedIn()) {
    set_flash_message('error', 'You must be logged in to change your password.');
    header('Location: ../public/login.php');
    exit();
}

$user_id = getUserId();
$current_password = $_POST['current_password'] ?? '';
$new_password = $_POST['new_password'] ?? '';
$confirm_new_password = $_POST['confirm_new_password'] ?? '';

// Input Validation
if (empty($current_password) || empty($new_password) || empty($confirm_new_password)) {
    set_flash_message('error', 'All password fields are required.');
    header('Location: ../customer/my_profile.php');
    exit();
}
if (strlen($new_password) < 8) {
    set_flash_message('error', 'New password must be at least 8 characters long.');
    header('Location: ../customer/my_profile.php');
    exit();
}
if ($new_password !== $confirm_new_password) {
    set_flash_message('error', 'New password and confirmation do not match.');
    header('Location: ../customer/my_profile.php');
    exit();
}

$database = new Database();
$conn = $database->getConnection();
$userClass = new User($conn);

try {
    $conn->beginTransaction();

    // 1. Verify current password
    $user_data = $userClass->findById($user_id);
    if (!$user_data || !password_verify($current_password, $user_data['password'])) {
        $conn->rollBack();
        set_flash_message('error', 'Current password is incorrect.');
        header('Location: ../customer/my_profile.php');
        exit();
    }

    // 2. Hash new password
    $new_password_hash = password_hash($new_password, PASSWORD_BCRYPT);

    // 3. Update password in the database
    $stmt_update_pass = $conn->prepare("UPDATE users SET password = :new_password_hash WHERE id = :id");
    $stmt_update_pass->bindParam(':new_password_hash', $new_password_hash);
    $stmt_update_pass->bindParam(':id', $user_id, PDO::PARAM_INT);
    $stmt_update_pass->execute();

    $conn->commit();
    set_flash_message('success', 'Password updated successfully!');
    header('Location: ../customer/my_profile.php');
    exit();

} catch (PDOException $e) {
    $conn->rollBack();
    error_log("Update Password PDO Exception: " . $e->getMessage());
    set_flash_message('error', 'A database error occurred while updating your password. Please try again.');
    header('Location: ../customer/my_profile.php');
    exit();
} catch (Exception $e) {
    $conn->rollBack();
    error_log("Update Password General Exception: " . $e->getMessage());
    set_flash_message('error', 'An unexpected error occurred. Please try again later.');
    header('Location: ../customer/my_profile.php');
    exit();
}