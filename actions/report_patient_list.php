<?php
// actions/report_patient_list.php
// Generate PDF of full patient list using FPDF

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
    public function Header() {
        // Title
        $this->SetFont('Arial','B',14);
        $this->Cell(0,10,'E-BHM Connect - Full Patient List',0,1,'C');
        
        // Show date range if filtered
        if (isset($_GET['start_date']) && $_GET['start_date'] !== '' || isset($_GET['end_date']) && $_GET['end_date'] !== '') {
            $this->SetFont('Arial','',10);
            $start = $_GET['start_date'] ?: 'Start';
            $end = $_GET['end_date'] ?: 'Present';
            $this->Cell(0,5,"Registered: $start to $end",0,1,'C');
        }
        
        $this->Ln(2);
        $this->SetLineWidth(0.5);
        $this->Line(10, $this->GetY(), 287, $this->GetY());
        $this->Ln(4);
    }

    public function Footer() {
        // Page number
        $this->SetY(-15);
        $this->SetFont('Arial','I',8);
        $this->Cell(0,10,'Page ' . $this->PageNo() ,0,0,'C');
    }
}

// Get filter parameters
$startDate = isset($_GET['start_date']) && $_GET['start_date'] !== '' ? $_GET['start_date'] : null;
$endDate = isset($_GET['end_date']) && $_GET['end_date'] !== '' ? $_GET['end_date'] : null;

try {
    $sql = "SELECT full_name, address, birthdate, sex, contact, created_at FROM patients WHERE 1=1";
    $params = [];
    
    if ($startDate) {
        $sql .= " AND DATE(created_at) >= ?";
        $params[] = $startDate;
    }
    if ($endDate) {
        $sql .= " AND DATE(created_at) <= ?";
        $params[] = $endDate;
    }
    
    $sql .= " ORDER BY full_name ASC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $patients = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Throwable $e) {
    error_log('Report patient list query error: ' . $e->getMessage());
    $patients = [];
}

$pdf = new PDF();
$pdf->AddPage('L','A4');
$pdf->SetFont('Arial','B',12);

// Table header
$pdf->SetFillColor(200,200,200);
$pdf->Cell(60,10,'Name',1,0,'L',true);
$pdf->Cell(100,10,'Address',1,0,'L',true);
$pdf->Cell(30,10,'Birthdate',1,0,'L',true);
$pdf->Cell(25,10,'Sex',1,0,'L',true);
$pdf->Cell(40,10,'Contact',1,0,'L',true);
$pdf->Ln();

$pdf->SetFont('Arial','',10);
foreach ($patients as $row) {
    $pdf->Cell(60,8,substr($row['full_name'] ?? '',0,40),1,0,'L');
    $pdf->Cell(100,8,substr($row['address'] ?? '',0,80),1,0,'L');
    $pdf->Cell(30,8,($row['birthdate'] ?? ''),1,0,'L');
    $pdf->Cell(25,8,($row['sex'] ?? ''),1,0,'L');
    $pdf->Cell(40,8,($row['contact'] ?? ''),1,0,'L');
    $pdf->Ln();
}

if (ob_get_length()) ob_clean();
$pdf->Output('D','patient_list.pdf');
exit();
