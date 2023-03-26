<?php
require_once 'database.php';
require_once '../bootstrap.php';
require_once 'InvoiceController.php';

use InvoicingSystem\DatabaseConnector;

// Allow any client to access
header("Access-Control-Allow-Origin: *");
// Let the client know the format of the data being returned
header("Content-Type: application/json");
// Get the HTTP method, path and body of the request
$method = $_SERVER['REQUEST_METHOD'];
// $request = explode('/', trim($_SERVER['PATH_INFO'],'/'));

$input = json_decode(file_get_contents('php://input'),true);

// Connect to the database
$dbConnection = (new DatabaseConnector())->getConnection();


$invoiceController = new InvoiceController($dbConnection);

$response = array();

switch ($method) {
    case 'POST':
        $invoiceController->createInvoice($input);
        break;
    case 'PUT':
        $invoiceController->updateInvoice($_GET['id'], $input);
        break;
    case 'GET':
        $invoiceController->getInvoice($_GET['id']);
        break;
    case 'DELETE':
        $invoiceController->deleteInvoice($_GET['id']);
        break;
    default:
        http_response_code(405);
        echo json_encode(["status" => false, 'message' => 'Invalid request method']);
        break;
}

/*
This is a sample input body
 {
    "company_id" : 1,
    "bill_to_name" : "John Smith",
    "bill_to_address" : "123 Main St",
    "bill_to_phone": "555-555-5555",
    "due_date": "2022-03-01",
    "items" : [
        {
            "description":"Computer",
            "quantity": 2,
            "unit": 1000
        },
        {
            "description":"Printer",
            "quantity": 1,
            "unit": 250
        }
    ],
    "comments" : [
        {
            "description" : "Great service"
        },
        {
            "description" : "Items arrived on time"
        }
    ]
}
 * 
 */


?>