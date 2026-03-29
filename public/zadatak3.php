<?php

$database = require __DIR__ . '/../src/bootstrap.php';

// get lang
$lang = $_GET['lang'] ?? 'hr';
if (!in_array($lang, ['hr', 'en'])) {
    $lang = 'hr';
}

// get products
$proizvodi = $database->fetchAll(
    "SELECT p.code, o.title, o.content 
     FROM zadatak3_produkti p
     INNER JOIN zadatak3_opisi o ON p.id = o.product_id
     WHERE o.lang = ?",
    [$lang]
);

$total = count($proizvodi);

?>
<!DOCTYPE html>
<html lang="<?= $lang ?>">
<head>
    <meta charset="UTF-8">
    <title>Zadatak 3</title>
</head>
<body>
    <h1>Proizvodi</h1>

    <nav>
        <a href="?lang=hr">🇭🇷 Hrvatski</a> |
        <a href="?lang=en">🇬🇧 English</a>
    </nav>

    <p><?= $lang === 'hr' ? 'Prikazano proizvoda:' : 'Shown Results:' ?> <strong><?= $total ?></strong></p>

    <hr>

    <?php foreach ($proizvodi as $p): ?>
        <div>
            <h3><?= htmlspecialchars($p->title) ?></h3>
            <p><?= htmlspecialchars($p->content) ?></p>
            <small>Code: <?= htmlspecialchars($p->code) ?></small>
        </div>
        <hr>
    <?php endforeach; ?>

</body>
</html>