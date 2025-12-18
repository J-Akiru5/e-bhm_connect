<?php
/**
 * E-BHM Connect - Security Helper
 * Provides CSRF protection, rate limiting, session security, and input sanitization
 * 
 * @package E-BHM Connect
 * @since 1.0.0
 */

require_once __DIR__ . '/../config/database.php';

/**
 * Configure secure session settings
 * Call this before session_start()
 */
function configure_secure_session(): void
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        return; // Session already started
    }
    
    $secure = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on';
    
    session_set_cookie_params([
        'lifetime' => 0, // Session cookie (expires when browser closes)
        'path' => '/',
        'domain' => '', // Current domain only
        'secure' => $secure, // Only send over HTTPS if available
        'httponly' => true, // Prevent JavaScript access
        'samesite' => 'Strict' // Prevent CSRF via cookies
    ]);
    
    // Use stronger session ID
    ini_set('session.use_strict_mode', '1');
    ini_set('session.use_only_cookies', '1');
}

/**
 * Set security headers for all responses
 */
function set_security_headers(): void
{
    // Prevent MIME type sniffing
    header('X-Content-Type-Options: nosniff');
    
    // Prevent clickjacking
    header('X-Frame-Options: SAMEORIGIN');
    
    // XSS protection for older browsers
    header('X-XSS-Protection: 1; mode=block');
    
    // Referrer policy
    header('Referrer-Policy: strict-origin-when-cross-origin');
    
    // Permissions policy (restrict powerful features)
    header('Permissions-Policy: camera=(), microphone=(), geolocation=()');
}

/**
 * Generate a CSRF token and store in session
 * 
 * @return string The generated token
 */
function generate_csrf_token(): string
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
    
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        $_SESSION['csrf_token_time'] = time();
    }
    
    // Regenerate token if older than 1 hour
    if (isset($_SESSION['csrf_token_time']) && (time() - $_SESSION['csrf_token_time']) > 3600) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        $_SESSION['csrf_token_time'] = time();
    }
    
    return $_SESSION['csrf_token'];
}

/**
 * Validate a CSRF token from request
 * 
 * @param string|null $token The token from the request
 * @return bool True if valid, false otherwise
 */
function validate_csrf_token(?string $token): bool
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
    
    if (empty($token) || empty($_SESSION['csrf_token'])) {
        return false;
    }
    
    return hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Generate hidden input field with CSRF token
 * 
 * @return string HTML input element
 */
function csrf_input(): string
{
    $token = generate_csrf_token();
    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token, ENT_QUOTES, 'UTF-8') . '">';
}

/**
 * Validate CSRF and die with error if invalid
 * Use at the start of POST handlers
 */
function require_csrf(): void
{
    $token = $_POST['csrf_token'] ?? $_GET['csrf_token'] ?? null;
    
    if (!validate_csrf_token($token)) {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        $_SESSION['form_error'] = 'Invalid security token. Please refresh the page and try again.';
        
        // Redirect back or to home
        $referer = $_SERVER['HTTP_REFERER'] ?? '/';
        header('Location: ' . $referer);
        exit();
    }
}

/**
 * Check rate limit for an action
 * 
 * @param string $action The action being rate limited (e.g., 'login_bhw', 'login_patient')
 * @param string $identifier The identifier (e.g., IP address, username)
 * @param int $maxAttempts Maximum attempts allowed
 * @param int $windowSeconds Time window in seconds
 * @return array ['allowed' => bool, 'remaining' => int, 'reset_time' => int]
 */
function check_rate_limit(string $action, string $identifier, int $maxAttempts = 5, int $windowSeconds = 900): array
{
    global $pdo;
    
    $key = $action . ':' . $identifier;
    $now = time();
    $windowStart = $now - $windowSeconds;
    
    try {
        // Clean up old entries
        $cleanup = $pdo->prepare('DELETE FROM rate_limits WHERE expires_at < :now');
        $cleanup->execute([':now' => date('Y-m-d H:i:s', $now)]);
        
        // Get current attempts
        $stmt = $pdo->prepare('
            SELECT attempts, first_attempt_at, expires_at 
            FROM rate_limits 
            WHERE rate_key = :key AND expires_at > :now
            LIMIT 1
        ');
        $stmt->execute([':key' => $key, ':now' => date('Y-m-d H:i:s', $now)]);
        $record = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$record) {
            // No record exists, user is allowed
            return [
                'allowed' => true,
                'remaining' => $maxAttempts,
                'reset_time' => $now + $windowSeconds
            ];
        }
        
        $attempts = (int)$record['attempts'];
        $expiresAt = strtotime($record['expires_at']);
        $remaining = max(0, $maxAttempts - $attempts);
        
        return [
            'allowed' => $attempts < $maxAttempts,
            'remaining' => $remaining,
            'reset_time' => $expiresAt
        ];
        
    } catch (Throwable $e) {
        error_log('Rate limit check error: ' . $e->getMessage());
        // Fail open - allow the request if there's a database error
        return [
            'allowed' => true,
            'remaining' => $maxAttempts,
            'reset_time' => $now + $windowSeconds
        ];
    }
}

