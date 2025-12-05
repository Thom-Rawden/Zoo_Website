<?php
session_start();
require 'db.php';


$error = '';

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

            // Password Validation

            $pattern = '/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[#?!@$%^&*-]).{8,}$/';
                if (!preg_match($pattern, $newPassword)) {
            $error = "<p>Password must be at least 8 characters long, include uppercase, lowercase, a number, and a special character.</p>";
            } else {

                if ($newPassword !== $confirmPassword) {
                    $error = "<p>Passwords do not match.</p>";
                } else {
                    $hash = password_hash($newPassword, PASSWORD_DEFAULT);

                    $stmt = $pdo->prepare("UPDATE users SET pass_hash = ?, reset_token = NULL WHERE reset_token = ?");
                    $stmt->execute([$hash, $token]);

                    header("Location: ../login-signup/login.php");
        }
    }
}

include "../templates/header.php";
?>

<div class = "login-main-content">
    <div class = "login-content">

            <form class = "login-option-wrapper" method="post">

                <h1>Reset Password</h1>

                <div class = "login-option">
                    <input type="password" name="password" placeholder="Password" required><br>
                </div>

                <div class = "login-option">
                    <input type="password" name="confirm_password" placeholder="Confirm Password" required><br>
                </div>

                <div class = "login-option">
                    <button type="submit">Reset Password</button>
                </div>

                <?php echo $error ?>

    </div>
</div>

<?php
} else {
        echo "Invalid or expired token.";
}

} else {
    echo "No reset token provided.";
}

?>

<?php
include "../templates/footer.php";
?>



