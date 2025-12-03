<?php
session_start();
if(empty($_SESSION['user'])){ header('Location: login.php'); exit; }
$areaDir = __DIR__.'/area_clients/'.$_SESSION['user'];
$cistellaFile = $areaDir.'/cistella.json';
if(!file_exists($cistellaFile)){ echo 'No hi ha cistella'; exit;}
$cart = json_decode(file_get_contents($cistellaFile), true);
$ordersFile = __DIR__.'/../data/orders.json';
$orders = json_decode(file_get_contents($ordersFile), true);
$id = date('Ymd_His').'_'.substr(md5(json_encode($cart)),0,8);
$order = ['id'=>$id,'user'=>$_SESSION['user'],'items'=>$cart['items'],'subtotal'=>0,'iva'=>0,'total'=>0,'status'=>'pendent','created_at'=>date('c')];
$orders[] = $order;
file_put_contents($ordersFile, json_encode($orders, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE));
$userOrdersDir = $areaDir.'/orders';
if (!file_exists($userOrdersDir)) {
    mkdir($userOrdersDir, 0777, true);
}
// Save the full order details, not just the raw cart
file_put_contents($userOrdersDir.'/'.$id.'.order', json_encode($order, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE));
// Remove the cart file
if (file_exists($cistellaFile)) {
    unlink($cistellaFile);
}

$backupOrdersDir = __DIR__.'/../comandes_copia';
if (!file_exists($backupOrdersDir)) {
    mkdir($backupOrdersDir, 0777, true);
}
file_put_contents($backupOrdersDir.'/'.$id.'.order', json_encode($order, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE));
?>
<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comanda Creada</title>
    <style>
        body { font-family: sans-serif; padding: 20px; text-align: center; }
        .message { margin: 20px 0; font-size: 1.2em; color: green; }
        .btn { display: inline-block; padding: 10px 20px; background-color: #007bff; color: white; text-decoration: none; border-radius: 5px; }
        .btn:hover { background-color: #0056b3; }
    </style>
</head>
<body>
    <h1>Comanda realitzada amb èxit</h1>
    <p class="message">Comanda creada amb id: <?php echo $id; ?></p>
    <a href="dashboard.php" class="btn">Tornar al Menú</a>
</body>
</html>