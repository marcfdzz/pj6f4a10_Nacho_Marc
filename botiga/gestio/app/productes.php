<?php
session_start();
if(empty($_SESSION['worker'])){ header('Location: login.php'); exit;}

$productsFile = __DIR__.'/../../data/products.json';
$products = json_decode(file_get_contents($productsFile), true);

// Handle delete
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    $products = array_filter($products, function($p) {
        return $p['product_id'] !== $_POST['delete_id'];
    });
    $products = array_values($products);
    file_put_contents($productsFile, json_encode($products, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE));
    header('Location: productes.php');
    exit;
}

// Handle add/edit
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save'])) {
    $pid = $_POST['product_id'] ?? '';
    $newProduct = [
        'product_id' => $pid ?: str_pad(count($products) + 1, 4, '0', STR_PAD_LEFT),
        'name' => $_POST['name'],
        'description' => $_POST['description'],
        'price' => $_POST['price'],
        'stock' => intval($_POST['stock']),
        'created_at' => $_POST['created_at'] ?? date('c'),
        'updated_at' => date('c')
    ];
    
    if ($pid) {
        // Edit existing
        foreach ($products as $key => $p) {
            if ($p['product_id'] === $pid) {
                $products[$key] = $newProduct;
                break;
            }
        }
    } else {
        // Add new
        $products[] = $newProduct;
    }
    
    file_put_contents($productsFile, json_encode($products, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE));
    header('Location: productes.php');
    exit;
}

// Get product for editing
$editProduct = null;
if (isset($_GET['edit'])) {
    foreach ($products as $p) {
        if ($p['product_id'] === $_GET['edit']) {
            $editProduct = $p;
            break;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestió de Productes</title>
    <style>
        body { font-family: sans-serif; padding: 20px; background: #f5f5f5; }
        .container { max-width: 1200px; margin: 0 auto; }
        h1 { color: #333; }
        .btn { padding: 8px 16px; border: none; border-radius: 4px; cursor: pointer; text-decoration: none; display: inline-block; margin: 2px; }
        .btn-primary { background: #007bff; color: white; }
        .btn-danger { background: #dc3545; color: white; }
        .btn-success { background: #28a745; color: white; }
        .btn-secondary { background: #6c757d; color: white; }
        table { width: 100%; background: white; border-collapse: collapse; margin: 20px 0; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #f8f9fa; font-weight: bold; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input[type="text"], input[type="number"], textarea { width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; }
        textarea { min-height: 80px; resize: vertical; }
        .form-container { background: white; padding: 20px; border-radius: 8px; margin-bottom: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Gestió de Productes</h1>
        <a href="dashboard.php" class="btn btn-secondary">← Tornar al Dashboard</a>
        
        <?php if ($editProduct || isset($_GET['add'])): ?>
        <div class="form-container">
            <h2><?php echo $editProduct ? 'Editar Producte' : 'Afegir Nou Producte'; ?></h2>
            <form method="post">
                <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($editProduct['product_id'] ?? ''); ?>">
                <input type="hidden" name="created_at" value="<?php echo htmlspecialchars($editProduct['created_at'] ?? ''); ?>">
                
                <div class="form-group">
                    <label>Nom del producte:</label>
                    <input type="text" name="name" value="<?php echo htmlspecialchars($editProduct['name'] ?? ''); ?>" required>
                </div>
                
                <div class="form-group">
                    <label>Descripció:</label>
                    <textarea name="description" required><?php echo htmlspecialchars($editProduct['description'] ?? ''); ?></textarea>
                </div>
                
                <div class="form-group">
                    <label>Preu (€):</label>
                    <input type="text" name="price" value="<?php echo htmlspecialchars($editProduct['price'] ?? ''); ?>" required placeholder="9.99">
                </div>
                
                <div class="form-group">
                    <label>Stock:</label>
                    <input type="number" name="stock" value="<?php echo htmlspecialchars($editProduct['stock'] ?? '0'); ?>" required min="0">
                </div>
                
                <button type="submit" name="save" class="btn btn-success">Guardar</button>
                <a href="productes.php" class="btn btn-secondary">Cancel·lar</a>
            </form>
        </div>
        <?php else: ?>
        <a href="?add=1" class="btn btn-primary" style="margin: 20px 0;">+ Afegir Producte</a>
        
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nom</th>
                    <th>Descripció</th>
                    <th>Preu</th>
                    <th>Stock</th>
                    <th>Accions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($products as $p): ?>
                <tr>
                    <td><?php echo htmlspecialchars($p['product_id']); ?></td>
                    <td><?php echo htmlspecialchars($p['name']); ?></td>
                    <td><?php echo htmlspecialchars($p['description']); ?></td>
                    <td><?php echo htmlspecialchars($p['price']); ?> €</td>
                    <td><?php echo htmlspecialchars($p['stock']); ?></td>
                    <td>
                        <a href="?edit=<?php echo urlencode($p['product_id']); ?>" class="btn btn-primary">Editar</a>
                        <form method="post" style="display:inline;" onsubmit="return confirm('Segur que vols eliminar aquest producte?');">
                            <input type="hidden" name="delete_id" value="<?php echo htmlspecialchars($p['product_id']); ?>">
                            <button type="submit" class="btn btn-danger">Eliminar</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>
</body>
</html>