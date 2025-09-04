<?php
require_once '../config/session.php';
require_once '../config/Database.php';

// Protect the page - only logged-in users can update their profile
protectPage(); // No role needed, as any logged-in user can access their own profile

// Check if the form was submitted using POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['update_details'])) {
    header('Location: ../customer/my_profile.php'); // Redirect if accessed directly
    exit();
}

// Basic validation
if (empty($_POST['full_name']) || empty($_POST['email'])) {
    set_flash_message('error', 'Full name and email are required.');
    header('Location: ../customer/my_profile.php');
    exit();
}

// Sanitize inputs
$user_id = $_SESSION['user_id'];
$full_name = filter_input(INPUT_POST, 'full_name', FILTER_SANITIZE_STRING);
$email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
$phone = filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_STRING);
$address = filter_input(INPUT_POST, 'address', FILTER_SANITIZE_STRING);

if (!$email) {
    set_flash_message('error', 'Please provide a valid email address.');
    header('Location: ../customer/my_profile.php');
    exit();
}

try {
    $database = new Database();
    $db = $database->getConnection();

    // Prepare the UPDATE statement
    $stmt = $db->prepare("UPDATE users SET name = :name, email = :email, phone = :phone, address = :address WHERE id = :id");

    // Bind parameters
    $stmt->bindParam(':name', $full_name);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':phone', $phone);
    $stmt->bindParam(':address', $address);
    $stmt->bindParam(':id', $user_id);

    // Execute the query
    if ($stmt->execute()) {
        // IMPORTANT: Update the session name in case it was changed
        $_SESSION['user_name'] = $full_name;
        
        set_flash_message('success', 'Your profile has been updated successfully.');
    } else {
        set_flash_message('error', 'There was an error updating your profile. Please try again.');
    }

} catch (PDOException $e) {
    // Check for duplicate email error
    if ($e->getCode() == 23000) { // Integrity constraint violation (duplicate entry)
         set_flash_message('error', 'That email address is already in use by another account.');
    } else {
        error_log("Profile Update Error: " . $e->getMessage());
        set_flash_message('error', 'A database error occurred. Please contact support.');
    }
}

// Redirect back to the profile page
$redirect_url = '../' . $_SESSION['user_role'] . '/' . 'my_profile.php';
header('Location: ' . $redirect_url);
exit();
?>