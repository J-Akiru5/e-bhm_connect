<?php
// seeder.php
// Simple data seeder for local development and testing the Analytics Dashboard.
// Constraints: DO NOT TRUNCATE or delete any existing tables. Only INSERT new rows.

require_once __DIR__ . '/config/database.php'; // provides $pdo

if (!isset($pdo) || !($pdo instanceof PDO)) {
    die("Error: PDO connection not available. Check config/database.php\n");
}

function tableExists(PDO $pdo, string $table): bool {
    $stmt = $pdo->prepare("SELECT 1 FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = :t LIMIT 1");
    $stmt->execute([':t' => $table]);
    return (bool)$stmt->fetchColumn();
}

function columnExists(PDO $pdo, string $table, string $column): bool {
    $stmt = $pdo->prepare("SELECT 1 FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = :t AND column_name = :c LIMIT 1");
    $stmt->execute([':t' => $table, ':c' => $column]);
    return (bool)$stmt->fetchColumn();
}

// Helper randoms
$firstNames = ["Juan","Maria","Jose","Ana","Liza","Mark","James","Grace","Pedro","Rosa","Miguel","Carmen","Luis","Carla","Rene","Rosa","Emilio","Nora","Ramon","Dina","Elias","Lourdes","Eugene","Alma","Victor","Jocelyn","Rafael","Noel","Mila","Teresita"];
$lastNames = ["Santos","Reyes","Cruz","Garcia","Delrosario","Dela Cruz","Ramos","Torres","Lopez","Gonzales","Martinez","Diaz","Rivera","Mendoza","Paredes","Alvarez","Fernandez","Silva","Bautista","Ortega"];
$puroks = ["Purok 1","Purok 2","Purok 3","Purok 4","Purok 5","Purok Mabini","Purok Rizal","Purok Luna","Purok Mabuhay","Purok Bagong Pag-asa"];
$units = ["bottles","boxes","packs","tablets","sachets","vials"];
$diagnoses = ["Hypertension Checkup","Flu Symptoms","Prenatal Checkup","Child Immunization","Diabetes Follow-up","Wound Dressing","Malaria Screening","Acute Respiratory Infection","Postnatal Visit","Nutrition Counseling","Dengue Suspected","Allergic Reaction","TB Follow-up","General Consultation","Eye Checkup"];

$now = new DateTime();

$log = [];

