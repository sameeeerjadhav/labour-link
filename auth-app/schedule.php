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
<title>Schedule – LabourLink</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="stylesheet" href="assets/style.css">
<link rel="stylesheet" href="assets/dashboard.css">
<style>
.schedule-header {
  background: linear-gradient(135deg, #10b981, #059669);
  padding: 24px 18px;
  color: #fff;
  margin-bottom: 0;
  border-radius: 0;
}

.schedule-header h2 {
  font-size: 1.3rem;
  font-weight: 700;
  margin: 0 0 6px 0;
}

.schedule-header p {
  font-size: 0.85rem;
  margin: 0;
  opacity: 0.9;
}

.calendar-nav {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 16px;
  padding: 14px 16px;
  background: #fff;
  border-radius: 12px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.05);
}

.calendar-month {
  font-size: 1.05rem;
  font-weight: 700;
  color: #2d2d3a;
}

.calendar-nav-btn {
  background: #f0fdf4;
  border: none;
  width: 32px;
  height: 32px;
  border-radius: 8px;
  color: #10b981;
  cursor: pointer;
  transition: all 0.2s;
}

.calendar-nav-btn:hover {
  background: #10b981;
  color: #fff;
}

.calendar-grid {
  display: grid;
  grid-template-columns: repeat(7, 1fr);
  gap: 6px;
  margin-bottom: 20px;
  padding: 0 4px;
}

.calendar-day-header {
  text-align: center;
  font-size: 0.7rem;
  font-weight: 700;
  color: #999;
  padding: 8px 0;
  text-transform: uppercase;
}

.calendar-day {
  aspect-ratio: 1;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  background: #fff;
  border-radius: 10px;
  font-size: 0.9rem;
  font-weight: 600;
  color: #2d2d3a;
  cursor: pointer;
  transition: all 0.2s;
  position: relative;
  border: 2px solid transparent;
  min-height: 42px;
}

.calendar-day:hover {
  background: #f0fdf4;
  border-color: #10b981;
}

.calendar-day.today {
  background: #10b981;
  color: #fff;
}

.calendar-day.has-event {
  border-color: #10b981;
}

.calendar-day.has-event::after {
  content: '';
  position: absolute;
  bottom: 4px;
  width: 4px;
  height: 4px;
  background: #10b981;
  border-radius: 50%;
}

.calendar-day.today.has-event::after {
  background: #fff;
}

.calendar-day.other-month {
  color: #ccc;
  background: #f9fafb;
}

.schedule-list-header {
  font-size: 0.9rem;
  font-weight: 700;
  color: #2d2d3a;
  margin-bottom: 12px;
  display: flex;
  align-items: center;
  gap: 8px;
}

.schedule-item {
  background: #fff;
  border-radius: 12px;
  padding: 14px;
  margin-bottom: 10px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.05);
  border-left: 4px solid #10b981;
}

.schedule-time {
  font-size: 0.75rem;
  font-weight: 700;
  color: #10b981;
  margin-bottom: 6px;
}

.schedule-title {
  font-size: 0.9rem;
  font-weight: 700;
  color: #2d2d3a;
  margin-bottom: 4px;
}

.schedule-details {
  font-size: 0.8rem;
  color: #666;
  display: flex;
  flex-wrap: wrap;
  gap: 12px;
}

.schedule-detail-item {
  display: flex;
  align-items: center;
  gap: 4px;
}

.schedule-detail-item i {
  font-size: 0.7rem;
  color: #10b981;
}

.empty-schedule {
  text-align: center;
  padding: 40px 20px;
  color: #999;
}

.empty-schedule i {
  font-size: 3rem;
  margin-bottom: 16px;
  color: #ddd;
}

.empty-schedule h3 {
  font-size: 1rem;
  font-weight: 700;
  color: #666;
  margin-bottom: 8px;
}

.empty-schedule p {
  font-size: 0.85rem;
  color: #999;
}

