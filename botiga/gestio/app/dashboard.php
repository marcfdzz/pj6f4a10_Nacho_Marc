<?php
session_start();
if(empty($_SESSION['worker'])){ header('Location: login.php'); exit;}
echo '<h1>Gestió - Benvingut '.htmlspecialchars($_SESSION['worker']).'</h1>';
echo '<p><a href="treballadors.php">Treballadors</a> | <a href="clients.php">Clients</a> | <a href="productes.php">Productes</a> | <a href="comandes.php">Comandes</a> | <a href="../../data/logout.php">Logout</a></p>';
?>