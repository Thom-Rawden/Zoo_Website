<?php
session_start();

$_SESSION['redirect'] = 'educational-visits.php';

if (!isset($_SESSION['user_id'])) {
    $_SESSION['last_site'] = '../public/educational-visits.php';
    header('Location: ../login-signup/login-signup-page.php');
    exit;
}

require_once __DIR__ . '/../includes/config.php';
$minDate = date('Y-m-d');

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

include "../templates/header.php";
?>

<main class="booking-main-content">
  <div class="booking-content">

    <div class="booking-option-wrapper">
      <h2>Educational Visits — Book Zoo Passes</h2>

      <?php
      if (!empty($_SESSION['zoo-booking_error'])) {
          echo '<div class="booking-message error">'.htmlspecialchars($_SESSION['zoo-booking_error']).'</div>';
          unset($_SESSION['zoo-booking_error']);
      }
      if (!empty($_SESSION['zoo-booking_success'])) {
          echo '<div class="booking-message success">'.htmlspecialchars($_SESSION['zoo-booking_success']).'</div>';
          unset($_SESSION['zoo-booking_success']);
      }
      ?>

      <div class="booking-option">
        <form method="post" action="../booking/zoo-process-booking.php" novalidate>
          <label for="zoo_pass">Visit date</label>
          <input type="date" id="zoo_pass" name="zoo_pass" required min="<?php echo $minDate; ?>">

          <label for="party_size">Visitors</label>
          <input type="number" id="party_size" name="party_size" min="1" max="100" value="1" required>

          <label style="display:block; margin-top:0.6vh;">
            <input type="checkbox" name="education_visit" value="1"> This booking is an educational visit (apply discount)
          </label>

          <div style="margin-top:0.8vh;">
            <button type="submit" class="btn">Book educational visit</button>
          </div>
        </form>
      </div>

    </div>

    <div class="booking-summary-wrapper">
      <div class="booking-details"></div>

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

  </div>
</main>

<?php include "../templates/footer.php"; ?>