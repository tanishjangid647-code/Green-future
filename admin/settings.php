<?php
$page_title = "Website Settings";
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';

require_role('admin');

if (isset($_GET['download_db'])) {
    header('Content-Type: text/plain');
    header('Content-Disposition: attachment; filename="green_future_backup_' . date('Y-m-d') . '.sql"');
    readfile(__DIR__ . '/../database/schema.sql');
    exit;
}
?>

<div class="container-fluid py-4 px-lg-5">
  <div class="row g-4">
    <div class="col-lg-3 col-xl-2">
      <?php require_once __DIR__ . '/../includes/sidebar.php'; ?>
    </div>

    <div class="col-lg-9 col-xl-10">
      <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
          <h3 class="fw-bold mb-0">Website Configuration & System Backup</h3>
          <p class="text-muted small">Manage portal parameters and download database backups</p>
        </div>
      </div>

      <div class="glass-card p-4 rounded-4 mb-4">
        <h5 class="fw-bold mb-3"><i class="fas fa-database text-success me-2"></i> Database Maintenance</h5>
        <p class="text-muted small">Generate and download a full SQL dump backup file of the Green Future database.</p>
        <a href="<?php echo base_url('admin/settings.php?download_db=1'); ?>" class="btn btn-primary-green rounded-pill px-4">
          <i class="fas fa-download me-2"></i> Download Database Backup (.SQL)
        </a>
      </div>

      <div class="glass-card p-4 rounded-4">
        <h5 class="fw-bold mb-3"><i class="fas fa-sliders-h text-success me-2"></i> System Parameters</h5>
        <form action="<?php echo base_url('admin/settings.php'); ?>" method="POST">
          <div class="row g-3 mb-3">
            <div class="col-md-6">
              <label class="form-label font-weight-semibold">System Title</label>
              <input type="text" class="form-control" value="Green Future - Smart Tree Plantation Platform">
            </div>
            <div class="col-md-6">
              <label class="form-label font-weight-semibold">Contact Email</label>
              <input type="email" class="form-control" value="contact@greenfuture.org">
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label font-weight-semibold">Weather API Key (OpenWeatherMap)</label>
            <input type="text" class="form-control" value="demo_key_auto_simulated">
          </div>

          <button type="button" onclick="Swal.fire('Settings Saved', 'System parameters updated.', 'success')" class="btn btn-primary-green rounded-pill px-4">
            <i class="fas fa-save me-1"></i> Save Settings
          </button>
        </form>
      </div>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
