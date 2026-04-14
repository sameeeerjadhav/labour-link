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
<title>My Applications – LabourLink</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="stylesheet" href="assets/style.css">
<link rel="stylesheet" href="assets/dashboard.css">
<style>
.applications-header {
  background: <?= $navGradient ?>;
  padding: 24px 18px;
  color: #fff;
  margin-bottom: 0;
  border-radius: 0;
}

.applications-header h2 {
  font-size: 1.3rem;
  font-weight: 700;
  margin: 0 0 6px 0;
}

.applications-header p {
  font-size: 0.85rem;
  margin: 0;
  opacity: 0.9;
}

.applications-container {
  padding: 20px 18px 20px;
  background: #f4f4fb;
  flex: 1;
}

.filter-tabs {
  display: flex;
  gap: 10px;
  margin-bottom: 18px;
  overflow-x: auto;
  padding-bottom: 6px;
  -webkit-overflow-scrolling: touch;
}

.filter-tabs::-webkit-scrollbar {
  height: 4px;
}

.filter-tab {
  padding: 10px 18px;
  border-radius: 20px;
  border: 2px solid #e5e7eb;
  background: #fff;
  font-size: 0.85rem;
  font-weight: 600;
  color: #6b7280;
  white-space: nowrap;
  cursor: pointer;
  transition: all 0.2s;
  flex-shrink: 0;
}

.filter-tab:hover {
  border-color: #f97316;
  color: #f97316;
}

.filter-tab.active {
  background: #f97316;
  color: #fff;
  border-color: #f97316;
  box-shadow: 0 2px 8px rgba(249, 115, 22, 0.3);
}

.application-card {
  background: #fff;
  border-radius: 16px;
  padding: 18px;
  margin-bottom: 14px;
  box-shadow: 0 2px 12px rgba(0,0,0,0.06);
  border-left: 4px solid #f97316;
  transition: all 0.2s;
}

.application-card:hover {
  box-shadow: 0 4px 16px rgba(0,0,0,0.1);
  transform: translateY(-2px);
}

.application-header {
  display: flex;
  justify-content: space-between;
  align-items: start;
  margin-bottom: 10px;
}

.application-title {
  font-size: 1.05rem;
  font-weight: 700;
  color: #1f2937;
  margin: 0 0 6px 0;
  line-height: 1.3;
}

.application-company {
  font-size: 0.85rem;
  color: #6b7280;
  display: flex;
  align-items: center;
  gap: 5px;
}

.status-badge {
  padding: 5px 12px;
  border-radius: 14px;
  font-size: 0.7rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.3px;
  flex-shrink: 0;
}

.status-pending {
  background: #fef3c7;
  color: #d97706;
  border: 1px solid #fde68a;
}

.status-shortlisted {
  background: #dbeafe;
  color: #2563eb;
  border: 1px solid #bfdbfe;
}

.status-accepted {
  background: #d1fae5;
  color: #059669;
  border: 1px solid #a7f3d0;
}

.status-rejected {
  background: #fee2e2;
  color: #dc2626;
  border: 1px solid #fecaca;
}

.application-meta {
  display: flex;
  flex-wrap: wrap;
  gap: 14px;
  margin-bottom: 12px;
  font-size: 0.82rem;
  color: #6b7280;
}

.application-meta-item {
  display: flex;
  align-items: center;
  gap: 5px;
}

.application-meta-item i {
  color: #f97316;
  font-size: 0.8rem;
}

.application-footer {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding-top: 12px;
  border-top: 1px solid #e5e7eb;
  font-size: 0.8rem;
  color: #9ca3af;
}

.application-date {
  display: flex;
  align-items: center;
  gap: 5px;
}

.btn-view-details {
  padding: 6px 16px;
  background: #fff7ed;
  color: #f97316;
  border: 1px solid #fed7aa;
  border-radius: 8px;
  font-size: 0.8rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s;
}

.btn-view-details:hover {
  background: #ffedd5;
}

.empty-state {
  text-align: center;
  padding: 60px 20px;
  color: #9ca3af;
  background: #f9fafb;
  border-radius: 16px;
  margin: 20px 0;
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
  margin-bottom: 24px;
}

