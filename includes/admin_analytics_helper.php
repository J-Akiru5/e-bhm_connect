<?php
/**
 * Admin analytics helper
 *
 * Provides dashboard data functions for the admin dashboard.
 *
 * Functions:
 * - fetch_dashboard_data($pdo): Main dashboard statistics
 * - get_inventory_chart_data($pdo): Medicine inventory for bar chart
 * - get_recent_visits($pdo, $limit): Recent patient visits
 * - get_recent_audit_logs($pdo, $limit): Recent audit log entries
 */

if (!function_exists('fetch_dashboard_data')) {
    function fetch_dashboard_data($pdo)
    {
        // Default safe response
        $result = [
            'total_patients' => 0,
            'total_bhws' => 0,
            'total_inventory' => 0,
            'total_announcements' => 0,
            'total_programs' => 0,
            'low_stock_items' => 0,
            'sms_stats' => [
                'sent' => 0,
                'failed' => 0,
                'pending' => 0,
            ],
            'recent_registrations' => [],
        ];

        try {
            // Basic type check
            if (!is_object($pdo) || (!($pdo instanceof PDO) && !method_exists($pdo, 'query'))) {
                throw new InvalidArgumentException('A valid PDO instance is required.');
            }

            // Total patients
            $stmt = $pdo->query('SELECT COUNT(*) FROM patients');
            $result['total_patients'] = (int) $stmt->fetchColumn();

            // Total BHWs
            $stmt = $pdo->query('SELECT COUNT(*) FROM bhw_users');
            $result['total_bhws'] = (int) $stmt->fetchColumn();

            // Total inventory items
            try {
                $stmt = $pdo->query('SELECT COUNT(*) FROM medication_inventory');
                $result['total_inventory'] = (int) $stmt->fetchColumn();
                
                // Low stock items (below alert threshold or <= 10 if no threshold set)
                $stmt = $pdo->query('SELECT COUNT(*) FROM medication_inventory WHERE quantity_in_stock <= COALESCE(stock_alert_limit, 10)');
                $result['low_stock_items'] = (int) $stmt->fetchColumn();
            } catch (Exception $e) {
                // Table might not exist
            }
            
            // Total announcements
            try {
                $stmt = $pdo->query('SELECT COUNT(*) FROM announcements');
                $result['total_announcements'] = (int) $stmt->fetchColumn();
            } catch (Exception $e) {
                // Table might not exist
            }
            
            // Total programs
            try {
                $stmt = $pdo->query('SELECT COUNT(*) FROM health_programs');
                $result['total_programs'] = (int) $stmt->fetchColumn();
            } catch (Exception $e) {
                // Table might not exist
            }

            // SMS stats grouped by status
            try {
                $smsStmt = $pdo->query("SELECT status, COUNT(*) AS count FROM sms_queue GROUP BY status");
                $rows = $smsStmt->fetchAll(PDO::FETCH_ASSOC);
                // normalize statuses to expected keys while preserving any other status values
                $stats = [
                    'sent' => 0,
                    'failed' => 0,
                    'pending' => 0,
                ];
                foreach ($rows as $r) {
                    $status = isset($r['status']) ? strtolower((string) $r['status']) : '';
                    $cnt = isset($r['count']) ? (int) $r['count'] : 0;
                    if ($status === '') continue;
                    if (array_key_exists($status, $stats)) {
                        $stats[$status] = $cnt;
                    } else {
                        // add other statuses dynamically
                        $stats[$status] = $cnt;
                    }
                }
                $result['sms_stats'] = $stats;
            } catch (Exception $e) {
                // sms_queue table might not exist yet
            }

            // Recent registrations: last 6 months (including current month)
            // Build the months skeleton so months with zero appear
            $months = [];
            $now = new DateTimeImmutable('now');
            // last 5 months + current = 6 months window
            for ($i = 5; $i >= 0; $i--) {
                $m = $now->modify("-{$i} months");
                $key = $m->format('Y-m');
                $months[$key] = [
                    'month' => $m->format('F Y'),
                    'count' => 0,
                ];
            }

            // Query counts grouped by year-month within the same window
            $sql = "SELECT DATE_FORMAT(created_at, '%Y-%m') AS ym, COUNT(*) AS count
                    FROM patients
                    WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 5 MONTH)
                    GROUP BY ym
                    ORDER BY ym ASC";
            $regStmt = $pdo->prepare($sql);
            $regStmt->execute();
            $regs = $regStmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($regs as $r) {
                $ym = $r['ym'] ?? null;
                $cnt = isset($r['count']) ? (int) $r['count'] : 0;
                if ($ym !== null && isset($months[$ym])) {
                    $months[$ym]['count'] = $cnt;
                }
            }

            // final ordered list
            $result['recent_registrations'] = array_values($months);

        } catch (Exception $e) {
            // Log the error for debugging, but return a safe empty result to the caller
            error_log('[admin_analytics_helper] ' . $e->getMessage());
            // return defaults
            return $result;
        }

        return $result;
    }
}

/**
 * Get inventory data for bar chart
 * Returns top items by quantity with their stock levels
 */
