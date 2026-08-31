<?php
$page_title = "Environmental Blog";
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';

$blogs = $pdo->query("SELECT b.*, u.full_name as author_name FROM blogs b LEFT JOIN users u ON b.author_id = u.id ORDER BY b.id DESC")->fetchAll();
?>

<div class="container py-5">
  <div class="text-center max-w-700 mx-auto mb-5">
    <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill mb-2">Climate Knowledge</span>
    <h2 class="fw-bold mb-2">Reforestation & Eco Articles</h2>
    <p class="text-muted">Guides on sapling maintenance, urban micro-forest techniques, and carbon offsets.</p>
  </div>

  <div class="row g-4">
    <?php foreach ($blogs as $blog): ?>
      <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100">
       <img 
    src="<?php echo content_image_url($blog, 600, 350); ?>" 
    class="img-fluid" 
    style="height: 200px; object-fit: cover;" 
    alt="<?php echo sanitize($blog['title']); ?>"
>
          <div class="card-body p-4 d-flex flex-column">
            <span class="badge bg-success-subtle text-success me-auto mb-2"><?php echo sanitize($blog['category']); ?></span>
            <h5 class="fw-bold mb-2 text-dark"><?php echo sanitize($blog['title']); ?></h5>
            <p class="small text-muted flex-grow-1"><?php echo substr(sanitize($blog['content']), 0, 110) . '...'; ?></p>

            <div class="pt-3 border-top d-flex justify-content-between align-items-center small text-muted">
              <span><i class="fas fa-user text-success me-1"></i> <?php echo sanitize($blog['author_name'] ?? 'Editor'); ?></span>
              <span><i class="fas fa-eye me-1"></i> <?php echo $blog['views']; ?> views</span>
            </div>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
