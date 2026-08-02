<?php
$page_title = "Update Profile";
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';

require_login();
$user = current_user();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = sanitize($_POST['full_name'] ?? '');
    $phone = sanitize($_POST['phone'] ?? '');
    $city = sanitize($_POST['city'] ?? '');
    $state = sanitize($_POST['state'] ?? '');

    $stmt = $pdo->prepare("UPDATE users SET full_name = ?, phone = ?, city = ?, state = ? WHERE id = ?");
    $stmt->execute([$full_name, $phone, $city, $state, $user['id']]);

    set_flash('success', 'Profile updated successfully!');
    header('Location: ' . base_url('user/profile.php'));
    exit;
}
?>

<div class="container py-4">
  <div class="row g-4">
    <div class="col-lg-3">
      <?php require_once __DIR__ . '/../includes/sidebar.php'; ?>
    </div>

    <div class="col-lg-9">
      <div class="glass-card p-4 rounded-4 mb-4">
        <h4 class="fw-bold mb-3"><i class="fas fa-user-edit text-success me-2"></i> Profile & Settings</h4>

        <form action="<?php echo base_url('user/profile.php'); ?>" method="POST">
          <div class="row g-3 mb-3">
            <div class="col-md-6">
              <label class="form-label font-weight-semibold">Full Name</label>
              <input type="text" name="full_name" class="form-control" value="<?php echo sanitize($user['full_name']); ?>" required>
            </div>
            <div class="col-md-6">
              <label class="form-label font-weight-semibold">Email Address (Read Only)</label>
              <input type="email" class="form-control bg-light" value="<?php echo sanitize($user['email']); ?>" readonly>
            </div>
          </div>

          <div class="row g-3 mb-3">
            <div class="col-md-4">
              <label class="form-label font-weight-semibold">Phone Number</label>
              <input type="text" name="phone" class="form-control" value="<?php echo sanitize($user['phone'] ?? ''); ?>">
            </div>
            <div class="col-md-4">
              <label class="form-label font-weight-semibold">City</label>
              <input type="text" name="city" class="form-control" value="<?php echo sanitize($user['city'] ?? ''); ?>">
            </div>
            <div class="col-md-4">
              <label class="form-label font-weight-semibold">State</label>
              <input type="text" name="state" class="form-control" value="<?php echo sanitize($user['state'] ?? ''); ?>">
            </div>
          </div>

          <button type="submit" class="btn btn-primary-green px-4">
            <i class="fas fa-save me-1"></i> Save Changes
          </button>
        </form>
      </div>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
