<?php
session_start();

if (isset($_SESSION['userID'])) {
    echo "Hello, " . htmlspecialchars($_SESSION['userFName']) . "! You are logged in.<br>";
    echo "<a href='logout.php'>Logout</a>";
} else {
    echo "You are not logged in.<br>";
    echo "<a href='login.php'>Login</a> or <a href='signup.php'>Sign Up</a>";
}
?>