<?php
require_once 'config.php';
if (!isset($_SESSION['user_id'])) { header('Location: index.php'); exit; }
if ($_SESSION['role'] !== 'farmer') { header('Location: dashboard.php'); exit; }

$username = htmlspecialchars($_SESSION['username'] ?? 'User');
$initials = strtoupper(substr($username, 0, 2));
$navGradient = 'linear-gradient(135deg,#6c63ff,#3b82f6)';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
<title>Find Labour – LabourLink</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="stylesheet" href="assets/style.css">
<link rel="stylesheet" href="assets/dashboard.css">
<style>
/* Fix layout structure */
.find-labour-container {
  display: flex;
  flex-direction: column;
  height: 100%;
  overflow: hidden;
}

.scrollable-content {
  flex: 1;
  overflow-y: auto;
  overflow-x: hidden;
}

.scrollable-content::-webkit-scrollbar { display: none; }

.search-header {
  background: linear-gradient(135deg, #6c63ff, #3b82f6);
  padding: 20px 18px;
  color: #fff;
  flex-shrink: 0;
}

.search-box {
  background: rgba(255,255,255,0.2);
  border: 1px solid rgba(255,255,255,0.3);
  border-radius: 12px;
  padding: 10px 14px;
  display: flex;
  align-items: center;
  gap: 10px;
  margin-top: 12px;
}

.search-box input {
  flex: 1;
  background: transparent;
  border: none;
  color: #fff;
  font-size: 0.9rem;
  outline: none;
}

.search-box input::placeholder { color: rgba(255,255,255,0.7); }

.search-box i { color: rgba(255,255,255,0.8); }

.filter-chips {
  display: flex;
  gap: 8px;
  padding: 12px 18px;
  overflow-x: auto;
  background: #fff;
  border-bottom: 1px solid #f0f0f8;
  flex-shrink: 0;
}

.filter-chips::-webkit-scrollbar { display: none; }

.filter-chip {
  background: #f5f4ff;
  border: 1px solid #e0dfff;
  border-radius: 20px;
  padding: 6px 14px;
  font-size: 0.8rem;
  font-weight: 600;
  color: #6c63ff;
  white-space: nowrap;
  cursor: pointer;
  transition: all 0.2s;
}

.filter-chip.active {
  background: #6c63ff;
  color: #fff;
  border-color: #6c63ff;
}

.labour-list {
  padding: 16px 18px 20px;
  background: #f4f4fb;
  min-height: 100%;
}

.labour-card {
  background: #fff;
  border-radius: 16px;
  padding: 16px;
  margin-bottom: 12px;
  box-shadow: 0 2px 10px rgba(0,0,0,0.05);
  display: flex;
  gap: 14px;
  cursor: pointer;
  transition: transform 0.2s, box-shadow 0.2s;
}

.labour-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 20px rgba(0,0,0,0.1);
}

