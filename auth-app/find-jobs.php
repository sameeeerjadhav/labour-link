<?php
require_once 'config.php';
if (!isset($_SESSION['user_id'])) { header('Location: index.php'); exit; }
if (isset($_GET['logout'])) { session_destroy(); header('Location: index.php'); exit; }

$username = htmlspecialchars($_SESSION['username'] ?? 'User');
$role = $_SESSION['role'] ?? 'labour';
$isFarmer = $role === 'farmer';

// Redirect farmers to their dashboard
if ($isFarmer) {
    header('Location: dashboard.php');
    exit;
}

$navGradient = 'linear-gradient(135deg,#f97316,#ea580c)';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
<title>Find Jobs – LabourLink</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="stylesheet" href="assets/style.css">
<link rel="stylesheet" href="assets/dashboard.css">
<style>
.jobs-header {
  background: <?= $navGradient ?>;
  padding: 24px 18px;
  color: #fff;
  margin-bottom: 0;
  border-radius: 0;
}

.jobs-header h2 {
  font-size: 1.3rem;
  font-weight: 700;
  margin: 0 0 6px 0;
}

.jobs-header p {
  font-size: 0.85rem;
  margin: 0;
  opacity: 0.9;
}

.jobs-container {
  padding: 20px 18px 20px;
  background: #f4f4fb;
  flex: 1;
}

.search-box {
  position: relative;
  margin-bottom: 16px;
}

.search-input {
  width: 100%;
  padding: 12px 16px 12px 44px;
  border: 2px solid #e5e7eb;
  border-radius: 12px;
  font-size: 0.9rem;
  background: #fff;
  transition: all 0.2s;
}

.search-input:focus {
  outline: none;
  border-color: #f97316;
  box-shadow: 0 0 0 3px rgba(249, 115, 22, 0.1);
}

.search-icon {
  position: absolute;
  left: 16px;
  top: 50%;
  transform: translateY(-50%);
  color: #9ca3af;
  font-size: 0.9rem;
}

.filter-row {
  display: flex;
  gap: 8px;
  margin-bottom: 18px;
  overflow-x: auto;
  padding-bottom: 6px;
  -webkit-overflow-scrolling: touch;
}

.filter-row::-webkit-scrollbar {
  height: 4px;
}

.filter-btn {
  padding: 8px 16px;
  border-radius: 20px;
  border: 2px solid #e5e7eb;
  background: #fff;
  font-size: 0.8rem;
  font-weight: 600;
  color: #6b7280;
  white-space: nowrap;
  cursor: pointer;
  transition: all 0.2s;
  flex-shrink: 0;
}

.filter-btn:hover,
.filter-btn.active {
  border-color: #f97316;
  color: #f97316;
  background: #fff7ed;
}

.job-card {
  background: #fff;
  border-radius: 16px;
  padding: 18px;
  margin-bottom: 14px;
  box-shadow: 0 2px 12px rgba(0,0,0,0.06);
  border-left: 4px solid #f97316;
  transition: all 0.2s;
  cursor: pointer;
}

.job-card:hover {
  box-shadow: 0 4px 16px rgba(0,0,0,0.1);
  transform: translateY(-2px);
}

.job-card-header {
  display: flex;
  justify-content: space-between;
  align-items: start;
  margin-bottom: 10px;
}

.job-title {
  font-size: 1.05rem;
  font-weight: 700;
  color: #1f2937;
  margin: 0 0 6px 0;
  line-height: 1.3;
}

.job-company {
  font-size: 0.85rem;
  color: #6b7280;
  display: flex;
  align-items: center;
  gap: 5px;
}

.job-badge {
  padding: 5px 12px;
  border-radius: 14px;
  font-size: 0.7rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.3px;
  flex-shrink: 0;
}

.badge-urgent {
  background: #fee2e2;
  color: #dc2626;
  border: 1px solid #fecaca;
}

.badge-new {
  background: #dbeafe;
  color: #2563eb;
  border: 1px solid #bfdbfe;
}

.job-meta {
  display: flex;
  flex-wrap: wrap;
  gap: 14px;
  margin-bottom: 12px;
  font-size: 0.82rem;
  color: #6b7280;
}

