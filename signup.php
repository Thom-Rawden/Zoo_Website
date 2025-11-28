<?php
session_start();
require 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $fname = $_POST['fname'];
    $sname = $_POST['sname'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $stmt = $pdo->prepare("INSERT INTO user (userEmail, userPass, userFName, userSName) VALUES (?, ?, ?, ?)");
    try {
        $stmt->execute([$email, $password, $fname, $sname]);
        echo "Signup successful! <a href='login.php'>Login here</a>";
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>

<form method="post">
    First Name: <input type="text" name="fname" required><br>
    Last Name: <input type="text" name="sname" required><br>
    Email: <input type="email" name="email" required><br>
    Password: <input type="password" name="password" required><br>
    <button type="submit">Sign Up</button>
</form>