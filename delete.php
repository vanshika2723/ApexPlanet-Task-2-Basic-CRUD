
<?php

session_start();
require_once "db.php";

// Only logged-in users can delete posts.
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

// Accept deletion requests only through POST.
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: dashboard.php");
    exit;
}

// Validate post ID.
$id = filter_input(INPUT_POST, "id", FILTER_VALIDATE_INT);

if (!$id || $id < 1) {
    header("Location: dashboard.php");
    exit;
}

try {

    // Delete post using a prepared statement.
    $stmt = $pdo->prepare(
        "DELETE FROM posts WHERE id = ?"
    );

    $stmt->execute([$id]);

    // Redirect according to deletion result.
    if ($stmt->rowCount() > 0) {
        header("Location: dashboard.php?status=deleted");
    } else {
        header("Location: dashboard.php");
    }

    exit;

} catch (PDOException $e) {

    error_log("Delete post error: " . $e->getMessage());

    http_response_code(500);

    exit("Unable to delete the post. Please try again.");
}

?>
