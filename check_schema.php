<?php
require_once __DIR__ . '/config/database.php';
$stmt = $pdo->query("DESCRIBE bhw_users");
foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
    echo $row['Field'] . " - " . $row['Type'] . "\n";
}
