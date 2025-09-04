<?php
// Start the session if it hasn't been started already.
// This should be one of the very first things your script does.
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// --- SIMULATION FOR TESTING ---
// To test the "logged in" state, you can temporarily uncomment these lines.
// In your real login script, you will set these after a successful login.
// $_SESSION['user_id'] = 123; 
// $_SESSION['user_type'] = 'customer';
// ------------------------------

/**
 * A simple helper function to check if the user is logged in.
 * This makes the code in your HTML cleaner and easier to read.
 * @return bool True if the user is logged in, false otherwise.
 */
function isUserLoggedIn() {
    return isset($_SESSION['user_id']);
}
?>