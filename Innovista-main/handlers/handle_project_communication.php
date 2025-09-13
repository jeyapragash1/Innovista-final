<?php
// C:\xampp1\htdocs\Innovista-final\Innovista-main\handlers\handle_project_communication.php

require_once '../public/session.php';
require_once '../handlers/flash_message.php';
require_once '../config/Database.php';

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../public/index.php'); // Redirect to home if not POST
    exit();
}

// Ensure user is logged in
if (!isUserLoggedIn()) {
    set_flash_message('error', 'You must be logged in to send messages.');
    header('Location: ../public/login.php');
    exit();
}

$user_id = getUserId();
$project_id = filter_input(INPUT_POST, 'project_id', FILTER_VALIDATE_INT);
$message_text = filter_input(INPUT_POST, 'message', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

if (!$project_id || empty($message_text)) {
    set_flash_message('error', 'Missing project ID or message content.');
    header('Location: ../customer/track_project.php?id=' . ($project_id ?? '')); // Redirect back to project page
    exit();
}

$database = new Database();
$conn = $database->getConnection();

try {
    $conn->beginTransaction();

    // Verify the project belongs to the logged-in customer (or admin)
    // Join with custom_quotations to ensure ownership
    $stmt_check_project = $conn->prepare("
        SELECT p.id 
        FROM projects p
        JOIN custom_quotations cq ON p.quotation_id = cq.id
        WHERE p.id = :project_id AND (cq.customer_id = :user_id OR :user_role = 'admin')
    ");
    $stmt_check_project->bindParam(':project_id', $project_id, PDO::PARAM_INT);
    $stmt_check_project->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt_check_project->bindParam(':user_role', getUserRole());
    $stmt_check_project->execute();

    if (!$stmt_check_project->fetch(PDO::FETCH_ASSOC)) {
        $conn->rollBack();
        set_flash_message('error', 'Project not found or you do not have permission to send messages.');
        header('Location: ../customer/my_projects.php');
        exit();
    }

    // Insert the message as a project update
    $stmt_insert_update = $conn->prepare("INSERT INTO project_updates (project_id, user_id, update_text, created_at) VALUES (:project_id, :user_id, :update_text, NOW())");
    $stmt_insert_update->bindParam(':project_id', $project_id, PDO::PARAM_INT);
    $stmt_insert_update->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt_insert_update->bindParam(':update_text', $message_text);
    $stmt_insert_update->execute();

    $conn->commit();
    set_flash_message('success', 'Message sent successfully!');
    header('Location: ../customer/track_project.php?id=' . $custom_quotation_id); // Redirect back to the project with custom_quotation_id
    exit();

} catch (PDOException $e) {
    $conn->rollBack();
    error_log("Project Communication Error: " . $e->getMessage());
    set_flash_message('error', 'A database error occurred while sending message. Please try again.');
    header('Location: ../customer/track_project.php?id=' . ($custom_quotation_id ?? ''));
    exit();
} catch (Exception $e) {
    $conn->rollBack();
    error_log("Project Communication General Error: " . $e->getMessage());
    set_flash_message('error', 'An unexpected error occurred. Please try again.');
    header('Location: ../customer/track_project.php?id=' . ($custom_quotation_id ?? ''));
    exit();
}