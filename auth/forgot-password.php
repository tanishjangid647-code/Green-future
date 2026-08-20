<?php
$page_title = "Forgot Password";
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';
?>

<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-md-5">
      <div class="glass-card p-4 p-md-5 rounded-4 shadow-lg">
        <div class="text-center mb-4">
          <div class="d-inline-flex p-3 bg-warning bg-opacity-10 text-warning rounded-circle mb-3 fs-2">
            <i class="fas fa-key"></i>
          </div>
          <h4 class="fw-bold">Reset Your Password</h4>
          <p class="text-muted small">Enter your registered email address to receive password recovery link.</p>
        </div>

        <form action="<?php echo base_url('auth/process-auth.php'); ?>" method="POST">
          <input type="hidden" name="action" value="forgot_password">
           <input type="hidden"
           name="csrf_token"
           value="<?php echo htmlspecialchars(generate_csrf_token()); ?>">

          <div class="mb-4">
            <label class="form-label font-weight-semibold">Email Address</label>
            <input type="email" name="email" class="form-control" placeholder="user@greenfuture.org" required>
          </div>

          <button type="submit" class="btn btn-primary-green w-100 py-2">
            <i class="fas fa-paper-plane me-2"></i> Send Reset Instructions
          </button>
        </form>

        <div class="mt-4 text-center">
          <a href="<?php echo base_url('auth/login.php'); ?>" class="small text-muted text-decoration-none">
            <i class="fas fa-arrow-left me-1"></i> Back to Login
          </a>
        </div>
      </div>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
