<?php

require_once 'Models/Invoice.php';
require_once 'Models/InvoiceItem.php';
require_once 'Models/InvoiceComment.php';

class InvoiceController
{
    private $db;
    private $invoiceModel;
    private $invoiceDescriptionModel;
    private $invoiceCommentModel;

    public function __construct($dbConnection)
    {
        $this->db = $dbConnection;
        $this->invoiceModel = new InvoiceModel($this->db);
        $this->invoiceDescriptionModel = new InvoiceItemModel($this->db);
        $this->invoiceCommentModel = new InvoiceCommentModel($this->db);
    }

    public function createInvoice($input)
    {
        $this->ValidateInput($input);

        //autogenerate invoice number
        $invoice_number = $this->generateInvoiceNumber();

        // Insert the invoice
        $invoice_id = $this->invoiceModel->insert($invoice_number, $input['bill_to_name'], $input['bill_to_address'], $input['bill_to_phone'], $input['due_date'], $input['company_id']);
        
        // Insert the invoice description items
        foreach ($input['items'] as $item) {
            $this->invoiceDescriptionModel->insert($invoice_id, $item['quantity'], $item['description'], $item['unit']);
        }
        
        // Insert the invoice comments
        foreach ($input['comments'] as $item) {
            $this->invoiceCommentModel->insert($invoice_id, $item['description']);
        }

        //Calculate total, tax and grand total
        $total = $this->invoiceModel->getInvoiceTotal($invoice_id);
        $tax = $this->invoiceModel->getInvoiceTax($invoice_id, 0.15);
        $grand_total = $this->invoiceModel->getInvoiceGrandTotal($invoice_id, 0.15);
        $data = ['invoice_id' => $invoice_id, 'total' => $total, 'tax' => $tax, 'grand_total' => $grand_total];

        // Return the invoice data
        http_response_code(201);
        echo json_encode(["status"=> true, "data"=> $data, 'message' => 'Invoice created successfully']);
    }

    public function getInvoice($invoice_id)
    {
        $invoice = $this->invoiceModel->findOne($invoice_id);
        if (!$invoice) {
            http_response_code(404);
            echo json_encode([
                'status' => false,
                'message' => 'Invoice not found']);
            exit();
        }
        http_response_code(200);
        echo json_encode([
            'status' => true,
            'data' => $invoice
        ]);
    }

    public function updateInvoice($id, $input)
    {
        // Validate the user input
        if (!isset($input['bill_to_name']) || !isset($input['bill_to_address']) || !isset($input['bill_to_phone']) || !isset($input['due_date'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid request']);
            exit();
        }
        $this->invoiceModel->update($id ,$input['bill_to_name'],$input['bill_to_address'],$input['bill_to_phone'],$input['due_date'],$input['company_id']);

        //Return success message
        http_response_code(200);
        echo json_encode(['message' => 'Invoice updated successfully']);
    }

    public function getAllInvoices()
    {
        $invoices = $this->invoiceModel->findAll();
        http_response_code(200);
        echo json_encode(['invoices' => $invoices]);
    }

    public function deleteInvoice($invoice_id)
    {
        $response = $this->invoiceModel->delete($invoice_id);
        if ($response){
            http_response_code(200);
            echo json_encode(["status" => true, 'message' => 'Invoice deleted successfully']);
        } else{
            http_response_code(400);
            echo json_encode(["status" => false, 'message' => 'Cannot delete Invoice']);
        }
    }

    public function generateInvoiceNumber() {
        // get the last invoice number from the database
        $lastInvoiceNumber = $this->invoiceModel->getLastInvoiceNumber();
        // add 1 to the last invoice number
        $nextInvoiceNumber = $lastInvoiceNumber + 1;

        return str_pad($nextInvoiceNumber, 6, "0", STR_PAD_LEFT);
    }

    public function ValidateInput($input){
        // Initialize variables for storing errors
        $errors = array();
        $error = false;

        // Validate the company_id
        if (!isset($input['company_id']) ) {
            $error = true;
            $errors['company_id'] = "Company ID is required.";
        } elseif (!is_numeric($input['company_id'])) {
            $error = true;
            $errors['company_id'] = "Company ID must be a number.";
        }

        // Validate the bill_to_name
        if (!isset($input['bill_to_name'])) {
            $error = true;
            $errors['bill_to_name'] = "Bill to name is required.";
        }

        // Validate the bill_to_address
        if (!isset($input['bill_to_address'])) {
            $error = true;
            $errors['bill_to_address'] = "Bill to address is required.";
        }

        // Validate the bill_to_phone
        if (!isset($input['bill_to_phone'])) {
            $error = true;
            $errors['bill_to_phone'] = "Bill to phone is required.";
        }

        // Validate the due_date
        if (!isset($input['due_date'])) {
                $error = true;
            $errors['due_date'] = "Due date is required.";
        } elseif (!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $input['due_date'])) {
            $error = true;
            $errors['due_date'] = "Due date must be in the format YYYY-MM-DD.";
        }

        // Validate the items array
        if (!isset($input['items']) || !is_array($input['items'])) {
            $error = true;
            $errors['items'] = "An array of items is required.";
        } else {
            $item_errors = array();
            foreach ($input['items'] as $index => $item) {
                // Validate the description
                if (empty($item['description'])) {
                    $item_errors[$index]['description'] = "Description is required.";
                }

                // Validate the quantity
                if (empty($item['quantity'])) {
                    $item_errors[$index]['quantity'] = "Quantity is required.";
                } elseif (!is_numeric($item['quantity'])) {
                    $item_errors[$index]['quantity'] = "Quantity must be a number.";
                }

                // Validate the unit
                if (empty($item['unit'])) {
                    $item_errors[$index]['unit'] = "Unit is required.";
                } elseif (!is_numeric($item['unit'])) {
                    $item_errors[$index]['unit'] = "Unit must be a number.";
                }
            
            }

            // If item errors, add them to the overall errors array
            if (!empty($item_errors)) {
                $error = true;
                $errors['items'] = $item_errors;
            }
        }

        // If there are any errors, return them
        if ($error) {
            http_response_code(400);
            echo json_encode(array("status" => false, "message" => $errors));
            exit;
        }

    }
}
