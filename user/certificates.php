<?php
$page_title = "My Certificates";
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';

require_login();
$user = current_user();

$stmt = $pdo->prepare("
  SELECT cert.*, c.title as campaign_name, t.tree_code, t.species
  FROM certificates cert
  LEFT JOIN campaigns c ON cert.campaign_id = c.id
  LEFT JOIN trees t ON cert.tree_id = t.id
  WHERE cert.user_id = ?
  ORDER BY cert.issue_date DESC
");
$stmt->execute([$user['id']]);
$certificates = $stmt->fetchAll();
?>

<div class="container py-4">
  <div class="row g-4">
    <div class="col-lg-3">
      <?php require_once __DIR__ . '/../includes/sidebar.php'; ?>
    </div>

    <div class="col-lg-9">
      <div class="glass-card p-4 rounded-4 mb-4">
        <h4 class="fw-bold mb-1"><i class="fas fa-certificate text-success me-2"></i> Plantation Certificates</h4>
        <p class="text-muted small">Download or print your official eco-impact certificates verified with QR Codes.</p>

        <?php if (!empty($certificates)): ?>
          <div class="row g-4 mt-2">
            <?php foreach ($certificates as $cert): ?>
              <div class="col-md-6">
                <div class="card border border-success-subtle bg-white shadow-sm rounded-4 p-4 text-center">
                  <div class="d-inline-flex p-3 bg-success bg-opacity-10 text-success rounded-circle mx-auto mb-3 fs-2">
                    <i class="fas fa-award"></i>
                  </div>
                  <h6 class="fw-bold text-dark mb-1">Certificate of Eco Impact</h6>
                  <span class="badge bg-light text-dark border mb-3 mx-auto"><?php echo sanitize($cert['cert_number']); ?></span>
                  
                  <p class="small text-muted mb-2">Awarded to <strong><?php echo sanitize($user['full_name']); ?></strong> for planting tree <strong><?php echo sanitize($cert['species'] ?? 'Native Tree'); ?></strong>.</p>
                  
                  <small class="text-muted d-block mb-3">Issued Date: <?php echo date('M d, Y', strtotime($cert['issue_date'])); ?></small>

                  <div class="d-flex justify-content-center gap-2">
                    <a href="<?php echo base_url('certificate-verify.php?cert=' . $cert['cert_number']); ?>" class="btn btn-sm btn-outline-success rounded-pill px-3" target="_blank">
                      <i class="fas fa-qrcode me-1"></i> Verify QR
                    </a>
                    <button onclick="window.print();" class="btn btn-sm btn-primary-green rounded-pill px-3">
                      <i class="fas fa-download me-1"></i> Print / Download
                    </button>
                  </div>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        <?php else: ?>
          <div class="text-center py-5 text-muted">
            <i class="fas fa-award fs-1 text-warning opacity-50 mb-3"></i>
            <h5>No Certificates Issued Yet</h5>
            <p>Participate in completed plantation drives to receive automated verified certificates.</p>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
