<?php
require_once 'config.php';
if (!isset($_SESSION['user_id'])) { header('Location: index.php'); exit; }
if (isset($_GET['logout'])) { session_destroy(); header('Location: index.php'); exit; }

$username = htmlspecialchars($_SESSION['username'] ?? 'User');
$role = $_SESSION['role'] ?? 'labour';
$isFarmer = $role === 'farmer';

$navGradient = $isFarmer ? 'linear-gradient(135deg,#10b981,#059669)' : 'linear-gradient(135deg,#f97316,#ea580c)';
$themeClass = $isFarmer ? '' : 'labour-theme';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
<title>Alerts – LabourLink</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="stylesheet" href="assets/style.css">
<link rel="stylesheet" href="assets/dashboard.css">
<style>
.alerts-header {
  background: <?= $navGradient ?>;
  padding: 24px 18px;
  color: #fff;
  margin-bottom: 0;
  border-radius: 0;
}

.alerts-header h2 {
  font-size: 1.3rem;
  font-weight: 700;
  margin: 0 0 6px 0;
}

.alerts-header p {
  font-size: 0.85rem;
  margin: 0;
  opacity: 0.9;
}

.alerts-container {
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
  border-color: <?= $isFarmer ? '#10b981' : '#f97316' ?>;
  color: <?= $isFarmer ? '#10b981' : '#f97316' ?>;
}

.filter-tab.active {
  background: <?= $isFarmer ? '#10b981' : '#f97316' ?>;
  color: #fff;
  border-color: <?= $isFarmer ? '#10b981' : '#f97316' ?>;
  box-shadow: 0 2px 8px <?= $isFarmer ? 'rgba(16, 185, 129, 0.3)' : 'rgba(249, 115, 22, 0.3)' ?>;
}

.alert-card {
  background: #fff;
  border-radius: 16px;
  padding: 16px;
  margin-bottom: 12px;
  box-shadow: 0 2px 12px rgba(0,0,0,0.06);
  border-left: 4px solid <?= $isFarmer ? '#10b981' : '#f97316' ?>;
  transition: all 0.2s;
  cursor: pointer;
  position: relative;
}

.alert-card:hover {
  box-shadow: 0 4px 16px rgba(0,0,0,0.1);
  transform: translateY(-2px);
}

.alert-card.unread {
  background: <?= $isFarmer ? '#f0fdf4' : '#fff7ed' ?>;
}

.alert-card.unread::before {
  content: '';
  position: absolute;
  top: 20px;
  right: 16px;
  width: 10px;
  height: 10px;
  background: <?= $isFarmer ? '#10b981' : '#f97316' ?>;
  border-radius: 50%;
  border: 2px solid #fff;
}

.alert-header {
  display: flex;
  align-items: start;
  gap: 12px;
  margin-bottom: 10px;
}

.alert-icon {
  width: 40px;
  height: 40px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.1rem;
  flex-shrink: 0;
}

.alert-icon.job {
  background: #eff6ff;
  color: #3b82f6;
}

.alert-icon.application {
  background: #f0fdf4;
  color: #10b981;
}

.alert-icon.message {
  background: #fef3c7;
  color: #f59e0b;
}

.alert-icon.system {
  background: #f3f4f6;
  color: #6b7280;
}

.alert-icon.payment {
  background: #fce7f3;
  color: #ec4899;
}

.alert-content {
  flex: 1;
}

.alert-title {
  font-size: 0.95rem;
  font-weight: 700;
  color: #1f2937;
  margin: 0 0 4px 0;
  line-height: 1.3;
}

.alert-message {
  font-size: 0.85rem;
  color: #6b7280;
  line-height: 1.4;
  margin-bottom: 8px;
}

.alert-meta {
  display: flex;
  align-items: center;
  gap: 12px;
  font-size: 0.75rem;
  color: #9ca3af;
}

.alert-time {
  display: flex;
  align-items: center;
  gap: 4px;
}

