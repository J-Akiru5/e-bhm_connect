<?php
/**
 * Health Records PDF Report Generator
 * E-BHM Connect - Export health records as PDF
 */

require_once __DIR__ . '/../includes/auth_bhw.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../lib/fpdf/fpdf.php';

// Get report type from URL
$reportType = $_GET['report'] ?? '';
$year = $_GET['year'] ?? date('Y');

class PDF extends FPDF {
    function Header() {
        $this->SetFont('Arial', 'B', 16);
        $this->Cell(0, 10, 'E-BHM Connect - Health Records', 0, 1, 'C');
        $this->SetFont('Arial', '', 10);
        $this->Cell(0, 5, 'Generated: ' . date('F j, Y g:i A'), 0, 1, 'C');
        $this->Ln(5);
    }
    
    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, 'Page ' . $this->PageNo(), 0, 0, 'C');
    }
}

switch ($reportType) {
    case 'pregnancy':
        generatePregnancyReport($pdo);
        break;
    case 'childcare':
        generateChildcareReport($pdo);
        break;
    case 'natality':
        generateNatalityReport($pdo);
        break;
    case 'mortality':
        generateMortalityReport($pdo, $year);
        break;
    case 'chronic':
        generateChronicReport($pdo);
        break;
    case 'ntp':
        generateNTPReport($pdo);
        break;
    case 'wra':
        generateWRAReport($pdo, $year);
        break;
    default:
        die('Invalid report type');
}

function generatePregnancyReport($pdo) {
    $pdf = new PDF();
    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 14);
    $pdf->Cell(0, 10, 'Pregnancy Tracking Report', 0, 1);
    $pdf->Ln(2);
    
    $stmt = $pdo->query("SELECT * FROM pregnancy_tracking ORDER BY lmp DESC");
    $records = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Table header
    $pdf->SetFont('Arial', 'B', 9);
    $pdf->SetFillColor(32, 201, 151);
    $pdf->SetTextColor(255);
    $pdf->Cell(60, 8, 'Mother Name', 1, 0, 'C', true);
    $pdf->Cell(30, 8, 'LMP', 1, 0, 'C', true);
    $pdf->Cell(30, 8, 'EDC', 1, 0, 'C', true);
    $pdf->Cell(25, 8, 'G/P', 1, 0, 'C', true);
    $pdf->Cell(45, 8, 'Outcome', 1, 1, 'C', true);
    
    // Data rows
    $pdf->SetFont('Arial', '', 8);
    $pdf->SetTextColor(0);
    foreach ($records as $r) {
        $pdf->Cell(60, 7, substr($r['mother_name'], 0, 25), 1);
        $pdf->Cell(30, 7, $r['lmp'] ? date('M j, Y', strtotime($r['lmp'])) : '-', 1);
        $pdf->Cell(30, 7, $r['edc'] ? date('M j, Y', strtotime($r['edc'])) : '-', 1);
        $pdf->Cell(25, 7, 'G' . $r['gravida'] . '/P' . $r['para'], 1, 0, 'C');
        $pdf->Cell(45, 7, substr($r['delivery_outcome'] ?: 'Ongoing', 0, 20), 1, 1);
    }
    
    $pdf->Output('D', 'Pregnancy_Report_' . date('Y-m-d') . '.pdf');
}

function generateChildcareReport($pdo) {
    $pdf = new PDF();
    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 14);
    $pdf->Cell(0, 10, 'Child Care Records (12-59 Months)', 0, 1);
    $pdf->Ln(2);
    
    $stmt = $pdo->query("SELECT * FROM child_care_records ORDER BY date_of_birth DESC");
    $records = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $pdf->SetFont('Arial', 'B', 9);
    $pdf->SetFillColor(245, 158, 11);
    $pdf->SetTextColor(255);
    $pdf->Cell(70, 8, 'Child Name', 1, 0, 'C', true);
    $pdf->Cell(20, 8, 'Age', 1, 0, 'C', true);
    $pdf->Cell(15, 8, 'Sex', 1, 0, 'C', true);
    $pdf->Cell(40, 8, 'Vit A Date', 1, 0, 'C', true);
    $pdf->Cell(45, 8, 'Deworm Date', 1, 1, 'C', true);
    
    $pdf->SetFont('Arial', '', 8);
    $pdf->SetTextColor(0);
    foreach ($records as $r) {
        $pdf->Cell(70, 7, substr($r['child_name'], 0, 30), 1);
        $pdf->Cell(20, 7, $r['age_months'] . ' mo', 1, 0, 'C');
        $pdf->Cell(15, 7, substr($r['sex'], 0, 1), 1, 0, 'C');
        $pdf->Cell(40, 7, $r['vitamin_a_date'] ? date('M j, Y', strtotime($r['vitamin_a_date'])) : 'Not Given', 1);
        $pdf->Cell(45, 7, $r['albendazole_date'] ? date('M j, Y', strtotime($r['albendazole_date'])) : 'Not Given', 1, 1);
    }
    
    $pdf->Output('D', 'ChildCare_Report_' . date('Y-m-d') . '.pdf');
}

