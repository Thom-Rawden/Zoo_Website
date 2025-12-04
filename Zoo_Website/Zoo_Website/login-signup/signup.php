<?php
session_start();
require 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $name = $_POST['name'];
    $plainPassword = $_POST['password'];

    // Name Validation

    if (!preg_match("/^[a-zA-Z' -]+$/", $name)) {
        echo "Name can only contain letters, spaces, hyphens, and apostrophes.";
    } else {


        // Password Validation

        $pattern = '/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[#?!@$%^&*-]).{8,}$/';
        if (!preg_match($pattern, $plainPassword)) {
            echo "Password must be at least 8 characters long, include uppercase, lowercase, a number, and a special character.";
        } else {

            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

            $stmt = $pdo->prepare("INSERT INTO users (email, pass_hash, name) VALUES (?, ?, ?)");
            try {
                $stmt->execute([$email, $password, $name]);
                echo "Signup successful! <a href='login.php'>Login here</a>";
            } catch (PDOException $e) {
                echo "Error: " . $e->getMessage();
            }
        }
    }
}

include "../templates/header.php";

?>

<form method="post">
    Name: <input type="text" name="name" required><br>
    Email: <input type="email" name="email" required><br>
    Password: <input type="password" name="password" required><br>
    <button type="submit">Sign Up</button>
</form>