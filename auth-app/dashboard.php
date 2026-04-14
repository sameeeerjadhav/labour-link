<?php
require_once 'config.php';
if (!isset($_SESSION['user_id'])) { header('Location: index.php'); exit; }
if (isset($_GET['logout']))       { session_destroy(); header('Location: index.php'); exit; }

$username  = htmlspecialchars($_SESSION['username'] ?? 'User');
$role      = $_SESSION['role'] ?? 'labour';
$isFarmer   = $role === 'farmer';
$initials   = strtoupper(substr($username, 0, 2));
$navGradient = $isFarmer
    ? 'linear-gradient(135deg,#6c63ff,#3b82f6)'
    : 'linear-gradient(135deg,#10b981,#059669)';
$bannerGradient = $navGradient;

// ── Fetch weather via wttr.in (no API key needed) ──
$weather = null;
try {
    $clientIp = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'] ?? '';
    // Use IP-based location; wttr.in auto-detects if no city given
    $wttrUrl  = 'https://wttr.in/?format=j1';
    $ctx      = stream_context_create(['http' => ['timeout' => 5, 'ignore_errors' => true]]);
    $raw      = @file_get_contents($wttrUrl, false, $ctx);
    if ($raw) {
        $w = json_decode($raw, true);
        if ($w) {
            $cur  = $w['current_condition'][0];
            $area = $w['nearest_area'][0];
            $weather = [
                'temp'      => $cur['temp_C'],
                'feels'     => $cur['FeelsLikeC'],
                'desc'      => $cur['weatherDesc'][0]['value'],
                'humidity'  => $cur['humidity'],
                'wind'      => $cur['windspeedKmph'],
                'city'      => $area['areaName'][0]['value'] . ', ' . $area['country'][0]['value'],
                'code'      => (int)$cur['weatherCode'],
            ];
        }
    }
} catch (Exception $e) { $weather = null; }

// Map wttr weather code to emoji + farm tip
function weatherEmoji(int $code): string {
    if ($code === 113) return '☀️';
    if (in_array($code, [116,119,122])) return '☁️';
    if (in_array($code, [143,248,260])) return '🌫️';
    if (in_array($code, [176,263,266,293,296,353])) return '🌦️';
    if (in_array($code, [299,302,305,308,356,359])) return '🌧️';
    if (in_array($code, [200,386,389,392,395])) return '⛈️';
    if ($code >= 317) return '❄️';
    return '🌤️';
}

