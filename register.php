
<?php
session_start();
require_once "db.php";

$message = "";
$messageType = "";

if (isset($_SESSION["user_id"])) {
    header("Location: dashboard.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST["username"] ?? "");
    $password = $_POST["password"] ?? "";
    $confirmPassword = $_POST["confirm_password"] ?? "";

    if ($username === "" || $password === "" || $confirmPassword === "") {
        $message = "Please fill in all fields.";
        $messageType = "error";
    } elseif (strlen($username) < 3 || strlen($username) > 100) {
        $message = "Username must be between 3 and 100 characters.";
        $messageType = "error";
    } elseif (strlen($password) < 8) {
        $message = "Password must contain at least 8 characters.";
        $messageType = "error";
    } elseif ($password !== $confirmPassword) {
        $message = "Passwords do not match.";
        $messageType = "error";
    } else {
        try {
            $check = $pdo->prepare(
                "SELECT id FROM users WHERE username = ?"
            );
            $check->execute([$username]);

            if ($check->fetch()) {
                $message = "Username already exists. Please choose another.";
                $messageType = "error";
            } else {
                $hashedPassword = password_hash(
                    $password,
                    PASSWORD_DEFAULT
                );

                $insert = $pdo->prepare(
                    "INSERT INTO users (username, password) VALUES (?, ?)"
                );

                $insert->execute([$username, $hashedPassword]);

                $message = "Registration successful! You can now log in.";
                $messageType = "success";
            }
        } catch (PDOException $e) {
            error_log("Registration error: " . $e->getMessage());
            $message = "Something went wrong. Please try again.";
            $messageType = "error";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Create Account | BlogSpace</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700;800&family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>

/* =========================
   1. Global Styles
========================= */

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: 'DM Sans', sans-serif;
    min-height: 100vh;
    background:
        radial-gradient(circle at 10% 15%, rgba(167, 139, 250, 0.45), transparent 32%),
        radial-gradient(circle at 90% 85%, rgba(192, 132, 252, 0.40), transparent 30%),
        linear-gradient(135deg, #ede9fe, #f5f3ff, #e9d5ff);
    background-attachment: fixed;
    color: #292524;
}

/* =========================
   2. Background Decoration
========================= */

.auth-container {
    min-height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
    padding: 35px 20px;
    position: relative;
    overflow: hidden;
}

.auth-container::before,
.auth-container::after {
    content: "";
    position: absolute;
    border-radius: 50%;
    pointer-events: none;
}

.auth-container::before {
    width: 300px;
    height: 300px;
    background: rgba(139, 92, 246, 0.17);
    top: -80px;
    left: -70px;
    box-shadow: 0 0 100px rgba(139, 92, 246, 0.15);
}

.auth-container::after {
    width: 370px;
    height: 370px;
    background: rgba(192, 132, 252, 0.20);
    bottom: -130px;
    right: -90px;
    box-shadow: 0 0 100px rgba(168, 85, 247, 0.15);
}

/* =========================
   3. Registration Card
========================= */

.auth-card {
    width: 100%;
    max-width: 460px;
    padding: 42px;
    position: relative;
    z-index: 1;
    background: rgba(255, 255, 255, 0.94);
    backdrop-filter: blur(15px);
    border: 1px solid rgba(255, 255, 255, 0.9);
    border-radius: 25px;
    box-shadow: 0 25px 65px rgba(76, 29, 149, 0.14);
    animation: slideUp 0.6s ease;
}

/* =========================
   4. Logo Icon
========================= */

.register-icon {
    width: 66px;
    height: 66px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, #a78bfa, #6d28d9);
    color: #ffffff;
    font-size: 32px;
    font-weight: 800;
    border-radius: 19px;
    margin-bottom: 24px;
    box-shadow: 0 9px 22px rgba(109, 40, 217, 0.25);
}

/* =========================
   5. Heading
========================= */

.auth-card h1 {
    font-family: 'Outfit', sans-serif;
    color: #2e1065;
    font-size: 32px;
    font-weight: 800;
    margin-bottom: 9px;
    letter-spacing: -0.7px;
}

.subtitle {
    color: #8b849b;
    font-size: 14px;
    line-height: 1.7;
    margin-bottom: 25px;
}

/* =========================
   6. Form Fields
========================= */

form label {
    display: block;
    color: #4c1d95;
    font-size: 14px;
    font-weight: 700;
    margin: 19px 0 8px;
}

input {
    width: 100%;
    padding: 14px 16px;
    border: 1px solid #e5defa;
    border-radius: 11px;
    background: #faf9ff;
    color: #292524;
    font-family: inherit;
    font-size: 14px;
    transition: all 0.25s ease;
}

input::placeholder {
    color: #aaa2ba;
}

input:hover {
    border-color: #c4b5fd;
}

input:focus {
    outline: none;
    background: #ffffff;
    border-color: #8b5cf6;
    box-shadow: 0 0 0 4px rgba(139, 92, 246, 0.12);
}

/* =========================
   7. Submit Button
========================= */

.btn {
    width: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 15px;
    margin-top: 27px;
    background: linear-gradient(135deg, #a78bfa, #7c3aed, #6d28d9);
    color: #ffffff;
    border: none;
    border-radius: 12px;
    font-family: inherit;
    font-size: 15px;
    font-weight: 800;
    cursor: pointer;
    box-shadow: 0 8px 20px rgba(109, 40, 217, 0.23);
    transition: all 0.3s ease;
}

.btn:hover {
    background: linear-gradient(135deg, #8b5cf6, #6d28d9, #5b21b6);
    transform: translateY(-3px);
    box-shadow: 0 12px 25px rgba(109, 40, 217, 0.3);
}

.btn:active {
    transform: translateY(0);
}

/* =========================
   8. Authentication Link
========================= */

.auth-link {
    margin-top: 26px;
    padding-top: 22px;
    border-top: 1px solid #eeeaf7;
    text-align: center;
    color: #8b849b;
    font-size: 14px;
}

.auth-link a {
    color: #7c3aed;
    font-weight: 800;
    text-decoration: none;
}

.auth-link a:hover {
    color: #5b21b6;
    text-decoration: underline;
}

/* =========================
   9. Messages
========================= */

.message {
    padding: 13px 15px;
    margin: 17px 0;
    border-radius: 11px;
    font-size: 13px;
    font-weight: 600;
    line-height: 1.6;
}

.message.success {
    background: #dcfce7;
    color: #166534;
    border: 1px solid #bbf7d0;
}

.message.error {
    background: #fee2e2;
    color: #b91c1c;
    border: 1px solid #fecaca;
}

/* =========================
   10. Animations
========================= */

@keyframes slideUp {
    from {
        opacity: 0;
        transform: translateY(25px);
    }

    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* =========================
   11. Responsive Design
========================= */

@media (max-width: 600px) {

    .auth-container {
        padding: 22px 16px;
    }

    .auth-card {
        padding: 30px 23px;
        border-radius: 20px;
    }

    .auth-card h1 {
        font-size: 28px;
    }

    .register-icon {
        width: 58px;
        height: 58px;
        font-size: 27px;
    }
}

@media (max-width: 380px) {

    .auth-card {
        padding: 25px 18px;
    }

    .auth-card h1 {
        font-size: 25px;
    }

    .subtitle {
        font-size: 13px;
    }

    input {
        padding: 12px;
    }

    .btn {
        padding: 13px;
    }
}

/* =========================
   12. Accessibility
========================= */

@media (prefers-reduced-motion: reduce) {
    *,
    *::before,
    *::after {
        animation: none !important;
        transition: none !important;
    }
}

    </style>
</head>

<body>

<main class="auth-container">

    <section class="auth-card">

        <div class="register-icon">✦</div>

        <h1>Create Account</h1>

        <p class="subtitle">
            Join BlogSpace and start managing your blog posts.
        </p>

        <?php if ($message !== ""): ?>
            <div class="message <?= htmlspecialchars($messageType) ?>">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="register.php">

            <label for="username">Username</label>

            <input
                type="text"
                id="username"
                name="username"
                placeholder="Enter your username"
                minlength="3"
                maxlength="100"
                autocomplete="username"
                value="<?= htmlspecialchars($_POST["username"] ?? "", ENT_QUOTES, "UTF-8") ?>"
                required
            >

            <label for="password">Password</label>

            <input
                type="password"
                id="password"
                name="password"
                placeholder="Create a strong password"
                minlength="8"
                autocomplete="new-password"
                required
            >

            <label for="confirm_password">Confirm Password</label>

            <input
                type="password"
                id="confirm_password"
                name="confirm_password"
                placeholder="Enter password again"
                minlength="8"
                autocomplete="new-password"
                required
            >

            <button type="submit" class="btn">
                Create Account &nbsp; →
            </button>

        </form>

        <p class="auth-link">
            Already have an account?
            <a href="login.php">Login here</a>
        </p>

    </section>

</main>

</body>
</html>
