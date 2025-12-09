<?php
session_start();
require_once __DIR__ . '/../classes/GestorFitxers.php';
require_once __DIR__ . '/../classes/Cistella.php';
require_once __DIR__ . '/../classes/Comanda.php';
require_once __DIR__ . '/../classes/Producte.php';

if (empty($_SESSION['usuari']) || ($_SESSION['rol'] ?? '') !== 'client') {
    header('Location: inici_sessio.php');
    exit;
}

$usuariActual = $_SESSION['usuari'];
$dirArea = __DIR__.'/area_clients/' . $usuariActual;
$fitxerCistella = $dirArea . '/cistella';

if (!file_exists($fitxerCistella)) {
    echo "No hi ha cistella.";
    exit;
}

$dadesCistella = GestorFitxers::llegirTot($fitxerCistella);
$cistella = new Cistella($dadesCistella);
$items = $cistella->obtenirProductes();

if (empty($items)) {
    echo "Cistella buida.";
    exit;
}

// Calcular total
$rutaProductes = __DIR__.'/../productes_copia/productes.json';
$dadesProductes = GestorFitxers::llegirTot($rutaProductes);

$total = 0;
foreach ($items as $pid => $qty) {
    foreach ($dadesProductes as $p) {
        if (($p['id'] ?? '') == $pid) {
            $total += floatval($p['preu'] ?? 0) * $qty;
            break;
        }
    }
}
$totalAmbIva = $total * 1.21;

// Crear Comanda
$idComanda = date('Ymd_His') . '_' . substr(md5(uniqid()), 0, 8);
$dataActual = date('c');

$comanda = new Comanda(
    $idComanda,
    $dataActual,
    $usuariActual,
    $items,
    $totalAmbIva
);

$dadesComanda = $comanda->obtenirDades();

// Guardar en area client
GestorFitxers::guardarTot($dirArea . '/' . $idComanda, $dadesComanda);

// Guardar en comandes_copia
GestorFitxers::guardarTot(__DIR__ . '/../comandes_copia/' . $idComanda, $dadesComanda);

// Buidar cistella
$cistella->establirProductes([]);
GestorFitxers::guardarTot($fitxerCistella, []);

?>
<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <title>Comanda Creada</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="../css/main.css">
</head>
<body>
    <div class="container" style="text-align: center;">
        <h1>Comanda realitzada amb èxit</h1>
        <div class="alert alert-success" style="font-size: 1.2em;">Comanda creada amb id: <?php echo htmlspecialchars($idComanda); ?></div>
        <p style="font-size: 1.2em;">Total: <strong><?php echo number_format($totalAmbIva, 2); ?> €</strong></p>
        <div class="mt-20">
            <a href="taulell.php" class="btn btn-primary">Tornar al Menú</a>
        </div>
    </div>
</body>
</html>