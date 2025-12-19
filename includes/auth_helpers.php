<?php
/**
 * E-BHM Connect - Authentication Helpers
 * Provides role-based access control and audit logging
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/translation_helper.php';

/**
 * Check if the current user is a superadmin
 * @return bool
 */
function is_superadmin(): bool
{
    if (!isset($_SESSION['bhw_id'])) {
        return false;
    }
    
    // Check both possible session keys for backward compatibility
    $role = $_SESSION['role'] ?? $_SESSION['bhw_role'] ?? '';
    return strtolower($role) === 'superadmin';
}

/**
 * Check if the current user is an admin (includes superadmin)
 * @return bool
 */
function is_admin(): bool
{
    if (!isset($_SESSION['bhw_id'])) {
        return false;
    }
    
    // Check both possible session keys for backward compatibility
    $role = strtolower($_SESSION['role'] ?? $_SESSION['bhw_role'] ?? '');
    return in_array($role, ['admin', 'superadmin']);
}

/**
 * Check if current user is a regular BHW
 * @return bool
 */
function is_bhw(): bool
{
    return isset($_SESSION['bhw_id']);
}

/**
 * Require superadmin access, redirect if not authorized
 * @param string $redirect URL to redirect to if not authorized
 */
function require_superadmin(string $redirect = '/pages/admin/dashboard.php'): void
{
    if (!is_superadmin()) {
        $_SESSION['flash_error'] = __('auth.unauthorized_superadmin') ?: 'You do not have permission to access this page.';
        header("Location: $redirect");
        exit;
    }
}

/**
 * Require admin access (admin or superadmin), redirect if not authorized
 * @param string $redirect URL to redirect to if not authorized
 */
function require_admin(string $redirect = '/pages/admin/dashboard.php'): void
{
    if (!is_admin()) {
        $_SESSION['flash_error'] = __('auth.unauthorized_admin') ?: 'Admin access required.';
        header("Location: $redirect");
        exit;
    }
}

/**
 * Check if user has a specific permission
 * @param string $permission Permission key to check
 * @return bool
 */
function has_permission(string $permission): bool
{
    // Superadmin has all permissions
    if (is_superadmin()) {
        return true;
    }

    // Check dynamic permissions fetched in header (or set elsewhere)
    if (isset($_SESSION['access_permissions']) && is_array($_SESSION['access_permissions'])) {
        return in_array($permission, $_SESSION['access_permissions']);
    }
    
    // Define permission groups (FALLBACK for legacy support or if session is empty)
    // Note: If dynamic permissions are strictly used, fallback might not be needed.
    // But keeping it for safety for now, or just returning false if we want strict mode.
    // Given user's request: "only view pages that they have access into", let's prioritize the dynamic check.
    // If session permissions are NOT set (e.g. migration didn't happen to this user), maybe it's safest to return false?
    // However, existing users might lose access. But migration added the column.
    
    return false;

    // Legacy manual map (commented out for reference or potential fallback if needed)
    /*
    $permissions = [
        'patients.view' => ['admin', 'bhw'],
        // ... (rest of old logic)
    ];
    */
}

/**
 * Require a specific permission, redirect if not authorized
 * @param string $permission Permission key to check
 * @param string $redirect URL to redirect to if not authorized
 */
function require_permission(string $permission, string $redirect = '/pages/admin/dashboard.php'): void
{
    if (!has_permission($permission)) {
        $_SESSION['flash_error'] = __('auth.permission_denied') ?: 'You do not have permission to perform this action.';
        header("Location: $redirect");
        exit;
    }
}

/**
 * Log an audit event
 * @param string $action Action performed (e.g., 'login', 'create_patient', 'update_inventory')
 * @param string|null $entityType Type of entity affected (e.g., 'patient', 'inventory', 'bhw')
 * @param int|null $entityId ID of the entity affected
 * @param array|null $details Additional details to log
 * @return bool Success status
 */
