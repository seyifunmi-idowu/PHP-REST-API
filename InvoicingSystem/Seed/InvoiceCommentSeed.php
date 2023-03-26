<?php
require 'bootstrap.php';

$statement = <<<EOS
  CREATE TABLE invoice_comment (
    id INT PRIMARY KEY AUTO_INCREMENT,
    invoice_id INT NOT NULL,
    description VARCHAR(255) NOT NULL,
    FOREIGN KEY (invoice_id) REFERENCES invoice(id)
  );

  INSERT INTO invoice_comment (invoice_id, description)
  VALUES (1, 'Comment 1'),
        (1, 'Comment 2'),
        (2, 'Comment 3'),
        (3, 'Comment 4');

EOS;

try {
    $createTable = $dbConnection->exec($statement);
    echo "Success!\n";
} catch (\PDOException $e) {
    exit($e->getMessage());
}
