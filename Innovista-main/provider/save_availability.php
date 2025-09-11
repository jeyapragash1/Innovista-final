<?php
header('Content-Type: application/json');
require_once '../config/Database.php';
require_once '../config/session.php';


$data = json_decode(file_get_contents('php://input'), true);
if (!isset($data['availability']) || !is_array($data['availability'])) {
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
foreach ($data['availability'] as $entry) {
    $date = $entry['date'];
    foreach ($entry['times'] as $time) {
        try {
            $stmt = $db->prepare('INSERT INTO provider_availability (provider_id, provider_name, available_date, available_time, created_at) VALUES (?, ?, ?, ?, NOW())');
            $stmt->execute([$provider_id, $provider_name, $date, $time]);
            $success = true;
        } catch (Exception $e) {
            $errorMsg = $e->getMessage();
        }
    }
}
echo json_encode(['success' => $success, 'error' => $errorMsg]);
