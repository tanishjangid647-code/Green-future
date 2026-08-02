<?php
$page_title = "Volunteer Dashboard";
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';

require_role(['volunteer', 'admin']);
$user = current_user();

// Fetch assigned trees
$stmt = $pdo->prepare("
  SELECT t.*, c.title as campaign_name 
  FROM trees t 
  LEFT JOIN campaigns c ON t.campaign_id = c.id
  WHERE t.assigned_volunteer_id = ? OR ? = 'admin'
  ORDER BY t.created_at DESC
");
$stmt->execute([$user['id'], $user['role']]);
$assigned_trees = $stmt->fetchAll();
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
            <h4 class="fw-bold mb-1"><i class="fas fa-tasks text-success me-2"></i> Volunteer Field Workspace</h4>
            <p class="text-muted small mb-0">Manage assigned saplings, inspect health parameters, and record growth measurements.</p>
          </div>
          <a href="<?php echo base_url('volunteer/verify-tree.php'); ?>" class="btn btn-primary-green rounded-pill px-4">
            <i class="fas fa-plus me-1"></i> Log Growth Inspection
          </a>
        </div>

        <div class="table-responsive">
          <table class="table align-middle">
            <thead class="table-light">
              <tr class="small text-muted">
                <th>TREE CODE</th>
                <th>SPECIES</th>
                <th>CAMPAIGN</th>
                <th>HEIGHT</th>
                <th>HEALTH STATUS</th>
                <th>WATER SCHEDULE</th>
                <th>ACTION</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($assigned_trees as $tree): ?>
                <tr>
                  <td><strong class="text-success"><?php echo sanitize($tree['tree_code']); ?></strong></td>
                  <td><?php echo sanitize($tree['species']); ?></td>
                  <td><small><?php echo sanitize($tree['campaign_name'] ?? 'N/A'); ?></small></td>
                  <td><strong><?php echo $tree['current_height_cm']; ?> cm</strong></td>
                  <td>
                    <span class="badge <?php 
                      if ($tree['health_status'] === 'Healthy') echo 'bg-success';
                      elseif ($tree['health_status'] === 'Needs Water') echo 'bg-warning text-dark';
                      else echo 'bg-danger';
                    ?>"><?php echo $tree['health_status']; ?></span>
                  </td>
                  <td><small class="text-muted"><?php echo sanitize($tree['water_schedule']); ?></small></td>
                  <td>
                    <a href="<?php echo base_url('volunteer/verify-tree.php?tree_id=' . $tree['id']); ?>" class="btn btn-sm btn-outline-success rounded-pill">
                      <i class="fas fa-edit me-1"></i> Update
                    </a>
                  </td>
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
