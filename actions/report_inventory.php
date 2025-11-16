<?php
// (index.php includes config/config.php)
// **FIX: We must manually include the database connection here**
require_once __DIR__ . '/../lib/fpdf/fpdf.php';
require_once __DIR__ . '/../config/database.php';

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
$pdf = new PDF();
$pdf->AddPage('P', 'A4'); // Portrait
$pdf->SetFont('Arial', 'B', 10);

// Database Query
$stmt = $pdo->query("SELECT * FROM medication_inventory ORDER BY item_name ASC");
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
    $pdf->Cell(40, 6, $item['item_name'], 1);
    $pdf->Cell(80, 6, $item['description'], 1);
    $pdf->Cell(20, 6, $item['quantity_in_stock'], 1, 0, 'C');
    $pdf->Cell(20, 6, $item['unit'], 1);
    $pdf->Cell(30, 6, $item['last_restock'], 1);
    $pdf->Ln();
}

// Output
$pdf->Output('D', 'inventory_stock.pdf');
exit;
?>