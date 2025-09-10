<?php
require_once '../config/session.php';
require_once '../config/Database.php';
require_once '../classes/NotificationManager.php';
protectPage('customer');

header('Content-Type: application/json');

function sendError($message, $code = 400) {
    http_response_code($code);
    echo json_encode(['success' => false, 'message' => $message]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendError('Invalid request method', 405);
}

try {
    $quotation_id = filter_var($_POST['quotation_id'] ?? 0, FILTER_VALIDATE_INT);
    $action = strtolower(trim($_POST['action'] ?? ''));

    if (!$quotation_id || $quotation_id <= 0) {
        sendError('Invalid quotation ID');
    }

    if (!in_array($action, ['confirm', 'cancel'])) {
        sendError('Invalid action');
    }

    $db = (new Database())->getConnection();

    // Verify quotation belongs to current customer
    $stmt = $db->prepare('SELECT * FROM quotations WHERE id = :id AND customer_id = :customer_id');
    $stmt->execute([
        ':id' => $quotation_id,
        ':customer_id' => $_SESSION['user_id']
    ]);
    $quotation = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$quotation) {
        sendError('Unauthorized access or quotation not found', 403);
    }

    $db->beginTransaction();

    // Update quotation status
    $stmt = $db->prepare('UPDATE quotations SET status = :status, updated_at = NOW() WHERE id = :id');
    $stmt->execute([
        ':status' => $action === 'confirm' ? 'Booked' : 'Cancelled',
        ':id' => $quotation_id
    ]);

    // Send notification
    $notificationManager = new NotificationManager($db);
    if ($action === 'confirm') {
        $notificationManager->notifyQuotationAccepted(
            $quotation['provider_id'],
            $_SESSION['user_id'],
            $quotation_id,
            $quotation['service_type']
        );
    } else {
        $reason = $_POST['reason'] ?? '';
        $notificationManager->notifyQuotationRejected(
            $quotation['provider_id'],
            $_SESSION['user_id'],
            $quotation_id,
            $quotation['service_type'],
            $reason
        );
    }

    $db->commit();

    echo json_encode([
        'success' => true,
        'message' => 'Quotation has been ' . ($action === 'confirm' ? 'accepted' : 'rejected') . ' successfully'
    ]);

} catch (PDOException $e) {
    if ($db->inTransaction()) $db->rollBack();
    error_log("Database error: " . $e->getMessage());
    sendError('A database error occurred', 500);
} catch (Exception $e) {
    if ($db->inTransaction()) $db->rollBack();
    error_log("Error: " . $e->getMessage());
    sendError($e->getMessage(), 500);
}
