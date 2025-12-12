<nav class="nav">
    <a href="../public/educational-visits.php">Educational Visits</a>
    <a href="../public/booking-page.php">Booking</a>

    <div class="dropdown">
        <a aria-haspopup="true" aria-expanded="false" tabindex="0">Info</a>

        <div class="dropdown-menu" role="menu" aria-label="Info menu">
            <!-- menu items mirror nav link styling -->
            <a href="../public/information.php" role="menuitem">Info</a>
        </div>
    </div>

    <?php if (isset($_SESSION['user_id'])) {
        echo '<a href="../login-signup/logout.php">Logout</a>';
    } else {
        echo '<a href="../login-signup/login-signup-page.php">Login / Sign Up</a>';
    }
    ?>
</nav>