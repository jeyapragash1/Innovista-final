<?php 
    $pageTitle = 'Login';
    require_once __DIR__ . '/../public/session.php'; // Correct path to session.php
    require_once __DIR__ . '/../handlers/flash_message.php'; // Include flash message functions

    // If user is already logged in, redirect them
    if (isUserLoggedIn()) {
        $userRole = getUserRole();
        if ($userRole === 'admin') {
            header("Location: ../admin/admin_dashboard.php");
        } elseif ($userRole === 'provider') {
            header("Location: provider_dashboard.php");
        } else { // customer or unknown
            header("Location: customer_dashboard.php");
        }
        exit();
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle); ?> - Innovista</title>
    
    <link rel="stylesheet" href="assets/css/main.css">
    <link rel="stylesheet" href="assets/css/login.css"> <!-- Your existing login styles -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="login-page-wrapper">
        <div class="login-container">
            <div class="login-form-side">
                <a href="index.php" class="home-link"><i class="fas fa-arrow-left"></i> Back to Home</a>
                
                <form id="loginForm" method="POST" action="../handlers/handle_login.php" autocomplete="off">
                    <h2 class="form-title">Welcome Back</h2>
                    
                    <!-- Container for server messages -->
                    <div class="flash-message-container">
                        <?php display_flash_message(); ?>
                    </div>

                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <div class="input-wrapper">
                            <i class="fas fa-envelope input-icon"></i>
                            <input type="email" id="email" name="email" placeholder="you@example.com" required value="<?php echo htmlspecialchars($_SESSION['login_data']['email'] ?? ''); unset($_SESSION['login_data']['email']); ?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <div class="input-wrapper">
                            <i class="fas fa-lock input-icon"></i>
                            <input type="password" id="password" name="password" placeholder="••••••••" required>
                        </div>
                    </div>
                    <div class="form-actions">
                        <a href="#" class="forgot-password-link">Forgot Password?</a>
                    </div>
                    <button type="submit" class="btn btn-primary login-btn">Login</button>
                    
                    <div class="form-footer">
                        Don't have an account? <a href="signup.php">Sign up</a>
                    </div>
                </form>
            </div>
            
            <div class="login-welcome-side">
                <div class="welcome-overlay">
                    <h1 class="welcome-title">Innovista</h1>
                    <p class="welcome-subtitle">Sign in to access your account and continue transforming spaces.</p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>