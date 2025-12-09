<?php
session_start();
require_once __DIR__ . '/../classes/GestorFitxers.php';

if (empty($_SESSION['usuari']) || ($_SESSION['rol'] ?? '') !== 'client') {
    header('Location: inici_sessio.php');
    exit;
}

$rutaProductes = __DIR__ . '/../productes_copia/productes.json';
$llistaProductes = GestorFitxers::llegirTot($rutaProductes);

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
            <a href='taulell.php' class="btn btn-secondary">← Tornar al Taulell</a>
        </div>
        
        <div class="nav-links">
            <a href='cistella.php'>Veure Cistella</a>
        </div>

        <form method='post' action='cistella.php'>
            <div class="product-grid">
                <?php foreach($llistaProductes as $p): ?>
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