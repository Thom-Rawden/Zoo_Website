<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    $_SESSION['last_site'] = '../public/account-page.php';
    header('Location: ../login-signup/login-signup-page.php');
    exit;
}

require_once __DIR__ . '/../login-signup/db.php';

// Generate CSRF token if not already set
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(16));
}

// Function to get user bookings
function get_user_bookings(?int $user_id): array
{
    if (empty($user_id) || !isset($GLOBALS['pdo'])) {
        return [];
    }

    try {
        $pdo = $GLOBALS['pdo'];
        $stmt = $pdo->prepare("
            SELECT id, user_id, room_id, booking_date, party_size, type, status, created_at
            FROM bookings
            WHERE user_id = ?
            ORDER BY created_at DESC
        ");
        $stmt->execute([(int)$user_id]);
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
      <h2>Your bookings</h2>

      <?php
      // flash messages
      if (!empty($_SESSION['booking_error'])) {
          echo '<div class="booking-message error">'.htmlspecialchars($_SESSION['booking_error']).'</div>';
          unset($_SESSION['booking_error']);
      }
      if (!empty($_SESSION['booking_success'])) {
          echo '<div class="booking-message success">'.htmlspecialchars($_SESSION['booking_success']).'</div>';
          unset($_SESSION['booking_success']);
      }

      $user_id = (int)($_SESSION['user_id'] ?? 0);
      $bookings = get_user_bookings($user_id);

      if (empty($bookings)): ?>
        <div class="booking-option">
          <p>No bookings found.</p>
        </div>
      <?php else:
        foreach ($bookings as $b): ?>
          <div class="booking-option">
            <strong><?php echo htmlspecialchars(ucfirst($b['type']) . ' booking'); ?></strong>

            <div class="meta">
              <div>Room ID: <?php echo htmlspecialchars($b['room_id']); ?></div>
              <div>Date: <?php echo htmlspecialchars($b['booking_date']); ?></div>
              <div>Party size: <?php echo (int)$b['party_size']; ?></div>
              <div>Status: <?php echo htmlspecialchars($b['status']); ?></div>
              <div>Created: <?php echo htmlspecialchars($b['created_at']); ?></div>
            </div>

            <div class="row" style="margin-top:0.6vh; gap:0.6vw;">
              <!-- Cancel -->
              <form method="post" action="../booking/cancel-booking.php" style="display:inline;">
                <input type="hidden" name="booking_id" value="<?php echo (int)$b['id']; ?>">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                <button type="submit" class="btn" onclick="return confirm('Cancel this booking?');">Cancel</button>
              </form>

              <!-- Confirm  -->
              <?php if ($b['status'] !== 'confirmed'): ?>
                <form method="post" action="../booking/confirm-booking.php" style="display:inline;">
                  <input type="hidden" name="booking_id" value="<?php echo (int)$b['id']; ?>">
                  <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                  <button type="submit" class="btn">Confirm</button>
                </form>
              <?php else: ?>
                <button class="btn" disabled>Confirmed</button>
              <?php endif; ?>
            </div>
          </div>
      <?php endforeach;
      endif;
      ?>
    </div>

    <div class="booking-summary-wrapper">
      <div class="booking-details"></div>
      <div class="booking-details"></div>
      <div class="booking-button"></div>
    </div>

  </div>
</main>

<?php include "../templates/footer.php"; ?>