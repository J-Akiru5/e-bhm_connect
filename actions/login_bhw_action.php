<?php
// actions/login_bhw_action.php
// Handle BHW login logic securely using PDO prepared statements.
// Includes: CSRF protection, rate limiting, account verification, session security

// Ensure session is started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include required configuration files
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/security_helper.php';
require_once __DIR__ . '/../includes/auth_helpers.php';

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
	header('Location: ' . BASE_URL . 'login-bhw');
	exit();
}

// Validate CSRF token
require_csrf();

$username = isset($_POST['username']) ? trim($_POST['username']) : '';
$password = isset($_POST['password']) ? $_POST['password'] : '';

if ($username === '' || $password === '') {
	$_SESSION['login_error'] = 'Please provide username and password.';
	header('Location: ' . BASE_URL . 'login-bhw');
	exit();
}

// Check rate limit before attempting login
$clientIp = get_client_ip();
$rateLimitCheck = check_rate_limit('login_bhw', $clientIp, 5, 900); // 5 attempts per 15 minutes

if (!$rateLimitCheck['allowed']) {
	$resetMinutes = ceil(($rateLimitCheck['reset_time'] - time()) / 60);
	$_SESSION['login_error'] = "Too many failed login attempts. Please try again in {$resetMinutes} minutes.";
	header('Location: ' . BASE_URL . 'login-bhw');
	exit();
}

try {
	$stmt = $pdo->prepare('
		SELECT bhw_id, full_name, username, email, password_hash, email_verified, account_status, role 
		FROM bhw_users 
		WHERE username = :username 
		LIMIT 1
	');
	$stmt->execute([':username' => $username]);
	$user = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (Throwable $e) {
	error_log('Login query error: ' . $e->getMessage());
	$_SESSION['login_error'] = 'An unexpected error occurred. Please try again later.';
	header('Location: ' . BASE_URL . 'login-bhw');
	exit();
}

if (!$user) {
	// Record failed attempt
	record_rate_limit('login_bhw', $clientIp, 900);
	log_audit('login_failed', 'bhw', null, ['username' => $username, 'reason' => 'user_not_found']);
	$_SESSION['login_error'] = 'Invalid username or password.';
	header('Location: ' . BASE_URL . 'login-bhw');
	exit();
}

// Verify password first
if (!isset($user['password_hash']) || !password_verify($password, $user['password_hash'])) {
	// Record failed attempt
	record_rate_limit('login_bhw', $clientIp, 900);
	log_audit('login_failed', 'bhw', $user['bhw_id'], ['username' => $username, 'reason' => 'invalid_password']);
	$_SESSION['login_error'] = 'Invalid username or password.';
	header('Location: ' . BASE_URL . 'login-bhw');
	exit();
}

// Check email verification status
if (isset($user['email_verified']) && !$user['email_verified']) {
	$_SESSION['login_error'] = 'Please verify your email address first. Check your inbox for the verification link.';
	header('Location: ' . BASE_URL . 'login-bhw');
	exit();
}

// Check account status - only 'approved' users can login
$accountStatus = $user['account_status'] ?? 'pending';

if ($accountStatus === 'pending') {
	$_SESSION['login_error'] = 'Your account is pending email verification. Please check your inbox for the verification link.';
	header('Location: ' . BASE_URL . 'login-bhw');
	exit();
}

if ($accountStatus === 'verified') {
	$_SESSION['login_error'] = 'Your account is pending approval by the Healthcare Center Head. You will receive an email once your account is approved.';
	header('Location: ' . BASE_URL . 'login-bhw');
	exit();
}

if ($accountStatus !== 'approved') {
	$_SESSION['login_error'] = 'Your account is not active. Please contact the administrator.';
	header('Location: ' . BASE_URL . 'login-bhw');
	exit();
}

// Successful login - clear rate limit and regenerate session
clear_rate_limit('login_bhw', $clientIp);
regenerate_session();

$_SESSION['bhw_id'] = $user['bhw_id'];
$_SESSION['bhw_full_name'] = $user['full_name'];
$_SESSION['bhw_email'] = $user['email'] ?? '';
$_SESSION['bhw_role'] = $user['role'] ?? 'bhw';
$_SESSION['bhw_logged_in'] = true;

// Log successful login
log_audit('login_success', 'bhw', $user['bhw_id'], ['username' => $username]);

header('Location: ' . BASE_URL . 'admin-dashboard');
exit();

