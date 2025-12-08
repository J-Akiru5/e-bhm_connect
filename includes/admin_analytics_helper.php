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

            // SMS stats grouped by status
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

    // Demographics helper: gender counts and age brackets
    if (!function_exists('get_demographics')) {
        function get_demographics($pdo)
        {
            $out = [
                'gender' => [
                    'male' => 0,
                    'female' => 0,
                    'other' => 0,
                ],
                'age_brackets' => [
                    '0-5' => 0,
                    '6-12' => 0,
                    '13-19' => 0,
                    '20-59' => 0,
                    '60+' => 0,
                ],
            ];

            try {
                if (!is_object($pdo) || (!($pdo instanceof PDO) && !method_exists($pdo, 'query'))) {
                    throw new InvalidArgumentException('A valid PDO instance is required.');
                }

                // Sex counts (column `sex` in patients table)
                $gstmt = $pdo->query("SELECT LOWER(COALESCE(sex,'')) AS g, COUNT(*) AS cnt FROM patients GROUP BY g");
                $grows = $gstmt->fetchAll(PDO::FETCH_ASSOC);
                foreach ($grows as $r) {
                    $g = trim(strtolower($r['g'] ?? ''));
                    $cnt = isset($r['cnt']) ? (int)$r['cnt'] : 0;
                    if ($g === 'male' || $g === 'm') {
                        $out['gender']['male'] += $cnt;
                    } elseif ($g === 'female' || $g === 'f') {
                        $out['gender']['female'] += $cnt;
                    } else {
                        $out['gender']['other'] += $cnt;
                    }
                }

                // Age brackets using DOB
                // Use `birthdate` column for age calculations
                $sql = "SELECT
                    SUM(CASE WHEN TIMESTAMPDIFF(YEAR, birthdate, CURDATE()) BETWEEN 0 AND 5 THEN 1 ELSE 0 END) AS b0_5,
                    SUM(CASE WHEN TIMESTAMPDIFF(YEAR, birthdate, CURDATE()) BETWEEN 6 AND 12 THEN 1 ELSE 0 END) AS b6_12,
                    SUM(CASE WHEN TIMESTAMPDIFF(YEAR, birthdate, CURDATE()) BETWEEN 13 AND 19 THEN 1 ELSE 0 END) AS b13_19,
                    SUM(CASE WHEN TIMESTAMPDIFF(YEAR, birthdate, CURDATE()) BETWEEN 20 AND 59 THEN 1 ELSE 0 END) AS b20_59,
                    SUM(CASE WHEN TIMESTAMPDIFF(YEAR, birthdate, CURDATE()) >= 60 THEN 1 ELSE 0 END) AS b60
                    FROM patients WHERE birthdate IS NOT NULL";
                $astmt = $pdo->query($sql);
                $ar = $astmt->fetch(PDO::FETCH_ASSOC);
                if ($ar) {
                    $out['age_brackets']['0-5'] = (int)($ar['b0_5'] ?? 0);
                    $out['age_brackets']['6-12'] = (int)($ar['b6_12'] ?? 0);
                    $out['age_brackets']['13-19'] = (int)($ar['b13_19'] ?? 0);
                    $out['age_brackets']['20-59'] = (int)($ar['b20_59'] ?? 0);
                    $out['age_brackets']['60+'] = (int)($ar['b60'] ?? 0);
                }

            } catch (Throwable $e) {
                error_log('[admin_analytics_helper::get_demographics] ' . $e->getMessage());
            }

            return $out;
        }
    }

    // Medicine stock grouped by category
    if (!function_exists('get_medicine_stock_by_category')) {
        function get_medicine_stock_by_category($pdo)
        {
            $out = [];
            try {
                if (!is_object($pdo) || (!($pdo instanceof PDO) && !method_exists($pdo, 'query'))) {
                    throw new InvalidArgumentException('A valid PDO instance is required.');
                }

                $sql = "SELECT COALESCE(c.name, 'Uncategorized') AS category, SUM(COALESCE(m.quantity_in_stock,0)) AS total
                        FROM medication_inventory m
                        LEFT JOIN inventory_categories c ON m.category_id = c.id
                        GROUP BY category
                        ORDER BY total DESC";
                $stmt = $pdo->query($sql);
                $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
                foreach ($rows as $r) {
                    $cat = $r['category'] ?? 'Uncategorized';
                    $out[] = [ 'category' => $cat, 'total' => (int)($r['total'] ?? 0) ];
                }
            } catch (Throwable $e) {
                error_log('[admin_analytics_helper::get_medicine_stock_by_category] ' . $e->getMessage());
            }
            return $out;
        }
    }
