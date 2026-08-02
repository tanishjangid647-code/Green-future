<?php
$page_title = "Book Plantation Drive";
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';

require_login();
$user = current_user();

// Fetch upcoming campaigns
$stmt = $pdo->prepare("SELECT * FROM campaigns WHERE event_date >= CURDATE() AND status != 'cancelled' ORDER BY event_date ASC");
$stmt->execute();
$campaigns = $stmt->fetchAll();
?>

<div class="container py-4">
  <div class="row g-4">
    <div class="col-lg-3">
      <?php require_once __DIR__ . '/../includes/sidebar.php'; ?>
    </div>

    <div class="col-lg-9">
      <div class="glass-card p-4 rounded-4 mb-4">
        <h4 class="fw-bold mb-1"><i class="fas fa-calendar-plus text-success me-2"></i> Book Plantation Drive</h4>
        <p class="text-muted small">Select an upcoming tree plantation event to reserve your volunteer slot.</p>

        <div class="row g-4 mt-2">
          <?php foreach ($campaigns as $camp): ?>
            <div class="col-md-6">
              <div class="campaign-card h-100 d-flex flex-column">
                <div class="campaign-img-wrapper">
                  <img src="https://picsum.photos/600/350?random=<?php echo $camp['id']; ?>" alt="Banner">
                  <span class="badge-status badge-upcoming">Upcoming</span>
                </div>
                <div class="p-4 d-flex flex-column flex-grow-1">
                  <h5 class="fw-bold mb-2"><?php echo sanitize($camp['title']); ?></h5>
                  <p class="text-muted small flex-grow-1"><?php echo substr(sanitize($camp['description']), 0, 100) . '...'; ?></p>
                  
                  <div class="small text-secondary mb-3">
                    <div class="mb-1"><i class="fas fa-map-marker-alt text-danger me-2"></i> <?php echo sanitize($camp['city']); ?>, <?php echo sanitize($camp['state']); ?></div>
                    <div class="mb-1"><i class="fas fa-calendar text-success me-2"></i> <?php echo date('M d, Y', strtotime($camp['event_date'])); ?></div>
                    <div><i class="fas fa-users text-primary me-2"></i> <?php echo $camp['current_volunteers']; ?> / <?php echo $camp['max_volunteers']; ?> Joined</div>
                  </div>

                  <a href="<?php echo base_url('campaign-detail.php?id=' . $camp['id']); ?>" class="btn btn-primary-green w-100 rounded-pill mt-auto">
                    <i class="fas fa-ticket-alt me-1"></i> Book Slot Now
                  </a>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
