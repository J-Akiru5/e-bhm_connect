<?php
// actions/report_bhw_activity.php
// Generate a PDF report for all BHW activities using FPDF

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
    // Get all BHWs with their activity counts
    $sql = "SELECT b.bhw_id, b.full_name, b.assigned_area, b.role,
                   (SELECT COUNT(*) FROM health_visits hv WHERE hv.bhw_id = b.bhw_id" .
                   ($startDate ? " AND hv.visit_date >= '$startDate'" : "") .
                   ($endDate ? " AND hv.visit_date <= '$endDate'" : "") .
                   ") as visit_count,
                   (SELECT COUNT(*) FROM patients p WHERE p.created_by = b.bhw_id) as patients_registered
            FROM bhw_users b 
            WHERE b.account_status = 'approved'
            ORDER BY visit_count DESC, b.full_name ASC";
    
    $stmt = $pdo->query($sql);
    $bhws = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get overall statistics
    $statsSql = "SELECT 
                    (SELECT COUNT(*) FROM health_visits" .
                    ($startDate || $endDate ? " WHERE 1=1" : "") .
                    ($startDate ? " AND visit_date >= '$startDate'" : "") .
                    ($endDate ? " AND visit_date <= '$endDate'" : "") .
                    ") as total_visits,
                    (SELECT COUNT(DISTINCT patient_id) FROM health_visits" .
                    ($startDate || $endDate ? " WHERE 1=1" : "") .
                    ($startDate ? " AND visit_date >= '$startDate'" : "") .
                    ($endDate ? " AND visit_date <= '$endDate'" : "") .
                    ") as patients_served,
                    (SELECT COUNT(*) FROM bhw_users WHERE account_status = 'approved') as total_bhws";
    
    $statsStmt = $pdo->query($statsSql);
    $stats = $statsStmt->fetch(PDO::FETCH_ASSOC);
    
} catch (Throwable $e) {
    error_log('Report BHW activity error: ' . $e->getMessage());
    die('An error occurred while fetching BHW activity data.');
}

class PDF extends FPDF {
    function Header() {
        $this->SetFont('Arial','B',14);
        $this->Cell(0,8,'E-BHM Connect - BHW Activity Report',0,1,'C');
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
$pdf->AddPage('P','A4');
$pdf->SetFont('Arial','B',16);

// Title
$titleText = 'BHW Activity Report';
if ($startDate || $endDate) {
    $titleText .= ' (' . ($startDate ?? 'Start') . ' to ' . ($endDate ?? 'Present') . ')';
}
$pdf->Cell(0,10,$titleText,0,1,'C');
$pdf->Ln(3);

// Summary Statistics
$pdf->SetFont('Arial','B',12);
$pdf->Cell(0,8,'Summary Statistics',1,1);
$pdf->SetFont('Arial','',11);
$pdf->Cell(65,7,'Total Health Visits: ' . number_format($stats['total_visits'] ?? 0),0,0);
$pdf->Cell(65,7,'Patients Served: ' . number_format($stats['patients_served'] ?? 0),0,0);
$pdf->Cell(60,7,'Active BHWs: ' . number_format($stats['total_bhws'] ?? 0),0,1);
$pdf->Ln(5);

// BHW Performance Table
$pdf->SetFont('Arial','B',12);
$pdf->Cell(0,8,'BHW Performance Summary',1,1);

if (empty($bhws)) {
    $pdf->SetFont('Arial','',11);
    $pdf->Cell(0,8,'No BHW records found.',0,1);
} else {
    // Table header
    $pdf->SetFont('Arial','B',10);
    $pdf->SetFillColor(230, 230, 230);
    $pdf->Cell(60,7,'BHW Name',1,0,'L',true);
    $pdf->Cell(50,7,'Assigned Area',1,0,'L',true);
    $pdf->Cell(30,7,'Role',1,0,'C',true);
    $pdf->Cell(25,7,'Visits',1,0,'C',true);
    $pdf->Cell(25,7,'Patients',1,1,'C',true);
    
    $pdf->SetFont('Arial','',9);
    $rowCount = 0;
    foreach ($bhws as $bhw) {
        // Alternate row colors
        if ($rowCount % 2 == 0) {
            $pdf->SetFillColor(255, 255, 255);
        } else {
            $pdf->SetFillColor(245, 245, 245);
        }
        
        $pdf->Cell(60,6,substr($bhw['full_name'] ?? '', 0, 35),1,0,'L',true);
        $pdf->Cell(50,6,substr($bhw['assigned_area'] ?? 'N/A', 0, 28),1,0,'L',true);
        $pdf->Cell(30,6,ucfirst($bhw['role'] ?? 'bhw'),1,0,'C',true);
        $pdf->Cell(25,6,number_format($bhw['visit_count'] ?? 0),1,0,'C',true);
        $pdf->Cell(25,6,number_format($bhw['patients_registered'] ?? 0),1,1,'C',true);
        
        $rowCount++;
        
        // Add new page if needed
        if ($pdf->GetY() > 260) {
            $pdf->AddPage('P','A4');
            // Repeat header
            $pdf->SetFont('Arial','B',10);
            $pdf->SetFillColor(230, 230, 230);
            $pdf->Cell(60,7,'BHW Name',1,0,'L',true);
            $pdf->Cell(50,7,'Assigned Area',1,0,'L',true);
            $pdf->Cell(30,7,'Role',1,0,'C',true);
            $pdf->Cell(25,7,'Visits',1,0,'C',true);
            $pdf->Cell(25,7,'Patients',1,1,'C',true);
            $pdf->SetFont('Arial','',9);
        }
    }
}

$pdf->Ln(5);
$pdf->SetFont('Arial','I',9);
$pdf->Cell(0,6,'Total BHWs: ' . count($bhws),0,1,'R');

$filename = 'BHW_Activity_Report_' . date('Y-m-d') . '.pdf';
$pdf->Output('D', $filename);
exit();
