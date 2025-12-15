<?php
// actions/login_patient_action.php
// Handle patient login for portal
// Ensure session is started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include required configuration files
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';


if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . BASE_URL . 'login-patient');
    exit();
}

$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$password = isset($_POST['password']) ? $_POST['password'] : '';

if ($email === '' || $password === '') {
    $_SESSION['login_error'] = 'Please provide email and password.';
    header('Location: ' . BASE_URL . 'login-patient');
    exit();
}

try {
    $stmt = $pdo->prepare('SELECT user_id, patient_id, email, password_hash FROM patient_users WHERE email = :email LIMIT 1');
    $stmt->execute([':email' => $email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && isset($user['password_hash']) && password_verify($password, $user['password_hash'])) {
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

