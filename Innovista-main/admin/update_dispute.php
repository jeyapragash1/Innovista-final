<?php
// update_dispute.php
require_once 'admin_header.php'; // Ensures admin is logged in
require_once '../config/Database.php';

$db = new Database();
$conn = $db->getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['id'])) {
    $dispute_id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
    $status = filter_input(INPUT_POST, 'status', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $admin_notes = filter_input(INPUT_POST, 'admin_notes', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    // Validate inputs
    if (!$dispute_id || !is_numeric($dispute_id) || !in_array($status, ['open', 'under_review', 'resolved'])) {
        header("Location: resolve_disputes.php?status=error&message=Invalid input for dispute resolution.");
        exit();
    }

    try {
        $stmt = $conn->prepare("UPDATE disputes SET status = :status, admin_notes = :admin_notes, updated_at = NOW() WHERE id = :id");
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':admin_notes', $admin_notes);
        $stmt->bindParam(':id', $dispute_id, PDO::PARAM_INT);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            header("Location: view_dispute.php?id={$dispute_id}&status=success&message=Dispute updated successfully.");
        } else {
            header("Location: view_dispute.php?id={$dispute_id}&status=info&message=No changes were made to the dispute.");
        }
        exit();

    } catch (PDOException $e) {
        header("Location: view_dispute.php?id={$dispute_id}&status=error&message=Database error: " . urlencode($e->getMessage()));
        exit();
    }

} else {
    header("Location: resolve_disputes.php?status=error&message=Invalid request method or missing ID.");
    exit();
}