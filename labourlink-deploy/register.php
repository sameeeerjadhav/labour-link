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
<title>LabourLink – Register</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="assets/style.css">
</head>
<body>

<div class="phone-shell">

  <!-- Header -->
  <div class="app-header">
    <div class="logo-icon">
      <i class="fa-solid fa-seedling text-white" style="font-size:1.8rem;"></i>
    </div>
    <h1>LabourLink</h1>
    <p>Create your account</p>
  </div>

  <!-- Form Area -->
  <div class="form-area">

    <p class="section-title">Register</p>
    <p class="section-sub">Choose your role and fill in your details</p>

    <!-- Alert -->
    <div class="alert-custom alert-error" id="regAlert">
      <i class="fa-solid fa-circle-exclamation"></i>
      <span id="regAlertMsg"></span>
    </div>

    <form id="regForm" novalidate>

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

      <!-- Full Name -->
      <div class="input-group-custom">
        <i class="fa-solid fa-id-card input-icon"></i>
        <input type="text" name="full_name" placeholder="Full Name" autocomplete="name" required>
      </div>

      <!-- Email -->
      <div class="input-group-custom">
        <i class="fa-solid fa-envelope input-icon"></i>
        <input type="email" name="email" placeholder="Email Address" autocomplete="email" required>
      </div>

      <!-- Phone -->
      <div class="input-group-custom">
        <i class="fa-solid fa-phone input-icon"></i>
        <input type="tel" name="phone" placeholder="Phone Number" autocomplete="tel" required>
      </div>

      <!-- Username -->
      <div class="input-group-custom">
        <i class="fa-solid fa-at input-icon"></i>
        <input type="text" name="username" placeholder="Username" autocomplete="username" required>
      </div>

      <!-- Password -->
      <div class="input-group-custom">
        <i class="fa-solid fa-lock input-icon"></i>
        <input type="password" id="regPassword" name="password" placeholder="Password" autocomplete="new-password"
               oninput="checkStrength(this.value)" required>
        <button type="button" class="toggle-pass" onclick="togglePass('regPassword', this)">
          <i class="fa-solid fa-eye"></i>
        </button>
      </div>

      <!-- Strength Bar -->
      <div class="strength-bar">
        <div class="strength-fill" id="strengthFill"></div>
      </div>

      <button type="submit" class="btn-primary-custom" id="regBtn">
        <span id="regBtnText">Create Account</span>
        <span id="regSpinner" class="d-none">
          <i class="fa-solid fa-spinner fa-spin me-2"></i>Creating...
        </span>
      </button>

    </form>

    <div class="divider">or</div>

    <div class="switch-link">
      Already have an account? <a href="index.php">Sign in</a>
    </div>

  </div>
</div>

<script>
// Role card toggle
document.querySelectorAll('.role-card').forEach(card => {
  card.addEventListener('click', () => {
    document.querySelectorAll('.role-card').forEach(c => c.classList.remove('active'));
    card.classList.add('active');
    card.querySelector('input[type=radio]').checked = true;
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

function checkStrength(val) {
  const fill = document.getElementById('strengthFill');
  let score  = 0;
  if (val.length >= 8)           score++;
  if (/[A-Z]/.test(val))         score++;
  if (/[0-9]/.test(val))         score++;
  if (/[^A-Za-z0-9]/.test(val))  score++;
  const map = ['0%','25%','50%','75%','100%'];
  const col = ['#e8e8f0','#ef4444','#f59e0b','#3b82f6','#10b981'];
  fill.style.width      = map[score];
  fill.style.background = col[score];
}

function showAlert(id, msgId, msg, type = 'error') {
  const el = document.getElementById(id);
  el.className = `alert-custom alert-${type} show`;
  document.getElementById(msgId).textContent = msg;
}

document.getElementById('regForm').addEventListener('submit', async function(e) {
  e.preventDefault();
  const btn     = document.getElementById('regBtn');
  const btnText = document.getElementById('regBtnText');
  const spinner = document.getElementById('regSpinner');

  btnText.classList.add('d-none');
  spinner.classList.remove('d-none');
  btn.classList.add('loading');

  const data = new FormData(this);
  data.append('action', 'register');

  try {
    const res  = await fetch('auth.php', { method: 'POST', body: data });
    const json = await res.json();

    if (json.status === 'success') {
      sessionStorage.setItem('otp_dev', json.otp_dev || '');
      showAlert('regAlert', 'regAlertMsg', 'Account created! Redirecting to verify...', 'success');
      setTimeout(() => window.location.href = 'verify.php', 1200);
    } else {
      showAlert('regAlert', 'regAlertMsg', json.message);
    }
  } catch {
    showAlert('regAlert', 'regAlertMsg', 'Network error. Please try again.');
  } finally {
    btnText.classList.remove('d-none');
    spinner.classList.add('d-none');
    btn.classList.remove('loading');
  }
});
</script>
</body>
</html>
