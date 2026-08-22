<?php
ob_start();
$page_title = "Manage Campaigns";
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';

require_role('admin');


// Handle Campaign Actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $action = $_POST['action'] ?? '';

    if ($action === 'create') {

        $title = sanitize($_POST['title']);
        $description = sanitize($_POST['description']);
        $organizer = sanitize($_POST['organizer']);
        $species = sanitize($_POST['tree_species']);

        $start_date = $_POST['start_date'];
        $end_date = $_POST['end_date'];
        $event_time = $_POST['event_time'];

        $city = sanitize($_POST['city']);
        $state = sanitize($_POST['state']);
        $address = sanitize($_POST['location_address']);
        $max_vols = intval($_POST['max_volunteers']);

        // Automatically determine campaign status
        $today = date('Y-m-d');

        if ($today < $start_date) {
            $status = 'upcoming';
        } elseif ($today > $end_date) {
            $status = 'completed';
        } else {
            $status = 'active';
        }

        $stmt = $pdo->prepare("
            INSERT INTO campaigns
            (
                title,
                description,
                organizer,
                tree_species,
                event_date,
                start_date,
                end_date,
                event_time,
                city,
                state,
                location_address,
                max_volunteers,
                status,
                created_by
            )
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");

        $stmt->execute([
            $title,
            $description,
            $organizer,
            $species,
            $start_date,
            $start_date,
            $end_date,
            $event_time,
            $city,
            $state,
            $address,
            $max_vols,
            $status,
            $_SESSION['user_id']
        ]);

        log_activity("Created campaign '{$title}'");
        set_flash('success', 'Campaign created successfully!');

        header('Location: ' . base_url('admin/campaigns.php'));
        exit;

    } elseif ($action === 'delete') {

        $id = intval($_POST['id']);

        $stmt = $pdo->prepare(
            "DELETE FROM campaigns WHERE id = ?"
        );

        $stmt->execute([$id]);

        log_activity("Deleted campaign #{$id}");
        set_flash('success', 'Campaign deleted.');

        header('Location: ' . base_url('admin/campaigns.php'));
        exit;
    }
}
$stmt = $pdo->query("SELECT * FROM campaigns ORDER BY id DESC");
$campaigns = $stmt->fetchAll();
?>

<div class="container-fluid py-4 px-lg-5">
  <div class="row g-4">
    <div class="col-lg-3 col-xl-2">
      <?php require_once __DIR__ . '/../includes/sidebar.php'; ?>
    </div>

    <div class="col-lg-9 col-xl-10">
      <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
          <h3 class="fw-bold mb-0">Manage Plantation Campaigns</h3>
          <p class="text-muted small">Create, edit, or launch reforestation campaigns</p>
        </div>
        <button class="btn btn-primary-green rounded-pill" data-bs-toggle="modal" data-bs-target="#createCampaignModal">
          <i class="fas fa-plus me-1"></i> Add New Campaign
        </button>
      </div>

      <div class="glass-card p-4 rounded-4">
        <div class="table-responsive">
          <table class="table align-middle">
            <thead class="table-light">
              <tr class="small text-muted">
                <th>TITLE</th>
                <th>ORGANIZER</th>
                <th>CITY</th>
                <th>DATE</th>
                <th>VOLUNTEERS</th>
                <th>STATUS</th>
                <th>ACTION</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($campaigns as $camp): ?>
                <tr>
                  <td><strong class="text-dark"><?php echo sanitize($camp['title']); ?></strong></td>
                  <td><small><?php echo sanitize($camp['organizer']); ?></small></td>
                  <td><i class="fas fa-map-marker-alt text-danger me-1"></i> <?php echo sanitize($camp['city']); ?></td>
                  <td><?php
if (!empty($camp['start_date']) && !empty($camp['end_date'])) {
    echo date('M d', strtotime($camp['start_date']))
       . ' - '
       . date('M d, Y', strtotime($camp['end_date']));
} else {
    echo date('M d, Y', strtotime($camp['event_date']));
}
?></td>
                  <td><?php echo $camp['current_volunteers']; ?> / <?php echo $camp['max_volunteers']; ?></td>
                  <td><?php
