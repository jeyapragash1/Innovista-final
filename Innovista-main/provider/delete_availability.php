<?php
header('Content-Type: application/json');
require_once '../config/Database.php';
require_once '../config/session.php';

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['date']) || !isset($data['time'])) {
    echo json_encode(['success' => false, 'error' => 'Invalid data']);
    exit;
}

$provider_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
if (!$provider_id) {
    echo json_encode(['success' => false, 'error' => 'Provider not logged in']);
    exit;
}

$db = (new Database())->getConnection();
try {
    $stmt = $db->prepare('DELETE FROM provider_availability WHERE provider_id = :provider_id AND available_date = :available_date AND available_time = :available_time');
    $stmt->bindParam(':provider_id', $provider_id);
    $stmt->bindParam(':available_date', $data['date']);
    $stmt->bindParam(':available_time', $data['time']);
    $stmt->execute();
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => 'DB error']);
}
