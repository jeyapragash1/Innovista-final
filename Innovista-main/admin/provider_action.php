<?php
// provider_action.php
require_once 'admin_header.php'; // Ensures admin is logged in
require_once '../config/Database.php';

$db = new Database();
$conn = $db->getConnection();

if (isset($_GET['id']) && isset($_GET['action'])) {
    $provider_id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
    $action = filter_input(INPUT_GET, 'action', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    if (!$provider_id || !is_numeric($provider_id)) {
        header("Location: manage_providers.php?status=error&message=Invalid provider ID.");
        exit();
    }

    $message = "";
    $status_type = "error";

    try {
        $conn->beginTransaction();

        switch ($action) {
            case 'approve':
                $stmt = $conn->prepare("UPDATE users SET provider_status = 'approved', status = 'active' WHERE id = :id AND role = 'provider'");
                $stmt->bindParam(':id', $provider_id, PDO::PARAM_INT);
                $stmt->execute();
                if ($stmt->rowCount() > 0) {
                    $message = "Provider approved and activated successfully.";
                    $status_type = "success";
                } else {
                    $message = "Failed to approve provider or provider not found/is not a provider.";
                }
                break;

            case 'reject':
                $stmt = $conn->prepare("UPDATE users SET provider_status = 'rejected', status = 'inactive' WHERE id = :id AND role = 'provider'");
                $stmt->bindParam(':id', $provider_id, PDO::PARAM_INT);
                $stmt->execute();
                if ($stmt->rowCount() > 0) {
                    $message = "Provider rejected and deactivated successfully.";
                    $status_type = "success";
                } else {
                    $message = "Failed to reject provider or provider not found/is not a provider.";
                }
                break;

            case 'deactivate':
                $stmt = $conn->prepare("UPDATE users SET status = 'inactive', provider_status = 'rejected' WHERE id = :id AND role = 'provider'");
                $stmt->bindParam(':id', $provider_id, PDO::PARAM_INT);
                $stmt->execute();
                if ($stmt->rowCount() > 0) {
                    $message = "Provider deactivated successfully.";
                    $status_type = "success";
                } else {
                    $message = "Failed to deactivate provider or provider not found/is not a provider.";
                }
                break;
            
            case 're_evaluate': // Used for re-evaluating a rejected provider, setting status back to pending
                $stmt = $conn->prepare("UPDATE users SET status = 'active', provider_status = 'pending' WHERE id = :id AND role = 'provider'");
                $stmt->bindParam(':id', $provider_id, PDO::PARAM_INT);
                $stmt->execute();
                if ($stmt->rowCount() > 0) {
                    $message = "Provider re-evaluated to pending status successfully.";
                    $status_type = "success";
                } else {
                    $message = "Failed to re-evaluate provider or provider not found/is not a provider.";
                }
                break;

            case 'delete':
                // Redirect to user_actions.php to handle full cascade deletion for this user_id
                // This consolidates the complex deletion logic in one place.
                header("Location: user_actions.php?id={$provider_id}&action=delete");
                exit();
                break; // This break is technically unreachable but good practice

            default:
                $message = "Invalid action specified.";
                break;
        }

        $conn->commit();

    } catch (PDOException $e) {
        $conn->rollBack();
        $message = "Database error: " . $e->getMessage();
        error_log("Provider Action Error: " . $e->getMessage());
    }

    header("Location: manage_providers.php?status={$status_type}&message=" . urlencode($message));
    exit();

} else {
    header("Location: manage_providers.php?status=error&message=Invalid request.");
    exit();
}