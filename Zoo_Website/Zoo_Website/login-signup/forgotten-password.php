<?php
session_start();
require 'db.php';

$error = '';

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
        $error = "<p>Email Not Found</p>";
    }
}

include "../templates/header.php";
?>

<div class = "login-main-content">
    <div class = "login-content">

            <form class = "login-option-wrapper" method="post">

                <h1>Forgotten Password</h1>

                <div class = "login-option">
                    <input type="email" name="email" placeholder="Email" required><br>
                </div>

                <div class = "login-option">
                    <button type="submit">Reset Password</button>
                </div>

                <?php echo $error ?>

    </div>
</div>

<?php
include "../templates/footer.php";
?>