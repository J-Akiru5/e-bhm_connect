<?php
// pages/admin/profile.php
// Profile management for logged-in BHW - Modernized
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../config/database.php';

$bhw_id = $_SESSION['bhw_id'] ?? null;
if (!$bhw_id) {
    header('Location: ' . BASE_URL . 'login-bhw');
    exit();
}

// Fetch full BHW data
$stmt = $pdo->prepare("SELECT * FROM bhw_users WHERE bhw_id = ?");
$stmt->execute([$bhw_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Calculate age if birthdate exists
$age = 'N/A';
if (!empty($user['birthdate'])) {
    $birthdate = new DateTime($user['birthdate']);
    $now = new DateTime();
    $age = $birthdate->diff($now)->y;
}

// Profile photo path
$profilePhoto = $user['profile_photo'] ?? null;
$photoPath = $profilePhoto ? BASE_URL . 'uploads/profiles/' . $profilePhoto : BASE_URL . 'assets/images/default-avatar.png';

include_once __DIR__ . '/../../includes/header_admin.php';
?>

<style>
/* Admin Profile Styles */
.profile-hero {
    background: linear-gradient(135deg, var(--primary), var(--primary-dark));
    border-radius: var(--radius-2xl);
    padding: var(--space-8);
    margin-bottom: var(--space-6);
    color: white;
    position: relative;
    overflow: hidden;
}

.profile-hero::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -20%;
    width: 60%;
    height: 200%;
    background: rgba(255,255,255,0.05);
    transform: rotate(15deg);
    pointer-events: none;
}

.profile-photo-container {
    position: relative;
    width: 140px;
    height: 140px;
    margin: 0 auto var(--space-4);
}

.profile-photo {
    width: 140px;
    height: 140px;
    border-radius: 50%;
    border: 4px solid rgba(255,255,255,0.3);
    object-fit: cover;
    background: rgba(255,255,255,0.1);
}

.photo-upload-btn {
    position: absolute;
    bottom: 5px;
    right: 5px;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: white;
    border: none;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: var(--shadow-md);
    transition: all var(--transition-fast);
}

.photo-upload-btn:hover {
    transform: scale(1.1);
    background: var(--primary-light);
}

.photo-upload-btn svg {
    color: var(--primary);
}

.profile-name {
    font-size: 1.75rem;
    font-weight: 700;
    text-align: center;
    margin-bottom: var(--space-2);
}

.profile-meta {
    text-align: center;
    opacity: 0.9;
    font-size: var(--font-size-sm);
}

.profile-section {
    background: var(--white);
    border-radius: var(--radius-xl);
    border: 1px solid var(--gray-200);
    margin-bottom: var(--space-6);
    overflow: hidden;
}

.profile-section-header {
    padding: var(--space-4) var(--space-6);
    background: var(--gray-50);
    border-bottom: 1px solid var(--gray-200);
    display: flex;
    align-items: center;
    gap: var(--space-3);
    font-weight: 600;
    color: var(--gray-700);
}

.profile-section-body {
    padding: var(--space-6);
}

.form-label {
    font-weight: 500;
    color: var(--gray-700);
    margin-bottom: var(--space-2);
}

.form-control {
    border: 1px solid var(--gray-300);
    border-radius: var(--radius-lg);
    padding: var(--space-3) var(--space-4);
    transition: all var(--transition-fast);
}

.form-control:focus {
    border-color: var(--primary);
    box-shadow: 0 0 0 3px rgba(32, 201, 151, 0.15);
}

.btn-save {
    background: linear-gradient(135deg, var(--primary), var(--primary-dark));
    color: white;
    border: none;
    padding: var(--space-3) var(--space-6);
    border-radius: var(--radius-lg);
    font-weight: 600;
    transition: all var(--transition-fast);
}

.btn-save:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-lg);
    color: white;
}

.back-btn {
    margin-bottom: var(--space-4);
}
</style>

<?php
// Flash messages via SweetAlert2
if (isset($_SESSION['form_success'])) {
    $msg = json_encode($_SESSION['form_success']);
    echo "<script>window.addEventListener('load', function(){ if (typeof Swal !== 'undefined') { Swal.fire({icon: 'success', title: 'Success', text: $msg}); } });</script>";
    unset($_SESSION['form_success']);
}
if (isset($_SESSION['form_error'])) {
    $emsg = json_encode($_SESSION['form_error']);
    echo "<script>window.addEventListener('load', function(){ if (typeof Swal !== 'undefined') { Swal.fire({icon: 'error', title: 'Error', text: $emsg}); } });</script>";
    unset($_SESSION['form_error']);
}
?>

<div class="back-btn">
    <a href="<?php echo BASE_URL; ?>admin-dashboard" class="btn btn-glass">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="me-1">
            <line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline>
        </svg>
        Back to Dashboard
    </a>
</div>

<!-- Profile Hero -->
<div class="profile-hero">
    <form id="photoForm" action="<?php echo BASE_URL; ?>?action=bhw-profile-save&type=photo" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="bhw_id" value="<?php echo $bhw_id; ?>">
        <input type="file" name="profile_photo" id="photoInput" accept="image/*" style="display: none;">
        
        <div class="profile-photo-container">
            <img src="<?php echo htmlspecialchars($photoPath); ?>" alt="Profile Photo" class="profile-photo" id="photoPreview">
            <button type="button" class="photo-upload-btn" onclick="document.getElementById('photoInput').click()">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"></path>
                    <circle cx="12" cy="13" r="4"></circle>
                </svg>
            </button>
        </div>
    </form>
    
    <div class="profile-name"><?php echo htmlspecialchars($user['full_name'] ?? 'BHW User'); ?></div>
    <div class="profile-meta">
        <?php if ($age !== 'N/A'): ?>
            <span><?php echo $age; ?> years old</span>
            <span class="mx-2">•</span>
        <?php endif; ?>
        <span><?php echo htmlspecialchars($user['sex'] ?? ''); ?></span>
        <?php if (!empty($user['address'])): ?>
            <span class="mx-2">•</span>
            <span><?php echo htmlspecialchars($user['address']); ?></span>
        <?php endif; ?>
    </div>
