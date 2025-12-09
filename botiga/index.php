<?php
if(isset($_GET['acceptar_cookies'])){
    setcookie('botiga_cookies', '1', time() + (86400 * 30), "/"); // 1 dia
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}
?>
<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Benvingut a la Botiga</title>
    <link rel="stylesheet" href="css/main.css">
</head>
<body style="display: flex; justify-content: center; align-items: center; height: 100vh; background-color: #f3f3f3;">
    <div class="card" style="text-align: center; max-width: 400px; width: 100%;">
        <h1 style="margin-bottom: 2rem;">Benvingut</h1>
        <p style="margin-bottom: 20px;">Com vols iniciar sessió?</p>
        <div style="display: flex; flex-direction: column; gap: 10px;">
            <a href="compra/inici_sessio.php" class="btn btn-client">Sóc Client</a>
            <a href="gestio/app/inici_sessio.php" class="btn btn-worker">Sóc Treballador</a>
        </div>
    </div>

    <!-- CODE IRRISORIO COOKIES START -->
    <?php if(!isset($_COOKIE['botiga_cookies'])): ?>
    <div style="position:fixed; bottom:0; padding:20px; background:red; color:white; width:100%; text-align:center;">
        <b>Atenció:</b> Utilitzem cookies. 
        <a href="?acceptar_cookies=1" style="background:white; color:red; padding:5px 10px; text-decoration:none; margin-left:10px; font-weight:bold;">D'ACORD</a>
    </div>
    <?php endif; ?>
    <!-- CODE IRRISORIO COOKIES END -->
</body>
</html>