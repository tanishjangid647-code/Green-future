<?php
$page_title = "User Dashboard";
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';

require_login();
$user = current_user();

// Fetch statistics
$stmt = $pdo->prepare("SELECT COUNT(*) FROM trees WHERE user_id = ?");
$stmt->execute([$user['id']]);
$user_trees = $stmt->fetchColumn();

$stmt = $pdo->prepare("SELECT COUNT(*) FROM campaign_participants WHERE user_id = ?");
$stmt->execute([$user['id']]);
$user_campaigns = $stmt->fetchColumn();

$stmt = $pdo->prepare("SELECT SUM(carbon_offset_kg) FROM trees WHERE user_id = ?");
$stmt->execute([$user['id']]);
$total_co2 = $stmt->fetchColumn() ?: 0;

// Fetch upcoming enrolled campaigns
$stmt = $pdo->prepare("
  SELECT c.* FROM campaigns c
  JOIN campaign_participants cp ON c.id = cp.campaign_id
  WHERE cp.user_id = ? AND c.event_date >= CURDATE()
  ORDER BY c.event_date ASC LIMIT 3
");
$stmt->execute([$user['id']]);
$enrolled_drives = $stmt->fetchAll();
?>

<div class="container py-4">
  <div class="row g-4">
    <!-- Sidebar -->
    <div class="col-lg-3">
      <?php require_once __DIR__ . '/../includes/sidebar.php'; ?>
    </div>

    <!-- Main Content -->
    <div class="col-lg-9">
      <!-- Welcome Banner -->
      <div class="glass-card p-4 mb-4 rounded-4 bg-success bg-gradient text-white border-0 shadow-sm">
        <div class="row align-items-center">
          <div class="col-md-8">
            <h3 class="fw-bold mb-1">Hello, <?php echo sanitize($user['full_name']); ?>! 👋</h3>
            <p class="mb-0 text-white-50">Welcome to your Green Dashboard. You have offset <strong><?php echo number_format($total_co2, 1); ?> kg of CO₂</strong> so far!</p>
          </div>
          <div class="col-md-4 text-md-end mt-3 mt-md-0">
            <a href="<?php echo base_url('campaigns.php'); ?>" class="btn btn-accent px-4 py-2 rounded-pill font-weight-bold">
              <i class="fas fa-plus-circle me-1"></i> Join New Drive
            </a>
          </div>
        </div>
      </div>

      <!-- Live Weather Card -->
      <div
    class="card p-3 mb-4 rounded-4"
    id="weather-widget"
    data-city="<?php echo sanitize(current_user()['city'] ?? 'Mumbai'); ?>"
>
        <!-- Loaded via weather-ai.js -->
      </div>

      <!-- Quick Metrics Grid -->
      <div class="row g-3 mb-4">
        <div class="col-md-4">
          <div class="glass-card p-3 rounded-4 border-start border-4 border-success">
            <div class="d-flex align-items-center justify-content-between">
              <div>
                <small class="text-muted d-block">Trees Planted</small>
                <h3 class="fw-bold mb-0 text-success"><?php echo $user_trees; ?></h3>
              </div>
              <div class="p-3 bg-success bg-opacity-10 text-success rounded-circle">
                <i class="fas fa-tree fs-4"></i>
              </div>
            </div>
          </div>
        </div>

        <div class="col-md-4">
          <div class="glass-card p-3 rounded-4 border-start border-4 border-warning">
            <div class="d-flex align-items-center justify-content-between">
              <div>
                <small class="text-muted d-block">Campaigns Joined</small>
                <h3 class="fw-bold mb-0 text-warning"><?php echo $user_campaigns; ?></h3>
              </div>
              <div class="p-3 bg-warning bg-opacity-10 text-warning rounded-circle">
                <i class="fas fa-calendar-check fs-4"></i>
              </div>
            </div>
          </div>
        </div>

        <div class="col-md-4">
          <div class="glass-card p-3 rounded-4 border-start border-4 border-primary">
            <div class="d-flex align-items-center justify-content-between">
              <div>
                <small class="text-muted d-block">Reward Points</small>
                <h3 class="fw-bold mb-0 text-primary"><?php echo number_format($user['reward_points']); ?></h3>
              </div>
              <div class="p-3 bg-primary bg-opacity-10 text-primary rounded-circle">
                <i class="fas fa-award fs-4"></i>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Enrolled Upcoming Drives -->
      <div class="glass-card p-4 rounded-4 mb-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <h5 class="fw-bold mb-0"><i class="fas fa-calendar-alt text-success me-2"></i> My Upcoming Plantation Drives</h5>
          <a href="<?php echo base_url('user/book-drive.php'); ?>" class="btn btn-sm btn-outline-success rounded-pill">Explore Drives</a>
        </div>

        <?php if (!empty($enrolled_drives)): ?>
          <div class="table-responsive">
            <table class="table align-middle">
              <thead>
                <tr class="text-muted small">
                  <th>CAMPAIGN TITLE</th>
                  <th>LOCATION</th>
                  <th>DATE & TIME</th>
                  <th>STATUS</th>
                  <th>ACTION</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($enrolled_drives as $drive): ?>
                  <tr>
                    <td class="fw-semibold text-dark"><?php echo sanitize($drive['title']); ?></td>
                    <td><i class="fas fa-map-marker-alt text-danger me-1"></i> <?php echo sanitize($drive['city']); ?></td>
                    <td><?php echo date('M d, Y', strtotime($drive['event_date'])); ?> at <?php echo date('h:i A', strtotime($drive['event_time'])); ?></td>
                    <td><span class="badge bg-success">Enrolled</span></td>
                    <td>
                      <a href="<?php echo base_url('campaign-detail.php?id=' . $drive['id']); ?>" class="btn btn-sm btn-primary-green rounded-pill">View Details</a>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        <?php else: ?>
          <div class="text-center py-4 text-muted">
            <i class="fas fa-leaf fs-1 mb-2 text-success opacity-50"></i>
            <p class="mb-2">You haven't joined any upcoming plantation drives yet.</p>
            <a href="<?php echo base_url('campaigns.php'); ?>" class="btn btn-sm btn-primary-green rounded-pill">Find Campaigns Near You</a>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
