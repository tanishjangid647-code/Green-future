<?php
$page_title = "Login";
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';
?>

<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-md-6 col-lg-5">
      <div class="glass-card p-4 p-md-5 rounded-4 shadow-lg">
        <div class="text-center mb-4">
          <div class="d-inline-flex p-3 bg-success bg-opacity-10 text-success rounded-circle mb-3 fs-2">
            <i class="fas fa-tree"></i>
          </div>
          <h3 class="fw-bold">Welcome Back</h3>
          <p class="text-muted small">Sign in to track trees, join drives, and manage campaigns</p>
        </div>

        <form action="<?php echo base_url('auth/process-auth.php'); ?>" method="POST">
          <input type="hidden" name="action" value="login">
          
          <div class="mb-3">
            <label class="form-label font-weight-semibold">Email Address</label>
            <div class="input-group">
              <span class="input-group-text bg-light border-end-0"><i class="fas fa-envelope text-muted"></i></span>
              <input type="email" name="email" class="form-control border-start-0" placeholder="admin@greenfuture.org" required>
            </div>
          </div>

          <div class="mb-3">
            <div class="d-flex justify-content-between align-items-center">
              <label class="form-label font-weight-semibold">Password</label>
              <a href="<?php echo base_url('auth/forgot-password.php'); ?>" class="small text-success text-decoration-none">Forgot Password?</a>
            </div>
            <div class="input-group">
              <span class="input-group-text bg-light border-end-0"><i class="fas fa-lock text-muted"></i></span>
              <input type="password" name="password" class="form-control border-start-0" placeholder="••••••••" required>
            </div>
          </div>

          <div class="mb-4 form-check">
            <input type="checkbox" class="form-check-input" id="remember">
            <label class="form-check-label small text-muted" for="remember">Remember me on this device</label>
          </div>

          <button type="submit" class="btn btn-primary-green w-100 py-2 fs-6">
            <i class="fas fa-sign-in-alt me-2"></i> Log In
          </button>
        </form>

        <div class="mt-4 pt-3 border-top text-center small text-muted">
          Don't have an account? <a href="<?php echo base_url('auth/signup.php'); ?>" class="text-success fw-bold text-decoration-none">Sign Up Here</a>
        </div>

        <div class="mt-4 p-3 bg-light rounded-3 small">
          <p class="fw-bold text-dark mb-1"><i class="fas fa-info-circle text-success me-1"></i> Quick Test Logins:</p>
          <div class="d-flex flex-wrap gap-1">
            <span class="badge bg-success">Admin: admin@greenfuture.org / Admin@123</span>
            <span class="badge bg-primary">Volunteer: volunteer@greenfuture.org / Volunteer@123</span>
            <span class="badge bg-secondary">User: user@greenfuture.org / User@123</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
