<?php

$database = require __DIR__ . '/../src/bootstrap.php';
$translations = require __DIR__ . '/../storage/translations.php';

// get lang
$lang = $_GET['lang'] ?? 'hr';
if (!in_array($lang, ['hr', 'en'])) {
    $lang = 'hr';
}

$t = $translations[$lang];

// get products
$proizvodi = $database->fetchAll(
    "SELECT p.code, o.title, o.content,
            c.name AS category, m.name AS manufacturer
     FROM zadatak3_produkti p
     INNER JOIN zadatak3_opisi o ON p.id = o.product_id
     LEFT JOIN zadatak3_categories c ON p.category_id = c.id
     LEFT JOIN zadatak3_manufacturers m ON p.manufacturer_id = m.id
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

    <p><?= $t['shown_results'] ?> <strong><?= $total ?></strong></p>

    <hr>

    <?php foreach ($proizvodi as $p): ?>
        <div>
            <h3><?= htmlspecialchars($p->title) ?></h3>
            <p><?= htmlspecialchars($p->content) ?></p>
            <small><?= $t['code'] ?> <?= htmlspecialchars($p->code) ?></small>

            <?php if ($p->category): ?>
                <small><?= $t['category'] ?> <?= htmlspecialchars($p->category) ?></small>
            <?php endif; ?>

            <?php if ($p->manufacturer): ?>
                <small><?= $t['manufacturer'] ?> <?= htmlspecialchars($p->manufacturer) ?></small>
            <?php endif; ?>
        </div>
        <hr>
    <?php endforeach; ?>

</body>
</html>