<?php
// config.php
// ===================================
//        SESSION & CONFIGURATION
// ===================================
// Start the session and set the timezone to Belgrade
session_start();
date_default_timezone_set('Europe/Belgrade');

// Define available colors for messages
$colors = [
    'green' => '#00FF00',
    'cyan' => '#26bcc9',
    'yellow' => '#e0cf14',
    'magenta' => '#FF00FF',
    'white' => '#FFFFFF'
];

// Default color selection if not set
if (!isset($_SESSION['selected_color'])) {
    $_SESSION['selected_color'] = 'green';
}
?>
