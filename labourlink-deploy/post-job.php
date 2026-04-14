<?php
require_once 'config.php';
if (!isset($_SESSION['user_id'])) { header('Location: index.php'); exit; }
if ($_SESSION['role'] !== 'farmer') { header('Location: dashboard.php'); exit; }

$username = htmlspecialchars($_SESSION['username'] ?? 'User');
$initials = strtoupper(substr($username, 0, 2));
$navGradient = 'linear-gradient(135deg,#6c63ff,#3b82f6)';

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $stmt = $pdo->prepare("
            INSERT INTO jobs (
                farmer_id, job_title, work_type, crop_type, field_size, field_size_unit,
                labours_required, wage_amount, wage_type, work_duration, duration_unit,
                start_date, end_date, work_hours_start, work_hours_end,
                location_address, latitude, longitude, description, requirements, facilities
            ) VALUES (
                :farmer_id, :job_title, :work_type, :crop_type, :field_size, :field_size_unit,
                :labours_required, :wage_amount, :wage_type, :work_duration, :duration_unit,
                :start_date, :end_date, :work_hours_start, :work_hours_end,
                :location_address, :latitude, :longitude, :description, :requirements, :facilities
            )
        ");
        
        $stmt->execute([
            'farmer_id' => $_SESSION['user_id'],
            'job_title' => $_POST['job_title'],
            'work_type' => $_POST['work_type'],
            'crop_type' => $_POST['crop_type'] ?: null,
            'field_size' => $_POST['field_size'] ?: null,
            'field_size_unit' => $_POST['field_size_unit'],
            'labours_required' => $_POST['labours_required'],
            'wage_amount' => $_POST['wage_amount'],
            'wage_type' => $_POST['wage_type'],
            'work_duration' => $_POST['work_duration'] ?: null,
            'duration_unit' => $_POST['duration_unit'],
            'start_date' => $_POST['start_date'],
            'end_date' => $_POST['end_date'] ?: null,
            'work_hours_start' => $_POST['work_hours_start'] ?: null,
            'work_hours_end' => $_POST['work_hours_end'] ?: null,
            'location_address' => $_POST['location_address'],
            'latitude' => $_POST['latitude'] ?: null,
            'longitude' => $_POST['longitude'] ?: null,
            'description' => $_POST['description'] ?: null,
            'requirements' => $_POST['requirements'] ?: null,
            'facilities' => $_POST['facilities'] ?: null
        ]);
        
        $success = 'Job posted successfully!';
        header('Location: dashboard.php?job_posted=1');
        exit;
        
    } catch (PDOException $e) {
        $error = 'Failed to post job: ' . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
<title>Post a Job – LabourLink</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="stylesheet" href="assets/style.css">
<link rel="stylesheet" href="assets/dashboard.css">
<style>
.form-container {
  padding: 20px 18px 30px;
  background: #f4f4fb;
}

.form-section {
  background: #fff;
  border-radius: 16px;
  padding: 18px;
  margin-bottom: 16px;
  box-shadow: 0 2px 10px rgba(0,0,0,0.05);
}

.section-title {
  font-size: 0.9rem;
  font-weight: 700;
  color: #2d2d3a;
  margin-bottom: 14px;
  display: flex;
  align-items: center;
  gap: 8px;
}

.section-title i {
  color: #6c63ff;
  font-size: 1rem;
}

.form-group {
  margin-bottom: 14px;
}

.form-label {
  font-size: 0.8rem;
  font-weight: 600;
  color: #555;
  margin-bottom: 6px;
  display: block;
}

.form-label .required {
  color: #ef4444;
  margin-left: 2px;
}

.form-input,
.form-select,
.form-textarea {
  width: 100%;
  padding: 10px 12px;
  border: 1.5px solid #e0e0e8;
  border-radius: 10px;
  font-size: 0.85rem;
  color: #2d2d3a;
  background: #fff;
  transition: border-color 0.2s;
}

.form-input:focus,
.form-select:focus,
.form-textarea:focus {
  outline: none;
  border-color: #6c63ff;
}

.form-textarea {
  resize: vertical;
  min-height: 80px;
  font-family: inherit;
}

.form-row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 10px;
}

.form-row-3 {
  display: grid;
  grid-template-columns: 2fr 1fr;
  gap: 10px;
}

.submit-btn {
  width: 100%;
  background: linear-gradient(135deg, #6c63ff, #3b82f6);
  color: #fff;
  border: none;
  border-radius: 12px;
  padding: 14px;
  font-size: 0.95rem;
  font-weight: 700;
  cursor: pointer;
  transition: transform 0.2s, box-shadow 0.2s;
  margin-top: 10px;
}

.submit-btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 20px rgba(108, 99, 255, 0.3);
}

