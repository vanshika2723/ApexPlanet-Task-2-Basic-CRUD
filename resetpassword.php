<?php
require_once "db.php";

$username = "vanshika123";
$newPassword = "BlogAdmin@2026!";

$hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

$stmt = $pdo->prepare(
    "UPDATE users SET password = ? WHERE username = ? AND role = 'admin'"
);

$stmt->execute([$hashedPassword, $username]);

echo $stmt->rowCount() > 0
    ? "Password reset successfully!"
    : "Admin not found!";
?>