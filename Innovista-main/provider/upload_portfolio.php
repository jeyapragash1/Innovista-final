<?php
require_once '../config/session.php';
require_once '../config/Database.php';
protectPage('provider');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['portfolio_photos'])) {
    $provider_id = $_SESSION['user_id'];
    $db = (new Database())->getConnection();

    // Fetch current portfolio from DB
    $stmt = $db->prepare('SELECT portfolio FROM service WHERE provider_id = :provider_id');
    $stmt->bindParam(':provider_id', $provider_id);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $current_portfolio = (isset($row['portfolio']) && $row['portfolio'] && $row['portfolio'] !== 'NULL') ? $row['portfolio'] : '';
    $portfolio_images = $current_portfolio ? explode(',', $current_portfolio) : [];

    $upload_dir = '../public/assets/images/';
    $uploaded_files = [];
    foreach ($_FILES['portfolio_photos']['tmp_name'] as $key => $tmp_name) {
        $file_name = basename($_FILES['portfolio_photos']['name'][$key]);
        if (!$file_name) continue;
        $target_file = $upload_dir . $file_name;
        if (move_uploaded_file($tmp_name, $target_file)) {
            $uploaded_files[] = $file_name;
        }
    }

    // Merge new images with existing, remove empty values
    $portfolio_images = array_filter(array_merge($portfolio_images, $uploaded_files));
    $portfolio_str = $portfolio_images ? implode(',', $portfolio_images) : '';

    // Update DB, never set NULL

    // Update service table
    $stmt2 = $db->prepare('UPDATE service SET portfolio = :portfolio WHERE provider_id = :provider_id');
    $stmt2->bindParam(':portfolio', $portfolio_str);
    $stmt2->bindParam(':provider_id', $provider_id);
    $stmt2->execute();

    // Also update user table
    $stmt3 = $db->prepare('UPDATE users SET portfolio = :portfolio WHERE id = :provider_id');
    $stmt3->bindParam(':portfolio', $portfolio_str);
    $stmt3->bindParam(':provider_id', $provider_id);
    $stmt3->execute();

    set_flash_message('success', 'Portfolio updated successfully!');
    // Redirect back to the page where the upload was triggered
    $referer = $_SERVER['HTTP_REFERER'] ?? '';
    if (strpos($referer, 'my_profile.php') !== false) {
        header('Location: my_profile.php');
    } else {
        header('Location: manage_portfolio.php');
    }
    exit();
} else {
    set_flash_message('error', 'No files uploaded.');
    $referer = $_SERVER['HTTP_REFERER'] ?? '';
    if (strpos($referer, 'my_profile.php') !== false) {
        header('Location: my_profile.php');
    } else {
        header('Location: manage_portfolio.php');
    }
    exit();
}
