<?php
// C:\xampp1\htdocs\Innovista-final\Innovista-main\public\session.php

// Start the session if it hasn't been started already.
// This should be one of the very first things your script does.
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// --- Helper functions for user authentication status ---
// These functions are defined ONCE here and then used across the application.

/**
 * A simple helper function to check if the user is logged in.
 * @return bool True if the user is logged in, false otherwise.
 */
function isUserLoggedIn(): bool {
    return isset($_SESSION['user_id']);
}

/**
 * Returns the user's role from the session.
 * @return string|null The user's role (e.g., 'admin', 'customer', 'provider') or null if not logged in.
 */
function getUserRole(): ?string {
    return $_SESSION['user_role'] ?? null;
}

/**
 * Returns the user's ID from the session.
 * @return int|null The user's ID or null if not logged in.
 */
function getUserId(): ?int {
    return $_SESSION['user_id'] ?? null;
}

// --- SIMULATION FOR TESTING ---
// To test the "logged in" state, you can temporarily uncomment these lines.
// In your real login script, you will set these after a successful login.
// For example, after a successful login, you'd set:
// $_SESSION['user_id'] = 1;
// $_SESSION['user_role'] = 'admin'; // Or 'customer', 'provider'
// $_SESSION['user_name'] = 'Test Admin'; // For displaying welcome messages etc.
// ------------------------------
?>