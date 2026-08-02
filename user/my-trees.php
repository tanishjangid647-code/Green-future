<?php
$page_title = "My Adopted Trees";
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';

require_login();
$user = current_user();

$stmt = $pdo->prepare("
  SELECT t.*, c.title as campaign_name 
  FROM trees t
  LEFT JOIN campaigns c ON t.campaign_id = c.id
  WHERE t.user_id = ?
  ORDER BY t.created_at DESC
");
$stmt->execute([$user['id']]);
$trees = $stmt->fetchAll();
?>

<div class="container py-4">
  <div class="row g-4">
    <div class="col-lg-3">
      <?php require_once __DIR__ . '/../includes/sidebar.php'; ?>
    </div>

    <div class="col-lg-9">
      <div class="glass-card p-4 rounded-4 mb-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <div>
            <h4 class="fw-bold mb-1"><i class="fas fa-seedling text-success me-2"></i> My Adopted & Planted Trees</h4>
            <p class="text-muted small mb-0">Track real-time growth, health status, and GPS locations of your trees.</p>
          </div>
        </div>

        <?php if (!empty($trees)): ?>
          <div class="row g-4 mt-1">
            <?php foreach ($trees as $tree): ?>
              <div class="col-md-6">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100">
                  <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                      <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill">
                        <?php echo sanitize($tree['tree_code']); ?>
                      </span>
                      <span class="badge <?php 
                        if ($tree['health_status'] === 'Healthy') echo 'bg-success';
                        elseif ($tree['health_status'] === 'Needs Water') echo 'bg-warning text-dark';
                        else echo 'bg-danger';
                      ?>">
                        <?php echo sanitize($tree['health_status']); ?>
                      </span>
                    </div>

                    <h5 class="fw-bold text-dark mb-1"><?php echo sanitize($tree['species']); ?></h5>
                    <p class="small text-muted mb-3"><i class="fas fa-bullhorn text-success me-1"></i> Campaign: <?php echo sanitize($tree['campaign_name'] ?? 'Direct Adoption'); ?></p>

                    <div class="row g-2 mb-3 text-center">
                      <div class="col-4">
                        <div class="p-2 bg-light rounded-3">
                          <small class="text-muted d-block">Height</small>
                          <strong class="text-dark"><?php echo $tree['current_height_cm']; ?> cm</strong>
                        </div>
                      </div>
                      <div class="col-4">
                        <div class="col-12 p-2 bg-light rounded-3">
                          <small class="text-muted d-block">CO₂ Offset</small>
                          <strong class="text-success"><?php echo $tree['carbon_offset_kg']; ?> kg</strong>
                        </div>
                      </div>
                      <div class="col-4">
                        <div class="p-2 bg-light rounded-3">
                          <small class="text-muted d-block">Planted</small>
                          <strong class="text-dark"><?php echo date('d/m/Y', strtotime($tree['plantation_date'])); ?></strong>
                        </div>
                      </div>
                    </div>

                    <div class="d-flex gap-2">
                      <a href="<?php echo base_url('trees.php?code=' . $tree['tree_code']); ?>" class="btn btn-sm btn-primary-green w-100 rounded-pill">
                        <i class="fas fa-search-location me-1"></i> Live GPS & Timeline
                      </a>
                    </div>
                  </div>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        <?php else: ?>
          <div class="text-center py-5 text-muted">
            <i class="fas fa-tree fs-1 text-success opacity-50 mb-3"></i>
            <h5>No Trees Planted Yet</h5>
            <p>Join a plantation drive today to get your first tree assigned with a live QR tracking tag!</p>
            <a href="<?php echo base_url('campaigns.php'); ?>" class="btn btn-primary-green rounded-pill px-4">Explore Campaigns</a>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
