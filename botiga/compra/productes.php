<?php
$products = json_decode(file_get_contents(__DIR__.'/../data/products.json'), true);
echo "<h1>Productes</h1>";
echo "<form method='post' action='cart.php'>";
foreach($products as $p){
  echo "<div style='border:1px solid #ddd; padding:10px; margin-bottom:10px;'>";
  echo "<h3>".htmlspecialchars($p['name'])."</h3>";
  echo "<p>".htmlspecialchars($p['description'])."</p>";
  echo "<p>Preu: ".htmlspecialchars($p['price'])." €</p>";
  echo "<label>Quantitat: <input type='number' name='quantities[".htmlspecialchars($p['product_id'])."]' value='0' min='0' size='2'></label>";
  echo "</div>";
}
echo "<button type='submit' style='padding:10px 20px; font-size:1.2em; background:#28a745; color:white; border:none; border-radius:5px; cursor:pointer;'>Afegir a la cistella</button>";
echo "</form>";
?>