<!-- Redirect if not logged in -->
<?php
session_start();

$_SESSION['redirect'] = 'booking-page.php';

if (!isset($_SESSION['user_id'])) {
    $_SESSION['last_site'] = '../public/booking-page.php';
    header("Location: ../login-signup/login-signup-page.php");
    exit;
}

// Function to get user bookings
function get_user_bookings(?int $user_id): array
{
    if (empty($user_id) || !isset($GLOBALS['pdo'])) {
        return [];
    }

    try {
        $pdo = $GLOBALS['pdo']; 
        $stmt = $pdo->prepare("SELECT * FROM bookings WHERE user_id = :id");
        $stmt->execute(['id' => $user_id]);

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $rows ?: [];
    } catch (Exception $e) {
        error_log('get_user_bookings error: ' . $e->getMessage());
        return [];
    }
}


?>
<?php include "../templates/header.php" ?>

<main class="booking-main-content">
    <div class="booking-content">

        <div class="booking-option-wrapper">

            <?php

            // minimal room list for demo purposes
            $rooms = [
                ['id'=>1,'label'=>'Room 1 - Standard £30, 2 people max'],
                ['id'=>2,'label'=>'Room 2 - Standard £30, 2 people max'],
                ['id'=>3,'label'=>'Room 3 - Suite £50, 3 people max'],
                ['id'=>4,'label'=>'Room 4 - Family £70, 5 people max'],
            ];
            $minDate = date('Y-m-d');
            foreach ($rooms as $r):
            ?>
            <div class="booking-option">
                
                <form method="post" action="../booking/hotel-process-booking.php" novalidate>
                    <input type="hidden" name="room_id" value="<?php echo (int)$r['id']; ?>">
                    <label for="room_<?php echo (int)$r['id']; ?>"><?php echo htmlspecialchars($r['label']); ?></label>
                    <input type="date" id="room_<?php echo (int)$r['id']; ?>" name="room" required min="<?php echo $minDate; ?>">
                    <button type="submit">Reserve</button>

                    
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
            <div class="booking-details">
                <h2>Book Zoo Pass</h2>

                <label for="zoo_pass"></label>
                <input type="date" id="zoo_pass" name="zoo_pass" required min="<?php echo $minDate; ?>">

                <input type="number" id="party_size" name="party_size" placeholder="visitors" min="1" max="10" required>

                <button type="submit">Reserve</button>

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
            </div>

            <!-- Show Bookings Summary -->
            <div class="booking-details">
                <h2>Your Bookings Summary</h2>
                
                <?php
                require_once __DIR__ . '/../login-signup/db.php';

                $bookings = get_user_bookings($_SESSION['user_id'] ?? null);
                if (empty($bookings)):
                ?>
                    <p>No bookings found.</p>
                <?php
                else:

                foreach ($bookings as $b):
                    ?>
                    
                    <div class="booking-summary-item">
                        <span class="booking-type"><?php echo htmlspecialchars(ucfirst($b['type'])); ?></span>
                        <span class="booking-date"><?php echo htmlspecialchars(date("d-m-Y", strtotime(($b['booking_date'])))); ?></span>
                        <?php if ($b['type'] === 'zoo'): ?>
                            <span class="booking-party-size">Visitors: <?php echo (int)$b['party_size']; ?></span>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
                <?php endif; ?>

            </div>
            <div class="booking-button">
                <a href="../public/account-page.php">Confirm Bookings</a>
            </div>
            </form>

    </div>
</main>

<?php include "../templates/footer.php" ?>
