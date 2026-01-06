<?php
// actions/register_bhw_action.php
// Handle BHW registration with email verification
// Ensure session is started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include required configuration files
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/email_helper.php';
require_once __DIR__ . '/../includes/security_helper.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . BASE_URL . 'register-bhw');
    exit();
}

// Validate CSRF token
require_csrf();

$full_name = isset($_POST['full_name']) ? trim($_POST['full_name']) : '';
$username = isset($_POST['username']) ? trim($_POST['username']) : '';
$email = isset($_POST['email']) ? trim(strtolower($_POST['email'])) : '';
$password = isset($_POST['password']) ? $_POST['password'] : '';
$password_confirm = isset($_POST['password_confirm']) ? $_POST['password_confirm'] : '';
$bhw_unique_id = isset($_POST['bhw_unique_id']) ? trim($_POST['bhw_unique_id']) : '';

// Store form data for repopulation on errors (excluding passwords)
$_SESSION['register_form_data'] = [
    'full_name' => $full_name,
    'username' => $username,
    'email' => $email,
    'bhw_unique_id' => $bhw_unique_id
];

// Basic validation
if ($full_name === '' || $username === '' || $email === '' || $password === '' || $password_confirm === '' || $bhw_unique_id === '') {
    $_SESSION['register_error'] = 'All fields are required.';
    header('Location: ' . BASE_URL . 'register-bhw');
    exit();
}

// Validate email format
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['register_error'] = 'Please enter a valid email address.';
    header('Location: ' . BASE_URL . 'register-bhw');
    exit();
}

// Password strength validation
if (strlen($password) < 8) {
    $_SESSION['register_error'] = 'Password must be at least 8 characters long.';
    header('Location: ' . BASE_URL . 'register-bhw');
    exit();
}

if ($password !== $password_confirm) {
    $_SESSION['register_error'] = 'Passwords do not match.';
    header('Location: ' . BASE_URL . 'register-bhw');
    exit();
}

try {
    // Check for existing username, email, or BHW ID
    $stmt = $pdo->prepare('SELECT bhw_id, username, email, bhw_unique_id FROM bhw_users WHERE username = :username OR email = :email OR bhw_unique_id = :bhw_unique_id LIMIT 1');
    $stmt->execute([
        ':username' => $username,
        ':email' => $email,
        ':bhw_unique_id' => $bhw_unique_id
    ]);
    $existing = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($existing) {
        if ($existing['username'] === $username) {
            $_SESSION['register_error'] = 'Username is already taken.';
        } elseif ($existing['email'] === $email) {
            $_SESSION['register_error'] = 'Email address is already registered.';
        } else {
            $_SESSION['register_error'] = 'BHW ID is already registered.';
        }
        header('Location: ' . BASE_URL . 'register-bhw');
        exit();
    }

    // Hash the password
    $password_hash = password_hash($password, PASSWORD_BCRYPT);

    // Insert new user with auto-approved status (no email verification required)
    $insert = $pdo->prepare('
        INSERT INTO bhw_users (
            full_name, 
            username, 
            email,
            password_hash, 
            bhw_unique_id,
            email_verified,
            account_status,
            role,
            created_at
        ) VALUES (
            :full_name, 
            :username, 
            :email,
            :password_hash, 
            :bhw_unique_id,
            1,
            \'approved\',
            \'bhw\',
            NOW()
        )
    ');
    
    $insert->execute([
        ':full_name' => $full_name,
        ':username' => $username,
        ':email' => $email,
        ':password_hash' => $password_hash,
        ':bhw_unique_id' => $bhw_unique_id
    ]);

    $_SESSION['register_success'] = 'Registration successful! You can now log in to your account.';
    
    // Clear form data on successful registration
    unset($_SESSION['register_form_data']);
    
    header('Location: ' . BASE_URL . 'login-bhw');
    exit();

} catch (PDOException $e) {
    error_log('Registration error: ' . $e->getMessage());
    
    // Check for duplicate entry errors
    if ($e->getCode() == 23000) {
        $_SESSION['register_error'] = 'An account with these details already exists.';
    } else {
        $_SESSION['register_error'] = 'An unexpected error occurred. Please try again later.';
    }
    
    header('Location: ' . BASE_URL . 'register-bhw');
    exit();
} catch (Throwable $e) {
    error_log('Registration error: ' . $e->getMessage());
    $_SESSION['register_error'] = 'An unexpected error occurred. Please try again later.';
    header('Location: ' . BASE_URL . 'register-bhw');
    exit();
}
