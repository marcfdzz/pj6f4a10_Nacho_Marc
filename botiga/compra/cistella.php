<?php
session_start();
require_once __DIR__ . '/../classes/GestorFitxers.php';
require_once __DIR__ . '/../classes/Cistella.php';
require_once __DIR__ . '/../classes/Producte.php';

// Validar sessió
if (empty($_SESSION['usuari']) || ($_SESSION['rol'] ?? '') !== 'client') {
    header('Location: inici_sessio.php');
    exit;
}

$usuariActual = $_SESSION['usuari'];
$fitxerProductes = __DIR__.'/../gestio/productes/productes.json';
$dadesProductes = GestorFitxers::llegirTot($fitxerProductes);

// Fitxer de cistella
$dirArea = __DIR__.'/area_clients/' . $usuariActual;
if (!is_dir($dirArea)) mkdir($dirArea, 0777, true);
$fitxerCistella = $dirArea . '/cistella';

// Carregar cistella
$dadesCistella = GestorFitxers::llegirTot($fitxerCistella);
$cistella = new Cistella($dadesCistella);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Afegir productes
    if (!empty($_POST['quantities']) && is_array($_POST['quantities'])) {
        foreach ($_POST['quantities'] as $pid => $q) {
            $qty = intval($q);
            if ($qty > 0) {
                // Mètode en català
                $cistella->afegir($pid, $qty);
            }
        }
        // Guardar utilitzant interfície obtenirDades
        GestorFitxers::guardarTot($fitxerCistella, $cistella->obtenirDades());
        echo "<script>alert('Productes afegits!'); window.location.href='cistella.php';</script>";
        exit;
    }
    
    // Buidar
    if (isset($_POST['clear'])) {
        $cistella->establirProductes([]);
        GestorFitxers::guardarTot($fitxerCistella, []);
        header('Location: cistella.php');
        exit;
    }
}

$itemsCistella = $cistella->obtenirProductes();
?>
<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <title>La meva cistella</title>
    <link rel="stylesheet" href="../css/compra.css">
</head>
<body>
    <div class="container">
        <div style="display:flex; justify-content:space-between; align-items:center;">
            <h1>Cistella de <?php echo htmlspecialchars($usuariActual); ?></h1>
            <a href="taulell.php" class="btn btn-secondary">← Tornar al Taulell</a>
        </div>
        
        <div class="nav-links">
            <a href="productes.php">Seguir comprant</a>
        </div>
        
        <?php if (!empty($itemsCistella)): ?>
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
                    $totalCistella = 0;
                    foreach ($itemsCistella as $pid => $qty): 
                        $producte = null;
                        foreach ($dadesProductes as $p) {
                            if (($p['id'] ?? '') == $pid) {
                                $producte = $p;
                                break;
                            }
                        }
                        if (!$producte) continue;
                        
                        $preu = floatval($producte['preu'] ?? 0);
                        $liniaTotal = $preu * $qty;
                        $totalCistella += $liniaTotal;
                    ?>
                    <tr>
                        <td><?php echo htmlspecialchars($producte['nom'] ?? 'Desconegut'); ?></td>
                        <td><?php echo number_format($preu, 2); ?> €</td>
                        <td><?php echo $qty; ?></td>
                        <td><?php echo number_format($liniaTotal, 2); ?> €</td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            
            <div class="text-right" style="font-size: 1.1em; margin-bottom: 20px;">
                <p>Base Imposable: <strong><?php echo number_format($totalCistella, 2); ?> €</strong></p>
                <p>IVA (21%): <strong><?php echo number_format($totalCistella * 0.21, 2); ?> €</strong></p>
                <p style="font-size: 1.3em; color: #28a745;">Total a Pagar: <strong><?php echo number_format($totalCistella * 1.21, 2); ?> €</strong></p>
            </div>
            
            <div style="display: flex; justify-content: space-between; margin-top: 20px;">
                <form method="post" onsubmit="return confirm('Segur que vols buidar la cistella?');">
                    <button type="submit" name="clear" class="btn btn-danger">Buidar Cistella</button>
                </form>
                <form action="crear_comanda.php" method="post">
                    <button type="submit" class="btn btn-success" style="font-size: 1.1em;">Confirmar i Crear Comanda</button>
                </form>
            </div>
        <?php else: ?>
            <div class="alert alert-danger">La cistella és buida.</div>
        <?php endif; ?>
        
        <hr style="margin: 40px 0;">
        <h2>Les meves comandes</h2>
        <?php
        $fitxers = scandir($dirArea);
        $comandes = [];
        foreach ($fitxers as $f) {
            if ($f === '.' || $f === '..' || $f === 'dades' || $f === 'cistella') continue;
            $comandes[] = $f;
        }
        rsort($comandes);
        
        if (empty($comandes)) {
            echo "<p>No hi ha comandes anteriors.</p>";
        } else {
            foreach ($comandes as $fitxerComanda) {
                $contingut = GestorFitxers::llegirTot($dirArea . '/' . $fitxerComanda);
                $total = $contingut['total'] ?? 0;
                $data = $contingut['data'] ?? 'Desconeguda';
                echo "<div style='border:1px solid #eee; padding:15px; margin-bottom:15px; background:#f9f9f9; border-radius: 5px;'>";
                echo "<strong>Comanda: " . htmlspecialchars($fitxerComanda) . "</strong><br>";
                echo "Data: " . htmlspecialchars($data) . "<br>";
                echo "Total: <strong>" . number_format($total, 2) . " €</strong>";
                echo "</div>";
            }
        }
        ?>
    </div>
</body>
</html>