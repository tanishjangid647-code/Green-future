<?php
$page_title = "Public Tree Tracking Portal";
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';

$code = sanitize($_GET['code'] ?? 'TREE-2026-001');

$stmt = $pdo->prepare("
  SELECT t.*, c.title as campaign_name, u.full_name as planter_name, v.full_name as volunteer_name
  FROM trees t
  LEFT JOIN campaigns c ON t.campaign_id = c.id
  LEFT JOIN users u ON t.user_id = u.id
  LEFT JOIN users v ON t.assigned_volunteer_id = v.id
  WHERE t.tree_code = ? OR t.id = ?
");
$stmt->execute([$code, intval($code)]);
$tree = $stmt->fetch();

$tree_logs = [];
if ($tree) {
    $stmt = $pdo->prepare("SELECT ti.*, u.full_name FROM tree_images ti LEFT JOIN users u ON ti.uploaded_by = u.id WHERE ti.tree_id = ? ORDER BY ti.uploaded_at DESC");
    $stmt->execute([$tree['id']]);
    $tree_logs = $stmt->fetchAll();
}
?>

<div class="container py-5">
  <div class="text-center max-w-700 mx-auto mb-5">
    <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill mb-2">Live GPS Verification</span>
    <h2 class="fw-bold mb-2">Tree Tracking & Growth Timeline</h2>
    <p class="text-muted">Enter any official Tree Tag Code to inspect real-time growth measurements, health logs, and carbon offset history.</p>

    <!-- Search Form -->
    <form action="<?php echo base_url('trees.php'); ?>" method="GET" class="d-flex gap-2 max-w-500 mx-auto mt-3">
      <input type="text" name="code" class="form-control form-control-lg rounded-pill" placeholder="Enter Tree Tag (e.g. TREE-2026-001)" value="<?php echo $code; ?>" required>
      <button type="submit" class="btn btn-primary-green btn-lg rounded-pill px-4"><i class="fas fa-search"></i></button>
    </form>
  </div>

  <?php if ($tree): ?>
    <div class="row g-4">
      <!-- Tree Details Card -->
      <div class="col-lg-6">
        <div class="glass-card p-4 p-md-5 rounded-4 shadow-sm h-100">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <span class="badge bg-success fs-6 rounded-pill px-3 py-2"><?php echo sanitize($tree['tree_code']); ?></span>
            <span class="badge <?php 
              if ($tree['health_status'] === 'Healthy') echo 'bg-success';
              elseif ($tree['health_status'] === 'Needs Water') echo 'bg-warning text-dark';
              else echo 'bg-danger';
            ?> px-3 py-2 rounded-pill"><?php echo $tree['health_status']; ?></span>
          </div>

          <h3 class="fw-bold text-dark mb-1"><?php echo sanitize($tree['species']); ?></h3>
          <p class="text-muted small mb-4"><i class="fas fa-bullhorn text-success me-1"></i> Drive: <?php echo sanitize($tree['campaign_name'] ?? 'Reforestation Drive'); ?></p>

          <div class="row g-3 mb-4 text-center">
            <div class="col-4">
              <div class="p-3 bg-light rounded-3">
                <small class="text-muted d-block">Current Height</small>
                <h4 class="fw-bold text-dark mb-0"><?php echo $tree['current_height_cm']; ?> cm</h4>
              </div>
            </div>
            <div class="col-4">
              <div class="p-3 bg-light rounded-3">
                <small class="text-muted d-block">CO₂ Offset</small>
                <h4 class="fw-bold text-success mb-0"><?php echo $tree['carbon_offset_kg']; ?> kg</h4>
              </div>
            </div>
            <div class="col-4">
              <div class="p-3 bg-light rounded-3">
                <small class="text-muted d-block">Planted Date</small>
                <h6 class="fw-bold text-dark mb-0 mt-1"><?php echo date('M d, Y', strtotime($tree['plantation_date'])); ?></h6>
              </div>
            </div>
          </div>

          <div class="small text-secondary mb-4">
            <div class="mb-2"><i class="fas fa-user text-primary me-2"></i> Planted / Adopted By: <strong><?php echo sanitize($tree['planter_name'] ?? 'Green Community'); ?></strong></div>
            <div class="mb-2"><i class="fas fa-user-shield text-success me-2"></i> Monitoring Volunteer: <strong><?php echo sanitize($tree['volunteer_name'] ?? 'Aarav Sharma'); ?></strong></div>
            <div><i class="fas fa-tint text-info me-2"></i> Water Routine: <strong><?php echo sanitize($tree['water_schedule']); ?></strong></div>
          </div>

          <!-- QR Verification Widget -->
          <div class="d-flex align-items-center gap-3 p-3 bg-light rounded-3">
            <img src="https://api.qrserver.com/v1/create-qr-code/?size=100x100&data=GF-TREE-<?php echo $tree['tree_code']; ?>" class="rounded border" alt="QR">
            <div>
              <h6 class="fw-bold mb-1">Official QR Verification Tag</h6>
              <small class="text-muted">Scan with any mobile camera to view live survival telemetry and location coordinates.</small>
            </div>
          </div>
        </div>
      </div>

      <!-- GPS Map & Inspection Log -->
      <div class="col-lg-6">
        <div class="glass-card p-4 p-md-5 rounded-4 shadow-sm mb-4">
          <h5 class="fw-bold mb-3"><i class="fas fa-map-marked-alt text-danger me-2"></i> GPS Location Map</h5>
          <div class="rounded-3 overflow-hidden shadow-sm mb-3" style="height: 200px;">
            <iframe src="https://maps.google.com/maps?q=<?php echo $tree['latitude']; ?>,<?php echo $tree['longitude']; ?>&hl=es;z=14&output=embed" width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
          </div>
          <small class="text-muted"><i class="fas fa-crosshairs me-1"></i> Coordinates: Latitude <code><?php echo $tree['latitude']; ?></code>, Longitude <code><?php echo $tree['longitude']; ?></code></small>
        </div>

        <div class="glass-card p-4 rounded-4 shadow-sm">
          <h5 class="fw-bold mb-3"><i class="fas fa-history text-success me-2"></i> Inspection & Growth History</h5>
          <?php if (!empty($tree_logs)): ?>
            <ul class="timeline list-unstyled mb-0">
              <?php foreach ($tree_logs as $log): ?>
                <li class="border-start border-success border-2 ps-3 pb-3 position-relative">
                  <small class="text-muted d-block"><?php echo date('M d, Y', strtotime($log['uploaded_at'])); ?> by <?php echo sanitize($log['full_name'] ?? 'Volunteer'); ?></small>
                  <strong class="text-dark">Measured Height: <?php echo $log['growth_height_cm']; ?> cm</strong>
                  <p class="small text-muted mb-0"><?php echo sanitize($log['note'] ?? 'Routine health inspection clean.'); ?></p>
                </li>
              <?php endforeach; ?>
            </ul>
          <?php else: ?>
            <p class="small text-muted mb-0">Initial plantation log recorded. Volunteer follow-up inspection scheduled next week.</p>
          <?php endif; ?>
        </div>
      </div>
    </div>
  <?php else: ?>
    <div class="text-center py-5 text-muted">
      <i class="fas fa-exclamation-circle fs-1 text-warning mb-3"></i>
      <h5>Tree Code Not Found</h5>
      <p>Please check the tree tag number and try again.</p>
    </div>
  <?php endif; ?>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