</div>

<div class="row g-4">
    <!-- Personal Information -->
    <div class="col-lg-6">
        <div class="profile-section">
            <div class="profile-section-header">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle>
                </svg>
                Personal Information
            </div>
            <div class="profile-section-body">
                <form action="<?php echo BASE_URL; ?>?action=bhw-profile-save&type=info" method="POST">
                    <input type="hidden" name="bhw_id" value="<?php echo $bhw_id; ?>">
                    
                    <div class="mb-3">
                        <label class="form-label">Full Name</label>
                        <input type="text" name="full_name" class="form-control" value="<?php echo htmlspecialchars($user['full_name'] ?? ''); ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Username</label>
                        <input type="text" name="username" class="form-control" value="<?php echo htmlspecialchars($user['username'] ?? ''); ?>" required>
                    </div>
                    
                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="form-label">Birthdate</label>
                            <input type="date" name="birthdate" class="form-control" value="<?php echo $user['birthdate'] ?? ''; ?>">
                        </div>
                        <div class="col-6">
                            <label class="form-label">Sex</label>
                            <select name="sex" class="form-control">
                                <option value="">Select...</option>
                                <option value="Male" <?php echo ($user['sex'] ?? '') === 'Male' ? 'selected' : ''; ?>>Male</option>
                                <option value="Female" <?php echo ($user['sex'] ?? '') === 'Female' ? 'selected' : ''; ?>>Female</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Address</label>
                        <textarea name="address" class="form-control" rows="2"><?php echo htmlspecialchars($user['address'] ?? ''); ?></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Contact Number</label>
                        <input type="text" name="contact" class="form-control" value="<?php echo htmlspecialchars($user['contact'] ?? ''); ?>">
                    </div>
                    
                    <div class="mb-4">
                        <label class="form-label">Email Address</label>
                        <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>" placeholder="Enter your email">
                    </div>
                    
                    <button type="submit" class="btn btn-save w-100">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="me-1">
                            <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path>
                            <polyline points="17 21 17 13 7 13 7 21"></polyline><polyline points="7 3 7 8 15 8"></polyline>
                        </svg>
                        Save Changes
                    </button>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Right Column -->
    <div class="col-lg-6">
        <!-- Change Password -->
        <div class="profile-section">
            <div class="profile-section-header">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                    <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                </svg>
                Change Password
            </div>
            <div class="profile-section-body">
                <form action="<?php echo BASE_URL; ?>?action=change-password" method="POST">
                    <input type="hidden" name="bhw_id" value="<?php echo $bhw_id; ?>">
                    
                    <div class="mb-3">
                        <label class="form-label">Current Password</label>
                        <input type="password" name="old_password" class="form-control" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">New Password</label>
                        <input type="password" name="new_password" class="form-control" required minlength="6">
                    </div>
                    
                    <div class="mb-4">
                        <label class="form-label">Confirm New Password</label>
                        <input type="password" name="confirm_new_password" class="form-control" required minlength="6">
                    </div>
                    
                    <button type="submit" class="btn btn-save w-100">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="me-1">
                            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                            <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                        </svg>
                        Update Password
                    </button>
                </form>
            </div>
        </div>
        
        <!-- Account Info Card -->
        <div class="profile-section">
            <div class="profile-section-header">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line>
                </svg>
                Account Information
            </div>
            <div class="profile-section-body">
                <div class="row g-3">
                    <div class="col-6">
                        <div class="text-muted small">Role</div>
                        <div class="fw-medium"><?php echo ucfirst($user['role'] ?? 'bhw'); ?></div>
                    </div>
                    <div class="col-6">
                        <div class="text-muted small">BHW ID</div>
                        <div class="fw-medium">#<?php echo $bhw_id; ?></div>
                    </div>
                    <div class="col-6">
                        <div class="text-muted small">Status</div>
                        <div class="fw-medium">
                            <?php if (($user['status'] ?? '') === 'approved'): ?>
                                <span class="badge badge-success">Approved</span>
                            <?php elseif (($user['status'] ?? '') === 'pending'): ?>
                                <span class="badge badge-warning">Pending</span>
                            <?php else: ?>
                                <span class="badge badge-secondary"><?php echo htmlspecialchars($user['status'] ?? 'N/A'); ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="text-muted small">Member Since</div>
                        <div class="fw-medium"><?php echo isset($user['created_at']) ? date('M j, Y', strtotime($user['created_at'])) : 'N/A'; ?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Photo preview and auto-submit
document.getElementById('photoInput').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(ev) {
            document.getElementById('photoPreview').src = ev.target.result;
        };
        reader.readAsDataURL(file);
        
        // Auto-submit after brief delay for preview
        setTimeout(() => {
            document.getElementById('photoForm').submit();
        }, 500);
    }
});
</script>

<?php include_once __DIR__ . '/../../includes/footer_admin.php'; ?>
