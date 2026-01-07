<?php
/**
 * E-BHM Connect Diagnostic Tool
 * Run this on the other computer to diagnose missing data
 * URL: http://localhost/e-bhm_connect/diagnostics.php
 */

require_once __DIR__ . '/config/database.php';

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
    <title>E-BHM Connect Diagnostics</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 900px; margin: 50px auto; padding: 20px; background: #1e293b; color: #fff; }
        .card { background: rgba(255,255,255,0.1); padding: 20px; border-radius: 10px; margin-bottom: 20px; }
        .success { background: rgba(16, 185, 129, 0.2); border: 1px solid #10b981; }
        .error { background: rgba(239, 68, 68, 0.2); border: 1px solid #ef4444; }
        .warning { background: rgba(245, 158, 11, 0.2); border: 1px solid #f59e0b; }
        h1 { color: #10b981; }
        h2 { color: #64748b; border-bottom: 1px solid #374151; padding-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; margin: 10px 0; }
        th, td { padding: 8px 12px; text-align: left; border-bottom: 1px solid #374151; }
        th { background: rgba(255,255,255,0.1); }
        .count { font-size: 24px; font-weight: bold; color: #10b981; }
    </style>
</head>
<body>
    <h1>🔍 E-BHM Connect Diagnostics</h1>
    <p>Run this to identify missing data or configuration issues.</p>

    <!-- Database Connection -->
    <div class="card">
        <h2>1. Database Connection</h2>
        <?php
        try {
            $test = $pdo->query("SELECT 1");
            echo '<div class="card success">✅ Database connection OK</div>';
        } catch (Exception $e) {
            echo '<div class="card error">❌ Database connection failed: ' . $e->getMessage() . '</div>';
        }
        ?>
    </div>

    <!-- Table Counts -->
    <div class="card">
        <h2>2. Data Counts</h2>
        <table>
            <tr><th>Table</th><th>Record Count</th><th>Status</th></tr>
            <?php
            $tables = [
                'inventory' => 'Medicine Inventory',
                'visits' => 'Patient Visits',
                'patients' => 'Patients',
                'bhw_users' => 'BHW Users',
                'sms_queue' => 'SMS Queue',
                'dispensing_log' => 'Dispensing Log'
            ];
            
            foreach ($tables as $table => $label) {
                try {
                    $stmt = $pdo->query("SELECT COUNT(*) as cnt FROM `$table`");
                    $count = $stmt->fetch(PDO::FETCH_ASSOC)['cnt'];
                    $status = $count > 0 ? '✅' : '⚠️ Empty';
                    $statusClass = $count > 0 ? 'success' : 'warning';
                    echo "<tr><td>$label</td><td class='count'>$count</td><td>$status</td></tr>";
                } catch (Exception $e) {
                    echo "<tr><td>$label</td><td>-</td><td>❌ Table missing</td></tr>";
                }
            }
            ?>
        </table>
    </div>

    <!-- Medicine Stock Levels -->
    <div class="card">
        <h2>3. Medicine Stock Levels (Top 10)</h2>
        <?php
        try {
            $stmt = $pdo->query("SELECT item_name, quantity, unit, category FROM inventory ORDER BY quantity ASC LIMIT 10");
            $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            if (empty($items)) {
                echo '<div class="card warning">⚠️ No inventory items found. Run migrations or add medicines.</div>';
            } else {
                echo '<table><tr><th>Medicine</th><th>Quantity</th><th>Unit</th><th>Category</th></tr>';
                foreach ($items as $item) {
                    $qty = $item['quantity'] ?? 0;
                    $class = $qty < 10 ? 'style="color: #ef4444;"' : '';
                    echo "<tr><td>{$item['item_name']}</td><td $class>$qty</td><td>{$item['unit']}</td><td>{$item['category']}</td></tr>";
                }
                echo '</table>';
            }
        } catch (Exception $e) {
            echo '<div class="card error">❌ Error: ' . $e->getMessage() . '</div>';
        }
        ?>
    </div>

    <!-- Recent Visits -->
    <div class="card">
        <h2>4. Recent Visits (Last 10)</h2>
        <?php
        try {
            $stmt = $pdo->query("
                SELECT v.visit_id, v.visit_date, v.visit_type, v.chief_complaint, p.full_name 
                FROM visits v 
                LEFT JOIN patients p ON v.patient_id = p.patient_id 
                ORDER BY v.visit_date DESC 
                LIMIT 10
            ");
            $visits = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            if (empty($visits)) {
                echo '<div class="card warning">⚠️ No visits found. The dashboard will show empty statistics.</div>';
            } else {
                echo '<table><tr><th>Date</th><th>Patient</th><th>Type</th><th>Complaint</th></tr>';
                foreach ($visits as $visit) {
                    echo "<tr><td>{$visit['visit_date']}</td><td>{$visit['full_name']}</td><td>{$visit['visit_type']}</td><td>" . substr($visit['chief_complaint'] ?? '', 0, 50) . "</td></tr>";
                }
                echo '</table>';
            }
        } catch (Exception $e) {
            echo '<div class="card error">❌ Error: ' . $e->getMessage() . '</div>';
        }
        ?>
    </div>

    <!-- Column Check -->
    <div class="card">
        <h2>5. Required Columns Check</h2>
        <?php
        $checks = [
            ['inventory', 'quantity', 'Medicine quantity field'],
            ['inventory', 'category', 'Medicine category field'],
            ['visits', 'visit_date', 'Visit date field'],
            ['visits', 'patient_id', 'Visit patient link'],
            ['sms_queue', 'updated_at', 'SMS updated_at (new column)'],
            ['sms_queue', 'last_response', 'SMS last_response (new column)'],
            ['bhw_users', 'role', 'User role field'],
            ['bhw_users', 'account_status', 'Account status field']
        ];
        
        echo '<table><tr><th>Table</th><th>Column</th><th>Purpose</th><th>Status</th></tr>';
        foreach ($checks as $check) {
            try {
                $stmt = $pdo->query("SHOW COLUMNS FROM `{$check[0]}` LIKE '{$check[1]}'");
                $exists = $stmt->rowCount() > 0;
                $status = $exists ? '✅ Exists' : '❌ Missing - Run migrations!';
                echo "<tr><td>{$check[0]}</td><td>{$check[1]}</td><td>{$check[2]}</td><td>$status</td></tr>";
            } catch (Exception $e) {
                echo "<tr><td>{$check[0]}</td><td>{$check[1]}</td><td>{$check[2]}</td><td>❌ Table error</td></tr>";
            }
        }
        echo '</table>';
        ?>
    </div>

    <!-- Recommendations -->
    <div class="card">
        <h2>6. Quick Fixes</h2>
        <ul>
            <li><strong>Missing columns?</strong> Run: <code>C:\xampp\php\php.exe run_migrations.php</code></li>
            <li><strong>Empty tables?</strong> Add sample data via the admin panel or run seeder migrations</li>
            <li><strong>SMS not working?</strong> Update IP in <code>config/sms_config.php</code></li>
        </ul>
    </div>

    <p style="text-align: center; color: #64748b; margin-top: 40px;">
        Generated: <?php echo date('Y-m-d H:i:s'); ?>
    </p>
</body>
</html>
