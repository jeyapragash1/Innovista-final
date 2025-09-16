<?php
// C:\xampp1\htdocs\Innovista-final\Innovista-main\handlers\handle_quote_action.php

require_once '../public/session.php';
require_once '../handlers/flash_message.php';
require_once '../config/Database.php';
protectPage('customer');

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $quotation_id = intval($_POST['quotation_id'] ?? 0);
    $action = $_POST['action'] ?? '';
    $db = (new Database())->getConnection();
    $success = false;

    // Try to update custom_quotations first
    $stmt = $db->prepare('SELECT id FROM custom_quotations WHERE quotation_id = :id');
    $stmt->bindParam(':id', $quotation_id);
    $stmt->execute();
    $custom = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($custom) {
        if ($action === 'confirm') {
            $stmt = $db->prepare('UPDATE custom_quotations SET status = "booked" WHERE quotation_id = :id');
            $stmt->bindParam(':id', $quotation_id);
            $success = $stmt->execute();
        } elseif ($action === 'cancel') {
            $stmt = $db->prepare('UPDATE custom_quotations SET status = "cancelled" WHERE quotation_id = :id');
            $stmt->bindParam(':id', $quotation_id);
            $success = $stmt->execute();
        }
    } else {
        // Fallback to quotations table
        if ($action === 'confirm') {
            $stmt = $db->prepare('UPDATE quotations SET status = "booked" WHERE id = :id');
            $stmt->bindParam(':id', $quotation_id);
            $success = $stmt->execute();
        } elseif ($action === 'cancel') {
            $stmt = $db->prepare('UPDATE quotations SET status = "cancelled" WHERE id = :id');
            $stmt->bindParam(':id', $quotation_id);
            $success = $stmt->execute();
        }
    }
    if ($success) {
        echo json_encode(['success' => true, 'message' => 'Request processed successfully.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Could not process request.']);
    }
    exit();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
    exit();
}