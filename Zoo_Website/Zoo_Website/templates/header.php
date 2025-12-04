<?php
session_start(); 
require '../includes/config.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Zoo Website</title>
    <link rel="stylesheet" href="../public/css/styles.css">
</head>
<body>

<header class="header">
    <div></div>
    <h1><a href = "../public/index.php">Riget Zoo</a></h1>
    <div><?php if (isset($_SESSION['user_id'])) {echo '<h1><a href = "../public/account.php">Account</a></h1>';} ?></div>
</header>