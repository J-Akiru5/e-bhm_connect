<?php
// actions/report_medicine_dispensing.php
// Generate PDF report for medicine dispensing log

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

// Get filter parameters
$startDate = isset($_GET['start_date']) && $_GET['start_date'] !== '' ? $_GET['start_date'] : null;
$endDate = isset($_GET['end_date']) && $_GET['end_date'] !== '' ? $_GET['end_date'] : null;

try {
    $sql = "SELECT mdl.id, mdl.dispensed_at, mdl.quantity, mdl.notes,
                   p.full_name as patient_name,
                   mi.item_name as medicine_name,
                   b.full_name as bhw_name
            FROM medicine_dispensing_log mdl
            LEFT JOIN patients p ON mdl.resident_id = p.patient_id
            LEFT JOIN medication_inventory mi ON mdl.item_id = mi.item_id
            LEFT JOIN bhw_users b ON mdl.bhw_id = b.bhw_id
            WHERE 1=1";
    $params = [];
    
    if ($startDate) {
        $sql .= " AND DATE(mdl.dispensed_at) >= ?";
        $params[] = $startDate;
    }
    if ($endDate) {
        $sql .= " AND DATE(mdl.dispensed_at) <= ?";
        $params[] = $endDate;
    }
    
    $sql .= " ORDER BY mdl.dispensed_at DESC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $records = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch (Throwable $e) {
    error_log('Report medicine dispensing error: ' . $e->getMessage());
    die('An error occurred while fetching dispensing records.');
}

class PDF extends FPDF {
    function Header() {
        $this->SetFont('Arial','B',14);
        $this->Cell(0,8,'E-BHM Connect - Medicine Dispensing Report',0,1,'C');
        $this->SetFont('Arial','',10);
        $this->Cell(0,6,'Generated: ' . date('F d, Y h:i A'),0,1,'C');
        $this->Ln(2);
        $this->SetDrawColor(0,0,0);
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

// Title
$titleText = 'Medicine Dispensing Report';
if ($startDate || $endDate) {
    $titleText .= ' (' . ($startDate ?? 'Start') . ' to ' . ($endDate ?? 'Present') . ')';
}
$pdf->Cell(0,10,$titleText,0,1,'C');
$pdf->Ln(3);

// Summary
$pdf->SetFont('Arial','B',12);
$pdf->Cell(0,8,'Total Dispensed: ' . count($records) . ' records',0,1);
$pdf->Ln(3);

if (empty($records)) {
    $pdf->SetFont('Arial','',11);
    $pdf->Cell(0,8,'No dispensing records found for the selected period.',0,1);
} else {
    // Table header
    $pdf->SetFont('Arial','B',10);
    $pdf->SetFillColor(230, 230, 230);
    $pdf->Cell(35,7,'Date',1,0,'C',true);
    $pdf->Cell(60,7,'Patient',1,0,'C',true);
    $pdf->Cell(60,7,'Medicine',1,0,'C',true);
    $pdf->Cell(20,7,'Qty',1,0,'C',true);
    $pdf->Cell(50,7,'Dispensed By',1,0,'C',true);
    $pdf->Cell(52,7,'Notes',1,1,'C',true);
    
    $pdf->SetFont('Arial','',9);
    $rowCount = 0;
    foreach ($records as $row) {
        $pdf->SetFillColor($rowCount % 2 == 0 ? 255 : 245, $rowCount % 2 == 0 ? 255 : 245, $rowCount % 2 == 0 ? 255 : 245);
        
        $pdf->Cell(35,6,date('Y-m-d H:i', strtotime($row['dispensed_at'])),1,0,'C',true);
        $pdf->Cell(60,6,substr($row['patient_name'] ?? 'Unknown', 0, 30),1,0,'L',true);
        $pdf->Cell(60,6,substr($row['medicine_name'] ?? 'N/A', 0, 30),1,0,'L',true);
        $pdf->Cell(20,6,$row['quantity'],1,0,'C',true);
        $pdf->Cell(50,6,substr($row['bhw_name'] ?? 'N/A', 0, 25),1,0,'L',true);
        $pdf->Cell(52,6,substr($row['notes'] ?? '', 0, 25),1,1,'L',true);
        
        $rowCount++;
        
        if ($pdf->GetY() > 180) {
            $pdf->AddPage('L','A4');
            $pdf->SetFont('Arial','B',10);
            $pdf->SetFillColor(230, 230, 230);
            $pdf->Cell(35,7,'Date',1,0,'C',true);
            $pdf->Cell(60,7,'Patient',1,0,'C',true);
            $pdf->Cell(60,7,'Medicine',1,0,'C',true);
            $pdf->Cell(20,7,'Qty',1,0,'C',true);
            $pdf->Cell(50,7,'Dispensed By',1,0,'C',true);
            $pdf->Cell(52,7,'Notes',1,1,'C',true);
            $pdf->SetFont('Arial','',9);
        }
    }
}

$filename = 'Medicine_Dispensing_Report_' . date('Y-m-d') . '.pdf';
if (ob_get_length()) ob_clean();
$pdf->Output('D', $filename);
exit();