$today = date('Y-m-d');

if (!empty($camp['start_date']) && !empty($camp['end_date'])) {
    if ($today < $camp['start_date']) {
        $display_status = 'upcoming';
    } elseif ($today > $camp['end_date']) {
        $display_status = 'completed';
    } else {
        $display_status = 'active';
    }
} else {
    $display_status = $camp['status'];
}

$status_classes = [
    'upcoming' => 'bg-warning text-dark',
    'active' => 'bg-success',
    'completed' => 'bg-secondary',
    'cancelled' => 'bg-danger'
];

$status_class = $status_classes[$display_status] ?? 'bg-secondary';
?>

<span class="badge <?php echo $status_class; ?>">
    <?php echo ucfirst($display_status); ?>
</span></td>
                  <td>
                    <form action="<?php echo base_url('admin/campaigns.php'); ?>" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this campaign?');">
                      <input type="hidden" name="action" value="delete">
                      <input type="hidden" name="id" value="<?php echo $camp['id']; ?>">
                      <button type="submit" class="btn btn-sm btn-outline-danger rounded-circle"><i class="fas fa-trash"></i></button>
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

<!-- Create Campaign Modal -->
<div class="modal fade" id="createCampaignModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content rounded-4 border-0">
      <div class="modal-header bg-success text-white">
        <h5 class="modal-title fw-bold"><i class="fas fa-plus-circle me-2"></i> Create Plantation Campaign</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <form action="<?php echo base_url('admin/campaigns.php'); ?>" method="POST">
        <input type="hidden" name="action" value="create">
        <div class="modal-body p-4">
          <div class="mb-3">
            <label class="form-label font-weight-semibold">Campaign Title *</label>
            <input type="text" name="title" class="form-control" placeholder="e.g. Clean Air Reforestation Drive 2026" required>
          </div>
          <div class="mb-3">
            <label class="form-label font-weight-semibold">Description *</label>
            <textarea name="description" class="form-control" rows="3" required></textarea>
          </div>
          <div class="row g-3 mb-3">
            <div class="col-md-6">
              <label class="form-label font-weight-semibold">Organizer Name *</label>
              <input type="text" name="organizer" class="form-control" value="Green Future Foundation" required>
            </div>
            <div class="col-md-6">
              <label class="form-label font-weight-semibold">Target Tree Species *</label>
              <input type="text" name="tree_species" class="form-control" value="Neem, Peepal, Banyan" required>
            </div>
          </div>
         <div class="row g-3 mb-3">

  <div class="col-md-4">
    <label class="form-label font-weight-semibold">Start Date *</label>
    <input type="date" name="start_date" class="form-control" required>
  </div>

  <div class="col-md-4">
    <label class="form-label font-weight-semibold">End Date *</label>
    <input type="date" name="end_date" class="form-control" required>
  </div>

  <div class="col-md-4">
    <label class="form-label font-weight-semibold">Event Time *</label>
    <input type="time" name="event_time" class="form-control" value="08:00" required>
  </div>

</div>
          <div class="row g-3 mb-3">
            <div class="col-md-4">
              <label class="form-label font-weight-semibold">City *</label>
              <input type="text" name="city" class="form-control" value="Mumbai" required>
            </div>
            <div class="col-md-4">
              <label class="form-label font-weight-semibold">State *</label>
              <input type="text" name="state" class="form-control" value="Maharashtra" required>
            </div>
            <div class="col-md-4">
              <label class="form-label font-weight-semibold">Max Volunteer Slots</label>
              <input type="number" name="max_volunteers" class="form-control" value="200">
            </div>
          </div>
          <div class="mb-3">
            <label class="form-label font-weight-semibold">Exact Location Address</label>
            <input type="text" name="location_address" class="form-control" placeholder="Grounds, Sector, Park Name">
          </div>
        </div>
        <div class="modal-footer bg-light">
          <button type="button" class="btn btn-secondary rounded-pill" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary-green rounded-pill px-4">Publish Campaign</button>
        </div>
      </form>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