function generateNatalityReport($pdo) {
    $pdf = new PDF();
    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 14);
    $pdf->Cell(0, 10, 'Natality (Birth) Records', 0, 1);
    $pdf->Ln(2);
    
    $stmt = $pdo->query("SELECT * FROM natality_records ORDER BY date_of_birth DESC");
    $records = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $pdf->SetFont('Arial', 'B', 9);
    $pdf->SetFillColor(16, 185, 129);
    $pdf->SetTextColor(255);
    $pdf->Cell(60, 8, 'Baby Name', 1, 0, 'C', true);
    $pdf->Cell(30, 8, 'Birth Date', 1, 0, 'C', true);
    $pdf->Cell(15, 8, 'Sex', 1, 0, 'C', true);
    $pdf->Cell(25, 8, 'Weight', 1, 0, 'C', true);
    $pdf->Cell(60, 8, 'Mother Name', 1, 1, 'C', true);
    
    $pdf->SetFont('Arial', '', 8);
    $pdf->SetTextColor(0);
    foreach ($records as $r) {
        $pdf->Cell(60, 7, substr($r['baby_name'], 0, 25), 1);
        $pdf->Cell(30, 7, date('M j, Y', strtotime($r['date_of_birth'])), 1);
        $pdf->Cell(15, 7, substr($r['sex'], 0, 1), 1, 0, 'C');
        $pdf->Cell(25, 7, $r['birth_weight_kg'] . ' kg', 1, 0, 'C');
        $pdf->Cell(60, 7, substr($r['mother_name'], 0, 25), 1, 1);
    }
    
    $pdf->Output('D', 'Natality_Report_' . date('Y-m-d') . '.pdf');
}

function generateMortalityReport($pdo, $year) {
    $pdf = new PDF();
    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 14);
    $pdf->Cell(0, 10, 'Mortality (Death) Records - ' . $year, 0, 1);
    $pdf->Ln(2);
    
    $stmt = $pdo->prepare("SELECT * FROM mortality_records WHERE YEAR(date_of_death) = ? ORDER BY date_of_death DESC");
    $stmt->execute([$year]);
    $records = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $pdf->SetFont('Arial', 'B', 9);
    $pdf->SetFillColor(107, 114, 128);
    $pdf->SetTextColor(255);
    $pdf->Cell(60, 8, 'Deceased Name', 1, 0, 'C', true);
    $pdf->Cell(30, 8, 'Death Date', 1, 0, 'C', true);
    $pdf->Cell(20, 8, 'Age', 1, 0, 'C', true);
    $pdf->Cell(80, 8, 'Cause of Death', 1, 1, 'C', true);
    
    $pdf->SetFont('Arial', '', 8);
    $pdf->SetTextColor(0);
    foreach ($records as $r) {
        $pdf->Cell(60, 7, substr($r['deceased_name'], 0, 25), 1);
        $pdf->Cell(30, 7, date('M j, Y', strtotime($r['date_of_death'])), 1);
        $pdf->Cell(20, 7, $r['age_at_death'] . ' yrs', 1, 0, 'C');
        $pdf->Cell(80, 7, substr($r['cause_of_death'], 0, 35), 1, 1);
    }
    
    $pdf->Output('D', 'Mortality_Report_' . $year . '.pdf');
}

