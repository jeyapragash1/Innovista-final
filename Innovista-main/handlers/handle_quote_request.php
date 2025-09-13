<?php
require_once '../config/session.php';
require_once '../config/Database.php';
if (session_status() === PHP_SESSION_NONE) { session_start(); }
$isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
error_log('SESSION DEBUG: ' . print_r($_SESSION, true));

// Unified session/customer check
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'customer') {
    // Always return JSON for AJAX requests
    if ($isAjax) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'You must be logged in as a customer to request a quote.']);
        exit;
    } else {
        // For non-AJAX, redirect
        header('Location: ../public/login.php');
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    if ($isAjax) {
        echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
        exit();
    } else {
        header('Location: ../customer/request_quotation.php');
        exit();
    }
}

// Basic validation
if (empty($_POST['service_type']) || empty($_POST['project_description'])) {
    if ($isAjax) {
        echo json_encode(['success' => false, 'message' => 'Service type and project description are required.']);
        exit();
    } else {
        set_flash_message('error', 'Service type and project description are required.');
        header('Location: ../customer/request_quotation.php');
        exit();
    }
}

$customer_id = $_SESSION['user_id'];
$service_type = $_POST['service_type'];
$project_description = $_POST['project_description'];

try {
    $database = new Database();
    $db = $database->getConnection();

    if (empty($_POST['provider_id'])) {
        if ($isAjax) {
            echo json_encode(['success' => false, 'message' => 'No provider selected.']);
            exit();
        } else {
            set_flash_message('error', 'No provider selected.');
            header('Location: ../customer/request_quotation.php');
            exit();
        }
    }
    $provider_id = $_POST['provider_id'];
    // Verify provider exists and is a provider
    $verifyStmt = $db->prepare('SELECT id FROM users WHERE id = :provider_id AND role = "provider"');
    $verifyStmt->bindParam(':provider_id', $provider_id);
    $verifyStmt->execute();
    if (!$verifyStmt->fetch()) {
        if ($isAjax) {
            echo json_encode(['success' => false, 'message' => 'Selected provider is not valid.']);
            exit();
        } else {
            set_flash_message('error', 'Selected provider is not valid.');
            header('Location: ../customer/request_quotation.php');
            exit();
        }
    }


    // Handle photo uploads
    $uploadedPhotoPaths = [];
    if (!empty($_FILES['photos']['name'][0])) {
        $uploadDir = __DIR__ . '/../uploads/quotations/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        foreach ($_FILES['photos']['tmp_name'] as $idx => $tmpName) {
            if ($_FILES['photos']['error'][$idx] === UPLOAD_ERR_OK) {
                $originalName = basename($_FILES['photos']['name'][$idx]);
                $ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
                $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp'];
                if (in_array($ext, $allowed)) {
                    $newName = 'quote_' . uniqid() . '.' . $ext;
                    $dest = $uploadDir . $newName;
                    if (move_uploaded_file($tmpName, $dest)) {
                        $uploadedPhotoPaths[] = 'uploads/quotations/' . $newName;
                    }
                }
            }
        }
    }
    $photosStr = !empty($uploadedPhotoPaths) ? implode(',', $uploadedPhotoPaths) : null;

    $subcategory = isset($_POST['subcategory']) ? $_POST['subcategory'] : '';
    $stmt = $db->prepare("INSERT INTO quotations (customer_id, provider_id, service_type, subcategory, project_description, photos, status, created_at) VALUES (:customer_id, :provider_id, :service_type, :subcategory, :project_description, :photos, :status, NOW())");
    $stmt->bindParam(':customer_id', $customer_id);
    $stmt->bindParam(':provider_id', $provider_id);
    $stmt->bindParam(':service_type', $service_type);
    $stmt->bindParam(':subcategory', $subcategory);
    $stmt->bindParam(':project_description', $project_description);
    $stmt->bindParam(':photos', $photosStr);
    $status = 'Awaiting Quote';
    $stmt->bindParam(':status', $status);

    if ($stmt->execute()) {
        if ($isAjax) {
            echo json_encode(['success' => true, 'message' => 'Your quotation request has been sent to the provider.']);
            exit();
        } else {
            set_flash_message('success', 'Your quotation request has been sent to the provider.');
            $redirectUrl = '../public/serviceprovider.php?service=' . urlencode($service_type);
            if (!empty($_POST['subcategory'])) {
                $redirectUrl .= '&subcategory=' . urlencode($_POST['subcategory']);
            }
            header('Location: ' . $redirectUrl);
            exit();
        }
    } else {
        $errorInfo = $stmt->errorInfo();
        $errorMsg = isset($errorInfo[2]) ? $errorInfo[2] : 'Unknown error.';
        if ($isAjax) {
            echo json_encode(['success' => false, 'message' => 'Could not send request. DB Error: ' . $errorMsg]);
            exit();
        } else {
            set_flash_message('error', 'There was an error submitting your request. DB Error: ' . $errorMsg);
            header('Location: ../customer/request_quotation.php');
            exit();
        }
    }
} catch (PDOException $e) {
    error_log("Quote Request Error: " . $e->getMessage());
    if ($isAjax) {
        echo json_encode(['success' => false, 'message' => 'A database error occurred. Please contact support.']);
        exit();
    } else {
        set_flash_message('error', 'A database error occurred. Please contact support.');
        header('Location: ../customer/request_quotation.php');
        exit();
    }
}
?>