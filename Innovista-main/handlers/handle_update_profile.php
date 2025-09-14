<?php
// C:\xampp1\htdocs\Innovista-final\Innovista-main\handlers\handle_update_profile.php

require_once '../public/session.php';
require_once '../handlers/flash_message.php';
require_once '../config/Database.php';

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../public/index.php');
    exit();
}

// Ensure user is logged in
if (!isUserLoggedIn()) {
    set_flash_message('error', 'You must be logged in to update your profile.');
    header('Location: ../public/login.php');
    exit();
}

$user_id = getUserId();
$name = filter_input(INPUT_POST, 'full_name', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
$phone = filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$address = filter_input(INPUT_POST, 'address', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$bio = filter_input(INPUT_POST, 'bio', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

// Input Validation
if (empty($name) || empty($email)) {
    set_flash_message('error', 'Full Name and Email are required.');
    header('Location: ../customer/my_profile.php');
    exit();
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    set_flash_message('error', 'Invalid email format.');
    header('Location: ../customer/my_profile.php');
    exit();
}

$database = new Database();
$conn = $database->getConnection();

$new_profile_image_path = null; // Initialize to null

try {
    $conn->beginTransaction();

    // 1. Get current user's profile_image_path from DB to handle deletion if needed
    $stmt_current_image = $conn->prepare("SELECT profile_image_path FROM users WHERE id = :id");
    $stmt_current_image->bindParam(':id', $user_id, PDO::PARAM_INT);
    $stmt_current_image->execute();
    $current_image_path = $stmt_current_image->fetchColumn();
    $new_profile_image_path = $current_image_path; // Assume current path unless new file uploaded

    // 2. Handle Profile Image Upload
    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../public/uploads/profiles/'; // Relative to handlers/
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        $file_tmp = $_FILES['profile_image']['tmp_name'];
        $file_ext = strtolower(pathinfo($_FILES['profile_image']['name'], PATHINFO_EXTENSION));
        $allowed_ext = ['jpg', 'jpeg', 'png', 'gif'];

        if (in_array($file_ext, $allowed_ext)) {
            $new_file_name = 'profile_' . $user_id . '_' . uniqid() . '.' . $file_ext;
            $destination_path = $upload_dir . $new_file_name;
            $public_image_path_for_db = 'uploads/profiles/' . $new_file_name; // Path stored in DB (relative to public/)

            if (move_uploaded_file($file_tmp, $destination_path)) {
                // Delete old image if it was a local upload (not URL or default)
                if ($current_image_path &&
                    !filter_var($current_image_path, FILTER_VALIDATE_URL) &&
                    $current_image_path !== 'assets/images/default-avatar.jpg' &&
                    file_exists('../public/' . $current_image_path)) { // Path relative to handlers/
                    unlink('../public/' . $current_image_path);
                }
                $new_profile_image_path = $public_image_path_for_db;
            } else {
                set_flash_message('error', 'Failed to upload new profile image.');
                header('Location: ../customer/my_profile.php');
                exit();
            }
        } else {
            set_flash_message('error', 'Invalid image file type. Only JPG, JPEG, PNG, GIF allowed.');
            header('Location: ../customer/my_profile.php');
            exit();
        }
    }

    // 3. Update user data
    $stmt_update = $conn->prepare("
        UPDATE users SET 
            name = :name, 
            email = :email, 
            phone = :phone, 
            address = :address, 
            bio = :bio, 
            profile_image_path = :profile_image_path
        WHERE id = :id
    ");
    $stmt_update->bindParam(':name', $name);
    $stmt_update->bindParam(':email', $email);
    $stmt_update->bindParam(':phone', $phone);
    $stmt_update->bindParam(':address', $address);
    $stmt_update->bindParam(':bio', $bio);
    $stmt_update->bindParam(':profile_image_path', $new_profile_image_path);
    $stmt_update->bindParam(':id', $user_id, PDO::PARAM_INT);
    $stmt_update->execute();

    // 4. Update session name if it was changed
    if ($_SESSION['user_name'] !== $name) {
        $_SESSION['user_name'] = $name;
    }

    $conn->commit();
    set_flash_message('success', 'Profile updated successfully!');
    header('Location: ../customer/my_profile.php');
    exit();

} catch (PDOException $e) {
    $conn->rollBack();
    // Check for duplicate email error specifically
    if ($e->getCode() == '23000' && str_contains($e->getMessage(), 'email')) {
        set_flash_message('error', 'This email is already registered to another account.');
    } else {
        error_log("Update Profile PDO Exception: " . $e->getMessage());
        set_flash_message('error', 'A database error occurred while updating your profile. Please try again.');
    }
    header('Location: ../customer/my_profile.php');
    exit();
} catch (Exception $e) {
    $conn->rollBack();
    error_log("Update Profile General Exception: " . $e->getMessage());
    set_flash_message('error', 'An unexpected error occurred. Please try again later.');
    header('Location: ../customer/my_profile.php');
    exit();
}