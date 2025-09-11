<?php
// save_settings.php
require_once 'admin_header.php'; // Ensures admin is logged in
require_once '../config/Database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db = new Database();
    $conn = $db->getConnection();

    // List of expected settings from the form
    $expected_settings = [
        'homepage_hero_h1', 'homepage_hero_p', 'homepage_how_it_works_title',
        'homepage_services_title', 'homepage_products_title', 'homepage_products_description',
        'homepage_why_choose_us_title', 'homepage_testimonials_title', 'homepage_our_work_title',
        'homepage_our_work_description', 'homepage_faq_title', 'homepage_cta_title',
        'homepage_cta_description', 'platform_name', 'admin_contact_email', 
        'platform_address', 'facebook_url', 'instagram_url'
    ];

    $success_count = 0;
    $total_settings_to_update = 0;

    try {
        $conn->beginTransaction();

        foreach ($expected_settings as $key) {
            if (isset($_POST[$key])) {
                $value = filter_input(INPUT_POST, $key, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
                if ($key === 'admin_contact_email') {
                    $value = filter_input(INPUT_POST, $key, FILTER_SANITIZE_EMAIL);
                } elseif (str_contains($key, '_url')) {
                    $value = filter_input(INPUT_POST, $key, FILTER_SANITIZE_URL);
                }

                $total_settings_to_update++;
                
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
        }
        $conn->commit();

        if ($success_count === $total_settings_to_update) {
            header("Location: settings.php?status=success&message=Settings saved successfully.");
        } else {
            header("Location: settings.php?status=error&message=Some settings could not be saved. (Saved {$success_count}/{$total_settings_to_update})");
        }
        exit();

    } catch (PDOException $e) {
        $conn->rollBack();
        error_log("Database error saving settings: " . $e->getMessage());
        header("Location: settings.php?status=error&message=Database error: " . urlencode($e->getMessage()));
        exit();
    }

} else {
    // If accessed directly without POST request
    header("Location: settings.php?status=error&message=Invalid request method.");
    exit();
}