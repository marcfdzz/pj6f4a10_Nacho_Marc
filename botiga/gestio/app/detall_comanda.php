<?php
session_start();
if(empty($_SESSION['worker'])){ header('Location: login.php'); exit;}

$id = $_GET['id'] ?? '';
$ordersFile = __DIR__.'/../../data/orders.json';
$orders = json_decode(file_get_contents($ordersFile), true);
$productsFile = __DIR__.'/../../data/products.json';
$products = json_decode(file_get_contents($productsFile), true);

$order = null;
foreach($orders as $o){
    if($o['id'] === $id){
        $order = $o;
        break;
    }
}

if(!$order){
    echo "Comanda no trobada.";
    exit;
}

// Helper to find product details
function getProduct($pid, $products) {
    foreach($products as $p) {
        if($p['product_id'] == $pid) return $p;
    }
    return null;
}

$totalOrder = 0;
?>
<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detall Comanda <?php echo htmlspecialchars($id); ?></title>
    <style>
        body { font-family: sans-serif; padding: 20px; background-color: #f4f4f4; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        h1 { border-bottom: 2px solid #eee; padding-bottom: 10px; }
        .info { margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { text-align: left; padding: 10px; border-bottom: 1px solid #ddd; }
        th { background-color: #f8f8f8; }
        .total { text-align: right; font-size: 1.2em; font-weight: bold; margin-top: 10px; }
        .actions { text-align: right; margin-top: 20px; }
        .btn { padding: 10px 20px; text-decoration: none; border-radius: 5px; color: white; font-weight: bold; }
        .btn-process { background-color: #28a745; }
        .btn-process:hover { background-color: #218838; }
        .btn-back { background-color: #6c757d; margin-right: 10px; }
        .btn-back:hover { background-color: #5a6268; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Detall de la Comanda</h1>
        <div class="info">
            <p><strong>ID Comanda:</strong> <?php echo htmlspecialchars($order['id']); ?></p>
            <p><strong>Usuari:</strong> <?php echo htmlspecialchars($order['user']); ?></p>
            <p><strong>Data:</strong> <?php echo htmlspecialchars($order['created_at']); ?></p>
            <p><strong>Estat:</strong> <?php echo htmlspecialchars($order['status']); ?></p>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Producte</th>
                    <th>Preu Unitari</th>
                    <th>Quantitat</th>
                    <th>Total Línia</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($order['items'] as $item): 
                    $prod = getProduct($item['product_id'], $products);
                    $name = $prod ? $prod['name'] : 'Producte desconegut (' . $item['product_id'] . ')';
                    $price = $prod ? floatval($prod['price']) : 0;
                    $qty = intval($item['quantity']);
                    $lineTotal = $price * $qty;
                    $totalOrder += $lineTotal;
                ?>
                <tr>
                    <td><?php echo htmlspecialchars($name); ?></td>
                    <td><?php echo number_format($price, 2); ?> €</td>
                    <td><?php echo $qty; ?></td>
                    <td><?php echo number_format($lineTotal, 2); ?> €</td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="total">
            Total Comanda: <?php echo number_format($totalOrder, 2); ?> €
        </div>

        <div class="actions">
            <a href="comandes.php" class="btn btn-back">Tornar</a>
            <?php if($order['status'] === 'pendent'): ?>
                <a href="process.php?id=<?php echo urlencode($order['id']); ?>" class="btn btn-process">Processar Comanda</a>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