function log_audit(
    string $action,
    ?string $entityType = null,
    ?int $entityId = null,
    ?array $details = null
): bool {
    global $pdo;
    
    try {
        // Determine user info
        $userId = null;
        $userType = 'system';
        
        if (isset($_SESSION['bhw_id'])) {
            $userId = $_SESSION['bhw_id'];
            $userType = 'bhw';
        } elseif (isset($_SESSION['patient_id'])) {
            $userId = $_SESSION['patient_id'];
            $userType = 'patient';
        }
        
        // Get IP address
        $ipAddress = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'] ?? null;
        if ($ipAddress && strpos($ipAddress, ',') !== false) {
            $ipAddress = trim(explode(',', $ipAddress)[0]);
        }
        
        // Get user agent
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? null;
        
        // Prepare details JSON
        $detailsJson = $details ? json_encode($details, JSON_UNESCAPED_UNICODE) : null;
        
        $stmt = $pdo->prepare("
            INSERT INTO audit_logs (user_id, user_type, action, entity_type, entity_id, details, ip_address, user_agent)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        
        $stmt->execute([
            $userId,
            $userType,
            $action,
            $entityType,
            $entityId,
            $detailsJson,
            $ipAddress,
            $userAgent
        ]);
        
        return true;
    } catch (PDOException $e) {
        error_log("Audit log error: " . $e->getMessage());
        return false;
    }
}

/**
 * Get recent audit logs
 * @param int $limit Number of logs to retrieve
 * @param int $offset Offset for pagination
 * @param array $filters Optional filters (action, entity_type, user_id, date_from, date_to)
 * @return array
 */
function get_audit_logs(int $limit = 50, int $offset = 0, array $filters = []): array
{
    global $pdo;
    
    try {
        $where = ['1=1'];
        $params = [];
        
        if (!empty($filters['action'])) {
            $where[] = 'al.action = ?';
            $params[] = $filters['action'];
        }
        
        if (!empty($filters['entity_type'])) {
            $where[] = 'al.entity_type = ?';
            $params[] = $filters['entity_type'];
        }
        
        if (!empty($filters['user_id'])) {
            $where[] = 'al.user_id = ?';
            $params[] = $filters['user_id'];
        }
        
        if (!empty($filters['user_type'])) {
            $where[] = 'al.user_type = ?';
            $params[] = $filters['user_type'];
        }
        
        if (!empty($filters['date_from'])) {
            $where[] = 'al.created_at >= ?';
            $params[] = $filters['date_from'] . ' 00:00:00';
        }
        
        if (!empty($filters['date_to'])) {
            $where[] = 'al.created_at <= ?';
            $params[] = $filters['date_to'] . ' 23:59:59';
        }
        
        $whereClause = implode(' AND ', $where);
        
        $sql = "
            SELECT 
                al.*,
                CASE 
                    WHEN al.user_type = 'bhw' THEN b.full_name
                    WHEN al.user_type = 'patient' THEN p.full_name
                    ELSE 'System'
                END as user_name,
                CASE 
                    WHEN al.user_type = 'bhw' THEN b.role
                    ELSE NULL
                END as user_role
            FROM audit_logs al
            LEFT JOIN bhw_users b ON al.user_type = 'bhw' AND al.user_id = b.bhw_id
            LEFT JOIN patients p ON al.user_type = 'patient' AND al.user_id = p.patient_id
            WHERE $whereClause
            ORDER BY al.created_at DESC
            LIMIT ? OFFSET ?
        ";
        
        $params[] = $limit;
        $params[] = $offset;
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Get audit logs error: " . $e->getMessage());
        return [];
    }
}

/**
 * Get user preferences
 * @param int|null $userId User ID (defaults to current session user)
 * @param string $userType 'bhw' or 'patient'
 * @return array
 */
function get_user_preferences(?int $userId = null, string $userType = 'bhw'): array
{
    global $pdo;
    
    // Get app-level defaults for theme and language
    $appDefaultTheme = get_app_setting('default_theme') ?? 'dark';
    $appDefaultLanguage = get_app_setting('default_language') ?? 'en';
    
    // Default preferences
    $defaults = [
        'theme' => $appDefaultTheme,
        'language' => $appDefaultLanguage,
        'notifications_enabled' => true,
        'email_notifications' => true,
        'dashboard_widgets' => ['stats', 'chart', 'recent_visits', 'audit_log']
    ];
    
    if ($userId === null) {
        if ($userType === 'bhw' && isset($_SESSION['bhw_id'])) {
            $userId = $_SESSION['bhw_id'];
        } elseif ($userType === 'patient' && isset($_SESSION['patient_id'])) {
            $userId = $_SESSION['patient_id'];
        } else {
            return $defaults;
        }
    }
    
    try {
        $stmt = $pdo->prepare("
            SELECT theme, language, notifications_enabled, email_notifications, dashboard_widgets
            FROM user_preferences
            WHERE user_id = ? AND user_type = ?
        ");
        $stmt->execute([$userId, $userType]);
        $prefs = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$prefs) {
            return $defaults;
        }
        
        // Decode dashboard_widgets if it's JSON
        if (!empty($prefs['dashboard_widgets'])) {
            $prefs['dashboard_widgets'] = json_decode($prefs['dashboard_widgets'], true) ?: $defaults['dashboard_widgets'];
        } else {
            $prefs['dashboard_widgets'] = $defaults['dashboard_widgets'];
        }
        
        // Convert to boolean
        $prefs['notifications_enabled'] = (bool)$prefs['notifications_enabled'];
        $prefs['email_notifications'] = (bool)$prefs['email_notifications'];
        
        return array_merge($defaults, $prefs);
    } catch (PDOException $e) {
        error_log("Get user preferences error: " . $e->getMessage());
        return $defaults;
    }
}

/**
 * Save user preferences
 * @param array $preferences Preferences to save
 * @param int|null $userId User ID (defaults to current session user)
 * @param string $userType 'bhw' or 'patient'
 * @return bool
 */
function save_user_preferences(array $preferences, ?int $userId = null, string $userType = 'bhw'): bool
{
    global $pdo;
    
    if ($userId === null) {
        if ($userType === 'bhw' && isset($_SESSION['bhw_id'])) {
            $userId = $_SESSION['bhw_id'];
        } elseif ($userType === 'patient' && isset($_SESSION['patient_id'])) {
            $userId = $_SESSION['patient_id'];
        } else {
            return false;
        }
    }
    
    try {
        // Encode dashboard_widgets as JSON
        if (isset($preferences['dashboard_widgets']) && is_array($preferences['dashboard_widgets'])) {
            $preferences['dashboard_widgets'] = json_encode($preferences['dashboard_widgets']);
        }
        
        $stmt = $pdo->prepare("
            INSERT INTO user_preferences (user_id, user_type, theme, language, notifications_enabled, email_notifications, dashboard_widgets)
            VALUES (?, ?, ?, ?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE
                theme = VALUES(theme),
                language = VALUES(language),
                notifications_enabled = VALUES(notifications_enabled),
                email_notifications = VALUES(email_notifications),
                dashboard_widgets = VALUES(dashboard_widgets),
                updated_at = CURRENT_TIMESTAMP
        ");
        
        return $stmt->execute([
            $userId,
            $userType,
            $preferences['theme'] ?? 'light',
            $preferences['language'] ?? 'en',
            $preferences['notifications_enabled'] ?? 1,
            $preferences['email_notifications'] ?? 1,
            $preferences['dashboard_widgets'] ?? null
        ]);
    } catch (PDOException $e) {
        error_log("Save user preferences error: " . $e->getMessage());
        return false;
    }
}

/**
 * Get application setting
 * @param string $key Setting key
 * @param mixed $default Default value if not found
 * @return mixed
 */
function get_app_setting(string $key, $default = null)
{
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("SELECT setting_value, setting_type FROM app_settings WHERE setting_key = ?");
        $stmt->execute([$key]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$row) {
            return $default;
        }
        
        $value = $row['setting_value'];
        
        // Cast based on setting_type
        switch ($row['setting_type']) {
            case 'integer':
                return (int)$value;
            case 'boolean':
                return filter_var($value, FILTER_VALIDATE_BOOLEAN);
            case 'json':
                return json_decode($value, true);
            case 'float':
                return (float)$value;
            default:
                return $value;
        }
    } catch (PDOException $e) {
        error_log("Get app setting error: " . $e->getMessage());
        return $default;
    }
}

/**
 * Set application setting
 * @param string $key Setting key
 * @param mixed $value Setting value
 * @param string $valueType Type of value (string, integer, boolean, json, float)
 * @param string|null $description Description of the setting
 * @return bool
 */
function set_app_setting(string $key, $value, string $valueType = 'string', ?string $description = null): bool
{
    global $pdo;
    
    try {
        // Convert value to string for storage
        if ($valueType === 'json' && is_array($value)) {
            $value = json_encode($value, JSON_UNESCAPED_UNICODE);
        } elseif ($valueType === 'boolean') {
            $value = $value ? '1' : '0';
        } else {
            $value = (string)$value;
        }
        
        $stmt = $pdo->prepare("
            INSERT INTO app_settings (setting_key, setting_value, setting_type, description)
            VALUES (?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE
                setting_value = VALUES(setting_value),
                setting_type = VALUES(setting_type),
                description = COALESCE(VALUES(description), description),
                updated_at = CURRENT_TIMESTAMP
        ");
        
        return $stmt->execute([$key, $value, $valueType, $description]);
    } catch (PDOException $e) {
        error_log("Set app setting error: " . $e->getMessage());
        return false;
    }
}

/**
 * Get all application settings
 * @return array
 */
function get_all_app_settings(): array
{
    global $pdo;
    
    try {
        $stmt = $pdo->query("SELECT * FROM app_settings ORDER BY setting_key");
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $settings = [];
        foreach ($rows as $row) {
            $value = $row['setting_value'];
            
            switch ($row['setting_type']) {
                case 'integer':
                    $value = (int)$value;
                    break;
                case 'boolean':
                    $value = filter_var($value, FILTER_VALIDATE_BOOLEAN);
                    break;
                case 'json':
                    $value = json_decode($value, true);
                    break;
                case 'float':
                    $value = (float)$value;
                    break;
            }
            
            $settings[$row['setting_key']] = [
                'value' => $value,
                'type' => $row['setting_type'],
                'description' => $row['description']
            ];
        }
        
        return $settings;
    } catch (PDOException $e) {
        error_log("Get all app settings error: " . $e->getMessage());
        return [];
    }
}

/**
 * Create a notification for a user
 * @param int $userId User ID
 * @param string $userType 'bhw' or 'patient'
 * @param string $type Notification type (info, success, warning, alert)
 * @param string $title Notification title
 * @param string $message Notification message
 * @param string|null $actionUrl URL to link to
 * @return bool
 */
function create_notification(
    int $userId,
    string $userType,
    string $type,
    string $title,
    string $message,
    ?string $actionUrl = null
): bool {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("
            INSERT INTO notifications (user_id, user_type, type, title, message, action_url)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        
        return $stmt->execute([$userId, $userType, $type, $title, $message, $actionUrl]);
    } catch (PDOException $e) {
        error_log("Create notification error: " . $e->getMessage());
        return false;
    }
}

/**
 * Get notifications for current user
 * @param int $limit Number of notifications to retrieve
 * @param bool $unreadOnly Only get unread notifications
 * @return array
 */
function get_notifications(int $limit = 20, bool $unreadOnly = false): array
{
    global $pdo;
    
    $userId = null;
    $userType = null;
    
    if (isset($_SESSION['bhw_id'])) {
        $userId = $_SESSION['bhw_id'];
        $userType = 'bhw';
    } elseif (isset($_SESSION['patient_id'])) {
        $userId = $_SESSION['patient_id'];
        $userType = 'patient';
    }
    
    if (!$userId) {
        return [];
    }
    
    try {
        $where = 'user_id = ? AND user_type = ?';
        $params = [$userId, $userType];
        
        if ($unreadOnly) {
            $where .= ' AND is_read = 0';
        }
        
        $stmt = $pdo->prepare("
            SELECT * FROM notifications
            WHERE $where
            ORDER BY created_at DESC
            LIMIT ?
        ");
        
        $params[] = $limit;
        $stmt->execute($params);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Get notifications error: " . $e->getMessage());
        return [];
    }
}

/**
 * Get unread notification count
 * @return int
 */
function get_unread_notification_count(): int
{
    global $pdo;
    
    $userId = null;
    $userType = null;
    
    if (isset($_SESSION['bhw_id'])) {
        $userId = $_SESSION['bhw_id'];
        $userType = 'bhw';
    } elseif (isset($_SESSION['patient_id'])) {
        $userId = $_SESSION['patient_id'];
        $userType = 'patient';
    }
    
    if (!$userId) {
        return 0;
    }
    
    try {
        $stmt = $pdo->prepare("
            SELECT COUNT(*) FROM notifications
            WHERE user_id = ? AND user_type = ? AND is_read = 0
        ");
        $stmt->execute([$userId, $userType]);
        
        return (int)$stmt->fetchColumn();
    } catch (PDOException $e) {
        error_log("Get unread count error: " . $e->getMessage());
        return 0;
    }
}

/**
 * Mark notification as read
 * @param int $notificationId Notification ID
 * @return bool
 */
function mark_notification_read(int $notificationId): bool
{
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("UPDATE notifications SET is_read = 1 WHERE notification_id = ?");
        return $stmt->execute([$notificationId]);
    } catch (PDOException $e) {
        error_log("Mark notification read error: " . $e->getMessage());
        return false;
    }
}

/**
 * Mark all notifications as read for current user
 * @return bool
 */
function mark_all_notifications_read(): bool
{
    global $pdo;
    
    $userId = null;
    $userType = null;
    
    if (isset($_SESSION['bhw_id'])) {
        $userId = $_SESSION['bhw_id'];
        $userType = 'bhw';
    } elseif (isset($_SESSION['patient_id'])) {
        $userId = $_SESSION['patient_id'];
        $userType = 'patient';
    }
    
    if (!$userId) {
        return false;
    }
    
    try {
        $stmt = $pdo->prepare("
            UPDATE notifications SET is_read = 1
            WHERE user_id = ? AND user_type = ? AND is_read = 0
        ");
        return $stmt->execute([$userId, $userType]);
    } catch (PDOException $e) {
        error_log("Mark all notifications read error: " . $e->getMessage());
        return false;
    }
}

/**
 * Initialize user session with preferences
 * Call this after successful login
 */
function init_user_session(): void
{
    // Get user preferences
    $prefs = get_user_preferences();
    
    // Initialize language
    init_translations($prefs['language'] ?? 'en');
    
    // Store theme preference in session for use in templates
    $_SESSION['theme'] = $prefs['theme'] ?? 'light';
    $_SESSION['language'] = $prefs['language'] ?? 'en';
}

/**
 * Get current user's role display name
 * @return string
 */
function get_role_display_name(): string
{
    // Check both possible session keys for backward compatibility
    $role = $_SESSION['role'] ?? $_SESSION['bhw_role'] ?? null;
    
    if (!$role) {
        return __('roles.guest') ?: 'Guest';
    }
    
    $roleKey = 'roles.' . strtolower($role);
    $translated = __($roleKey);
    
    return $translated ?: ucfirst($role);
}