/**
 * Record a rate limit attempt
 * 
 * @param string $action The action being rate limited
 * @param string $identifier The identifier
 * @param int $windowSeconds Time window in seconds
 */
function record_rate_limit(string $action, string $identifier, int $windowSeconds = 900): void
{
    global $pdo;
    
    $key = $action . ':' . $identifier;
    $now = time();
    $expiresAt = date('Y-m-d H:i:s', $now + $windowSeconds);
    
    try {
        // Try to update existing record
        $stmt = $pdo->prepare('
            UPDATE rate_limits 
            SET attempts = attempts + 1 
            WHERE rate_key = :key AND expires_at > :now
        ');
        $stmt->execute([':key' => $key, ':now' => date('Y-m-d H:i:s', $now)]);
        
        if ($stmt->rowCount() === 0) {
            // No existing record, insert new one
            $insert = $pdo->prepare('
                INSERT INTO rate_limits (rate_key, attempts, first_attempt_at, expires_at) 
                VALUES (:key, 1, :first, :expires)
            ');
            $insert->execute([
                ':key' => $key,
                ':first' => date('Y-m-d H:i:s', $now),
                ':expires' => $expiresAt
            ]);
        }
    } catch (Throwable $e) {
        error_log('Rate limit record error: ' . $e->getMessage());
        // Silently fail - don't break the application
    }
}

/**
 * Clear rate limit for an action/identifier (e.g., after successful login)
 * 
 * @param string $action The action
 * @param string $identifier The identifier
 */
function clear_rate_limit(string $action, string $identifier): void
{
    global $pdo;
    
    $key = $action . ':' . $identifier;
    
    try {
        $stmt = $pdo->prepare('DELETE FROM rate_limits WHERE rate_key = :key');
        $stmt->execute([':key' => $key]);
    } catch (Throwable $e) {
        error_log('Rate limit clear error: ' . $e->getMessage());
    }
}

/**
 * Get client IP address (handles proxies)
 * 
 * @return string IP address
 */
function get_client_ip(): string
{
    $headers = ['HTTP_CF_CONNECTING_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_REAL_IP', 'REMOTE_ADDR'];
    
    foreach ($headers as $header) {
        if (!empty($_SERVER[$header])) {
            $ip = $_SERVER[$header];
            // X-Forwarded-For can contain multiple IPs, get the first one
            if (strpos($ip, ',') !== false) {
                $ip = trim(explode(',', $ip)[0]);
            }
            if (filter_var($ip, FILTER_VALIDATE_IP)) {
                return $ip;
            }
        }
    }
    
    return '127.0.0.1';
}

/**
 * Sanitize output for HTML display (prevent XSS)
 * 
 * @param string|null $data The data to sanitize
 * @return string Sanitized string
 */
function h(?string $data): string
{
    if ($data === null) {
        return '';
    }
    return htmlspecialchars($data, ENT_QUOTES | ENT_HTML5, 'UTF-8');
}

/**
 * Sanitize output for JavaScript strings
 * 
 * @param string|null $data The data to sanitize
 * @return string JSON-encoded string (safe for JS)
 */
function js(?string $data): string
{
    if ($data === null) {
        return '""';
    }
    return json_encode($data, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP);
}

/**
 * Regenerate session ID (call after login)
 * Prevents session fixation attacks
 */
function regenerate_session(): void
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        // Regenerate session ID but keep session data
        session_regenerate_id(true);
        
        // Update CSRF token
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        $_SESSION['csrf_token_time'] = time();
    }
}

/**
 * Validate and sanitize email
 * 
 * @param string $email Raw email input
 * @return string|false Sanitized email or false if invalid
 */
function validate_email(string $email)
{
    $email = trim(strtolower($email));
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

/**
 * Validate phone number (Philippine format)
 * 
 * @param string $phone Raw phone input
 * @return string|false Formatted phone or false if invalid
 */
function validate_phone(string $phone)
{
    // Remove all non-numeric characters
    $phone = preg_replace('/[^0-9]/', '', $phone);
    
    // Philippine mobile: 09XXXXXXXXX (11 digits) or +639XXXXXXXXX (12 digits)
    if (strlen($phone) === 11 && substr($phone, 0, 2) === '09') {
        return $phone;
    }
    
    if (strlen($phone) === 12 && substr($phone, 0, 3) === '639') {
        return '0' . substr($phone, 2); // Convert to 09XX format
    }
    
    if (strlen($phone) === 10 && substr($phone, 0, 1) === '9') {
        return '0' . $phone; // Add leading 0
    }
    
    return false;
}

/**
 * Validate date string
 * 
 * @param string $date Date string (Y-m-d format expected)
 * @return string|false Valid date or false
 */
function validate_date(string $date)
{
    $d = DateTime::createFromFormat('Y-m-d', $date);
    if ($d && $d->format('Y-m-d') === $date) {
        return $date;
    }
    return false;
}
