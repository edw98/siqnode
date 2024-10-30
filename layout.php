<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Siqnode Chat</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/styles.css">
    <link rel="manifest" href="/manifest.json">
    <link href="https://fonts.googleapis.com/css2?family=Roboto+Mono:wght@400;700&display=swap" rel="stylesheet">

</head>
<body>
    <div class="container">
        <!-- ========== HEADER: Logout & Clear Chat ========== -->
        <div class="header">
            <form method="POST" style="margin: 0;">
                <input type="hidden" name="clear_chat" value="1">
                <button type="submit">Clear Chat</button>
            </form>
            <form action="logout.php" method="post" style="margin: 0;">
                <button type="submit">Logout</button>
            </form>
        </div>
        <!-- ========== CHAT AREA ========== -->
        <div class="chat-container" id="chat-container">
            <div class="comments" id="comments">
                <!-- Comments loaded via AJAX -->
            </div>
        </div>
        <!-- ========== FOOTER: Color Selection & Comment Form ========== -->
        <div class="footer">
            <form method="POST" class="color-selection-form">
                <?php global $colors; foreach ($colors as $name => $hex): ?>
                    <button type="submit" name="color_select" value="<?= $name ?>" class="color-button <?= $name ?> <?= $_SESSION['selected_color'] === $name ? 'selected' : '' ?>"></button>
                <?php endforeach; ?>
                    <button id="reload-btn" type="button" class="reload-button">Reload</button> <!-- New Reload Button -->
            </form>
            <form method="POST" enctype="multipart/form-data" class="comment-form">
                <textarea name="content" placeholder="(AES-256-CBC) Type your message..." autocomplete="off"></textarea>
                <label for="file-upload" class="upload-btn" id="upload-btn">+</label>
                <input id="file-upload" type="file" name="files[]" accept="image/*,video/*,.gif" multiple style="display: none;">
                <input type="submit" value="Send">
            </form>
        </div>
    </div>

    <!-- ===================================
        MODAL FOR VIEWING MEDIA
    =================================== -->
    <div id="mediaModal" class="modal">
        <div class="arrow left" onclick="changeMedia(-1)">&#10094;</div>
        <div class="media-content">
            <img id="modal-image" style="display:none;" />
            <video id="modal-video" controls style="display:none;"></video>
        </div>
        <div class="arrow right" onclick="changeMedia(1)">&#10095;</div>
    </div>


    <script src="js/script.js"></script>
</body>
</html>
