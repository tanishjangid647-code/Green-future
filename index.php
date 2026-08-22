<?php
$page_title = "Smart Tree Plantation Campaign Management System";
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';

// Fetch stats for counter
$total_trees = $pdo->query("SELECT COUNT(*) FROM trees")->fetchColumn();
$total_co2 = $pdo->query("SELECT SUM(carbon_offset_kg) FROM trees")->fetchColumn() ?: 125.5;
$total_volunteers = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'volunteer'")->fetchColumn();
$total_campaigns = $pdo->query("SELECT COUNT(*) FROM campaigns")->fetchColumn();

// Fetch featured upcoming campaigns
$featured_campaigns = $pdo->query("SELECT * FROM campaigns ORDER BY event_date ASC LIMIT 3")->fetchAll();
?>

<!-- Hero Section with Animated Nature & Leaf Animation -->
<section class="hero-section text-center text-md-start">
  <div class="hero-overlay"></div>
  <div class="container position-relative z-1">
    <div class="row align-items-center g-5">
      <div class="col-lg-7" data-aos="fade-right">
        <span class="badge bg-warning text-dark px-3 py-2 rounded-pill fw-bold mb-3 shadow-sm">
          <i class="fas fa-leaf me-1"></i> Digital Climate Action Platform
        </span>
        <h1 class="display-4 fw-extrabold mb-3 text-white" data-i18n="hero-title">
          Planting Trees, Securing Tomorrow
        </h1>
        <p class="lead mb-4 text-white-50" data-i18n="hero-subtitle">
          Join India's leading digital tree plantation movement. Track tree growth with real-time GPS & QR codes, receive official verified certificates, and calculate your CO₂ footprint.
        </p>

        <div class="d-flex flex-wrap gap-3">
          <a href="<?php echo base_url('campaigns.php'); ?>" class="btn btn-accent btn-lg px-4 shadow" data-i18n="btn-join">
            <i class="fas fa-seedling me-2"></i> Join Campaign
          </a>
          <button class="btn btn-outline-light btn-lg px-4 rounded-pill" data-bs-toggle="modal" data-bs-target="#carbonCalcModal">
            <i class="fas fa-calculator me-2"></i> Calculate CO₂ Footprint
          </button>
        </div>
      </div>

      <div class="col-lg-5" data-aos="fade-left">
        <!-- Live AI Tree Recommender Card -->
        <div class="card border-0 glass-card text-dark p-4 shadow-lg rounded-4">
          <div class="d-flex align-items-center justify-content-between mb-3">
            <h5 class="fw-bold mb-0 text-success"><i class="fas fa-robot me-2"></i> AI Species Advisor</h5>
            <span class="badge bg-success-subtle text-success">Smart Engine</span>
          </div>
      <form id="ai-recommender-form">

  <!-- Location -->
  <div class="mb-3">
    <label class="form-label small font-weight-semibold">Target Location</label>
    <select id="ai-city" class="form-select form-select-sm">
      <option value="Mumbai">Mumbai</option>
      <option value="Pune">Pune</option>
      <option value="Bangalore">Bangalore</option>
      <option value="Delhi">Delhi</option>
      <option value="Kolkata">Kolkata</option>
      <option value="Chennai">Chennai</option>
      <option value="Kochi">Kochi</option>
      <option value="Jaipur">Jaipur</option>
      <option value="Ahmedabad">Ahmedabad</option>
      <option value="Bhubaneswar">Bhubaneswar</option>
      <option value="Other">Other Indian Location</option>
    </select>
  </div>

  <!-- Environment -->
  <div class="mb-3">
    <label class="form-label small font-weight-semibold">Environment</label>
    <select id="ai-environment" class="form-select form-select-sm">
      <option value="urban">Urban / Residential</option>
      <option value="coastal">Coastal / Saline</option>
      <option value="riverbank">Riverbank / Wetland</option>
      <option value="dry">Dry / Semi-Arid</option>
      <option value="hilly">Hilly / Highland</option>
      <option value="rural">Rural / Agricultural</option>
    </select>
  </div>

  <!-- Soil + Water -->
  <div class="row g-2 mb-3">

    <div class="col-6">
      <label class="form-label small font-weight-semibold">Soil Type</label>
      <select id="ai-soil" class="form-select form-select-sm">
        <option value="loamy">Loamy / Rich</option>
        <option value="clay">Clay / Heavy</option>
        <option value="sandy">Sandy</option>
        <option value="saline">Saline</option>
        <option value="black">Black Soil</option>
        <option value="red">Red Soil</option>
      </select>
    </div>

    <div class="col-6">
      <label class="form-label small font-weight-semibold">Water Availability</label>
      <select id="ai-water" class="form-select form-select-sm">
        <option value="normal">Regular</option>
        <option value="low">Low / Limited</option>
        <option value="high">High / Wet</option>
        <option value="tidal">Tidal / Periodically Flooded</option>
      </select>
    </div>

  </div>

  <!-- Sunlight -->
  <div class="mb-3">
    <label class="form-label small font-weight-semibold">Sunlight</label>
    <select id="ai-sunlight" class="form-select form-select-sm">
      <option value="full">Full Sun</option>
      <option value="partial">Partial Shade</option>
      <option value="shade">Mostly Shaded</option>
    </select>
  </div>

  <!-- Purpose -->
  <div class="mb-3">
    <label class="form-label small font-weight-semibold">Plantation Goal</label>
    <select id="ai-purpose" class="form-select form-select-sm">
      <option value="shade">Urban Canopy / Shade</option>
      <option value="fruit">Fruit Production</option>
      <option value="air">Air Purification</option>
      <option value="biodiversity">Biodiversity</option>
      <option value="coastal_protection">Coastal Protection</option>
      <option value="erosion">Soil / Erosion Control</option>
    </select>
  </div>

  <button type="submit" class="btn btn-primary-green btn-sm w-100 py-2 rounded-pill">
    <i class="fas fa-magic me-1"></i>
    Analyze & Recommend Species
  </button>

