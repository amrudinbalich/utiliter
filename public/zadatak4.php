<?php

require __DIR__ . '/../src/bootstrap.php';

session_start();

use App\Utiliter\Services\StepCounter;

$stepCounter = new StepCounter();

if (isset($_GET['action'])) {
    $action = $_GET['action'];
    if ($action === 'step') $stepCounter->increment();
    if ($action === 'reset') $stepCounter->reset();
    header('Location: zadatak4.php');
    exit;
}

?>
<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"><title>Zadatak 4</title></head>
<body>

    <?= $stepCounter->formatUserText() ?>
    <a href="?action=reset">Reset</a>

</body>
</html>