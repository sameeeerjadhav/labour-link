<?php
require_once 'config.php';
if (!isset($_SESSION['user_id'])) { header('Location: index.php'); exit; }
if (isset($_GET['logout']))       { session_destroy(); header('Location: index.php'); exit; }

// Fetch full user from DB
$stmt = $conn->prepare("SELECT full_name, email, phone, username, role, created_at FROM users WHERE id=?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$user) { session_destroy(); header('Location: index.php'); exit; }

$username  = htmlspecialchars($user['username']);
$fullName  = htmlspecialchars($user['full_name']);
$email     = htmlspecialchars($user['email']);
$phone     = htmlspecialchars($user['phone']);
$role      = $user['role'];
$joinedAt  = date('d M Y', strtotime($user['created_at']));
$isFarmer  = $role === 'farmer';
$initials  = strtoupper(substr($fullName, 0, 2));
$navGradient = $isFarmer
    ? 'linear-gradient(135deg,#6c63ff,#3b82f6)'
    : 'linear-gradient(135deg,#10b981,#059669)';
$accentColor = $isFarmer ? '#6c63ff' : '#10b981';

// Handle profile update
$successMsg = '';
$errorMsg   = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $newName  = trim($_POST['full_name'] ?? '');
    $newPhone = trim($_POST['phone'] ?? '');

    if (!$newName || !$newPhone) {
        $errorMsg = 'Name and phone cannot be empty.';
    } else {
        $stmt = $conn->prepare("UPDATE users SET full_name=?, phone=? WHERE id=?");
        $stmt->bind_param("ssi", $newName, $newPhone, $_SESSION['user_id']);
        if ($stmt->execute()) {
            $successMsg = 'Profile updated successfully.';
            $fullName   = htmlspecialchars($newName);
            $phone      = htmlspecialchars($newPhone);
            $initials   = strtoupper(substr($fullName, 0, 2));
        } else {
            $errorMsg = 'Update failed. Please try again.';
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
<title>LabourLink – My Profile</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="stylesheet" href="assets/style.css">
<link rel="stylesheet" href="assets/dashboard.css">
<link rel="stylesheet" href="assets/profile.css">
</head>
<body class="<?= $isFarmer ? '' : 'labour-theme' ?>">
<div class="phone-shell">

  <!-- TOP NAVBAR -->
  <nav class="dash-nav" style="background:<?= $navGradient ?>">
    <a href="dashboard.php" class="nav-back-btn">
      <i class="fa-solid fa-arrow-left"></i>
    </a>
    <div class="dash-nav-brand">
      <i class="fa-solid fa-seedling"></i>
      <span>My Profile</span>
    </div>
    <a href="dashboard.php?logout=1" class="nav-icon-btn" title="Logout" style="text-decoration:none;">
      <i class="fa-solid fa-right-from-bracket"></i>
    </a>
  </nav>

  <div class="dash-body" id="top">

    <!-- Profile Card -->
    <div class="profile-card" style="--accent:<?= $accentColor ?>">
      <div class="profile-avatar-lg"><?= $initials ?></div>
      <div class="profile-card-name"><?= $fullName ?></div>
      <div class="profile-card-username">@<?= $username ?></div>
      <div class="profile-role-badge" style="background:<?= $accentColor ?>22; color:<?= $accentColor ?>; border:1.5px solid <?= $accentColor ?>">
        <i class="fa-solid <?= $isFarmer ? 'fa-tractor' : 'fa-person-digging' ?>"></i>
        <?= ucfirst($role) ?>
      </div>
      <div class="profile-joined">Member since <?= $joinedAt ?></div>
    </div>

    <!-- Alerts -->
    <?php if ($successMsg): ?>
    <div class="alert-custom alert-success show" style="margin-bottom:14px;">
      <i class="fa-solid fa-circle-check"></i> <?= $successMsg ?>
    </div>
    <?php endif; ?>
    <?php if ($errorMsg): ?>
    <div class="alert-custom alert-error show" style="margin-bottom:14px;">
      <i class="fa-solid fa-circle-exclamation"></i> <?= $errorMsg ?>
    </div>
    <?php endif; ?>

    <!-- Info Section -->
    <p class="dash-section-title">Account Info</p>
    <div class="info-list">
      <div class="info-row">
        <div class="info-icon" style="background:<?= $accentColor ?>22; color:<?= $accentColor ?>">
          <i class="fa-solid fa-envelope"></i>
        </div>
        <div class="info-content">
          <span class="info-label">Email</span>
          <span class="info-value"><?= $email ?></span>
        </div>
      </div>
      <div class="info-row">
        <div class="info-icon" style="background:<?= $accentColor ?>22; color:<?= $accentColor ?>">
          <i class="fa-solid fa-phone"></i>
        </div>
        <div class="info-content">
          <span class="info-label">Phone</span>
          <span class="info-value"><?= $phone ?></span>
        </div>
      </div>
      <div class="info-row">
        <div class="info-icon" style="background:<?= $accentColor ?>22; color:<?= $accentColor ?>">
          <i class="fa-solid fa-at"></i>
        </div>
        <div class="info-content">
          <span class="info-label">Username</span>
          <span class="info-value"><?= $username ?></span>
        </div>
      </div>
    </div>

    <!-- Edit Profile Form -->
    <p class="dash-section-title" style="margin-top:20px;" id="settings">Edit Profile</p>
    <div class="edit-card">
      <form method="POST">
        <div class="input-group-custom">
          <i class="fa-solid fa-id-card input-icon"></i>
          <input type="text" name="full_name" value="<?= $fullName ?>" placeholder="Full Name" required>
        </div>
        <div class="input-group-custom">
          <i class="fa-solid fa-phone input-icon"></i>
          <input type="tel" name="phone" value="<?= $phone ?>" placeholder="Phone Number" required>
        </div>
        <button type="submit" name="update_profile" class="btn-primary-custom"
                style="background:<?= $navGradient ?>">
          <i class="fa-solid fa-floppy-disk me-2"></i> Save Changes
        </button>
      </form>
    </div>

    <!-- Logout Button -->
    <a href="dashboard.php?logout=1" class="logout-full-btn">
      <i class="fa-solid fa-right-from-bracket"></i> Logout
    </a>

  </div>

  <!-- ── BOTTOM NAV ── -->
  <nav class="bottom-nav">
    <a href="dashboard.php" class="bottom-nav-item" id="bnHome">
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
    <a href="profile.php" class="bottom-nav-item active" id="bnProfile">
      <i class="fa-solid fa-user"></i>
      <span>Profile</span>
    </a>
  </nav>

</div>

<script>
<?php if (($_GET['tab'] ?? '') === 'settings'): ?>
window.addEventListener('load', () => {
  document.getElementById('settings').scrollIntoView({ behavior: 'smooth' });
});
<?php endif; ?>
</script>
</body>
</html>
