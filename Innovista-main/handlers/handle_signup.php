<?php
require_once '../config/session.php';
require_once '../config/Database.php';
require_once '../classes/User.php';

// Check if the form was submitted using POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../public/signup.php'); // This is correct if the handler is in /handlers and signup.php is in /public
    exit();
}

// Get the user type ('customer' or 'provider')
$userType = $_POST['userType'] ?? 'customer';

// Determine which form fields to use based on user type

if ($userType === 'provider') {
    $name = $_POST['providerFullname'] ?? '';
    $email = $_POST['providerEmail'] ?? '';
    $service = $_POST['providerService'] ?? '';
    if (is_array($service)) {
        $service = implode(',', $service);
    }
    $subcategories = isset($_POST['providerSubcategories']) ? $_POST['providerSubcategories'] : [];
    $subcategories_str = implode(',', $subcategories);
    $providerPhone = $_POST['providerPhone'] ?? '';
    $providerAddress = $_POST['providerAddress'] ?? '';
    $providerDetails = $_POST['provider_bio'] ?? '';
    $portfolioFile = '';
    if (isset($_FILES['providerCV']) && $_FILES['providerCV']['error'] === UPLOAD_ERR_OK) {
        $portfolioTmp = $_FILES['providerCV']['tmp_name'];
        $portfolioName = basename($_FILES['providerCV']['name']);
        $portfolioPath = '../uploads/portfolio_' . time() . '_' . $portfolioName;
        if (move_uploaded_file($portfolioTmp, $portfolioPath)) {
            $portfolioFile = $portfolioPath;
        }
    }
} else {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $service = '';
    $customerPhone = $_POST['customerPhone'] ?? '';
    $customerAddress = $_POST['customerAddress'] ?? '';
}

$password = $_POST['password'] ?? '';
$confirm_password = $_POST['confirm_password'] ?? '';

// --- Validation ---

if ($userType === 'provider' && empty($service)) {
    set_flash_message('error', 'Please select a service.');
    header('Location: ../public/signup.php');
    exit();
}

if (empty($name) || empty($email) || empty($password)) {
    set_flash_message('error', 'Please fill in all required fields.');
    header('Location: ../public/signup.php');
    exit();
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    set_flash_message('error', 'Invalid email format.');
    header('Location: ../public/signup.php');
    exit();
}

if (strlen($password) < 8) {
    set_flash_message('error', 'Password must be at least 8 characters long.');
    header('Location: ../public/signup.php');
    exit();
}

if ($password !== $confirm_password) {
    set_flash_message('error', 'Passwords do not match.');
    header('Location: ../public/signup.php');
    exit();
}

// --- Registration Logic ---
$database = new Database();
$db = $database->getConnection();
$user = new User($db);



// Pass phone and address for customer
if ($userType === 'customer') {
    $result = $user->register($name, $email, $password, $userType, '', '', $customerPhone, $customerAddress);
} else {
    $result = $user->register($name, $email, $password, $userType, $service, $subcategories_str, $providerPhone, $providerAddress, $portfolioFile, $providerDetails);
}

if ($result === true) {
    // Registration was successful
    // If provider, insert service details
    if ($userType === 'provider') {
        // Get the provider's user ID
        $stmt = $db->prepare('SELECT id FROM users WHERE email = :email LIMIT 1');
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        $provider = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($provider) {
            $provider_id = $provider['id'];
            $service_stmt = $db->prepare('INSERT INTO service (provider_id, provider_name, provider_email, main_service, subcategories, provider_phone, provider_address, portfolio, provider_bio) VALUES (:provider_id, :provider_name, :provider_email, :main_service, :subcategories, :provider_phone, :provider_address, :portfolio, :provider_bio)');
            $service_stmt->bindParam(':provider_id', $provider_id);
            $service_stmt->bindParam(':provider_name', $name);
            $service_stmt->bindParam(':provider_email', $email);
            $service_stmt->bindParam(':main_service', $service);
            $service_stmt->bindParam(':subcategories', $subcategories_str);
            $service_stmt->bindParam(':provider_phone', $providerPhone);
            $service_stmt->bindParam(':provider_address', $providerAddress);
            $service_stmt->bindParam(':portfolio', $portfolioFile);
            $service_stmt->bindParam(':provider_bio', $providerDetails);
            $service_stmt->execute();
        }
    }
    set_flash_message('success', 'Registration successful! Please log in.');
    header('Location: ../public/login.php');
    exit();
} else {
    // The register method returned an error message (e.g., "Email already exists.")
    set_flash_message('error', $result);
    header('Location: ../public/signup.php');
    exit();
}
?>