.labour-avatar {
  width: 56px;
  height: 56px;
  border-radius: 50%;
  background: linear-gradient(135deg, #10b981, #059669);
  color: #fff;
  font-size: 1.1rem;
  font-weight: 700;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.labour-info {
  flex: 1;
  min-width: 0;
}

.labour-name {
  font-size: 0.95rem;
  font-weight: 700;
  color: #2d2d3a;
  margin-bottom: 4px;
}

.labour-meta {
  display: flex;
  align-items: center;
  gap: 12px;
  font-size: 0.75rem;
  color: #888;
  margin-bottom: 6px;
}

.labour-meta i {
  font-size: 0.7rem;
  margin-right: 3px;
}

.labour-skills {
  display: flex;
  gap: 6px;
  flex-wrap: wrap;
}

.skill-tag {
  background: #f0fdf4;
  color: #10b981;
  font-size: 0.7rem;
  font-weight: 600;
  padding: 3px 8px;
  border-radius: 6px;
}

.labour-actions {
  display: flex;
  flex-direction: column;
  gap: 6px;
  align-items: flex-end;
}

.contact-btn {
  background: #6c63ff;
  color: #fff;
  border: none;
  border-radius: 8px;
  padding: 6px 12px;
  font-size: 0.75rem;
  font-weight: 600;
  cursor: pointer;
  transition: background 0.2s;
}

.contact-btn:hover { background: #5a52d5; }

.distance-badge {
  background: #f0f0f8;
  color: #666;
  font-size: 0.7rem;
  font-weight: 600;
  padding: 4px 8px;
  border-radius: 6px;
}

.loading-spinner {
  text-align: center;
  padding: 40px 20px;
  color: #999;
}

.loading-spinner i {
  font-size: 2rem;
  animation: spin 1s linear infinite;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}

.no-results {
  text-align: center;
  padding: 40px 20px;
  color: #999;
}

.no-results i {
  font-size: 3rem;
  margin-bottom: 12px;
  display: block;
  color: #ddd;
}

.location-error {
  background: #fff5f5;
  border: 1px solid #fecaca;
  border-radius: 12px;
  padding: 16px;
  margin: 16px 18px;
  color: #ef4444;
  font-size: 0.85rem;
  display: flex;
  align-items: center;
  gap: 10px;
}

.location-error i { font-size: 1.2rem; }
</style>
</head>
<body>
<div class="phone-shell">

  <!-- ── TOP NAVBAR ── -->
  <nav class="dash-nav" style="background:<?= $navGradient ?>">
    <a href="dashboard.php" class="hamburger-btn" title="Back">
      <i class="fa-solid fa-arrow-left"></i>
    </a>
    <div class="dash-nav-brand">
      <i class="fa-solid fa-users"></i>
      <span>Find Labour</span>
    </div>
    <div class="dash-nav-right">
      <button class="nav-icon-btn" id="locationBtn" title="Use my location">
        <i class="fa-solid fa-location-crosshairs"></i>
      </button>
    </div>
  </nav>

  <!-- ── SCROLLABLE CONTENT ── -->
  <div class="scrollable-content">
    <!-- ── SEARCH HEADER ── -->
    <div class="search-header">
      <div style="font-size:0.85rem;font-weight:600;margin-bottom:4px;">Find skilled labour nearby</div>
      <div class="search-box">
        <i class="fa-solid fa-magnifying-glass"></i>
        <input type="text" id="searchInput" placeholder="Search by name or skill...">
      </div>
    </div>

    <!-- ── FILTER CHIPS ── -->
    <div class="filter-chips">
      <div class="filter-chip active" data-filter="all">All</div>
      <div class="filter-chip" data-filter="5">Within 5 km</div>
      <div class="filter-chip" data-filter="10">Within 10 km</div>
      <div class="filter-chip" data-filter="25">Within 25 km</div>
      <div class="filter-chip" data-filter="verified">Verified Only</div>
    </div>

    <!-- ── LABOUR LIST ── -->
    <div class="labour-list" id="labourList">
      <div class="loading-spinner">
        <i class="fa-solid fa-spinner"></i>
        <p style="margin-top:12px;font-size:0.85rem;">Finding nearby labour...</p>
      </div>
    </div>
  </div>

  <!-- ── BOTTOM NAV ── -->
  <nav class="bottom-nav">
    <a href="dashboard.php" class="bottom-nav-item">
      <i class="fa-solid fa-house"></i>
      <span>Home</span>
    </a>
    <a href="#" class="bottom-nav-item">
      <i class="fa-solid fa-briefcase"></i>
      <span>My Jobs</span>
    </a>
    <a href="find-labour.php" class="bottom-nav-item active">
      <i class="fa-solid fa-users"></i>
      <span>Labour</span>
    </a>
    <a href="#" class="bottom-nav-item">
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
let userLat = null;
let userLng = null;
let allLabour = [];
let currentFilter = 'all';

// Get user location on load
window.addEventListener('DOMContentLoaded', () => {
  getUserLocation();
  
  // Search functionality
  document.getElementById('searchInput').addEventListener('input', filterLabour);
  
  // Filter chips
  document.querySelectorAll('.filter-chip').forEach(chip => {
    chip.addEventListener('click', function() {
      document.querySelectorAll('.filter-chip').forEach(c => c.classList.remove('active'));
      this.classList.add('active');
      currentFilter = this.dataset.filter;
      filterLabour();
    });
  });
  
  // Location button
  document.getElementById('locationBtn').addEventListener('click', getUserLocation);
});

function getUserLocation() {
  if (!navigator.geolocation) {
    showError('Geolocation is not supported by your browser');
    return;
  }
  
  document.getElementById('labourList').innerHTML = `
    <div class="loading-spinner">
      <i class="fa-solid fa-spinner"></i>
      <p style="margin-top:12px;font-size:0.85rem;">Getting your location...</p>
    </div>
  `;
  
  navigator.geolocation.getCurrentPosition(
    position => {
      userLat = position.coords.latitude;
      userLng = position.coords.longitude;
      fetchLabour();
    },
    error => {
      console.error('Location error:', error);
      showError('Unable to get your location. Please enable location services.');
      // Fetch without location
      fetchLabour();
    }
  );
}

function fetchLabour() {
  const params = new URLSearchParams();
  if (userLat && userLng) {
    params.append('lat', userLat);
    params.append('lng', userLng);
  }
  
  fetch('api/get-labour.php?' + params.toString())
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        allLabour = data.labour;
        filterLabour();
      } else {
        showError(data.message || 'Failed to fetch labour');
      }
    })
    .catch(error => {
      console.error('Fetch error:', error);
      showError('Network error. Please try again.');
    });
}

