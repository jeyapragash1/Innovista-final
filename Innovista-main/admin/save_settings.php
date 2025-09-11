<?php
// save_settings.php
require_once 'admin_header.php'; // Ensures admin is logged in
require_once '../config/Database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db = new Database();
    $conn = $db->getConnection();

    $settings_to_save = [
        'homepage_welcome_message' => filter_input(INPUT_POST, 'homepage_welcome_message', FILTER_SANITIZE_FULL_SPECIAL_CHARS),
        'homepage_about_text'      => filter_input(INPUT_POST, 'homepage_about_text', FILTER_SANITIZE_FULL_SPECIAL_CHARS),
        'platform_name'            => filter_input(INPUT_POST, 'platform_name', FILTER_SANITIZE_FULL_SPECIAL_CHARS),
        'admin_contact_email'      => filter_input(INPUT_POST, 'admin_contact_email', FILTER_SANITIZE_EMAIL),
        'platform_address'         => filter_input(INPUT_POST, 'platform_address', FILTER_SANITIZE_FULL_SPECIAL_CHARS),
        'facebook_url'             => filter_input(INPUT_POST, 'facebook_url', FILTER_SANITIZE_URL),
        'instagram_url'            => filter_input(INPUT_POST, 'instagram_url', FILTER_SANITIZE_URL),
    ];

    $success_count = 0;
    $total_settings = count($settings_to_save);

    foreach ($settings_to_save as $key => $value) {
        // Use UPSERT: try to update, if no rows affected (key not found), then insert
        $stmt = $conn->prepare("
            INSERT INTO settings (setting_key, setting_value)
            VALUES (:key, :value)
            ON DUPLICATE KEY UPDATE setting_value = :value
        ");
        $stmt->bindParam(':key', $key);
        $stmt->bindParam(':value', $value);

        if ($stmt->execute()) {
            $success_count++;
        }
    }

    if ($success_count === $total_settings) {
        header("Location: settings.php?status=success&message=Settings saved successfully.");
    } else {
        header("Location: settings.php?status=error&message=Some settings could not be saved.");
    }
    exit();

} else {
    // If accessed directly without POST request
    header("Location: settings.php?status=error&message=Invalid request method.");
    exit();
}
?>