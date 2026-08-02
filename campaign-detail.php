<?php
$page_title = "Campaign Details";
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';

$id = intval($_GET['id'] ?? 1);

// Join action handler
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'join') {
    require_login();
    $user_id = $_SESSION['user_id'];
    
    // Check if already registered
    $stmt = $pdo->prepare("SELECT id FROM campaign_participants WHERE campaign_id = ? AND user_id = ?");
    $stmt->execute([$id, $user_id]);
    if (!$stmt->fetch()) {
        $stmt = $pdo->prepare("INSERT INTO campaign_participants (campaign_id, user_id) VALUES (?, ?)");
        $stmt->execute([$id, $user_id]);

        $stmt = $pdo->prepare("UPDATE campaigns SET current_volunteers = current_volunteers + 1 WHERE id = ?");
        $stmt->execute([$id]);

        log_activity("Joined campaign #{$id}");
        set_flash('success', 'Successfully registered for this plantation drive!');
    } else {
        set_flash('error', 'You are already registered for this campaign.');
    }
    header('Location: ' . base_url('campaign-detail.php?id=' . $id));
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM campaigns WHERE id = ?");
$stmt->execute([$id]);
$camp = $stmt->fetch();

if (!$camp) {
    echo "<div class='container py-5 text-center'><h3>Campaign Not Found</h3></div>";
    require_once __DIR__ . '/includes/footer.php';
    exit;
}

$user = current_user();
$is_registered = false;
if ($user) {
    $stmt = $pdo->prepare("SELECT id FROM campaign_participants WHERE campaign_id = ? AND user_id = ?");
    $stmt->execute([$id, $user['id']]);
    $is_registered = (bool)$stmt->fetch();
}

$reviews = $pdo->query("SELECT r.*, u.full_name FROM reviews r LEFT JOIN users u ON r.user_id = u.id WHERE r.campaign_id = {$id} ORDER BY r.id DESC")->fetchAll();
?>

<div class="container py-5">
  <div class="row g-4">
    <div class="col-lg-8">
      <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
        <img src="https://picsum.photos/1200/500?random=<?php echo $camp['id']; ?>" class="img-fluid" style="height: 350px; object-fit: cover;" alt="Banner">
        <div class="card-body p-4 p-md-5">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-2">
              <i class="fas fa-certificate me-1"></i> Official NGO Drive
            </span>
            <span class="text-muted small"><i class="fas fa-calendar me-1"></i> <?php echo date('F d, Y', strtotime($camp['event_date'])); ?></span>
          </div>

          <h2 class="fw-bold mb-3"><?php echo sanitize($camp['title']); ?></h2>
          <p class="text-secondary leading-relaxed mb-4"><?php echo nl2br(sanitize($camp['description'])); ?></p>

          <h5 class="fw-bold mb-3"><i class="fas fa-info-circle text-success me-2"></i> Event Specifications</h5>
          <div class="row g-3 mb-4">
            <div class="col-md-6">
              <div class="p-3 bg-light rounded-3">
                <small class="text-muted d-block">Organizing Body</small>
                <strong class="text-dark"><?php echo sanitize($camp['organizer']); ?></strong>
              </div>
            </div>
            <div class="col-md-6">
              <div class="p-3 bg-light rounded-3">
                <small class="text-muted d-block">Target Tree Species</small>
                <strong class="text-success"><?php echo sanitize($camp['tree_species']); ?></strong>
              </div>
            </div>
            <div class="col-md-6">
              <div class="p-3 bg-light rounded-3">
                <small class="text-muted d-block">Event Time</small>
                <strong class="text-dark"><?php echo date('h:i A', strtotime($camp['event_time'])); ?> IST</strong>
              </div>
            </div>
            <div class="col-md-6">
              <div class="p-3 bg-light rounded-3">
                <small class="text-muted d-block">Volunteer Slots</small>
                <strong class="text-primary"><?php echo $camp['current_volunteers']; ?> / <?php echo $camp['max_volunteers']; ?> Joined</strong>
              </div>
            </div>
          </div>

          <!-- Reviews Section -->
          <h5 class="fw-bold mb-3"><i class="fas fa-star text-warning me-2"></i> Participant Feedback & Reviews</h5>
          <?php if (!empty($reviews)): ?>
            <?php foreach ($reviews as $rev): ?>
              <div class="border-bottom py-3">
                <div class="d-flex justify-content-between align-items-center mb-1">
                  <strong class="text-dark"><?php echo sanitize($rev['full_name']); ?></strong>
                  <div class="text-warning small">
                    <?php for($i=1; $i<=5; $i++): ?>
                      <i class="fas fa-star<?php echo $i <= $rev['rating'] ? '' : '-o'; ?>"></i>
                    <?php endfor; ?>
                  </div>
                </div>
                <p class="small text-muted mb-0"><?php echo sanitize($rev['comment']); ?></p>
              </div>
            <?php endforeach; ?>
          <?php else: ?>
            <p class="small text-muted">No reviews posted yet for this campaign.</p>
          <?php endif; ?>
        </div>
      </div>
    </div>

    <!-- Sidebar Card -->
    <div class="col-lg-4">
      <div class="glass-card p-4 rounded-4 sticky-top" style="top: 90px;">
        <h5 class="fw-bold mb-3">Registration & Location</h5>
        <p class="small text-muted mb-3"><i class="fas fa-map-marker-alt text-danger me-2"></i> <?php echo sanitize($camp['location_address']); ?>, <?php echo sanitize($camp['city']); ?></p>

        <?php if ($is_registered): ?>
          <div class="alert alert-success rounded-3 text-center mb-3">
            <i class="fas fa-check-circle me-1"></i> You are enrolled in this drive!
          </div>
        <?php else: ?>
          <form action="<?php echo base_url('campaign-detail.php?id=' . $camp['id']); ?>" method="POST">
            <input type="hidden" name="action" value="join">
            <button type="submit" class="btn btn-primary-green w-100 py-2 fs-6 mb-3">
              <i class="fas fa-user-plus me-1"></i> Join Plantation Drive
            </button>
          </form>
        <?php endif; ?>

        <!-- QR Registration Tag -->
        <div class="text-center p-3 bg-light rounded-3">
          <small class="text-muted d-block mb-2">Scan QR for Check-in Badge</small>
          <img src="https://api.qrserver.com/v1/create-qr-code/?size=140x140&data=GF-CAMPAIGN-<?php echo $camp['id']; ?>" class="img-fluid rounded border" alt="QR">
        </div>
      </div>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
