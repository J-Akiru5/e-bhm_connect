<?php
// actions/register_patient_action.php
// Handle patient portal registration
// Ensure session is started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include required configuration files
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';


if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . BASE_URL . 'register-patient');
    exit();
}

 $full_name = isset($_POST['full_name']) ? trim($_POST['full_name']) : '';
 $birthdate = isset($_POST['birthdate']) ? trim($_POST['birthdate']) : '';
 $sex = isset($_POST['sex']) ? trim($_POST['sex']) : '';
 $contact = isset($_POST['contact']) ? trim($_POST['contact']) : '';
 $address = isset($_POST['address']) ? trim($_POST['address']) : '';
 $email = isset($_POST['email']) ? trim($_POST['email']) : '';
 $password = isset($_POST['password']) ? $_POST['password'] : '';
 $password_confirm = isset($_POST['password_confirm']) ? $_POST['password_confirm'] : '';

// Basic validation
if ($full_name === '' || $birthdate === '' || $email === '' || $password === '' || $password_confirm === '') {
    // Require core identity + account fields
    $_SESSION['register_error'] = 'Name, birthdate, email and password are required.';
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
    // Begin smart-register logic: either update existing patient or create new
    $pdo->beginTransaction();

    // Look for existing resident by full_name + birthdate
    $stmt = $pdo->prepare('SELECT * FROM patients WHERE full_name = :full_name AND birthdate = :birthdate LIMIT 1');
    $stmt->execute([':full_name' => $full_name, ':birthdate' => $birthdate]);
    $patient = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($patient) {
        $patient_id = (int) $patient['patient_id'];

        // Build update only for missing fields
        $updates = [];
        $params = [':patient_id' => $patient_id];

        if ($contact !== '' && (empty($patient['contact']) || $patient['contact'] !== $contact)) {
            $updates[] = 'contact = :contact';
            $params[':contact'] = $contact;
        }
        if ($address !== '' && (empty($patient['address']) || $patient['address'] !== $address)) {
            $updates[] = 'address = :address';
            $params[':address'] = $address;
        }
        if ($sex !== '' && (empty($patient['sex']) || $patient['sex'] !== $sex)) {
            $updates[] = 'sex = :sex';
            $params[':sex'] = $sex;
        }

        if (!empty($updates)) {
            $sql = 'UPDATE patients SET ' . implode(', ', $updates) . ' WHERE patient_id = :patient_id';
            $u = $pdo->prepare($sql);
            $u->execute($params);
        }
    } else {
        // Insert new patient record
        $ins = $pdo->prepare('INSERT INTO patients (full_name, birthdate, address, sex, contact) VALUES (:full_name, :birthdate, :address, :sex, :contact)');
        $ins->execute([
            ':full_name' => $full_name,
            ':birthdate' => $birthdate,
            ':address' => $address,
            ':sex' => $sex,
            ':contact' => $contact
        ]);
        $patient_id = (int) $pdo->lastInsertId();
    }

    // Create patient_users login
    $password_hash = password_hash($password, PASSWORD_BCRYPT);
    $insertUser = $pdo->prepare('INSERT INTO patient_users (patient_id, email, password_hash) VALUES (:patient_id, :email, :password_hash)');
    $insertUser->execute([
        ':patient_id' => $patient_id,
        ':email' => $email,
        ':password_hash' => $password_hash
    ]);

    $pdo->commit();

    $_SESSION['register_success'] = 'Registration successful! You can now log in.';
    header('Location: ' . BASE_URL . 'login-patient');
    exit();

} catch (Throwable $e) {
    error_log('Patient registration error: ' . $e->getMessage());
    $_SESSION['register_error'] = 'An unexpected error occurred. Please try again later.';
    header('Location: ' . BASE_URL . 'register-patient');
    exit();
}
