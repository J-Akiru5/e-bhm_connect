<?php
// actions/report_child_care.php
// Generate PDF report for child care records (12-59 months)

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
    // Columns: child_name, date_of_birth, sex, age_months, vitamin_a_date, albendazole_date, bhw_id
    $sql = "SELECT cc.*, b.full_name as bhw_name
            FROM child_care_records cc
            LEFT JOIN bhw_users b ON cc.bhw_id = b.bhw_id
            WHERE 1=1";
    $params = [];
    
    if ($startDate) {
        $sql .= " AND cc.created_at >= ?";
        $params[] = $startDate;
    }
    if ($endDate) {
        $sql .= " AND cc.created_at <= ?";
        $params[] = $endDate . ' 23:59:59';
    }
    
    $sql .= " ORDER BY cc.created_at DESC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $records = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch (Throwable $e) {
    error_log('Report child care error: ' . $e->getMessage());
    die('An error occurred while fetching child care records.');
}

class PDF extends FPDF {
    function Header() {
        $this->SetFont('Arial','B',14);
        $this->Cell(0,8,'E-BHM Connect - Child Care Report (12-59 Months)',0,1,'C');
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

$titleText = 'Child Care Report';
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
    $pdf->Cell(0,8,'No child care records found.',0,1);
} else {
    $pdf->SetFont('Arial','B',9);
    $pdf->SetFillColor(230, 230, 230);
    $pdf->Cell(60,7,'Child Name',1,0,'C',true);
    $pdf->Cell(30,7,'Birthdate',1,0,'C',true);
    $pdf->Cell(20,7,'Sex',1,0,'C',true);
    $pdf->Cell(20,7,'Age (mo)',1,0,'C',true);
    $pdf->Cell(35,7,'Vitamin A Date',1,0,'C',true);
    $pdf->Cell(35,7,'Albendazole Date',1,0,'C',true);
    $pdf->Cell(77,7,'BHW In Charge',1,1,'C',true);
    
    $pdf->SetFont('Arial','',8);
    $rowCount = 0;
    foreach ($records as $row) {
        $pdf->SetFillColor($rowCount % 2 == 0 ? 255 : 245, $rowCount % 2 == 0 ? 255 : 245, $rowCount % 2 == 0 ? 255 : 245);
        
        $pdf->Cell(60,6,substr($row['child_name'] ?? 'Unknown', 0, 35),1,0,'L',true);
        $pdf->Cell(30,6,$row['date_of_birth'] ?? '',1,0,'C',true);
        $pdf->Cell(20,6,$row['sex'] ?? '',1,0,'C',true);
        $pdf->Cell(20,6,$row['age_months'] ?? '',1,0,'C',true);
        $pdf->Cell(35,6,$row['vitamin_a_date'] ?? '-',1,0,'C',true);
        $pdf->Cell(35,6,$row['albendazole_date'] ?? '-',1,0,'C',true);
        $pdf->Cell(77,6,substr($row['bhw_name'] ?? 'N/A', 0, 40),1,1,'L',true);
        
        $rowCount++;
        if ($pdf->GetY() > 180) {
            $pdf->AddPage('L','A4');
            $pdf->SetFont('Arial','B',9);
            $pdf->SetFillColor(230, 230, 230);
            $pdf->Cell(60,7,'Child Name',1,0,'C',true);
            $pdf->Cell(30,7,'Birthdate',1,0,'C',true);
            $pdf->Cell(20,7,'Sex',1,0,'C',true);
            $pdf->Cell(20,7,'Age (mo)',1,0,'C',true);
            $pdf->Cell(35,7,'Vitamin A Date',1,0,'C',true);
            $pdf->Cell(35,7,'Albendazole Date',1,0,'C',true);
            $pdf->Cell(77,7,'BHW In Charge',1,1,'C',true);
            $pdf->SetFont('Arial','',8);
        }
    }
}

if (ob_get_length()) ob_clean();
$pdf->Output('D', 'Child_Care_Report_' . date('Y-m-d') . '.pdf');
exit();
