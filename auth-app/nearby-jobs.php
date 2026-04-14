<?php
require_once 'config.php';
if (!isset($_SESSION['user_id'])) { header('Location: index.php'); exit; }
if (isset($_GET['logout'])) { session_destroy(); header('Location: index.php'); exit; }

$username = htmlspecialchars($_SESSION['username'] ?? 'User');
$role = $_SESSION['role'] ?? 'labour';
$isFarmer = $role === 'farmer';

// Redirect farmers
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
<title>Nearby Jobs – LabourLink</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="stylesheet" href="assets/style.css">
<link rel="stylesheet" href="assets/dashboard.css">
<style>
.nearby-header {
  background: <?= $navGradient ?>;
  padding: 24px 18px;
  color: #fff;
  margin-bottom: 0;
  border-radius: 0;
}

.nearby-header h2 {
  font-size: 1.3rem;
  font-weight: 700;
  margin: 0 0 6px 0;
}

.nearby-header p {
  font-size: 0.85rem;
  margin: 0;
  opacity: 0.9;
}

.nearby-container {
  padding: 20px 18px 20px;
  background: #f4f4fb;
  flex: 1;
}

.location-banner {
  background: linear-gradient(135deg, #3b82f6, #2563eb);
  border-radius: 16px;
  padding: 16px;
  margin-bottom: 18px;
  color: #fff;
  display: flex;
  align-items: center;
  gap: 12px;
}

.location-icon {
  width: 48px;
  height: 48px;
  background: rgba(255,255,255,0.2);
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.3rem;
  flex-shrink: 0;
}

.location-info h3 {
  font-size: 0.95rem;
  font-weight: 700;
  margin: 0 0 4px 0;
}

.location-info p {
  font-size: 0.8rem;
  margin: 0;
  opacity: 0.9;
}

.distance-filter {
  display: flex;
  align-items: center;
  gap: 12px;
  margin-bottom: 18px;
  padding: 14px;
  background: #fff;
  border-radius: 12px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.05);
}

.distance-filter label {
  font-size: 0.85rem;
  font-weight: 600;
  color: #4b5563;
  flex-shrink: 0;
}

.distance-slider {
  flex: 1;
  height: 6px;
  border-radius: 3px;
  background: #e5e7eb;
  outline: none;
  -webkit-appearance: none;
}

.distance-slider::-webkit-slider-thumb {
  -webkit-appearance: none;
  appearance: none;
  width: 20px;
  height: 20px;
  border-radius: 50%;
  background: #f97316;
  cursor: pointer;
  box-shadow: 0 2px 4px rgba(0,0,0,0.2);
}

.distance-slider::-moz-range-thumb {
  width: 20px;
  height: 20px;
  border-radius: 50%;
  background: #f97316;
  cursor: pointer;
  border: none;
  box-shadow: 0 2px 4px rgba(0,0,0,0.2);
}

.distance-value {
  font-size: 0.9rem;
  font-weight: 700;
  color: #f97316;
  min-width: 50px;
  text-align: right;
}

.section-title {
  font-size: 0.9rem;
  font-weight: 700;
  color: #1f2937;
  margin-bottom: 12px;
  display: flex;
  align-items: center;
  gap: 8px;
}

.section-title i {
  color: #f97316;
}

.job-card-nearby {
  background: #fff;
  border-radius: 16px;
  padding: 16px;
  margin-bottom: 12px;
  box-shadow: 0 2px 12px rgba(0,0,0,0.06);
  border-left: 4px solid #f97316;
  transition: all 0.2s;
  cursor: pointer;
}

.job-card-nearby:hover {
  box-shadow: 0 4px 16px rgba(0,0,0,0.1);
  transform: translateY(-2px);
}

.job-header-nearby {
  display: flex;
  justify-content: space-between;
  align-items: start;
  margin-bottom: 10px;
}

.job-title-nearby {
  font-size: 1rem;
  font-weight: 700;
  color: #1f2937;
  margin: 0 0 4px 0;
}