function generateChronicReport($pdo) {
    $pdf = new PDF();
    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 14);
    $pdf->Cell(0, 10, 'Chronic Disease Masterlist', 0, 1);
    $pdf->Ln(2);
    
    $stmt = $pdo->query("SELECT * FROM chronic_disease_masterlist ORDER BY patient_name");
    $records = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $pdf->SetFont('Arial', 'B', 9);
    $pdf->SetFillColor(239, 68, 68);
    $pdf->SetTextColor(255);
    $pdf->Cell(70, 8, 'Patient Name', 1, 0, 'C', true);
    $pdf->Cell(15, 8, 'HTN', 1, 0, 'C', true);
    $pdf->Cell(15, 8, 'DM', 1, 0, 'C', true);
    $pdf->Cell(30, 8, 'Blood Sugar', 1, 0, 'C', true);
    $pdf->Cell(60, 8, 'Last Checkup', 1, 1, 'C', true);
    
    $pdf->SetFont('Arial', '', 8);
    $pdf->SetTextColor(0);
    foreach ($records as $r) {
        $pdf->Cell(70, 7, substr($r['patient_name'], 0, 30), 1);
        $pdf->Cell(15, 7, $r['is_hypertensive'] ? 'Yes' : 'No', 1, 0, 'C');
        $pdf->Cell(15, 7, $r['is_diabetic'] ? 'Yes' : 'No', 1, 0, 'C');
        $pdf->Cell(30, 7, $r['blood_sugar_level'] ?: '-', 1, 0, 'C');
        $pdf->Cell(60, 7, $r['last_checkup_date'] ? date('M j, Y', strtotime($r['last_checkup_date'])) : '-', 1, 1);
    }
    
    $pdf->Output('D', 'Chronic_Disease_Report_' . date('Y-m-d') . '.pdf');
}

function generateNTPReport($pdo) {
    $pdf = new PDF();
    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 14);
    $pdf->Cell(0, 10, 'NTP (TB Program) Client Monitoring', 0, 1);
    $pdf->Ln(2);
    
    $stmt = $pdo->query("SELECT * FROM ntp_client_monitoring ORDER BY registration_date DESC");
    $records = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $pdf->SetFont('Arial', 'B', 9);
    $pdf->SetFillColor(139, 92, 246);
    $pdf->SetTextColor(255);
    $pdf->Cell(70, 8, 'Client Name', 1, 0, 'C', true);
    $pdf->Cell(30, 8, 'Reg Date', 1, 0, 'C', true);
    $pdf->Cell(25, 8, 'Type', 1, 0, 'C', true);
    $pdf->Cell(65, 8, 'Outcome', 1, 1, 'C', true);
    
    $pdf->SetFont('Arial', '', 8);
    $pdf->SetTextColor(0);
    foreach ($records as $r) {
        $pdf->Cell(70, 7, substr($r['client_name'], 0, 30), 1);
        $pdf->Cell(30, 7, date('M j, Y', strtotime($r['registration_date'])), 1);
        $pdf->Cell(25, 7, $r['registration_type'], 1, 0, 'C');
        $pdf->Cell(65, 7, substr($r['treatment_outcome'] ?: 'On Treatment', 0, 25), 1, 1);
    }
    
    $pdf->Output('D', 'NTP_Report_' . date('Y-m-d') . '.pdf');
}

function generateWRAReport($pdo, $year) {
    $pdf = new PDF();
    $pdf->AddPage('L'); // Landscape for 12-month grid
    $pdf->SetFont('Arial', 'B', 14);
    $pdf->Cell(0, 10, 'Women of Reproductive Age Tracking - ' . $year, 0, 1);
    $pdf->Ln(2);
    
    $stmt = $pdo->prepare("SELECT * FROM wra_tracking WHERE tracking_year = ? ORDER BY woman_name");
    $stmt->execute([$year]);
    $records = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $pdf->SetFont('Arial', 'B', 8);
    $pdf->SetFillColor(59, 130, 246);
    $pdf->SetTextColor(255);
    $pdf->Cell(60, 8, 'Woman Name', 1, 0, 'C', true);
    $pdf->Cell(40, 8, 'FP Method', 1, 0, 'C', true);
    // Monthly columns
    $months = ['J', 'F', 'M', 'A', 'M', 'J', 'J', 'A', 'S', 'O', 'N', 'D'];
    foreach ($months as $m) {
        $pdf->Cell(12, 8, $m, 1, 0, 'C', true);
    }
    $pdf->Ln();
    
    $pdf->SetFont('Arial', '', 7);
    $pdf->SetTextColor(0);
    foreach ($records as $r) {
        $pdf->Cell(60, 6, substr($r['woman_name'], 0, 25), 1);
        $pdf->Cell(40, 6, substr($r['fp_method'], 0, 18), 1);
        $monthlyStatus = json_decode($r['monthly_status'], true);
        $monthKeys = ['jan','feb','mar','apr','may','jun','jul','aug','sep','oct','nov','dec'];
        foreach ($monthKeys as $mk) {
            $pdf->Cell(12, 6, $monthlyStatus[$mk] ?? '-', 1, 0, 'C');
        }
        $pdf->Ln();
    }
    
    $pdf->Output('D', 'WRA_Report_' . $year . '.pdf');
}
