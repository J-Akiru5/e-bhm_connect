<?php
/**
 * Database Backup Action Handler (Super Admin Only)
 * E-BHM Connect
 * 
 * Handles create, download, restore, and delete backup operations
 */

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
        // Get database credentials from environment
        $host = $_ENV['DB_HOST'] ?? 'localhost';
        $dbname = $_ENV['DB_NAME'] ?? 'ebhm_connect';
        $user = $_ENV['DB_USER'] ?? 'root';
        $pass = $_ENV['DB_PASS'] ?? '';
        
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
        
        // Create a backup before restore
        $preRestoreBackup = "pre_restore_" . date('Y-m-d_H-i-s') . ".sql";
        
        // Execute restore
        $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, true);
        
        // Split SQL into statements
        $statements = array_filter(
            array_map('trim', explode(';', $sql)),
            fn($s) => !empty($s) && !str_starts_with($s, '--')
        );
        
        $pdo->beginTransaction();
        
        foreach ($statements as $statement) {
            if (!empty(trim($statement))) {
                $pdo->exec($statement);
            }
        }
        
        $pdo->commit();
        
        // Log the action
        log_audit('backup_restore', 'database', null, [
            'filename' => $uploadedFile['name'],
            'size' => $uploadedFile['size'],
            'statements' => count($statements)
        ]);
        
        $_SESSION['flash_success'] = 'Database restored successfully from ' . $uploadedFile['name'];
        
    } catch (Throwable $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        error_log('Database restore error: ' . $e->getMessage());
        $_SESSION['flash_error'] = 'Failed to restore database: ' . $e->getMessage();
    }
    
    header('Location: ' . BASE_URL . 'admin-db-backup');
    exit;
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
