<?php
header('Content-Type: application/json');
require_once '../config/Database.php';
require_once '../config/session.php';

$data = json_decode(file_get_contents('php://input'), true);
if (!isset($data['dates']) || !is_array($data['dates'])) {
    echo json_encode(['success' => false, 'error' => 'Invalid data']);
    exit;
}

// Get provider info from session
$provider_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
$provider_name = isset($_SESSION['user_name']) ? $_SESSION['user_name'] : '';
if (!$provider_id || !$provider_name) {
    echo json_encode(['success' => false, 'error' => 'Provider not logged in']);
    exit;
}

try {
    $db = (new Database())->getConnection();
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => 'Database connection error: ' . $e->getMessage()]);
    exit;
}

$success = false;
$errorMsg = '';
foreach ($data['dates'] as $date) {
    try {
        $stmt = $db->prepare('INSERT IGNORE INTO provider_availability (provider_id, provider_name, available_date) VALUES (:provider_id, :provider_name, :available_date)');
        $stmt->bindParam(':provider_id', $provider_id);
        $stmt->bindParam(':provider_name', $provider_name);
        $stmt->bindParam(':available_date', $date);
        $stmt->execute();
        $success = true; // Set to true if at least one insert succeeds
    } catch (Exception $e) {
        $errorMsg = $e->getMessage();
    }
}
echo json_encode(['success' => $success, 'error' => $errorMsg]);
