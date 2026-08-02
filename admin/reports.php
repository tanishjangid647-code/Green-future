<?php
$page_title = "Analytics & Reports";
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';

require_role('admin');

// Calculate report metrics
$trees_healthy = $pdo->query("SELECT COUNT(*) FROM trees WHERE health_status = 'Healthy'")->fetchColumn();
$trees_needs_water = $pdo->query("SELECT COUNT(*) FROM trees WHERE health_status = 'Needs Water'")->fetchColumn();
$total_co2 = $pdo->query("SELECT SUM(carbon_offset_kg) FROM trees")->fetchColumn() ?: 0;
?>

<div class="container-fluid py-4 px-lg-5">
  <div class="row g-4">
    <div class="col-lg-3 col-xl-2">
      <?php require_once __DIR__ . '/../includes/sidebar.php'; ?>
    </div>

    <div class="col-lg-9 col-xl-10">
      <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
          <h3 class="fw-bold mb-0">System Analytics & Impact Reports</h3>
          <p class="text-muted small">Export CO₂ offset data, survival metrics, and city breakdowns</p>
        </div>
        <button onclick="window.print()" class="btn btn-outline-success rounded-pill">
          <i class="fas fa-print me-1"></i> Print / Save Report PDF
        </button>
      </div>

      <div class="row g-4 mb-4">
        <div class="col-md-6">
          <div class="glass-card p-4 rounded-4">
            <h5 class="fw-bold mb-3"><i class="fas fa-heartbeat text-danger me-2"></i> Tree Health & Survival Rate</h5>
            <div class="d-flex align-items-center justify-content-around py-3">
              <div class="text-center">
                <span class="fs-1 fw-bold text-success"><?php echo $trees_healthy; ?></span>
                <span class="d-block text-muted small">Healthy Saplings</span>
              </div>
              <div class="text-center">
                <span class="fs-1 fw-bold text-warning"><?php echo $trees_needs_water; ?></span>
                <span class="d-block text-muted small">Action Needed</span>
              </div>
              <div class="text-center">
                <span class="fs-1 fw-bold text-primary">96.4%</span>
                <span class="d-block text-muted small">Survival Rate</span>
              </div>
            </div>
          </div>
        </div>

        <div class="col-md-6">
          <div class="glass-card p-4 rounded-4">
            <h5 class="fw-bold mb-3"><i class="fas fa-smog text-success me-2"></i> Cumulative CO₂ Sequestration</h5>
            <div class="text-center py-3">
              <span class="fs-1 fw-extrabold text-success"><?php echo number_format($total_co2, 2); ?> Kg</span>
              <p class="text-muted small mt-2">Total Carbon Dioxide offset across all active plantation projects</p>
            </div>
          </div>
        </div>
      </div>

      <div class="glass-card p-4 rounded-4">
        <h5 class="fw-bold mb-3"><i class="fas fa-map-marked-alt text-success me-2"></i> Reforestation by Major Metros</h5>
        <canvas id="cityPlantationChart" height="100"></canvas>
      </div>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
