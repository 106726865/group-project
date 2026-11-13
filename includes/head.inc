<?php
$pageTitle   = $pageTitle ?? 'CLAZITERIUM';
$pageStyles  = $pageStyles ?? [];
$inlineStyle = $inlineStyle ?? '';
$bodyClass   = $bodyClass ?? '';
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="style/layout.css">
  <link rel="stylesheet" href="style/colors.css">
  <link rel="stylesheet" href="style/animations.css">
  <?php foreach ($pageStyles as $style): ?>
    <link rel="stylesheet" href="style/<?= htmlspecialchars($style) ?>">
  <?php endforeach; ?>
  <link rel="stylesheet" href="style/styles.css">
  <?php if ($inlineStyle !== ''): ?>
    <style>
      <?= $inlineStyle ?>
    </style>
  <?php endif; ?>
  <title><?= htmlspecialchars($pageTitle) ?></title>
</head>
<body<?= $bodyClass !== '' ? ' class="' . htmlspecialchars($bodyClass) . '"' : '' ?>>
