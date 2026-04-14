<?php require_once 'config.php';
if (isset($_SESSION['user_id'])) header('Location: dashboard.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
<meta name="mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-capable" content="yes">
<title>LabourLink – Login</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="assets/style.css">
</head>
<body class="farmer-theme">

<div class="phone-shell">

  <!-- Header -->
  <div class="app-header">
    <div class="logo-icon">
      <i class="fa-solid fa-seedling text-white" style="font-size:1.8rem;"></i>
    </div>
    <h1>LabourLink</h1>
    <p>Sign in to continue</p>
  </div>

  <!-- Form Area -->
  <div class="form-area">

    <p class="section-title">Login</p>
    <p class="section-sub">Select your role and enter credentials</p>

    <!-- Alert -->
    <div class="alert-custom alert-error" id="loginAlert">
      <i class="fa-solid fa-circle-exclamation"></i>
      <span id="loginAlertMsg"></span>
    </div>

    <form id="loginForm" novalidate>

      <!-- Role Selector -->
      <div class="role-selector">
        <label class="role-card active" id="roleCardFarmer">
          <input type="radio" name="role" value="farmer" checked hidden>
          <i class="fa-solid fa-tractor"></i>
          <span>Farmer</span>
        </label>
        <label class="role-card" id="roleCardLabour">
          <input type="radio" name="role" value="labour" hidden>
          <i class="fa-solid fa-person-digging"></i>
          <span>Labour</span>
        </label>
      </div>

      <!-- Username -->
      <div class="input-group-custom">
        <i class="fa-solid fa-user input-icon"></i>
        <input type="text" id="loginUsername" name="username" placeholder="Username" autocomplete="username" required>
      </div>

      <!-- Password -->
      <div class="input-group-custom">
        <i class="fa-solid fa-lock input-icon"></i>
        <input type="password" id="loginPassword" name="password" placeholder="Password" autocomplete="current-password" required>
        <button type="button" class="toggle-pass" onclick="togglePass('loginPassword', this)">
          <i class="fa-solid fa-eye"></i>
        </button>
      </div>

      <div class="text-end mb-3">
        <a href="#" class="forgot-link" style="font-size:0.82rem; text-decoration:none;">Forgot password?</a>
      </div>

      <button type="submit" class="btn-primary-custom" id="loginBtn">
        <span id="loginBtnText">Sign In</span>
        <span id="loginSpinner" class="d-none">
          <i class="fa-solid fa-spinner fa-spin me-2"></i>Signing in...
        </span>
      </button>

    </form>

    <div class="divider">or</div>

    <div class="switch-link">
      Don't have an account? <a href="register.php">Create one</a>
    </div>

  </div>
</div>

<script>
// Role card toggle with theme switching
document.querySelectorAll('.role-card').forEach(card => {
  card.addEventListener('click', () => {
    document.querySelectorAll('.role-card').forEach(c => c.classList.remove('active'));
    card.classList.add('active');
    card.querySelector('input[type=radio]').checked = true;
    
    // Switch theme based on role
    const role = card.querySelector('input[type=radio]').value;
    document.body.className = role === 'farmer' ? 'farmer-theme' : 'labour-theme';
  });
});

function togglePass(id, btn) {
  const input = document.getElementById(id);
  const icon  = btn.querySelector('i');
  if (input.type === 'password') {
    input.type = 'text';
    icon.classList.replace('fa-eye', 'fa-eye-slash');
  } else {
    input.type = 'password';
    icon.classList.replace('fa-eye-slash', 'fa-eye');
  }
}

function showAlert(id, msgId, msg, type = 'error') {
  const el = document.getElementById(id);
  el.className = `alert-custom alert-${type} show`;
  document.getElementById(msgId).textContent = msg;
}

document.getElementById('loginForm').addEventListener('submit', async function(e) {
  e.preventDefault();
  const btn     = document.getElementById('loginBtn');
  const btnText = document.getElementById('loginBtnText');
  const spinner = document.getElementById('loginSpinner');

  btnText.classList.add('d-none');
  spinner.classList.remove('d-none');
  btn.classList.add('loading');

  const data = new FormData(this);
  data.append('action', 'login');

  try {
    const res  = await fetch('auth.php', { method: 'POST', body: data });
    const json = await res.json();

    if (json.status === 'success') {
      showAlert('loginAlert', 'loginAlertMsg', 'Login successful! Redirecting...', 'success');
      setTimeout(() => window.location.href = 'dashboard.php', 1000);
    } else if (json.status === 'verify') {
      sessionStorage.setItem('otp_dev', json.otp_dev || '');
      window.location.href = 'verify.php';
    } else {
      showAlert('loginAlert', 'loginAlertMsg', json.message);
    }
  } catch {
    showAlert('loginAlert', 'loginAlertMsg', 'Network error. Please try again.');
  } finally {
    btnText.classList.remove('d-none');
    spinner.classList.add('d-none');
    btn.classList.remove('loading');
  }
});
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
