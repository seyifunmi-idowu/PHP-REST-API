<?php
require 'vendor/autoload.php';

use Dotenv\Dotenv;

use InvoicingSystem\DatabaseConnector;

$dotenv = new Dotenv(__DIR__);
$dotenv->load();

$dbConnection = (new DatabaseConnector())->getConnection();
