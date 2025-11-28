<?php
session_start();
require 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM user WHERE userEmail = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['userPass'])) {
        $_SESSION['userID'] = $user['userID'];
        $_SESSION['userFName'] = $user['userFName'];
        header("Location: index.php");
        exit;
    } else {
        echo "Invalid email or password.";
    }
}
?>

<form method="post">
    Email: <input type="email" name="email" required><br>
    Password: <input type="password" name="password" required><br>
    <button type="submit">Login</button>
</form>