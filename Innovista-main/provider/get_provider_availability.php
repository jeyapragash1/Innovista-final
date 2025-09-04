<?php
header('Content-Type: application/json');
require_once '../config/Database.php';
require_once '../config/session.php';

// Accept provider_id via GET or POST
$provider_id = null;
if (isset($_GET['provider_id'])) {
    $provider_id = $_GET['provider_id'];
} elseif (isset($_POST['provider_id'])) {
    $provider_id = $_POST['provider_id'];
}

if (!$provider_id) {
    echo json_encode(['success' => false, 'error' => 'Provider ID required', 'available_dates' => []]);
    exit;
}

$db = (new Database())->getConnection();
$stmt = $db->prepare('SELECT available_date FROM provider_availability WHERE provider_id = :provider_id');
$stmt->bindParam(':provider_id', $provider_id);
$stmt->execute();
$dates = $stmt->fetchAll(PDO::FETCH_COLUMN);
echo json_encode(['success' => true, 'available_dates' => $dates]);
