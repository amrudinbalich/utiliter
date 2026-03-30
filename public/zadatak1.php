<?php

$database = require __DIR__ . '/../src/bootstrap.php';

use App\Utiliter\Services\EcbImport;

// perform
$ecbImport = new EcbImport($database);
$ecbImport->import();

// output
echo $ecbImport->getImportTextStatus();