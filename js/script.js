let isAtBottom = true;
const chatContainer = document.getElementById('chat-container');
const commentsDiv = document.getElementById('comments');
const messageInput = document.querySelector('.comment-form textarea[name="content"]');
let lastTimestamp = null;
let lastFileModified = null; // Track the last modified timestamp of the comments file
let mediaList = [];
let currentIndex = -1;
let existingCommentIds = new Set(); // Track existing comment timestamps/IDs to prevent duplicates

// Detect if the user is on a mobile device
const isMobile = /iPhone|iPad|iPod|Android/i.test(navigator.userAgent);

// Function to make URLs in the comment text clickable
function makeLinksClickable(text) {
    const urlPattern = /(\b(https?:\/\/[-A-Z0-9+&@#\/%?=~_|!:,.;]*[-A-Z0-9+&@#\/%=~_|])|\b(www\.[-A-Z0-9+&@#\/%?=~_|!:,.;]*[-A-Z0-9+&@#\/%=~_|]))/ig;
    
    return text.replace(urlPattern, function(url) {
        let hyperlink = url;
        if (!hyperlink.startsWith('http')) {
            hyperlink = 'http://' + hyperlink;
        }
        return `<a href="${hyperlink}" target="_blank">${url}</a>`;
    });
}

// Fetch comments via AJAX and append only new ones
async function fetchComments(fetchAll = false) {
    let url = 'fetch_comments.php';
    
    if (!fetchAll && lastTimestamp) {
        url += `?last_timestamp=${encodeURIComponent(lastTimestamp)}`;
    }

    try {
        const response = await fetch(url, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });
        if (!response.ok) throw new Error('Network response was not ok');

        const data = await response.json();

        // If file has changed, refresh all comments
        if (lastFileModified !== data.fileModified) {
            commentsDiv.innerHTML = ''; // Clear all comments
            lastTimestamp = null; // Reset last timestamp to fetch all new comments
            existingCommentIds.clear(); // Clear the set of existing comment IDs
            lastFileModified = data.fileModified; // Update file modified timestamp
            fetchComments(true); // Fetch all comments again
            return;
        }

        // Sort the comments by timestamp to ensure correct order before appending
        data.comments.sort((a, b) => new Date(a.timestamp) - new Date(b.timestamp));

        // Loop through the sorted comments and append them
        let hasOrderIssue = false;
        data.comments.forEach(comment => {
            // Use a unique identifier or timestamp to track each comment
            const commentId = `${comment.timestamp}-${comment.color}-${comment.content}`;

            // Skip appending if the comment already exists
            if (existingCommentIds.has(commentId)) return;

            // Mark the comment as processed
            existingCommentIds.add(commentId);

            // Check if the current comment should be appended at the end
            const lastCommentInDOM = commentsDiv.lastElementChild;
            if (lastCommentInDOM && new Date(lastCommentInDOM.dataset.timestamp) > new Date(comment.timestamp)) {
                hasOrderIssue = true;
            }

            const commentElement = createCommentElement(comment);
            commentsDiv.appendChild(commentElement);

            // Update last timestamp based on the most recent comment fetched
            if (!lastTimestamp || comment.timestamp > lastTimestamp) {
                lastTimestamp = comment.timestamp;
            }
        });

        // If there is an order issue, clear and fetch all comments again
        if (hasOrderIssue) {
            commentsDiv.innerHTML = ''; // Clear all comments
            lastTimestamp = null;
            existingCommentIds.clear();
            fetchComments(true); // Fetch all comments again
            return;
        }

        // Populate the media list after new comments are appended
        populateMediaList();

        if (isAtBottom) scrollChatToBottom();
    } catch (error) {
        console.error('Error fetching comments:', error);
    }
}

// Create a comment element based on the comment data
function createCommentElement(comment) {
    const commentDiv = document.createElement('div');
    commentDiv.classList.add('comment', comment.color, comment.alignmentClass);

    const contentP = document.createElement('p');
    contentP.innerHTML = makeLinksClickable(comment.content);
    commentDiv.appendChild(contentP);

    // Add file previews if any
    if (comment.files) {
        comment.files.forEach(filePath => {
            if (filePath.endsWith('.gif') || filePath.match(/\.(jpg|jpeg|png|webp)$/i)) {
                const img = document.createElement('img');
                img.src = filePath;
                img.classList.add('preview');
                img.style.maxWidth = '250px';
                img.style.maxHeight = '250px';
                commentDiv.appendChild(img);
            } else if (filePath.match(/\.(mp4|webm)$/i)) {
                const video = document.createElement('video');
                video.src = filePath;
                video.classList.add('preview');
                video.controls = true;
                video.style.maxWidth = '250px';
                video.style.maxHeight = '250px';
                commentDiv.appendChild(video);
            }
        });
    }

    // Add timestamp
    const timestampP = document.createElement('p');
    timestampP.classList.add('timestamp');
    timestampP.innerHTML = `${comment.timestamp} <span class="encrypted-check">&#10004;&#10004;</span>`;
    commentDiv.appendChild(timestampP);

    return commentDiv;
}

// Populate the media list based on the new comments fetched
function populateMediaList() {
    mediaList = [];
    const mediaElements = commentsDiv.querySelectorAll('img.preview, video.preview');

    mediaElements.forEach(media => {
        const type = media.tagName.toLowerCase() === 'img' ? 'image' : 'video';
        mediaList.push({ type, src: media.src });

        // Handle click events for images and videos
        media.addEventListener('click', (event) => {
            event.preventDefault();
            openModal(type, media.src);
        });

        // Handle touchstart event specifically for videos to open the modal
        if (type === 'video') {
            media.addEventListener('touchstart', (event) => {
                if (modal.style.display !== 'flex') {
                    event.preventDefault();
                    openModal(type, media.src);
                }
            });
        }
    });
}

// Initial fetch of all comments and scroll to bottom
window.addEventListener('load', () => {
    lastTimestamp = null;
    currentIndex = -1;
    fetchComments(true);
    messageInput.focus();
    scrollChatToBottom();
});

// Ensure modal is hidden on load
const modal = document.getElementById('mediaModal');
modal.style.display = 'none';

// Periodically fetch new comments (every 2 seconds)
setInterval(() => {
    fetchComments(false);
}, 2000);

// Scroll to bottom
function scrollChatToBottom() {
    chatContainer.scrollTop = chatContainer.scrollHeight;
}

// Track scroll position to determine if user is at bottom
chatContainer.addEventListener('scroll', () => {
    const threshold = 50;
    const position = chatContainer.scrollTop + chatContainer.clientHeight;
    const height = chatContainer.scrollHeight;
    isAtBottom = (height - position) <= threshold;
});

// Handle form submission via AJAX
const commentForm = document.querySelector('.comment-form');

// Add event listener to allow "Shift + Enter" to insert a new line, and "Enter" to submit the form
messageInput.addEventListener('keydown', (event) => {
    if (event.key === 'Enter') {
        if (event.shiftKey) {
            // Prevent default action to avoid form submission
            event.preventDefault();
            // Insert a new line at the cursor position
            const cursorPosition = messageInput.selectionStart;
            messageInput.value = messageInput.value.slice(0, cursorPosition) + '\n' + messageInput.value.slice(cursorPosition);
            // Move the cursor to the new position after the line break
            messageInput.selectionStart = messageInput.selectionEnd = cursorPosition + 1;
        } else {
            // Submit the form when "Enter" is pressed without "Shift"
            event.preventDefault();
            commentForm.requestSubmit();
        }
    }
});

// Adjust the textarea height dynamically
messageInput.addEventListener('input', () => {
    // Reset height to allow for shrinkage if necessary
    messageInput.style.height = "auto"; 
    
    // Calculate new height, limiting to a maximum of 150px
    const newHeight = Math.min(messageInput.scrollHeight, 150); // 150px is the max height
    messageInput.style.height = `${newHeight}px`;
});

commentForm.addEventListener('submit', (e) => {
    e.preventDefault();
    const formData = new FormData(commentForm);

    const xhr = new XMLHttpRequest();
    xhr.open('POST', commentForm.action, true);
    xhr.onload = function() {
        if (this.status === 200) {
            setTimeout(() => {
                fetchComments();
                messageInput.value = ''; // Clear the input
                messageInput.style.height = '40px'; // Reset to initial height after sending message
                fileUpload.value = '';
                uploadBtn.textContent = '+';
                scrollChatToBottom();
            }, 100);
        }
    };
    xhr.send(formData);
});


// Handle file upload button
const fileUpload = document.getElementById('file-upload');
const uploadBtn = document.getElementById('upload-btn');
fileUpload.addEventListener('change', () => {
    const fileCount = fileUpload.files.length;
    uploadBtn.textContent = fileCount > 0 ? fileCount : '+';
});

// Function to open the modal with specific media type and source
function openModal(mediaType, mediaSrc) {
    if (!mediaSrc) return;

    const img = document.getElementById('modal-image');
    const video = document.getElementById('modal-video');

    img.style.display = 'none';
    video.style.display = 'none';

    if (mediaType === 'image') {
        img.src = mediaSrc;
        img.style.display = 'block';
    } else if (mediaType === 'video') {
        video.src = mediaSrc;
        video.style.display = 'block';
    }

    modal.style.display = 'flex';
    currentIndex = mediaList.findIndex(media => media.src === mediaSrc);

    video.replaceWith(video.cloneNode(true));
    const newVideo = document.getElementById('modal-video');

    // Add event listeners only if it's a video
    if (mediaType === 'video') {
        addVideoEventListeners(newVideo);
    }
}

// Function to add event listeners to the video element
function addVideoEventListeners(videoElement) {
    videoElement.addEventListener('click', toggleVideoPlayback);
    videoElement.addEventListener('touchstart', toggleVideoPlayback);
}

// Toggle video playback on click or touch
function toggleVideoPlayback(event) {
    const video = event.target;
    // Check if the modal is already open and prevent default behavior only when interacting with video inside modal
    if (modal.style.display === 'flex') {
        event.preventDefault();
        if (video.paused) {
            video.play();
        } else {
            video.pause();
        }
    }
}

// Function to close the modal
function closeModal() {
    const video = document.getElementById('modal-video');
    video.pause();
    modal.style.display = 'none';
}

// Change media function (left or right)
function changeMedia(direction) {
    let newIndex = currentIndex + direction;

    if (newIndex < 0) newIndex = 0;
    if (newIndex >= mediaList.length) newIndex = mediaList.length - 1;

    currentIndex = newIndex;
    const currentMedia = mediaList[currentIndex];
    if (currentMedia) {
        openModal(currentMedia.type, currentMedia.src);
    }
}

// Close modal when clicking outside of the modal content or pressing Escape
modal.addEventListener('click', (event) => {
    if (event.target === modal) closeModal();
});

// Add keyboard navigation to handle left and right arrows as well as the escape key
window.addEventListener('keydown', (event) => {
    if (modal.style.display === 'flex') {
        if (event.key === 'ArrowLeft') changeMedia(-1);
        if (event.key === 'ArrowRight') changeMedia(1);
        if (event.key === 'Escape') closeModal();
    }
});

// Get the reload button
const reloadBtn = document.getElementById('reload-btn');

// Add a click event listener to the reload button
reloadBtn.addEventListener('click', () => {
    // Change button content to a loading spinner
    reloadBtn.innerHTML = '<span class="loading-spinner"></span>';

    // Simulate a reload process with a timeout (you can replace it with actual fetch logic)
    setTimeout(() => {
        // Clear existing comments to reload everything
        commentsDiv.innerHTML = '';
        lastTimestamp = null;
        existingCommentIds.clear();
        fetchComments(true); // Fetch all comments again

        // Restore the original button content after reloading
        reloadBtn.innerHTML = 'Reload';
    }, 200); // 1 second delay for the reload simulation
});