.job-meta-item {
  display: flex;
  align-items: center;
  gap: 5px;
}

.job-meta-item i {
  color: #f97316;
  font-size: 0.8rem;
}

.job-description {
  font-size: 0.85rem;
  color: #6b7280;
  line-height: 1.5;
  margin-bottom: 12px;
}

.job-footer {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding-top: 12px;
  border-top: 1px solid #e5e7eb;
}

.job-salary {
  font-size: 1rem;
  font-weight: 700;
  color: #f97316;
}

.btn-apply {
  padding: 8px 20px;
  background: #f97316;
  color: #fff;
  border: none;
  border-radius: 10px;
  font-size: 0.85rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s;
}

.btn-apply:hover {
  background: #ea580c;
  transform: translateY(-1px);
}

.btn-apply:active {
  transform: scale(0.96);
}

.btn-applied {
  background: #e5e7eb;
  color: #6b7280;
  cursor: default;
}

.btn-applied:hover {
  background: #e5e7eb;
  transform: none;
}

.empty-state {
  text-align: center;
  padding: 60px 20px;
  color: #9ca3af;
}

.empty-state i {
  font-size: 4rem;
  margin-bottom: 18px;
  color: #d1d5db;
}

.empty-state h3 {
  font-size: 1.1rem;
  font-weight: 700;
  color: #4b5563;
  margin-bottom: 10px;
}

.empty-state p {
  font-size: 0.9rem;
  color: #9ca3af;
  line-height: 1.5;
}

.results-count {
  font-size: 0.85rem;
  color: #6b7280;
  margin-bottom: 12px;
}

.results-count strong {
  color: #f97316;
  font-weight: 700;
}

