<?php
session_start();

// If the user is already authenticated, redirect to the main page
if (isset($_SESSION['authenticated']) && $_SESSION['authenticated'] === true) {
    header('Location: /');
    exit();
}

// Handle login form submission
if (isset($_POST['password'])) {
    $password = $_POST['password'];
    $hashedPassword = '$2y$10$T518a2C.Ww0MR41JqVbV1u0NeiiYI4NznDMmv3fsIHwSacitQz6DO'; // Replace with your hashed password

    if (password_verify($password, $hashedPassword)) {
        session_regenerate_id(true);
        $_SESSION['authenticated'] = true;
        header('Location: /');
        exit();
    } else {
        $error = 'Incorrect password.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - Siqnode</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- Responsive design -->

    <!-- Custom font and neon effect styling -->
    <style>
        @font-face {
            font-family: 'neontubes';
            src: url('/fonts/neontubes-webfont.ttf') format('truetype');
            font-weight: normal;
            font-style: normal;
        }

        html, body {
            margin: 0;
            padding: 0;
            width: 100%;
            height: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: black;
        }

        .container {
            text-align: center;
        }

        .enter {
            font-family: neontubes;
            color: #00FF00; /* Green color */
            font-size: 55px; /* Font size proportional to screen width */
            line-height: 1.2;
            text-shadow:
                4px 4px 0px rgba(0, 128, 0, 0.9), /* Visible drop shadow to the bottom-right */
                0 0 10px rgba(0, 255, 0, 10),    /* Subtle neon glow */
                0 0 15px rgba(0, 255, 0, 0.6);   /* Soft outer glow */
            animation: flicker 1.5s infinite alternate;
        }

        @keyframes flicker {
            0%, 19%, 21%, 23%, 25%, 54%, 56%, 100% {
                text-shadow:
                    4px 4px 0px rgba(0, 128, 0, 0.9), /* Keep the drop shadow strong */
                    0 0 8px rgba(0, 255, 0, 10),    /* Subtle neon glow */
                    0 0 15px rgba(0, 255, 0, 0.6);   /* Outer glow */
            }
            20%, 24%, 55% {
                text-shadow: 
                    2px 2px 0px rgba(0, 128, 0, 0.6); /* Drop shadow remains, glow fades */
            }
        }

        .login-form {
            text-align: center;
            margin-top: 10px;
        }

        .login-form form {
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .login-form input[type="password"],
        .login-form input[type="submit"] {
            background-color: black;
            color: #00FF00;
            padding: 10px;
            margin-bottom: 14px;
            font-size: 1.2em;
            text-align: center;
            width: 100%;
            max-width: 270px;
            box-sizing: border-box;
            border-radius: 5px;
            font-family: monospace;
        }

        .login-form input[type="password"] {
            margin-right: 10px;
            border: 1px solid #FFFFFF; /* White outline */
        }

        .login-form input[type="submit"] {
            width: auto;
            border: 1px solid green;
        }

        .login-form input[type="submit"]:hover {
            background: #00FF00;
            color: black;
            border: none;
            box-shadow: 0 0 5px #00FF00,
                        0 0 25px #00FF00,
                        0 0 50px #00FF00,
                        0 0 200px #00FF00;
            -webkit-box-reflect: below 4px linear-gradient(transparent, #0005);
        }

        .error {
            color: red;
        }

        @media (max-width: 600px) {
            .enter {
                font-size: 9.5vw; /* Smaller size on mobile */
                line-height: 14vw;
            }
            .login-form input[type="password"],
            .login-form input[type="submit"] {
                max-width: auto;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="enter">Enter Siqnode</div>
        <div class="login-form">
            <?php if (isset($error)): ?>
                <p class="error"><?= htmlspecialchars($error); ?></p>
            <?php endif; ?>
            <form method="POST">
                <input type="password" name="password" placeholder="Password" required>
                <input type="submit" value="Log In">
            </form>
        </div>
    </div>
</body>
</html>
