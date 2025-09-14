<?php
// user_actions.php
require_once 'admin_header.php'; // Ensures admin is logged in
require_once '../config/Database.php';
require_once '../public/session.php'; // For getImageSrc to unlink files

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
                // --- Comprehensive Deletion of Dependent Records FIRST ---

                // 1. Get user's profile image path before potentially deleting the user record
                $old_profile_image_stmt = $conn->prepare("SELECT profile_image_path, role FROM users WHERE id = :id");
                $old_profile_image_stmt->bindParam(':id', $user_id, PDO::PARAM_INT);
                $old_profile_image_stmt->execute();
                $user_info = $old_profile_image_stmt->fetch(PDO::FETCH_ASSOC);
                $old_profile_image_path = $user_info['profile_image_path'] ?? null;
                $user_role = $user_info['role'] ?? null;

                if (!$user_info) {
                    $message = "User not found for deletion.";
                    break;
                }

                // Identify all custom_quotation IDs where this user is involved (as customer or provider)
                $stmt_cq_ids = $conn->prepare("SELECT id FROM custom_quotations WHERE customer_id = :user_id OR provider_id = :user_id");
                $stmt_cq_ids->bindParam(':user_id', $user_id, PDO::PARAM_INT);
                $stmt_cq_ids->execute();
                $custom_quotation_ids = $stmt_cq_ids->fetchAll(PDO::FETCH_COLUMN);

                // Identify all quotation IDs where this user is involved (as customer or provider)
                $stmt_q_ids = $conn->prepare("SELECT id FROM quotations WHERE customer_id = :user_id OR provider_id = :user_id");
                $stmt_q_ids->bindParam(':user_id', $user_id, PDO::PARAM_INT);
                $stmt_q_ids->execute();
                $quotation_ids = $stmt_q_ids->fetchAll(PDO::FETCH_COLUMN);


                // --- DELETE FROM LEAF TABLES FIRST ---

                // a) Delete from project_updates linked to projects from custom_quotations involving this user
                if (!empty($custom_quotation_ids)) {
                    $cq_placeholders = implode(',', array_fill(0, count($custom_quotation_ids), '?'));
                    $stmt_project_ids = $conn->prepare("SELECT id FROM projects WHERE quotation_id IN ($cq_placeholders)");
                    $stmt_project_ids->execute($custom_quotation_ids);
                    $project_ids = $stmt_project_ids->fetchAll(PDO::FETCH_COLUMN);

                    if (!empty($project_ids)) {
                        $p_placeholders = implode(',', array_fill(0, count($project_ids), '?'));
                        // Unlink images from project_updates
                        $stmt_pu_images = $conn->prepare("SELECT image_path FROM project_updates WHERE project_id IN ($p_placeholders) AND image_path IS NOT NULL");
                        $stmt_pu_images->execute($project_ids);
                        $pu_image_paths = $stmt_pu_images->fetchAll(PDO::FETCH_COLUMN);
                        foreach ($pu_image_paths as $img_path) {
                            if (!filter_var($img_path, FILTER_VALIDATE_URL) && file_exists('../public/' . $img_path)) {
                                unlink('../public/' . $img_path);
                            }
                        }
                        $stmt = $conn->prepare("DELETE FROM project_updates WHERE project_id IN ($p_placeholders)");
                        $stmt->execute($project_ids);
                    }
                }
                // Also delete project_updates made by this user, regardless of project origin
                $stmt = $conn->prepare("DELETE FROM project_updates WHERE user_id = :user_id");
                $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT); $stmt->execute();


                // b) Delete payments linked to custom_quotations involving this user
                if (!empty($custom_quotation_ids)) {
                    $cq_placeholders = implode(',', array_fill(0, count($custom_quotation_ids), '?'));
                    $stmt = $conn->prepare("DELETE FROM payments WHERE quotation_id IN ($cq_placeholders)");
                    $stmt->execute($custom_quotation_ids);
                }

                // c) Delete disputes where this user is reported_by or reported_against,
                //    or disputes linked to custom_quotations involving this user.
                //    It's simplest to just delete all disputes where this user is involved in any capacity.
                $stmt = $conn->prepare("DELETE FROM disputes WHERE reported_by_id = :user_id OR reported_against_id = :user_id");
                $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT); $stmt->execute();
                // Also, if disputes are directly linked to custom_quotation_id, handle those.
                // Assuming disputes.quotation_id is custom_quotations.id.
                if (!empty($custom_quotation_ids)) {
                    $cq_placeholders = implode(',', array_fill(0, count($custom_quotation_ids), '?'));
                    $stmt = $conn->prepare("DELETE FROM disputes WHERE quotation_id IN ($cq_placeholders)");
                    $stmt->execute($custom_quotation_ids);
                }

                // d) Delete projects linked to custom_quotations involving this user
                if (!empty($custom_quotation_ids)) {
                    $cq_placeholders = implode(',', array_fill(0, count($custom_quotation_ids), '?'));
                    $stmt = $conn->prepare("DELETE FROM projects WHERE quotation_id IN ($cq_placeholders)");
                    $stmt->execute($custom_quotation_ids);
                }
                
                // e) Delete reviews where this user is customer_id or provider_id
                $stmt = $conn->prepare("DELETE FROM reviews WHERE customer_id = :user_id OR provider_id = :user_id");
                $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT); $stmt->execute();
                
                // f) Delete custom_quotations where this user is customer_id or provider_id
                if (!empty($custom_quotation_ids)) {
                    $cq_placeholders = implode(',', array_fill(0, count($custom_quotation_ids), '?'));
                    $stmt = $conn->prepare("DELETE FROM custom_quotations WHERE id IN ($cq_placeholders)");
                    $stmt->execute($custom_quotation_ids);
                }
                
                // g) Delete quotations where this user is customer_id or provider_id
                if (!empty($quotation_ids)) {
                    $q_placeholders = implode(',', array_fill(0, count($quotation_ids), '?'));
                    $stmt = $conn->prepare("DELETE FROM quotations WHERE id IN ($q_placeholders)");
                    $stmt->execute($quotation_ids);
                }

                // --- PROVIDER-SPECIFIC DELETIONS ---
                // Only if the user being deleted is a provider
                if ($user_role === 'provider') {
                    // h) Delete from provider_availability
                    $stmt = $conn->prepare("DELETE FROM provider_availability WHERE provider_id = :user_id");
                    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT); $stmt->execute();
                    // i) Delete from service
                    $stmt = $conn->prepare("DELETE FROM service WHERE provider_id = :user_id");
                    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT); $stmt->execute();
                    // j) Delete from portfolio_items (and associated files)
                    $stmt_portfolio_images = $conn->prepare("SELECT image_path FROM portfolio_items WHERE provider_id = :user_id AND image_path IS NOT NULL");
                    $stmt_portfolio_images->bindParam(':user_id', $user_id, PDO::PARAM_INT); $stmt_portfolio_images->execute();
                    $portfolio_image_paths = $stmt_portfolio_images->fetchAll(PDO::FETCH_COLUMN);
                    foreach ($portfolio_image_paths as $img_path) {
                        if (!filter_var($img_path, FILTER_VALIDATE_URL) && file_exists('../public/' . $img_path)) {
                            unlink('../public/' . $img_path);
                        }
                    }
                    $stmt = $conn->prepare("DELETE FROM portfolio_items WHERE provider_id = :user_id");
                    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT); $stmt->execute();
                }

                // --- FINALLY, DELETE THE USER ---
                $stmt_user = $conn->prepare("DELETE FROM users WHERE id = :user_id");
                $stmt_user->bindParam(':user_id', $user_id, PDO::PARAM_INT);
                $stmt_user->execute();
                
                if ($stmt_user->rowCount() > 0) {
                    // Unlink profile image if it was a local upload
                    if ($old_profile_image_path && 
                        !filter_var($old_profile_image_path, FILTER_VALIDATE_URL) &&
                        $old_profile_image_path !== 'assets/images/default-avatar.jpg' &&
                        file_exists('../public/' . $old_profile_image_path)) {
                        unlink('../public/' . $old_profile_image_path);
                    }
                    $message = "User and all associated data deleted successfully.";
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
        $message = "Database error during deletion: " . $e->getMessage();
        error_log("User Action Error: " . $e->getMessage());
    }

    header("Location: manage_users.php?status={$status_type}&message=" . urlencode($message));
    exit();

} else {
    header("Location: manage_users.php?status=error&message=Invalid request.");
    exit();
}