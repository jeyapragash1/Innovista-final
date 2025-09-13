<?php
require_once '../config/session.php';
protectPage('admin');

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../classes/Review.php';

$database = new Database();
$db = $database->getConnection();

$reviewObj = new Review($db);

// Handle delete request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    $reviewObj->delete((int)$_POST['delete_id']);
    header('Location: ../admin/feedback_all.php?msg=Deleted');
    exit;
}

// Fetch all feedback for listing
$reviews = $reviewObj->all();

// Render admin view
include __DIR__ . '/../admin/feedback_all.php';
