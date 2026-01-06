<?php
// actions/report_chronic.php
// Generate chronic disease report

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
        $this->Cell(0, 10, 'E-BHM Connect - Chronic Disease Report', 0, 1, 'C');
        
        if (isset($_GET['start_date']) && $_GET['start_date'] !== '' || isset($_GET['end_date']) && $_GET['end_date'] !== '') {
            $this->SetFont('Arial','',10);
            $start = $_GET['start_date'] ?: 'Start';
            $end = $_GET['end_date'] ?: 'Present';
            $this->Cell(0,5,"Last Updated: $start to $end",0,1,'C');
        }
        
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

try {
    // Database Query
    $sql = "SELECT p.full_name, p.address, p.contact, r.chronic_disease_mgmt 
            FROM patients p
            JOIN patient_health_records r ON p.patient_id = r.patient_id
            WHERE r.chronic_disease_mgmt IS NOT NULL AND r.chronic_disease_mgmt != ''";
    $params = [];
    
    if ($startDate) {
        $sql .= " AND DATE(r.last_updated) >= ?";
        $params[] = $startDate;
    }
    if ($endDate) {
        $sql .= " AND DATE(r.last_updated) <= ?";
        $params[] = $endDate;
    }
    
    $sql .= " ORDER BY p.full_name ASC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $patients = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Throwable $e) {
    error_log('Report chronic disease error: ' . $e->getMessage());
    die('An error occurred while fetching chronic disease records.');
}

$pdf = new PDF();
$pdf->AddPage('L', 'A4'); // Landscape
$pdf->SetFont('Arial', 'B', 10);

// Table Header
$pdf->SetFillColor(230, 230, 230);
$pdf->Cell(60, 7, 'Patient Name', 1, 0, 'C', true);
$pdf->Cell(80, 7, 'Address', 1, 0, 'C', true);
$pdf->Cell(40, 7, 'Contact', 1, 0, 'C', true);
$pdf->Cell(97, 7, 'Condition/Notes', 1, 1, 'C', true);

// Table Body
$pdf->SetFont('Arial', '', 9);
if (empty($patients)) {
    $pdf->SetFont('Arial','',11);
    $pdf->Cell(0,8,'No chronic disease records found.',0,1);
} else {
    foreach ($patients as $patient) {
        $pdf->Cell(60, 6, substr($patient['full_name'] ?? '', 0, 30), 1);
        $pdf->Cell(80, 6, substr($patient['address'] ?? '', 0, 40), 1);
        $pdf->Cell(40, 6, substr($patient['contact'] ?? '', 0, 20), 1);
        $pdf->Cell(97, 6, substr($patient['chronic_disease_mgmt'] ?? '', 0, 50), 1);
        $pdf->Ln();
    }
}

// Output
if (ob_get_length()) ob_clean();
$pdf->Output('D', 'chronic_disease_report.pdf');
exit;