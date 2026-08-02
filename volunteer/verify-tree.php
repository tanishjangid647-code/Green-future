<?php
$page_title = "Verify & Update Tree";
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';

require_role(['volunteer', 'admin']);
$user = current_user();

$tree_id = $_GET['tree_id'] ?? null;
$tree = null;

if ($tree_id) {
    $stmt = $pdo->prepare("SELECT * FROM trees WHERE id = ?");
    $stmt->execute([$tree_id]);
    $tree = $stmt->fetch();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $target_tree_id = intval($_POST['tree_id']);
    $height = intval($_POST['height_cm']);
    $status = sanitize($_POST['health_status']);
    $note = sanitize($_POST['note'] ?? '');

    // Update tree main table
    $stmt = $pdo->prepare("UPDATE trees SET current_height_cm = ?, health_status = ? WHERE id = ?");
    $stmt->execute([$height, $status, $target_tree_id]);

    // Insert growth image / log
    $stmt = $pdo->prepare("INSERT INTO tree_images (tree_id, growth_height_cm, note, uploaded_by, image_url) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$target_tree_id, $height, $note, $user['id'], 'tree-log-default.jpg']);

    set_flash('success', 'Tree inspection record updated successfully!');
    header('Location: ' . base_url('volunteer/dashboard.php'));
    exit;
}

// Fetch all trees for dropdown selection
$stmt = $pdo->query("SELECT id, tree_code, species FROM trees ORDER BY tree_code ASC");
$all_trees = $stmt->fetchAll();
?>

<div class="container py-4">
  <div class="row g-4">
    <div class="col-lg-3">
      <?php require_once __DIR__ . '/../includes/sidebar.php'; ?>
    </div>

    <div class="col-lg-9">
      <div class="glass-card p-4 rounded-4 mb-4">
        <h4 class="fw-bold mb-3"><i class="fas fa-qrcode text-success me-2"></i> Log Growth Inspection & Tree Health</h4>

        <form action="<?php echo base_url('volunteer/verify-tree.php'); ?>" method="POST">
          <div class="mb-3">
            <label class="form-label font-weight-semibold">Select Tree Code / Tag</label>
            <select name="tree_id" class="form-select" required>
              <option value="">-- Choose Tree --</option>
              <?php foreach ($all_trees as $t): ?>
                <option value="<?php echo $t['id']; ?>" <?php echo ($tree && $tree['id'] == $t['id']) ? 'selected' : ''; ?>>
                  <?php echo sanitize($t['tree_code']); ?> - <?php echo sanitize($t['species']); ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="row g-3 mb-3">
            <div class="col-md-6">
              <label class="form-label font-weight-semibold">Current Height (cm)</label>
              <input type="number" name="height_cm" class="form-control" value="<?php echo $tree['current_height_cm'] ?? 50; ?>" required>
            </div>
            <div class="col-md-6">
              <label class="form-label font-weight-semibold">Health Condition</label>
              <select name="health_status" class="form-select" required>
                <option value="Healthy" <?php echo ($tree && $tree['health_status'] == 'Healthy') ? 'selected' : ''; ?>>Healthy</option>
                <option value="Needs Water" <?php echo ($tree && $tree['health_status'] == 'Needs Water') ? 'selected' : ''; ?>>Needs Water / Hydration</option>
                <option value="Damaged" <?php echo ($tree && $tree['health_status'] == 'Damaged') ? 'selected' : ''; ?>>Damaged / Pruning Required</option>
                <option value="Dead" <?php echo ($tree && $tree['health_status'] == 'Dead') ? 'selected' : ''; ?>>Dead / Re-planting Needed</option>
              </select>
            </div>
          </div>

          <div class="mb-4">
            <label class="form-label font-weight-semibold">Field Observation Notes</label>
            <textarea name="note" class="form-control" rows="3" placeholder="Sapling leaf condition, fertilizing applied, soil moisture details..."></textarea>
          </div>

          <button type="submit" class="btn btn-primary-green px-4">
            <i class="fas fa-check me-1"></i> Submit Inspection Record
          </button>
        </form>
      </div>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