/* Mobile fixes */
@media (max-width: 479px) {
  .jobs-container {
    padding-bottom: 120px !important;
  }
  
  .job-card:last-child {
    margin-bottom: 20px;
  }
}
</style>
</head>
<body class="labour-theme">
<div class="phone-shell">

  <!-- TOP NAVBAR -->
  <nav class="dash-nav" style="background:<?= $navGradient ?>">
    <button class="hamburger-btn" onclick="openDrawer()" title="Menu">
      <i class="fa-solid fa-bars"></i>
    </button>
    <div class="dash-nav-brand">
      <i class="fa-solid fa-magnifying-glass"></i>
      <span>Find Jobs</span>
    </div>
    <button class="nav-icon-btn" onclick="window.location.href='nearby-jobs.php'" title="Nearby Jobs">
      <i class="fa-solid fa-map-location-dot"></i>
    </button>
  </nav>

  <!-- SIDE DRAWER -->
  <div class="drawer-overlay" id="drawerOverlay" onclick="closeDrawer()"></div>
  <div class="side-drawer" id="sideDrawer">
    <div class="drawer-header" style="background:<?= $navGradient ?>">
      <div class="drawer-avatar"><?= strtoupper(substr($username, 0, 2)) ?></div>
      <div>
        <div class="drawer-name"><?= $username ?></div>
        <div class="drawer-role">Labour</div>
      </div>
      <button class="drawer-close" onclick="closeDrawer()"><i class="fa-solid fa-xmark"></i></button>
    </div>
    <nav class="drawer-nav">
      <a href="dashboard.php" class="drawer-link">
        <i class="fa-solid fa-house"></i> Home
      </a>
      <a href="profile.php" class="drawer-link">
        <i class="fa-solid fa-user"></i> My Profile
      </a>
      <a href="find-jobs.php" class="drawer-link active">
        <i class="fa-solid fa-magnifying-glass"></i> Find Jobs
      </a>
      <a href="applications.php" class="drawer-link">
        <i class="fa-solid fa-file-alt"></i> My Applications
      </a>
      <a href="nearby-jobs.php" class="drawer-link">
        <i class="fa-solid fa-map-location-dot"></i> Nearby Jobs
      </a>
      <a href="profile.php?tab=settings" class="drawer-link">
        <i class="fa-solid fa-gear"></i> Settings
      </a>
      <a href="alerts.php" class="drawer-link">
        <i class="fa-solid fa-bell"></i> Notifications
      </a>
      <a href="#" class="drawer-link" onclick="closeDrawer(); return false;">
        <i class="fa-solid fa-circle-question"></i> Help & Support
      </a>
      <div class="drawer-divider"></div>
      <a href="dashboard.php?logout=1" class="drawer-link drawer-logout">
        <i class="fa-solid fa-right-from-bracket"></i> Logout
      </a>
    </nav>
  </div>

  <!-- SCROLLABLE CONTENT -->
  <div class="scrollable-content">
    
    <div class="jobs-header">
      <h2>Find Jobs</h2>
      <p>Browse available farm work opportunities</p>
    </div>

    <div class="jobs-container">

      <!-- Search Box -->
      <div class="search-box">
        <i class="fa-solid fa-magnifying-glass search-icon"></i>
        <input type="text" class="search-input" placeholder="Search jobs by title, location..." id="searchInput" onkeyup="searchJobs()">
      </div>

      <!-- Filter Row -->
      <div class="filter-row">
        <button class="filter-btn active" onclick="filterJobs('all')">All Jobs</button>
        <button class="filter-btn" onclick="filterJobs('harvesting')">Harvesting</button>
        <button class="filter-btn" onclick="filterJobs('planting')">Planting</button>
        <button class="filter-btn" onclick="filterJobs('irrigation')">Irrigation</button>
        <button class="filter-btn" onclick="filterJobs('maintenance')">Maintenance</button>
      </div>

      <!-- Results Count -->
      <div class="results-count">
        Showing <strong id="resultsCount">6</strong> jobs
      </div>

      <!-- Jobs List -->
      <div id="jobsList">
        
        <!-- Job Card 1 -->
        <div class="job-card" data-category="harvesting" data-applied="false">
          <div class="job-card-header">
            <div>
              <h3 class="job-title">Wheat Harvesting</h3>
              <p class="job-company">
                <i class="fa-solid fa-user"></i>
                Rajesh Farm
              </p>
            </div>
            <span class="job-badge badge-urgent">Urgent</span>
          </div>
          
          <div class="job-meta">
            <span class="job-meta-item">
              <i class="fa-solid fa-location-dot"></i>
              Ahmedabad, Gujarat
            </span>
            <span class="job-meta-item">
              <i class="fa-solid fa-users"></i>
              5 workers needed
            </span>
            <span class="job-meta-item">
              <i class="fa-solid fa-clock"></i>
              7 days
            </span>
          </div>

          <p class="job-description">
            Need experienced workers for wheat harvesting. Must have prior experience with harvesting equipment.
          </p>

          <div class="job-footer">
            <div class="job-salary">
              <i class="fa-solid fa-indian-rupee-sign"></i> 500/day
            </div>
            <button class="btn-apply" onclick="applyJob(this)">
              <i class="fa-solid fa-paper-plane"></i> Apply Now
            </button>
          </div>
        </div>

        <!-- Job Card 2 -->
        <div class="job-card" data-category="planting" data-applied="false">
          <div class="job-card-header">
            <div>
              <h3 class="job-title">Rice Planting</h3>
              <p class="job-company">
                <i class="fa-solid fa-user"></i>
                Patel Agriculture
              </p>
            </div>
            <span class="job-badge badge-new">New</span>
          </div>
          
          <div class="job-meta">
            <span class="job-meta-item">
              <i class="fa-solid fa-location-dot"></i>
              Surat, Gujarat
            </span>
            <span class="job-meta-item">
              <i class="fa-solid fa-users"></i>
              3 workers needed
            </span>
            <span class="job-meta-item">
              <i class="fa-solid fa-clock"></i>
              5 days
            </span>
          </div>

          <p class="job-description">
            Looking for skilled workers for rice planting season. Good working conditions and meals provided.
          </p>

          <div class="job-footer">
            <div class="job-salary">
              <i class="fa-solid fa-indian-rupee-sign"></i> 450/day
            </div>
            <button class="btn-apply" onclick="applyJob(this)">
              <i class="fa-solid fa-paper-plane"></i> Apply Now
            </button>
          </div>
        </div>

        <!-- Job Card 3 -->
        <div class="job-card" data-category="irrigation" data-applied="false">
          <div class="job-card-header">
            <div>
              <h3 class="job-title">Irrigation System Setup</h3>
              <p class="job-company">
                <i class="fa-solid fa-user"></i>
                Kumar Farms
              </p>
            </div>
          </div>
          
          <div class="job-meta">
            <span class="job-meta-item">
              <i class="fa-solid fa-location-dot"></i>
              Vadodara, Gujarat
            </span>
            <span class="job-meta-item">
              <i class="fa-solid fa-users"></i>
              2 workers needed
            </span>
            <span class="job-meta-item">
              <i class="fa-solid fa-clock"></i>
              3 days
            </span>
          </div>

          <p class="job-description">
            Need workers to help install drip irrigation system. Technical knowledge preferred but not required.
          </p>

          <div class="job-footer">
            <div class="job-salary">
              <i class="fa-solid fa-indian-rupee-sign"></i> 600/day
            </div>
            <button class="btn-apply" onclick="applyJob(this)">
              <i class="fa-solid fa-paper-plane"></i> Apply Now
            </button>
          </div>
        </div>

        <!-- Job Card 4 -->
        <div class="job-card" data-category="maintenance" data-applied="false">
          <div class="job-card-header">
            <div>
              <h3 class="job-title">Farm Equipment Maintenance</h3>
              <p class="job-company">
                <i class="fa-solid fa-user"></i>
                Shah Agriculture
              </p>
            </div>
          </div>
          
          <div class="job-meta">
            <span class="job-meta-item">
              <i class="fa-solid fa-location-dot"></i>
              Rajkot, Gujarat
            </span>
            <span class="job-meta-item">
              <i class="fa-solid fa-users"></i>
              1 worker needed
            </span>
            <span class="job-meta-item">
              <i class="fa-solid fa-clock"></i>
              2 days
            </span>
          </div>

          <p class="job-description">
            Looking for someone with mechanical skills to help maintain tractors and other farm equipment.
          </p>

          <div class="job-footer">
            <div class="job-salary">
              <i class="fa-solid fa-indian-rupee-sign"></i> 700/day
            </div>
            <button class="btn-apply" onclick="applyJob(this)">
              <i class="fa-solid fa-paper-plane"></i> Apply Now
            </button>
          </div>
        </div>

        <!-- Job Card 5 -->
        <div class="job-card" data-category="harvesting" data-applied="false">
          <div class="job-card-header">
            <div>
              <h3 class="job-title">Cotton Harvesting</h3>
              <p class="job-company">
                <i class="fa-solid fa-user"></i>
                Desai Cotton Farm
              </p>
            </div>
            <span class="job-badge badge-new">New</span>
          </div>
          
          <div class="job-meta">
            <span class="job-meta-item">
              <i class="fa-solid fa-location-dot"></i>
              Bhavnagar, Gujarat
            </span>
            <span class="job-meta-item">
              <i class="fa-solid fa-users"></i>
              8 workers needed
            </span>
            <span class="job-meta-item">
              <i class="fa-solid fa-clock"></i>
              10 days
            </span>
          </div>

          <p class="job-description">
            Large cotton farm needs workers for harvesting season. Accommodation available for outstation workers.
          </p>

          <div class="job-footer">
            <div class="job-salary">
              <i class="fa-solid fa-indian-rupee-sign"></i> 550/day
            </div>
            <button class="btn-apply" onclick="applyJob(this)">
              <i class="fa-solid fa-paper-plane"></i> Apply Now
            </button>
          </div>
        </div>

        <!-- Job Card 6 -->
        <div class="job-card" data-category="planting" data-applied="false">
          <div class="job-card-header">
            <div>
              <h3 class="job-title">Vegetable Planting</h3>
              <p class="job-company">
                <i class="fa-solid fa-user"></i>
                Green Valley Farms
              </p>
            </div>
          </div>
          
          <div class="job-meta">
            <span class="job-meta-item">
              <i class="fa-solid fa-location-dot"></i>
              Anand, Gujarat
            </span>
            <span class="job-meta-item">
              <i class="fa-solid fa-users"></i>
              4 workers needed
            </span>
            <span class="job-meta-item">
              <i class="fa-solid fa-clock"></i>
              4 days
            </span>
          </div>

          <p class="job-description">
            Need workers for planting seasonal vegetables. Experience with organic farming is a plus.
          </p>

          <div class="job-footer">
            <div class="job-salary">
              <i class="fa-solid fa-indian-rupee-sign"></i> 480/day
            </div>
            <button class="btn-apply" onclick="applyJob(this)">
              <i class="fa-solid fa-paper-plane"></i> Apply Now
            </button>
          </div>
        </div>

      </div>

      <!-- Empty State -->
      <div class="empty-state" style="display:none;" id="emptyState">
        <i class="fa-solid fa-briefcase"></i>
        <h3>No jobs found</h3>
        <p>Try adjusting your search or filters</p>
      </div>

    </div>
  </div>

  <!-- BOTTOM NAV -->
  <nav class="bottom-nav">
    <a href="dashboard.php" class="bottom-nav-item">
      <i class="fa-solid fa-house"></i>
      <span>Home</span>
    </a>
    <a href="find-jobs.php" class="bottom-nav-item active">
      <i class="fa-solid fa-magnifying-glass"></i>
      <span>Find Jobs</span>
    </a>
    <a href="applications.php" class="bottom-nav-item">
      <i class="fa-solid fa-file-alt"></i>
      <span>Applied</span>
    </a>
    <a href="alerts.php" class="bottom-nav-item">
      <i class="fa-solid fa-bell"></i>
      <span>Alerts</span>
    </a>
    <a href="profile.php" class="bottom-nav-item">
      <i class="fa-solid fa-user"></i>
      <span>Profile</span>
    </a>
  </nav>