.job-distance {
  display: flex;
  align-items: center;
  gap: 4px;
  font-size: 0.75rem;
  font-weight: 700;
  color: #f97316;
  background: #fff7ed;
  padding: 4px 10px;
  border-radius: 12px;
  flex-shrink: 0;
}

.job-location-nearby {
  font-size: 0.8rem;
  color: #6b7280;
  display: flex;
  align-items: center;
  gap: 5px;
  margin-bottom: 10px;
}

.job-details-nearby {
  display: flex;
  gap: 12px;
  margin-bottom: 10px;
  font-size: 0.8rem;
  color: #6b7280;
}

.job-detail-item {
  display: flex;
  align-items: center;
  gap: 4px;
}

.job-detail-item i {
  color: #f97316;
  font-size: 0.75rem;
}

.job-footer-nearby {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding-top: 10px;
  border-top: 1px solid #e5e7eb;
}

.job-salary-nearby {
  font-size: 0.95rem;
  font-weight: 700;
  color: #1f2937;
}

.btn-quick-apply {
  padding: 6px 16px;
  background: #f97316;
  color: #fff;
  border: none;
  border-radius: 8px;
  font-size: 0.8rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s;
}

.btn-quick-apply:hover {
  background: #ea580c;
}

.map-placeholder {
  background: linear-gradient(135deg, #e0f2fe, #bae6fd);
  border-radius: 16px;
  padding: 40px 20px;
  text-align: center;
  margin-bottom: 18px;
  position: relative;
  overflow: hidden;
}

.map-placeholder::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><circle cx="50" cy="50" r="2" fill="%23f97316" opacity="0.3"/></svg>');
  background-size: 20px 20px;
  opacity: 0.3;
}

.map-icon {
  font-size: 3rem;
  color: #0284c7;
  margin-bottom: 12px;
}

