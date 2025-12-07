<?php
session_start();
require_once __DIR__ . '/../classes/FileManager.php';

if (empty($_SESSION['user']) || ($_SESSION['role'] ?? '') !== 'client') {
    header('Location: login.php');
    exit;
}

$productsFile = __DIR__ . '/../gestio/productes/productes.json';
$products = FileManager::readJson($productsFile);

?>
<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Productes</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <div style="display:flex; justify-content:space-between; align-items:center;">
            <h1>Productes</h1>
            <a href='dashboard.php' class="btn btn-secondary">← Tornar al Dashboard</a>
        </div>
        
        <div class="nav-links">
            <a href='cart.php'>Veure Cistella</a>
        </div>

        <form method='post' action='cart.php'>
            <div class="product-grid">
                <?php foreach($products as $p): ?>
                <div class="product-card">
                    <div>
                        <h3><?php echo htmlspecialchars($p['nom'] ?? ''); ?></h3>
                        <div class="description"><?php echo htmlspecialchars($p['descripcio'] ?? ''); ?></div>
                        <div class="price"><?php echo htmlspecialchars($p['preu'] ?? ''); ?> €</div>
                    </div>
                    <div>
                        <label>Quantitat: <input type='number' name='quantities[<?php echo htmlspecialchars($p['id'] ?? ''); ?>]' value='0' min='0' size='2'></label>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            
            <div class="mt-20 text-right">
                <button type='submit' class="btn btn-success" style="font-size: 1.1em;">Afegir a la cistella</button>
            </div>
        </form>
    </div>
</body>
</html>