<?php
/**
 * Database Backup Action Handler (Super Admin Only)
 * E-BHM Connect
 * 
 * Handles create, download, restore, and delete backup operations
 */

// Ensure session is started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/auth_helpers.php';
require_once __DIR__ . '/../includes/security_helper.php';

// Require superadmin access
if (!is_superadmin()) {
    $_SESSION['flash_error'] = 'You do not have permission to perform this action.';
    header('Location: ' . BASE_URL . 'admin-dashboard');
    exit;
}

$action = $_REQUEST['action'] ?? '';
$backupDir = __DIR__ . '/../backups';

// Ensure backup directory exists
if (!is_dir($backupDir)) {
    mkdir($backupDir, 0755, true);
}

// Protect backup directory with .htaccess
$htaccessPath = $backupDir . '/.htaccess';
if (!file_exists($htaccessPath)) {
    file_put_contents($htaccessPath, "Deny from all\n");
}

switch ($action) {
    case 'create':
        createBackup();
        break;
    case 'download':
        downloadBackup();
        break;
    case 'restore':
        restoreBackup();
        break;
    case 'delete':
        deleteBackup();
        break;
    default:
        $_SESSION['flash_error'] = 'Invalid action.';
        header('Location: ' . BASE_URL . 'admin-db-backup');
        exit;
}

/**
 * Create a new database backup
 */
function createBackup(): void
{
    global $pdo, $backupDir;
    
    // Verify CSRF token
    if (!validate_csrf_token($_POST['csrf_token'] ?? '')) {
        $_SESSION['flash_error'] = 'Invalid security token. Please try again.';
        header('Location: ' . BASE_URL . 'admin-db-backup');
        exit;
    }
    
    try {
        // Get database credentials from config (set in database.php)
        global $DB_HOST, $DB_NAME, $DB_USER, $DB_PASS;
        
        // Fallback to environment variables if config not available
        $host = $DB_HOST ?? $_ENV['DB_HOST'] ?? 'localhost';
        $dbname = $DB_NAME ?? $_ENV['DB_NAME'] ?? 'e-bhw_connect';
        $user = $DB_USER ?? $_ENV['DB_USER'] ?? 'root';
        $pass = $DB_PASS ?? $_ENV['DB_PASS'] ?? '';
        
        // Generate backup filename
        $timestamp = date('Y-m-d_H-i-s');
        $filename = "backup_{$dbname}_{$timestamp}.sql";
        $filepath = $backupDir . '/' . $filename;
        
        // Get all tables
        $tables = [];
        $stmt = $pdo->query("SHOW TABLES");
        while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $tables[] = $row[0];
        }
        
        // Build SQL backup
        $sql = "-- E-BHM Connect Database Backup\n";
        $sql .= "-- Generated: " . date('Y-m-d H:i:s') . "\n";
        $sql .= "-- Database: {$dbname}\n\n";
        $sql .= "SET FOREIGN_KEY_CHECKS=0;\n\n";
        
        foreach ($tables as $table) {
            // Get table structure
            $stmt = $pdo->query("SHOW CREATE TABLE `{$table}`");
            $row = $stmt->fetch(PDO::FETCH_NUM);
            
            $sql .= "-- Table: {$table}\n";
            $sql .= "DROP TABLE IF EXISTS `{$table}`;\n";
            $sql .= $row[1] . ";\n\n";
            
            // Get table data
            $stmt = $pdo->query("SELECT * FROM `{$table}`");
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            if (!empty($rows)) {
                $columns = array_keys($rows[0]);
                $columnList = '`' . implode('`, `', $columns) . '`';
                
                foreach ($rows as $row) {
                    $values = array_map(function($value) use ($pdo) {
                        if ($value === null) {
                            return 'NULL';
                        }
                        return $pdo->quote($value);
                    }, array_values($row));
                    
                    $sql .= "INSERT INTO `{$table}` ({$columnList}) VALUES (" . implode(', ', $values) . ");\n";
                }
                $sql .= "\n";
            }
        }
        
        $sql .= "SET FOREIGN_KEY_CHECKS=1;\n";
        
        // Write to file
        if (file_put_contents($filepath, $sql) === false) {
            throw new Exception('Failed to write backup file.');
        }
        
        // Log the action
        log_audit('backup_create', 'database', null, [
            'filename' => $filename,
            'size' => filesize($filepath),
            'tables' => count($tables)
        ]);
        
        $_SESSION['flash_success'] = 'Backup created successfully: ' . $filename;
        
    } catch (Throwable $e) {
        error_log('Backup creation error: ' . $e->getMessage());
        $_SESSION['flash_error'] = 'Failed to create backup: ' . $e->getMessage();
    }
    
    header('Location: ' . BASE_URL . 'admin-db-backup');
    exit;
}

