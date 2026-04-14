<?php require_once 'config.php';
if (isset($_SESSION['user_id'])) header('Location: dashboard.php');
if (!isset($_SESSION['pending_user'])) header('Location: index.php');
$phone = $_SESSION['pending_phone'] ?? '';
$masked = preg_replace('/(\+?\d{1,3})\d+(\d{4})/', '$1****$2', $phone);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
<meta name="mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-capable" content="yes">
<title>Verify Phone</title>
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
      <i class="fa-solid fa-mobile-screen-button text-white" style="font-size:1.8rem;"></i>
    </div>
    <h1>Verify Phone</h1>
    <p>We sent a code to <?= htmlspecialchars($masked) ?></p>
  </div>

  <!-- Form Area -->
  <div class="form-area" style="text-align:center;">

    <p class="section-title">Enter OTP</p>
    <p class="section-sub">6-digit code sent to your phone number</p>

    <!-- Dev OTP Badge (remove in production) -->
    <div class="dev-otp-badge" id="devBadge">
      <i class="fa-solid fa-flask me-1"></i> Dev mode — your OTP:
      <br><strong id="devOtpCode">------</strong>
    </div>

    <!-- Alert -->
    <div class="alert-custom" id="otpAlert">
      <i class="fa-solid fa-circle-exclamation"></i>
      <span id="otpAlertMsg"></span>
    </div>

    <form id="otpForm" novalidate>

      <div class="otp-inputs" id="otpInputs">
        <input type="text" maxlength="1" inputmode="numeric" pattern="[0-9]" aria-label="OTP digit 1">
        <input type="text" maxlength="1" inputmode="numeric" pattern="[0-9]" aria-label="OTP digit 2">
        <input type="text" maxlength="1" inputmode="numeric" pattern="[0-9]" aria-label="OTP digit 3">
        <input type="text" maxlength="1" inputmode="numeric" pattern="[0-9]" aria-label="OTP digit 4">
        <input type="text" maxlength="1" inputmode="numeric" pattern="[0-9]" aria-label="OTP digit 5">
        <input type="text" maxlength="1" inputmode="numeric" pattern="[0-9]" aria-label="OTP digit 6">
      </div>

      <!-- Countdown -->
      <p style="font-size:0.82rem; color:#999; margin-bottom:16px;">
        Code expires in <span id="countdown" style="color:#6c63ff; font-weight:600;">10:00</span>
      </p>

      <button type="submit" class="btn-primary-custom" id="otpBtn">
        <span id="otpBtnText">Verify Code</span>
        <span id="otpSpinner" class="d-none">
          <i class="fa-solid fa-spinner fa-spin me-2"></i>Verifying...
        </span>
      </button>

    </form>

    <div class="divider">or</div>

    <div class="switch-link">
      <a href="index.php"><i class="fa-solid fa-arrow-left me-1"></i>Back to Login</a>
    </div>

  </div>
</div>

<script>
// ── Dev OTP display ──────────────────────────────────
const devOtp = sessionStorage.getItem('otp_dev');
if (devOtp) {
  document.getElementById('devBadge').classList.add('show');
  document.getElementById('devOtpCode').textContent = devOtp;
}

// ── OTP input auto-advance ───────────────────────────
const inputs = document.querySelectorAll('#otpInputs input');
inputs.forEach((inp, i) => {
  inp.addEventListener('input', () => {
    inp.value = inp.value.replace(/\D/g, '');
    if (inp.value && i < inputs.length - 1) inputs[i + 1].focus();
  });
  inp.addEventListener('keydown', (e) => {
    if (e.key === 'Backspace' && !inp.value && i > 0) inputs[i - 1].focus();
  });
  inp.addEventListener('paste', (e) => {
    e.preventDefault();
    const pasted = e.clipboardData.getData('text').replace(/\D/g, '').slice(0, 6);
    [...pasted].forEach((ch, idx) => { if (inputs[idx]) inputs[idx].value = ch; });
    const next = Math.min(pasted.length, inputs.length - 1);
    inputs[next].focus();
  });
});

// ── Countdown timer ──────────────────────────────────
let seconds = 600;
const tick = setInterval(() => {
  seconds--;
  const m = String(Math.floor(seconds / 60)).padStart(2, '0');
  const s = String(seconds % 60).padStart(2, '0');
  document.getElementById('countdown').textContent = `${m}:${s}`;
  if (seconds <= 0) {
    clearInterval(tick);
    document.getElementById('countdown').textContent = 'Expired';
    document.getElementById('otpBtn').disabled = true;
  }
}, 1000);

// ── Alert helper ─────────────────────────────────────
function showAlert(msg, type = 'error') {
  const el = document.getElementById('otpAlert');
  el.className = `alert-custom alert-${type} show`;
  document.getElementById('otpAlertMsg').textContent = msg;
}

// ── Submit ────────────────────────────────────────────
document.getElementById('otpForm').addEventListener('submit', async function(e) {
  e.preventDefault();
  const otp = [...inputs].map(i => i.value).join('');
  if (otp.length < 6) { showAlert('Please enter all 6 digits.'); return; }

  const btn     = document.getElementById('otpBtn');
  const btnText = document.getElementById('otpBtnText');
  const spinner = document.getElementById('otpSpinner');

  btnText.classList.add('d-none');
  spinner.classList.remove('d-none');
  btn.classList.add('loading');

  const data = new FormData();
  data.append('action', 'verify_otp');
  data.append('otp', otp);

  try {
    const res  = await fetch('auth.php', { method: 'POST', body: data });
    const json = await res.json();

    if (json.status === 'success') {
      clearInterval(tick);
      sessionStorage.removeItem('otp_dev');
      showAlert('Verified! Redirecting...', 'success');
      setTimeout(() => window.location.href = 'dashboard.php', 1000);
    } else {
      showAlert(json.message);
      inputs.forEach(i => { i.value = ''; });
      inputs[0].focus();
    }
  } catch {
    showAlert('Network error. Please try again.');
  } finally {
    btnText.classList.remove('d-none');
    spinner.classList.add('d-none');
    btn.classList.remove('loading');
  }
});
</script>
</body>
</html>
