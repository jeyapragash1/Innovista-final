<?php
// user_actions.php
require_once 'admin_header.php'; // Ensures admin is logged in
require_once '../config/Database.php';

$db = new Database();
$conn = $db->getConnection();

if (isset($_GET['id']) && isset($_GET['action'])) {
    $user_id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
    $action = filter_input(INPUT_GET, 'action', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    if (!$user_id || !is_numeric($user_id)) {
        header("Location: manage_users.php?status=error&message=Invalid user ID.");
        exit();
    }

    // Prevent admin from deactivating/deleting themselves
    if ((int)$user_id === (int)$_SESSION['user_id']) {
        header("Location: manage_users.php?status=error&message=You cannot perform this action on your own account.");
        exit();
    }

    $message = "";
    $status_type = "error";

    try {
        $conn->beginTransaction();

        switch ($action) {
            case 'activate':
                $stmt = $conn->prepare("UPDATE users SET status = 'active' WHERE id = :id");
                $stmt->bindParam(':id', $user_id, PDO::PARAM_INT);
                $stmt->execute();
                if ($stmt->rowCount() > 0) {
                    $message = "User activated successfully.";
                    $status_type = "success";
                } else {
                    $message = "Failed to activate user or user not found.";
                }
                break;

            case 'deactivate':
                $stmt = $conn->prepare("UPDATE users SET status = 'inactive' WHERE id = :id");
                $stmt->bindParam(':id', $user_id, PDO::PARAM_INT);
                $stmt->execute();
                if ($stmt->rowCount() > 0) {
                    $message = "User deactivated successfully.";
                    $status_type = "success";
                } else {
                    $message = "Failed to deactivate user or user not found.";
                }
                break;

            case 'delete':
                // For a full system, you'd need to handle cascade deletes (e.g., related quotes, projects, reviews)
                // For now, let's just delete the user.
                $stmt = $conn->prepare("DELETE FROM users WHERE id = :id");
                $stmt->bindParam(':id', $user_id, PDO::PARAM_INT);
                $stmt->execute();
                if ($stmt->rowCount() > 0) {
                    $message = "User deleted successfully. Note: Related data might still exist."; // Improve with actual cascade
                    $status_type = "success";
                } else {
                    $message = "Failed to delete user or user not found.";
                }
                break;

            default:
                $message = "Invalid action specified.";
                break;
        }

        $conn->commit();

    } catch (PDOException $e) {
        $conn->rollBack();
        $message = "Database error: " . $e->getMessage();
    }

    header("Location: manage_users.php?status={$status_type}&message=" . urlencode($message));
    exit();

} else {
    header("Location: manage_users.php?status=error&message=Invalid request.");
    exit();
}
?>