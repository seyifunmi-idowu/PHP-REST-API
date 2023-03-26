<?php

class InvoiceItemModel
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function findAll($invoice_id)
    {
        $query = "SELECT * FROM invoice_items WHERE invoice_id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(1, $invoice_id);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        return $stmt->fetchAll();
    }

    public function findOne($id)
    {
        $query = "SELECT * FROM invoice_items WHERE id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(1, $id);
        $stmt->execute();
        return $stmt->fetch();
    }

    public function insert($invoice_id, $quantity, $description, $unit)
    {
        try {
            $amount = $unit * $quantity;
            $query = "INSERT INTO invoice_items (invoice_id, quantity, description, unit, amount) VALUES (?,?,?,?,?)";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(1, $invoice_id);
            $stmt->bindParam(2, $quantity);
            $stmt->bindParam(3, $description);
            $stmt->bindParam(4, $unit);
            $stmt->bindParam(5, $amount);
            $stmt->execute();
            return true;
        } catch (PDOException $e) {
            echo $e->getMessage();
            return false;
        }
    }

    public function update($id, $invoice_id, $quantity, $description, $unit)
    {
        try {
            $query = "UPDATE invoice_items SET invoice_id = ?, quantity = ?, description = ?, unit = ?, amount = ? WHERE id = ?";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(1, $invoice_id);
            $stmt->bindParam(2, $quantity);
            $stmt->bindParam(3, $description);
            $stmt->bindParam(4, $unit);
            $stmt->bindParam(5, $unit * $quantity);
            $stmt->bindParam(6, $id);
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
            $query = "DELETE FROM invoice_items WHERE id = ?";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(1, $id);
            $stmt->execute();
            return true;
        } catch (PDOException $e) {
            echo $e->getMessage();
            return false;
        }
    }

    public function deleteByInvoiceId($invoice_id)
    {
        $query = "DELETE FROM invoice_items WHERE invoice_id = :invoice_id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':invoice_id', $invoice_id);
        $stmt->execute();
    }

}
