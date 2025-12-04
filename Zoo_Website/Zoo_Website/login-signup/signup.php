<?php
session_start();
require 'db.php';

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $name = $_POST['name'];
    $plainPassword = $_POST['password'];

    // Name Validation

    if (!preg_match("/^[a-zA-Z' -]+$/", $name)) {
        $error = "<p>Name can only contain letters, spaces, hyphens, and apostrophes.</p>";
    } else {


        // Password Validation

        $pattern = '/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[#?!@$%^&*-]).{8,}$/';
        if (!preg_match($pattern, $plainPassword)) {
            $error = "<p>Password must be at least 8 characters long, include uppercase, lowercase, a number, and a special character.</p>";
        } else {

            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

            $stmt = $pdo->prepare("INSERT INTO users (email, pass_hash, name) VALUES (?, ?, ?)");
            try {
                $stmt->execute([$email, $password, $name]);
                header("Location: ../login-signup/login.php");
            } catch (PDOException $e) {
                $error = "<p>Email already in use</p>";
            }
        }
    }
}

include "../templates/header.php";

?>
<div class = "login-main-content">
    <div class = "login-content">

            <form class = "login-option-wrapper" method="post">

                <div class = "login-option">
                    <input type="text" name="name" placeholder="Name" required><br>
                </div>

                <div class = "login-option">
                    <input type="email" name="email" placeholder="Email" required><br>
                </div>

                <div class = "login-option">
                    <input type="password" name="password" placeholder="Password" required><br>
                </div>

                <div class = "login-option">
                    <button type="submit">Sign-Up</button>
                </div>

                <?php echo $error ?>

    </div>
</div>

<?php
include "../templates/footer.php";
?>