<?php
session_start();
require_once __DIR__ . '/../classes/FileManager.php';
require_once __DIR__ . '/../classes/Cistella.php';
require_once __DIR__ . '/../classes/Comanda.php';
require_once __DIR__ . '/../classes/Producte.php';

if (empty($_SESSION['user']) || ($_SESSION['role'] ?? '') !== 'client') {
    header('Location: login.php');
    exit;
}

$currentUser = $_SESSION['user'];
$areaDir = __DIR__.'/area_clients/' . $currentUser;
$cistellaFile = $areaDir . '/cistella';

if (!file_exists($cistellaFile)) {
    echo "No hi ha cistella.";
    exit;
}

$cartData = FileManager::readJson($cistellaFile);
$cistella = new Cistella($cartData);
$items = $cistella->getProductes();

if (empty($items)) {
    echo "Cistella buida.";
    exit;
}

// Calculate total
$productsFile = __DIR__.'/../gestio/productes/productes.json';
$productsData = FileManager::readJson($productsFile);

$total = 0;
foreach ($items as $pid => $qty) {
    foreach ($productsData as $p) {
        if (($p['id'] ?? '') == $pid) {
            $total += floatval($p['preu'] ?? 0) * $qty;
            break;
        }
    }
}
$totalWithIva = $total * 1.21;

// Create Comanda object
$orderId = date('Ymd_His') . '_' . substr(md5(uniqid()), 0, 8);
$date = date('c');

$comanda = new Comanda(
    $orderId,
    $date,
    $currentUser,
    $items,
    $totalWithIva
);

$orderData = $comanda->toArray();

// Save to user area (directly in user folder as per requirement)
FileManager::saveJson($areaDir . '/' . $orderId, $orderData);

// Save to comandes_copia
FileManager::saveJson(__DIR__ . '/../comandes_copia/' . $orderId, $orderData);

// Clear cart
$cistella->setProductes([]);
FileManager::saveJson($cistellaFile, []);

?>
<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <title>Comanda Creada</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container" style="text-align: center;">
        <h1>Comanda realitzada amb èxit</h1>
        <div class="alert alert-success" style="font-size: 1.2em;">Comanda creada amb id: <?php echo htmlspecialchars($orderId); ?></div>
        <p style="font-size: 1.2em;">Total: <strong><?php echo number_format($totalWithIva, 2); ?> €</strong></p>
        <div class="mt-20">
            <a href="dashboard.php" class="btn btn-primary">Tornar al Menú</a>
        </div>
    </div>
</body>
</html>