if (!function_exists('get_inventory_chart_data')) {
    function get_inventory_chart_data($pdo, $limit = 10)
    {
        $result = [
            'labels' => [],
            'quantities' => [],
            'alert_levels' => [],
            'colors' => [],
        ];
        
        try {
            $stmt = $pdo->prepare("
                SELECT 
                    item_name,
                    quantity_in_stock,
                    COALESCE(stock_alert_limit, 10) as alert_limit
                FROM medication_inventory
                ORDER BY quantity_in_stock ASC
                LIMIT ?
            ");
            $stmt->execute([$limit]);
            $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            foreach ($items as $item) {
                $result['labels'][] = $item['item_name'];
                $result['quantities'][] = (int)$item['quantity_in_stock'];
                $result['alert_levels'][] = (int)$item['alert_limit'];
                
                // Color based on stock level
                $qty = (int)$item['quantity_in_stock'];
                $alert = (int)$item['alert_limit'];
                if ($qty <= $alert) {
                    $result['colors'][] = 'rgba(239, 68, 68, 0.8)'; // Red - low stock
                } elseif ($qty <= $alert * 2) {
                    $result['colors'][] = 'rgba(245, 158, 11, 0.8)'; // Yellow - warning
                } else {
                    $result['colors'][] = 'rgba(32, 201, 151, 0.8)'; // Green - good
                }
            }
        } catch (Exception $e) {
            error_log('[get_inventory_chart_data] ' . $e->getMessage());
        }
        
        return $result;
    }
}

/**
 * Get recent patient visits
 */
if (!function_exists('get_recent_visits')) {
    function get_recent_visits($pdo, $limit = 10)
    {
        $visits = [];
        
        try {
            $stmt = $pdo->prepare("
                SELECT 
                    hv.visit_id,
                    hv.visit_date,
                    hv.visit_type,
                    hv.notes,
                    hv.created_at,
                    CONCAT(p.first_name, ' ', p.last_name) as patient_name,
                    p.patient_id,
                    CONCAT(b.first_name, ' ', b.last_name) as bhw_name
                FROM health_visits hv
                LEFT JOIN patients p ON hv.patient_id = p.patient_id
                LEFT JOIN bhw_users b ON hv.bhw_id = b.bhw_id
                ORDER BY hv.created_at DESC
                LIMIT ?
            ");
            $stmt->execute([$limit]);
            $visits = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log('[get_recent_visits] ' . $e->getMessage());
        }
        
        return $visits;
    }
}

/**
 * Get recent audit logs for dashboard
 */
if (!function_exists('get_recent_audit_logs')) {
    function get_recent_audit_logs($pdo, $limit = 10)
    {
        $logs = [];
        
        try {
            $stmt = $pdo->prepare("
                SELECT 
                    al.log_id,
                    al.action,
                    al.entity_type,
                    al.entity_id,
                    al.details,
                    al.created_at,
                    al.user_type,
                    CASE 
                        WHEN al.user_type = 'bhw' THEN CONCAT(b.first_name, ' ', b.last_name)
                        WHEN al.user_type = 'patient' THEN CONCAT(p.first_name, ' ', p.last_name)
                        ELSE 'System'
                    END as user_name
                FROM audit_logs al
                LEFT JOIN bhw_users b ON al.user_type = 'bhw' AND al.user_id = b.bhw_id
                LEFT JOIN patients p ON al.user_type = 'patient' AND al.user_id = p.patient_id
                ORDER BY al.created_at DESC
                LIMIT ?
            ");
            $stmt->execute([$limit]);
            $logs = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            // Table might not exist yet
            error_log('[get_recent_audit_logs] ' . $e->getMessage());
        }
        
        return $logs;
    }
}

/**
 * Get visits per month for chart
 */
if (!function_exists('get_visits_chart_data')) {
    function get_visits_chart_data($pdo)
    {
        $result = [
            'labels' => [],
            'counts' => [],
        ];
        
        try {
            $months = [];
            $now = new DateTimeImmutable('now');
            for ($i = 5; $i >= 0; $i--) {
                $m = $now->modify("-{$i} months");
                $key = $m->format('Y-m');
                $months[$key] = [
                    'label' => $m->format('M Y'),
                    'count' => 0,
                ];
            }
            
            $stmt = $pdo->prepare("
                SELECT DATE_FORMAT(visit_date, '%Y-%m') AS ym, COUNT(*) AS count
                FROM health_visits
                WHERE visit_date >= DATE_SUB(CURDATE(), INTERVAL 5 MONTH)
                GROUP BY ym
                ORDER BY ym ASC
            ");
            $stmt->execute();
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            foreach ($rows as $r) {
                $ym = $r['ym'] ?? null;
                if ($ym !== null && isset($months[$ym])) {
                    $months[$ym]['count'] = (int)$r['count'];
                }
            }
            
            foreach ($months as $data) {
                $result['labels'][] = $data['label'];
                $result['counts'][] = $data['count'];
            }
        } catch (Exception $e) {
            error_log('[get_visits_chart_data] ' . $e->getMessage());
        }
        
        return $result;
    }
}
