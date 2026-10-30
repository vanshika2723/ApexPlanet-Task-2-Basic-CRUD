
<?php
session_start();
require_once "db.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

$message = "";
$title = "";
$content = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $title = trim($_POST["title"] ?? "");
    $content = trim($_POST["content"] ?? "");

    if ($title === "" || $content === "") {
        $message = "Please enter both title and content.";
    } elseif (strlen($title) > 255) {
        $message = "Title cannot exceed 255 characters.";
    } else {
        try {
            $stmt = $pdo->prepare(
                "INSERT INTO posts (title, content) VALUES (?, ?)"
            );

            $stmt->execute([$title, $content]);

            header("Location: dashboard.php?status=created");
            exit;
        } catch (PDOException $e) {
            error_log("Create post error: " . $e->getMessage());
            $message = "Unable to create the post. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Create Post | BlogSpace</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700;800&family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            min-height: 100vh;
            font-family: 'DM Sans', sans-serif;
            color: #25213d;
            background:
                radial-gradient(circle at 10% 15%, rgba(167, 139, 250, 0.30), transparent 30%),
                radial-gradient(circle at 90% 80%, rgba(196, 181, 253, 0.35), transparent 32%),
                linear-gradient(135deg, #f5f3ff, #f8f7ff 50%, #eeebff);
            overflow-x: hidden;
        }

        a {
            text-decoration: none;
        }

        .navbar {
            width: 100%;
            height: 76px;
            background: rgba(255, 255, 255, 0.78);
            backdrop-filter: blur(18px);
            border-bottom: 1px solid rgba(109, 78, 190, 0.10);
            position: sticky;
            top: 0;
            z-index: 10;
        }

        .nav-inner {
            max-width: 1180px;
            height: 100%;
            margin: auto;
            padding: 0 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 11px;
            color: #29234a;
            font-family: 'Outfit', sans-serif;
            font-size: 24px;
            font-weight: 800;
        }

        .brand-icon {
            width: 39px;
            height: 39px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
            color: white;
            font-size: 20px;
            background: linear-gradient(135deg, #8b5cf6, #635bdb);
            box-shadow: 0 5px 13px rgba(99, 91, 219, 0.25);
        }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .nav-links a {
            color: #625d79;
            font-size: 14px;
            font-weight: 600;
            padding: 10px 15px;
            border-radius: 10px;
            transition: 0.25s ease;
        }

        .nav-links a:hover {
            color: #7048d8;
            background: #f1edff;
        }

        .container {
            max-width: 1180px;
            margin: auto;
            padding: 55px 24px 70px;
        }

        .form-container {
            max-width: 850px;
        }

        .breadcrumb {
            display: flex;
            align-items: center;
            gap: 9px;
            margin-bottom: 25px;
            color: #89849f;
            font-size: 13px;
            font-weight: 600;
        }

        .breadcrumb a {
            color: #7654d9;
        }

        .breadcrumb span {
            color: #b3adc7;
        }

        .form-card {
            position: relative;
            overflow: hidden;
            background: rgba(255, 255, 255, 0.91);
            border: 1px solid rgba(255, 255, 255, 0.95);
            border-radius: 24px;
            padding: 40px;
            box-shadow: 0 18px 55px rgba(75, 55, 130, 0.09);
        }

        .form-card::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: linear-gradient(90deg, #8b5cf6, #a78bfa, #c4b5fd);
        }

        .form-header {
            display: flex;
            align-items: flex-start;
            gap: 17px;
            margin-bottom: 32px;
        }

        .form-icon {
            width: 55px;
            height: 55px;
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f0ebff;
            color: #7952dc;
            border-radius: 17px;
            font-size: 25px;
        }

        h1 {
            font-family: 'Outfit', sans-serif;
            color: #29234a;
            font-size: 30px;
            font-weight: 700;
            margin-bottom: 7px;
        }

        .subtitle {
            color: #89849f;
            font-size: 14px;
            line-height: 1.6;
        }

        .message {
            padding: 14px 16px;
            border-radius: 12px;
            margin-bottom: 23px;
            font-size: 14px;
            font-weight: 500;
        }

        .message.error {
            color: #b4233b;
            background: #fff0f2;
            border: 1px solid #ffd6de;
        }

        .form-group {
            margin-bottom: 24px;
        }

        label {
            display: block;
            color: #38334f;
            font-size: 14px;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .required {
            color: #e45d7a;
        }

        input,
        textarea {
            width: 100%;
            padding: 15px 17px;
            border: 1px solid #e7e3f1;
            border-radius: 12px;
            outline: none;
            background: #fcfbff;
            color: #302b49;
            font-family: 'DM Sans', sans-serif;
            font-size: 14px;
            transition: 0.25s ease;
        }

        input::placeholder,
        textarea::placeholder {
            color: #aaa5ba;
        }

        input:focus,
        textarea:focus {
            border-color: #9270e8;
            background: #fff;
            box-shadow: 0 0 0 4px rgba(139, 92, 246, 0.10);
        }

        textarea {
            min-height: 230px;
            resize: vertical;
            line-height: 1.8;
        }

        .field-hint {
            color: #9993ac;
            font-size: 12px;
            margin-top: 8px;
        }

        .form-actions {
            display: flex;
            align-items: center;
            gap: 13px;
            margin-top: 30px;
            padding-top: 24px;
            border-top: 1px solid #f0edf6;
        }

        .btn {
            min-height: 46px;
            display: inline-flex;
            justify-content: center;
            align-items: center;
            gap: 9px;
            padding: 12px 23px;
            border: none;
            border-radius: 11px;
            background: linear-gradient(135deg, #8b5cf6, #6654d9);
            color: white;
            font-family: 'DM Sans', sans-serif;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            box-shadow: 0 7px 17px rgba(112, 78, 218, 0.20);
            transition: 0.25s ease;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 22px rgba(112, 78, 218, 0.28);
        }

        .btn-secondary {
            background: #f2effa;
            color: #62577e;
            box-shadow: none;
        }

        .btn-secondary:hover {
            background: #e9e3fa;
            box-shadow: none;
        }

        .writing-tip {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            margin-top: 27px;
            padding: 17px;
            border-radius: 13px;
            background: #f7f4ff;
            border: 1px solid #eee8ff;
        }

        .tip-icon {
            font-size: 19px;
        }

        .writing-tip p {
            color: #79718f;
            font-size: 12px;
            line-height: 1.7;
        }

        .writing-tip strong {
            display: block;
            color: #57458b;
            font-size: 13px;
            margin-bottom: 3px;
        }

        @media (max-width: 600px) {
            .navbar {
                height: 68px;
            }

            .nav-inner {
                padding: 0 16px;
            }

            .brand {
                font-size: 21px;
            }

            .brand-icon {
                width: 35px;
                height: 35px;
                font-size: 17px;
            }

            .nav-links {
                gap: 2px;
            }

            .nav-links a {
                padding: 9px 10px;
                font-size: 12px;
            }

            .container {
                padding: 32px 15px 45px;
            }

            .form-card {
                padding: 26px 19px;
                border-radius: 18px;
            }

            .form-header {
                gap: 12px;
                margin-bottom: 26px;
            }

            .form-icon {
                width: 46px;
                height: 46px;
                font-size: 21px;
            }

            h1 {
                font-size: 24px;
            }

            .subtitle {
                font-size: 13px;
            }

            textarea {
                min-height: 190px;
            }

            .form-actions {
                flex-direction: column;
                align-items: stretch;
            }

            .btn {
                width: 100%;
            }
        }
    </style>
</head>

<body>

    <header class="navbar">
        <div class="nav-inner">

            <a class="brand" href="dashboard.php">
                <span class="brand-icon">✎</span>
                BlogSpace
            </a>

            <div class="nav-links">
                <a href="dashboard.php">Dashboard</a>
                <a href="logout.php">Logout</a>
            </div>

        </div>
    </header>

    <main class="container form-container">

        <div class="breadcrumb">
            <a href="dashboard.php">Dashboard</a>
            <span>/</span>
            <span>Create Post</span>
        </div>

        <section class="form-card">

            <div class="form-header">

                <div class="form-icon">✍</div>

                <div>
                    <h1>Create New Post</h1>
                    <p class="subtitle">
                        Share your thoughts, ideas and stories with the world.
                    </p>
                </div>

            </div>

            <?php if ($message !== ""): ?>
                <div class="message error">
                    <?= htmlspecialchars($message, ENT_QUOTES, "UTF-8") ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="create.php">

                <div class="form-group">

                    <label for="title">
                        Post Title <span class="required">*</span>
                    </label>

                    <input
                        type="text"
                        id="title"
                        name="title"
                        maxlength="255"
                        value="<?= htmlspecialchars($title, ENT_QUOTES, "UTF-8") ?>"
                        placeholder="Give your blog post a catchy title..."
                        required
                    >

                    <p class="field-hint">
                        Keep your title clear and interesting. Maximum 255 characters.
                    </p>

                </div>

                <div class="form-group">

                    <label for="content">
                        Post Content <span class="required">*</span>
                    </label>

                    <textarea
                        id="content"
                        name="content"
                        rows="8"
                        placeholder="Start writing your story here..."
                        required
                    ><?= htmlspecialchars($content, ENT_QUOTES, "UTF-8") ?></textarea>

                    <p class="field-hint">
                        Write your thoughts in detail. Make your content engaging and useful.
                    </p>

                </div>

                <div class="form-actions">

                    <button type="submit" class="btn">
                        <span>✦</span>
                        Publish Post
                    </button>

                    <a href="dashboard.php" class="btn btn-secondary">
                        Cancel
                    </a>

                </div>

            </form>

            <div class="writing-tip">
                <span class="tip-icon">💡</span>

                <p>
                    <strong>Writing Tip</strong>
                    A great blog post starts with a meaningful title.
                    Organize your thoughts and share something valuable with your readers.
                </p>
            </div>

        </section>

    </main>

</body>
</html>
