<?php
$user = current_user();
?>
<nav class="navbar navbar-expand-lg navbar-custom sticky-top">
  <div class="container">
    <a class="navbar-brand d-flex align-items-center" href="<?php echo base_url('index.php'); ?>">
      <span class="brand-badge me-2"><i class="fas fa-tree"></i> Green Future</span>
    </a>
    
    <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain">
      <span class="navbar-toggler-icon"></span>
    </button>
    
    <div class="collapse navbar-collapse" id="navbarMain">
      <ul class="navbar-nav mx-auto mb-2 mb-lg-0 fw-semibold">
        <li class="nav-item"><a class="nav-link" href="<?php echo base_url('index.php'); ?>" data-i18n="nav-home">Home</a></li>
        <li class="nav-item"><a class="nav-link" href="<?php echo base_url('campaigns.php'); ?>" data-i18n="nav-campaigns">Campaigns</a></li>
        <li class="nav-item"><a class="nav-link" href="<?php echo base_url('trees.php'); ?>" data-i18n="nav-trees">Tree Tracking</a></li>
        <li class="nav-item"><a class="nav-link" href="<?php echo base_url('leaderboard.php'); ?>" data-i18n="nav-leaderboard">Leaderboard</a></li>
        <li class="nav-item"><a class="nav-link" href="<?php echo base_url('gallery.php'); ?>" data-i18n="nav-gallery">Gallery</a></li>
        <li class="nav-item"><a class="nav-link" href="<?php echo base_url('blog.php'); ?>" data-i18n="nav-blog">Blog</a></li>
        <li class="nav-item"><a class="nav-link" href="<?php echo base_url('contact.php'); ?>" data-i18n="nav-contact">Contact</a></li>
      </ul>
      
      <div class="d-flex align-items-center gap-3">
        <!-- Language Switcher -->
        <select id="lang-switch" class="form-select form-select-sm border-success text-success fw-bold rounded-pill" style="width: 100px;">
          <option value="en">English</option>
          <option value="hi">हिंदी (HI)</option>
        </select>
        
        <!-- Dark Mode Toggle Switch -->
        <div class="form-check form-switch mb-0">
          <input class="form-check-input" type="checkbox" id="dark-mode-toggle" title="Toggle Dark Mode">
          <label class="form-check-label text-muted small ms-1" for="dark-mode-toggle"><i class="fas fa-moon"></i></label>
        </div>

        <?php if ($user): ?>
          <div class="dropdown">
            <button class="btn btn-outline-success rounded-pill dropdown-toggle d-flex align-items-center gap-2" type="button" data-bs-toggle="dropdown">
              <i class="fas fa-user-circle fs-5"></i>
              <span><?php echo sanitize($user['full_name']); ?></span>
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow border-0 rounded-3">
              <li><h6 class="dropdown-header text-uppercase text-muted small">Role: <?php echo ucfirst($user['role']); ?></h6></li>
              <?php if ($user['role'] === 'admin'): ?>
                <li><a class="dropdown-item" href="<?php echo base_url('admin/dashboard.php'); ?>"><i class="fas fa-chart-line text-success me-2"></i> Admin Panel</a></li>
              <?php elseif ($user['role'] === 'volunteer'): ?>
                <li><a class="dropdown-item" href="<?php echo base_url('volunteer/dashboard.php'); ?>"><i class="fas fa-tasks text-success me-2"></i> Volunteer Portal</a></li>
           <?php else: ?>

  <li>
    <a class="dropdown-item"
       href="<?php echo base_url('user/dashboard.php'); ?>">
      <i class="fas fa-th-large text-success me-2"></i>
      My Dashboard
    </a>
  </li>

  <li>
    <a class="dropdown-item"
       href="<?php echo base_url('user/my-trees.php'); ?>">
      <i class="fas fa-seedling text-success me-2"></i>
      My Trees
    </a>
  </li>

  <li>
    <a class="dropdown-item"
       href="<?php echo base_url('user/wishlist.php'); ?>">
      <i class="fas fa-heart text-danger me-2"></i>
      My Wishlist
    </a>
  </li>




  
  <li>
    <a class="dropdown-item"
       href="<?php echo base_url('user/certificates.php'); ?>">
      <i class="fas fa-certificate text-success me-2"></i>
      Certificates
    </a>
  </li>

<?php endif; ?>
              <li><hr class="dropdown-divider"></li>
              <li><a class="dropdown-item text-danger" href="<?php echo base_url('auth/logout.php'); ?>"><i class="fas fa-sign-out-alt me-2"></i> Logout</a></li>
            </ul>
          </div>
        <?php else: ?>
          <a href="<?php echo base_url('auth/login.php'); ?>" class="btn btn-outline-success rounded-pill px-3 fw-semibold">Login</a>
          <a href="<?php echo base_url('auth/signup.php'); ?>" class="btn btn-primary-green px-4">Register</a>
        <?php endif; ?>
      </div>
    </div>
  </div>
</nav>

<!-- Flash Alerts Container -->
<?php 
$success_msg = get_flash('success');
$error_msg = get_flash('error');
if ($success_msg || $error_msg):
?>
<div class="container mt-3">
  <?php if ($success_msg): ?>
    <div class="alert alert-success alert-dismissible fade show rounded-4 shadow-sm" role="alert">
      <i class="fas fa-check-circle me-2"></i> <?php echo $success_msg; ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  <?php endif; ?>
  <?php if ($error_msg): ?>
    <div class="alert alert-danger alert-dismissible fade show rounded-4 shadow-sm" role="alert">
      <i class="fas fa-exclamation-triangle me-2"></i> <?php echo $error_msg; ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  <?php endif; ?>
</div>
<?php endif; ?>
