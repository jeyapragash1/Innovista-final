<?php
// C:\xampp1\htdocs\Innovista-final\Innovista-main\public\session.php

// Start the session if it hasn't been started already.
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// --- Helper functions for user authentication status ---

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

/**
 * Determines the correct image source URL based on whether it's an external URL
 * or a relative path to an internally uploaded file.
 * Automatically adjusts path for admin panel vs public pages.
 *
 * @param string $imagePath The raw image path from the database.
 * @return string The correctly formatted URL for the <img> tag.
 */
function getImageSrc(string $imagePath): string {
    // Basic validation for an empty path
    if (empty($imagePath)) {
        return htmlspecialchars('assets/images/placeholder.jpg'); // Return a generic placeholder if path is empty
    }

    // Check if the path is a full URL (starts with http/https)
    if (filter_var($imagePath, FILTER_VALIDATE_URL)) {
        return htmlspecialchars($imagePath);
    } else {
        // It's a relative path. Assume it's relative to the 'public' directory from the web server's perspective.
        // We need to determine if the current script is being executed from the 'admin' folder.
        $base_url_prefix = '';
        // Check if the current script path contains '/admin/'
        // $_SERVER['SCRIPT_NAME'] gives the URL path, e.g., '/Innovista-final/Innovista-main/admin/some_page.php'
        if (str_contains($_SERVER['SCRIPT_NAME'], '/admin/')) {
            $base_url_prefix = '../public/'; // From admin/ to public/
        }
        // If not in admin, then it's a public page, path is already relative to public/
        return htmlspecialchars($base_url_prefix . $imagePath);
    }
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