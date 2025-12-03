<?php
$workers = json_decode(file_get_contents(__DIR__.'/../../data/workers.json'), true);
echo '<h1>Treballadors</h1>';
foreach($workers as $w) echo '<div>'.htmlspecialchars($w['username']).' - '.htmlspecialchars($w['role']).'</div>';
?>