.submit-btn:disabled {
  opacity: 0.6;
  cursor: not-allowed;
  transform: none;
}

.alert {
  padding: 12px 14px;
  border-radius: 10px;
  font-size: 0.85rem;
  margin-bottom: 16px;
}

.alert-success {
  background: #f0fdf4;
  color: #10b981;
  border: 1px solid #d1fae5;
}

.alert-error {
  background: #fff5f5;
  color: #ef4444;
  border: 1px solid #fecaca;
}

.location-btn {
  background: #f5f4ff;
  color: #6c63ff;
  border: 1.5px solid #e0dfff;
  border-radius: 10px;
  padding: 10px 14px;
  font-size: 0.8rem;
  font-weight: 600;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 6px;
  transition: all 0.2s;
  margin-top: 8px;
}

.location-btn:hover {
  background: #6c63ff;
  color: #fff;
}

.location-btn i {
  font-size: 0.9rem;
}

.help-text {
  font-size: 0.72rem;
  color: #999;
  margin-top: 4px;
}
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
      <i class="fa-solid fa-plus-circle"></i>
      <span>Post a Job</span>
    </div>
    <div class="dash-nav-right"></div>
  </nav>

  <!-- ── SCROLLABLE CONTENT ── -->
  <div class="scrollable-content">
    <form method="POST" class="form-container" id="jobForm">
      
      <?php if ($success): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
      <?php endif; ?>
      
      <?php if ($error): ?>
        <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>

      <!-- Basic Information -->
      <div class="form-section">
        <div class="section-title">
          <i class="fa-solid fa-circle-info"></i>
          Basic Information
        </div>
        
        <div class="form-group">
          <label class="form-label">Job Title <span class="required">*</span></label>
          <input type="text" name="job_title" class="form-input" placeholder="e.g., Wheat Harvesting Workers Needed" required>
        </div>

        <div class="form-group">
          <label class="form-label">Type of Work <span class="required">*</span></label>
          <select name="work_type" class="form-select" required>
            <option value="">Select work type</option>
            <option value="planting">Planting/Sowing</option>
            <option value="harvesting">Harvesting</option>
            <option value="irrigation">Irrigation</option>
            <option value="weeding">Weeding</option>
            <option value="pesticide">Pesticide Application</option>
            <option value="ploughing">Ploughing/Tilling</option>
            <option value="other">Other</option>
          </select>
        </div>

        <div class="form-group">
          <label class="form-label">Crop Type</label>
          <input type="text" name="crop_type" class="form-input" placeholder="e.g., Wheat, Rice, Cotton">
          <div class="help-text">Optional: Specify the crop if applicable</div>
        </div>
      </div>

      <!-- Field & Labour Details -->
      <div class="form-section">
        <div class="section-title">
          <i class="fa-solid fa-users"></i>
          Field & Labour Requirements
        </div>

        <div class="form-row-3">
          <div class="form-group">
            <label class="form-label">Field Size</label>
            <input type="number" name="field_size" class="form-input" placeholder="e.g., 5" step="0.01" min="0">
          </div>
          <div class="form-group">
            <label class="form-label">Unit</label>
            <select name="field_size_unit" class="form-select">
              <option value="acres">Acres</option>
              <option value="hectares">Hectares</option>
              <option value="bigha">Bigha</option>
            </select>
          </div>
        </div>

        <div class="form-group">
          <label class="form-label">Number of Labours Required <span class="required">*</span></label>
          <input type="number" name="labours_required" class="form-input" placeholder="e.g., 10" min="1" required>
        </div>
      </div>

      <!-- Wages & Duration -->
      <div class="form-section">
        <div class="section-title">
          <i class="fa-solid fa-indian-rupee-sign"></i>
          Wages & Duration
        </div>

        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Wage Amount <span class="required">*</span></label>
            <input type="number" name="wage_amount" class="form-input" placeholder="₹ 500" min="0" step="0.01" required>
          </div>
          <div class="form-group">
            <label class="form-label">Wage Type <span class="required">*</span></label>
            <select name="wage_type" class="form-select" required>
              <option value="per_day">Per Day</option>
              <option value="per_hour">Per Hour</option>
              <option value="fixed">Fixed</option>
            </select>
          </div>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Work Duration</label>
            <input type="number" name="work_duration" class="form-input" placeholder="e.g., 5" min="1">
          </div>
          <div class="form-group">
            <label class="form-label">Duration Unit</label>
            <select name="duration_unit" class="form-select">
              <option value="days">Days</option>
              <option value="hours">Hours</option>
              <option value="weeks">Weeks</option>
            </select>
          </div>
        </div>
      </div>

      <!-- Schedule -->
      <div class="form-section">
        <div class="section-title">
          <i class="fa-solid fa-calendar-days"></i>
          Schedule
        </div>

        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Start Date <span class="required">*</span></label>
            <input type="date" name="start_date" class="form-input" required>
          </div>
          <div class="form-group">
            <label class="form-label">End Date</label>
            <input type="date" name="end_date" class="form-input">
          </div>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Work Hours Start</label>
            <input type="time" name="work_hours_start" class="form-input">
          </div>
          <div class="form-group">
            <label class="form-label">Work Hours End</label>
            <input type="time" name="work_hours_end" class="form-input">
          </div>
        </div>
      </div>

      <!-- Location -->
      <div class="form-section">
        <div class="section-title">
          <i class="fa-solid fa-location-dot"></i>
          Location
        </div>

        <div class="form-group">
          <label class="form-label">Work Location Address <span class="required">*</span></label>
          <textarea name="location_address" class="form-textarea" placeholder="Enter complete address with landmarks" required></textarea>
        </div>

        <button type="button" class="location-btn" onclick="getLocation()">
          <i class="fa-solid fa-location-crosshairs"></i>
          Use My Current Location
        </button>

        <input type="hidden" name="latitude" id="latitude">
        <input type="hidden" name="longitude" id="longitude">
      </div>

      <!-- Additional Details -->
      <div class="form-section">
        <div class="section-title">
          <i class="fa-solid fa-file-lines"></i>
          Additional Details
        </div>

        <div class="form-group">
          <label class="form-label">Job Description</label>
          <textarea name="description" class="form-textarea" placeholder="Describe the work in detail..."></textarea>
        </div>

        <div class="form-group">
          <label class="form-label">Requirements</label>
          <textarea name="requirements" class="form-textarea" placeholder="Any specific skills or experience needed..."></textarea>
        </div>

        <div class="form-group">
          <label class="form-label">Facilities Provided</label>
          <textarea name="facilities" class="form-textarea" placeholder="e.g., Food, Transportation, Accommodation..."></textarea>
        </div>
      </div>

      <button type="submit" class="submit-btn">
        <i class="fa-solid fa-check"></i> Post Job
      </button>

    </form>
  </div>

  <!-- ── BOTTOM NAV ── -->
  <nav class="bottom-nav">
    <a href="dashboard.php" class="bottom-nav-item">
      <i class="fa-solid fa-house"></i>
      <span>Home</span>
    </a>
    <a href="#" class="bottom-nav-item active">
      <i class="fa-solid fa-briefcase"></i>
      <span>My Jobs</span>
    </a>
    <a href="find-labour.php" class="bottom-nav-item">
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
// Set minimum date to today
document.querySelector('input[name="start_date"]').min = new Date().toISOString().split('T')[0];