.legend {
  display: flex;
  gap: 16px;
  padding: 12px;
  background: #f9fafb;
  border-radius: 8px;
  margin-bottom: 16px;
  font-size: 0.75rem;
}

.legend-item {
  display: flex;
  align-items: center;
  gap: 6px;
}

.legend-dot {
  width: 8px;
  height: 8px;
  border-radius: 50%;
}

.legend-dot.today {
  background: #10b981;
}

.legend-dot.event {
  border: 2px solid #10b981;
  background: #fff;
}

.schedule-container {
  padding: 20px 18px 20px;
  background: #f4f4fb;
  flex: 1;
}

/* Mobile fixes */
@media (max-width: 479px) {
  .schedule-container {
    padding-bottom: 120px !important;
  }
  
  .form-container {
    padding-bottom: 120px !important;
  }
  
  #scheduleList .schedule-item:last-child {
    margin-bottom: 20px;
  }
  
  .calendar-grid {
    margin-bottom: 24px;
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
      <i class="fa-solid fa-calendar-days"></i>
      <span>Schedule</span>
    </div>
    <button class="nav-icon-btn" title="Add Event">
      <i class="fa-solid fa-plus"></i>
    </button>
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
      <a href="my-listings.php" class="drawer-link">
        <i class="fa-solid fa-list-check"></i> My Listings
      </a>
      <a href="schedule.php" class="drawer-link active">
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
    
    <div class="schedule-header">
      <h2>Work Schedule</h2>
      <p>Manage your farm work calendar</p>
    </div>

    <div class="schedule-container">

      <!-- Calendar Navigation -->
      <div class="calendar-nav">
        <button class="calendar-nav-btn" onclick="previousMonth()">
          <i class="fa-solid fa-chevron-left"></i>
        </button>
        <span class="calendar-month" id="currentMonth">January 2024</span>
        <button class="calendar-nav-btn" onclick="nextMonth()">
          <i class="fa-solid fa-chevron-right"></i>
        </button>
      </div>

      <!-- Legend -->
      <div class="legend">
        <div class="legend-item">
          <span class="legend-dot today"></span>
          <span>Today</span>
        </div>
        <div class="legend-item">
          <span class="legend-dot event"></span>
          <span>Has Events</span>
        </div>
      </div>

      <!-- Calendar Grid -->
      <div class="calendar-grid">
        <div class="calendar-day-header">Sun</div>
        <div class="calendar-day-header">Mon</div>
        <div class="calendar-day-header">Tue</div>
        <div class="calendar-day-header">Wed</div>
        <div class="calendar-day-header">Thu</div>
        <div class="calendar-day-header">Fri</div>
        <div class="calendar-day-header">Sat</div>
        
        <!-- Sample calendar days -->
        <div class="calendar-day other-month">28</div>
        <div class="calendar-day other-month">29</div>
        <div class="calendar-day other-month">30</div>
        <div class="calendar-day other-month">31</div>
        <div class="calendar-day">1</div>
        <div class="calendar-day">2</div>
        <div class="calendar-day">3</div>
        
        <div class="calendar-day">4</div>
        <div class="calendar-day has-event">5</div>
        <div class="calendar-day">6</div>
        <div class="calendar-day">7</div>
        <div class="calendar-day has-event">8</div>
        <div class="calendar-day">9</div>
        <div class="calendar-day">10</div>
        
        <div class="calendar-day">11</div>
        <div class="calendar-day">12</div>
        <div class="calendar-day">13</div>
        <div class="calendar-day today">14</div>
        <div class="calendar-day has-event">15</div>
        <div class="calendar-day">16</div>
        <div class="calendar-day">17</div>
        
        <div class="calendar-day">18</div>
        <div class="calendar-day">19</div>
        <div class="calendar-day has-event">20</div>
        <div class="calendar-day">21</div>
        <div class="calendar-day">22</div>
        <div class="calendar-day">23</div>
        <div class="calendar-day">24</div>
        
        <div class="calendar-day">25</div>
        <div class="calendar-day">26</div>
        <div class="calendar-day">27</div>
        <div class="calendar-day">28</div>
        <div class="calendar-day">29</div>
        <div class="calendar-day">30</div>
        <div class="calendar-day other-month">1</div>
      </div>

      <!-- Today's Schedule -->
      <div class="schedule-list-header">
        <i class="fa-solid fa-calendar-check"></i>
        Today's Schedule
      </div>

      <div id="scheduleList">
        
        <!-- Sample Schedule Item -->
        <div class="schedule-item">
          <div class="schedule-time">
            <i class="fa-solid fa-clock"></i> 8:00 AM - 5:00 PM
          </div>
          <div class="schedule-title">Wheat Harvesting</div>
          <div class="schedule-details">
            <span class="schedule-detail-item">
              <i class="fa-solid fa-users"></i>
              5 workers
            </span>
            <span class="schedule-detail-item">
              <i class="fa-solid fa-location-dot"></i>
              North Field
            </span>
          </div>
        </div>

        <div class="schedule-item">
          <div class="schedule-time">
            <i class="fa-solid fa-clock"></i> 2:00 PM - 4:00 PM
          </div>
          <div class="schedule-title">Equipment Maintenance</div>
          <div class="schedule-details">
            <span class="schedule-detail-item">
              <i class="fa-solid fa-wrench"></i>
              Tractor service
            </span>
            <span class="schedule-detail-item">
              <i class="fa-solid fa-location-dot"></i>
              Workshop
            </span>
          </div>
        </div>

        <!-- Empty State (hidden by default) -->
        <div class="empty-schedule" style="display:none;" id="emptySchedule">
          <i class="fa-solid fa-calendar-xmark"></i>
          <h3>No events scheduled</h3>
          <p>Your schedule for today is clear</p>
        </div>

      </div>

      <!-- Upcoming Events -->
      <div class="schedule-list-header" style="margin-top:24px;">
        <i class="fa-solid fa-calendar-days"></i>
        Upcoming Events
      </div>

      <div class="schedule-item">
        <div class="schedule-time">
          <i class="fa-solid fa-calendar"></i> Tomorrow, 9:00 AM
        </div>
        <div class="schedule-title">Rice Planting</div>
        <div class="schedule-details">
          <span class="schedule-detail-item">
            <i class="fa-solid fa-users"></i>
            3 workers
          </span>
          <span class="schedule-detail-item">
            <i class="fa-solid fa-location-dot"></i>
            South Field
          </span>
        </div>
      </div>

      <div class="schedule-item">
        <div class="schedule-time">
          <i class="fa-solid fa-calendar"></i> Jan 20, 7:00 AM
        </div>
        <div class="schedule-title">Fertilizer Application</div>
        <div class="schedule-details">
          <span class="schedule-detail-item">
            <i class="fa-solid fa-users"></i>
            2 workers
          </span>
          <span class="schedule-detail-item">
            <i class="fa-solid fa-location-dot"></i>
            All Fields
          </span>
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
    <a href="post-job.php" class="bottom-nav-item">
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
const months = ['January', 'February', 'March', 'April', 'May', 'June', 
                'July', 'August', 'September', 'October', 'November', 'December'];
// Set to April 2026 to match the calendar display
let currentMonthIndex = 3; // April (0-indexed)
let currentYear = 2026;

function updateMonthDisplay() {
  document.getElementById('currentMonth').textContent = `${months[currentMonthIndex]} ${currentYear}`;
}

function previousMonth() {
  currentMonthIndex--;
  if (currentMonthIndex < 0) {
    currentMonthIndex = 11;
    currentYear--;
  }
  updateMonthDisplay();
}

function nextMonth() {
  currentMonthIndex++;
  if (currentMonthIndex > 11) {
    currentMonthIndex = 0;
    currentYear++;
  }
  updateMonthDisplay();
}

// Initialize
updateMonthDisplay();

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
