<?php
// actions/register_patient_action.php
// Handle patient portal registration

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . BASE_URL . 'register-patient');
    exit();
}

$full_name = isset($_POST['full_name']) ? trim($_POST['full_name']) : '';
$birthdate = isset($_POST['birthdate']) ? trim($_POST['birthdate']) : '';
$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$password = isset($_POST['password']) ? $_POST['password'] : '';
$password_confirm = isset($_POST['password_confirm']) ? $_POST['password_confirm'] : '';

// Basic validation
if ($full_name === '' || $birthdate === '' || $email === '' || $password === '' || $password_confirm === '') {
    $_SESSION['register_error'] = 'All fields are required.';
    header('Location: ' . BASE_URL . 'register-patient');
    exit();
}

if ($password !== $password_confirm) {
    $_SESSION['register_error'] = 'Passwords do not match.';
    header('Location: ' . BASE_URL . 'register-patient');
    exit();
}

try {
    // Check if email already registered
    $stmt = $pdo->prepare('SELECT user_id FROM patient_users WHERE email = :email LIMIT 1');
    $stmt->execute([':email' => $email]);
    $existing = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($existing) {
        $_SESSION['register_error'] = 'Email already registered. Try logging in or use a different email.';
        header('Location: ' . BASE_URL . 'register-patient');
        exit();
    }

    // Find patient record by full name and birthdate
    $stmt = $pdo->prepare('SELECT patient_id FROM patients WHERE full_name = :full_name AND birthdate = :birthdate LIMIT 1');
    $stmt->execute([':full_name' => $full_name, ':birthdate' => $birthdate]);
    $patient = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$patient) {
        $_SESSION['register_error'] = 'No patient record found. Please ensure your name and birthdate match the ones on file.';
        header('Location: ' . BASE_URL . 'register-patient');
        exit();
    }

    $patient_id = $patient['patient_id'];

    // Hash the password
    $password_hash = password_hash($password, PASSWORD_BCRYPT);

    // Insert into patient_users
    $insert = $pdo->prepare('INSERT INTO patient_users (patient_id, email, password_hash) VALUES (:patient_id, :email, :password_hash)');
    $insert->execute([
        ':patient_id' => $patient_id,
        ':email' => $email,
        ':password_hash' => $password_hash
    ]);

    $_SESSION['register_success'] = 'Registration successful! You can now log in.';
    header('Location: ' . BASE_URL . 'login-patient');
    exit();

} catch (Throwable $e) {
    error_log('Patient registration error: ' . $e->getMessage());
    $_SESSION['register_error'] = 'An unexpected error occurred. Please try again later.';
    header('Location: ' . BASE_URL . 'register-patient');
    exit();
}
