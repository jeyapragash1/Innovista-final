<?php
// C:\xampp1\htdocs\Innovista-final\Innovista-main\handlers\generate_invoice.php

session_start(); // Start session for authentication check

// Basic authentication check
if (!isset($_SESSION['user_id'])) {
    // Redirect to the login page if not logged in
    header("Location: ../public/login.php");
    exit();
}

ob_end_clean(); // Ensure no output is buffered before PDF headers are sent

require_once '../config/Database.php';
require_once '../vendor/fpdf/fpdf.php'; // Path to FPDF library

$db = new Database();
$conn = $db->getConnection();
$loggedInUserId = $_SESSION['user_id'];
$loggedInUserRole = $_SESSION['user_role'];

$payment_id = filter_input(INPUT_GET, 'payment_id', FILTER_VALIDATE_INT);

if (!$payment_id) {
    die("Error: Invalid payment ID.");
}

// Fetch payment and associated project/customer/provider details
try {
    $stmt_invoice = $conn->prepare("
        SELECT 
            py.id AS payment_id, py.amount, py.payment_type, py.transaction_id, py.payment_date,
            cq.id AS custom_quotation_id, cq.project_description, cq.amount AS quoted_amount_total, cq.advance, cq.start_date AS project_start_date, cq.end_date AS project_end_date,
            cust.id AS customer_id, cust.name AS customer_name, cust.email AS customer_email, cust.phone AS customer_phone, cust.address AS customer_address,
            prov.id AS provider_id, prov.name AS provider_name, prov.email AS provider_email, prov.phone AS provider_phone, prov.address AS provider_address
        FROM payments py
        JOIN custom_quotations cq ON py.quotation_id = cq.id
        JOIN users cust ON cq.customer_id = cust.id
        JOIN users prov ON cq.provider_id = prov.id
        WHERE py.id = :payment_id AND (cust.id = :customer_id OR :user_role = 'admin')
    ");
    $stmt_invoice->bindParam(':payment_id', $payment_id, PDO::PARAM_INT);
    $stmt_invoice->bindParam(':customer_id', $loggedInUserId, PDO::PARAM_INT);
    $stmt_invoice->bindParam(':user_role', $loggedInUserRole);
    $stmt_invoice->execute();
    $invoice_data = $stmt_invoice->fetch(PDO::FETCH_ASSOC);

    if (!$invoice_data) {
        die("Error: Invoice not found or you do not have permission to view it.");
    }

    // Fetch platform settings for footer/contact info
    $settings = [];
    $stmt_settings = $conn->prepare("SELECT setting_key, setting_value FROM settings");
    $stmt_settings->execute();
    while ($row = $stmt_settings->fetch(PDO::FETCH_ASSOC)) {
        $settings[$row['setting_key']] = $row['setting_value'];
    }
    $platform_name = $settings['platform_name'] ?? 'Innovista';
    $platform_address = $settings['platform_address'] ?? 'N/A';
    $admin_contact_email = $settings['admin_contact_email'] ?? 'N/A';


    // --- FPDF Invoice Generation ---
    class PDF extends FPDF
    {
        protected $invoiceData;
        protected $platformSettings;

        function setInvoiceData($data, $settings) {
            $this->invoiceData = $data;
            $this->platformSettings = $settings;
        }

        function Header()
        {
            // Logo
            $logo_path = '../public/assets/images/logo1.png';
            if (file_exists($logo_path)) {
                $this->Image($logo_path, 10, 8, 30);
            }
            $this->SetFont('Arial', 'B', 18);
            $this->SetTextColor(40, 40, 40);
            $this->Cell(0, 10, 'INVOICE', 0, 1, 'R');
            $this->SetFont('Arial', '', 10);
            $this->Cell(0, 5, 'Invoice ID: #' . str_pad($this->invoiceData['payment_id'], 6, '0', STR_PAD_LEFT), 0, 1, 'R');
            $this->Cell(0, 5, 'Transaction ID: ' . $this->invoiceData['transaction_id'], 0, 1, 'R');
            $this->Ln(10);
        }

        function Footer()
        {
            $this->SetY(-25);
            $this->SetFont('Arial', 'I', 8);
            $this->SetTextColor(150, 150, 150);
            $this->Cell(0, 5, 'Thank you for your business!', 0, 1, 'C');
            $this->Cell(0, 5, $this->platformSettings['platform_name'] . ' | ' . $this->platformSettings['platform_address'] . ' | ' . $this->platformSettings['admin_contact_email'], 0, 0, 'C');
        }

        function InvoiceTable($header, $data)
        {
            $this->SetFillColor(240, 240, 240);
            $this->SetTextColor(50, 50, 50);
            $this->SetDrawColor(180, 180, 180);
            $this->SetLineWidth(.3);
            $this->SetFont('Arial', 'B', 10);

            $w = [100, 30, 30, 30]; // Description, Qty, Unit Price, Total
            for ($i = 0; $i < count($header); $i++) {
                $this->Cell($w[$i], 7, $header[$i], 1, 0, 'C', true);
            }
            $this->Ln();

            $this->SetFillColor(255, 255, 255);
            $this->SetTextColor(0);
            $this->SetFont('Arial', '', 10);

            // Invoice Item (simplified to one line for payment type)
            $this->Cell($w[0], 6, htmlspecialchars($data['project_description'] . ' (' . ucfirst($data['payment_type']) . ')'), 'LR', 0, 'L');
            $this->Cell($w[1], 6, '1', 'LR', 0, 'C'); // Quantity
            $this->Cell($w[2], 6, 'Rs ' . number_format($data['amount'], 2), 'LR', 0, 'R'); // Unit Price
            $this->Cell($w[3], 6, 'Rs ' . number_format($data['amount'], 2), 'LR', 0, 'R'); // Total
            $this->Ln();

            // Closing line
            $this->Cell(array_sum($w), 0, '', 'T');
            $this->Ln(10);

            // Summary Totals
            $this->Cell(array_sum($w) - $w[3], 6, 'Subtotal:', 0, 0, 'R');
            $this->Cell($w[3], 6, 'Rs ' . number_format($data['amount'], 2), 1, 1, 'R');
            $this->Cell(array_sum($w) - $w[3], 6, 'Total Paid:', 0, 0, 'R');
            $this->SetFont('Arial', 'B', 10);
            $this->Cell($w[3], 6, 'Rs ' . number_format($data['amount'], 2), 1, 1, 'R');
            $this->SetFont('Arial', '', 10);
        }
    }

    $pdf = new PDF();
    $pdf->AliasNbPages();
    $pdf->setInvoiceData($invoice_data, [
        'platform_name' => $platform_name,
        'platform_address' => $platform_address,
        'admin_contact_email' => $admin_contact_email
    ]);
    $pdf->AddPage();
    $pdf->SetY(40); // Start content below header

    // Billing Information
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(0, 6, 'Bill To:', 0, 1);
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(0, 6, htmlspecialchars($invoice_data['customer_name']), 0, 1);
    $pdf->Cell(0, 6, htmlspecialchars($invoice_data['customer_address'] ?? 'N/A'), 0, 1);
    $pdf->Cell(0, 6, 'Email: ' . htmlspecialchars($invoice_data['customer_email']), 0, 1);
    $pdf->Cell(0, 6, 'Phone: ' . htmlspecialchars($invoice_data['customer_phone'] ?? 'N/A'), 0, 1);
    $pdf->Ln(10);

    // Provider Information (From whom the service was purchased)
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(0, 6, 'Service Provider:', 0, 1);
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(0, 6, htmlspecialchars($invoice_data['provider_name']), 0, 1);
    $pdf->Cell(0, 6, htmlspecialchars($invoice_data['provider_address'] ?? 'N/A'), 0, 1);
    $pdf->Cell(0, 6, 'Email: ' . htmlspecialchars($invoice_data['provider_email']), 0, 1);
    $pdf->Cell(0, 6, 'Phone: ' . htmlspecialchars($invoice_data['provider_phone'] ?? 'N/A'), 0, 1);
    $pdf->Ln(10);


    // Payment Details
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(0, 6, 'Payment Date: ' . date('d M Y', strtotime($invoice_data['payment_date'])), 0, 1);
    $pdf->Cell(0, 6, 'Project Period: ' . date('d M Y', strtotime($invoice_data['project_start_date'])) . ' - ' . date('d M Y', strtotime($invoice_data['project_end_date'])), 0, 1);
    $pdf->Ln(10);

    // Invoice Table
    $header = ['Description', 'Qty', 'Unit Price', 'Total'];
    $pdf->InvoiceTable($header, $invoice_data);

    $filename = "invoice_" . $invoice_data['transaction_id'] . ".pdf";
    $pdf->Output('D', $filename);
    exit;

} catch (PDOException $e) {
    error_log("Generate Invoice PDO Exception: " . $e->getMessage());
    die("Database error: Unable to generate invoice. Please contact support.");
} catch (Exception $e) {
    error_log("Generate Invoice General Exception: " . $e->getMessage());
    die("Error generating invoice: " . htmlspecialchars($e->getMessage()));
}