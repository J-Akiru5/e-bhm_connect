<?php
/**
 * Translation Helper
 * 
 * Provides internationalization (i18n) support for E-BHM Connect.
 * Supports English (en) and Tagalog (tl) languages.
 */

// Prevent direct access
if (!defined('BASE_URL')) {
    die('Direct access not allowed');
}

// Global translations array
$GLOBALS['translations'] = [];
$GLOBALS['current_language'] = 'en';

/**
 * Initialize the translation system
 * 
 * @param string $lang Language code ('en' or 'tl')
 * @return void
 */
function init_translations($lang = null) {
    // Determine language from various sources
    if ($lang === null) {
        // 1. Check session
        if (isset($_SESSION['user_language'])) {
            $lang = $_SESSION['user_language'];
        }
        // 2. Check cookie
        elseif (isset($_COOKIE['ebhm_language'])) {
            $lang = $_COOKIE['ebhm_language'];
        }
        // 3. Check database preferences (if user is logged in)
        elseif (isset($_SESSION['bhw_id']) || isset($_SESSION['patient_user_id'])) {
            $lang = get_user_language_preference();
        }
        // 4. Default to English
        else {
            $lang = 'en';
        }
    }
    
    // Validate language
    $supported = ['en', 'tl'];
    if (!in_array($lang, $supported)) {
        $lang = 'en';
    }
    
    $GLOBALS['current_language'] = $lang;
    
    // Load translation file
    $lang_file = __DIR__ . '/lang/' . $lang . '.php';
    if (file_exists($lang_file)) {
        $GLOBALS['translations'] = include $lang_file;
    } else {
        // Fallback to English
        $en_file = __DIR__ . '/lang/en.php';
        if (file_exists($en_file)) {
            $GLOBALS['translations'] = include $en_file;
        }
    }
}

/**
 * Get user language preference from database
 * 
 * @return string Language code
 */
function get_user_language_preference() {
    global $pdo;
    
    try {
        if (isset($_SESSION['bhw_id'])) {
            $stmt = $pdo->prepare("SELECT language FROM user_preferences WHERE user_id = ? AND user_type = 'bhw'");
            $stmt->execute([$_SESSION['bhw_id']]);
        } elseif (isset($_SESSION['patient_user_id'])) {
            $stmt = $pdo->prepare("SELECT language FROM user_preferences WHERE user_id = ? AND user_type = 'patient'");
            $stmt->execute([$_SESSION['patient_user_id']]);
        } else {
            return 'en';
        }
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result['language'] : 'en';
    } catch (Exception $e) {
        return 'en';
    }
}

/**
 * Translate a string
 * 
 * @param string $key Translation key
 * @param array $replacements Optional placeholder replacements
 * @return string Translated string or key if not found
 */
function __($key, $replacements = []) {
    $translations = $GLOBALS['translations'] ?? [];
    
    // Get translation or fallback to key
    $text = $translations[$key] ?? $key;
    
    // Handle nested keys (e.g., 'nav.dashboard')
    if (strpos($key, '.') !== false && $text === $key) {
        $parts = explode('.', $key);
        $current = $translations;
        foreach ($parts as $part) {
            if (isset($current[$part])) {
                $current = $current[$part];
            } else {
                $current = $key;
                break;
            }
        }
        $text = is_string($current) ? $current : $key;
    }
    
    // Replace placeholders
    if (!empty($replacements)) {
        foreach ($replacements as $placeholder => $value) {
            $text = str_replace(':' . $placeholder, $value, $text);
        }
    }
    
    return $text;
}

/**
 * Echo translated string (shorthand)
 * 
 * @param string $key Translation key
 * @param array $replacements Optional placeholder replacements
 * @return void
 */
function _e($key, $replacements = []) {
    echo __($key, $replacements);
}

/**
 * Get current language code
 * 
 * @return string Language code
 */
function get_current_language() {
    return $GLOBALS['current_language'] ?? 'en';
}

/**
 * Set user language preference
 * 
 * @param string $lang Language code ('en' or 'tl')
 * @return bool Success
 */
function set_user_language($lang) {
    global $pdo;
    
    $supported = ['en', 'tl'];
    if (!in_array($lang, $supported)) {
        return false;
    }
    
    // Store in session
    $_SESSION['user_language'] = $lang;
    
    // Store in cookie (30 days)
    setcookie('ebhm_language', $lang, time() + (30 * 24 * 60 * 60), '/');
    
    // Store in database if logged in
    try {
        if (isset($_SESSION['bhw_id'])) {
            $stmt = $pdo->prepare("
                INSERT INTO user_preferences (user_id, user_type, language) 
                VALUES (?, 'bhw', ?)
                ON DUPLICATE KEY UPDATE language = ?
            ");
            $stmt->execute([$_SESSION['bhw_id'], $lang, $lang]);
        } elseif (isset($_SESSION['patient_user_id'])) {
            $stmt = $pdo->prepare("
                INSERT INTO user_preferences (user_id, user_type, language) 
                VALUES (?, 'patient', ?)
                ON DUPLICATE KEY UPDATE language = ?
            ");
            $stmt->execute([$_SESSION['patient_user_id'], $lang, $lang]);
        }
    } catch (Exception $e) {
        error_log('Error saving language preference: ' . $e->getMessage());
    }
    
    // Reinitialize translations
    init_translations($lang);
    
    return true;
}

/**
 * Get available languages
 * 
 * @return array Language options
 */
function get_available_languages() {
    return [
        'en' => [
            'code' => 'en',
            'name' => 'English',
            'native' => 'English',
            'flag' => '🇺🇸'
        ],
        'tl' => [
            'code' => 'tl',
            'name' => 'Tagalog',
            'native' => 'Tagalog',
            'flag' => '🇵🇭'
        ]
    ];
}

/**
 * Format date according to current language
 * 
 * @param string $date Date string or timestamp
 * @param string $format Format style ('short', 'medium', 'long')
 * @return string Formatted date
 */
function format_date_localized($date, $format = 'medium') {
    $lang = get_current_language();
    $timestamp = is_numeric($date) ? $date : strtotime($date);
    
    if ($lang === 'tl') {
        // Tagalog months
        $months_tl = [
            1 => 'Enero', 2 => 'Pebrero', 3 => 'Marso', 4 => 'Abril',
            5 => 'Mayo', 6 => 'Hunyo', 7 => 'Hulyo', 8 => 'Agosto',
            9 => 'Setyembre', 10 => 'Oktubre', 11 => 'Nobyembre', 12 => 'Disyembre'
        ];
        
        $month = $months_tl[(int)date('n', $timestamp)];
        $day = date('j', $timestamp);
        $year = date('Y', $timestamp);
        
        switch ($format) {
            case 'short':
                return date('m/d/Y', $timestamp);
            case 'long':
                return "$month $day, $year";
            default: // medium
                return "$month $day, $year";
        }
    }
    
    // English format
    switch ($format) {
        case 'short':
            return date('m/d/Y', $timestamp);
        case 'long':
            return date('F j, Y', $timestamp);
        default: // medium
            return date('M j, Y', $timestamp);
    }
}