/**
 * Download a backup file
 */
function downloadBackup(): void
{
    global $backupDir;
    
    $filename = $_GET['file'] ?? '';
    
    // Validate filename (prevent directory traversal)
    if (empty($filename) || preg_match('/[\/\\\\]/', $filename)) {
        $_SESSION['flash_error'] = 'Invalid file.';
        header('Location: ' . BASE_URL . 'admin-db-backup');
        exit;
    }
    
    $filepath = $backupDir . '/' . $filename;
    
    if (!file_exists($filepath) || pathinfo($filename, PATHINFO_EXTENSION) !== 'sql') {
        $_SESSION['flash_error'] = 'Backup file not found.';
        header('Location: ' . BASE_URL . 'admin-db-backup');
        exit;
    }
    
    // Log the action
    log_audit('backup_download', 'database', null, ['filename' => $filename]);
    
    // Send file
    header('Content-Type: application/sql');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Content-Length: ' . filesize($filepath));
    header('Cache-Control: no-cache, must-revalidate');
    
    readfile($filepath);
    exit;
}

/**
 * Restore database from backup
 * Uses a robust SQL parser that respects quoted strings
 */
function restoreBackup(): void
{
    global $pdo, $backupDir;
    
    // Verify CSRF token
    if (!validate_csrf_token($_POST['csrf_token'] ?? '')) {
        $_SESSION['flash_error'] = 'Invalid security token. Please try again.';
        header('Location: ' . BASE_URL . 'admin-db-backup');
        exit;
    }
    
    // Check for uploaded file
    if (!isset($_FILES['backup_file']) || $_FILES['backup_file']['error'] !== UPLOAD_ERR_OK) {
        $_SESSION['flash_error'] = 'No file uploaded or upload error.';
        header('Location: ' . BASE_URL . 'admin-db-backup');
        exit;
    }
    
    $uploadedFile = $_FILES['backup_file'];
    
    // Validate file extension
    if (pathinfo($uploadedFile['name'], PATHINFO_EXTENSION) !== 'sql') {
        $_SESSION['flash_error'] = 'Only .sql files are allowed.';
        header('Location: ' . BASE_URL . 'admin-db-backup');
        exit;
    }
    
    // Validate file size (max 50MB)
    if ($uploadedFile['size'] > 50 * 1024 * 1024) {
        $_SESSION['flash_error'] = 'File too large. Maximum size is 50MB.';
        header('Location: ' . BASE_URL . 'admin-db-backup');
        exit;
    }
    
    try {
        // Read SQL content
        $sql = file_get_contents($uploadedFile['tmp_name']);
        
        if (empty($sql)) {
            throw new Exception('Backup file is empty.');
        }
        
        // Create a pre-restore backup for safety
        createPreRestoreBackup();
        
        // Execute restore
        $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, true);
        
        // Disable foreign key checks first (critical for restore)
        $pdo->exec("SET FOREIGN_KEY_CHECKS=0");
        
        // Parse SQL into statements using a robust parser
        $statements = parseSqlStatements($sql);
        
        $executedCount = 0;
        $errors = [];
        
        // Note: We don't use a transaction because DDL statements (CREATE TABLE, DROP TABLE)
        // cause implicit commits in MySQL and cannot be rolled back
        
        foreach ($statements as $statement) {
            $statement = trim($statement);
            if (empty($statement)) {
                continue;
            }
            
            try {
                // Convert CREATE TABLE to CREATE TABLE IF NOT EXISTS for safety
                if (preg_match('/^CREATE\s+TABLE\s+(?!IF\s+NOT\s+EXISTS)/i', $statement)) {
                    $statement = preg_replace(
                        '/^CREATE\s+TABLE\s+/i',
                        'CREATE TABLE IF NOT EXISTS ',
                        $statement
                    );
                }
                
                $pdo->exec($statement);
                $executedCount++;
            } catch (PDOException $e) {
                // Log error but continue with other statements
                $shortStatement = substr($statement, 0, 100);
                $errors[] = "Statement failed: {$shortStatement}... - " . $e->getMessage();
                error_log("Restore statement error: " . $e->getMessage());
            }
        }
        
        // Re-enable foreign key checks
        $pdo->exec("SET FOREIGN_KEY_CHECKS=1");
        
        // Log the action
        log_audit('backup_restore', 'database', null, [
            'filename' => $uploadedFile['name'],
            'size' => $uploadedFile['size'],
            'statements' => $executedCount,
            'errors' => count($errors)
        ]);
        
        if (empty($errors)) {
            $_SESSION['flash_success'] = "Database restored successfully from {$uploadedFile['name']}. Executed {$executedCount} statements.";
        } else {
            $_SESSION['flash_success'] = "Database restored with {$executedCount} statements. " . count($errors) . " statements had errors (check logs).";
        }
        
    } catch (Throwable $e) {
        // Re-enable foreign key checks even on error
        try { $pdo->exec("SET FOREIGN_KEY_CHECKS=1"); } catch (Exception $ex) {}
        
        error_log('Database restore error: ' . $e->getMessage());
        $_SESSION['flash_error'] = 'Failed to restore database: ' . $e->getMessage();
    }
    
    header('Location: ' . BASE_URL . 'admin-db-backup');
    exit;
}