</form>

<div id="ai-recommendation-result"></div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Animated Statistics Counter -->
<section class="py-5 bg-white border-bottom">
  <div class="container">
    <div class="row g-4 text-center">
      <div class="col-6 col-md-3" data-aos="zoom-in" data-aos-delay="100">
        <div class="p-3">
          <i class="fas fa-tree fs-1 text-success mb-2"></i>
          <h2 class="counter-number mb-0" data-target="<?php echo max($total_trees, 2450); ?>">0</h2>
          <span class="text-muted fw-semibold small" data-i18n="stat-planted">Trees Planted</span>
        </div>
      </div>
      <div class="col-6 col-md-3" data-aos="zoom-in" data-aos-delay="200">
        <div class="p-3">
          <i class="fas fa-smog fs-1 text-warning mb-2"></i>
          <h2 class="counter-number mb-0" data-target="<?php echo max(intval($total_co2), 53400); ?>">0</h2>
          <span class="text-muted fw-semibold small" data-i18n="stat-co2">Kg CO₂ Saved</span>
        </div>
      </div>
      <div class="col-6 col-md-3" data-aos="zoom-in" data-aos-delay="300">
        <div class="p-3">
          <i class="fas fa-users fs-1 text-primary mb-2"></i>
          <h2 class="counter-number mb-0" data-target="<?php echo max($total_volunteers, 1280); ?>">0</h2>
          <span class="text-muted fw-semibold small" data-i18n="stat-volunteers">Active Volunteers</span>
        </div>
      </div>
      <div class="col-6 col-md-3" data-aos="zoom-in" data-aos-delay="400">
        <div class="p-3">
          <i class="fas fa-bullhorn fs-1 text-danger mb-2"></i>
          <h2 class="counter-number mb-0" data-target="<?php echo max($total_campaigns, 48); ?>">0</h2>
          <span class="text-muted fw-semibold small" data-i18n="stat-campaigns">Successful Campaigns</span>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Featured Campaigns Section -->
