<?php
session_start();
if(empty($_SESSION['user']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'treballador')){ header('Location: login.php'); exit;}
?>
<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Gestió</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="container">
        <h1>Gestió - Benvingut <?php echo htmlspecialchars($_SESSION['user']); ?></h1>
        <div class="nav-links">
            <?php if($_SESSION['role'] === 'admin'): ?>
                <a href="treballadors.php">Treballadors</a>
            <?php endif; ?>
            <a href="clients.php">Clients</a>
            <a href="productes.php">Productes</a>
            <a href="comandes.php">Comandes</a>
            <a href="logout.php" style="color: #dc3545;">Logout</a>
        </div>
        <p>Selecciona una opció del menú per gestionar la botiga.</p>
    </div>
</body>
</html>