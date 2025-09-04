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
    $current_portfolio = isset($row['portfolio']) ? $row['portfolio'] : '';
    $portfolio_images = $current_portfolio ? explode(',', $current_portfolio) : [];

    $upload_dir = '../public/assets/images/';
    $uploaded_files = [];
    foreach ($_FILES['portfolio_photos']['tmp_name'] as $key => $tmp_name) {
        $file_name = basename($_FILES['portfolio_photos']['name'][$key]);
        $target_file = $upload_dir . $file_name;
        if (move_uploaded_file($tmp_name, $target_file)) {
            $uploaded_files[] = $file_name;
        }
    }

    // Merge new images with existing
    $portfolio_images = array_merge($portfolio_images, $uploaded_files);
    $portfolio_str = implode(',', $portfolio_images);

    // Update DB
    $stmt2 = $db->prepare('UPDATE service SET portfolio = :portfolio WHERE provider_id = :provider_id');
    $stmt2->bindParam(':portfolio', $portfolio_str);
    $stmt2->bindParam(':provider_id', $provider_id);
    $stmt2->execute();

    set_flash_message('success', 'Portfolio updated successfully!');
    header('Location: manage_portfolio.php');
    exit();
} else {
    set_flash_message('error', 'No files uploaded.');
    header('Location: manage_portfolio.php');
    exit();
}
