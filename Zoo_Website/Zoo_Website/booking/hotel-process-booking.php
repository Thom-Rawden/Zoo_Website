<?php
session_start();

require '../login-signup/db.php';
require '../includes/config.php';

$_SESSION['booking_error'] = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $room_id   = $_POST['room_id'];
    $room_date = $_POST['room'];

    // Validate date format (YYYY-MM-DD)
    $dateObj = DateTime::createFromFormat('Y-m-d', $room_date);
    $isValidDate = $dateObj && $dateObj->format('Y-m-d') === $room_date;

    if (!$isValidDate) {
        $_SESSION['booking_error'] = "Invalid date format.";
        header("Location: ../public/booking-page.php");
        exit;

    } else {
        // prevent past dates
        $today = new DateTime();
        if ($dateObj < $today) {
            $_SESSION['booking_error'] = "Booking date cannot be in the past.";
            header("Location: ../public/booking-page.php");
            exit;

        }
    }

    if (empty($error)) {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM bookings WHERE room_id = :id AND booking_date = :date");
        $stmt->execute(['id' => $room_id, 'date' => $room_date]);

        // Check if booking already exists
        $exists = $stmt->fetchColumn() > 0;

        if ($exists) {
            $_SESSION['booking_error'] = "Record already exists!";
            header("Location: ../public/booking-page.php");
            exit;

        } else {
            $stmt = $pdo->prepare("INSERT INTO bookings (user_id, room_id, booking_date, type) VALUES (?, ?, ?, ?)");
            $stmt->execute([$_SESSION['user_id'], $room_id, $room_date, 'hotel']);

            header("Location: ../public/booking-page.php");
            exit;

            // Redirect to confirmation page after booking
            //header("Location: ../public/confirmation.php");
            //exit;
        }
    }
}
?>