function filterLabour() {
  const searchTerm = document.getElementById('searchInput').value.toLowerCase();
  
  let filtered = allLabour.filter(labour => {
    // Search filter
    const matchesSearch = !searchTerm || 
      labour.full_name.toLowerCase().includes(searchTerm) ||
      labour.username.toLowerCase().includes(searchTerm);
    
    if (!matchesSearch) return false;
    
    // Distance filter
    if (currentFilter !== 'all' && currentFilter !== 'verified') {
      const maxDistance = parseInt(currentFilter);
      if (!labour.distance || labour.distance > maxDistance) return false;
    }
    
    // Verified filter
    if (currentFilter === 'verified' && !labour.is_verified) return false;
    
    return true;
  });
  
  displayLabour(filtered);
}

function displayLabour(labour) {
  const container = document.getElementById('labourList');
  
  if (labour.length === 0) {
    container.innerHTML = `
      <div class="no-results">
        <i class="fa-solid fa-user-slash"></i>
        <p>No labour found matching your criteria</p>
      </div>
    `;
    return;
  }
  
  container.innerHTML = labour.map(person => {
    const initials = person.full_name.substring(0, 2).toUpperCase();
    const distanceBadge = person.distance 
      ? `<div class="distance-badge"><i class="fa-solid fa-location-dot"></i> ${person.distance.toFixed(1)} km</div>`
      : '';
    
    return `
      <div class="labour-card" onclick="viewLabour(${person.id})">
        <div class="labour-avatar">${initials}</div>
        <div class="labour-info">
          <div class="labour-name">
            ${person.full_name}
            ${person.is_verified ? '<i class="fa-solid fa-circle-check" style="color:#10b981;font-size:0.85rem;"></i>' : ''}
          </div>
          <div class="labour-meta">
            <span><i class="fa-solid fa-phone"></i> ${person.phone}</span>
            ${person.distance ? `<span><i class="fa-solid fa-location-dot"></i> ${person.distance.toFixed(1)} km away</span>` : ''}
          </div>
          <div class="labour-skills">
            <span class="skill-tag">Available</span>
          </div>
        </div>
        <div class="labour-actions">
          <button class="contact-btn" onclick="event.stopPropagation(); contactLabour('${person.phone}')">
            <i class="fa-solid fa-phone"></i> Contact
          </button>
          ${distanceBadge}
        </div>
      </div>
    `;
  }).join('');
}

function showError(message) {
  document.getElementById('labourList').innerHTML = `
    <div class="location-error">
      <i class="fa-solid fa-triangle-exclamation"></i>
      <div>${message}</div>
    </div>
  `;
}

function viewLabour(id) {
  // Navigate to labour profile
  window.location.href = 'labour-profile.php?id=' + id;
}

function contactLabour(phone) {
  window.location.href = 'tel:' + phone;
}
</script>
</body>
</html>
