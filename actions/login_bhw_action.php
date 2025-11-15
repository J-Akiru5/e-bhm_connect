<?php
// actions/login_bhw_action.php
// Handle BHW login logic securely using PDO prepared statements.
session_start();

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
	header('Location: ../login-bhw');
	exit();
}

require_once __DIR__ . '/../config/database.php';

$username = isset($_POST['username']) ? trim($_POST['username']) : '';
$password = isset($_POST['password']) ? $_POST['password'] : '';

if ($username === '' || $password === '') {
	$_SESSION['login_error'] = 'Please provide username and password.';
	header('Location: ../login-bhw');
	exit();
}

try {
	$stmt = $pdo->prepare('SELECT bhw_id, full_name, username, password_hash FROM bhw_users WHERE username = :username LIMIT 1');
	$stmt->execute([':username' => $username]);
	$user = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (Throwable $e) {
	error_log('Login query error: ' . $e->getMessage());
	$_SESSION['login_error'] = 'An unexpected error occurred. Please try again later.';
	header('Location: ../login-bhw');
	exit();
}

if ($user && isset($user['password_hash']) && password_verify($password, $user['password_hash'])) {
	// Successful login
	$_SESSION['bhw_id'] = $user['bhw_id'];
	// Store full name under bhw_full_name for admin header
	$_SESSION['bhw_full_name'] = $user['full_name'];
	// Optionally mark as logged in flag
	$_SESSION['bhw_logged_in'] = true;

	header('Location: ../admin-dashboard');
	exit();
} else {
	// Failed login
	$_SESSION['login_error'] = 'Invalid username or password.';
	header('Location: ../login-bhw');
	exit();
}
