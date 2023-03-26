<?php

require_once 'Invoice.php';
require_once 'InvoiceDescription.php';

// Connect to the database
$host = getenv('DB_HOST');
$db   = getenv('DB_DATABASE');
$user = getenv('DB_USERNAME');
$pass = getenv('DB_PASSWORD');

try {
    $dbConnection = new \PDO("mysql:host=$host;charset=utf8mb4;dbname=$db", $user, $pass);
} catch (\PDOException $e) {
    print_r("error");
    exit($e->getMessage());
}

// Initialize the invoice and invoice_description classes
$invoice = new Invoice($dbConnection);
$invoiceDescription = new InvoiceDescription($dbConnection);

// Get the user input
$inputJSON = file_get_contents('php://input');
$input = json_decode($inputJSON, true);

// Validate the user input
if (!isset($input['invoice_number']) || !isset($input['bill_to_name']) || !isset($input['due_date']) || !isset($input['invoice_description'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid request']);
    exit();
}

// Insert the invoice
$invoiceData = [
    'invoice_number' => $input['invoice_number'],
    'bill_to_name' => $input['bill_to_name'],
    'bill_to_address' => $input['bill_to_address'],
    'bill_to_phone' => $input['bill_to_phone'],
    'due_date' => $input['due_date'],
    'created_at' => date('Y-m-d H:i:s'),
    'updated_at' => date('Y-m-d H:i:s'),
    'company_id' => $input['company_id'],
];
$invoice_id = $invoice->insert($invoiceData);

// Insert the invoice description items
foreach ($input['invoice_description'] as $item) {
    $invoiceDescription->insert($invoice_id, $item['quantity'], $item['description'], $item['unit'], $item['amount']);
}

//Calculate total, tax and grand total
$total = $invoice->getInvoiceTotal($invoice_id);
$tax = $invoice->getInvoiceTax($invoice_id, 0.15);
$grand_total = $invoice->getInvoiceGrandTotal($invoice_id, 0.15);

// Return the invoice data
http_response_code(201);
echo json_encode(['invoice_id' => $invoice_id, 'total' => $total, 'tax' => $tax, 'grand_total' => $grand_total, 'message' => 'Invoice created successfully']);
