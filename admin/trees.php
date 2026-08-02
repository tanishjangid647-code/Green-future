<?php
$page_title = "Manage Trees";
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';

require_role('admin');

// Handle Actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    if ($action === 'create') {
        $tree_code = 'TREE-2026-' . rand(100, 999);
        $species = sanitize($_POST['species']);
        $campaign_id = intval($_POST['campaign_id']);
        $plantation_date = $_POST['plantation_date'];
        $lat = floatval($_POST['latitude']);
        $lng = floatval($_POST['longitude']);
        $vol_id = !empty($_POST['volunteer_id']) ? intval($_POST['volunteer_id']) : null;
        $user_id = !empty($_POST['user_id']) ? intval($_POST['user_id']) : null;

        $stmt = $pdo->prepare("INSERT INTO trees (tree_code, species, campaign_id, plantation_date, latitude, longitude, assigned_volunteer_id, user_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$tree_code, $species, $campaign_id, $plantation_date, $lat, $lng, $vol_id, $user_id]);

        log_activity("Registered new tree #{$tree_code}");
        set_flash('success', "Tree #{$tree_code} registered!");
        header('Location: ' . base_url('admin/trees.php'));
        exit;
    }
}

$trees = $pdo->query("SELECT t.*, c.title as campaign_name, u.full_name as user_name FROM trees t LEFT JOIN campaigns c ON t.campaign_id = c.id LEFT JOIN users u ON t.user_id = u.id ORDER BY t.id DESC")->fetchAll();
$volunteers = $pdo->query("SELECT id, full_name FROM users WHERE role = 'volunteer'")->fetchAll();
$campaigns = $pdo->query("SELECT id, title FROM campaigns")->fetchAll();
$users = $pdo->query("SELECT id, full_name FROM users WHERE role = 'registered'")->fetchAll();
?>

<div class="container-fluid py-4 px-lg-5">
  <div class="row g-4">
    <div class="col-lg-3 col-xl-2">
      <?php require_once __DIR__ . '/../includes/sidebar.php'; ?>
    </div>

    <div class="col-lg-9 col-xl-10">
      <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
          <h3 class="fw-bold mb-0">Master Tree Registry & QR Tracking</h3>
          <p class="text-muted small">Monitor individual sapling metrics, GPS coordinates, and assigned volunteers</p>
        </div>
        <button class="btn btn-primary-green rounded-pill" data-bs-toggle="modal" data-bs-target="#createTreeModal">
          <i class="fas fa-plus me-1"></i> Register New Tree
        </button>
      </div>

      <div class="glass-card p-4 rounded-4">
        <div class="table-responsive">
          <table class="table align-middle">
            <thead class="table-light">
              <tr class="small text-muted">
                <th>CODE</th>
                <th>SPECIES</th>
                <th>CAMPAIGN</th>
                <th>ADOPTED BY</th>
                <th>HEIGHT</th>
                <th>HEALTH</th>
                <th>ACTION</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($trees as $tree): ?>
                <tr>
                  <td><strong class="text-success"><?php echo sanitize($tree['tree_code']); ?></strong></td>
                  <td><?php echo sanitize($tree['species']); ?></td>
                  <td><small><?php echo sanitize($tree['campaign_name'] ?? 'Direct Drive'); ?></small></td>
                  <td><small><?php echo sanitize($tree['user_name'] ?? 'Unassigned'); ?></small></td>
                  <td><strong><?php echo $tree['current_height_cm']; ?> cm</strong></td>
                  <td><span class="badge bg-success"><?php echo $tree['health_status']; ?></span></td>
                  <td>
                    <a href="<?php echo base_url('trees.php?code=' . $tree['tree_code']); ?>" class="btn btn-sm btn-outline-success rounded-pill" target="_blank">
                      <i class="fas fa-search-location"></i> Track
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

<!-- Modal -->
<div class="modal fade" id="createTreeModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content rounded-4 border-0">
      <div class="modal-header bg-success text-white">
        <h5 class="modal-title fw-bold"><i class="fas fa-seedling me-2"></i> Register New Sapling</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <form action="<?php echo base_url('admin/trees.php'); ?>" method="POST">
        <input type="hidden" name="action" value="create">
        <div class="modal-body p-4">
          <div class="row g-3 mb-3">
            <div class="col-md-6">
              <label class="form-label font-weight-semibold">Species Name *</label>
              <input type="text" name="species" class="form-control" value="Azadirachta indica (Neem)" required>
            </div>
            <div class="col-md-6">
              <label class="form-label font-weight-semibold">Campaign *</label>
              <select name="campaign_id" class="form-select" required>
                <?php foreach ($campaigns as $c): ?>
                  <option value="<?php echo $c['id']; ?>"><?php echo sanitize($c['title']); ?></option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>

          <div class="row g-3 mb-3">
            <div class="col-md-4">
              <label class="form-label font-weight-semibold">Plantation Date *</label>
              <input type="date" name="plantation_date" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
            </div>
            <div class="col-md-4">
              <label class="form-label font-weight-semibold">GPS Latitude</label>
              <input type="text" name="latitude" class="form-control" value="19.1488">
            </div>
            <div class="col-md-4">
              <label class="form-label font-weight-semibold">GPS Longitude</label>
              <input type="text" name="longitude" class="form-control" value="72.8815">
            </div>
          </div>

          <div class="row g-3 mb-3">
            <div class="col-md-6">
              <label class="form-label font-weight-semibold">Assigned Volunteer</label>
              <select name="volunteer_id" class="form-select">
                <option value="">-- None --</option>
                <?php foreach ($volunteers as $v): ?>
                  <option value="<?php echo $v['id']; ?>"><?php echo sanitize($v['full_name']); ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label font-weight-semibold">Assign User / Sponsor</label>
              <select name="user_id" class="form-select">
                <option value="">-- None --</option>
                <?php foreach ($users as $u): ?>
                  <option value="<?php echo $u['id']; ?>"><?php echo sanitize($u['full_name']); ?></option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>
        </div>
        <div class="modal-footer bg-light">
          <button type="button" class="btn btn-secondary rounded-pill" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary-green rounded-pill px-4">Register Tree</button>
        </div>
      </form>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