/**
 * Parse SQL into individual statements, respecting quoted strings
 * This prevents breaking on semicolons inside text values
 * 
 * @param string $sql The full SQL content
 * @return array Array of individual SQL statements
 */
function parseSqlStatements(string $sql): array
{
    $statements = [];
    $currentStatement = '';
    $inString = false;
    $stringChar = '';
    $length = strlen($sql);
    
    for ($i = 0; $i < $length; $i++) {
        $char = $sql[$i];
        $prevChar = $i > 0 ? $sql[$i - 1] : '';
        
        // Handle string delimiters (single and double quotes)
        if (($char === "'" || $char === '"') && $prevChar !== '\\') {
            if (!$inString) {
                $inString = true;
                $stringChar = $char;
            } elseif ($char === $stringChar) {
                // Check for escaped quote ('' or "")
                $nextChar = $i + 1 < $length ? $sql[$i + 1] : '';
                if ($nextChar === $char) {
                    // Escaped quote, add both and skip next
                    $currentStatement .= $char . $nextChar;
                    $i++;
                    continue;
                }
                $inString = false;
                $stringChar = '';
            }
        }
        
        // Check for statement terminator (semicolon outside quotes)
        if ($char === ';' && !$inString) {
            $trimmed = trim($currentStatement);
            // Skip comments and empty statements
            if (!empty($trimmed) && !preg_match('/^--/', $trimmed) && !preg_match('/^\/\*/', $trimmed)) {
                $statements[] = $trimmed;
            }
            $currentStatement = '';
            continue;
        }
        
        $currentStatement .= $char;
    }
    
    // Add final statement if exists
    $trimmed = trim($currentStatement);
    if (!empty($trimmed) && !preg_match('/^--/', $trimmed) && !preg_match('/^\/\*/', $trimmed)) {
        $statements[] = $trimmed;
    }
    
    return $statements;
}

