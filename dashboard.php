
<?php
session_start();
require_once "db.php";

// Protect dashboard: login required
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

try {
    $stmt = $pdo->query(
        "SELECT id, title, content, created_at FROM posts ORDER BY created_at DESC"
    );
    $posts = $stmt->fetchAll();
} catch (PDOException $e) {
    error_log("Dashboard error: " . $e->getMessage());
    $posts = [];
    $errorMessage = "Unable to load posts. Please try again.";
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Dashboard | BlogSpace</title>

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

:root {
    --primary: #8b5cf6;
    --primary-dark: #6d28d9;
    --primary-light: #f3e8ff;
    --background: #f7f5ff;
    --surface: #ffffff;
    --text: #29213d;
    --muted: #89829a;
    --border: #eee9f7;
    --success: #15803d;
    --success-bg: #dcfce7;
    --danger: #dc2626;
    --danger-bg: #fee2e2;
}

body {
    font-family: 'DM Sans', sans-serif;
    background:
        radial-gradient(circle at 10% 10%, rgba(196, 181, 253, 0.25), transparent 30%),
        radial-gradient(circle at 90% 90%, rgba(216, 180, 254, 0.22), transparent 30%),
        var(--background);
    color: var(--text);
    min-height: 100vh;
    line-height: 1.7;
}

a {
    text-decoration: none;
    transition: 0.25s ease;
}

button,
input {
    font-family: inherit;
}

/* =========================
   2. Navigation Bar
========================= */

.navbar {
    position: sticky;
    top: 0;
    z-index: 100;

    background: rgba(255, 255, 255, 0.90);
    backdrop-filter: blur(16px);
    -webkit-backdrop-filter: blur(16px);

    border-bottom: 1px solid var(--border);
    padding: 17px 28px;
    box-shadow: 0 3px 15px rgba(76, 29, 149, 0.04);
}

.nav-inner {
    width: 100%;
    max-width: 1500px;
    margin: 0 auto;
    padding: 0 28px;

    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 20px;
}

.brand {
    display: flex;
    align-items: center;
    gap: 11px;

    font-family: 'Outfit', sans-serif;
    color: #6d28d9;
    font-size: 25px;
    font-weight: 800;
    letter-spacing: -0.7px;
}

.brand-icon {
    width: 42px;
    height: 42px;

    display: flex;
    align-items: center;
    justify-content: center;

    background: linear-gradient(135deg, #a78bfa, #7c3aed);
    color: white;

    border-radius: 13px;
    font-size: 21px;

    box-shadow: 0 5px 13px rgba(109, 40, 217, 0.2);
}

.nav-links {
    display: flex;
    align-items: center;
    gap: 18px;

    color: var(--muted);
    font-size: 14px;
    font-weight: 600;
}

.welcome-user {
    background: #f5f3ff;
    color: #6d28d9;
    padding: 9px 15px;
    border-radius: 30px;
    border: 1px solid #ede9fe;
}

.logout-btn {
    padding: 10px 18px;
    background: #fff1f2;
    color: #e11d48;
    border: 1px solid #ffe4e6;
    border-radius: 9px;
    font-weight: 700;
}

.logout-btn:hover {
    background: #ffe4e6;
    color: #be123c;
}

/* =========================
   3. Main Container
========================= */

.container {
    width: 100%;
    max-width: 1500px;
    margin: 0 auto;
    padding: 35px 28px 50px;
}
/* =========================
   4. Dashboard Heading
========================= */

.dashboard-heading {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 25px;

    margin-bottom: 30px;
}

.dashboard-heading h1 {
    font-family: 'Outfit', sans-serif;
    color: #2e1065;
    font-size: 36px;
    font-weight: 800;
    letter-spacing: -1px;
    margin-bottom: 7px;
}

.dashboard-heading .subtitle {
    color: var(--muted);
    font-size: 14px;
    margin: 0;
}

/* =========================
   5. Create Button
========================= */

.btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;

    padding: 12px 21px;

    background: linear-gradient(135deg, #a78bfa, #7c3aed, #6d28d9);
    color: white;

    border: none;
    border-radius: 10px;

    font-size: 14px;
    font-weight: 700;

    cursor: pointer;

    box-shadow: 0 6px 17px rgba(109, 40, 217, 0.20);
    transition: all 0.25s ease;
}
@media (max-width: 600px) {

    .nav-inner {
        padding: 0 16px;
    }

    .container {
        width: 100%;
        padding: 28px 17px;
    }
}
.btn:hover {
    background: linear-gradient(135deg, #8b5cf6, #6d28d9);
    color: white;
    transform: translateY(-3px);
    box-shadow: 0 10px 22px rgba(109, 40, 217, 0.25);
}

/* =========================
   6. Stats Card
========================= */

.stats-card {
    display: flex;
    align-items: center;
    justify-content: space-between;

    max-width: 350px;
    padding: 23px 25px;
    margin-bottom: 38px;

    background: linear-gradient(135deg, #7c3aed, #8b5cf6, #a78bfa);
    color: #ffffff;

    border-radius: 18px;
    box-shadow: 0 12px 28px rgba(109, 40, 217, 0.19);
}

.stats-label {
    font-size: 13px;
    font-weight: 600;
    opacity: 0.9;
}

.stats-number {
    display: block;
    font-family: 'Outfit', sans-serif;
    font-size: 34px;
    font-weight: 800;
    line-height: 1.3;
}

.stats-icon {
    width: 55px;
    height: 55px;

    display: flex;
    align-items: center;
    justify-content: center;

    background: rgba(255, 255, 255, 0.19);
    border: 1px solid rgba(255, 255, 255, 0.25);
    border-radius: 15px;

    font-size: 25px;
}

/* =========================
   7. Messages
========================= */

.message {
    padding: 14px 17px;
    margin: 20px 0;
    border-radius: 11px;
    font-size: 14px;
    font-weight: 600;
    animation: fadeUp 0.4s ease;
}

.message.success {
    background: var(--success-bg);
    color: var(--success);
    border: 1px solid #bbf7d0;
}

.message.error {
    background: var(--danger-bg);
    color: #b91c1c;
    border: 1px solid #fecaca;
}

/* =========================
   8. Posts Section
========================= */

.posts-section {
    margin-top: 10px;
}

.posts-section h2 {
    font-family: 'Outfit', sans-serif;
    color: #2e1065;
    font-size: 25px;
    font-weight: 800;
    margin-bottom: 23px;
}

.posts-count {
    display: inline-flex;
    align-items: center;
    justify-content: center;

    min-width: 30px;
    height: 30px;

    padding: 0 9px;
    margin-left: 7px;

    background: #ede9fe;
    color: #6d28d9;

    border-radius: 9px;
    font-size: 14px;
}

/* =========================
   9. Posts Grid
========================= */

.posts-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 24px;
}

/* =========================
   10. Post Cards
========================= */

.post-card {
    position: relative;
    overflow: hidden;

    background: #ffffff;
    padding: 27px;

    border: 1px solid var(--border);
    border-radius: 18px;

    box-shadow: 0 7px 24px rgba(76, 29, 149, 0.05);

    min-width: 0;
    transition: all 0.3s ease;
}

.post-card::before {
    content: "";
    position: absolute;
    top: 0;
    left: 0;

    width: 100%;
    height: 4px;

    background: linear-gradient(90deg, #8b5cf6, #c4b5fd);
}

.post-card:hover {
    transform: translateY(-6px);
    border-color: #d8ccff;
    box-shadow: 0 17px 35px rgba(76, 29, 149, 0.10);
}

.post-card h3 {
    font-family: 'Outfit', sans-serif;
    color: #35205e;
    font-size: 21px;
    font-weight: 800;
    margin-bottom: 11px;
    overflow-wrap: anywhere;
}

.post-date {
    display: inline-block;

    background: #f5f3ff;
    color: #7c3aed;

    padding: 5px 12px;
    border-radius: 30px;

    font-size: 11px;
    font-weight: 700;
    margin-bottom: 17px;
}

.post-content {
    color: #696276;
    font-size: 14px;
    line-height: 1.9;
    overflow-wrap: anywhere;
    white-space: normal;
}

/* =========================
   11. Post Actions
========================= */

.post-actions {
    display: flex;
    align-items: center;
    gap: 11px;
    flex-wrap: wrap;

    border-top: 1px solid var(--border);
    margin-top: 23px;
    padding-top: 19px;
}

.post-actions form {
    margin: 0;
}

.post-actions .btn {
    margin: 0;
}

.btn-secondary {
    background: #f5f3ff;
    color: #6d28d9;
    border: 1px solid #ddd6fe;
    box-shadow: none;
}

.btn-secondary:hover {
    background: #ede9fe;
    color: #5b21b6;
    box-shadow: none;
}

.btn-danger {
    background: #fff1f2;
    color: #dc2626;
    border: 1px solid #fecdd3;
    box-shadow: none;
}

.btn-danger:hover {
    background: #fee2e2;
    color: #b91c1c;
    border-color: #fca5a5;
    box-shadow: none;
}

/* =========================
   12. Empty State
========================= */

.empty-state {
    background: rgba(255, 255, 255, 0.85);
    padding: 65px 25px;

    text-align: center;

    border: 2px dashed #d8ccff;
    border-radius: 20px;
}

.empty-icon {
    width: 75px;
    height: 75px;

    display: flex;
    align-items: center;
    justify-content: center;

    background: #f3e8ff;
    color: #7c3aed;

    font-size: 33px;
    border-radius: 22px;

    margin: 0 auto 20px;
}

.empty-state h3 {
    font-family: 'Outfit', sans-serif;
    color: #35205e;
    font-size: 24px;
    margin-bottom: 9px;
}

.empty-state p {
    color: var(--muted);
    font-size: 14px;
    margin-bottom: 23px;
}

/* =========================
   13. Animations
========================= */

@keyframes fadeUp {
    from {
        opacity: 0;
        transform: translateY(18px);
    }

    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* =========================
   14. Responsive Design
========================= */

@media (max-width: 850px) {

    .posts-grid {
        grid-template-columns: 1fr;
    }

    .dashboard-heading h1 {
        font-size: 31px;
    }
}

@media (max-width: 600px) {

    .navbar {
        padding: 14px 17px;
    }

    .nav-inner {
        align-items: flex-start;
        flex-direction: column;
        gap: 13px;
    }

    .brand {
        font-size: 22px;
    }

    .nav-links {
        width: 100%;
        justify-content: space-between;
        gap: 10px;
        font-size: 12px;
    }

    .welcome-user {
        padding: 8px 11px;
    }

    .container {
        padding: 28px 17px;
    }

    .dashboard-heading {
        align-items: flex-start;
        flex-direction: column;
        gap: 17px;
    }

    .dashboard-heading h1 {
        font-size: 28px;
    }

    .stats-card {
        max-width: 100%;
        margin-bottom: 30px;
    }

    .posts-section h2 {
        font-size: 22px;
    }

    .post-card {
        padding: 22px;
    }

    .empty-state {
        padding: 45px 18px;
    }
}

@media (max-width: 380px) {

    .brand {
        font-size: 20px;
    }

    .nav-links {
        font-size: 11px;
    }

    .container {
        padding: 23px 13px;
    }

    .dashboard-heading h1 {
        font-size: 25px;
    }

    .post-card {
        padding: 18px;
    }

    .post-actions {
        flex-direction: column;
        align-items: stretch;
    }

    .post-actions .btn {
        width: 100%;
    }

    .post-actions form {
        width: 100%;
    }

    .stats-number {
        font-size: 29px;
    }
}

/* =========================
   15. Accessibility
========================= */

button:focus-visible,
a:focus-visible {
    outline: 3px solid rgba(139, 92, 246, 0.4);
    outline-offset: 3px;
}

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

<!-- =========================
     Navigation
========================= -->

<header class="navbar">

    <div class="nav-inner">

        <a class="brand" href="dashboard.php">

            <span class="brand-icon">✦</span>

            <span>BlogSpace</span>

        </a>

        <div class="nav-links">

            <span class="welcome-user">
                Welcome,
                <?= htmlspecialchars($_SESSION["username"], ENT_QUOTES, "UTF-8") ?>
            </span>

            <a href="logout.php" class="logout-btn">
                Logout ↗
            </a>

        </div>

    </div>

</header>

<!-- =========================
     Main Dashboard
========================= -->

<main class="container">

    <!-- Dashboard Heading -->

    <section class="dashboard-heading">

        <div>

            <h1>Blog Dashboard</h1>

            <p class="subtitle">
                Manage your ideas, stories and blog posts in one place.
            </p>

        </div>

        <a href="create.php" class="btn">
            + Create New Post
        </a>

    </section>

    <!-- Statistics -->

    <section class="stats-card">

        <div>

            <p class="stats-label">
                Total Blog Posts
            </p>

            <span class="stats-number">
                <?= count($posts) ?>
            </span>

        </div>

        <div class="stats-icon">
            ✎
        </div>

    </section>

    <!-- Status Messages -->

    <?php if (isset($_GET["status"])): ?>

        <?php
        $statusMessages = [
            "created" => "Post created successfully!",
            "updated" => "Post updated successfully!",
            "deleted" => "Post deleted successfully!"
        ];

        $status = $_GET["status"];
        ?>

        <?php if (isset($statusMessages[$status])): ?>

            <div class="message success">
                ✓ <?= htmlspecialchars($statusMessages[$status]) ?>
            </div>

        <?php endif; ?>

    <?php endif; ?>

    <?php if (isset($errorMessage)): ?>

        <div class="message error">
            <?= htmlspecialchars($errorMessage) ?>
        </div>

    <?php endif; ?>

    <!-- Posts Section -->

    <section class="posts-section">

        <h2>
            All Blog Posts

            <span class="posts-count">
                <?= count($posts) ?>
            </span>
        </h2>

        <?php if (empty($posts)): ?>

            <div class="empty-state">

                <div class="empty-icon">
                    ✎
                </div>

                <h3>No Posts Yet!</h3>

                <p>
                    Your blogging journey starts here.
                    Create your first post and share your ideas.
                </p>

                <a href="create.php" class="btn">
                    + Create First Post
                </a>

            </div>

        <?php else: ?>

            <div class="posts-grid">

                <?php foreach ($posts as $post): ?>

                    <article class="post-card">

                        <h3>
                            <?= htmlspecialchars(
                                $post["title"],
                                ENT_QUOTES,
                                "UTF-8"
                            ) ?>
                        </h3>

                        <p class="post-date">
                            📅
                            <?= htmlspecialchars(
                                $post["created_at"],
                                ENT_QUOTES,
                                "UTF-8"
                            ) ?>
                        </p>

                        <p class="post-content">
                            <?= nl2br(htmlspecialchars(
                                $post["content"],
                                ENT_QUOTES,
                                "UTF-8"
                            )) ?>
                        </p>

                        <div class="post-actions">

                            <a
                                href="edit.php?id=<?= (int) $post["id"] ?>"
                                class="btn btn-secondary"
                            >
                                ✎ Edit
                            </a>

                            <form
                                method="POST"
                                action="delete.php"
                                onsubmit="return confirm('Are you sure you want to delete this post?');"
                            >

                                <input
                                    type="hidden"
                                    name="id"
                                    value="<?= (int) $post["id"] ?>"
                                >

                                <button
                                    type="submit"
                                    class="btn btn-danger"
                                >
                                    Delete
                                </button>

                            </form>

                        </div>

                    </article>

                <?php endforeach; ?>

            </div>

        <?php endif; ?>

    </section>

</main>

</body>
</html>

