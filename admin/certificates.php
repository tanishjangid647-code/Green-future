<?php
$page_title = "Manage Certificates";
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';

require_role('admin');

// Issue Certificate
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = intval($_POST['user_id']);
    $campaign_id = intval($_POST['campaign_id']);
    $tree_id = intval($_POST['tree_id']);
    $cert_number = 'GF-CERT-' . date('Y') . '-' . rand(10000, 99999);
    $qr_code = $cert_number . '-QR.png';

    $stmt = $pdo->prepare("INSERT INTO certificates (cert_number, user_id, campaign_id, tree_id, issue_date, qr_code) VALUES (?, ?, ?, ?, CURDATE(), ?)");
    $stmt->execute([$cert_number, $user_id, $campaign_id, $tree_id, $qr_code]);

    log_activity("Issued certificate #{$cert_number}");
    set_flash('success', "Certificate #{$cert_number} issued!");
    header('Location: ' . base_url('admin/certificates.php'));
    exit;
}

$certificates = $pdo->query("SELECT cert.*, u.full_name, c.title as campaign_name FROM certificates cert LEFT JOIN users u ON cert.user_id = u.id LEFT JOIN campaigns c ON cert.campaign_id = c.id ORDER BY cert.id DESC")->fetchAll();
$users = $pdo->query("SELECT id, full_name FROM users")->fetchAll();
$campaigns = $pdo->query("SELECT id, title FROM campaigns")->fetchAll();
$trees = $pdo->query("SELECT id, tree_code FROM trees")->fetchAll();
?>

<div class="container-fluid py-4 px-lg-5">
  <div class="row g-4">
    <div class="col-lg-3 col-xl-2">
      <?php require_once __DIR__ . '/../includes/sidebar.php'; ?>
    </div>

    <div class="col-lg-9 col-xl-10">
      <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
          <h3 class="fw-bold mb-0">Certificate Issuance & Verification</h3>
          <p class="text-muted small">Generate QR-verifiable certificates for planters and sponsors</p>
        </div>
        <button class="btn btn-primary-green rounded-pill" data-bs-toggle="modal" data-bs-target="#issueCertModal">
          <i class="fas fa-certificate me-1"></i> Issue Certificate
        </button>
      </div>

      <div class="glass-card p-4 rounded-4">
        <div class="table-responsive">
          <table class="table align-middle">
            <thead class="table-light">
              <tr class="small text-muted">
                <th>CERTIFICATE NO</th>
                <th>RECIPIENT</th>
                <th>CAMPAIGN</th>
                <th>ISSUE DATE</th>
                <th>ACTION</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($certificates as $c): ?>
                <tr>
                  <td><strong class="text-success"><?php echo sanitize($c['cert_number']); ?></strong></td>
                  <td><strong class="text-dark"><?php echo sanitize($c['full_name']); ?></strong></td>
                  <td><small><?php echo sanitize($c['campaign_name'] ?? 'Direct Plantation'); ?></small></td>
                  <td><?php echo date('M d, Y', strtotime($c['issue_date'])); ?></td>
                  <td>
                    <a href="<?php echo base_url('certificate-verify.php?cert=' . $c['cert_number']); ?>" class="btn btn-sm btn-outline-success rounded-pill" target="_blank">
                      <i class="fas fa-qrcode me-1"></i> Verify QR
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
<div class="modal fade" id="issueCertModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content rounded-4 border-0">
      <div class="modal-header bg-success text-white">
        <h5 class="modal-title fw-bold"><i class="fas fa-award me-2"></i> Issue New Certificate</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <form action="<?php echo base_url('admin/certificates.php'); ?>" method="POST">
        <div class="modal-body p-4">
          <div class="mb-3">
            <label class="form-label font-weight-semibold">Select Recipient User *</label>
            <select name="user_id" class="form-select" required>
              <?php foreach ($users as $u): ?>
                <option value="<?php echo $u['id']; ?>"><?php echo sanitize($u['full_name']); ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label font-weight-semibold">Campaign *</label>
            <select name="campaign_id" class="form-select" required>
              <?php foreach ($campaigns as $camp): ?>
                <option value="<?php echo $camp['id']; ?>"><?php echo sanitize($camp['title']); ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label font-weight-semibold">Tree Tag *</label>
            <select name="tree_id" class="form-select" required>
              <?php foreach ($trees as $tr): ?>
                <option value="<?php echo $tr['id']; ?>"><?php echo sanitize($tr['tree_code']); ?></option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>
        <div class="modal-footer bg-light">
          <button type="button" class="btn btn-secondary rounded-pill" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary-green rounded-pill px-4">Generate Certificate</button>
        </div>
      </form>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
