<?php
// C:\xampp1\htdocs\Innovista-final\Innovista-main\handlers\handle_quote_action.php

require_once '../public/session.php';
require_once '../handlers/flash_message.php';
require_once '../config/Database.php';

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../public/index.php'); // Redirect to home if not POST
    exit();
}

// Ensure user is logged in as a customer
if (!isUserLoggedIn() || getUserRole() !== 'customer') {
    set_flash_message('error', 'Please log in as a customer to perform this action.');
    header('Location: ../public/login.php');
    exit();
}

$customer_id = getUserId();
$quote_id = filter_input(INPUT_POST, 'quote_id', FILTER_VALIDATE_INT);
$action = filter_input(INPUT_POST, 'action', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$quote_type = filter_input(INPUT_POST, 'quote_type', FILTER_SANITIZE_FULL_SPECIAL_CHARS); // Should be 'custom' here

if (!$quote_id || empty($action) || $quote_type !== 'custom') {
    set_flash_message('error', 'Invalid request for quote action.');
    header('Location: ../customer/my_projects.php');
    exit();
}

$database = new Database();
$conn = $database->getConnection();

try {
    $conn->beginTransaction();

    // Verify the custom quotation belongs to this customer
    $stmt_check = $conn->prepare("SELECT status FROM custom_quotations WHERE id = :id AND customer_id = :customer_id");
    $stmt_check->bindParam(':id', $quote_id, PDO::PARAM_INT);
    $stmt_check->bindParam(':customer_id', $customer_id, PDO::PARAM_INT);
    $stmt_check->execute();
    $current_status = $stmt_check->fetchColumn();

    if (!$current_status) {
        $conn->rollBack();
        set_flash_message('error', 'Quotation not found or you do not have permission.');
        header('Location: ../customer/my_projects.php');
        exit();
    }

    // Only allow action if quote is in 'sent' or 'pending' status
    if ($current_status !== 'sent' && $current_status !== 'pending') {
        $conn->rollBack();
        set_flash_message('info', 'This quotation cannot be ' . $action . 'd at this time. Its status is ' . htmlspecialchars($current_status) . '.');
        header('Location: ../customer/view_quote.php?id=' . $quote_id . '&type=custom');
        exit();
    }

    if ($action === 'decline') {
        $stmt_update_quote = $conn->prepare("UPDATE custom_quotations SET status = 'declined' WHERE id = :id");
        $stmt_update_quote->bindParam(':id', $quote_id, PDO::PARAM_INT);
        $stmt_update_quote->execute();

        set_flash_message('success', 'Quotation successfully declined.');
        $conn->commit();
        header('Location: ../customer/my_projects.php');
        exit();

    } /* The 'accept' action is handled by handle_booking.php after payment */
    else {
        $conn->rollBack();
        set_flash_message('error', 'Invalid action specified.');
        header('Location: ../customer/view_quote.php?id=' . $quote_id . '&type=custom');
        exit();
    }

} catch (PDOException $e) {
    $conn->rollBack();
    error_log("Quote Action Error: " . $e->getMessage());
    set_flash_message('error', 'A database error occurred. Please try again.');
    header('Location: ../customer/view_quote.php?id=' . $quote_id . '&type=custom');
    exit();
}