<?php
$clients = json_decode(file_get_contents(__DIR__.'/../../data/clients.json'), true);
echo '<h1>Clients</h1>';
foreach($clients as $c) echo '<div>'.htmlspecialchars($c['username']).' - '.htmlspecialchars($c['email']).'</div>';
?>