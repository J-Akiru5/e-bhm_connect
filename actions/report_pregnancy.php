<?php
// actions/report_pregnancy.php
// Generate PDF report for pregnancy tracking

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../lib/fpdf/fpdf.php';

// Allow access if logged in
if (!isset($_SESSION['bhw_id'])) {
    die('Unauthorized access.');
}

$startDate = isset($_GET['start_date']) && $_GET['start_date'] !== '' ? $_GET['start_date'] : null;
$endDate = isset($_GET['end_date']) && $_GET['end_date'] !== '' ? $_GET['end_date'] : null;

try {
    // Columns: pregnant_woman_name, lmp, edc, age, gravida_para, nhts_status, remarks
    $sql = "SELECT pt.*, b.full_name as bhw_name
            FROM pregnancy_tracking pt
            LEFT JOIN bhw_users b ON pt.bhw_id = b.bhw_id
            WHERE 1=1";
    $params = [];
    
    if ($startDate) {
        $sql .= " AND pt.lmp >= ?";
        $params[] = $startDate;
    }
    if ($endDate) {
        $sql .= " AND pt.lmp <= ?";
        $params[] = $endDate;
    }
    
    $sql .= " ORDER BY pt.edc DESC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $records = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch (Throwable $e) {
    error_log('Report pregnancy error: ' . $e->getMessage());
    die('An error occurred while fetching pregnancy records.');
}

class PDF extends FPDF {
    function Header() {
        $this->SetFont('Arial','B',14);
        $this->Cell(0,8,'E-BHM Connect - Pregnancy Tracking Report',0,1,'C');
        $this->SetFont('Arial','',10);
        $this->Cell(0,6,'Generated: ' . date('F d, Y h:i A'),0,1,'C');
        $this->Ln(2);
        $this->Line(10, $this->GetY(), 287, $this->GetY());
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
$pdf->AddPage('L','A4');
$pdf->SetFont('Arial','B',16);

$titleText = 'Pregnancy Tracking Report';
if ($startDate || $endDate) {
    $titleText .= ' (' . ($startDate ?? 'Start') . ' to ' . ($endDate ?? 'Present') . ')';
}
$pdf->Cell(0,10,$titleText,0,1,'C');
$pdf->Ln(3);

$pdf->SetFont('Arial','B',12);
$pdf->Cell(0,8,'Total Records: ' . count($records),0,1);
$pdf->Ln(3);

if (empty($records)) {
    $pdf->SetFont('Arial','',11);
    $pdf->Cell(0,8,'No pregnancy records found.',0,1);
} else {
    $pdf->SetFont('Arial','B',9);
    $pdf->SetFillColor(230, 230, 230);
    $pdf->Cell(60,7,'Patient Name',1,0,'C',true);
    $pdf->Cell(25,7,'LMP',1,0,'C',true);
    $pdf->Cell(25,7,'EDC',1,0,'C',true);
    $pdf->Cell(15,7,'Age',1,0,'C',true);
    $pdf->Cell(25,7,'G-P Score',1,0,'C',true);
    $pdf->Cell(30,7,'NHTS',1,0,'C',true);
    $pdf->Cell(45,7,'BHW In Charge',1,0,'C',true);
    $pdf->Cell(52,7,'Remarks',1,1,'C',true);
    
    $pdf->SetFont('Arial','',8);
    $rowCount = 0;
    foreach ($records as $row) {
        $pdf->SetFillColor($rowCount % 2 == 0 ? 255 : 245, $rowCount % 2 == 0 ? 255 : 245, $rowCount % 2 == 0 ? 255 : 245);
        
        $pdf->Cell(60,6,substr($row['pregnant_woman_name'] ?? 'Unknown', 0, 30),1,0,'L',true);
        $pdf->Cell(25,6,$row['lmp'] ?? '',1,0,'C',true);
        $pdf->Cell(25,6,$row['edc'] ?? '',1,0,'C',true);
        $pdf->Cell(15,6,$row['age'] ?? '',1,0,'C',true);
        $pdf->Cell(25,6,$row['gravida_para'] ?? '-',1,0,'C',true);
        $pdf->Cell(30,6,$row['nhts_status'] ?? '',1,0,'C',true);
        $pdf->Cell(45,6,substr($row['bhw_name'] ?? 'N/A', 0, 22),1,0,'L',true);
        $pdf->Cell(52,6,substr($row['remarks'] ?? '', 0, 25),1,1,'L',true);
        
        $rowCount++;
        if ($pdf->GetY() > 180) {
            $pdf->AddPage('L','A4');
            $pdf->SetFont('Arial','B',9);
            $pdf->SetFillColor(230, 230, 230);
            $pdf->Cell(60,7,'Patient Name',1,0,'C',true);
            $pdf->Cell(25,7,'LMP',1,0,'C',true);
            $pdf->Cell(25,7,'EDC',1,0,'C',true);
            $pdf->Cell(15,7,'Age',1,0,'C',true);
            $pdf->Cell(25,7,'G-P Score',1,0,'C',true);
            $pdf->Cell(30,7,'NHTS',1,0,'C',true);
            $pdf->Cell(45,7,'BHW In Charge',1,0,'C',true);
            $pdf->Cell(52,7,'Remarks',1,1,'C',true);
            $pdf->SetFont('Arial','',8);
        }
    }
}

if (ob_get_length()) ob_clean();
$pdf->Output('D', 'Pregnancy_Tracking_Report_' . date('Y-m-d') . '.pdf');
exit();
