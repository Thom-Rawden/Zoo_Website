<?php
session_start();
require '../login-signup/db.php';
require '../includes/config.php';

$_SESSION['zoo-booking_error'] = '';
$_SESSION['zoo-booking_success'] = '';

$stock = 10; // capacity in people for the date

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../public/booking-page.php");
    exit;
}

$pass_date_raw = trim($_POST['zoo_pass'] ?? '');
$party_size = max(1, (int)($_POST['party_size'] ?? 1));

// Validate date strictly (YYYY-MM-DD)
$dateObj = DateTime::createFromFormat('Y-m-d', $pass_date_raw);
$errors = DateTime::getLastErrors();
if (!$dateObj || $errors['warning_count'] > 0 || $errors['error_count'] > 0 || $dateObj->format('Y-m-d') !== $pass_date_raw) {
    $_SESSION['zoo-booking_error'] = 'Invalid date format.';
    header("Location: ../public/booking-page.php");
    exit;
}

// No past dates
$today = new DateTimeImmutable('today');
$d = DateTimeImmutable::createFromFormat('Y-m-d', $pass_date_raw);
if ($d < $today) {
    $_SESSION['zoo-booking_error'] = 'Booking date cannot be in the past.';
    header("Location: ../public/booking-page.php");
    exit;
}

try {
    // Check date availability
    $stmt = $pdo->prepare("SELECT COALESCE(SUM(party_size),0) FROM bookings WHERE type = ? AND booking_date = ? AND status = 'confirmed'");
    $stmt->execute(['zoo', $pass_date_raw]);
    $booked = (int)$stmt->fetchColumn();

    if ($booked + $party_size > $stock) {
        $_SESSION['zoo-booking_error'] = 'No tickets available for this date.';
        header("Location: ../public/booking-page.php");
        exit;
    }

    // Insert booking including party_size and user_id (if available)
    $ins = $pdo->prepare("INSERT INTO bookings (user_id, booking_date, type, party_size, status) VALUES (?, ?, ?, ?, 'confirmed')");
    $ins->execute([$_SESSION['user_id'] ?? null, $pass_date_raw, 'zoo', $party_size]);

    $_SESSION['zoo-booking_success'] = 'Booking confirmed.';
    header("Location: ../public/booking-page.php");
    exit;

} catch (Exception $e) {
    error_log('Zoo booking error: '.$e->getMessage());
    $_SESSION['zoo-booking_error'] = 'Server error, please try again.';
    header("Location: ../public/booking-page.php");
    exit;
}
?>