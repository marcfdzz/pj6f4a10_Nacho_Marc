<?php
session_start();
if(empty($_SESSION['usuari']) || ($_SESSION['rol'] !== 'admin' && $_SESSION['rol'] !== 'treballador')){ header('Location: inici_sessio.php'); exit;}
?>
<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <title>Taulell Gestió</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="container">
        <h1>Gestió - Benvingut <?php echo htmlspecialchars($_SESSION['usuari']); ?></h1>
        <div class="nav-links">
            <?php if($_SESSION['rol'] === 'admin'): ?>
                <a href="treballadors.php">Treballadors</a>
            <?php endif; ?>
            <a href="clients.php">Clients</a>
            <a href="productes.php">Productes</a>
            <a href="comandes.php">Comandes</a>
            <a href="tancar_sessio.php" style="color: #dc3545;">Tancar Sessió</a>
        </div>
        <p>Selecciona una opció del menú per gestionar la botiga.</p>
    </div>
</body>
</html>