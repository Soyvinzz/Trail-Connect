<?php
require_once __DIR__ . '/../../includes/paths.php';

$bodyClass = isset($bodyClass) ? (string) $bodyClass : '';
$pageTitle = isset($pageTitle) ? (string) $pageTitle : 'TrailConnect';
if (!isset($assetBase)) {
    $assetBase = tc_public_base();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8'); ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,500;0,600;0,700;1,500&family=Montserrat:wght@300;400;500;600;700&family=Outfit:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo htmlspecialchars(tc_asset_url('assets/css/style.css'), ENT_QUOTES, 'UTF-8'); ?>">
    <link rel="stylesheet" href="<?php echo htmlspecialchars(tc_asset_url('assets/css/layout.css'), ENT_QUOTES, 'UTF-8'); ?>">
    <link rel="stylesheet" href="<?php echo htmlspecialchars(tc_asset_url('assets/css/components.css'), ENT_QUOTES, 'UTF-8'); ?>">
</head>
<body<?php echo $bodyClass !== '' ? ' class="' . htmlspecialchars($bodyClass, ENT_QUOTES, 'UTF-8') . '"' : ''; ?>>
