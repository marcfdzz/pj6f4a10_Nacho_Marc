<?php
session_start();
require_once __DIR__ . '/../../classes/GestorFitxers.php';

// Check role
if (empty($_SESSION['usuari']) || ($_SESSION['rol'] !== 'admin' && $_SESSION['rol'] !== 'treballador')) {
    header('Location: inici_sessio.php');
    exit;
}

$fitxer = $_GET['file'] ?? '';
$dirPendents = __DIR__ . '/../../comandes_copia';
$rutaFitxer = $dirPendents . '/' . $fitxer;

if (!$fitxer || !file_exists($rutaFitxer)) {
    echo "Comanda no trobada.";
    exit;
}

$comanda = GestorFitxers::llegirTot($rutaFitxer);

// Get products for names
$fitxerProductes = __DIR__ . '/../productes/productes.json';
$dadesProductes = GestorFitxers::llegirTot($fitxerProductes);

function obtenirNomProducte($pid, $dadesProductes) {
    foreach ($dadesProductes as $p) {
        if (($p['id'] ?? '') == $pid) return $p['nom'] ?? 'Desconegut';
    }
    return 'Desconegut';
}
?>
<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <title>Detall Comanda</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="container">
        <div style="display:flex; justify-content:space-between; align-items:center;">
            <h1>Detall Comanda: <?php echo htmlspecialchars($comanda['id'] ?? ''); ?></h1>
            <a href="comandes.php" class="btn btn-secondary no-print">← Tornar</a>
        </div>
        <p><strong>Client:</strong> <?php echo htmlspecialchars($comanda['usuari'] ?? $comanda['client'] ?? ''); ?></p>
        <p><strong>Data:</strong> <?php echo htmlspecialchars($comanda['data'] ?? ''); ?></p>
        
        <table>
            <thead>
                <tr>
                    <th>Producte</th>
                    <th>Quantitat</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($comanda['productes'] ?? [] as $pid => $qty): ?>
                <tr>
                    <td><?php echo htmlspecialchars(obtenirNomProducte($pid, $dadesProductes)); ?></td>
                    <td><?php echo htmlspecialchars($qty); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
        <p><strong>Total:</strong> <?php echo number_format($comanda['total'] ?? 0, 2); ?> €</p>
        
        <div class="mt-20 no-print">
            <form action="processar.php" method="post" style="display:inline;">
                <input type="hidden" name="file" value="<?php echo htmlspecialchars($fitxer); ?>">
                <button type="submit" name="process" class="btn btn-success">Processar i Moure</button>
                <button type="submit" name="email" class="btn btn-info">Enviar Correu</button>
            </form>
            <button onclick="window.print()" class="btn btn-primary">Generar PDF (Imprimir)</button>
        </div>
    </div>
</body>
</html>
