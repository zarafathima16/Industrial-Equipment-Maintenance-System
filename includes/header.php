<?php
if (!isset($pageTitle)) { $pageTitle = 'IndusCare'; }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?> | IndusCare</title>
    <link rel="stylesheet" href="css/style.css">
    <?php if (strpos($_SERVER['SCRIPT_NAME'], '/includes/') !== false): ?>
        <link rel="stylesheet" href="../css/style.css">
    <?php endif; ?>
</head>
<body>
<header class="topbar">
    <div class="brand"><div class="brand-mark">IC</div><div><strong>IndusCare</strong><span>Industrial Maintenance Management</span></div></div>
    <nav>
        <a href="index.php">Dashboard</a>
        <a href="equipment.php">Equipment</a>
        <a href="maintenance.php">Maintenance</a>
    </nav>
</header>
<main class="container">
