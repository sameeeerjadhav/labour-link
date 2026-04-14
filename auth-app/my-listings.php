<?php
require_once 'config.php';
if (!isset($_SESSION['user_id'])) { header('Location: index.php'); exit; }
if (isset($_GET['logout'])) { session_destroy(); header('Location: index.php'); exit; }

$username = htmlspecialchars($_SESSION['username'] ?? 'User');
$role = $_SESSION['role'] ?? 'labour';
$isFarmer = $role === 'farmer';

// Redirect labour users to dashboard
if (!$isFarmer) {
    header('Location: dashboard.php');
    exit;
}

$navGradient = 'linear-gradient(135deg,#10b981,#059669)';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
<title>My Listings – LabourLink</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="stylesheet" href="assets/style.css">
<link rel="stylesheet" href="assets/dashboard.css">
<style>
.listings-header {
  background: linear-gradient(135deg, #10b981, #059669);
  padding: 24px 18px;
  color: #fff;
  margin-bottom: 0;
  border-radius: 0;
}

.listings-header h2 {
  font-size: 1.3rem;
  font-weight: 700;
  margin: 0 0 6px 0;
}

.listings-header p {
  font-size: 0.85rem;
  margin: 0;
  opacity: 0.9;
}

.form-container {
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

.filter-tabs::-webkit-scrollbar-thumb {
  background: #d1d5db;
  border-radius: 4px;
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
  border-color: #10b981;
  color: #10b981;
}

.filter-tab.active {
  background: #10b981;
  color: #fff;
  border-color: #10b981;
  box-shadow: 0 2px 8px rgba(16, 185, 129, 0.3);
}

.job-card {
  background: #fff;
  border-radius: 16px;
  padding: 18px;
  margin-bottom: 14px;
  box-shadow: 0 2px 12px rgba(0,0,0,0.06);
  border-left: 4px solid #10b981;
  transition: all 0.2s;
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

.job-status {
  padding: 5px 12px;
  border-radius: 14px;
  font-size: 0.7rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.3px;
  flex-shrink: 0;
}

.status-active {
  background: #d1fae5;
  color: #059669;
  border: 1px solid #a7f3d0;
}

.status-closed {
  background: #fee2e2;
  color: #dc2626;
  border: 1px solid #fecaca;
}

.status-draft {
  background: #fef3c7;
  color: #d97706;
  border: 1px solid #fde68a;
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
  color: #10b981;
  font-size: 0.8rem;
}

.job-stats {
  display: flex;
  gap: 18px;
  padding: 12px 0 0;
  border-top: 1px solid #e5e7eb;
  margin-top: 12px;
}

.job-stat {
  display: flex;
  align-items: center;
  gap: 6px;
  font-size: 0.82rem;
  color: #6b7280;
}

.job-stat i {
  font-size: 0.75rem;
  color: #9ca3af;
}

.job-stat-num {
  font-weight: 700;
  color: #10b981;
  font-size: 0.9rem;
}

.job-actions {
  display: flex;
  gap: 10px;
  margin-top: 14px;
}

.btn-action {
  flex: 1;
  padding: 10px 12px;
  border-radius: 10px;
  font-size: 0.82rem;
  font-weight: 600;
  border: none;
  cursor: pointer;
  transition: all 0.2s;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 6px;
}

.btn-action:active {
  transform: scale(0.96);
}

.btn-view {
  background: #f0fdf4;
  color: #10b981;
  border: 1px solid #d1fae5;
}

.btn-view:hover {
  background: #dcfce7;
}

.btn-edit {
  background: #eff6ff;
  color: #3b82f6;
  border: 1px solid #dbeafe;
}

.btn-edit:hover {
  background: #dbeafe;
}

.btn-delete {
  background: #fef2f2;
  color: #ef4444;
  border: 1px solid #fee2e2;
}

.btn-delete:hover {
  background: #fee2e2;
}

.empty-state {
  text-align: center;
  padding: 50px 20px;
  color: #9ca3af;
  background: #f9fafb;
  border-radius: 16px;
  margin: 20px 0;
}

.empty-state i {
  font-size: 3.5rem;
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
  margin-bottom: 24px;
  line-height: 1.5;
}

.btn-primary-custom {
  background: #10b981;
  color: #fff;
  padding: 12px 24px;
  border-radius: 12px;
  font-weight: 600;
  font-size: 0.9rem;
  transition: all 0.2s;
  border: none;
}

.btn-primary-custom:hover {
  background: #059669;
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
}

.form-container {
  padding: 0 18px 20px;
}

/* Mobile fixes */
@media (max-width: 479px) {
  .form-container {
    padding-bottom: 120px !important;
  }
  
  #jobsList {
    margin-bottom: 20px;
  }
  
  .job-card:last-child {
    margin-bottom: 20px;
  }
  
  .job-actions {
    flex-wrap: wrap;
  }
  
  .btn-action {
    min-width: calc(50% - 5px);
  }
  
  .filter-tabs {
    padding: 0 0 6px 0;
    margin-left: -18px;
    margin-right: -18px;
    padding-left: 18px;
    padding-right: 18px;
  }
}
</style>
</head>
<body>
<div class="phone-shell">

  <!-- TOP NAVBAR -->
  <nav class="dash-nav" style="background:<?= $navGradient ?>">
    <button class="hamburger-btn" id="hamburgerBtn" onclick="openDrawer()" title="Menu">
      <i class="fa-solid fa-bars"></i>
    </button>
    <div class="dash-nav-brand">
      <i class="fa-solid fa-list-check"></i>
      <span>My Listings</span>
    </div>
    <a href="post-job.php" class="nav-icon-btn" title="Post New Job">
      <i class="fa-solid fa-plus"></i>
    </a>
  </nav>

  <!-- ── SIDE DRAWER ── -->
  <div class="drawer-overlay" id="drawerOverlay" onclick="closeDrawer()"></div>
  <div class="side-drawer" id="sideDrawer">
    <div class="drawer-header" style="background:<?= $navGradient ?>">
      <div class="drawer-avatar"><?= strtoupper(substr($username, 0, 2)) ?></div>
      <div>
        <div class="drawer-name"><?= $username ?></div>
        <div class="drawer-role">Farmer</div>
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
      <a href="post-job.php" class="drawer-link">
        <i class="fa-solid fa-plus-circle"></i> Post a Job
      </a>
      <a href="find-labour.php" class="drawer-link">
        <i class="fa-solid fa-users"></i> Find Labour
      </a>
      <a href="my-listings.php" class="drawer-link active">
        <i class="fa-solid fa-list-check"></i> My Listings
      </a>
      <a href="schedule.php" class="drawer-link">
        <i class="fa-solid fa-calendar-days"></i> Schedule
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
    
    <div class="listings-header">
      <h2>Your Job Listings</h2>
      <p>Manage and track your posted jobs</p>
    </div>

    <div class="form-container">

      <!-- Filter Tabs -->
      <div class="filter-tabs">
        <button class="filter-tab active" onclick="filterJobs('all')">All</button>
        <button class="filter-tab" onclick="filterJobs('active')">Active</button>
        <button class="filter-tab" onclick="filterJobs('closed')">Closed</button>
        <button class="filter-tab" onclick="filterJobs('draft')">Drafts</button>
      </div>

      <!-- Job Listings -->
      <div id="jobsList">
        
        <!-- Sample Job Card - Active -->
        <div class="job-card" data-status="active">
          <div class="job-card-header">
            <div>
              <h3 class="job-title">Wheat Harvesting</h3>
              <div class="job-meta">
                <span class="job-meta-item">
                  <i class="fa-solid fa-location-dot"></i>
                  Ahmedabad, Gujarat
                </span>
                <span class="job-meta-item">
                  <i class="fa-solid fa-calendar"></i>
                  Posted 2 days ago
                </span>
              </div>
            </div>
            <span class="job-status status-active">Active</span>
          </div>
          
          <div class="job-meta">
            <span class="job-meta-item">
              <i class="fa-solid fa-indian-rupee-sign"></i>
              ₹500/day
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

          <div class="job-stats">
            <div class="job-stat">
              <i class="fa-solid fa-eye"></i>
              <span class="job-stat-num">45</span> views
            </div>
            <div class="job-stat">
              <i class="fa-solid fa-file-alt"></i>
              <span class="job-stat-num">12</span> applications
            </div>
          </div>

          <div class="job-actions">
            <button class="btn-action btn-view">
              <i class="fa-solid fa-eye"></i> View
            </button>
            <button class="btn-action btn-edit">
              <i class="fa-solid fa-pen"></i> Edit
            </button>
            <button class="btn-action btn-delete">
              <i class="fa-solid fa-trash"></i> Delete
            </button>
          </div>
        </div>

        <!-- Sample Job Card - Closed -->
        <div class="job-card" data-status="closed">
          <div class="job-card-header">
            <div>
              <h3 class="job-title">Rice Planting</h3>
              <div class="job-meta">
                <span class="job-meta-item">
                  <i class="fa-solid fa-location-dot"></i>
                  Surat, Gujarat
                </span>
                <span class="job-meta-item">
                  <i class="fa-solid fa-calendar"></i>
                  Posted 1 week ago
                </span>
              </div>
            </div>
            <span class="job-status status-closed">Closed</span>
          </div>
          
          <div class="job-meta">
            <span class="job-meta-item">
              <i class="fa-solid fa-indian-rupee-sign"></i>
              ₹450/day
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

          <div class="job-stats">
            <div class="job-stat">
              <i class="fa-solid fa-eye"></i>
              <span class="job-stat-num">78</span> views
            </div>
            <div class="job-stat">
              <i class="fa-solid fa-file-alt"></i>
              <span class="job-stat-num">23</span> applications
            </div>
            <div class="job-stat">
              <i class="fa-solid fa-check-circle"></i>
              <span class="job-stat-num">3</span> hired
            </div>
          </div>

          <div class="job-actions">
            <button class="btn-action btn-view">
              <i class="fa-solid fa-eye"></i> View
            </button>
          </div>
        </div>

        <!-- Empty State (hidden by default) -->
        <div class="empty-state" style="display:none;" id="emptyState">
          <i class="fa-solid fa-inbox"></i>
          <h3>No listings found</h3>
          <p>You haven't posted any jobs yet</p>
          <a href="post-job.php" class="btn-primary-custom" style="display:inline-block; text-decoration:none;">
            <i class="fa-solid fa-plus me-2"></i>Post Your First Job
          </a>
        </div>

      </div>

    </div>
  </div>

  <!-- BOTTOM NAV -->
  <nav class="bottom-nav">
    <a href="dashboard.php" class="bottom-nav-item">
      <i class="fa-solid fa-house"></i>
      <span>Home</span>
    </a>
    <a href="post-job.php" class="bottom-nav-item active">
      <i class="fa-solid fa-briefcase"></i>
      <span>My Jobs</span>
    </a>
    <a href="find-labour.php" class="bottom-nav-item">
      <i class="fa-solid fa-users"></i>
      <span>Labour</span>
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
function filterJobs(status) {
  // Update active tab
  document.querySelectorAll('.filter-tab').forEach(tab => {
    tab.classList.remove('active');
  });
  event.target.classList.add('active');

  // Filter job cards
  const cards = document.querySelectorAll('.job-card');
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
