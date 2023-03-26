<?php
require 'bootstrap.php';

$statement = <<<EOS
CREATE TABLE invoice_items (
    id INT PRIMARY KEY AUTO_INCREMENT,
    invoice_id INT NOT NULL,
    description VARCHAR(255) NOT NULL,
    quantity INT NOT NULL,
    unit INT NOT NULL,
    amount DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (invoice_id) REFERENCES invoice(id)
  );
  
  DELIMITER $$
  CREATE TRIGGER update_invoice_time
  AFTER INSERT ON invoice_items
  FOR EACH ROW
  BEGIN
  UPDATE invoice SET updated_at = NOW() WHERE id = NEW.invoice_id;
  END $$
  DELIMITER ;

  INSERT INTO invoice_items (invoice_id, description, quantity, unit, amount)
    VALUES (1, 'Product 1', 5, 10, 50.00),
        (1, 'Product 2', 10, 20, 200.00),
        (2, 'Product 3', 2, 15, 30.00),
        (2, 'Product 4', 8, 25, 200.00),
        (3, 'Product 5', 3, 30, 90.00);
        (3, 'Product 4', 15, 10, 150.00);

EOS;

try {
    $createTable = $dbConnection->exec($statement);
    echo "Success!\n";
} catch (\PDOException $e) {
    exit($e->getMessage());
}