.btn-browse-jobs {
  display: inline-block;
  padding: 12px 24px;
  background: #f97316;
  color: #fff;
  border-radius: 12px;
  font-weight: 600;
  font-size: 0.9rem;
  text-decoration: none;
  transition: all 0.2s;
}

.btn-browse-jobs:hover {
  background: #ea580c;
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(249, 115, 22, 0.3);
  color: #fff;
}

/* Mobile fixes */
@media (max-width: 479px) {
  .applications-container {
    padding-bottom: 120px !important;
  }
  
  .application-card:last-child {
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
      <i class="fa-solid fa-file-alt"></i>
      <span>Applications</span>
    </div>
    <button class="nav-icon-btn" onclick="window.location.href='find-jobs.php'" title="Find More Jobs">
      <i class="fa-solid fa-plus"></i>
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
      <a href="applications.php" class="drawer-link active">
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
    
    <div class="applications-header">
      <h2>My Applications</h2>
      <p>Track your job applications</p>
    </div>

    <div class="applications-container">

      <!-- Filter Tabs -->
      <div class="filter-tabs">
        <button class="filter-tab active" onclick="filterApplications('all')">All</button>
        <button class="filter-tab" onclick="filterApplications('pending')">Pending</button>
        <button class="filter-tab" onclick="filterApplications('shortlisted')">Shortlisted</button>
        <button class="filter-tab" onclick="filterApplications('accepted')">Accepted</button>
        <button class="filter-tab" onclick="filterApplications('rejected')">Rejected</button>
      </div>

      <!-- Applications List -->
      <div id="applicationsList">
        
        <!-- Application Card 1 - Shortlisted -->
        <div class="application-card" data-status="shortlisted">
          <div class="application-header">
            <div>
              <h3 class="application-title">Wheat Harvesting</h3>
              <p class="application-company">
                <i class="fa-solid fa-user"></i>
                Rajesh Farm
              </p>
            </div>
            <span class="status-badge status-shortlisted">Shortlisted</span>
          </div>
          
          <div class="application-meta">
            <span class="application-meta-item">
              <i class="fa-solid fa-location-dot"></i>
              Ahmedabad, Gujarat
            </span>
            <span class="application-meta-item">
              <i class="fa-solid fa-indian-rupee-sign"></i>
              ₹500/day
            </span>
            <span class="application-meta-item">
              <i class="fa-solid fa-clock"></i>
              7 days
            </span>
          </div>

          <div class="application-footer">
            <span class="application-date">
              <i class="fa-solid fa-calendar"></i>
              Applied 2 days ago
            </span>
            <button class="btn-view-details" onclick="viewDetails('Wheat Harvesting')">
              View Details
            </button>
          </div>
        </div>

        <!-- Application Card 2 - Pending -->
        <div class="application-card" data-status="pending">
          <div class="application-header">
            <div>
              <h3 class="application-title">Rice Planting</h3>
              <p class="application-company">
                <i class="fa-solid fa-user"></i>
                Patel Agriculture
              </p>
            </div>
            <span class="status-badge status-pending">Pending</span>
          </div>
          
          <div class="application-meta">
            <span class="application-meta-item">
              <i class="fa-solid fa-location-dot"></i>
              Surat, Gujarat
            </span>
            <span class="application-meta-item">
              <i class="fa-solid fa-indian-rupee-sign"></i>
              ₹450/day
            </span>
            <span class="application-meta-item">
              <i class="fa-solid fa-clock"></i>
              5 days
            </span>
          </div>

          <div class="application-footer">
            <span class="application-date">
              <i class="fa-solid fa-calendar"></i>
              Applied 5 hours ago
            </span>
            <button class="btn-view-details" onclick="viewDetails('Rice Planting')">
              View Details
            </button>
          </div>
        </div>

        <!-- Application Card 3 - Accepted -->
        <div class="application-card" data-status="accepted">
          <div class="application-header">
            <div>
              <h3 class="application-title">Irrigation System Setup</h3>
              <p class="application-company">
                <i class="fa-solid fa-user"></i>
                Kumar Farms
              </p>
            </div>
            <span class="status-badge status-accepted">Accepted</span>
          </div>
          
          <div class="application-meta">
            <span class="application-meta-item">
              <i class="fa-solid fa-location-dot"></i>
              Vadodara, Gujarat
            </span>
            <span class="application-meta-item">
              <i class="fa-solid fa-indian-rupee-sign"></i>
              ₹600/day
            </span>
            <span class="application-meta-item">
              <i class="fa-solid fa-clock"></i>
              3 days
            </span>
          </div>

          <div class="application-footer">
            <span class="application-date">
              <i class="fa-solid fa-calendar"></i>
              Applied 1 week ago
            </span>
            <button class="btn-view-details" onclick="viewDetails('Irrigation System Setup')">
              View Details
            </button>
          </div>
        </div>

        <!-- Application Card 4 - Rejected -->
        <div class="application-card" data-status="rejected">
          <div class="application-header">
            <div>
              <h3 class="application-title">Cotton Harvesting</h3>
              <p class="application-company">
                <i class="fa-solid fa-user"></i>
                Desai Cotton Farm
              </p>
            </div>
            <span class="status-badge status-rejected">Not Selected</span>
          </div>
          
          <div class="application-meta">
            <span class="application-meta-item">
              <i class="fa-solid fa-location-dot"></i>
              Bhavnagar, Gujarat
            </span>
            <span class="application-meta-item">
              <i class="fa-solid fa-indian-rupee-sign"></i>
              ₹550/day
            </span>
            <span class="application-meta-item">
              <i class="fa-solid fa-clock"></i>
              10 days
            </span>
          </div>

          <div class="application-footer">
            <span class="application-date">
              <i class="fa-solid fa-calendar"></i>
              Applied 2 weeks ago
            </span>
            <button class="btn-view-details" onclick="viewDetails('Cotton Harvesting')">
              View Details
            </button>
          </div>
        </div>

        <!-- Application Card 5 - Pending -->
        <div class="application-card" data-status="pending">
          <div class="application-header">
            <div>
              <h3 class="application-title">Vegetable Planting</h3>
              <p class="application-company">
                <i class="fa-solid fa-user"></i>
                Green Valley Farms
              </p>
            </div>
            <span class="status-badge status-pending">Pending</span>
          </div>
          
          <div class="application-meta">
            <span class="application-meta-item">
              <i class="fa-solid fa-location-dot"></i>
              Anand, Gujarat
            </span>
            <span class="application-meta-item">
              <i class="fa-solid fa-indian-rupee-sign"></i>
              ₹480/day
            </span>
            <span class="application-meta-item">
              <i class="fa-solid fa-clock"></i>
              4 days
            </span>
          </div>

          <div class="application-footer">
            <span class="application-date">
              <i class="fa-solid fa-calendar"></i>
              Applied 3 days ago
            </span>
            <button class="btn-view-details" onclick="viewDetails('Vegetable Planting')">
              View Details
            </button>
          </div>
        </div>

      </div>

      <!-- Empty State -->
      <div class="empty-state" style="display:none;" id="emptyState">
        <i class="fa-solid fa-file-circle-xmark"></i>
        <h3>No applications yet</h3>
        <p>Start applying to jobs to see them here</p>
        <a href="find-jobs.php" class="btn-browse-jobs">
          <i class="fa-solid fa-magnifying-glass"></i> Browse Jobs
        </a>
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
    <a href="applications.php" class="bottom-nav-item active">
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
function filterApplications(status) {
  // Update active tab
  document.querySelectorAll('.filter-tab').forEach(tab => {
    tab.classList.remove('active');
  });
  event.target.classList.add('active');

  // Filter application cards
  const cards = document.querySelectorAll('.application-card');
  const emptyState = document.getElementById('emptyState');
  let visibleCount = 0;

  cards.forEach(card => {
    if (status === 'all' || card.dataset.status === status) {
      card.style.display = 'block';
      visibleCount++;
    } else {
      card.style.display = 'none';
    }
  });

  // Show empty state if no cards visible
  emptyState.style.display = visibleCount === 0 ? 'block' : 'none';
  document.getElementById('applicationsList').style.display = visibleCount === 0 ? 'none' : 'block';
}

function viewDetails(jobTitle) {
  alert(`Viewing details for: ${jobTitle}\n\nThis would show:\n- Full job description\n- Farmer contact info\n- Application status history\n- Messages from farmer`);
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
