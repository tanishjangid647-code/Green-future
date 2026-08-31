<?php
$page_title = "Tree Plantation Gallery";
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';

$gallery = $pdo->query("SELECT * FROM gallery ORDER BY id DESC")->fetchAll();
?>

<div class="container py-5">
  <div class="text-center max-w-700 mx-auto mb-5">
    <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill mb-2">Visual Impact</span>
    <h2 class="fw-bold mb-2">Plantation Drive Gallery</h2>
    <p class="text-muted">High-resolution captures from our urban forestry drives, youth events, and riparian restoration programs.</p>
  </div>

  <div class="row g-4">
    <?php foreach ($gallery as $item): ?>
      <div class="col-md-4 col-lg-3" data-aos="zoom-in">
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100">
        <img 
    src="<?php echo content_image_url($item, 600, 400); ?>" 
    class="img-fluid" 
    style="height: 220px; object-fit: cover;" 
    alt="<?php echo sanitize($item['title']); ?>"
>
          <div class="card-body p-3">
            <span class="badge bg-success-subtle text-success mb-1"><?php echo sanitize($item['category']); ?></span>
            <h6 class="fw-bold mb-0 text-dark"><?php echo sanitize($item['title']); ?></h6>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
