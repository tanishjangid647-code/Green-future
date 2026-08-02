<?php
$page_title = "Verify Certificate";
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';

$cert_num = sanitize($_GET['cert'] ?? 'GF-CERT-2026-78901');

$stmt = $pdo->prepare("
  SELECT cert.*, u.full_name, c.title as campaign_name, t.tree_code, t.species, t.plantation_date
  FROM certificates cert
  LEFT JOIN users u ON cert.user_id = u.id
  LEFT JOIN campaigns c ON cert.campaign_id = c.id
  LEFT JOIN trees t ON cert.tree_id = t.id
  WHERE cert.cert_number = ?
");
$stmt->execute([$cert_num]);
$cert = $stmt->fetch();
?>

<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-lg-8">
      <?php if ($cert): ?>
        <div class="card border-3 border-success shadow-lg rounded-4 p-4 p-md-5 bg-white text-center">
          <div class="d-inline-flex p-3 bg-success bg-opacity-10 text-success rounded-circle mx-auto mb-3 fs-1">
            <i class="fas fa-certificate"></i>
          </div>

          <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-2 mx-auto mb-2 fw-bold">
            <i class="fas fa-check-circle me-1"></i> OFFICIAL VERIFIED CERTIFICATE
          </span>

          <h2 class="fw-bold text-success font-monospace mb-1"><?php echo sanitize($cert['cert_number']); ?></h2>
          <p class="text-muted small mb-4">Verification Registry ID: GF-AUTH-<?php echo md5($cert['cert_number']); ?></p>

          <div class="py-4 my-2 border-top border-bottom">
            <h4 class="fw-light mb-1">This certifies that</h4>
            <h2 class="fw-bold text-dark text-uppercase mb-3"><?php echo sanitize($cert['full_name']); ?></h2>
            <p class="lead text-secondary mb-0">
              has successfully planted and sponsored a <strong><?php echo sanitize($cert['species'] ?? 'Native Tree'); ?></strong> sapling 
              under the <strong><?php echo sanitize($cert['campaign_name'] ?? 'Mega Reforestation Drive'); ?></strong>.
            </p>
          </div>

          <div class="row g-3 my-3 text-start small">
            <div class="col-md-6">
              <strong>Tree Tag:</strong> <code><?php echo sanitize($cert['tree_code'] ?? 'TREE-2026-001'); ?></code>
            </div>
            <div class="col-md-6">
              <strong>Plantation Date:</strong> <?php echo date('F d, Y', strtotime($cert['plantation_date'] ?? $cert['issue_date'])); ?>
            </div>
            <div class="col-md-6">
              <strong>Verified By:</strong> Green Future NGO Secretariat
            </div>
            <div class="col-md-6">
              <strong>Issuance Status:</strong> Active & Valid
            </div>
          </div>

          <div class="mt-4 pt-3 border-top d-flex justify-content-center gap-3">
            <button onclick="window.print();" class="btn btn-primary-green rounded-pill px-4">
              <i class="fas fa-print me-1"></i> Print / Download PDF
            </button>
            <a href="<?php echo base_url('index.php'); ?>" class="btn btn-outline-secondary rounded-pill px-4">Home</a>
          </div>
        </div>
      <?php else: ?>
        <div class="card border-0 shadow rounded-4 p-5 text-center">
          <i class="fas fa-times-circle fs-1 text-danger mb-3"></i>
          <h4>Certificate Invalid or Not Found</h4>
          <p class="text-muted">The requested certificate number could not be verified in our registry database.</p>
        </div>
      <?php endif; ?>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
