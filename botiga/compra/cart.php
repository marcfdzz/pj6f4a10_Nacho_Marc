<?php
session_start();

// Ensure user is logged in as a client
if (empty($_SESSION['user']) || ($_SESSION['role'] ?? '') !== 'client') {
    header('Location: login.php');
    exit;
}

$currentUser = $_SESSION['user'];
$dataFile = __DIR__.'/../data/products.json';
$products = json_decode(file_get_contents($dataFile), true);

// Cart is stored in: compra/area_clients/<username>/cistella.json
$areaDir = __DIR__.'/area_clients/' . $currentUser;
$cistellaFile = $areaDir . '/cistella.json';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $itemsToAdd = [];
    
    // Handle bulk addition
    if (!empty($_POST['quantities']) && is_array($_POST['quantities'])) {
        foreach ($_POST['quantities'] as $pid => $q) {
            $qty = intval($q);
            if ($qty > 0) {
                $itemsToAdd[] = ['product_id' => $pid, 'quantity' => $qty];
            }
        }
    } 
    // Handle legacy single addition (just in case)
    elseif (!empty($_POST['product_id'])) {
        $pid = $_POST['product_id'];
        $qty = max(1, intval($_POST['qty'] ?? 1));
        $itemsToAdd[] = ['product_id' => $pid, 'quantity' => $qty];
    }

    if (!empty($itemsToAdd)) {
        if (!is_dir($areaDir)) {
            mkdir($areaDir, 0777, true);
        }
        
        $cart = file_exists($cistellaFile) ? json_decode(file_get_contents($cistellaFile), true) : ['items' => [], 'user' => $currentUser];
        
        // Ensure the cart belongs to the current user
        if (isset($cart['user']) && $cart['user'] !== $currentUser) {
            $cart = ['items' => [], 'user' => $currentUser];
        }
        
        foreach ($itemsToAdd as $newItem) {
            // Optional: Merge quantities if product already in cart? 
            // For simplicity, just appending as per original logic, or we could merge.
            // Let's append to match previous behavior, but merging is usually better.
            // Given the prompt didn't ask for merging, I'll stick to appending or simple logic.
            // Actually, let's just append to keep it simple and consistent with previous code.
            $cart['items'][] = $newItem;
        }
        
        $cart['user'] = $currentUser;
        
        file_put_contents($cistellaFile, json_encode($cart, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        echo "<p>Productes afegits a la cistella.</p>";
    }
}

echo "<h1>Cistella de " . htmlspecialchars($currentUser) . "</h1>";

if (file_exists($cistellaFile)) {
    $cart = json_decode(file_get_contents($cistellaFile), true);
    
    // Double check ownership
    if (($cart['user'] ?? '') === $currentUser) {
        if (!empty($cart['items'])) {
            echo "<div style='background:#fff; padding:15px; border:1px solid #ddd; border-radius:5px;'>";
            $totalCart = 0;
            foreach ($cart['items'] as $it) {
                $pName = $it['product_id'];
                $pPrice = 0;
                foreach ($products as $prod) {
                    if ($prod['product_id'] == $it['product_id']) {
                        $pName = $prod['name'];
                        $pPrice = floatval($prod['price']);
                        break;
                    }
                }
                $lineTotal = $pPrice * $it['quantity'];
                $totalCart += $lineTotal;
                
                echo "<div style='padding:8px 0; border-bottom:1px solid #eee;'>";
                echo "<span>" . htmlspecialchars($pName) . " x" . htmlspecialchars($it['quantity']) . "</span>";
                echo " <span style='color:#666;'>" . number_format($pPrice, 2) . " €</span>";
                echo " = <span style='font-weight:bold;'>" . number_format($lineTotal, 2) . " €</span>";
                echo "</div>";
            }
            echo "<div style='margin-top:10px; text-align:right; font-weight:bold; font-size:1.2em;'>Total: " . number_format($totalCart, 2) . " €</div>";
            echo "</div>";
            echo "<form method='post' action='order_create.php'><button>Confirmar i crear comanda</button></form>";
        } else {
            echo "<p>La cistella és buida.</p>";
        }
    } else {
        echo "<p>Error: La cistella trobada no pertany a l'usuari actual.</p>";
    }
} else {
    echo "<p>No tens cap cistella activa.</p>";
}

echo "<hr><h2>Les meves comandes</h2>";
$ordersDir = $areaDir . '/orders';
if (is_dir($ordersDir)) {
    $files = glob($ordersDir . '/*.order');
    if ($files) {
        rsort($files);
        foreach ($files as $f) {
            $od = json_decode(file_get_contents($f), true);
            $oid = $od['id'] ?? basename($f, '.order');
            $odate = $od['created_at'] ?? 'Data desconeguda';
            echo "<div style='border:1px solid #ccc; padding:10px; margin-bottom:10px; background:#f9f9f9;'>";
            echo "<strong>Comanda: " . htmlspecialchars($oid) . "</strong><br>";
            echo "<small>Data: " . htmlspecialchars($odate) . "</small><br>";
            
            $orderTotal = 0;
            if (!empty($od['items'])) {
                echo "<div style='margin-top:10px;'>";
                foreach ($od['items'] as $item) {
                     $pName = $item['product_id'];
                     $pPrice = 0;
                     foreach ($products as $prod) {
                        if ($prod['product_id'] == $item['product_id']) {
                            $pName = $prod['name'];
                            $pPrice = floatval($prod['price']);
                            break;
                        }
                     }
                     $lineTotal = $pPrice * $item['quantity'];
                     $orderTotal += $lineTotal;
                     
                     echo "<div style='padding:5px 0;'>";
                     echo "<span>" . htmlspecialchars($pName) . " x" . htmlspecialchars($item['quantity']) . "</span>";
                     echo " <span style='color:#666;'>" . number_format($pPrice, 2) . " €</span>";
                     echo " = <span style='font-weight:bold;'>" . number_format($lineTotal, 2) . " €</span>";
                     echo "</div>";
                }
                echo "<div style='margin-top:8px; padding-top:8px; border-top:1px solid #ddd; text-align:right; font-weight:bold;'>";
                echo "Total: " . number_format($orderTotal, 2) . " €";
                echo "</div>";
                echo "</div>";
            }
            echo "</div>";
        }
    } else {
        echo "<p>No hi ha comandes realitzades.</p>";
    }
}

echo "<p><a href='productes.php'>Seguir comprant</a> | <a href='dashboard.php'>Tornar al dashboard</a></p>";
?>