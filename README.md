# Siqnode: Secure, Minimalistic Chat Platform

## Overview
**Siqnode** is a lightweight, privacy-focused chat platform designed to offer a secure and visually appealing communication experience. Built with **AES-256-CBC** encryption, it emphasizes privacy while maintaining a clean and intuitive user interface. Users can share text messages, media, and customize chat colors, while administrators can securely manage different chat spaces.

## Features
- **AES-256-CBC Encryption** for message privacy.
- **Customizable Colors** for chat bubbles.
- **Upload and Preview Media** including images, videos, and GIFs.
- **Support for File Encryption** to enhance data security.
- **Responsive Design** for mobile and desktop experiences.
- **Real-time updates using AJAX**.

## Directory Structure

```plaintext
Siqnode/
├── assets/
│   ├── icon.png
│   ├── icon.ico
├── css/
│   ├── styles.css
├── fonts/
│   ├── neontubes-webfont.ttf
├── js/
│   └── script.js
├── uploads/
├── comments.json
├── config.php
├── favicon.ico
├── fetch_comments.php
├── functions.php
├── index.php
├── layout.php
├── login.php
├── logout.php
```


## Directory & File Descriptions

### **assets/**
Contains graphical assets such as the icons used in the application (`icon.png`, `icon.ico`).

### **css/**
Contains stylesheets that define the visual appearance of Siqnode.
- **`styles.css`**: The main CSS file for the chat application. Defines the layout, colors, animations, and styles for various elements like chat bubbles, buttons, modals, and the entire user interface.

### **fonts/**
Contains the custom font files used in Siqnode.
- **`neontubes-webfont.ttf`**: A neon-style font used in the login page for aesthetic appeal.

### **js/**
Contains JavaScript files responsible for client-side functionality.
- **`script.js`**: Handles interactions on the front end, such as fetching comments via AJAX, updating the chat interface, and implementing dynamic elements like media modals, color selection, and real-time updates.

### **uploads/**
A directory where uploaded files (such as images or videos) are stored. This folder is automatically created if it doesn't exist.

### **config.php**
Contains configuration and initialization code, such as session handling, timezone setting, and defining available colors.

### **comments.json**
The JSON file where encrypted chat messages are stored. Messages are encrypted and decrypted using the configured encryption key.

### **fetch_comments.php**
A PHP script responsible for fetching comments via AJAX requests. It verifies user authentication and checks for direct access attempts.

### **functions.php**
Contains helper functions used throughout the project, including encryption and decryption of messages, link conversion, and other utilities.

### **index.php**
The main entry point for the chat application. It handles user authentication, color selection, comment submission, and includes the HTML layout.

### **layout.php**
Defines the structure of the HTML page, including the header, chat area, color selection, comment form, and modal for viewing media.

### **login.php**
A login page for user authentication. It includes a neon effect for the title and a password input field.

### **logout.php**
Handles the logout logic by destroying the session and redirecting the user to the login page.

## Project Workflow

- **Login**: Users must authenticate themselves using a password, which is validated using PHP’s password_verify function.
- **Color Selection**: Users can select a color to differentiate their messages. The chosen color is stored in a session.
- **Chat**: Users can send messages, which are stored in `comments.json` after being encrypted with AES-256-CBC. The messages are displayed in a responsive chat interface.
- **File Uploads**: Users can upload images or videos, which are stored in the `uploads/` directory and previewed in the chat.
- **AJAX Fetching**: New messages are fetched every 2 seconds to ensure a real-time experience.
- **Logout**: Users can securely log out, destroying their session and redirecting them to the login page.
