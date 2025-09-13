<?php
require_once __DIR__ . '/../config/session.php';
protectPage('customer');

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../classes/Review.php';

$customerId  = $_SESSION['user_id'] ?? null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $bookingId = (int)($_POST['booking_id'] ?? 0); // projects.id
    $rating    = (int)($_POST['rating'] ?? 0);
    $comment   = trim($_POST['comment'] ?? '');

    if ($bookingId <= 0 || $rating < 1 || $rating > 5) {
        header('Location: ../customer/my_projects.php?msg=Invalid+input');
        exit;
    }

    $database = new Database();
    $db = $database->getConnection();
    $reviewObj = new Review($db);

    // Resolve quotation_id and provider_id from booking (project) and ensure completed
    $sql = "SELECT q.id AS quotation_id, q.provider_id
            FROM projects p
            JOIN quotations q ON p.quotation_id = q.id
            WHERE p.id = ? AND q.customer_id = ? AND p.status = 'completed'
            LIMIT 1";
    $stmt = $db->prepare($sql);
    $stmt->execute([$bookingId, $customerId]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$row) {
        header('Location: ../customer/my_projects.php?msg=Invalid+booking+or+not+completed');
        exit;
    }

    $quotationId = (int)$row['quotation_id'];
    $providerId  = (int)$row['provider_id'];

    // Prevent duplicate review (based on quotation_id + customer)
    if ($reviewObj->existsForBooking($quotationId, $customerId)) {
        header('Location: ../customer/my_projects.php?msg=Already+reviewed');
        exit;
    }

    $ok = $reviewObj->create($quotationId, $customerId, $providerId, $rating, $comment);
    if ($ok) {
        header('Location: ../customer/my_projects.php?msg=Feedback+submitted');
        exit;
    }

    header('Location: ../customer/my_projects.php?msg=Failed+to+submit');
    exit;
}
