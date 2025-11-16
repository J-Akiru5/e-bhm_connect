<?php
// actions/chart_data.php
// Returns JSON data for Chart.js showing counts of visit_type
header('Content-Type: application/json');

$year = isset($_GET['year']) ? trim($_GET['year']) : '';

$sql = "SELECT visit_type, COUNT(*) as count FROM health_visits";
$params = [];

if ($year !== '') {
    $sql .= " WHERE YEAR(visit_date) = ?";
    $params[] = $year;
}

$sql .= " GROUP BY visit_type";

try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $labels = [];
    $data = [];
    foreach ($results as $row) {
        $labels[] = $row['visit_type'];
        $data[] = (int)$row['count'];
    }

    echo json_encode(['labels' => $labels, 'data' => $data]);
    exit();

} catch (Throwable $e) {
    error_log('Chart data error: ' . $e->getMessage());
    echo json_encode(['labels' => [], 'data' => []]);
    exit();
}

?>
