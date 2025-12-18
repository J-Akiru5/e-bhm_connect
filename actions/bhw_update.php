<?php
// actions/bhw_update.php
// Update BHW user information

// Ensure session is started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include required configuration files
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . BASE_URL . 'admin-bhw-users');
    exit();
}

// Security & Auth Checks
require_once __DIR__ . '/../includes/security_helper.php';
require_once __DIR__ . '/../includes/auth_helpers.php';

require_csrf();
require_admin(); // Only Admins/Superadmins can update BHWs

$bhw_id = isset($_POST['bhw_id']) ? intval($_POST['bhw_id']) : 0;
$full_name = isset($_POST['full_name']) ? trim($_POST['full_name']) : '';
$username = isset($_POST['username']) ? trim($_POST['username']) : '';
$bhw_unique_id = isset($_POST['bhw_unique_id']) ? trim($_POST['bhw_unique_id']) : '';
$address = isset($_POST['address']) ? trim($_POST['address']) : '';
$birthdate = isset($_POST['birthdate']) ? trim($_POST['birthdate']) : null;
$contact = isset($_POST['contact']) ? trim($_POST['contact']) : '';
$training_cert = isset($_POST['training_cert']) ? trim($_POST['training_cert']) : '';
$assigned_area = isset($_POST['assigned_area']) ? trim($_POST['assigned_area']) : '';
$employment_status = isset($_POST['employment_status']) ? trim($_POST['employment_status']) : '';

if ($bhw_id <= 0) {
    $_SESSION['form_error'] = 'Invalid BHW ID.';
    header('Location: ' . BASE_URL . 'admin-bhw-users');
    exit();
}

try {
    // Handle File Upload for Training Certificate
    $training_cert_path = isset($_POST['existing_cert']) ? $_POST['existing_cert'] : '';

    if (isset($_FILES['training_cert_file']) && $_FILES['training_cert_file']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['training_cert_file']['tmp_name'];
        $fileName = $_FILES['training_cert_file']['name'];
        $fileSize = $_FILES['training_cert_file']['size'];
        $fileType = $_FILES['training_cert_file']['type'];
        $fileNameCmps = explode(".", $fileName);
        $fileExtension = strtolower(end($fileNameCmps));

        $allowedfileExtensions = array('jpg', 'gif', 'png', 'jpeg', 'pdf');
        if (in_array($fileExtension, $allowedfileExtensions)) {
            // Directory where certs will be saved
            $uploadFileDir = __DIR__ . '/../assets/uploads/certs/';
            if (!is_dir($uploadFileDir)) {
                mkdir($uploadFileDir, 0755, true);
            }
            
            // Generate unique name
            $newFileName = 'cert_' . $bhw_id . '_' . md5(time() . $fileName) . '.' . $fileExtension;
            $dest_path = $uploadFileDir . $newFileName;

            if(move_uploaded_file($fileTmpPath, $dest_path)) {
                $training_cert_path = 'assets/uploads/certs/' . $newFileName;
            } else {
                // If upload fails, maybe log it but continue with other updates
                error_log("File upload failed for BHW ID $bhw_id");
            }
        }
    }

    // Handle Permissions
    $permissions = isset($_POST['permissions']) ? $_POST['permissions'] : [];
    $access_permissions_json = json_encode($permissions);

    $sql = 'UPDATE bhw_users SET 
            full_name = :full_name, 
            username = :username, 
            bhw_unique_id = :bhw_unique_id, 
            address = :address, 
            birthdate = :birthdate, 
            contact = :contact, 
            training_cert = :training_cert, 
            assigned_area = :assigned_area, 
            employment_status = :employment_status,
            access_permissions = :access_permissions
            WHERE bhw_id = :bhw_id';
            
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':full_name' => $full_name,
        ':username' => $username,
        ':bhw_unique_id' => $bhw_unique_id,
        ':address' => $address,
        ':birthdate' => $birthdate,
        ':contact' => $contact,
        ':training_cert' => $training_cert_path,
        ':assigned_area' => $assigned_area,
        ':employment_status' => $employment_status,
        ':access_permissions' => $access_permissions_json,
        ':bhw_id' => $bhw_id
    ]);

    $_SESSION['form_success'] = 'BHW profile and permissions updated successfully.';
    header('Location: ' . BASE_URL . 'admin-bhw-users');
    exit();

} catch (Throwable $e) {
    error_log('BHW update error: ' . $e->getMessage());
    $_SESSION['form_error'] = 'An error occurred updating the profile. Username or ID might be taken.';
    header('Location: ' . BASE_URL . 'admin-bhw-edit?id=' . $bhw_id);
    exit();
}

?>