function getLocation() {
  if (!navigator.geolocation) {
    alert('Geolocation is not supported by your browser');
    return;
  }

  const btn = event.target.closest('.location-btn');
  const originalHTML = btn.innerHTML;
  btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Getting location...';
  btn.disabled = true;

  navigator.geolocation.getCurrentPosition(
    position => {
      document.getElementById('latitude').value = position.coords.latitude;
      document.getElementById('longitude').value = position.coords.longitude;
      btn.innerHTML = '<i class="fa-solid fa-check"></i> Location Captured';
      btn.style.background = '#10b981';
      btn.style.color = '#fff';
      btn.style.borderColor = '#10b981';
      
      setTimeout(() => {
        btn.innerHTML = originalHTML;
        btn.disabled = false;
        btn.style.background = '';
        btn.style.color = '';
        btn.style.borderColor = '';
      }, 2000);
    },
    error => {
      alert('Unable to get your location. Please enter address manually.');
      btn.innerHTML = originalHTML;
      btn.disabled = false;
    }
  );
}

// Form validation
document.getElementById('jobForm').addEventListener('submit', function(e) {
  const startDate = new Date(document.querySelector('input[name="start_date"]').value);
  const endDate = document.querySelector('input[name="end_date"]').value;
  
  if (endDate) {
    const endDateObj = new Date(endDate);
    if (endDateObj < startDate) {
      e.preventDefault();
      alert('End date cannot be before start date');
      return false;
    }
  }
  
  const startTime = document.querySelector('input[name="work_hours_start"]').value;
  const endTime = document.querySelector('input[name="work_hours_end"]').value;
  
  if (startTime && endTime && endTime <= startTime) {
    e.preventDefault();
    alert('End time must be after start time');
    return false;
  }
});
</script>
</body>
</html>
