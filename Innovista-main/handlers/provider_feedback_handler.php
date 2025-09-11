<?php
require_once '../config/session.php';
protectPage('provider');

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../classes/Review.php';

$providerId = $_SESSION['user_id'] ?? null;

// DB setup
$database = new Database();
$db = $database->getConnection();

$reviewObj = new Review($db);
$reviews = $reviewObj->byProvider((int)$providerId);

// Render the provider feedback list view
include __DIR__ . '/../provider/feedback_list.php';
