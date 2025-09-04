<?php
require_once '../config/session.php';
require_once '../config/Database.php';
require_once '../classes/User.php'; // We need the User class to find the user

// Protect the page
protectPage();

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['update_password'])) {
    header('Location: ../customer/my_profile.php');
    exit();
}

// --- Validation ---
$current_password = $_POST['current_password'] ?? '';
$new_password = $_POST['new_password'] ?? '';
$confirm_password = $_POST['confirm_password'] ?? '';

if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
    set_flash_message('error', 'All password fields are required.');
    header('Location: ../customer/my_profile.php');
    exit();
}

if (strlen($new_password) < 8) {
    set_flash_message('error', 'New password must be at least 8 characters long.');
    header('Location: ../customer/my_profile.php');
    exit();
}

if ($new_password !== $confirm_password) {
    set_flash_message('error', 'New passwords do not match.');
    header('Location: ../customer/my_profile.php');
    exit();
}

// --- Password Update Logic ---
$user_id = $_SESSION['user_id'];
$database = new Database();
$db = $database->getConnection();
$userObject = new User($db); // Create an instance of the User class

try {
    // 1. Get current user's data from DB to verify current password
    $stmt = $db->prepare("SELECT password FROM users WHERE id = :id");
    $stmt->bindParam(':id', $user_id);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user || !password_verify($current_password, $user['password'])) {
        // Current password provided is incorrect
        set_flash_message('error', 'Your current password is incorrect.');
        header('Location: ../customer/my_profile.php');
        exit();
    }
    
    // 2. If current password is correct, hash the new password
    $new_password_hash = password_hash($new_password, PASSWORD_BCRYPT);

    // 3. Update the password in the database
    $update_stmt = $db->prepare("UPDATE users SET password = :password WHERE id = :id");
    $update_stmt->bindParam(':password', $new_password_hash);
    $update_stmt->bindParam(':id', $user_id);

    if ($update_stmt->execute()) {
        set_flash_message('success', 'Your password has been changed successfully.');
    } else {
        set_flash_message('error', 'Failed to update password. Please try again.');
    }

} catch (PDOException $e) {
    error_log("Password Update Error: " . $e->getMessage());
    set_flash_message('error', 'A database error occurred.');
}

// Redirect back to the profile page
$redirect_url = '../' . $_SESSION['user_role'] . '/' . 'my_profile.php';
header('Location: ' . $redirect_url);
exit();
?>