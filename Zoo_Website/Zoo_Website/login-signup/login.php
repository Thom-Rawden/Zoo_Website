<?php
session_start();
require 'db.php';

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['pass_hash'])) {
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['userFName'] = $user['userFName'];
        header("Location: " . $_SESSION['last_site']);
        exit;
    } else {
        $error = "<p>Invalid email or password. <br><a href = '../login-signup/forgotten-password.php'>Forgotten Password?</a></p>";
    }
}

include "../templates/header.php";

?>
<div class = "login-main-content">
    <div class = "login-content">

            <form class = "login-option-wrapper" method="post">

                <h1>Login</h1>

                <div class = "login-option">
                    <input type="email" name="email" placeholder="Email" required><br>
                </div>

                <div class = "login-option">
                    <input type="password" name="password" placeholder="Password" required><br>
                </div>

                <div class = "login-option">
                    <button type="submit">Login</button>
                </div>

                <?php echo $error ?>
            
            </form>

    </div>
</div>

<?php
include "../templates/footer.php";
?>