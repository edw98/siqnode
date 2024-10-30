<?php
// functions.php
// ========================================
// ENCRYPTION/DECRYPTION FUNCTIONS
// ========================================
function encryptData($data, $encryptionKey) {
    $iv = random_bytes(16); // Generate a 16-byte initialization vector (IV)
    $encryptedData = openssl_encrypt($data, 'AES-256-CBC', $encryptionKey, 0, $iv);
    
    // Return the IV concatenated with the encrypted data (IV is required for decryption)
    return base64_encode($iv . $encryptedData);
}

function decryptData($encryptedData, $encryptionKey) {
    $data = base64_decode($encryptedData);
    
    // Extract the first 16 bytes for the IV
    $iv = substr($data, 0, 16);
    
    // Extract the remaining bytes as the encrypted text
    $encryptedText = substr($data, 16);
    
    // Decrypt the encrypted text using the extracted IV
    return openssl_decrypt($encryptedText, 'AES-256-CBC', $encryptionKey, 0, $iv);
}

// Other helper functions
function makeLinksClickable($text) {
    $pattern = '/(https?:\/\/[^\s]+|www\.[^\s]+|[a-zA-Z0-9-]+\.[a-zA-Z]{2,6}[^\s]*)/i';
    return preg_replace_callback($pattern, function($matches) {
        $url = $matches[0];
        if (strpos($url, 'www.') === 0) {
            $url = 'http://' . $url;
        }

        // Check if the URL ends with .gif (case-insensitive)
        if (preg_match('/\.(gif)$/i', $url)) {
            return '<img src="' . htmlspecialchars($url) . '" class="preview gif" style="max-width:100px; max-height:100px; margin:5px;" />';
        } else {
            return '<a href="' . htmlspecialchars($url) . '" target="_blank">' . htmlspecialchars($url) . '</a>';
        }
    }, $text);
}
?>
