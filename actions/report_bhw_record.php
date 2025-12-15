<?php
// actions/report_bhw_record.php
// Generate a PDF report for a single BHW using FPDF

// Ensure session is started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include required configuration files
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../lib/fpdf/fpdf.php';

// Ensure an ID is provided
if (!isset($_GET['id']) || trim($_GET['id']) === '') {
    die('BHW ID not provided.');
}

$bhw_id = (int) $_GET['id'];

try {
    $stmt = $pdo->prepare('SELECT * FROM bhw_users WHERE bhw_id = ? LIMIT 1');
    $stmt->execute([$bhw_id]);
    $bhw = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (Throwable $e) {
    error_log('Report BHW fetch error: ' . $e->getMessage());
    die('An error occurred while fetching BHW data.');
}

if (!$bhw) {
    die('BHW not found.');
}

class PDF extends FPDF {
    // Header
    function Header() {
        // Logo could be added here
        $this->SetFont('Arial','B',14);
        $this->Cell(0,8,'E-BHM Connect - BHW Profile Report',0,1,'C');
        $this->Ln(2);
        $this->SetDrawColor(0,0,0);
        $this->Line(10, $this->GetY(), 200, $this->GetY());
        $this->Ln(6);
    }

    // Footer
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

// Title: Full name
$pdf->Cell(0,10,($bhw['full_name'] ?? 'BHW'),0,1,'C');
$pdf->Ln(4);

$pdf->SetFont('Arial','',12);
$pdf->SetLeftMargin(15);
$pdf->SetRightMargin(15);

// Helper to print label/value pairs
function pdf_field($pdf, $label, $value) {
    $pdf->SetFont('Arial','B',12);
    $pdf->Cell(50,8,$label,0,0);
    $pdf->SetFont('Arial','',12);
    $pdf->MultiCell(0,8,$value,0,1);
}

pdf_field($pdf, 'Username:', $bhw['username'] ?? '');
pdf_field($pdf, 'BHW ID:', $bhw['bhw_unique_id'] ?? '');
pdf_field($pdf, 'Address:', $bhw['address'] ?? '');
pdf_field($pdf, 'Birthdate:', $bhw['birthdate'] ?? '');
pdf_field($pdf, 'Contact:', $bhw['contact'] ?? '');
pdf_field($pdf, 'Assigned Area:', $bhw['assigned_area'] ?? '');
pdf_field($pdf, 'Employment Status:', $bhw['employment_status'] ?? '');

$pdf->SetFont('Arial','B',12);
$pdf->Cell(50,8,'Training & Certification:',0,0);
$pdf->SetFont('Arial','',12);
$pdf->MultiCell(0,8,$bhw['training_certification'] ?? 'N/A',0,1);

$filename = 'BHW_Report_' . ($bhw['bhw_id'] ?? $bhw_id) . '.pdf';
$pdf->Output('D', $filename);
exit();
