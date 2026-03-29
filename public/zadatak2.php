<?php

$database = require __DIR__ . '/../src/bootstrap.php';

use App\Utiliter\Services\ProductImport;

header('Content-type: application/json');

// do import
$import = new ProductImport($database);
$import->import();

// show output
echo json_encode([
    'kategorije' => $database->fetchAll('SELECT * FROM zadatak2_kategorije'),
    'proizvodjaci' => $database->fetchAll('SELECT * FROM zadatak2_proizvodjaci'),
    'produkti' => $database->fetchAll('SELECT * FROM zadatak2_produkti')
]);