<?php

$database = require __DIR__ . '/../src/bootstrap.php';

use App\Utiliter\Services\EcbImport;

$ecbImport = new EcbImport($database);
$ecbImport->import();

echo $ecbImport->getImportTextStatus();