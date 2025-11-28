<?php
$host = "localhost";       // usually localhost
$dbname = "thom_database"; // your database name from phpMyAdmin
$user = "root";            // your phpMyAdmin username
$pass = "bimt";                // your phpMyAdmin password (empty if none)

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("DB Connection failed: " . $e->getMessage());
}
?>