/**
 * Create a backup before restoring (safety net)
 */
function createPreRestoreBackup(): void
{
    global $pdo, $backupDir;
    
    try {
        $dbname = $_ENV['DB_NAME'] ?? 'ebhm_connect';
        $timestamp = date('Y-m-d_H-i-s');
        $filename = "pre_restore_{$dbname}_{$timestamp}.sql";
        $filepath = $backupDir . '/' . $filename;
        
        // Get all tables
        $tables = [];
        $stmt = $pdo->query("SHOW TABLES");
        while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $tables[] = $row[0];
        }
        
        // Build SQL backup
        $sql = "-- E-BHM Connect Pre-Restore Backup\n";
        $sql .= "-- Generated: " . date('Y-m-d H:i:s') . "\n";
        $sql .= "-- Database: {$dbname}\n\n";
        $sql .= "SET FOREIGN_KEY_CHECKS=0;\n\n";
        
        foreach ($tables as $table) {
            // Get table structure
            $stmt = $pdo->query("SHOW CREATE TABLE `{$table}`");
            $row = $stmt->fetch(PDO::FETCH_NUM);
            
            $sql .= "-- Table: {$table}\n";
            $sql .= "DROP TABLE IF EXISTS `{$table}`;\n";
            $sql .= $row[1] . ";\n\n";
            
            // Get table data
            $stmt = $pdo->query("SELECT * FROM `{$table}`");
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            if (!empty($rows)) {
                $columns = array_keys($rows[0]);
                $columnList = '`' . implode('`, `', $columns) . '`';
                
                foreach ($rows as $row) {
                    $values = array_map(function($value) use ($pdo) {
                        if ($value === null) {
                            return 'NULL';
                        }
                        return $pdo->quote($value);
                    }, array_values($row));
                    
                    $sql .= "INSERT INTO `{$table}` ({$columnList}) VALUES (" . implode(', ', $values) . ");\n";
                }
                $sql .= "\n";
            }
        }
        
        $sql .= "SET FOREIGN_KEY_CHECKS=1;\n";
        
        // Write to file
        file_put_contents($filepath, $sql);
        
    } catch (Throwable $e) {
        error_log('Pre-restore backup failed: ' . $e->getMessage());
        // Don't throw - this is a safety feature, not critical
    }
}

/**
 * Delete a backup file
 */
function deleteBackup(): void
{
    global $backupDir;
    
    // Verify CSRF token
    if (!validate_csrf_token($_POST['csrf_token'] ?? '')) {
        $_SESSION['flash_error'] = 'Invalid security token. Please try again.';
        header('Location: ' . BASE_URL . 'admin-db-backup');
        exit;
    }
    
    $filename = $_POST['file'] ?? '';
    
    // Validate filename (prevent directory traversal)
    if (empty($filename) || preg_match('/[\/\\\\]/', $filename)) {
        $_SESSION['flash_error'] = 'Invalid file.';
        header('Location: ' . BASE_URL . 'admin-db-backup');
        exit;
    }
    
    $filepath = $backupDir . '/' . $filename;
    
    if (!file_exists($filepath) || pathinfo($filename, PATHINFO_EXTENSION) !== 'sql') {
        $_SESSION['flash_error'] = 'Backup file not found.';
        header('Location: ' . BASE_URL . 'admin-db-backup');
        exit;
    }
    
    try {
        if (unlink($filepath)) {
            // Log the action
            log_audit('backup_delete', 'database', null, ['filename' => $filename]);
            $_SESSION['flash_success'] = 'Backup deleted: ' . $filename;
        } else {
            throw new Exception('Could not delete file.');
        }
    } catch (Throwable $e) {
        error_log('Backup delete error: ' . $e->getMessage());
        $_SESSION['flash_error'] = 'Failed to delete backup: ' . $e->getMessage();
    }
    
    header('Location: ' . BASE_URL . 'admin-db-backup');
    exit;
}
