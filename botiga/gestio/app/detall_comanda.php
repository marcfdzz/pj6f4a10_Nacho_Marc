<?php
session_start();
require_once __DIR__ . '/../../classes/FileManager.php';

// Check role: admin or treballador
if (empty($_SESSION['user']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'treballador')) {
    header('Location: login.php');
    exit;
}

$file = $_GET['file'] ?? '';
$pendingDir = __DIR__ . '/../../comandes_copia';
$filePath = $pendingDir . '/' . $file;

if (!$file || !file_exists($filePath)) {
    echo "Comanda no trobada.";
    exit;
}

$order = FileManager::readJson($filePath);

// Get products for names
$productsFile = __DIR__ . '/../productes/productes.json';
$productsData = FileManager::readJson($productsFile);

function getProductName($pid, $productsData) {
    foreach ($productsData as $p) {
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
            <h1>Detall Comanda: <?php echo htmlspecialchars($order['id'] ?? ''); ?></h1>
            <a href="comandes.php" class="btn btn-secondary no-print">← Tornar</a>
        </div>
        <p><strong>Client:</strong> <?php echo htmlspecialchars($order['client'] ?? ''); ?></p>
        <p><strong>Data:</strong> <?php echo htmlspecialchars($order['data'] ?? ''); ?></p>
        
        <table>
            <thead>
                <tr>
                    <th>Producte</th>
                    <th>Quantitat</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($order['productes'] ?? [] as $pid => $qty): ?>
                <tr>
                    <td><?php echo htmlspecialchars(getProductName($pid, $productsData)); ?></td>
                    <td><?php echo htmlspecialchars($qty); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
        <p><strong>Total:</strong> <?php echo number_format($order['total'] ?? 0, 2); ?> €</p>
        
        <div class="mt-20 no-print">
            <form action="process.php" method="post" style="display:inline;">
                <input type="hidden" name="file" value="<?php echo htmlspecialchars($file); ?>">
                <button type="submit" name="process" class="btn btn-success">Processar i Moure</button>
                <button type="submit" name="email" class="btn btn-info">Enviar Correu</button>
            </form>
            <button onclick="window.print()" class="btn btn-primary">Generar PDF (Imprimir)</button>
        </div>
    </div>
</body>
</html>