</div>

<script>
function filterJobs(category) {
  // Update active filter button
  document.querySelectorAll('.filter-btn').forEach(btn => {
    btn.classList.remove('active');
  });
  event.target.classList.add('active');

  // Filter job cards
  const cards = document.querySelectorAll('.job-card');
  const emptyState = document.getElementById('emptyState');
  let visibleCount = 0;

  cards.forEach(card => {
    if (category === 'all' || card.dataset.category === category) {
      card.style.display = 'block';
      visibleCount++;
    } else {
      card.style.display = 'none';
    }
  });

  // Update results count
  document.getElementById('resultsCount').textContent = visibleCount;

  // Show empty state if no results
  emptyState.style.display = visibleCount === 0 ? 'block' : 'none';
  document.getElementById('jobsList').style.display = visibleCount === 0 ? 'none' : 'block';
}

function searchJobs() {
  const searchTerm = document.getElementById('searchInput').value.toLowerCase();
  const cards = document.querySelectorAll('.job-card');
  const emptyState = document.getElementById('emptyState');
  let visibleCount = 0;

  cards.forEach(card => {
    const title = card.querySelector('.job-title').textContent.toLowerCase();
    const location = card.querySelector('.job-meta-item').textContent.toLowerCase();
    const company = card.querySelector('.job-company').textContent.toLowerCase();
    
    if (title.includes(searchTerm) || location.includes(searchTerm) || company.includes(searchTerm)) {
      card.style.display = 'block';
      visibleCount++;
    } else {
      card.style.display = 'none';
    }
  });

  // Update results count
  document.getElementById('resultsCount').textContent = visibleCount;

  // Show empty state if no results
  emptyState.style.display = visibleCount === 0 ? 'block' : 'none';
  document.getElementById('jobsList').style.display = visibleCount === 0 ? 'none' : 'block';
}

function applyJob(button) {
  const card = button.closest('.job-card');
  card.dataset.applied = 'true';
  
  button.innerHTML = '<i class="fa-solid fa-check"></i> Applied';
  button.classList.add('btn-applied');
  button.onclick = null;
  
  // Show success message
  alert('Application submitted successfully! The farmer will review your profile.');
}

function openDrawer() {
  document.getElementById('sideDrawer').classList.add('open');
  document.getElementById('drawerOverlay').classList.add('active');
}

function closeDrawer() {
  document.getElementById('sideDrawer').classList.remove('open');
  document.getElementById('drawerOverlay').classList.remove('active');
}
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
