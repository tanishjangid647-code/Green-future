<?php
// Green Future - Database Configuration

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'green_future');

try {
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];
    
    $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
} catch (PDOException $e) {
    // If database doesn't exist yet, attempt auto connection to MySQL server for diagnostic message
    try {
        $pdo_root = new PDO("mysql:host=" . DB_HOST . ";charset=utf8mb4", DB_USER, DB_PASS);
        $pdo_root->exec("CREATE DATABASE IF NOT EXISTS `" . DB_NAME . "` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");
        $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
    } catch (PDOException $e_root) {
        die("<div style='padding:20px; font-family:sans-serif; background:#ffebee; color:#c62828; border:1px solid #ef9a9a; border-radius:8px; margin:20px;'>
            <h2>Database Connection Error</h2>
            <p>Could not connect to MySQL server. Please make sure MySQL is running in XAMPP Control Panel.</p>
            <p><strong>Error:</strong> " . htmlspecialchars($e->getMessage()) . "</p>
            <p>Import <code>database/schema.sql</code> into phpMyAdmin to set up tables automatically.</p>
        </div>");
    }
}
?>
