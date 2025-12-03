<?php
session_start();
if(empty($_SESSION['user'])){ header('Location: login.php'); exit; }
echo "<h1>Benvingut, ".htmlspecialchars($_SESSION['user'])."</h1>";
echo "<p><a href='productes.php'>Productes</a> | <a href='cart.php'>La meva cistella</a> | <a href='../data/logout.php'>Logout</a></p>";
?>