function farmTip(int $code): string {
    if (in_array($code, [200,386,389,392,395])) return '⚠️ Avoid outdoor farm work today.';
    if (in_array($code, [299,302,305,308,356,359])) return '💧 Check drainage systems.';
    if (in_array($code, [176,263,266,293,296,353])) return '🌱 Light rain — good for seedlings.';
    if ($code >= 317 && $code <= 395) return '❄️ Protect crops from frost.';
    if ($code === 113) return '✅ Great day for field work!';
    if (in_array($code, [116,119,122])) return '🌥️ Comfortable working conditions.';
    return '🌾 Check conditions before heading out.';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
<title>LabourLink – <?= $isFarmer ? 'Farmer' : 'Labour' ?> Dashboard</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="stylesheet" href="assets/style.css">
<link rel="stylesheet" href="assets/dashboard.css">
</head>
<body class="<?= $isFarmer ? '' : 'labour-theme' ?>">
<div class="phone-shell">

  <!-- ── TOP NAVBAR ── -->
  <nav class="dash-nav" style="background:<?= $navGradient ?>">
    <button class="hamburger-btn" id="hamburgerBtn" onclick="openDrawer()" title="Menu">
      <i class="fa-solid fa-bars"></i>
    </button>
    <div class="dash-nav-brand">
      <i class="fa-solid fa-seedling"></i>
      <span>LabourLink</span>
    </div>
    <div class="dash-nav-right">
      <button class="nav-icon-btn" title="Notifications">
        <i class="fa-solid fa-bell"></i>
        <span class="notif-dot"></span>
      </button>
    </div>
  </nav>

  <!-- ── SIDE DRAWER ── -->
  <div class="drawer-overlay" id="drawerOverlay" onclick="closeDrawer()"></div>
  <div class="side-drawer" id="sideDrawer">
    <div class="drawer-header" style="background:<?= $navGradient ?>">
      <div class="drawer-avatar"><?= $initials ?></div>
      <div>
        <div class="drawer-name"><?= $username ?></div>
        <div class="drawer-role"><?= $isFarmer ? 'Farmer' : 'Labour' ?></div>
      </div>
      <button class="drawer-close" onclick="closeDrawer()"><i class="fa-solid fa-xmark"></i></button>
    </div>
    <nav class="drawer-nav">
      <a href="dashboard.php" class="drawer-link active">
        <i class="fa-solid fa-house"></i> Home
      </a>
      <a href="profile.php" class="drawer-link">
        <i class="fa-solid fa-user"></i> My Profile
      </a>
      <a href="profile.php?tab=settings" class="drawer-link">
        <i class="fa-solid fa-gear"></i> Settings
      </a>
      <a href="#" class="drawer-link" onclick="closeDrawer(); return false;">
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

  <!-- ── SCROLLABLE CONTENT ── -->
  <div class="dash-body">

    <?php if ($isFarmer): ?>

    <!-- Welcome Banner -->
    <div class="welcome-banner" style="background:<?= $bannerGradient ?>">
      <div>
        <p class="welcome-greeting">Good day, <?= $username ?> 👋</p>
        <p class="welcome-sub">Manage your farm workforce from here</p>
      </div>
      <div class="welcome-icon"><i class="fa-solid fa-tractor"></i></div>
    </div>

    <!-- Weather Card -->
    <?php if ($weather): ?>
    <div class="weather-card">
      <div class="weather-content">
        <div class="weather-left">
          <div class="weather-icon"><?= weatherEmoji($weather['code']) ?></div>
          <div>
            <div class="weather-temp"><?= $weather['temp'] ?>°C</div>
            <div class="weather-desc"><?= htmlspecialchars($weather['desc']) ?></div>
          </div>
        </div>
        <div class="weather-right">
          <div class="weather-location"><i class="fa-solid fa-location-dot"></i> <?= htmlspecialchars($weather['city']) ?></div>
          <div class="weather-meta">
            <span><i class="fa-solid fa-droplet"></i> <?= $weather['humidity'] ?>%</span>
            <span><i class="fa-solid fa-wind"></i> <?= $weather['wind'] ?> km/h</span>
          </div>
          <div class="weather-farm-tip"><?= farmTip($weather['code']) ?></div>
        </div>
      </div>
    </div>
    <?php else: ?>
    <div class="weather-card">
      <div class="weather-error"><i class="fa-solid fa-triangle-exclamation"></i> Weather unavailable</div>
    </div>
    <?php endif; ?>

    <!-- Stats Row -->
    <div class="stats-row">
      <div class="stat-card">
        <span class="stat-num">0</span>
        <span class="stat-label">Active Jobs</span>
      </div>
      <div class="stat-card">
        <span class="stat-num">0</span>
        <span class="stat-label">Applicants</span>
      </div>
      <div class="stat-card">
        <span class="stat-num">0</span>
        <span class="stat-label">Hired</span>
      </div>
    </div>

    <!-- Quick Actions -->
    <p class="dash-section-title">Quick Actions</p>
    <div class="action-grid">
      <div class="action-tile" style="--clr:#6c63ff;">
        <div class="action-tile-icon"><i class="fa-solid fa-plus-circle"></i></div>
        <span>Post a Job</span>
      </div>
      <div class="action-tile" style="--clr:#f59e0b;">
        <div class="action-tile-icon"><i class="fa-solid fa-users"></i></div>
        <span>Find Labour</span>
      </div>
      <div class="action-tile" style="--clr:#10b981;">
        <div class="action-tile-icon"><i class="fa-solid fa-list-check"></i></div>
        <span>My Listings</span>
      </div>
      <div class="action-tile" style="--clr:#3b82f6;">
        <div class="action-tile-icon"><i class="fa-solid fa-calendar-days"></i></div>
        <span>Schedule</span>
      </div>
    </div>

    <!-- Recent Activity -->
    <p class="dash-section-title" style="margin-top:20px;">Recent Activity</p>
    <div class="activity-list">
      <div class="activity-empty">
        <i class="fa-solid fa-inbox"></i>
        <p>No activity yet. Post your first job to get started.</p>
      </div>
    </div>

    <?php else: ?>

    <!-- Labour Dashboard -->
    <div class="welcome-banner" style="background:<?= $bannerGradient ?>">
      <div>
        <p class="welcome-greeting">Good day, <?= $username ?> 👋</p>
        <p class="welcome-sub">Find farm work near you</p>
      </div>
      <div class="welcome-icon"><i class="fa-solid fa-person-digging"></i></div>
    </div>

    <!-- Weather Card -->
    <?php if ($weather): ?>
    <div class="weather-card">
      <div class="weather-content">
        <div class="weather-left">
          <div class="weather-icon"><?= weatherEmoji($weather['code']) ?></div>
          <div>
            <div class="weather-temp"><?= $weather['temp'] ?>°C</div>
            <div class="weather-desc"><?= htmlspecialchars($weather['desc']) ?></div>
          </div>
        </div>
        <div class="weather-right">
          <div class="weather-location"><i class="fa-solid fa-location-dot"></i> <?= htmlspecialchars($weather['city']) ?></div>
          <div class="weather-meta">
            <span><i class="fa-solid fa-droplet"></i> <?= $weather['humidity'] ?>%</span>
            <span><i class="fa-solid fa-wind"></i> <?= $weather['wind'] ?> km/h</span>
          </div>
          <div class="weather-farm-tip"><?= farmTip($weather['code']) ?></div>
        </div>
      </div>
    </div>
    <?php else: ?>
    <div class="weather-card">
      <div class="weather-error"><i class="fa-solid fa-triangle-exclamation"></i> Weather unavailable</div>
    </div>
    <?php endif; ?>

    <!-- Stats Row -->
    <div class="stats-row">
      <div class="stat-card">
        <span class="stat-num">0</span>
        <span class="stat-label">Applied</span>
      </div>
      <div class="stat-card">
        <span class="stat-num">0</span>
        <span class="stat-label">Shortlisted</span>
      </div>
      <div class="stat-card">
        <span class="stat-num">0</span>
        <span class="stat-label">Completed</span>
      </div>
    </div>

    <!-- Quick Actions -->
    <p class="dash-section-title">Quick Actions</p>
    <div class="action-grid">
      <div class="action-tile" style="--clr:#10b981;">
        <div class="action-tile-icon"><i class="fa-solid fa-magnifying-glass"></i></div>
        <span>Find Jobs</span>
      </div>
      <div class="action-tile" style="--clr:#f59e0b;">
        <div class="action-tile-icon"><i class="fa-solid fa-file-alt"></i></div>
        <span>Applications</span>
      </div>
      <div class="action-tile" style="--clr:#3b82f6;">
        <div class="action-tile-icon"><i class="fa-solid fa-star"></i></div>
        <span>My Profile</span>
      </div>
      <div class="action-tile" style="--clr:#059669;">
        <div class="action-tile-icon"><i class="fa-solid fa-map-location-dot"></i></div>
        <span>Nearby Jobs</span>
      </div>
    </div>

    <!-- Recent Activity -->
    <p class="dash-section-title" style="margin-top:20px;">Recent Activity</p>
    <div class="activity-list">
      <div class="activity-empty">
        <i class="fa-solid fa-inbox"></i>
        <p>No activity yet. Start by browsing available jobs.</p>
      </div>
    </div>

    <?php endif; ?>

  </div><!-- /.dash-body -->

  <!-- ── BOTTOM NAV ── -->
  <nav class="bottom-nav">
    <a href="dashboard.php" class="bottom-nav-item active" id="bnHome">
      <i class="fa-solid fa-house"></i>
      <span>Home</span>
    </a>
    <?php if ($isFarmer): ?>
    <a href="#" class="bottom-nav-item" id="bnJobs">
      <i class="fa-solid fa-briefcase"></i>
      <span>My Jobs</span>
    </a>
    <a href="#" class="bottom-nav-item" id="bnLabour">
      <i class="fa-solid fa-users"></i>
      <span>Labour</span>
    </a>
    <?php else: ?>
    <a href="#" class="bottom-nav-item" id="bnSearch">
      <i class="fa-solid fa-magnifying-glass"></i>
      <span>Find Jobs</span>
    </a>
    <a href="#" class="bottom-nav-item" id="bnApply">
      <i class="fa-solid fa-file-alt"></i>
      <span>Applied</span>
    </a>
    <?php endif; ?>
    <a href="#" class="bottom-nav-item" id="bnNotif">
      <i class="fa-solid fa-bell"></i>
      <span>Alerts</span>
      <span class="bn-dot"></span>
    </a>
    <a href="profile.php" class="bottom-nav-item" id="bnProfile">
      <i class="fa-solid fa-user"></i>
      <span>Profile</span>
    </a>
  </nav>

</div><!-- /.phone-shell -->

<script>
function openDrawer() {
  document.getElementById('sideDrawer').classList.add('open');
  document.getElementById('drawerOverlay').classList.add('active');
}

function closeDrawer() {
  document.getElementById('sideDrawer').classList.remove('open');
  document.getElementById('drawerOverlay').classList.remove('active');
}
</script>
</body>
</html>
