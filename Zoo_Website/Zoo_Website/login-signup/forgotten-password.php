<?php
session_start();
require 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        // Generate token
        $token = bin2hex(random_bytes(16));

        // Store token in DB
        $stmt = $pdo->prepare("UPDATE users SET reset_token = ? WHERE email = ?");
        $stmt->execute([$token, $email]);

        // Redirect to reset page with token
        header("Location: ../login-signup/reset-password.php?token=$token");
        exit;
    } else {
        echo "Email Not Found";
    }
}

include "../templates/header.php";
?>

<form method="post">
    Email: <input type="email" name="email" required><br>
    <button type="submit">Reset Password</button>
</form>