try {
    $pdo->beginTransaction();

    // 1) Generate 50 Residents (patients)
    $patientsTable = 'patients';
    $insertedPatients = 0;
    if (!tableExists($pdo, $patientsTable)) {
        $log[] = "Table '{$patientsTable}' does not exist — skipping patient inserts.";
    } else {
        $patientCols = ['full_name','address','birthdate','sex','contact','created_at'];
        $insertCols = array_filter($patientCols, function($c) use($pdo, $patientsTable){ return columnExists($pdo, $patientsTable, $c); });
        $colList = implode(',', $insertCols);
        $placeholders = implode(',', array_map(function($c){ return ':' . $c; }, $insertCols));
        $sql = "INSERT INTO {$patientsTable} ({$colList}) VALUES ({$placeholders})";
        $stmt = $pdo->prepare($sql);

        for ($i=0; $i<50; $i++) {
            $gender = (mt_rand(0,100) < 50) ? 'Male' : 'Female';
            // Age bracket roughly: infants 0-5 (10%), children 6-12 (10%), teens 13-19 (10%), adults 20-59 (55%), seniors 60+ (15%)
            $r = mt_rand(1,100);
            if ($r <= 10) { $age = mt_rand(0,5); }
            elseif ($r <= 20) { $age = mt_rand(6,12); }
            elseif ($r <= 30) { $age = mt_rand(13,19); }
            elseif ($r <= 85) { $age = mt_rand(20,59); }
            else { $age = mt_rand(60,85); }

            $birth = (clone $now)->modify("-{$age} years");
            // Add random days within the year for more spread
            $birth->modify('-' . mt_rand(0,364) . ' days');
            $birthdate = $birth->format('Y-m-d');

            $first = $firstNames[array_rand($firstNames)];
            $last = $lastNames[array_rand($lastNames)];
            $full = "$first $last";

            $purok = $puroks[array_rand($puroks)];
            $address = "$purok, Barangay Bacong";

            // created_at spread over last 180 days
            $daysBack = mt_rand(0,180);
            $createdAt = (clone $now)->modify("-{$daysBack} days")->format('Y-m-d H:i:s');

            // phone: generate Philippine mobile-ish numbers (09#########)
            $phone = '09' . str_pad((string)mt_rand(100000000, 999999999), 9, '0', STR_PAD_LEFT);

            $params = [];
            foreach ($insertCols as $col) {
                switch ($col) {
                    case 'full_name': $params[':full_name'] = $full; break;
                    case 'address': $params[':address'] = $address; break;
                    case 'birthdate': $params[':birthdate'] = $birthdate; break;
                    case 'sex': $params[':sex'] = $gender; break;
                    case 'contact': $params[':contact'] = $phone; break;
                    case 'created_at': $params[':created_at'] = $createdAt; break;
                }
            }

            $stmt->execute($params);
            $insertedPatients++;
        }
        $log[] = "Inserted {$insertedPatients} patients into '{$patientsTable}'.";
    }

    // 2) Inventory Items (single-table structure)
    $inventoryTable = 'medication_inventory';
    $insertedItems = 0;

    $categories = ['Antibiotics','Vitamins','Pain Relief','First Aid','Maintenance','Supplies'];

    if (!tableExists($pdo, $inventoryTable)) {
        $log[] = "Table '{$inventoryTable}' does not exist — skipping inventory inserts.";
    } else {
        // Columns we intend to fill
        $desiredCols = ['item_name','category','batch_number','expiry_date','quantity_in_stock','stock_alert_limit','unit','last_restock','description'];
        $availableCols = array_values(array_filter($desiredCols, function($c) use($pdo, $inventoryTable){ return columnExists($pdo,$inventoryTable,$c); }));

        if (empty($availableCols)) {
            $log[] = "No writable columns found on {$inventoryTable} — skipping inventory inserts.";
        } else {
            $colList = implode(',', $availableCols);
            $placeholders = implode(',', array_map(function($c){ return ':' . $c; }, $availableCols));
            // Use INSERT IGNORE to avoid duplicates errors if unique constraints exist, or ON DUPLICATE KEY UPDATE no-op
            $sql = "INSERT INTO {$inventoryTable} ({$colList}) VALUES ({$placeholders}) ON DUPLICATE KEY UPDATE item_name = VALUES(item_name)";
            $stmtItem = $pdo->prepare($sql);

            // Determine indices for expired, near-expiry, and low-stock (20% each)
            $total = 20;
            $indices = range(0, $total-1);
            shuffle($indices);
            $countEach = max(1, (int)round($total * 0.2));
            $expiredIdx = array_slice($indices, 0, $countEach);
            $nearExpiryIdx = array_slice($indices, $countEach, $countEach);
            $lowStockIdx = array_slice($indices, $countEach * 2, $countEach);

            for ($i=0; $i<$total; $i++) {
                // sample medicine names
                $sampleNames = ["Amoxicillin 500mg","Paracetamol 500mg","Cefuroxime 250mg","Ibuprofen 200mg","Vitamin C 1000mg","Iron Syrup","Alcohol 70%","Betadine","Saline","Multivitamin Tablets","Doxycycline","Aspirin 81mg","Metformin","Lisinopril","Cough Syrup"];
                $name = $sampleNames[array_rand($sampleNames)];
                // append index to make semi-unique
                $name .= ' #' . mt_rand(1,999);

                $category = $categories[array_rand($categories)];

                $batch = 'BATCH-' . strtoupper(substr(md5(uniqid((string)mt_rand(), true)), 0, 6));

                // expiry logic
                if (in_array($i, $expiredIdx)) {
                    // expired: random past date within last 180 days
                    $expiry = (clone $now)->modify('-' . mt_rand(1,180) . ' days');
                } elseif (in_array($i, $nearExpiryIdx)) {
                    // near expiry: within next 30 days
                    $expiry = (clone $now)->modify('+' . mt_rand(1,30) . ' days');
                } else {
                    // normal expiry: +60 to +365 days
                    $expiry = (clone $now)->modify('+' . mt_rand(60,365) . ' days');
                }
                $expiryDate = $expiry->format('Y-m-d');

                // quantity logic
                if (in_array($i, $lowStockIdx)) {
                    $quantity = mt_rand(0,9);
                } else {
                    $quantity = mt_rand(0,100);
                }

                $stockAlert = 10; // default
                $unit = $units[array_rand($units)];
                $lastRestock = (clone $now)->modify('-' . mt_rand(0,90) . ' days')->format('Y-m-d');
                $description = 'Auto-generated inventory item for testing.';

                $params = [];
                foreach ($availableCols as $col) {
                    switch ($col) {
                        case 'item_name': $params[':item_name'] = $name; break;
                        case 'category': $params[':category'] = $category; break;
                        case 'batch_number': $params[':batch_number'] = $batch; break;
                        case 'expiry_date': $params[':expiry_date'] = $expiryDate; break;
                        case 'quantity_in_stock': $params[':quantity_in_stock'] = $quantity; break;
                        case 'stock_alert_limit': $params[':stock_alert_limit'] = $stockAlert; break;
                        case 'unit': $params[':unit'] = $unit; break;
                        case 'last_restock': $params[':last_restock'] = $lastRestock; break;
                        case 'description': $params[':description'] = $description; break;
                    }
                }

                $stmtItem->execute($params);
                $insertedItems++;
            }

            $log[] = "Inserted {$insertedItems} medicine items into '{$inventoryTable}'.";
        }
    }

    // 3) Health Visits (100 visits over last 12 months)
    $visitsTable = 'health_visits';
    $insertedVisits = 0;
    if (!tableExists($pdo, $visitsTable)) {
        $log[] = "Table '{$visitsTable}' does not exist — skipping health visit inserts.";
    } else {
        // Ensure BHW users exist (health_visits.bhw_id is NOT NULL in schema)
        $bhwIds = $pdo->query("SELECT bhw_id FROM bhw_users ORDER BY bhw_id ASC")->fetchAll(PDO::FETCH_COLUMN,0);
        if (count($bhwIds) === 0) {
            // insert a few BHW users
            $insB = $pdo->prepare("INSERT INTO bhw_users (full_name, username, password_hash, created_at) VALUES (:name, :username, :pw, NOW())");
            for ($b=1;$b<=5;$b++) {
                $n = "BHW " . ($b);
                $u = "bhw{$b}";
                // store plaintext-ish hash for local testing (NOT for production)
                $pw = password_hash('password123', PASSWORD_DEFAULT);
                $insB->execute([':name'=>$n, ':username'=>$u, ':pw'=>$pw]);
            }
            $bhwIds = $pdo->query("SELECT bhw_id FROM bhw_users ORDER BY bhw_id ASC")->fetchAll(PDO::FETCH_COLUMN,0);
            $log[] = "No BHW users found — inserted " . count($bhwIds) . " sample BHW accounts.";
        }

        // get patient ids (use all existing patients)
        $patientIds = $pdo->query("SELECT patient_id FROM patients ORDER BY patient_id ASC")->fetchAll(PDO::FETCH_COLUMN,0);
        if (count($patientIds) === 0) {
            $log[] = "No patients found to attach visits to — skipping visits.";
        } else {
            $insV = $pdo->prepare("INSERT INTO {$visitsTable} (patient_id, bhw_id, visit_date, visit_type, remarks) VALUES (:patient_id, :bhw_id, :visit_date, :visit_type, :remarks)");
            for ($v=0;$v<100;$v++) {
                $pid = (int)$patientIds[array_rand($patientIds)];
                $bhw = (int)$bhwIds[array_rand($bhwIds)];
                // visit date spread over last 365 days
                $days = mt_rand(0,365);
                $visitDate = (clone $now)->modify('-' . $days . ' days')->format('Y-m-d');
                $diag = $diagnoses[array_rand($diagnoses)];
                $remarks = "Visit note: " . $diag;
                $insV->execute([':patient_id'=>$pid, ':bhw_id'=>$bhw, ':visit_date'=>$visitDate, ':visit_type'=>$diag, ':remarks'=>$remarks]);
                $insertedVisits++;
            }
            $log[] = "Inserted {$insertedVisits} health visits into '{$visitsTable}'.";
        }
    }

    // 4) SMS Queue (30 entries): 20 sent, 5 failed, 5 pending
    $smsTable = 'sms_queue';
    $insertedSms = 0;
    if (!tableExists($pdo, $smsTable)) {
        $log[] = "Table '{$smsTable}' does not exist — skipping SMS queue inserts.";
    } else {
        $statuses = array_merge(array_fill(0,20,'sent'), array_fill(0,5,'failed'), array_fill(0,5,'pending'));
        shuffle($statuses);
        // Determine available columns
        $desiredSmsCols = ['phone_number','message','status','created_at'];
        $availableSmsCols = array_filter($desiredSmsCols, function($c) use($pdo,$smsTable){ return columnExists($pdo,$smsTable,$c); });
        $colList = implode(',', $availableSmsCols);
        $placeholders = implode(',', array_map(function($c){ return ':' . $c; }, $availableSmsCols));
        $sql = "INSERT INTO {$smsTable} ({$colList}) VALUES ({$placeholders})";
        $stmtSms = $pdo->prepare($sql);

        foreach ($statuses as $st) {
            $phone = '09' . str_pad((string)mt_rand(100000000, 999999999), 9, '0', STR_PAD_LEFT);
            $msg = "Test SMS: " . ($st === 'pending' ? 'Pending message' : 'Status ' . $st);
            $created = (clone $now)->modify('-' . mt_rand(0,120) . ' days')->format('Y-m-d H:i:s');
            $params = [];
            foreach ($availableSmsCols as $col) {
                switch ($col) {
                    case 'phone_number': $params[':phone_number'] = $phone; break;
                    case 'message': $params[':message'] = $msg; break;
                    case 'status': $params[':status'] = $st; break;
                    case 'created_at': $params[':created_at'] = $created; break;
                }
            }
            $stmtSms->execute($params);
            $insertedSms++;
        }
        $log[] = "Inserted {$insertedSms} rows into '{$smsTable}'.";
    }

    $pdo->commit();
} catch (Throwable $e) {
    $pdo->rollBack();
    echo "Seeder failed: ", $e->getMessage(), "\n";
    exit(1);
}

// Output log
echo "--- Seeder Summary ---\n";
foreach ($log as $l) {
    echo $l . "\n";
}

echo "Done.\n";

?>
