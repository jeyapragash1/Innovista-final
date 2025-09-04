<?php
require_once '../config/session.php';
require_once '../config/Database.php';
require_once '../classes/User.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (empty($_POST['email']) || empty($_POST['password'])) {
        set_flash_message('error', 'Email and password are required.');
        header('Location: ../public/login.php');
        exit();
    }

    $database = new Database();
    $db = $database->getConnection();
    $user = new User($db);

    $loggedInUser = $user->login($_POST['email'], $_POST['password']);

    if ($loggedInUser) {
        // Set session variables
        $_SESSION['user_id'] = $loggedInUser['id'];
        $_SESSION['user_name'] = $loggedInUser['name'];
        $_SESSION['user_role'] = $loggedInUser['role'];

        // Redirect based on role
        switch ($loggedInUser['role']) {
            case 'admin':
                header('Location: ../admin/admin_dashboard.php');
                break;
            case 'provider':
                header('Location: ../provider/provider_dashboard.php');
                break;
            case 'customer':
                header('Location: ../customer/customer_dashboard.php');
                break;
            default:
                header('Location: ../public/index.php'); // Fallback
                break;
        }
        exit();
    } else {
        set_flash_message('error', 'Invalid email or password.');
        header('Location: ../public/login.php');
        exit();
    }
} else {
    header('Location: ../public/login.php');
    exit();
}
?>