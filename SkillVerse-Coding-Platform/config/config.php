<?php
// Start Session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Database Credentials
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'skillverse');

// General Config
define('SITE_NAME', 'SkillVerse');
define('SITE_URL', 'http://localhost/SkillVerse-Coding-Platform'); // Adjust as necessary for deployment

// AI Integration Key (can be configured by developer)
define('GEMINI_API_KEY', ''); // Insert Gemini API Key here for real AI assistance

// Error Reporting (development mode)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Helper function to check authentication and roles
function check_auth($allowed_roles = []) {
    if (!isset($_SESSION['user_id'])) {
        header('Location: ' . SITE_URL . '/auth/login.php');
        exit;
    }
    
    if (!empty($allowed_roles) && !in_array($_SESSION['user_role'], $allowed_roles)) {
        // Redirect unauthorized users to their respective dashboards or home
        $_SESSION['error'] = "You are not authorized to access that page.";
        if ($_SESSION['user_role'] === 'admin') {
            header('Location: ' . SITE_URL . '/admin/dashboard.php');
        } elseif ($_SESSION['user_role'] === 'instructor') {
            header('Location: ' . SITE_URL . '/instructor/dashboard.php');
        } else {
            header('Location: ' . SITE_URL . '/student/dashboard.php');
        }
        exit;
    }
}

// Helper to escape HTML output securely
function sanitize($data) {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

// Get user profile pic url helper
function get_avatar_url($filename) {
    if (empty($filename) || $filename === 'default-profile.png' || $filename === 'default-profile.svg') {
        return SITE_URL . '/assets/img/default-profile.svg';
    }
    return SITE_URL . '/uploads/profiles/' . $filename;
}
?>
