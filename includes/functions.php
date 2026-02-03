<?php
/**
 * Khutta Ma Jutta - Core Functions
 * Includes Security, Authentication, and Template Engine helpers.
 */

// -----------------------------------------------------------------------------
// 1. Security & Validation
// -----------------------------------------------------------------------------

/**
 * Escapes HTML characters for output security (XSS Prevention).
 * @param string $string
 * @return string
 */
function h($string) {
    return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
}

/**
 * Generates a CSRF token and stores it in the session.
 * @return string
 */
function generate_csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verifies the POSTed CSRF token against the session token.
 * Terminates execution if invalid.
 */
function verify_csrf_token() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            die('CSRF Validation Failed. Please refresh the page and try again.');
        }
    }
}

// -----------------------------------------------------------------------------
// 2. Authentication & Session
// -----------------------------------------------------------------------------

/**
 * Checks if the user is currently logged in.
 * @return bool
 */
function is_logged_in() {
    return isset($_SESSION['user_id']);
}

/**
 * Enforces login requirement. Redirects to login page if authentication fails.
 */
function require_login() {
    if (!is_logged_in()) {
        header("Location: login.php");
        exit;
    }
}

/**
 * Enforces Admin role requirement.
 */
function require_admin() {
    require_login();
    // Superadmin is also an admin
    if ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'superadmin') {
        die('Access Denied. Administrator privileges required.');
    }
}

/**
 * Checks if current user is superadmin.
 */
function is_superadmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'superadmin';
}

/**
 * Enforces Super Admin requirement.
 */
function require_superadmin() {
    require_login();
    if (!is_superadmin()) {
        die('Access Denied. Super Administrator privileges required.');
    }
}

// -----------------------------------------------------------------------------
// 3. Flash Messages
// -----------------------------------------------------------------------------

/**
 * Sets a flash message for the next request.
 * @param string $type success|danger|warning|info
 * @param string $message
 */
function set_flash($type, $message) {
    $_SESSION['flash'] = [
        'type' => $type,
        'message' => $message
    ];
}

/**
 * Retrieves and clears the current flash message.
 * @return array|null
 */
function get_flash() {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

// -----------------------------------------------------------------------------
// 4. Template Engine (MVC View Render)
// -----------------------------------------------------------------------------

/**
 * Renders a template file within a layout.
 * @param string $templatePath Relative path to template (e.g., 'auth/login')
 * @param array $data Associative array of variables to pass to the view
 * @param string $layout Layout file to use (default: 'layout')
 */
function render_view($templatePath, $data = [], $layout = 'layout') {
    // Extract data variables to local scope
    extract($data);

    // Buffer the Specific View Content
    ob_start();
    $viewFile = __DIR__ . '/../templates/' . $templatePath . '.php';
    if (file_exists($viewFile)) {
        require $viewFile;
    } else {
        echo "<p>Error: View file <code>$templatePath</code> not found.</p>";
    }
    $content = ob_get_clean();

    // Render the Master Layout with the content
    if ($layout) {
        $layoutFile = __DIR__ . '/../templates/' . $layout . '.php';
        if (file_exists($layoutFile)) {
            require $layoutFile;
        } else {
            echo $content; // Fallback if layout missing
            echo "<p>Error: Layout file <code>$layout</code> not found.</p>";
        }
    } else {
        echo $content; // Render without layout (e.g. for AJAX partials)
    }
}
?>
