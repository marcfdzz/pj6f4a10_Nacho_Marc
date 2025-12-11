<?php
session_start();
if(empty($_SESSION['usuari'])){ header('Location: inici_sessio.php'); exit; }
?>
<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Client</title>
    <link rel="stylesheet" href="../css/compra.css">
</head>
<body>
    <div class="container">
        <h1>Benvingut, <?php echo htmlspecialchars($_SESSION['usuari']); ?></h1>
        <div class="nav-links">
            <a href='productes.php'>Productes</a>
            <a href='cistella.php'>La meva cistella</a>
            <a href='dades.php'>Les meves dades</a>
            <a href='sollicitud.php'>Sol·licitar canvi dades</a>
            <a href='tancar_sessio.php' style="color: #dc3545;">Tancar Sessió</a>
        </div>
        <p>Selecciona una opció del menú per començar.</p>
    </div>
</body>
</html>