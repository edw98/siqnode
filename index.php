<?php
// index.php
require 'config.php';
require 'functions.php';

// ===================================
// Get the path from the URL
// ===================================




// ===================================
//        USER AUTHENTICATION
// ===================================
if (!isset($_SESSION['authenticated']) || $_SESSION['authenticated'] !== true) {
    header('Location: /login.php');
    exit();
}

$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// ===================================
//     HANDLE COLOR SELECTION
// ===================================
if (isset($_POST['color_select']) && array_key_exists($_POST['color_select'], $colors)) {
    $_SESSION['selected_color'] = $_POST['color_select'];
    header('Location: /'); // Redirect to prevent form resubmission
    exit();
}

// ===================================
//      COMMENT SUBMISSION LOGIC
// ===================================
$encryptionKey = getenv('ENCRYPTION_KEY'); // Load the encryption key from environment variables
if ($_SERVER['REQUEST_METHOD'] === 'POST' && (!empty($_POST['content']) || !empty($_FILES['files']['name'][0]))) {
    $content = isset($_POST['content']) ? trim($_POST['content']) : '';
    $color = $_SESSION['selected_color'];
    $files = [];

    // Handle file uploads
    if (!empty($_FILES['files']['name'][0])) {
        $uploadsDir = 'uploads/';
        if (!file_exists($uploadsDir)) {
            mkdir($uploadsDir, 0777, true); // Create uploads directory if not existing
        }

        foreach ($_FILES['files']['name'] as $key => $fileName) {
            $fileTmpPath = $_FILES['files']['tmp_name'][$key];
            $extension = pathinfo($fileName, PATHINFO_EXTENSION);
            $newFileName = uniqid() . '.' . $extension;
            $filePath = $uploadsDir . $newFileName;

            // Move uploaded file to the server
            if (move_uploaded_file($fileTmpPath, $filePath)) {
                $files[] = $filePath; // Store the file path
            }
        }
    }

    // Load existing comments
    $commentsFile = 'comments.json';
    $comments = file_exists($commentsFile) ? json_decode(decryptData(file_get_contents($commentsFile), $encryptionKey), true) : [];

    // Add the new comment
    $comments[] = [
        'content' => $content,
        'color' => $color,
        'files' => $files,
        'timestamp' => date('Y-m-d H:i:s')
    ];

    // Encrypt and save the updated comments
    $encryptedComments = encryptData(json_encode($comments), $encryptionKey);
    file_put_contents($commentsFile, $encryptedComments);
    header('Location: /');
    exit();
}

// ===================================
//     HANDLE CLEAR CHAT ACTION
// ===================================
if (isset($_POST['clear_chat'])) {
    // Define the default message to retain
    $defaultMessage = [
        'content' => '🔒Messages are encrypted with AES-256-CBC.',
        'color' => 'white', // Use 'white' or any color class you prefer
        'files' => [],
        'timestamp' => date('Y-m-d H:i:s') // Current timestamp
    ];

    // Encrypt and save only the default message
    $encryptedComments = encryptData(json_encode([$defaultMessage]), $encryptionKey);
    file_put_contents('comments.json', $encryptedComments);
    header('Location: /');
    exit();
}


// Include the HTML layout
include 'layout.php';
?>
