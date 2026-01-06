<?php
// actions/login_patient_action.php
// Handle patient login for portal
// Includes: CSRF protection, rate limiting, session security

// Ensure session is started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include required configuration files
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/security_helper.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . BASE_URL . 'login-patient');
    exit();
}

// Validate CSRF token
require_csrf();

$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$password = isset($_POST['password']) ? $_POST['password'] : '';

if ($email === '' || $password === '') {
    $_SESSION['login_error'] = 'Please provide email and password.';
    header('Location: ' . BASE_URL . 'login-patient');
    exit();
}

// Check rate limit before attempting login
$clientIp = get_client_ip();
$rateLimitCheck = check_rate_limit('login_patient', $clientIp, 5, 900); // 5 attempts per 15 minutes

if (!$rateLimitCheck['allowed']) {
    $resetMinutes = ceil(($rateLimitCheck['reset_time'] - time()) / 60);
    $_SESSION['login_error'] = "Too many failed login attempts. Please try again in {$resetMinutes} minutes.";
    header('Location: ' . BASE_URL . 'login-patient');
    exit();
}

try {
    $stmt = $pdo->prepare('SELECT user_id, patient_id, email, password_hash, email_verified, status FROM patient_users WHERE email = :email LIMIT 1');
    $stmt->execute([':email' => $email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && isset($user['password_hash']) && password_verify($password, $user['password_hash'])) {
        // Check account status
        $status = $user['status'] ?? 'active';
        if ($status === 'suspended') {
            $_SESSION['login_error'] = 'Your account has been suspended. Please contact your Barangay Health Worker.';
            header('Location: ' . BASE_URL . 'login-patient');
            exit();
        }

        // Clear rate limit on successful login
        clear_rate_limit('login_patient', $clientIp);

        // Regenerate session to prevent session fixation
        regenerate_session();

        // Fetch patient details
        $pstmt = $pdo->prepare('SELECT patient_id, full_name FROM patients WHERE patient_id = :patient_id LIMIT 1');
        $pstmt->execute([':patient_id' => $user['patient_id']]);
        $patient = $pstmt->fetch(PDO::FETCH_ASSOC);

        // Set session values
        $_SESSION['patient_id'] = $user['patient_id'];
        $_SESSION['patient_user_id'] = $user['user_id'];
        $_SESSION['patient_full_name'] = $patient ? $patient['full_name'] : '';
        $_SESSION['patient_logged_in'] = true;

        // Update last_login
        $update = $pdo->prepare('UPDATE patient_users SET last_login = NOW() WHERE user_id = :user_id');
        $update->execute([':user_id' => $user['user_id']]);

        header('Location: ' . BASE_URL . 'portal-dashboard');
        exit();
    } else {
        // Record failed attempt
        record_rate_limit('login_patient', $clientIp, 900);
        $_SESSION['login_error'] = 'Invalid email or password.';
        header('Location: ' . BASE_URL . 'login-patient');
        exit();
    }

} catch (Throwable $e) {
    error_log('Patient login error: ' . $e->getMessage());
    $_SESSION['login_error'] = 'An unexpected error occurred. Please try again later.';
    header('Location: ' . BASE_URL . 'login-patient');
    exit();
}

