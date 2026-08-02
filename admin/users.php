<?php
$page_title = "Manage Users";
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';

require_role('admin');

// Toggle User Status Action
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = intval($_POST['user_id']);
    $new_status = sanitize($_POST['status']);
    $stmt = $pdo->prepare("UPDATE users SET status = ? WHERE id = ?");
    $stmt->execute([$new_status, $user_id]);
    log_activity("Changed user #{$user_id} status to {$new_status}");
    set_flash('success', 'User status updated.');
    header('Location: ' . base_url('admin/users.php'));
    exit;
}

$all_users = $pdo->query("SELECT * FROM users ORDER BY id DESC")->fetchAll();
?>

<div class="container-fluid py-4 px-lg-5">
  <div class="row g-4">
    <div class="col-lg-3 col-xl-2">
      <?php require_once __DIR__ . '/../includes/sidebar.php'; ?>
    </div>

    <div class="col-lg-9 col-xl-10">
      <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
          <h3 class="fw-bold mb-0">User & Volunteer Administration</h3>
          <p class="text-muted small">Manage account roles, permissions, badges, and active statuses</p>
        </div>
      </div>

      <div class="glass-card p-4 rounded-4">
        <div class="table-responsive">
          <table class="table align-middle">
            <thead class="table-light">
              <tr class="small text-muted">
                <th>NAME & EMAIL</th>
                <th>ROLE</th>
                <th>CITY</th>
                <th>BADGE</th>
                <th>POINTS</th>
                <th>STATUS</th>
                <th>ACTION</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($all_users as $u): ?>
                <tr>
                  <td>
                    <strong class="text-dark d-block"><?php echo sanitize($u['full_name']); ?></strong>
                    <small class="text-muted"><?php echo sanitize($u['email']); ?></small>
                  </td>
                  <td>
                    <span class="badge <?php 
                      if ($u['role'] === 'admin') echo 'bg-danger';
                      elseif ($u['role'] === 'volunteer') echo 'bg-primary';
                      else echo 'bg-success';
                    ?>"><?php echo ucfirst($u['role']); ?></span>
                  </td>
                  <td><small><?php echo sanitize($u['city'] ?? 'N/A'); ?></small></td>
                  <td><span class="badge bg-warning text-dark"><?php echo sanitize($u['badge']); ?></span></td>
                  <td><strong><?php echo number_format($u['reward_points']); ?></strong></td>
                  <td>
                    <span class="badge <?php echo $u['status'] === 'active' ? 'bg-success' : 'bg-secondary'; ?>">
                      <?php echo ucfirst($u['status']); ?>
                    </span>
                  </td>
                  <td>
                    <form action="<?php echo base_url('admin/users.php'); ?>" method="POST" class="d-inline">
                      <input type="hidden" name="user_id" value="<?php echo $u['id']; ?>">
                      <input type="hidden" name="status" value="<?php echo $u['status'] === 'active' ? 'banned' : 'active'; ?>">
                      <button type="submit" class="btn btn-sm <?php echo $u['status'] === 'active' ? 'btn-outline-danger' : 'btn-outline-success'; ?> rounded-pill">
                        <?php echo $u['status'] === 'active' ? 'Ban User' : 'Activate'; ?>
                      </button>
                    </form>
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
