<?php
// Green Future - Helpers & Core Application Logic

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/db.php';

// Base URL generator
function base_url($path = '') {

    // Detect HTTP or HTTPS
    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        ? 'https'
        : 'http';

    // Get current host
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';

    // Get project directory
    $script_dir = str_replace(
        '\\',
        '/',
        dirname($_SERVER['SCRIPT_NAME'])
    );

    // Remove nested folders such as /admin, /user, /volunteer, /auth
    $base_path = $script_dir;

    $folders = [
        '/admin',
        '/user',
        '/volunteer',
        '/auth'
    ];

    foreach ($folders as $folder) {

        if (strpos($base_path, $folder) !== false) {
            $base_path = dirname($base_path);
            break;
        }
    }

    $base_path = rtrim($base_path, '/');

    // Build complete website URL
    $base = $scheme . '://' . $host . $base_path;

    return rtrim($base, '/') . '/' . ltrim($path, '/');
}

// Sanitize string output for XSS protection
function sanitize($data) {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

// Generate CSRF Token
function generate_csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// Verify CSRF Token
function verify_csrf_token($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

// Auth Guards
function is_logged_in() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

function current_user() {
    global $pdo;
    if (!is_logged_in()) return null;
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    return $stmt->fetch();
}

function require_login() {
    if (!is_logged_in()) {
        $_SESSION['flash_error'] = "Please log in to access this page.";
        header("Location: " . base_url('auth/login.php'));
        exit;
    }
}

function require_role($allowed_roles = []) {
    require_login();
    if (!in_array($_SESSION['user_role'], (array)$allowed_roles)) {
        $_SESSION['flash_error'] = "Unauthorized access level.";
        header("Location: " . base_url('index.php'));
        exit;
    }
}

// Activity Logging
function log_activity($action) {
    global $pdo;
    $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
    $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
    $stmt = $pdo->prepare("INSERT INTO activity_logs (user_id, action, ip_address) VALUES (?, ?, ?)");
    $stmt->execute([$user_id, $action, $ip]);
}

// Flash Message helper
function set_flash($type, $message) {
    $_SESSION['flash_' . $type] = $message;
}

function get_flash($type) {
    if (isset($_SESSION['flash_' . $type])) {
        $msg = $_SESSION['flash_' . $type];
        unset($_SESSION['flash_' . $type]);
        return $msg;
    }
    return null;
}
// Automatically generate a suitable campaign image URL
function campaign_image_url($campaign, $width = 600, $height = 350) {

    $title = strtolower($campaign['title'] ?? '');
    $description = strtolower($campaign['description'] ?? '');
    $species = strtolower($campaign['tree_species'] ?? '');
    $city = strtolower($campaign['city'] ?? '');

    $text = $title . ' ' . $description . ' ' . $species . ' ' . $city;

    // Select suitable image category
    if (
        strpos($text, 'mangrove') !== false ||
        strpos($text, 'coastal') !== false ||
        strpos($text, 'marine') !== false
    ) {
        $tags = 'mangrove,forest,trees,nature';
    } elseif (
        strpos($text, 'river') !== false ||
        strpos($text, 'riverbank') !== false ||
        strpos($text, 'wetland') !== false
    ) {
        $tags = 'river,forest,trees,nature';
    } elseif (
        strpos($text, 'school') !== false ||
        strpos($text, 'campus') !== false
    ) {
        $tags = 'school,garden,trees,planting';
    } elseif (
        strpos($text, 'urban') !== false ||
        strpos($text, 'city') !== false ||
        strpos($text, 'air') !== false
    ) {
        $tags = 'urban,trees,greenery,city';
    } elseif (
        strpos($text, 'fruit') !== false ||
        strpos($text, 'mango') !== false ||
        strpos($text, 'guava') !== false
    ) {
        $tags = 'fruit,tree,planting,nature';
    } else {
        $tags = 'tree,forest,planting,nature';
    }

    // lock makes the same campaign keep the same image
    $lock = intval($campaign['id'] ?? 1);

    return "https://loremflickr.com/{$width}/{$height}/{$tags}?lock={$lock}";
}
// Automatically generate a suitable image for gallery/blog content
function content_image_url($content, $width = 600, $height = 350) {

    $title = strtolower($content['title'] ?? '');
    $description = strtolower($content['description'] ?? '');
    $content_text = strtolower($content['content'] ?? '');

    $text = $title . ' ' . $description . ' ' . $content_text;

    // Select suitable image category
    if (
        strpos($text, 'mangrove') !== false ||
        strpos($text, 'coastal') !== false ||
        strpos($text, 'marine') !== false
    ) {
        $tags = 'mangrove,coast,forest,nature';

    } elseif (
        strpos($text, 'river') !== false ||
        strpos($text, 'wetland') !== false
    ) {
        $tags = 'river,trees,forest,nature';

    } elseif (
        strpos($text, 'school') !== false ||
        strpos($text, 'campus') !== false
    ) {
        $tags = 'school,garden,trees,planting';

    } elseif (
        strpos($text, 'climate') !== false ||
        strpos($text, 'carbon') !== false ||
        strpos($text, 'pollution') !== false ||
        strpos($text, 'air') !== false
    ) {
        $tags = 'climate,environment,trees,nature';

    } elseif (
        strpos($text, 'biodiversity') !== false ||
        strpos($text, 'wildlife') !== false
    ) {
        $tags = 'biodiversity,forest,wildlife,nature';

    } elseif (
        strpos($text, 'plant') !== false ||
        strpos($text, 'tree') !== false ||
        strpos($text, 'forest') !== false
    ) {
        $tags = 'tree,forest,planting,nature';

    } else {
        $tags = 'nature,environment,trees,forest';
    }

    // Use content ID so the same item keeps the same image
    $lock = intval($content['id'] ?? 1);

    return "https://loremflickr.com/{$width}/{$height}/{$tags}?lock={$lock}";
}
?>

