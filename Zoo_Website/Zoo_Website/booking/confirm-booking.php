<?php
session_start();
require_once __DIR__ . '/../login-signup/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../public/account-page.php');
    exit;
}

$user_id = $_SESSION['user_id'] ?? null;
if (empty($user_id)) {
    $_SESSION['booking_error'] = 'You must be logged in.';
    header('Location: ../public/account-page.php');
    exit;
}

$booking_id = isset($_POST['booking_id']) ? (int)$_POST['booking_id'] : 0;
$token = $_POST['csrf_token'] ?? '';

if ($booking_id <= 0 || empty($token) || empty($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $token)) {
    $_SESSION['booking_error'] = 'Invalid request.';
    header('Location: ../public/account-page.php');
    exit;
}

try {
    // ensure booking belongs to user
    $stmt = $pdo->prepare('SELECT id, status FROM bookings WHERE id = ? AND user_id = ? LIMIT 1');
    $stmt->execute([$booking_id, (int)$user_id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$row) {
        $_SESSION['booking_error'] = 'Booking not found.';
        header('Location: ../public/account-page.php');
        exit;
    }

    // update status to confirmed
    $upd = $pdo->prepare("UPDATE bookings SET status = 'confirmed' WHERE id = ? AND user_id = ?");
    $upd->execute([$booking_id, (int)$user_id]);

    $_SESSION['booking_success'] = 'Booking confirmed.';
    header('Location: ../public/account-page.php');
    exit;
} catch (Exception $e) {
    error_log('confirm-booking error: ' . $e->getMessage());
    $_SESSION['booking_error'] = 'Server error, please try again.';
    header('Location: ../public/account-page.php');
    exit;
}
?>