<section class="py-5">
  <div class="container">
    <div class="d-flex justify-content-between align-items-end mb-4">
      <div>
        <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill mb-1">Upcoming Events</span>
        <h2 class="fw-bold mb-0">Featured Reforestation Drives</h2>
      </div>
      <a href="<?php echo base_url('campaigns.php'); ?>" class="btn btn-outline-success rounded-pill px-4">View All Campaigns</a>
    </div>

    <div class="row g-4">
      <?php foreach ($featured_campaigns as $camp): ?>
        <div class="col-md-4" data-aos="fade-up">
          <div class="campaign-card h-100 d-flex flex-column">
            <div class="campaign-img-wrapper">
              <img src="https://picsum.photos/600/350?random=<?php echo $camp['id']; ?>" alt="Campaign Banner">
              <span class="badge-status badge-upcoming"><?php echo ucfirst($camp['status']); ?></span>
            </div>
            <div class="p-4 d-flex flex-column flex-grow-1">
              <h5 class="fw-bold mb-2 text-dark"><?php echo sanitize($camp['title']); ?></h5>
              <p class="text-muted small flex-grow-1"><?php echo substr(sanitize($camp['description']), 0, 110) . '...'; ?></p>
              
              <div class="small text-secondary mb-3">
                <div class="mb-1"><i class="fas fa-map-marker-alt text-danger me-2"></i> <?php echo sanitize($camp['city']); ?>, <?php echo sanitize($camp['state']); ?></div>
                <div class="mb-1"><i class="fas fa-calendar text-success me-2"></i> <?php echo date('M d, Y', strtotime($camp['event_date'])); ?></div>
                <div><i class="fas fa-seedling text-warning me-2"></i> Species: <?php echo sanitize($camp['tree_species']); ?></div>
              </div>

              <a href="<?php echo base_url('campaign-detail.php?id=' . $camp['id']); ?>" class="btn btn-primary-green w-100 rounded-pill mt-auto">
                <i class="fas fa-arrow-right me-1"></i> View Campaign Details
              </a>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- System Features & Workflow -->
<section class="py-5 bg-white border-top border-bottom">
  <div class="container text-center">
    <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill mb-2">Platform Core</span>
    <h2 class="fw-bold mb-5">How Green Future Works</h2>

    <div class="row g-4">
      <div class="col-md-3" data-aos="fade-up" data-aos-delay="100">
        <div class="p-4 rounded-4 glass-card h-100">
          <div class="d-inline-flex p-3 bg-success bg-opacity-10 text-success rounded-circle mb-3 fs-2">
            <i class="fas fa-calendar-plus"></i>
          </div>
          <h5 class="fw-bold">1. Join a Drive</h5>
          <p class="small text-muted mb-0">Browse urban forestry drives near your city and reserve your volunteer slot.</p>
        </div>
      </div>
      <div class="col-md-3" data-aos="fade-up" data-aos-delay="200">
        <div class="p-4 rounded-4 glass-card h-100">
          <div class="d-inline-flex p-3 bg-warning bg-opacity-10 text-warning rounded-circle mb-3 fs-2">
            <i class="fas fa-qrcode"></i>
          </div>
          <h5 class="fw-bold">2. Plant & Tag Tree</h5>
          <p class="small text-muted mb-0">Plant a sapling and generate a unique QR code with exact GPS coordinates.</p>
        </div>
      </div>
      <div class="col-md-3" data-aos="fade-up" data-aos-delay="300">
        <div class="p-4 rounded-4 glass-card h-100">
          <div class="d-inline-flex p-3 bg-primary bg-opacity-10 text-primary rounded-circle mb-3 fs-2">
            <i class="fas fa-mobile-alt"></i>
          </div>
          <h5 class="fw-bold">3. Volunteer Monitoring</h5>
          <p class="small text-muted mb-0">Local volunteers inspect sapling growth, watering routines, and log height photos.</p>
        </div>
      </div>
      <div class="col-md-3" data-aos="fade-up" data-aos-delay="400">
        <div class="p-4 rounded-4 glass-card h-100">
          <div class="d-inline-flex p-3 bg-info bg-opacity-10 text-info rounded-circle mb-3 fs-2">
            <i class="fas fa-award"></i>
          </div>
          <h5 class="fw-bold">4. Verified Certificate</h5>
          <p class="small text-muted mb-0">Receive a downloadable QR-verified plantation certificate and earn eco points.</p>
        </div>
      </div>
    </div>
  </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
