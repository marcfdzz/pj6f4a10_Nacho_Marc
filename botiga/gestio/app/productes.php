<?php
$products = json_decode(file_get_contents(__DIR__.'/../../data/products.json'), true);
echo '<h1>Productes</h1>';
foreach($products as $p) echo '<div>'.htmlspecialchars($p['product_id']).' - '.htmlspecialchars($p['name']).' - '.htmlspecialchars($p['price']).'€</div>';
?>