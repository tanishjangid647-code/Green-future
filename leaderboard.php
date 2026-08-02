<?php
$page_title = "Leaderboard & Gamification";
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';

// Fetch top green warriors
$stmt = $pdo->query("SELECT *, (SELECT COUNT(*) FROM trees WHERE user_id = users.id) as trees_count FROM users ORDER BY reward_points DESC LIMIT 10");
$leaderboard = $stmt->fetchAll();
?>

<div class="container py-5">
  <div class="text-center max-w-700 mx-auto mb-5">
    <span class="badge bg-warning text-dark px-3 py-2 rounded-pill fw-bold mb-2 shadow-sm"><i class="fas fa-trophy me-1"></i> Hall of Fame</span>
    <h2 class="fw-bold mb-2">Green Warriors Leaderboard</h2>
    <p class="text-muted">Recognizing top volunteers, citizens, and sponsors driving large-scale reforestation.</p>
  </div>

  <div class="row justify-content-center">
    <div class="col-lg-10">
      <!-- Top 3 Winners Podium -->
      <div class="row g-4 mb-5 align-items-end text-center">
        <?php if (count($leaderboard) >= 2): ?>
          <div class="col-md-4">
            <div class="glass-card p-4 rounded-4 border-2 border-secondary shadow">
              <span class="badge bg-secondary mb-2 fs-6"><i class="fas fa-medal"></i> SILVER #2</span>
              <h5 class="fw-bold mb-1"><?php echo sanitize($leaderboard[1]['full_name']); ?></h5>
              <span class="badge bg-warning text-dark mb-3"><?php echo sanitize($leaderboard[1]['badge']); ?></span>
              <h3 class="fw-bold text-success mb-0"><?php echo number_format($leaderboard[1]['reward_points']); ?> Pts</h3>
              <small class="text-muted"><?php echo $leaderboard[1]['trees_count']; ?> Trees Planted</small>
            </div>
          </div>
        <?php endif; ?>

        <?php if (count($leaderboard) >= 1): ?>
          <div class="col-md-4">
            <div class="glass-card p-4 rounded-4 border-3 border-warning bg-warning bg-opacity-10 shadow-lg" style="transform: scale(1.08);">
              <span class="badge bg-warning text-dark mb-2 fs-6 px-3 py-2"><i class="fas fa-crown me-1"></i> GOLD #1 CHAMPION</span>
              <h4 class="fw-bold mb-1"><?php echo sanitize($leaderboard[0]['full_name']); ?></h4>
              <span class="badge bg-success mb-3"><?php echo sanitize($leaderboard[0]['badge']); ?></span>
              <h2 class="fw-bold text-success mb-0"><?php echo number_format($leaderboard[0]['reward_points']); ?> Pts</h2>
              <small class="text-muted fw-bold"><?php echo $leaderboard[0]['trees_count']; ?> Trees Planted</small>
            </div>
          </div>
        <?php endif; ?>

        <?php if (count($leaderboard) >= 3): ?>
          <div class="col-md-4">
            <div class="glass-card p-4 rounded-4 border-2 border-danger shadow">
              <span class="badge bg-danger mb-2 fs-6"><i class="fas fa-award"></i> BRONZE #3</span>
              <h5 class="fw-bold mb-1"><?php echo sanitize($leaderboard[2]['full_name']); ?></h5>
              <span class="badge bg-warning text-dark mb-3"><?php echo sanitize($leaderboard[2]['badge']); ?></span>
              <h3 class="fw-bold text-success mb-0"><?php echo number_format($leaderboard[2]['reward_points']); ?> Pts</h3>
              <small class="text-muted"><?php echo $leaderboard[2]['trees_count']; ?> Trees Planted</small>
            </div>
          </div>
        <?php endif; ?>
      </div>

      <!-- Rankings Table -->
      <div class="glass-card p-4 rounded-4">
        <h5 class="fw-bold mb-3"><i class="fas fa-list-ol text-success me-2"></i> Overall Volunteer Standings</h5>
        <div class="table-responsive">
          <table class="table align-middle">
            <thead class="table-light">
              <tr class="small text-muted">
                <th>RANK</th>
                <th>WARRIOR NAME</th>
                <th>CITY</th>
                <th>ACHIEVEMENT BADGE</th>
                <th>TREES</th>
                <th>ECO POINTS</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($leaderboard as $index => $u): ?>
                <tr>
                  <td><strong>#<?php echo $index + 1; ?></strong></td>
                  <td>
                    <div class="d-flex align-items-center gap-2">
                      <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($u['full_name']); ?>&background=2E7D32&color=fff" class="rounded-circle" width="36" height="36" alt="Avatar">
                      <strong class="text-dark"><?php echo sanitize($u['full_name']); ?></strong>
                    </div>
                  </td>
                  <td><small><?php echo sanitize($u['city'] ?? 'India'); ?></small></td>
                  <td><span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill"><?php echo sanitize($u['badge']); ?></span></td>
                  <td><strong><?php echo $u['trees_count']; ?></strong></td>
                  <td><strong class="text-success"><?php echo number_format($u['reward_points']); ?></strong></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
