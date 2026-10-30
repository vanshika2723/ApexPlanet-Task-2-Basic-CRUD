
<?php
session_start();
require_once "db.php";

$message = "";

if (isset($_SESSION["user_id"])) {
    header("Location: dashboard.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST["username"] ?? "");
    $password = $_POST["password"] ?? "";

    if ($username === "" || $password === "") {
        $message = "Please enter your username and password.";
    } else {
        try {
            $stmt = $pdo->prepare(
                "SELECT id, username, password FROM users WHERE username = ?"
            );
            $stmt->execute([$username]);

            $user = $stmt->fetch();

            if ($user && password_verify($password, $user["password"])) {
                session_regenerate_id(true);

                $_SESSION["user_id"] = $user["id"];
                $_SESSION["username"] = $user["username"];

                header("Location: dashboard.php");
                exit;
            } else {
                $message = "Invalid username or password.";
            }
        } catch (PDOException $e) {
            error_log("Login error: " . $e->getMessage());
            $message = "Something went wrong. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Login | BlogSpace</title>

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
    color: #292524;

    background:
        radial-gradient(circle at 10% 15%, rgba(167, 139, 250, 0.45), transparent 32%),
        radial-gradient(circle at 90% 85%, rgba(192, 132, 252, 0.40), transparent 30%),
        linear-gradient(135deg, #ede9fe, #f5f3ff, #e9d5ff);

    background-attachment: fixed;
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
   3. Login Card
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
   4. Login Icon
========================= */

.login-icon {
    width: 66px;
    height: 66px;

    display: flex;
    justify-content: center;
    align-items: center;

    background: linear-gradient(135deg, #a78bfa, #6d28d9);
    color: #ffffff;

    font-size: 31px;
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
   7. Login Button
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
   9. Error Message
========================= */

.message {
    padding: 13px 15px;
    margin: 17px 0;

    border-radius: 11px;

    font-size: 13px;
    font-weight: 600;
    line-height: 1.6;
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

    .login-icon {
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

        <div class="login-icon">✦</div>

        <h1>Welcome Back!</h1>

        <p class="subtitle">
            Login to your BlogSpace account and manage your blog posts.
        </p>

        <?php if ($message !== ""): ?>
            <div class="message error">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="login.php">

            <label for="username">Username</label>

            <input
                type="text"
                id="username"
                name="username"
                placeholder="Enter your username"
                autocomplete="username"
                value="<?= htmlspecialchars($_POST["username"] ?? "", ENT_QUOTES, "UTF-8") ?>"
                required
            >

            <label for="password">Password</label>

            <input
                type="password"
                id="password"
                name="password"
                placeholder="Enter your password"
                autocomplete="current-password"
                required
            >

            <button type="submit" class="btn">
                Login to Account &nbsp; →
            </button>

        </form>

        <p class="auth-link">
            Don't have an account?
            <a href="register.php">Create Account</a>
        </p>

    </section>

</main>

</body>
</html>
