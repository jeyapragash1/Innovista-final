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
$stmt = $db->prepare('SELECT available_date, available_time FROM provider_availability WHERE provider_id = :provider_id');
$stmt->bindParam(':provider_id', $provider_id);
$stmt->execute();
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Group times by date
$availability = [];
foreach ($rows as $row) {
    $date = $row['available_date'];
    $time = $row['available_time'];
    if (!isset($availability[$date])) {
        $availability[$date] = [];
    }
    $availability[$date][] = $time;
}
echo json_encode(['success' => true, 'availability' => $availability]);
