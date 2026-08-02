<?php
$page_title = "Admin Portal";
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';

require_role('admin');

// Fetch admin statistics
$total_users = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'registered'")->fetchColumn();
$total_volunteers = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'volunteer'")->fetchColumn();
$total_campaigns = $pdo->query("SELECT COUNT(*) FROM campaigns")->fetchColumn();
$total_trees = $pdo->query("SELECT COUNT(*) FROM trees")->fetchColumn();
$total_certs = $pdo->query("SELECT COUNT(*) FROM certificates")->fetchColumn();

// Fetch recent activity logs
$activity_logs = $pdo->query("SELECT a.*, u.full_name FROM activity_logs a LEFT JOIN users u ON a.user_id = u.id ORDER BY a.created_at DESC LIMIT 6")->fetchAll();
?>

<div class="container-fluid py-4 px-lg-5">
  <div class="row g-4">
    <div class="col-lg-3 col-xl-2">
      <?php require_once __DIR__ . '/../includes/sidebar.php'; ?>
    </div>

    <div class="col-lg-9 col-xl-10">
      <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
          <h3 class="fw-bold mb-0">System Executive Dashboard</h3>
          <p class="text-muted small">Real-time statistics across campaigns, tree tracking, and volunteer operations</p>
        </div>
        <a href="<?php echo base_url('admin/campaigns.php'); ?>" class="btn btn-primary-green rounded-pill">
          <i class="fas fa-plus me-1"></i> Create Campaign
        </a>
      </div>

      <!-- Stat Cards Row -->
      <div class="row g-3 mb-4">
        <div class="col-md-4 col-xl">
          <div class="glass-card p-3 rounded-4 border-start border-4 border-success">
            <small class="text-muted d-block font-weight-semibold">Total Trees Planted</small>
            <h2 class="fw-bold mb-0 text-success"><?php echo number_format($total_trees); ?></h2>
          </div>
        </div>

        <div class="col-md-4 col-xl">
          <div class="glass-card p-3 rounded-4 border-start border-4 border-primary">
            <small class="text-muted d-block font-weight-semibold">Active Campaigns</small>
            <h2 class="fw-bold mb-0 text-primary"><?php echo number_format($total_campaigns); ?></h2>
          </div>
        </div>

        <div class="col-md-4 col-xl">
          <div class="glass-card p-3 rounded-4 border-start border-4 border-warning">
            <small class="text-muted d-block font-weight-semibold">Volunteers</small>
            <h2 class="fw-bold mb-0 text-warning"><?php echo number_format($total_volunteers); ?></h2>
          </div>
        </div>

        <div class="col-md-4 col-xl">
          <div class="glass-card p-3 rounded-4 border-start border-4 border-info">
            <small class="text-muted d-block font-weight-semibold">Registered Users</small>
            <h2 class="fw-bold mb-0 text-info"><?php echo number_format($total_users); ?></h2>
          </div>
        </div>

        <div class="col-md-4 col-xl">
          <div class="glass-card p-3 rounded-4 border-start border-4 border-secondary">
            <small class="text-muted d-block font-weight-semibold">Certificates Issued</small>
            <h2 class="fw-bold mb-0 text-secondary"><?php echo number_format($total_certs); ?></h2>
          </div>
        </div>
      </div>

      <!-- Analytics Charts Row -->
      <div class="row g-4 mb-4">
        <div class="col-lg-8">
          <div class="glass-card p-4 rounded-4">
            <h5 class="fw-bold mb-3"><i class="fas fa-chart-line text-success me-2"></i> Monthly Plantation Growth</h5>
            <canvas id="monthlyPlantationChart" height="120"></canvas>
          </div>
        </div>
        <div class="col-lg-4">
          <div class="glass-card p-4 rounded-4">
            <h5 class="fw-bold mb-3"><i class="fas fa-chart-pie text-success me-2"></i> Species Breakdown</h5>
            <canvas id="speciesDistributionChart" height="200"></canvas>
          </div>
        </div>
      </div>

      <!-- Recent System Activity Logs -->
      <div class="glass-card p-4 rounded-4">
        <h5 class="fw-bold mb-3"><i class="fas fa-history text-success me-2"></i> Audit & Activity Trail</h5>
        <div class="table-responsive">
          <table class="table align-middle small">
            <thead>
              <tr class="text-muted">
                <th>TIME</th>
                <th>PERFORMED BY</th>
                <th>ACTION DESCRIPTION</th>
                <th>IP ADDRESS</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($activity_logs as $log): ?>
                <tr>
                  <td><?php echo date('M d, H:i', strtotime($log['created_at'])); ?></td>
                  <td><strong class="text-dark"><?php echo sanitize($log['full_name'] ?? 'System'); ?></strong></td>
                  <td><?php echo sanitize($log['action']); ?></td>
                  <td><code><?php echo sanitize($log['ip_address']); ?></code></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
