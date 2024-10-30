<?php
// fetch_comments.php
require 'config.php';
require 'functions.php';

// Check if the user is authenticated
if (!isset($_SESSION['authenticated']) || $_SESSION['authenticated'] !== true) {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized access.']);
    exit();
}

// Verify if the request is an AJAX call
$requestedWith = $_SERVER['HTTP_X_REQUESTED_WITH'] ?? '';
if (strtolower($requestedWith) !== 'xmlhttprequest') {
    http_response_code(400);
    echo json_encode(['error' => "Oops! Siqnode says you can’t access this page directly."], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit();
}

$encryptionKey = getenv('ENCRYPTION_KEY'); // Load the encryption key from environment variables
$commentsFile = 'comments.json';
$lastTimestamp = isset($_GET['last_timestamp']) ? $_GET['last_timestamp'] : null;

if (file_exists($commentsFile)) {
    $encryptedComments = file_get_contents($commentsFile);
    $comments = json_decode(decryptData($encryptedComments, $encryptionKey), true);

    // Sort comments by timestamp in ascending order to ensure correct order
    usort($comments, function ($a, $b) {
        return strtotime($a['timestamp']) - strtotime($b['timestamp']);
    });

    $newComments = [];
    foreach ($comments as $index => $comment) {
        if ($lastTimestamp && $comment['timestamp'] <= $lastTimestamp) {
            continue; // Skip old comments
        }

        // Determine alignment class based on color
        if ($comment['color'] === 'white') {
            $comment['alignmentClass'] = 'center';
        } else {
            $comment['alignmentClass'] = ($_SESSION['selected_color'] === $comment['color']) ? 'right' : 'left';
        }

        $newComments[] = $comment;
    }

    // Get the file last modified time
    $fileModifiedTime = filemtime($commentsFile);

    header('Content-Type: application/json');
    echo json_encode(['comments' => $newComments, 'fileModified' => $fileModifiedTime]);
} else {
    echo json_encode(['comments' => [], 'fileModified' => null]);
}
