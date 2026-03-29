<?php

$database = require __DIR__ . '/../src/bootstrap.php';

use App\Utiliter\Services\XmlProductImport;

$import = new XmlProductImport($database);
$import->import();

echo "Import uspješan!";