<?php
session_start();
if(empty($_SESSION['worker'])){ header('Location: login.php'); exit;}
$id = $_GET['id'] ?? '';
$ordersFile = __DIR__.'/../../data/orders.json';
$orders = json_decode(file_get_contents($ordersFile), true);
foreach($orders as &$o){
  if($o['id']===$id && $o['status']=='pendent'){
    $o['status']='processada';
    $o['processed_by'] = $_SESSION['worker'];
    $o['processed_at'] = date('c');
    file_put_contents($ordersFile, json_encode($orders, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE));
    $sourceFile = __DIR__.'/../../comandes_copia/'.$id.'.order';
    $destDir = __DIR__.'/../../gestio/comandes_gestionades';
    if (!file_exists($destDir)) {
        mkdir($destDir, 0777, true);
    }
    if (file_exists($sourceFile)) {
        rename($sourceFile, $destDir.'/'.$id.'.order');
    }
    
    // Remove the order from the client's view
    $clientOrderFile = __DIR__.'/../../compra/area_clients/'.$o['user'].'/orders/'.$id.'.order';
    if (file_exists($clientOrderFile)) {
        unlink($clientOrderFile);
    }

    // header('Location: comandes.php');
    ?>
    <!DOCTYPE html>
    <html lang="ca">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Comanda Processada</title>
        <style>
            body { font-family: sans-serif; padding: 20px; text-align: center; }
            .message { margin: 20px 0; font-size: 1.2em; color: green; }
            .btn { display: inline-block; padding: 10px 20px; background-color: #007bff; color: white; text-decoration: none; border-radius: 5px; }
            .btn:hover { background-color: #0056b3; }
        </style>
    </head>
    <body>
        <h1>Comanda Processada Correctament</h1>
        <p class="message">La comanda <?php echo htmlspecialchars($id); ?> s'ha marcat com a processada.</p>
        <a href="comandes.php" class="btn">Tornar a Comandes</a>
    </body>
    </html>
    <?php
    exit;
  }
}
echo "No s'ha trobat la comanda o ja està processada.";
?>