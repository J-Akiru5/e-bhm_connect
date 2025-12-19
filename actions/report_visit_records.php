<?php
// actions/report_visit_records.php
// Generate a PDF report for all visit records using FPDF

// Ensure session is started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include required configuration files
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth_helpers.php';
require_once __DIR__ . '/../lib/fpdf/fpdf.php';

// Require BHW login
if (!isset($_SESSION['bhw_id'])) {
    die('Unauthorized access.');
}

// Get filter parameters
$startDate = isset($_GET['start_date']) && $_GET['start_date'] !== '' ? $_GET['start_date'] : null;
$endDate = isset($_GET['end_date']) && $_GET['end_date'] !== '' ? $_GET['end_date'] : null;

try {
    // Build query with optional date filters
    $sql = "SELECT hv.*, p.full_name as patient_name, b.full_name as bhw_name 
            FROM health_visits hv 
            LEFT JOIN patients p ON hv.patient_id = p.patient_id 
            LEFT JOIN bhw_users b ON hv.bhw_id = b.bhw_id 
            WHERE 1=1";
    $params = [];
    
    if ($startDate) {
        $sql .= " AND hv.visit_date >= ?";
        $params[] = $startDate;
    }
    if ($endDate) {
        $sql .= " AND hv.visit_date <= ?";
        $params[] = $endDate;
    }
    
    $sql .= " ORDER BY hv.visit_date DESC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $visits = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get summary statistics
    $countSql = "SELECT COUNT(*) as total, 
                        COUNT(DISTINCT patient_id) as unique_patients,
                        COUNT(DISTINCT bhw_id) as unique_bhws
                 FROM health_visits WHERE 1=1";
    $countParams = [];
    
    if ($startDate) {
        $countSql .= " AND visit_date >= ?";
        $countParams[] = $startDate;
    }
    if ($endDate) {
        $countSql .= " AND visit_date <= ?";
        $countParams[] = $endDate;
    }
    
    $countStmt = $pdo->prepare($countSql);
    $countStmt->execute($countParams);
    $summary = $countStmt->fetch(PDO::FETCH_ASSOC);
    
} catch (Throwable $e) {
    error_log('Report visit records error: ' . $e->getMessage());
    die('An error occurred while fetching visit records.');
}

class PDF extends FPDF {
    function Header() {
        $this->SetFont('Arial','B',14);
        $this->Cell(0,8,'E-BHM Connect - Visit Records Report',0,1,'C');
        $this->SetFont('Arial','',10);
        $this->Cell(0,6,'Generated: ' . date('F d, Y h:i A'),0,1,'C');
        $this->Ln(2);
        $this->SetDrawColor(0,0,0);
        $this->Line(10, $this->GetY(), 200, $this->GetY());
        $this->Ln(4);
    }

    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial','I',8);
        $this->Cell(0,10,'Page ' . $this->PageNo() . '/{nb}',0,0,'C');
    }
}

$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage('L','A4'); // Landscape for more columns
$pdf->SetFont('Arial','B',16);

// Title
$titleText = 'Visit Records Report';
if ($startDate || $endDate) {
    $titleText .= ' (' . ($startDate ?? 'Start') . ' to ' . ($endDate ?? 'Present') . ')';
}
$pdf->Cell(0,10,$titleText,0,1,'C');
$pdf->Ln(3);

// Summary Statistics
$pdf->SetFont('Arial','B',12);
$pdf->Cell(0,8,'Summary',1,1);
$pdf->SetFont('Arial','',11);
$pdf->Cell(90,7,'Total Visits: ' . number_format($summary['total'] ?? 0),0,0);
$pdf->Cell(90,7,'Unique Patients: ' . number_format($summary['unique_patients'] ?? 0),0,0);
$pdf->Cell(90,7,'Active BHWs: ' . number_format($summary['unique_bhws'] ?? 0),0,1);
$pdf->Ln(5);

// Visit Records Table
$pdf->SetFont('Arial','B',12);
$pdf->Cell(0,8,'Detailed Visit Records',1,1);

if (empty($visits)) {
    $pdf->SetFont('Arial','',11);
    $pdf->Cell(0,8,'No visit records found for the selected period.',0,1);
} else {
    // Table header
    $pdf->SetFont('Arial','B',10);
    $pdf->SetFillColor(230, 230, 230);
    $pdf->Cell(25,7,'Date',1,0,'C',true);
    $pdf->Cell(55,7,'Patient Name',1,0,'C',true);
    $pdf->Cell(40,7,'Visit Type',1,0,'C',true);
    $pdf->Cell(50,7,'BHW',1,0,'C',true);
    $pdf->Cell(107,7,'Remarks',1,1,'C',true);
    
    $pdf->SetFont('Arial','',9);
    $rowCount = 0;
    foreach ($visits as $visit) {
        // Alternate row colors
        if ($rowCount % 2 == 0) {
            $pdf->SetFillColor(255, 255, 255);
        } else {
            $pdf->SetFillColor(245, 245, 245);
        }
        
        $pdf->Cell(25,6,$visit['visit_date'] ?? '',1,0,'C',true);
        $pdf->Cell(55,6,substr($visit['patient_name'] ?? 'Unknown', 0, 30),1,0,'L',true);
        $pdf->Cell(40,6,substr($visit['visit_type'] ?? '', 0, 20),1,0,'L',true);
        $pdf->Cell(50,6,substr($visit['bhw_name'] ?? 'N/A', 0, 25),1,0,'L',true);
        $pdf->Cell(107,6,substr($visit['remarks'] ?? '', 0, 55),1,1,'L',true);
        
        $rowCount++;
        
        // Add new page if needed
        if ($pdf->GetY() > 180) {
            $pdf->AddPage('L','A4');
            // Repeat header
            $pdf->SetFont('Arial','B',10);
            $pdf->SetFillColor(230, 230, 230);
            $pdf->Cell(25,7,'Date',1,0,'C',true);
            $pdf->Cell(55,7,'Patient Name',1,0,'C',true);
            $pdf->Cell(40,7,'Visit Type',1,0,'C',true);
            $pdf->Cell(50,7,'BHW',1,0,'C',true);
            $pdf->Cell(107,7,'Remarks',1,1,'C',true);
            $pdf->SetFont('Arial','',9);
        }
    }
}

$pdf->Ln(5);
$pdf->SetFont('Arial','I',9);
$pdf->Cell(0,6,'Total Records: ' . count($visits),0,1,'R');

$filename = 'Visit_Records_Report_' . date('Y-m-d') . '.pdf';
$pdf->Output('D', $filename);
exit();
