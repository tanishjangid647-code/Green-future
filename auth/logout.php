<?php
require_once __DIR__ . '/../config/helpers.php';
log_activity("Logged out of the system");
session_unset();
session_destroy();
session_start();
set_flash('success', 'You have been logged out successfully.');
header('Location: ' . base_url('index.php'));
exit;
?>
