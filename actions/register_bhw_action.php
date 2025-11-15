<?php
// actions/register_bhw_action.php
// Handle BHW registration securely
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../register-bhw');
    exit();
}

require_once __DIR__ . '/../config/database.php';

$full_name = isset($_POST['full_name']) ? trim($_POST['full_name']) : '';
$username = isset($_POST['username']) ? trim($_POST['username']) : '';
$password = isset($_POST['password']) ? $_POST['password'] : '';
$password_confirm = isset($_POST['password_confirm']) ? $_POST['password_confirm'] : '';
$bhw_unique_id = isset($_POST['bhw_unique_id']) ? trim($_POST['bhw_unique_id']) : '';

// Basic validation
if ($full_name === '' || $username === '' || $password === '' || $password_confirm === '' || $bhw_unique_id === '') {
    $_SESSION['register_error'] = 'All fields are required.';
    header('Location: ../register-bhw');
    exit();
}

if ($password !== $password_confirm) {
    $_SESSION['register_error'] = 'Passwords do not match.';
    header('Location: ../register-bhw');
    exit();
}

try {
    // Check for existing username or BHW ID
    $stmt = $pdo->prepare('SELECT bhw_id FROM bhw_users WHERE username = :username OR bhw_unique_id = :bhw_unique_id LIMIT 1');
    $stmt->execute([':username' => $username, ':bhw_unique_id' => $bhw_unique_id]);
    $existing = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($existing) {
        $_SESSION['register_error'] = 'Username or BHW ID already taken.';
        header('Location: ../register-bhw');
        exit();
    }

    // Hash the password
    $password_hash = password_hash($password, PASSWORD_BCRYPT);

    // Insert new user
    $insert = $pdo->prepare('INSERT INTO bhw_users (full_name, username, password_hash, bhw_unique_id) VALUES (:full_name, :username, :password_hash, :bhw_unique_id)');
    $insert->execute([
        ':full_name' => $full_name,
        ':username' => $username,
        ':password_hash' => $password_hash,
        ':bhw_unique_id' => $bhw_unique_id
    ]);

    $_SESSION['register_success'] = 'Registration successful! You can now log in.';
    header('Location: ../login-bhw');
    exit();

} catch (Throwable $e) {
    error_log('Registration error: ' . $e->getMessage());
    $_SESSION['register_error'] = 'An unexpected error occurred. Please try again later.';
    header('Location: ../register-bhw');
    exit();
}
