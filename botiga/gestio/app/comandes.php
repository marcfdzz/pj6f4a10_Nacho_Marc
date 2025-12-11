<?php
session_start();
require_once __DIR__ . '/../../classes/GestorFitxers.php';

// Validar rol
if (empty($_SESSION['usuari']) || ($_SESSION['rol'] !== 'admin' && $_SESSION['rol'] !== 'treballador')) {
    header('Location: inici_sessio.php');
    exit;
}

$dirPendents = __DIR__ . '/../../comandes_copia';
if (!is_dir($dirPendents)) {
    mkdir($dirPendents, 0777, true);
}
$fitxers = scandir($dirPendents);
$comandes = [];
if ($fitxers !== false) {
    foreach ($fitxers as $f) {
        if ($f === '.' || $f === '..') continue;
        $comandes[] = $f;
    }
}
?>
<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <title>Comandes Pendents</title>
    <link rel="stylesheet" href="../../css/gestio.css">
</head>
<body>
    <div class="container">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 20px;">
            <h1>Comandes Pendents</h1>
            <a href="taulell.php" class="btn btn-secondary">← Tornar al Taulell</a>
        </div>
        
        <?php if (empty($comandes)): ?>
            <p>No hi ha comandes pendents.</p>
        <?php else: ?>
            <?php foreach ($comandes as $fComanda): 
                $dades = GestorFitxers::llegirTot($dirPendents . '/' . $fComanda);
                $id = $dades['id'] ?? $fComanda;
                $usuari = $dades['usuari'] ?? $dades['client'] ?? 'Desconegut';
                $total = $dades['total'] ?? 0;
            ?>
            <div class="order-item">
                <div>
                    <strong>ID:</strong> <?php echo htmlspecialchars($id); ?> <br>
                    <strong>Client:</strong> <?php echo htmlspecialchars($usuari); ?> <br>
                    <strong>Total:</strong> <?php echo number_format($total, 2); ?> €
                </div>
                <div>
                    <a href="detall_comanda.php?file=<?php echo urlencode($fComanda); ?>" class="btn">Veure i Processar</a>
                </div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</body>
</html>