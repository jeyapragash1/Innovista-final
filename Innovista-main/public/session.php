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
 * @param mixed $rawImagePath The raw image path from the database (can be string, array, or null).
 * @return string The correctly formatted URL for the <img> tag.
 */
function getImageSrc(mixed $rawImagePath): string {
    // --- Step 1: Ensure $imagePath is a string at all costs ---
    $imagePath = ''; // Initialize as an empty string

    if (is_string($rawImagePath)) {
        $imagePath = $rawImagePath;
    } elseif ($rawImagePath === null) {
        // null is a common value for empty DB fields, treat as empty string
        $imagePath = '';
    } elseif (is_array($rawImagePath) || is_object($rawImagePath)) {
        // This is the problematic case: an array or object was passed.
        // Log this to find where the bad data is coming from.
        error_log("getImageSrc received unexpected array/object type for imagePath: " . var_export($rawImagePath, true));
        // Return a specific error placeholder, or just the generic one
        return htmlspecialchars('assets/images/error_placeholder.jpg'); // Make sure this file exists
    } else {
        // Handle other unexpected types (e.g., boolean, int) by casting to string
        error_log("getImageSrc received unexpected non-string, non-array, non-null type: " . gettype($rawImagePath) . " value: " . var_export($rawImagePath, true));
        $imagePath = (string) $rawImagePath;
    }

    // --- Step 2: Handle empty path after type conversion ---
    if (empty($imagePath)) {
        return htmlspecialchars('assets/images/placeholder.jpg'); // Return a generic placeholder if path is empty
    }

    // --- Step 3: Determine if it's a URL or relative path ---
    if (filter_var($imagePath, FILTER_VALIDATE_URL)) {
        // It's a full URL, use it directly
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
        // This is where the concatenation happens: $base_url_prefix . $imagePath
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