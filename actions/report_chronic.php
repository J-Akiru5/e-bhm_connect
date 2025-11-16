<?php
// (index.php includes config/config.php)
// **FIX: We must manually include the database connection here**
require_once __DIR__ . '/../lib/fpdf/fpdf.php';
require_once __DIR__ . '/../config/database.php';

class PDF extends FPDF {
    // Page header
    function Header() {
        $this->SetFont('Arial', 'B', 14);
        $this->Cell(0, 10, 'E-BHM Connect - Chronic Disease Report', 0, 1, 'C');
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
$pdf->AddPage('L', 'A4'); // Landscape
$pdf->SetFont('Arial', 'B', 10);

// Database Query
$stmt = $pdo->query("SELECT p.full_name, p.address, p.contact, r.chronic_disease_mgmt 
                     FROM patients p
                     JOIN patient_health_records r ON p.patient_id = r.patient_id
                     WHERE r.chronic_disease_mgmt IS NOT NULL AND r.chronic_disease_mgmt != ''
                     ORDER BY p.full_name ASC");
$patients = $stmt->fetchAll();

// Table Header
$pdf->Cell(60, 7, 'Patient Name', 1);
$pdf->Cell(80, 7, 'Address', 1);
$pdf->Cell(40, 7, 'Contact', 1);
$pdf->Cell(97, 7, 'Condition/Notes', 1);
$pdf->Ln(); // New line

// Table Body
$pdf->SetFont('Arial', '', 9);
foreach ($patients as $patient) {
    $pdf->Cell(60, 6, $patient['full_name'], 1);
    $pdf->Cell(80, 6, $patient['address'], 1);
    $pdf->Cell(40, 6, $patient['contact'], 1);
    $pdf->Cell(97, 6, $patient['chronic_disease_mgmt'], 1);
    $pdf->Ln();
}

// Output
$pdf->Output('D', 'chronic_disease_report.pdf');
exit;
?>