.map-text {
  font-size: 0.9rem;
  color: #0369a1;
  font-weight: 600;
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

/* Mobile fixes */
@media (max-width: 479px) {
  .nearby-container {
    padding-bottom: 120px !important;
  }
  
  .job-card-nearby:last-child {
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
      <i class="fa-solid fa-map-location-dot"></i>
      <span>Nearby Jobs</span>
    </div>
    <button class="nav-icon-btn" onclick="refreshLocation()" title="Refresh Location">
      <i class="fa-solid fa-arrows-rotate"></i>
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
      <a href="find-jobs.php" class="drawer-link">
        <i class="fa-solid fa-magnifying-glass"></i> Find Jobs
      </a>
      <a href="applications.php" class="drawer-link">
        <i class="fa-solid fa-file-alt"></i> My Applications
      </a>
      <a href="nearby-jobs.php" class="drawer-link active">
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
    
    <div class="nearby-header">
      <h2>Nearby Jobs</h2>
      <p>Find work opportunities near you</p>
    </div>

    <div class="nearby-container">

      <!-- Location Banner -->
      <div class="location-banner">
        <div class="location-icon">
          <i class="fa-solid fa-location-crosshairs"></i>
        </div>
        <div class="location-info">
          <h3>Your Location</h3>
          <p id="currentLocation">Ahmedabad, Gujarat</p>
        </div>
      </div>

      <!-- Map Placeholder -->
      <div class="map-placeholder">
        <div class="map-icon">
          <i class="fa-solid fa-map-marked-alt"></i>
        </div>
        <div class="map-text">Interactive map view coming soon</div>
      </div>

      <!-- Distance Filter -->
      <div class="distance-filter">
        <label for="distanceRange">
          <i class="fa-solid fa-ruler"></i> Distance:
        </label>
        <input type="range" id="distanceRange" class="distance-slider" min="5" max="50" value="20" step="5" oninput="updateDistance(this.value)">
        <span class="distance-value" id="distanceValue">20 km</span>
      </div>

      <!-- Nearby Jobs List -->
      <div class="section-title">
        <i class="fa-solid fa-briefcase"></i>
        <span id="jobsCount">5 jobs</span> within <span id="rangeDisplay">20 km</span>
      </div>

      <div id="nearbyJobsList">
        
        <!-- Job Card 1 - Closest -->
        <div class="job-card-nearby" data-distance="3">
          <div class="job-header-nearby">
            <div>
              <h3 class="job-title-nearby">Wheat Harvesting</h3>
              <p class="job-location-nearby">
                <i class="fa-solid fa-location-dot"></i>
                Ahmedabad, Gujarat
              </p>
            </div>
            <span class="job-distance">
              <i class="fa-solid fa-location-arrow"></i>
              3 km
            </span>
          </div>
          
          <div class="job-details-nearby">
            <span class="job-detail-item">
              <i class="fa-solid fa-users"></i>
              5 workers
            </span>
            <span class="job-detail-item">
              <i class="fa-solid fa-clock"></i>
              7 days
            </span>
            <span class="job-detail-item">
              <i class="fa-solid fa-calendar"></i>
              Posted today
            </span>
          </div>

          <div class="job-footer-nearby">
            <div class="job-salary-nearby">
              <i class="fa-solid fa-indian-rupee-sign"></i> 500/day
            </div>
            <button class="btn-quick-apply" onclick="quickApply(this, 'Wheat Harvesting')">
              Quick Apply
            </button>
          </div>
        </div>

        <!-- Job Card 2 -->
        <div class="job-card-nearby" data-distance="8">
          <div class="job-header-nearby">
            <div>
              <h3 class="job-title-nearby">Rice Planting</h3>
              <p class="job-location-nearby">
                <i class="fa-solid fa-location-dot"></i>
                Gandhinagar, Gujarat
              </p>
            </div>
            <span class="job-distance">
              <i class="fa-solid fa-location-arrow"></i>
              8 km
            </span>
          </div>
          
          <div class="job-details-nearby">
            <span class="job-detail-item">
              <i class="fa-solid fa-users"></i>
              3 workers
            </span>
            <span class="job-detail-item">
              <i class="fa-solid fa-clock"></i>
              5 days
            </span>
            <span class="job-detail-item">
              <i class="fa-solid fa-calendar"></i>
              Posted 1 day ago
            </span>
          </div>

          <div class="job-footer-nearby">
            <div class="job-salary-nearby">
              <i class="fa-solid fa-indian-rupee-sign"></i> 450/day
            </div>
            <button class="btn-quick-apply" onclick="quickApply(this, 'Rice Planting')">
              Quick Apply
            </button>
          </div>
        </div>

        <!-- Job Card 3 -->
        <div class="job-card-nearby" data-distance="12">
          <div class="job-header-nearby">
            <div>
              <h3 class="job-title-nearby">Irrigation System Setup</h3>
              <p class="job-location-nearby">
                <i class="fa-solid fa-location-dot"></i>
                Sanand, Gujarat
              </p>
            </div>
            <span class="job-distance">
              <i class="fa-solid fa-location-arrow"></i>
              12 km
            </span>
          </div>
          
          <div class="job-details-nearby">
            <span class="job-detail-item">
              <i class="fa-solid fa-users"></i>
              2 workers
            </span>
            <span class="job-detail-item">
              <i class="fa-solid fa-clock"></i>
              3 days
            </span>
            <span class="job-detail-item">
              <i class="fa-solid fa-calendar"></i>
              Posted 2 days ago
            </span>
          </div>

          <div class="job-footer-nearby">
            <div class="job-salary-nearby">
              <i class="fa-solid fa-indian-rupee-sign"></i> 600/day
            </div>
            <button class="btn-quick-apply" onclick="quickApply(this, 'Irrigation System Setup')">
              Quick Apply
            </button>
          </div>
        </div>

        <!-- Job Card 4 -->
        <div class="job-card-nearby" data-distance="18">
          <div class="job-header-nearby">
            <div>
              <h3 class="job-title-nearby">Vegetable Planting</h3>
              <p class="job-location-nearby">
                <i class="fa-solid fa-location-dot"></i>
                Bavla, Gujarat
              </p>
            </div>
            <span class="job-distance">
              <i class="fa-solid fa-location-arrow"></i>
              18 km
            </span>
          </div>
          
          <div class="job-details-nearby">
            <span class="job-detail-item">
              <i class="fa-solid fa-users"></i>
              4 workers
            </span>
            <span class="job-detail-item">
              <i class="fa-solid fa-clock"></i>
              4 days
            </span>
            <span class="job-detail-item">
              <i class="fa-solid fa-calendar"></i>
              Posted 3 days ago
            </span>
          </div>

          <div class="job-footer-nearby">
            <div class="job-salary-nearby">
              <i class="fa-solid fa-indian-rupee-sign"></i> 480/day
            </div>
            <button class="btn-quick-apply" onclick="quickApply(this, 'Vegetable Planting')">
              Quick Apply
            </button>
          </div>
        </div>

        <!-- Job Card 5 -->
        <div class="job-card-nearby" data-distance="25">
          <div class="job-header-nearby">
            <div>
              <h3 class="job-title-nearby">Farm Equipment Maintenance</h3>
              <p class="job-location-nearby">
                <i class="fa-solid fa-location-dot"></i>
                Viramgam, Gujarat
              </p>
            </div>
            <span class="job-distance">
              <i class="fa-solid fa-location-arrow"></i>
              25 km
            </span>
          </div>
          
          <div class="job-details-nearby">
            <span class="job-detail-item">
              <i class="fa-solid fa-users"></i>
              1 worker
            </span>
            <span class="job-detail-item">
              <i class="fa-solid fa-clock"></i>
              2 days
            </span>
            <span class="job-detail-item">
              <i class="fa-solid fa-calendar"></i>
              Posted 1 week ago
            </span>
          </div>

          <div class="job-footer-nearby">
            <div class="job-salary-nearby">
              <i class="fa-solid fa-indian-rupee-sign"></i> 700/day
            </div>
            <button class="btn-quick-apply" onclick="quickApply(this, 'Farm Equipment Maintenance')">
              Quick Apply
            </button>
          </div>
        </div>

      </div>

      <!-- Empty State -->
      <div class="empty-state" style="display:none;" id="emptyState">
        <i class="fa-solid fa-map-location"></i>
        <h3>No jobs nearby</h3>
        <p>Try increasing the distance range or check back later</p>
      </div>

    </div>
  </div>

  <!-- BOTTOM NAV -->
  <nav class="bottom-nav">
    <a href="dashboard.php" class="bottom-nav-item">
      <i class="fa-solid fa-house"></i>
      <span>Home</span>
    </a>
    <a href="find-jobs.php" class="bottom-nav-item">
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
function updateDistance(value) {
  document.getElementById('distanceValue').textContent = value + ' km';
  document.getElementById('rangeDisplay').textContent = value + ' km';
  filterByDistance(parseInt(value));
}

function filterByDistance(maxDistance) {
  const cards = document.querySelectorAll('.job-card-nearby');
  const emptyState = document.getElementById('emptyState');
  let visibleCount = 0;

  cards.forEach(card => {
    const distance = parseInt(card.dataset.distance);
    if (distance <= maxDistance) {
      card.style.display = 'block';
      visibleCount++;
    } else {
      card.style.display = 'none';
    }
  });

  // Update count
  document.getElementById('jobsCount').textContent = visibleCount + ' jobs';

  // Show empty state if no results
  emptyState.style.display = visibleCount === 0 ? 'block' : 'none';
  document.getElementById('nearbyJobsList').style.display = visibleCount === 0 ? 'none' : 'block';
}

function quickApply(button, jobTitle) {
  button.innerHTML = '<i class="fa-solid fa-check"></i> Applied';
  button.style.background = '#10b981';
  button.onclick = null;
  
  setTimeout(() => {
    alert(`Applied to: ${jobTitle}\n\nYour application has been sent to the farmer.`);
  }, 300);
}

function refreshLocation() {
  alert('Refreshing your location...\n\nThis would use GPS to update your current location and show the most relevant nearby jobs.');
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
