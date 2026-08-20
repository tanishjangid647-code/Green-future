<?php
$page_title = "Register";
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';
?>

<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-md-8 col-lg-7">
      <div class="glass-card p-4 p-md-5 rounded-4 shadow-lg">
        <div class="text-center mb-4">
          <div class="d-inline-flex p-3 bg-success bg-opacity-10 text-success rounded-circle mb-3 fs-2">
            <i class="fas fa-user-plus"></i>
          </div>
          <h3 class="fw-bold">Create Your Green Account</h3>
          <p class="text-muted small">Join thousands of green warriors protecting nature</p>
        </div>

        <form action="<?php echo base_url('auth/process-auth.php'); ?>" method="POST">
          <input type="hidden" name="action" value="signup">
          <input type="hidden"
       name="csrf_token"
       value="<?php echo htmlspecialchars(generate_csrf_token()); ?>">

          <div class="row g-3 mb-3">
            <div class="col-md-6">
              <label class="form-label font-weight-semibold">Full Name *</label>
              <input type="text" name="full_name" class="form-control" placeholder="Aarav Sharma" required>
            </div>
            <div class="col-md-6">
              <label class="form-label font-weight-semibold">Email Address *</label>
              <input type="email" name="email" class="form-control" placeholder="aarav@example.com" required>
            </div>
          </div>

          <div class="row g-3 mb-3">
            <div class="col-md-6">
              <label class="form-label font-weight-semibold">Phone Number</label>
              <input type="text" name="phone" class="form-control" placeholder="+91 9876543210">
            </div>
            <div class="col-md-6">
              <label class="form-label font-weight-semibold">Join As *</label>
             
            </div>
          </div>

          <div class="row g-3 mb-3">
            <div class="col-md-6">
              <label class="form-label font-weight-semibold">City</label>
              <input type="text" name="city" class="form-control" value="Mumbai">
            </div>
            <div class="col-md-6">
              <label class="form-label font-weight-semibold">State</label>
              <input type="text" name="state" class="form-control" value="Maharashtra">
            </div>
          </div>

          <div class="row g-3 mb-4">
            <div class="col-md-6">
              <label class="form-label font-weight-semibold">Password *</label>
              <input type="password" name="password" class="form-control" placeholder="••••••••" required>
            </div>
            <div class="col-md-6">
              <label class="form-label font-weight-semibold">Confirm Password *</label>
              <input type="password" name="confirm_password" class="form-control" placeholder="••••••••" required>
            </div>
          </div>

          <button type="submit" class="btn btn-primary-green w-100 py-2 fs-6">
            <i class="fas fa-check-circle me-2"></i> Register Account
          </button>
        </form>

        <div class="mt-4 pt-3 border-top text-center small text-muted">
          Already registered? <a href="<?php echo base_url('auth/login.php'); ?>" class="text-success fw-bold text-decoration-none">Login Here</a>
        </div>
      </div>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
