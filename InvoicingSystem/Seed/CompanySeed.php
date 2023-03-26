<?php
require 'bootstrap.php';

$statement = <<<EOS
CREATE TABLE company (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    address VARCHAR(255) NOT NULL,
    city VARCHAR(255) NOT NULL,
    phone VARCHAR(255) NOT NULL,
    fax VARCHAR(255) DEFAULT NULL,
    website VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
  ) ENGINE=INNODB;

INSERT INTO company (name, address, city, phone, fax, website) VALUES ('Acme Inc', '123 Main St', 'New York', '555-555-5555', '555-555-5556', 'www.acmeinc.com');
INSERT INTO company (name, address, city, phone, fax, website) VALUES ('XYZ Corp', '456 Market St', 'Chicago', '555-555-5557', '555-555-5558', 'www.xyzcorp.com');
INSERT INTO company (name, address, city, phone, fax, website) VALUES ('ABC LLC', '789 Elm St', 'Los Angeles', '555-555-5559', '555-555-5560', 'www.abcllc.com');

EOS;

try {
    $createTable = $dbConnection->exec($statement);
    echo "Success!\n";
} catch (\PDOException $e) {
    exit($e->getMessage());
}
