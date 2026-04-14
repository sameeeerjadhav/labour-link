<?php
// Suppress any warnings/notices from polluting JSON output
error_reporting(0);
ini_set('display_errors', 0);
header('Content-Type: application/json');

require_once 'config.php';

$action = $_POST['action'] ?? '';

// ── REGISTER ──────────────────────────────────────────────
if ($action === 'register') {
    $full_name = trim($_POST['full_name'] ?? '');
    $email     = trim($_POST['email'] ?? '');
    $phone     = trim($_POST['phone'] ?? '');
    $username  = trim($_POST['username'] ?? '');
    $password  = $_POST['password'] ?? '';
    $role      = $_POST['role'] ?? '';

    if (!$full_name || !$email || !$phone || !$username || !$password || !$role) {
        echo json_encode(['status' => 'error', 'message' => 'All fields are required.']);
        exit;
    }

    if (!in_array($role, ['farmer', 'labour'])) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid role selected.']);
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid email address.']);
        exit;
    }

    $stmt = $conn->prepare("SELECT id FROM users WHERE email=? OR username=?");
    $stmt->bind_param("ss", $email, $username);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        echo json_encode(['status' => 'error', 'message' => 'Email or username already exists.']);
        exit;
    }
    $stmt->close();

    $hashed = password_hash($password, PASSWORD_BCRYPT);
    $otp    = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
    $expiry = date('Y-m-d H:i:s', strtotime('+10 minutes'));

    $stmt = $conn->prepare("INSERT INTO users (full_name, email, phone, username, password, role, otp_code, otp_expires_at) VALUES (?,?,?,?,?,?,?,?)");
    $stmt->bind_param("ssssssss", $full_name, $email, $phone, $username, $hashed, $role, $otp, $expiry);

    if ($stmt->execute()) {
        $_SESSION['pending_user']  = $username;
        $_SESSION['pending_phone'] = $phone;
        echo json_encode(['status' => 'success', 'message' => 'Registration successful.', 'otp_dev' => $otp]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Registration failed. Try again.']);
    }
    $stmt->close();
    exit;
}

// ── LOGIN ─────────────────────────────────────────────────
if ($action === 'login') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $role     = $_POST['role'] ?? '';

    if (!$username || !$password || !$role) {
        echo json_encode(['status' => 'error', 'message' => 'All fields are required.']);
        exit;
    }

    if (!in_array($role, ['farmer', 'labour'])) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid role selected.']);
        exit;
    }

    $stmt = $conn->prepare("SELECT id, username, password, role, is_verified, phone, otp_code, otp_expires_at FROM users WHERE username=?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $user   = $result->fetch_assoc();
    $stmt->close();

    if (!$user || !password_verify($password, $user['password'])) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid username or password.']);
        exit;
    }

    // Role mismatch check
    if ($user['role'] !== $role) {
        echo json_encode(['status' => 'error', 'message' => 'Incorrect role selected for this account.']);
        exit;
    }

    if (!$user['is_verified']) {
        $otp    = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
        $expiry = date('Y-m-d H:i:s', strtotime('+10 minutes'));
        $stmt   = $conn->prepare("UPDATE users SET otp_code=?, otp_expires_at=? WHERE id=?");
        $stmt->bind_param("ssi", $otp, $expiry, $user['id']);
        $stmt->execute();
        $stmt->close();

        $_SESSION['pending_user']  = $username;
        $_SESSION['pending_phone'] = $user['phone'];
        echo json_encode(['status' => 'verify', 'message' => 'OTP sent to your phone.', 'otp_dev' => $otp]);
        exit;
    }

    $_SESSION['user_id']   = $user['id'];
    $_SESSION['username']  = $user['username'];
    $_SESSION['role']      = $user['role'];
    echo json_encode(['status' => 'success', 'message' => 'Login successful.']);
    exit;
}

// ── VERIFY OTP ────────────────────────────────────────────
if ($action === 'verify_otp') {
    $otp      = trim($_POST['otp'] ?? '');
    $username = $_SESSION['pending_user'] ?? '';

    if (!$username) {
        echo json_encode(['status' => 'error', 'message' => 'Session expired. Please login again.']);
        exit;
    }

    $stmt = $conn->prepare("SELECT id, role, otp_code, otp_expires_at FROM users WHERE username=?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $user   = $result->fetch_assoc();
    $stmt->close();

    if (!$user) {
        echo json_encode(['status' => 'error', 'message' => 'User not found.']);
        exit;
    }

    if ($user['otp_code'] !== $otp) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid OTP code.']);
        exit;
    }

    if (strtotime($user['otp_expires_at']) < time()) {
        echo json_encode(['status' => 'error', 'message' => 'OTP has expired. Please login again.']);
        exit;
    }

    $stmt = $conn->prepare("UPDATE users SET is_verified=1, otp_code=NULL, otp_expires_at=NULL WHERE id=?");
    $stmt->bind_param("i", $user['id']);
    $stmt->execute();
    $stmt->close();

    $_SESSION['user_id']  = $user['id'];
    $_SESSION['username'] = $username;
    $_SESSION['role']     = $user['role'];
    unset($_SESSION['pending_user'], $_SESSION['pending_phone']);

    echo json_encode(['status' => 'success', 'message' => 'Verified successfully.']);
    exit;
}

echo json_encode(['status' => 'error', 'message' => 'Invalid action.']);
