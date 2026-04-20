<?php
$flash = getFlash();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="<?= isset($assetBase) ? e($assetBase) : '.'; ?>/assets/ico.ico" type="image/x-icon">
    <title><?= isset($pageTitle) ? e($pageTitle) . ' | ' . APP_NAME : APP_NAME; ?></title>
    <link rel="stylesheet" href="<?= isset($assetBase) ? e($assetBase) : '.'; ?>/assets/css/style.css">
</head>
<body>
<?php if ($flash): ?>
    <div class="flash flash-<?= e($flash['type']) ?>">
        <?= e($flash['message']) ?>
    </div>
<?php endif; ?>
