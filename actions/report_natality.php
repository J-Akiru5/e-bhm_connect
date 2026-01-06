<?php
// actions/report_natality.php
// Generate PDF report for natality records

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../lib/fpdf/fpdf.php';

if (!isset($_SESSION['bhw_id'])) {
    die('Unauthorized access.');
}

$startDate = isset($_GET['start_date']) && $_GET['start_date'] !== '' ? $_GET['start_date'] : null;
$endDate = isset($_GET['end_date']) && $_GET['end_date'] !== '' ? $_GET['end_date'] : null;

try {
    $sql = "SELECT nr.*, b.full_name as bhw_name
            FROM natality_records nr
            LEFT JOIN bhw_users b ON nr.bhw_id = b.bhw_id
            WHERE 1=1";
    $params = [];
    
    if ($startDate) {
        $sql .= " AND nr.date_of_birth >= ?";
        $params[] = $startDate;
    }
    if ($endDate) {
        $sql .= " AND nr.date_of_birth <= ?";
        $params[] = $endDate;
    }
    
    $sql .= " ORDER BY nr.date_of_birth DESC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $records = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch (Throwable $e) {
    error_log('Report natality error: ' . $e->getMessage());
    die('An error occurred while fetching natality records.');
}

class PDF extends FPDF {
    function Header() {
        $this->SetFont('Arial','B',14);
        $this->Cell(0,8,'E-BHM Connect - Natality (Birth) Records Report',0,1,'C');
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

$titleText = 'Natality Records Report';
if ($startDate || $endDate) {
    $titleText .= ' (' . ($startDate ?? 'Start') . ' to ' . ($endDate ?? 'Present') . ')';
}
$pdf->Cell(0,10,$titleText,0,1,'C');
$pdf->Ln(3);

$pdf->SetFont('Arial','B',12);
$pdf->Cell(0,8,'Total Births: ' . count($records),0,1);
$pdf->Ln(3);

if (empty($records)) {
    $pdf->SetFont('Arial','',11);
    $pdf->Cell(0,8,'No birth records found.',0,1);
} else {
    $pdf->SetFont('Arial','B',9);
    $pdf->SetFillColor(230, 230, 230);
    $pdf->Cell(30,7,'Date of Birth',1,0,'C',true);
    $pdf->Cell(50,7,'Baby Name',1,0,'C',true);
    $pdf->Cell(15,7,'Sex',1,0,'C',true);
    $pdf->Cell(20,7,'Weight',1,0,'C',true);
    $pdf->Cell(25,7,'Type',1,0,'C',true);
    $pdf->Cell(40,7,'Place',1,0,'C',true);
    $pdf->Cell(50,7,'Mother',1,0,'C',true);
    $pdf->Cell(47,7,'BHW',1,1,'C',true);
    
    $pdf->SetFont('Arial','',8);
    $rowCount = 0;
    foreach ($records as $row) {
        $pdf->SetFillColor($rowCount % 2 == 0 ? 255 : 245, $rowCount % 2 == 0 ? 255 : 245, $rowCount % 2 == 0 ? 255 : 245);
        
        $pdf->Cell(30,6,$row['date_of_birth'] ?? '',1,0,'C',true);
        $pdf->Cell(50,6,substr($row['baby_complete_name'] ?? 'Unknown', 0, 25),1,0,'L',true);
        $pdf->Cell(15,6,$row['sex'] ?? '',1,0,'C',true);
        $pdf->Cell(20,6,($row['weight_kg'] ?? '') . ' kg',1,0,'C',true);
        $pdf->Cell(25,6,$row['delivery_type'] ?? '',1,0,'C',true);
        $pdf->Cell(40,6,substr($row['place_of_delivery'] ?? '', 0, 20),1,0,'L',true);
        $pdf->Cell(50,6,substr($row['mother_complete_name'] ?? '', 0, 25),1,0,'L',true);
        $pdf->Cell(47,6,substr($row['bhw_name'] ?? 'N/A', 0, 25),1,1,'L',true);
        
        $rowCount++;
        if ($pdf->GetY() > 180) {
            $pdf->AddPage('L','A4');
            $pdf->SetFont('Arial','B',9);
            $pdf->SetFillColor(230, 230, 230);
            $pdf->Cell(30,7,'Date of Birth',1,0,'C',true);
            $pdf->Cell(50,7,'Baby Name',1,0,'C',true);
            $pdf->Cell(15,7,'Sex',1,0,'C',true);
            $pdf->Cell(20,7,'Weight',1,0,'C',true);
            $pdf->Cell(25,7,'Type',1,0,'C',true);
            $pdf->Cell(40,7,'Place',1,0,'C',true);
            $pdf->Cell(50,7,'Mother',1,0,'C',true);
            $pdf->Cell(47,7,'BHW',1,1,'C',true);
            $pdf->SetFont('Arial','',8);
        }
    }
}

if (ob_get_length()) ob_clean();
$pdf->Output('D', 'Natality_Records_Report_' . date('Y-m-d') . '.pdf');
exit();
