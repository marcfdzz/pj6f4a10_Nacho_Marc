<?php
$orders = json_decode(file_get_contents(__DIR__.'/../../data/orders.json'), true);
echo '<h1>Comandes pendents</h1>';
foreach($orders as $o){
  if($o['status']==='pendent'){
    echo '<div>ID: '.htmlspecialchars($o['id']).' - Usuari: '.htmlspecialchars($o['user']).' - <a href="detall_comanda.php?id='.urlencode($o['id']).'">Veure Detalls i Processar</a></div>';
  }
}
echo '<p><a href="dashboard.php">Tornar al Dashboard</a></p>';
?>