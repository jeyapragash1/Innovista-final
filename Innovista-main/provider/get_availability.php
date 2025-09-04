<?php
header('Content-Type: application/json');
require_once '../config/Database.php';
require_once '../config/session.php';

$provider_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
if (!$provider_id) {
    echo json_encode(['success' => false, 'error' => 'Provider not logged in', 'dates' => []]);
    exit;
}

$db = (new Database())->getConnection();
$stmt = $db->prepare('SELECT available_date FROM provider_availability WHERE provider_id = :provider_id');
$stmt->bindParam(':provider_id', $provider_id);
$stmt->execute();
$dates = $stmt->fetchAll(PDO::FETCH_COLUMN);
echo json_encode(['success' => true, 'dates' => $dates]);
