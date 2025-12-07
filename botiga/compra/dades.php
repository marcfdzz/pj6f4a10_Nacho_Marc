<?php
session_start();
require_once __DIR__ . '/../classes/FileManager.php';

if (empty($_SESSION['user']) || ($_SESSION['role'] ?? '') !== 'client') {
    header('Location: login.php');
    exit;
}

$user = $_SESSION['user'];
$dataFile = __DIR__ . '/area_clients/' . $user . '/dades';

if (!file_exists($dataFile)) {
    echo "No s'han trobat les dades.";
    exit;
}

$userData = FileManager::readJson($dataFile);
?>
<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <title>Les meves dades</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <div style="display:flex; justify-content:space-between; align-items:center;">
            <h1>Dades del Client</h1>
            <a href="dashboard.php" class="btn btn-secondary no-print">← Tornar al Dashboard</a>
        </div>
        
        <div class="field" style="margin-top: 20px;">
            <span class="label">Usuari:</span>
            <span class="value"><?php echo htmlspecialchars($userData['username'] ?? ''); ?></span>
        </div>
        <div class="field">
            <span class="label">Nom Complet:</span>
            <span class="value"><?php echo htmlspecialchars($userData['name'] ?? ''); ?></span>
        </div>
        <div class="field">
            <span class="label">Email:</span>
            <span class="value"><?php echo htmlspecialchars($userData['email'] ?? ''); ?></span>
        </div>
        <div class="field">
            <span class="label">Telèfon:</span>
            <span class="value"><?php echo htmlspecialchars($userData['phone'] ?? ''); ?></span>
        </div>
        <div class="field">
            <span class="label">Adreça:</span>
            <span class="value"><?php echo htmlspecialchars($userData['address'] ?? ''); ?></span>
        </div>
        
        <div class="no-print mt-20">
            <button onclick="window.print()" class="btn btn-primary">Descarregar PDF</button>
        </div>
    </div>
</body>
</html>
