<nav class="nav">
    <a href="#">Educational Visits</a>
    <a href="../public/booking-page.php">Booking</a>
    <a href="../public/information.php">Info</a>
    <a href="#">Reviews</a>
    <?php if (isset($_SESSION['user_id'])) {
        echo '<a href="../login-signup/logout.php">Logout</a>';
    } else {
    echo '<a href="../login-signup/login-signup-page.php">Login / Sign up</a>';
    }
    ?>
</nav>