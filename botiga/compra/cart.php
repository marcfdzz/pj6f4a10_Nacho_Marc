<?php
session_start();
require_once __DIR__ . '/../classes/FileManager.php';
require_once __DIR__ . '/../classes/Cistella.php';
require_once __DIR__ . '/../classes/Producte.php';

// Ensure user is logged in as a client
if (empty($_SESSION['user']) || ($_SESSION['role'] ?? '') !== 'client') {
    header('Location: login.php');
    exit;
}

$currentUser = $_SESSION['user'];
$productsFile = __DIR__.'/../gestio/productes/productes.json';
$productsData = FileManager::readJson($productsFile);

// Cart file: compra/area_clients/<username>/cistella
$areaDir = __DIR__.'/area_clients/' . $currentUser;
if (!is_dir($areaDir)) mkdir($areaDir, 0777, true);
$cistellaFile = $areaDir . '/cistella';

// Load cart
$cartData = FileManager::readJson($cistellaFile);
$cistella = new Cistella($cartData);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle bulk addition from productes.php
    if (!empty($_POST['quantities']) && is_array($_POST['quantities'])) {
        foreach ($_POST['quantities'] as $pid => $q) {
            $qty = intval($q);
            if ($qty > 0) {
                $cistella->add($pid, $qty);
            }
        }
        FileManager::saveJson($cistellaFile, $cistella->getProductes());
        echo "<script>alert('Productes afegits!'); window.location.href='cart.php';</script>";
        exit;
    }
    
    // Handle clear cart
    if (isset($_POST['clear'])) {
        $cistella->setProductes([]);
        FileManager::saveJson($cistellaFile, []);
        header('Location: cart.php');
        exit;
    }
}

$cartItems = $cistella->getProductes();
?>
<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <title>La meva cistella</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <div style="display:flex; justify-content:space-between; align-items:center;">
            <h1>Cistella de <?php echo htmlspecialchars($currentUser); ?></h1>
            <a href="dashboard.php" class="btn btn-secondary">← Tornar al Dashboard</a>
        </div>
        
        <div class="nav-links">
            <a href="productes.php">Seguir comprant</a>
        </div>
        
        <?php if (!empty($cartItems)): ?>
            <table>
                <thead>
                    <tr>
                        <th>Producte</th>
                        <th>Preu Unitari</th>
                        <th>Quantitat</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $totalCart = 0;
                    foreach ($cartItems as $pid => $qty): 
                        $product = null;
                        foreach ($productsData as $p) {
                            if (($p['id'] ?? '') == $pid) {
                                $product = $p;
                                break;
                            }
                        }
                        if (!$product) continue;
                        
                        $price = floatval($product['preu'] ?? 0);
                        $lineTotal = $price * $qty;
                        $totalCart += $lineTotal;
                    ?>
                    <tr>
                        <td><?php echo htmlspecialchars($product['nom'] ?? 'Desconegut'); ?></td>
                        <td><?php echo number_format($price, 2); ?> €</td>
                        <td><?php echo $qty; ?></td>
                        <td><?php echo number_format($lineTotal, 2); ?> €</td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            
            <div class="text-right" style="font-size: 1.1em; margin-bottom: 20px;">
                <p>Base Imposable: <strong><?php echo number_format($totalCart, 2); ?> €</strong></p>
                <p>IVA (21%): <strong><?php echo number_format($totalCart * 0.21, 2); ?> €</strong></p>
                <p style="font-size: 1.3em; color: #28a745;">Total a Pagar: <strong><?php echo number_format($totalCart * 1.21, 2); ?> €</strong></p>
            </div>
            
            <div style="display: flex; justify-content: space-between; margin-top: 20px;">
                <form method="post" onsubmit="return confirm('Segur que vols buidar la cistella?');">
                    <button type="submit" name="clear" class="btn btn-danger">Buidar Cistella</button>
                </form>
                <form action="order_create.php" method="post">
                    <button type="submit" class="btn btn-success" style="font-size: 1.1em;">Confirmar i Crear Comanda</button>
                </form>
            </div>
        <?php else: ?>
            <div class="alert alert-danger">La cistella és buida.</div>
        <?php endif; ?>
        
        <hr style="margin: 40px 0;">
        <h2>Les meves comandes</h2>
        <?php
        // List orders: compra/area_clients/<user>/<files>
        // Requirement: "comandes individuales (fecha + hash)"
        // We look for files that are NOT 'dades' or 'cistella'
        $files = scandir($areaDir);
        $orders = [];
        foreach ($files as $f) {
            if ($f === '.' || $f === '..' || $f === 'dades' || $f === 'cistella') continue;
            // Assume any other file is an order
            $orders[] = $f;
        }
        rsort($orders); // Show newest first
        
        if (empty($orders)) {
            echo "<p>No hi ha comandes anteriors.</p>";
        } else {
            foreach ($orders as $oFile) {
                $content = FileManager::readJson($areaDir . '/' . $oFile);
                $total = $content['total'] ?? 0;
                $date = $content['data'] ?? 'Desconeguda';
                echo "<div style='border:1px solid #eee; padding:15px; margin-bottom:15px; background:#f9f9f9; border-radius: 5px;'>";
                echo "<strong>Comanda: " . htmlspecialchars($oFile) . "</strong><br>";
                echo "Data: " . htmlspecialchars($date) . "<br>";
                echo "Total: <strong>" . number_format($total, 2) . " €</strong>";
                echo "</div>";
            }
        }
        ?>
    </div>
</body>
</html>