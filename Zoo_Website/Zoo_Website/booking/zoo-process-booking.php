<?php
session_start();
require '../login-signup/db.php';
require '../includes/config.php';

$_SESSION['zoo-booking_error'] = '';
$_SESSION['zoo-booking_success'] = '';

// capacity: prefer config constant if set
$stock = defined('ZOO_DAILY_CAPACITY') ? (int)ZOO_DAILY_CAPACITY : 100;

// price config (used only for message/display; not required in DB)
$unit_price = defined('ZOO_UNIT_PRICE') ? (float)ZOO_UNIT_PRICE : 0.00;
$education_discount = defined('EDUCATION_DISCOUNT') ? (float)EDUCATION_DISCOUNT : 0.0;

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../public/educational-visits.php');
    exit;
}

$pass_date_raw = trim($_POST['zoo_pass'] ?? '');
$party_raw = $_POST['party_size'] ?? null;
$party_size = filter_var($party_raw, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1, 'max_range' => 100]]);
if ($party_size === false) {
    $_SESSION['zoo-booking_error'] = 'Please enter a valid number of visitors (1-100).';
    header('Location: ../public/educational-visits.php');
    exit;
}

// validate date strictly YYYY-MM-DD
$dateObj = DateTime::createFromFormat('Y-m-d', $pass_date_raw);
$dateErrors = DateTime::getLastErrors();
if (!$dateObj || $dateErrors['warning_count'] > 0 || $dateErrors['error_count'] > 0 || $dateObj->format('Y-m-d') !== $pass_date_raw) {
    $_SESSION['zoo-booking_error'] = 'Invalid date format.';
    header('Location: ../public/educational-visits.php');
    exit;
}

// prevent past dates
$today = new DateTimeImmutable('today');
$d = DateTimeImmutable::createFromFormat('Y-m-d', $pass_date_raw);
if ($d < $today) {
    $_SESSION['zoo-booking_error'] = 'Booking date cannot be in the past.';
    header('Location: ../public/educational-visits.php');
    exit;
}

// education flag
$education_flag = (!empty($_POST['education_visit']) && $_POST['education_visit'] == '1') ? 1 : 0;

try {
    // sum existing confirmed people for that date
    $stmt = $pdo->prepare("SELECT COALESCE(SUM(party_size),0) FROM bookings WHERE type = ? AND booking_date = ? AND status = 'confirmed'");
    $stmt->execute(['zoo', $pass_date_raw]);
    $booked = (int)$stmt->fetchColumn();

    if ($booked + $party_size > $stock) {
        $_SESSION['zoo-booking_error'] = 'No tickets available for this date.';
        header('Location: ../public/educational-visits.php');
        exit;
    }

    // insert booking
    $ins = $pdo->prepare("INSERT INTO bookings (user_id, room_id, booking_date, party_size, type, status, created_at) VALUES (?, NULL, ?, ?, 'zoo', 'pending', NOW())");
    $ins->execute([$_SESSION['user_id'] ?? null, $pass_date_raw, $party_size]);

    // compute a display price if unit price configured
    $subtotal = $unit_price > 0 ? round($unit_price * $party_size, 2) : 0.00;
    $discount_amount = $education_flag ? round($subtotal * $education_discount, 2) : 0.00;
    $total = round($subtotal - $discount_amount, 2);

    if ($unit_price > 0) {
        $msg = 'Booking created';
        if ($education_flag && $education_discount > 0) {
            $msg .= sprintf('. Price: £%0.2f (discount applied)', $total);
        } else {
            $msg .= sprintf('. Price: £%0.2f', $subtotal);
        }
    } else {
        $msg = 'Booking created.';
    }

    $_SESSION['zoo-booking_success'] = $msg;
    header('Location: ../public/'.$_SESSION['redirect'] ?? 'booking-page.php');
    exit;
} catch (Exception $e) {
    error_log('zoo booking error: ' . $e->getMessage());
    $_SESSION['zoo-booking_error'] = 'Server error, please try again.';
    header('Location: ../public/educational-visits.php');
    exit;
}
?>