.alert-actions {
  display: flex;
  gap: 8px;
  margin-top: 12px;
  padding-top: 12px;
  border-top: 1px solid #e5e7eb;
}

.btn-alert-action {
  flex: 1;
  padding: 8px 12px;
  border-radius: 10px;
  font-size: 0.8rem;
  font-weight: 600;
  border: none;
  cursor: pointer;
  transition: all 0.2s;
}

.btn-alert-primary {
  background: <?= $isFarmer ? '#10b981' : '#f97316' ?>;
  color: #fff;
}

.btn-alert-primary:hover {
  background: <?= $isFarmer ? '#059669' : '#ea580c' ?>;
}

.btn-alert-secondary {
  background: #f3f4f6;
  color: #6b7280;
}

.btn-alert-secondary:hover {
  background: #e5e7eb;
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

.mark-all-read {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 8px 16px;
  background: #fff;
  border: 2px solid #e5e7eb;
  border-radius: 10px;
  font-size: 0.85rem;
  font-weight: 600;
  color: #6b7280;
  cursor: pointer;
  transition: all 0.2s;
  margin-bottom: 16px;
}

.mark-all-read:hover {
  border-color: <?= $isFarmer ? '#10b981' : '#f97316' ?>;
  color: <?= $isFarmer ? '#10b981' : '#f97316' ?>;
}

/* Mobile fixes */
@media (max-width: 479px) {
  .alerts-container {
    padding-bottom: 120px !important;
  }
  
  .alert-card:last-child {
    margin-bottom: 20px;
  }
}
</style>
</head>
<body>
<div class="phone-shell <?= $themeClass ?>">

  <!-- TOP NAVBAR -->
  <nav class="dash-nav" style="background:<?= $navGradient ?>">
    <button class="hamburger-btn" id="hamburgerBtn" onclick="openDrawer()" title="Menu">
      <i class="fa-solid fa-bars"></i>
    </button>
    <div class="dash-nav-brand">
      <i class="fa-solid fa-bell"></i>
      <span>Alerts</span>
    </div>
    <button class="nav-icon-btn" onclick="markAllRead()" title="Mark all as read">
      <i class="fa-solid fa-check-double"></i>
    </button>
  </nav>

  <!-- ── SIDE DRAWER ── -->
  <div class="drawer-overlay" id="drawerOverlay" onclick="closeDrawer()"></div>
  <div class="side-drawer" id="sideDrawer">
    <div class="drawer-header" style="background:<?= $navGradient ?>">
      <div class="drawer-avatar"><?= strtoupper(substr($username, 0, 2)) ?></div>
      <div>
        <div class="drawer-name"><?= $username ?></div>
        <div class="drawer-role"><?= ucfirst($role) ?></div>
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
      <?php if ($isFarmer): ?>
      <a href="post-job.php" class="drawer-link">
        <i class="fa-solid fa-plus-circle"></i> Post a Job
      </a>
      <a href="find-labour.php" class="drawer-link">
        <i class="fa-solid fa-users"></i> Find Labour
      </a>
      <a href="my-listings.php" class="drawer-link">
        <i class="fa-solid fa-list-check"></i> My Listings
      </a>
      <a href="schedule.php" class="drawer-link">
        <i class="fa-solid fa-calendar-days"></i> Schedule
      </a>
      <?php else: ?>
      <a href="find-labour.php" class="drawer-link">
        <i class="fa-solid fa-briefcase"></i> Find Jobs
      </a>
      <?php endif; ?>
      <a href="profile.php?tab=settings" class="drawer-link">
        <i class="fa-solid fa-gear"></i> Settings
      </a>
      <a href="alerts.php" class="drawer-link active">
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
    
    <div class="alerts-header">
      <h2>Notifications</h2>
      <p>Stay updated with your activity</p>
    </div>

    <div class="alerts-container">

      <!-- Filter Tabs -->
      <div class="filter-tabs">
        <button class="filter-tab active" onclick="filterAlerts('all')">All</button>
        <button class="filter-tab" onclick="filterAlerts('unread')">Unread</button>
        <button class="filter-tab" onclick="filterAlerts('job')">Jobs</button>
        <button class="filter-tab" onclick="filterAlerts('application')">Applications</button>
        <button class="filter-tab" onclick="filterAlerts('message')">Messages</button>
      </div>

      <!-- Alerts List -->
      <div id="alertsList">
        
        <!-- Sample Alert - New Application (Unread) -->
        <div class="alert-card unread" data-type="application" data-read="false" onclick="markAsRead(this)">
          <div class="alert-header">
            <div class="alert-icon application">
              <i class="fa-solid fa-file-alt"></i>
            </div>
            <div class="alert-content">
              <h3 class="alert-title">New Application Received</h3>
              <p class="alert-message">Rajesh Kumar applied for your Wheat Harvesting job posting</p>
              <div class="alert-meta">
                <span class="alert-time">
                  <i class="fa-solid fa-clock"></i>
                  2 hours ago
                </span>
              </div>
            </div>
          </div>
          <div class="alert-actions">
            <button class="btn-alert-action btn-alert-primary" onclick="event.stopPropagation(); viewApplication()">
              <i class="fa-solid fa-eye"></i> View Application
            </button>
            <button class="btn-alert-action btn-alert-secondary" onclick="event.stopPropagation(); dismissAlert(this)">
              Dismiss
            </button>
          </div>
        </div>

        <!-- Sample Alert - Job Match (Unread) -->
        <div class="alert-card unread" data-type="job" data-read="false" onclick="markAsRead(this)">
          <div class="alert-header">
            <div class="alert-icon job">
              <i class="fa-solid fa-briefcase"></i>
            </div>
            <div class="alert-content">
              <h3 class="alert-title">New Job Match</h3>
              <p class="alert-message">A new Rice Planting job in your area matches your profile</p>
              <div class="alert-meta">
                <span class="alert-time">
                  <i class="fa-solid fa-clock"></i>
                  5 hours ago
                </span>
              </div>
            </div>
          </div>
          <div class="alert-actions">
            <button class="btn-alert-action btn-alert-primary" onclick="event.stopPropagation(); viewJob()">
              <i class="fa-solid fa-eye"></i> View Job
            </button>
            <button class="btn-alert-action btn-alert-secondary" onclick="event.stopPropagation(); dismissAlert(this)">
              Dismiss
            </button>
          </div>
        </div>

        <!-- Sample Alert - Message (Read) -->
        <div class="alert-card" data-type="message" data-read="true">
          <div class="alert-header">
            <div class="alert-icon message">
              <i class="fa-solid fa-message"></i>
            </div>
            <div class="alert-content">
              <h3 class="alert-title">New Message</h3>
              <p class="alert-message">Priya Sharma sent you a message about the job details</p>
              <div class="alert-meta">
                <span class="alert-time">
                  <i class="fa-solid fa-clock"></i>
                  1 day ago
                </span>
              </div>
            </div>
          </div>
        </div>

        <!-- Sample Alert - Payment (Read) -->
        <div class="alert-card" data-type="payment" data-read="true">
          <div class="alert-header">
            <div class="alert-icon payment">
              <i class="fa-solid fa-indian-rupee-sign"></i>
            </div>
            <div class="alert-content">
              <h3 class="alert-title">Payment Received</h3>
              <p class="alert-message">You received ₹2,500 for completed work on Wheat Harvesting</p>
              <div class="alert-meta">
                <span class="alert-time">
                  <i class="fa-solid fa-clock"></i>
                  2 days ago
                </span>
              </div>
            </div>
          </div>
        </div>

        <!-- Sample Alert - System (Read) -->
        <div class="alert-card" data-type="system" data-read="true">
          <div class="alert-header">
            <div class="alert-icon system">
              <i class="fa-solid fa-circle-info"></i>
            </div>
            <div class="alert-content">
              <h3 class="alert-title">Profile Update</h3>
              <p class="alert-message">Your profile has been successfully updated with new skills</p>
              <div class="alert-meta">
                <span class="alert-time">
                  <i class="fa-solid fa-clock"></i>
                  3 days ago
                </span>
              </div>
            </div>
          </div>
        </div>

        <!-- Empty State (hidden by default) -->
        <div class="empty-state" style="display:none;" id="emptyState">
          <i class="fa-solid fa-bell-slash"></i>
          <h3>No notifications</h3>
          <p>You're all caught up! Check back later for updates.</p>
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
    <?php if ($isFarmer): ?>
    <a href="post-job.php" class="bottom-nav-item">
      <i class="fa-solid fa-briefcase"></i>
      <span>My Jobs</span>
    </a>
    <a href="find-labour.php" class="bottom-nav-item">
      <i class="fa-solid fa-users"></i>
      <span>Labour</span>
    </a>
    <?php else: ?>
    <a href="find-jobs.php" class="bottom-nav-item">
      <i class="fa-solid fa-magnifying-glass"></i>
      <span>Find Jobs</span>
    </a>
    <a href="applications.php" class="bottom-nav-item">
      <i class="fa-solid fa-file-alt"></i>
      <span>Applied</span>
    </a>
    <?php endif; ?>
    <a href="alerts.php" class="bottom-nav-item active">
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
function filterAlerts(type) {
  // Update active tab
  document.querySelectorAll('.filter-tab').forEach(tab => {
    tab.classList.remove('active');
  });
  event.target.classList.add('active');

  // Filter alert cards
  const cards = document.querySelectorAll('.alert-card');
  const emptyState = document.getElementById('emptyState');
  let visibleCount = 0;

  cards.forEach(card => {
    let show = false;
    
    if (type === 'all') {
      show = true;
    } else if (type === 'unread') {
      show = card.dataset.read === 'false';
    } else {
      show = card.dataset.type === type;
    }

    if (show) {
      card.style.display = 'block';
      visibleCount++;
    } else {
      card.style.display = 'none';
    }
  });

  // Show empty state if no cards visible
  emptyState.style.display = visibleCount === 0 ? 'block' : 'none';
}

function markAsRead(card) {
  card.classList.remove('unread');
  card.dataset.read = 'true';
  updateUnreadCount();
}

function markAllRead() {
  document.querySelectorAll('.alert-card.unread').forEach(card => {
    card.classList.remove('unread');
    card.dataset.read = 'true';
  });
  updateUnreadCount();
}

function dismissAlert(button) {
  const card = button.closest('.alert-card');
  card.style.opacity = '0';
  card.style.transform = 'translateX(100%)';
  setTimeout(() => {
    card.remove();
    checkEmpty();
  }, 300);
}

function checkEmpty() {
  const cards = document.querySelectorAll('.alert-card');
  const emptyState = document.getElementById('emptyState');
  const activeTab = document.querySelector('.filter-tab.active');
  
  let visibleCount = 0;
  cards.forEach(card => {
    if (card.style.display !== 'none') visibleCount++;
  });
  
  emptyState.style.display = visibleCount === 0 ? 'block' : 'none';
}

function updateUnreadCount() {
  const unreadCount = document.querySelectorAll('.alert-card.unread').length;
  // Update notification dots in nav if they exist
  document.querySelectorAll('.notif-dot, .bn-dot').forEach(dot => {
    dot.style.display = unreadCount > 0 ? 'block' : 'none';
  });
}

function viewApplication() {
  alert('View Application - This would navigate to the application details page');
}

function viewJob() {
  window.location.href = 'find-labour.php';
}

function openDrawer() {
  document.getElementById('sideDrawer').classList.add('open');
  document.getElementById('drawerOverlay').classList.add('active');
}

function closeDrawer() {
  document.getElementById('sideDrawer').classList.remove('open');
  document.getElementById('drawerOverlay').classList.remove('active');
}

// Initialize
updateUnreadCount();
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
