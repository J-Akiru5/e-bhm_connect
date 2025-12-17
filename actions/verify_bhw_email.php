<?php
// actions/verify_bhw_email.php
// Handle BHW email verification

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';

$token = isset($_GET['token']) ? trim($_GET['token']) : '';

if (empty($token)) {
    $_SESSION['login_error'] = 'Invalid verification link.';
    header('Location: ' . BASE_URL . 'login-bhw');
    exit();
}

try {
    // Find user by token and check if not expired
    $stmt = $pdo->prepare('
        SELECT bhw_id, full_name, email, email_verified, account_status, verification_token_expires 
        FROM bhw_users 
        WHERE verification_token = :token 
        LIMIT 1
    ');
    $stmt->execute([':token' => $token]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        $_SESSION['login_error'] = 'Invalid or expired verification link. Please register again or contact the administrator.';
        header('Location: ' . BASE_URL . 'login-bhw');
        exit();
    }

    // Check if already verified
    if ($user['email_verified']) {
        $_SESSION['verify_success'] = 'Your email is already verified. Please wait for admin approval to access the system.';
        header('Location: ' . BASE_URL . 'login-bhw');
        exit();
    }

    // Check if token expired
    if (strtotime($user['verification_token_expires']) < time()) {
        $_SESSION['login_error'] = 'Verification link has expired. Please contact the administrator to resend the verification email.';
        header('Location: ' . BASE_URL . 'login-bhw');
        exit();
    }

    // Update user - mark email as verified and update account status
    $update = $pdo->prepare('
        UPDATE bhw_users 
        SET email_verified = 1, 
            verification_token = NULL, 
            verification_token_expires = NULL,
            account_status = \'verified\'
        WHERE bhw_id = :bhw_id
    ');
    $update->execute([':bhw_id' => $user['bhw_id']]);

    $_SESSION['verify_success'] = 'Email verified successfully! Your account is now pending approval by the Healthcare Center Head. You will receive an email once your account is approved.';
    header('Location: ' . BASE_URL . 'login-bhw');
    exit();

} catch (PDOException $e) {
    error_log('Email verification error: ' . $e->getMessage());
    $_SESSION['login_error'] = 'An error occurred during verification. Please try again later.';
    header('Location: ' . BASE_URL . 'login-bhw');
    exit();
}
