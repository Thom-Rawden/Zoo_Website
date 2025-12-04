<?php
session_start();
require 'db.php';

if (isset($_GET['token'])) {
    $token = $_GET['token'];

    // Verify token exists
    $stmt = $pdo->prepare("SELECT * FROM users WHERE reset_token = ?");
    $stmt->execute([$token]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $newPassword = $_POST['password'];
            $confirmPassword = $_POST['confirm_password'];

            if ($newPassword !== $confirmPassword) {
                echo "Passwords do not match.";
            } else {
                $hash = password_hash($newPassword, PASSWORD_DEFAULT);

                $stmt = $pdo->prepare("UPDATE users SET pass_hash = ?, reset_token = NULL WHERE reset_token = ?");
                $stmt->execute([$hash, $token]);

                echo "Password successfully reset! <a href='login.php'>Login</a>";
    }
}

include "../templates/header.php";

?>

<form method="post">
    Password: <input type="password" name="password" required><br>
    Confirm Password: <input type="password" name="confirm_password" required><br>
    <button type="submit">Reset Password</button>
</form>
        <?php
    } else {
        echo "Invalid or expired token.";
    }
} else {
    echo "No reset token provided.";
}

?>