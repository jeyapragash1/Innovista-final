<?php
// Usage: Place this file in your project root and run it once in the browser or via CLI
// It will insert test availability for provider_id 26

require_once __DIR__ . '/../config/Database.php';

$providerId = 26; // Change this to your provider's ID if needed
$availabilities = [
    ['2025-09-14', '10:30 AM'],
    ['2025-09-14', '12:30 PM'],
    ['2025-09-14', '03:00 PM'],
    ['2025-09-15', '11:00 AM'],
    ['2025-09-15', '02:00 PM'],
];

$database = new Database();
$db = $database->getConnection();

foreach ($availabilities as $entry) {
    $stmt = $db->prepare('INSERT INTO provider_availability (provider_id, available_date, available_time) VALUES (?, ?, ?)');
    $stmt->execute([$providerId, $entry[0], $entry[1]]);
}

echo "Test availability inserted for provider_id $providerId.";
