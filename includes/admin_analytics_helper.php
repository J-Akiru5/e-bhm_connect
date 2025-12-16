<?php
/**
 * Admin analytics helper
 *
 * Provides a single function fetch_dashboard_data($pdo) that returns
 * an array with metrics used by the admin dashboard.
 *
 * - total_patients: int
 * - total_bhws: int
 * - sms_stats: array (keys: sent, failed, pending)
 * - recent_registrations: array of ['month' => 'Mon YYYY', 'count' => int] for the last 6 months
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
