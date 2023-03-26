<?php
require_once 'InvoiceItem.php';
require_once 'InvoiceComment.php';

class InvoiceModel
{
    private $db;
    private $invoiceItem;
    private $invoiceComment;

    public function __construct($db)
    {
        $this->db = $db;
        $this->invoiceItem = new InvoiceItemModel($this->db);
        $this->invoiceComment = new InvoiceCommentModel($this->db);
    }

    public function findAll()
    {
        $query = "SELECT * FROM invoice";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        return $stmt->fetchAll();
    }

    public function findOne($id)
    {
        $query = "SELECT * FROM invoice WHERE id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(1, $id);
        $stmt->execute();
        if ($stmt->rowCount() > 0) {
            $invoice = $stmt->fetchObject();
            $invoice->total = $this->getInvoiceTotal($id);
            $invoice->tax = $this->getInvoiceTax($id, 1.5);
            $invoice->grand_total = $this->getInvoiceGrandTotal($id, 1.5);
            $invoice->items = $this->invoiceItem->findAll($id);
            $invoice->comments = $this->invoiceComment->findAll($id);
            return $invoice;
        } else {
            return null;
        }
    }

    public function insert($invoice_number, $bill_to_name, $bill_to_address, $bill_to_phone, $due_date, $company_id)
    {
        try {
            $query = "INSERT INTO invoice (invoice_number, bill_to_name, bill_to_address, bill_to_phone, due_date, company_id) VALUES (?,?,?,?,?,?)";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(1, $invoice_number);
            $stmt->bindParam(2, $bill_to_name);
            $stmt->bindParam(3, $bill_to_address);
            $stmt->bindParam(4, $bill_to_phone);
            $stmt->bindParam(5, $due_date);
            $stmt->bindParam(6, $company_id);
            $stmt->execute();
            return $this->db->lastInsertId();
        } catch (PDOException $e) {
            echo $e->getMessage();
            return false;
        }
    }

    public function update($id, $bill_to_name, $bill_to_address, $bill_to_phone, $due_date)
    {
        try {
            $query = "UPDATE invoice SET bill_to_name = ?, bill_to_address = ?, bill_to_phone = ?, due_date = ? WHERE id = ?";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(1, $bill_to_name);
            $stmt->bindParam(2, $bill_to_address);
            $stmt->bindParam(3, $bill_to_phone);
            $stmt->bindParam(4, $due_date);
            $stmt->bindParam(5, $id);
            $stmt->execute();
            return true;
        } catch (PDOException $e) {
            echo $e->getMessage();
            return false;
        }
    }

    public function delete($id)
    {
        try {
            $this->invoiceComment->deleteByInvoiceId($id);
            $this->invoiceItem->deleteByInvoiceId($id);
            $query = "DELETE FROM invoice WHERE id = ?";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(1, $id);
            $stmt->execute();
            return true;
        } catch (PDOException $e) {
            echo $e->getMessage();
            return false;
        }
    }

    public function getInvoiceTotal($invoice_id)
    {
        $query = "SELECT SUM(amount) as total FROM invoice_items WHERE invoice_id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(1, $invoice_id);
        $stmt->execute();
        $result = $stmt->fetch();
        return (string)round($result['total'],2);
    }

    public function getInvoiceTax($invoice_id, $tax_rate)
    {
        $total = $this->getInvoiceTotal($invoice_id);
        $tax = $total * ($tax_rate / 100);
        return (string)round($tax, 2);
    }

    public function getInvoiceGrandTotal($invoice_id, $tax_rate)
    {
        $total = $this->getInvoiceTotal($invoice_id);
        $tax = $this->getInvoiceTax($invoice_id, $tax_rate);
        $grand_total = $total + $tax;
        return (string)round($grand_total, 2);
    }

    public function getLastInvoiceNumber() 
    {
        try {
            $query = "SELECT invoice_number FROM invoice ORDER BY invoice_number DESC LIMIT 1";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            $result = $stmt->fetch();
            return $result['invoice_number'];
        } catch (PDOException $e) {
            echo $e->getMessage();
            return false;
        }
    }
}