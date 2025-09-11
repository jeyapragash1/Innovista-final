<?php
// Run this script once to clean up spaces in main_service and subcategories columns
require_once __DIR__ . '/../config/Database.php';

$db = (new Database())->getConnection();

// Fetch all providers
$stmt = $db->query('SELECT provider_id, main_service, subcategories FROM service');
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($rows as $row) {
    $provider_id = $row['provider_id'];
    $main_services = array_filter(array_map('trim', explode(',', $row['main_service'])));
    $subcategories = array_filter(array_map('trim', explode(',', $row['subcategories'])));
    $main_service_clean = implode(',', $main_services);
    $subcategories_clean = implode(',', $subcategories);
    $update = $db->prepare('UPDATE service SET main_service = :main_service, subcategories = :subcategories WHERE provider_id = :provider_id');
    $update->bindParam(':main_service', $main_service_clean);
    $update->bindParam(':subcategories', $subcategories_clean);
    $update->bindParam(':provider_id', $provider_id);
    $update->execute();
}
echo "Service table cleaned up successfully.";
