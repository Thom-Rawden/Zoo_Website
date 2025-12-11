<!-- Redirect if not logged in -->
<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    $_SESSION['last_site'] = '../public/booking-page.php';
    header("Location: ../login-signup/login-signup-page.php");
    exit;
}
?>
<?php include "../templates/header.php" ?>

<main class="booking-main-content">
    <div class="booking-content">

        <div class="booking-option-wrapper">

            <?php

            // minimal room list for demo purposes
            $rooms = [
                ['id'=>1,'label'=>'Room 1 - Standard'],
                ['id'=>2,'label'=>'Room 2 - Standard'],
                ['id'=>3,'label'=>'Room 3 - Suite'],
                ['id'=>4,'label'=>'Room 4 - Family']
            ];
            $minDate = date('Y-m-d');
            foreach ($rooms as $r):
            ?>
            <div class="booking-option">
                
                <form method="post" action="../booking/hotel-process-booking.php" novalidate>
                    <input type="hidden" name="room_id" value="<?php echo (int)$r['id']; ?>">
                    <label for="room_<?php echo (int)$r['id']; ?>"><?php echo htmlspecialchars($r['label']); ?></label>
                    <input type="date" id="room_<?php echo (int)$r['id']; ?>" name="room" required min="<?php echo $minDate; ?>">
                    <button type="submit">Book</button>
                </form>

                <?php
                // show messages from process booking
                if (!empty($_SESSION['booking_error'])) {
                    echo '<div class="booking-message error">' . htmlspecialchars($_SESSION['booking_error']) . '</div>';
                    unset($_SESSION['booking_error']);
                }
                if (!empty($_SESSION['booking_success'])) {
                    echo '<div class="booking-message success">' . htmlspecialchars($_SESSION['booking_success']) . '</div>';
                    unset($_SESSION['booking_success']);
                }
                ?>

            </div>
            <?php endforeach; ?>

        </div>
        
        <form class="booking-summary-wrapper" method="post" action="../booking/zoo-process-booking.php" novalidate>
            <div class="booking-details"></div>
            <div class="booking-details">
                <h2>Book Zoo Pass</h2>

                <label for="Zoo Pass"></label>
                <input type="date" id="zoo_pass" name="zoo_pass" required min="<?php echo $minDate; ?>">

                <?php

                    // show messages from zoo booking
                    if (!empty($_SESSION['zoo-booking_error'])) {
                        echo '<div class="booking-message error">' . htmlspecialchars($_SESSION['zoo-booking_error']) . '</div>';
                        unset($_SESSION['zoo-booking_error']);
                    }
                    if (!empty($_SESSION['zoo-booking_success'])) {
                        echo '<div class="booking-message success">' . htmlspecialchars($_SESSION['zoo-booking_success']) . '</div>';
                        unset($_SESSION['zoo-booking_success']);
                    }
                ?>

                <input type="number" id="party_size" name="party_size" placeholder="visitors" min="1" max="10" required>

            </div>
            <div class="booking-button">

                <button type="submit">Book</button>

            </div>
            </form>

    </div>
</main>

<?php include "../templates/footer.php" ?>