<?php
$page_title = "Campaigns";
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';

$search = sanitize($_GET['search'] ?? '');
$city_filter = sanitize($_GET['city'] ?? '');

$sql = "SELECT * FROM campaigns WHERE 1=1";
$params = [];

if (!empty($search)) {
    $sql .= " AND (title LIKE ? OR description LIKE ? OR tree_species LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if (!empty($city_filter)) {
    $sql .= " AND city = ?";
    $params[] = $city_filter;
}

$sql .= " ORDER BY event_date ASC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$campaigns = $stmt->fetchAll();

// Unique cities for dropdown
$cities = $pdo->query("SELECT DISTINCT city FROM campaigns ORDER BY city ASC")->fetchAll(PDO::FETCH_COLUMN);
?>

<div class="container py-5">
  <div class="row align-items-center mb-4">
    <div class="col-md-6">
      <h2 class="fw-bold mb-1">Tree Plantation Campaigns</h2>
      <p class="text-muted small">Explore active and upcoming reforestation projects near your city</p>
    </div>
    <div class="col-md-6">
      <form action="<?php echo base_url('campaigns.php'); ?>" method="GET" class="d-flex gap-2">
        <input type="text" name="search" class="form-control rounded-pill" placeholder="Search campaign, species..." value="<?php echo $search; ?>">
        <select name="city" class="form-select rounded-pill" style="max-width: 160px;">
          <option value="">All Cities</option>
          <?php foreach ($cities as $c): ?>
            <option value="<?php echo $c; ?>" <?php echo $city_filter === $c ? 'selected' : ''; ?>><?php echo $c; ?></option>
          <?php endforeach; ?>
        </select>
        <button type="submit" class="btn btn-primary-green rounded-pill px-4"><i class="fas fa-search"></i></button>
      </form>
    </div>
  </div>

  <div class="row g-4">
    <?php if (!empty($campaigns)): ?>
      <?php foreach ($campaigns as $camp): ?>
        <div class="col-md-4">
          <div class="campaign-card h-100 d-flex flex-column">
            <div class="campaign-img-wrapper">
              <img src="https://picsum.photos/600/350?random=<?php echo $camp['id']; ?>" alt="Banner">
              <span class="badge-status badge-upcoming"><?php echo ucfirst($camp['status']); ?></span>
            </div>
            <div class="p-4 d-flex flex-column flex-grow-1">
              <h5 class="fw-bold mb-2 text-dark"><?php echo sanitize($camp['title']); ?></h5>
              <p class="text-muted small flex-grow-1"><?php echo substr(sanitize($camp['description']), 0, 120) . '...'; ?></p>

              <div class="small text-secondary mb-3">
                <div class="mb-1"><i class="fas fa-map-marker-alt text-danger me-2"></i> <?php echo sanitize($camp['city']); ?>, <?php echo sanitize($camp['state']); ?></div>
                <div class="mb-1"><i class="fas fa-calendar text-success me-2"></i> <?php echo date('M d, Y', strtotime($camp['event_date'])); ?></div>
                <div><i class="fas fa-users text-primary me-2"></i> <?php echo $camp['current_volunteers']; ?> / <?php echo $camp['max_volunteers']; ?> Volunteers</div>
              </div>

              <a href="<?php echo base_url('campaign-detail.php?id=' . $camp['id']); ?>" class="btn btn-primary-green w-100 rounded-pill mt-auto">
                <i class="fas fa-info-circle me-1"></i> View Details & Join
              </a>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <div class="col-12 text-center py-5 text-muted">
        <i class="fas fa-search fs-1 text-success opacity-50 mb-3"></i>
        <h5>No Campaigns Found</h5>
        <p>Try searching with a different keyword or city filter.</p>
      </div>
    <?php endif; ?>
  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
