<?php
session_start();
if(empty($_SESSION['user'])){ header('Location: login.php'); exit; }
?>
<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Client</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <h1>Benvingut, <?php echo htmlspecialchars($_SESSION['user']); ?></h1>
        <div class="nav-links">
            <a href='productes.php'>Productes</a>
            <a href='cart.php'>La meva cistella</a>
            <a href='dades.php'>Les meves dades</a>
            <a href='sollicitud.php'>Sol·licitar canvi dades</a>
            <a href='logout.php' style="color: #dc3545;">Logout</a>
        </div>
        <p>Selecciona una opció del menú per començar.</p>
    </div>
</body>
</html>