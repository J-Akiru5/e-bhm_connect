<?php
// actions/report_inventory.php
// Generate inventory stock report

// Ensure session is started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include required configuration files
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../lib/fpdf/fpdf.php';

// Allow access if logged in
if (!isset($_SESSION['bhw_id'])) {
    die('Unauthorized access.');
}

class PDF extends FPDF {
    // Page header
    function Header() {
        $this->SetFont('Arial', 'B', 14);
        $this->Cell(0, 10, 'E-BHM Connect - Current Inventory Stock', 0, 1, 'C');
        $this->Ln(5);
    }
    // Page footer
    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, 'Page ' . $this->PageNo(), 0, 0, 'C');
    }
}

// --- Main Logic ---
$startDate = isset($_GET['start_date']) && $_GET['start_date'] !== '' ? $_GET['start_date'] : null;
$endDate = isset($_GET['end_date']) && $_GET['end_date'] !== '' ? $_GET['end_date'] : null;

$pdf = new PDF();
$pdf->AddPage('P', 'A4'); // Portrait
$pdf->SetFont('Arial', 'B', 10);

// Database Query
$sql = "SELECT * FROM medication_inventory WHERE 1=1";
$params = [];

if ($startDate) {
    $sql .= " AND last_restock >= ?";
    $params[] = $startDate;
}
if ($endDate) {
    $sql .= " AND last_restock <= ?";
    $params[] = $endDate;
}

$sql .= " ORDER BY item_name ASC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$inventory_items = $stmt->fetchAll();

// Table Header
$pdf->Cell(40, 7, 'Item Name', 1);
$pdf->Cell(80, 7, 'Description', 1);
$pdf->Cell(20, 7, 'Quantity', 1);
$pdf->Cell(20, 7, 'Unit', 1);
$pdf->Cell(30, 7, 'Last Restock', 1);
$pdf->Ln(); // New line

// Table Body
$pdf->SetFont('Arial', '', 9);
foreach ($inventory_items as $item) {
    $pdf->Cell(40, 6, substr($item['item_name'] ?? '', 0, 25), 1);
    $pdf->Cell(80, 6, substr($item['description'] ?? '', 0, 50), 1);
    $pdf->Cell(20, 6, $item['quantity_in_stock'], 1);
    $pdf->Cell(20, 6, $item['unit'], 1);
    $pdf->Cell(30, 6, $item['last_restock'], 1);
    $pdf->Ln();
}

// Output
if (ob_get_length()) ob_clean();
$pdf->Output('D', 'inventory_stock.pdf');
exit;
?>