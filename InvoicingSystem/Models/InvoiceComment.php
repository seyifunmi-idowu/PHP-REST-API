<?php 

class InvoiceCommentModel
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function findAll($invoiceId)
    {
        $stmt = $this->db->prepare('SELECT * FROM invoice_comment WHERE invoice_id = :invoiceId');
        $stmt->execute([':invoiceId' => $invoiceId]);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        return $stmt->fetchAll();
    }

    public function findOne($id)
    {
        $stmt = $this->db->prepare('SELECT * FROM invoice_comment WHERE id = :id');
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    public function insert($invoiceId, $description)
    {
        $stmt = $this->db->prepare('INSERT INTO invoice_comment (invoice_id, description) VALUES (:invoiceId, :description)');
        $stmt->execute([':invoiceId' => $invoiceId, ':description' => $description]);
    }

    public function update($id, $invoiceId, $description)
    {
        $stmt = $this->db->prepare('UPDATE invoice_comment SET invoice_id = :invoiceId, description = :description WHERE id = :id');
        $stmt->execute([':id' => $id, ':invoiceId' => $invoiceId, ':description' => $description]);
    }

    public function delete($id)
    {
        $stmt = $this->db->prepare('DELETE FROM invoice_comment WHERE id = :id');
        $stmt->execute([':id' => $id]);
    }

    public function deleteByInvoiceId($invoiceId)
    {
        $stmt = $this->db->prepare('DELETE FROM invoice_comment WHERE invoice_id = :invoiceId');
        $stmt->execute([':invoiceId' => $invoiceId]);
    }

}
