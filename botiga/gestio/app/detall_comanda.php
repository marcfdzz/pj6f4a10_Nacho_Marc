<?php
session_start();
require_once __DIR__ . '/../../classes/GestorFitxers.php';

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

$fitxerProductes = __DIR__ . '/../productes/productes.json';
$dadesProductes = GestorFitxers::llegirTot($fitxerProductes);

function obtenirNomProducte($pid, $dadesProductes) {
    foreach ($dadesProductes as $p) {
        if (($p['id'] ?? '') == $pid) return $p['nom'] ?? 'Desconegut';
    }
    return 'Desconegut';
}

if (isset($_GET['generar_pdf'])) {
    require_once __DIR__ . '/../../../vendor/autoload.php';
    
    $dompdf = new \Dompdf\Dompdf();
    
    $idComanda = htmlspecialchars($comanda['id'] ?? '');
    $nomClient = htmlspecialchars($comanda['usuari'] ?? $comanda['client'] ?? '');
    $dataComanda = htmlspecialchars($comanda['data'] ?? '');
    $total = number_format($comanda['total'] ?? 0, 2);
    
    $html = '<!DOCTYPE html>
    <html lang="ca">
    <head>
        <meta charset="UTF-8">
        <style>
            body { font-family: Arial, sans-serif; padding: 20px; }
            h1 { color: #333; border-bottom: 2px solid #333; padding-bottom: 10px; }
            table { width: 100%; border-collapse: collapse; margin: 20px 0; }
            th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
            th { background-color: #f2f2f2; font-weight: bold; }
            .total { font-size: 18px; font-weight: bold; margin-top: 20px; }
        </style>
    </head>
    <body>
        <h1>Detall Comanda: ' . $idComanda . '</h1>
        <p><strong>Client:</strong> ' . $nomClient . '</p>
        <p><strong>Data:</strong> ' . $dataComanda . '</p>
        <table>
            <thead>
                <tr>
                    <th>Producte</th>
                    <th>Quantitat</th>
                </tr>
            </thead>
            <tbody>';
    
    foreach ($comanda['productes'] ?? [] as $pid => $qty) {
        $nomProd = htmlspecialchars(obtenirNomProducte($pid, $dadesProductes));
        $html .= '<tr><td>' . $nomProd . '</td><td>' . $qty . '</td></tr>';
    }
    
    $html .= '</tbody>
        </table>
        <p class="total">Total: ' . $total . ' €</p>
        <p style="margin-top: 30px; font-size: 12px; color: #666;">Generat el ' . date('d/m/Y H:i') . '</p>
    </body>
    </html>';
    
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();
    $dompdf->stream("comanda_" . $idComanda . ".pdf", array("Attachment" => true));
    exit;
}
?>
<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <title>Detall Comanda</title>
    <link rel="stylesheet" href="../../css/gestio.css">
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
            <a href="?file=<?php echo urlencode($fitxer); ?>&generar_pdf=1" class="btn btn-primary">Generar PDF</a>
        </div>
    </div>
</body>
</html>
