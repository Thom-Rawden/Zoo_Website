<!-- Redirect if not logged in -->
<?php


session_start();

if (!isset($_SESSION['user_id'])) {
    $_SESSION['last_site'] = '../public/booking-page.php';
    header("Location: ../login-signup/login-signup-page.php");
    exit;
}
?>
<!-- Include the header and footer templates -->
<?php include "../templates/header.php" ?>


<main class="booking-main-content">
    <div class = "booking-content">
        <div class = "booking-option-wrapper">
            <div class = "booking-option">1</div>
            <div class = "booking-option">2</div>
            <div class = "booking-option">3</div>
            <div class = "booking-option">4</div>
        </div>
        <div class = "booking-summary-wrapper">
            <div class = "booking-details"></div>
            <div class = "booking-details"></div>
            <div class = "booking-button"></div>
        </div>
    </div>
</main>


<?php include "../templates/footer.php" ?>