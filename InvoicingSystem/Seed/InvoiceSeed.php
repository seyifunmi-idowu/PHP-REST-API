<?php
require 'bootstrap.php';

$statement = <<<EOS
    CREATE TABLE IF NOT EXISTS invoice (
        id INT PRIMARY KEY AUTO_INCREMENT,
        invoice_number INT NOT NULL,
        bill_to_name VARCHAR(255) NOT NULL,
        bill_to_address VARCHAR(255) NOT NULL,
        bill_to_phone VARCHAR(255) NOT NULL,
        due_date DATE NOT NULL,
        company_id INT,
        FOREIGN KEY (company_id) REFERENCES company(id),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=INNODB;

    INSERT INTO invoice (invoice_number, bill_to_name, bill_to_address, bill_to_phone, due_date, company_id)
    VALUES (1, 'John Doe', '123 Main St', '555-555-5555', '2022-01-01', 1), 
       (2, 'Jane Smith', '456 Park Ave', '555-555-5556', '2022-02-01', 2),
       (3, 'Bob Johnson', '789 Elm St', '555-555-5557', '2022-03-01', 3);
       
EOS;

try {
    $createTable = $dbConnection->exec($statement);
    echo "Success!\n";
} catch (\PDOException $e) {
    exit($e->getMessage());
}
