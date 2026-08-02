<?php
$user = current_user();
$role = $user['role'] ?? 'registered';
$current_page = basename($_SERVER['PHP_SELF']);
?>
<div class="glass-card p-3 mb-4 rounded-4">
  <div class="text-center py-3 border-bottom mb-3">
    <div class="position-relative d-inline-block">
      <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($user['full_name'] ?? 'User'); ?>&background=2E7D32&color=fff" class="rounded-circle shadow-sm" width="70" height="70" alt="Avatar">
      <span class="position-absolute bottom-0 end-0 bg-success border border-white rounded-circle p-1" title="Active"></span>
    </div>
    <h6 class="fw-bold mt-2 mb-0"><?php echo sanitize($user['full_name'] ?? ''); ?></h6>
    <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill mt-1">
      <i class="fas fa-award me-1"></i> <?php echo sanitize($user['badge'] ?? 'Green Starter'); ?>
    </span>
  </div>

  <div class="nav flex-column nav-pills gap-1">
    <?php if ($role === 'admin'): ?>
      <a href="<?php echo base_url('admin/dashboard.php'); ?>" class="nav-link <?php echo $current_page=='dashboard.php'?'active bg-success':'text-dark'; ?> rounded-3">
        <i class="fas fa-chart-pie me-2"></i> Dashboard Overview
      </a>
      <a href="<?php echo base_url('admin/campaigns.php'); ?>" class="nav-link <?php echo $current_page=='campaigns.php'?'active bg-success':'text-dark'; ?> rounded-3">
        <i class="fas fa-bullhorn me-2"></i> Manage Campaigns
      </a>
      <a href="<?php echo base_url('admin/trees.php'); ?>" class="nav-link <?php echo $current_page=='trees.php'?'active bg-success':'text-dark'; ?> rounded-3">
        <i class="fas fa-tree me-2"></i> Manage Trees
      </a>
      <a href="<?php echo base_url('admin/users.php'); ?>" class="nav-link <?php echo $current_page=='users.php'?'active bg-success':'text-dark'; ?> rounded-3">
        <i class="fas fa-users me-2"></i> Manage Users
      </a>
      <a href="<?php echo base_url('admin/certificates.php'); ?>" class="nav-link <?php echo $current_page=='certificates.php'?'active bg-success':'text-dark'; ?> rounded-3">
        <i class="fas fa-certificate me-2"></i> Certificates
      </a>
      <a href="<?php echo base_url('admin/reports.php'); ?>" class="nav-link <?php echo $current_page=='reports.php'?'active bg-success':'text-dark'; ?> rounded-3">
        <i class="fas fa-file-invoice me-2"></i> Analytics & Reports
      </a>
      <a href="<?php echo base_url('admin/settings.php'); ?>" class="nav-link <?php echo $current_page=='settings.php'?'active bg-success':'text-dark'; ?> rounded-3">
        <i class="fas fa-cog me-2"></i> Settings & Logs
      </a>

    <?php elseif ($role === 'volunteer'): ?>
      <a href="<?php echo base_url('volunteer/dashboard.php'); ?>" class="nav-link <?php echo $current_page=='dashboard.php'?'active bg-success':'text-dark'; ?> rounded-3">
        <i class="fas fa-tasks me-2"></i> Assigned Tasks
      </a>
      <a href="<?php echo base_url('volunteer/verify-tree.php'); ?>" class="nav-link <?php echo $current_page=='verify-tree.php'?'active bg-success':'text-dark'; ?> rounded-3">
        <i class="fas fa-qrcode me-2"></i> Verify & Update Trees
      </a>

    <?php else: ?>
      <a href="<?php echo base_url('user/dashboard.php'); ?>" class="nav-link <?php echo $current_page=='dashboard.php'?'active bg-success':'text-dark'; ?> rounded-3">
        <i class="fas fa-columns me-2"></i> My Dashboard
      </a>
      <a href="<?php echo base_url('user/book-drive.php'); ?>" class="nav-link <?php echo $current_page=='book-drive.php'?'active bg-success':'text-dark'; ?> rounded-3">
        <i class="fas fa-calendar-plus me-2"></i> Book Plantation Drive
      </a>
      <a href="<?php echo base_url('user/my-trees.php'); ?>" class="nav-link <?php echo $current_page=='my-trees.php'?'active bg-success':'text-dark'; ?> rounded-3">
        <i class="fas fa-seedling me-2"></i> My Adopted Trees
      </a>
      <a href="<?php echo base_url('user/certificates.php'); ?>" class="nav-link <?php echo $current_page=='certificates.php'?'active bg-success':'text-dark'; ?> rounded-3">
        <i class="fas fa-award me-2"></i> Certificates
      </a>
      <a href="<?php echo base_url('user/profile.php'); ?>" class="nav-link <?php echo $current_page=='profile.php'?'active bg-success':'text-dark'; ?> rounded-3">
        <i class="fas fa-user-edit me-2"></i> Edit Profile
      </a>
    <?php endif; ?>

    <hr class="my-2">
    <a href="<?php echo base_url('auth/logout.php'); ?>" class="nav-link text-danger rounded-3">
      <i class="fas fa-sign-out-alt me-2"></i> Logout
    </a>
  </div>
</div>
