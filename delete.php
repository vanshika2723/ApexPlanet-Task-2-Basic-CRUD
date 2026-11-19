
<?php

session_start();
require_once "db.php";

// 1. Authentication: user must be logged in
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

// 2. Authorization: only admin can delete posts
$userRole = $_SESSION["role"] ?? "editor";

if ($userRole !== "admin") {
    http_response_code(403);
    exit("Access denied. Only administrators can delete posts.");
}

// 3. Accept POST requests only
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: dashboard.php");
    exit;
}

// 4. CSRF protection
$sessionToken = $_SESSION["csrf_token"] ?? "";
$submittedToken = $_POST["csrf_token"] ?? "";

if (
    empty($sessionToken) ||
    empty($submittedToken) ||
    !hash_equals($sessionToken, $submittedToken)
) {
    http_response_code(403);
    exit("Invalid security token. Please refresh the page and try again.");
}

// 5. Validate post ID
$id = filter_input(INPUT_POST, "id", FILTER_VALIDATE_INT);

if (!$id || $id < 1) {
    header("Location: dashboard.php");
    exit;
}

try {

    // 6. Delete post using prepared statement
    $stmt = $pdo->prepare(
        "DELETE FROM posts WHERE id = :id"
    );

    $stmt->execute([
        ":id" => $id
    ]);

    // 7. Redirect based on result
    if ($stmt->rowCount() > 0) {
        header("Location: dashboard.php?status=deleted");
    } else {
        header("Location: dashboard.php?status=not_found");
    }

    exit;

} catch (PDOException $e) {

    // Log technical details safely
    error_log("Delete post error: " . $e->getMessage());

    http_response_code(500);

    exit("Unable to delete the post. Please try again.");

}

?>
