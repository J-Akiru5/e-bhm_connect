<?php
// actions/verify_patient_email.php
// Handle patient email verification from registration

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth_helpers.php';

$token = isset($_GET['token']) ? trim($_GET['token']) : '';

if ($token === '') {
    $_SESSION['login_error'] = 'Invalid verification link.';
    header('Location: ' . BASE_URL . 'login-patient');
    exit();
}

try {
    // Find the user with this token
    $stmt = $pdo->prepare('
        SELECT user_id, patient_id, email, verification_expires_at 
        FROM patient_users 
        WHERE verification_token = :token AND email_verified = 0
        LIMIT 1
    ');
    $stmt->execute([':token' => $token]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        $_SESSION['login_error'] = 'Invalid or expired verification link. The link may have already been used.';
        header('Location: ' . BASE_URL . 'login-patient');
        exit();
    }

    // Check if token has expired
    if (strtotime($user['verification_expires_at']) < time()) {
        $_SESSION['login_error'] = 'Verification link has expired. Please register again or contact your BHW.';
        header('Location: ' . BASE_URL . 'login-patient');
        exit();
    }

    // Activate the account
    $update = $pdo->prepare('
        UPDATE patient_users 
        SET email_verified = 1, 
            status = :status,
            verification_token = NULL, 
            verification_expires_at = NULL 
        WHERE user_id = :user_id
    ');
    $update->execute([
        ':status' => 'active',
        ':user_id' => $user['user_id']
    ]);

    // Log the verification
    log_audit('patient_email_verified', 'patient_users', $user['patient_id'], ['email' => $user['email']]);

    $_SESSION['login_success'] = 'Email verified successfully! You can now log in to your portal.';
    header('Location: ' . BASE_URL . 'login-patient');
    exit();

} catch (Throwable $e) {
    error_log('Patient email verification error: ' . $e->getMessage());
    $_SESSION['login_error'] = 'An error occurred during verification. Please try again.';
    header('Location: ' . BASE_URL . 'login-patient');
    exit();
}
