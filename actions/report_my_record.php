<?php
// actions/report_my_record.php
// Generate a PDF report for the logged-in patient using session ID and FPDF

// Ensure session is started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include required configuration files
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../lib/fpdf/fpdf.php';

// Ensure session has patient_id
if (!isset($_SESSION['patient_id'])) {
    die('Not authorized.');
}

$patient_id = (int) $_SESSION['patient_id'];

try {
    // Patient info
    $stmt = $pdo->prepare('SELECT * FROM patients WHERE patient_id = ? LIMIT 1');
    $stmt->execute([$patient_id]);
    $patient = $stmt->fetch(PDO::FETCH_ASSOC);

    // Health records
    $stmt2 = $pdo->prepare('SELECT * FROM patient_health_records WHERE patient_id = ? LIMIT 1');
    $stmt2->execute([$patient_id]);
    $health_records = $stmt2->fetch(PDO::FETCH_ASSOC);

    // Vitals
    $stmt3 = $pdo->prepare('SELECT * FROM patient_vitals WHERE patient_id = ? ORDER BY recorded_at DESC');
    $stmt3->execute([$patient_id]);
    $vitals = $stmt3->fetchAll(PDO::FETCH_ASSOC);

    // Visits
    $stmt4 = $pdo->prepare('SELECT * FROM health_visits WHERE patient_id = ? ORDER BY visit_date DESC');
    $stmt4->execute([$patient_id]);
    $visits = $stmt4->fetchAll(PDO::FETCH_ASSOC);
} catch (Throwable $e) {
    error_log('Report my record fetch error: ' . $e->getMessage());
    die('An error occurred while fetching patient data.');
}

if (!$patient) {
    die('Patient not found.');
}

class PDF extends FPDF {
    function Header() {
        $this->SetFont('Arial','B',14);
        $this->Cell(0,8,'E-BHM Connect - Patient Health Record',0,1,'C');
        $this->Ln(2);
        $this->SetDrawColor(0,0,0);
        $this->Line(10, $this->GetY(), 200, $this->GetY());
        $this->Ln(6);
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

$pdf->Cell(0,10,($patient['full_name'] ?? 'Patient'),0,1,'C');
$pdf->Ln(5);

$pdf->SetFont('Arial','B',12);
$pdf->Cell(0,10,'Personal Information',1,1);
$pdf->SetFont('Arial','',12);
$pdf->Cell(0,8,'Address: ' . ($patient['address'] ?? 'N/A'),0,1);
$pdf->Cell(0,8,'Birthdate: ' . ($patient['birthdate'] ?? 'N/A'),0,1);
$pdf->Cell(0,8,'Sex: ' . ($patient['sex'] ?? 'N/A'),0,1);
$pdf->Cell(0,8,'Contact: ' . ($patient['contact'] ?? 'N/A'),0,1);
$pdf->Ln(4);

$pdf->SetFont('Arial','B',12);
$pdf->Cell(0,10,'Health Records',1,1);
$pdf->SetFont('Arial','',12);
$pdf->MultiCell(0,8,'Medical History: ' . ($health_records['medical_history'] ?? 'N/A'));
$pdf->MultiCell(0,8,'Immunization: ' . ($health_records['immunization_records'] ?? 'N/A'));
$pdf->MultiCell(0,8,'Medication Records: ' . ($health_records['medication_records'] ?? 'N/A'));
$pdf->MultiCell(0,8,'Maternal & Child Health: ' . ($health_records['maternal_child_health'] ?? 'N/A'));
$pdf->MultiCell(0,8,'Chronic Disease Management: ' . ($health_records['chronic_disease_mgmt'] ?? 'N/A'));
$pdf->MultiCell(0,8,'Referral Information: ' . ($health_records['referral_information'] ?? 'N/A'));
$pdf->Ln(4);

$pdf->SetFont('Arial','B',12);
$pdf->Cell(0,10,'Vitals History',1,1);
$pdf->SetFont('Arial','',11);

if (empty($vitals)) {
    $pdf->Cell(0,8,'No vitals recorded.',0,1);
} else {
    // Table header
    $pdf->SetFont('Arial','B',11);
    $pdf->Cell(45,8,'Recorded At',1,0);
    $pdf->Cell(35,8,'Blood Pressure',1,0);
    $pdf->Cell(30,8,'Heart Rate',1,0);
    $pdf->Cell(30,8,'Temperature',1,0);
    $pdf->Cell(50,8,'Notes',1,1);
    $pdf->SetFont('Arial','',11);
    foreach ($vitals as $v) {
        $pdf->Cell(45,8,($v['recorded_at'] ?? ''),1,0);
        $pdf->Cell(35,8,($v['blood_pressure'] ?? ''),1,0);
        $pdf->Cell(30,8,($v['heart_rate'] ?? ''),1,0);
        $pdf->Cell(30,8,($v['temperature'] ?? ''),1,0);
        $pdf->Cell(50,8,substr(($v['notes'] ?? ''),0,60),1,1);
    }
}

$pdf->Ln(4);
$pdf->SetFont('Arial','B',12);
$pdf->Cell(0,10,'Visit History',1,1);
$pdf->SetFont('Arial','',11);

if (empty($visits)) {
    $pdf->Cell(0,8,'No visits recorded.',0,1);
} else {
    // Table header
    $pdf->SetFont('Arial','B',11);
    $pdf->Cell(45,8,'Visit Date',1,0);
    $pdf->Cell(60,8,'Type',1,0);
    $pdf->Cell(85,8,'Remarks',1,1);
    $pdf->SetFont('Arial','',11);
    foreach ($visits as $visit) {
        $pdf->Cell(45,8,($visit['visit_date'] ?? ''),1,0);
        $pdf->Cell(60,8,substr(($visit['visit_type'] ?? ''),0,35),1,0);
        $pdf->Cell(85,8,substr(($visit['remarks'] ?? ''),0,80),1,1);
    }
}

$pdf->Output('D', 'My_Health_Record.pdf');
exit();
