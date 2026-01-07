<?php
/**
 * Record History Helper Functions
 * E-BHM Connect - Full Modification History Tracking
 * 
 * Provides functions to:
 * - Log record changes (before/after values)
 * - Retrieve change history for a record
 * - Format change data for display
 */

/**
 * Log a record change with before/after values
 * 
 * @param PDO $pdo Database connection
 * @param string $tableName Name of the table being modified
 * @param int $recordId Primary key of the record
 * @param string $action Type of action: 'insert', 'update', 'delete'
 * @param array|null $oldValues Previous values (null for inserts)
 * @param array|null $newValues New values (null for deletes)
 * @return bool Success status
 */
function log_record_change($pdo, $tableName, $recordId, $action, $oldValues = null, $newValues = null) {
    try {
        $stmt = $pdo->prepare("INSERT INTO record_changes 
            (table_name, record_id, action, changed_by, old_values, new_values, ip_address)
            VALUES (?, ?, ?, ?, ?, ?, ?)");
        
        return $stmt->execute([
            $tableName,
            $recordId,
            $action,
            $_SESSION['bhw_id'] ?? null,
            $oldValues ? json_encode($oldValues) : null,
            $newValues ? json_encode($newValues) : null,
            $_SERVER['REMOTE_ADDR'] ?? null
        ]);
    } catch (PDOException $e) {
        error_log("log_record_change error: " . $e->getMessage());
        return false;
    }
}

/**
 * Get the current record data before an update
 * 
 * @param PDO $pdo Database connection
 * @param string $tableName Table name
 * @param string $primaryKey Primary key column name
 * @param int $recordId Record ID
 * @return array|null Current record data or null
 */
function get_record_before_update($pdo, $tableName, $primaryKey, $recordId) {
    try {
        // Whitelist of allowed tables for security
        $allowedTables = [
            'pregnancy_tracking' => 'pregnancy_id',
            'child_care_records' => 'child_care_id',
            'natality_records' => 'natality_id',
            'mortality_records' => 'mortality_id',
            'chronic_disease_masterlist' => 'chronic_id',
            'ntp_client_monitoring' => 'ntp_id',
            'wra_tracking' => 'wra_id',
            'patients' => 'patient_id'
        ];
        
        if (!isset($allowedTables[$tableName])) {
            return null;
        }
        
        $pk = $allowedTables[$tableName];
        $stmt = $pdo->prepare("SELECT * FROM `$tableName` WHERE `$pk` = ?");
        $stmt->execute([$recordId]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    } catch (PDOException $e) {
        error_log("get_record_before_update error: " . $e->getMessage());
        return null;
    }
}

/**
 * Get change history for a specific record
 * 
 * @param PDO $pdo Database connection
 * @param string $tableName Table name
 * @param int $recordId Record ID
 * @param int $limit Number of records to fetch
 * @return array Change history records
 */
function get_record_history($pdo, $tableName, $recordId, $limit = 50) {
    try {
        $stmt = $pdo->prepare("SELECT rc.*, b.full_name as changed_by_name 
            FROM record_changes rc
            LEFT JOIN bhw_users b ON rc.changed_by = b.bhw_id
            WHERE rc.table_name = ? AND rc.record_id = ?
            ORDER BY rc.changed_at DESC
            LIMIT ?");
        $stmt->execute([$tableName, $recordId, $limit]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("get_record_history error: " . $e->getMessage());
        return [];
    }
}

/**
 * Compare old and new values to find what changed
 * 
 * @param array $oldValues Previous values
 * @param array $newValues New values
 * @return array Array of changed fields with old and new values
 */
function compare_record_changes($oldValues, $newValues) {
    $changes = [];
    
    // Decode JSON if strings
    if (is_string($oldValues)) {
        $oldValues = json_decode($oldValues, true) ?? [];
    }
    if (is_string($newValues)) {
        $newValues = json_decode($newValues, true) ?? [];
    }
    
    // Get all keys from both arrays
    $allKeys = array_unique(array_merge(array_keys($oldValues ?? []), array_keys($newValues ?? [])));
    
    // Skip internal fields
    $skipFields = ['updated_at', 'created_at', 'password_hash', 'password'];
    
    foreach ($allKeys as $key) {
        if (in_array($key, $skipFields)) continue;
        
        $oldVal = $oldValues[$key] ?? null;
        $newVal = $newValues[$key] ?? null;
        
        // Compare values
        if ($oldVal !== $newVal) {
            $changes[] = [
                'field' => format_field_name($key),
                'field_key' => $key,
                'old' => $oldVal ?? '-',
                'new' => $newVal ?? '-'
            ];
        }
    }
    
    return $changes;
}

/**
 * Format a field name for display (snake_case to Title Case)
 * 
 * @param string $fieldName Field name
 * @return string Formatted field name
 */
function format_field_name($fieldName) {
    // Replace underscores with spaces and capitalize
    $formatted = ucwords(str_replace('_', ' ', $fieldName));
    
    // Handle common abbreviations
    $replacements = [
        'Bhw' => 'BHW',
        'Id' => 'ID',
        'Dob' => 'DOB',
        'Ntp' => 'NTP',
        'Wra' => 'WRA',
        'Nhts' => 'NHTS'
    ];
    
    return str_replace(array_keys($replacements), array_values($replacements), $formatted);
}

/**
 * Get action display label with color class
 * 
 * @param string $action Action type
 * @return array ['label' => string, 'class' => string]
 */
function get_action_display($action) {
    $displays = [
        'insert' => ['label' => 'Created', 'class' => 'badge-success'],
        'update' => ['label' => 'Updated', 'class' => 'badge-primary'],
        'delete' => ['label' => 'Deleted', 'class' => 'badge-danger']
    ];
    
    return $displays[$action] ?? ['label' => ucfirst($action), 'class' => 'badge-secondary'];
}

/**
 * Get table display name
 * 
 * @param string $tableName Database table name
 * @return string Human-readable table name
 */
function get_table_display_name($tableName) {
    $names = [
        'pregnancy_tracking' => 'Pregnancy Tracking',
        'child_care_records' => 'Child Care',
        'natality_records' => 'Natality (Birth)',
        'mortality_records' => 'Mortality (Death)',
        'chronic_disease_masterlist' => 'Chronic Disease',
        'ntp_client_monitoring' => 'NTP Monitoring',
        'wra_tracking' => 'WRA Tracking',
        'patients' => 'Patient'
    ];
    
    return $names[$tableName] ?? ucwords(str_replace('_', ' ', $tableName));
}
