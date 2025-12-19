<?php
// actions/register_patient_action.php
// Handle patient portal registration
// Requires existing patient record (created by BHW) + email verification

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth_helpers.php';

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
    $_SESSION['register_error'] = 'Name, birthdate, email and password are required.';
    header('Location: ' . BASE_URL . 'register-patient');
    exit();
}

if ($password !== $password_confirm) {
    $_SESSION['register_error'] = 'Passwords do not match.';
    header('Location: ' . BASE_URL . 'register-patient');
    exit();
}

if (strlen($password) < 8) {
    $_SESSION['register_error'] = 'Password must be at least 8 characters.';
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

    // Get registration mode from app settings
    $registrationMode = get_app_setting('portal_registration_mode', 'linked_only');

    // Look for existing resident by full_name + birthdate
    $stmt = $pdo->prepare('SELECT * FROM patients WHERE full_name = :full_name AND birthdate = :birthdate LIMIT 1');
    $stmt->execute([':full_name' => $full_name, ':birthdate' => $birthdate]);
    $patient = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$patient && $registrationMode === 'linked_only') {
        // No matching record found - reject registration
        $_SESSION['register_error'] = 'No matching resident record found. Please ensure your name and birthdate match our records, or contact your Barangay Health Worker to be added to the system.';
        header('Location: ' . BASE_URL . 'register-patient');
        exit();
    }

    $pdo->beginTransaction();

    if ($patient) {
        $patient_id = (int) $patient['patient_id'];

        // Check if this patient already has a portal account
        $checkStmt = $pdo->prepare('SELECT user_id FROM patient_users WHERE patient_id = :patient_id LIMIT 1');
        $checkStmt->execute([':patient_id' => $patient_id]);
        if ($checkStmt->fetch()) {
            $pdo->rollBack();
            $_SESSION['register_error'] = 'A portal account already exists for this resident. Please login or contact your BHW.';
            header('Location: ' . BASE_URL . 'register-patient');
            exit();
        }

        // Update missing fields in patient record
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
        // Mode is 'open' - create new patient record
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

    // Generate verification token
    $verificationToken = bin2hex(random_bytes(32));
    $verificationExpires = date('Y-m-d H:i:s', strtotime('+24 hours'));

    // Create patient_users login with pending status
    $password_hash = password_hash($password, PASSWORD_BCRYPT);
    $insertUser = $pdo->prepare('
        INSERT INTO patient_users (patient_id, email, password_hash, status, email_verified, verification_token, verification_expires_at) 
        VALUES (:patient_id, :email, :password_hash, :status, 0, :token, :expires)
    ');
    $insertUser->execute([
        ':patient_id' => $patient_id,
        ':email' => $email,
        ':password_hash' => $password_hash,
        ':status' => 'pending',
        ':token' => $verificationToken,
        ':expires' => $verificationExpires
    ]);

    $pdo->commit();

    // Log the registration
    log_audit('patient_registered', 'patient_users', $patient_id, ['email' => $email]);

    // TODO: Send verification email (for now, show success with token in dev)
    // In production, implement actual email sending
    $verifyUrl = BASE_URL . "?action=verify-patient-email&token=" . $verificationToken;
    
    // For development: log the verification URL
    error_log("Patient verification URL: " . $verifyUrl);

    $_SESSION['register_success'] = 'Registration successful! Please check your email to verify your account before logging in.';
    header('Location: ' . BASE_URL . 'login-patient');
    exit();

} catch (Throwable $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    error_log('Patient registration error: ' . $e->getMessage());
    $_SESSION['register_error'] = 'An unexpected error occurred. Please try again later.';
    header('Location: ' . BASE_URL . 'register-patient');
    exit();
}
