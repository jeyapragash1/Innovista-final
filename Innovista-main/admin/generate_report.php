<?php
// generate_report.php
// This script generates a PDF report using FPDF.
// It explicitly avoids including admin_header.php to prevent HTML output
// from corrupting the PDF file, and handles its own session and DB connection.

session_start(); // Start session for authentication check

// Basic authentication check (replicated from admin_header.php)
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    // Redirect to the login page if not logged in as admin
    header("Location: ../public/login.php");
    exit();
}

// Ensure no output is buffered before PDF headers are sent
ob_end_clean(); 

require_once '../config/Database.php';
// Correct the path to FPDF. Assuming FPDF is in 'Innovista-main/vendor/fpdf'
require_once '../vendor/fpdf/fpdf.php'; 

$db = new Database();
$conn = $db->getConnection();

$report_type = filter_input(INPUT_GET, 'type', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$period = filter_input(INPUT_GET, 'period', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?? 'monthly';

if (empty($report_type)) {
    // If report type is not specified, output a simple error message as headers haven't been sent yet
    die("Error: Report type not specified.");
}

// Prepare query details
$query = "";
$report_title = "";
$headers_csv = [];
$data_columns = []; // For mapping fetched data to table columns

if ($report_type === 'income') {
    $report_title = "Income Report (" . ucfirst($period) . ")";
    $headers_csv = ["Period", "Total Income (Rs)"];
    $data_columns = ['period', 'total_amount'];

    if ($period === 'monthly') {
        $query = "SELECT DATE_FORMAT(payment_date, '%Y-%m') as period, SUM(amount) as total_amount 
                  FROM payments GROUP BY period ORDER BY period DESC";
    } elseif ($period === 'weekly') {
        $query = "SELECT YEARWEEK(payment_date) as period, SUM(amount) as total_amount 
                  FROM payments GROUP BY period ORDER BY period DESC";
    } elseif ($period === 'yearly') {
        $query = "SELECT DATE_FORMAT(payment_date, '%Y') as period, SUM(amount) as total_amount 
                  FROM payments GROUP BY period ORDER BY period DESC";
    }
} elseif ($report_type === 'booking') {
    $report_title = "Booking Report (" . ucfirst($period) . ")";
    $headers_csv = ["Period", "Total Bookings"];
    $data_columns = ['period', 'total_bookings'];

    if ($period === 'monthly') {
        $query = "SELECT DATE_FORMAT(created_at, '%Y-%m') as period, COUNT(*) as total_bookings 
                  FROM quotations GROUP BY period ORDER BY period DESC";
    } elseif ($period === 'weekly') {
        $query = "SELECT YEARWEEK(created_at) as period, COUNT(*) as total_bookings 
                  FROM quotations GROUP BY period ORDER BY period DESC";
    } elseif ($period === 'yearly') {
        $query = "SELECT DATE_FORMAT(created_at, '%Y') as period, COUNT(*) as total_bookings 
                  FROM quotations GROUP BY period ORDER BY period DESC";
    }
} else {
    die("Error: Invalid report type specified.");
}

// Execute query and fetch data
try {
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $report_data = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Database error while fetching report data: " . htmlspecialchars($e->getMessage()));
}


// --- FPDF Generation ---
class PDF extends FPDF
{
    protected $reportTitle;

    function Header()
    {
        // Logo (Optional) - adjust path as needed relative to this file's execution
        // From admin/generate_report.php, ../public/assets/images/logo1.png
        $logo_path = '../public/assets/images/logo1.png';
        if (file_exists($logo_path)) {
            $this->Image($logo_path, 10, 8, 30);
        }
        
        $this->SetFont('Arial', 'B', 15);
        // Move to the right
        $this->Cell(80);
        // Title
        $this->Cell(30, 10, 'Innovista ' . $this->reportTitle, 0, 0, 'C');
        // Line break
        $this->Ln(20);
    }

    function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, 'Page ' . $this->PageNo() . '/{nb}', 0, 0, 'C');
        $this->Cell(0, 10, 'Generated on ' . date('d M Y H:i'), 0, 0, 'R');
    }

    function setReportTitle($title) {
        $this->reportTitle = $title;
    }

    function FancyTable($header, $data, $reportType)
    {
        // Colors, line width and font
        $this->SetFillColor(240, 240, 240); // Light grey for header background
        $this->SetTextColor(50, 50, 50); // Darker text
        $this->SetDrawColor(180, 180, 180); // Border color
        $this->SetLineWidth(.3);
        $this->SetFont('Arial', 'B', 10);
        
        // Header
        $w = [95, 95]; // Column widths for A4 (total 190mm usable width)
        for ($i = 0; $i < count($header); $i++) {
            $this->Cell($w[$i], 7, $header[$i], 1, 0, 'C', true);
        }
        $this->Ln();
        
        // Color and font restoration
        $this->SetFillColor(255, 255, 255);
        $this->SetTextColor(0);
        $this->SetFont('Arial', '', 10);
        
        // Data
        $fill = false; // Alternating row background
        if (empty($data)) {
            $this->Cell(array_sum($w), 10, "No data available for this period.", 1, 0, 'C', $fill);
            $this->Ln();
        } else {
            foreach ($data as $row) {
                // Alternating row color
                $this->SetFillColor($fill ? 248 : 255, $fill ? 248 : 255, $fill ? 248 : 255);
                
                $this->Cell($w[0], 6, htmlspecialchars($row['period']), 'LR', 0, 'L', $fill);
                if ($reportType === 'income') {
                    $this->Cell($w[1], 6, 'Rs ' . number_format($row['total_amount'], 2), 'LR', 0, 'R', $fill);
                } else { // booking
                    $this->Cell($w[1], 6, number_format($row['total_bookings']), 'LR', 0, 'R', $fill);
                }
                $this->Ln();
                $fill = !$fill; // Toggle fill for next row
            }
        }
        // Closing line
        $this->Cell(array_sum($w), 0, '', 'T');
    }
}

$pdf = new PDF();
$pdf->AliasNbPages(); // Required for {nb} page numbering
$pdf->setReportTitle($report_title); // Set the dynamic title
$pdf->AddPage();
// Set top margin after header for content
$pdf->SetY(35); // Adjust this value to control spacing below the header

$pdf->SetFont('Arial', 'B', 12);
// The "FancyTable" method will draw the headers and data
$pdf->FancyTable($headers_csv, $report_data, $report_type);

// Output PDF for download
$filename = "report_" . $report_type . "_" . $period . "_" . date('Ymd_His') . ".pdf";
$pdf->Output('D', $filename);
exit;