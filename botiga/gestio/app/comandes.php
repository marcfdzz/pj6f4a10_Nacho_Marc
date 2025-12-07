<?php
session_start();
require_once __DIR__ . '/../../classes/FileManager.php';

// Check role: admin or treballador
if (empty($_SESSION['user']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'treballador')) {
    header('Location: login.php');
    exit;
}

$pendingDir = __DIR__ . '/../../comandes_copia';
$files = scandir($pendingDir);
$orders = [];
foreach ($files as $f) {
    if ($f === '.' || $f === '..') continue;
    $orders[] = $f;
}
?>
<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <title>Comandes Pendents</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="container">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 20px;">
            <h1>Comandes Pendents</h1>
            <a href="dashboard.php" class="btn btn-secondary">← Tornar al Dashboard</a>
        </div>
        
        <?php if (empty($orders)): ?>
            <p>No hi ha comandes pendents.</p>
        <?php else: ?>
            <?php foreach ($orders as $oFile): 
                $data = FileManager::readJson($pendingDir . '/' . $oFile);
                $id = $data['id'] ?? $oFile;
                $user = $data['client'] ?? 'Desconegut'; // Comanda class uses 'client'
                $total = $data['total'] ?? 0;
            ?>
            <div class="order-item">
                <div>
                    <strong>ID:</strong> <?php echo htmlspecialchars($id); ?> <br>
                    <strong>Client:</strong> <?php echo htmlspecialchars($user); ?> <br>
                    <strong>Total:</strong> <?php echo number_format($total, 2); ?> €
                </div>
                <div>
                    <a href="detall_comanda.php?file=<?php echo urlencode($oFile); ?>" class="btn">Veure i Processar</a>
                </div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</body>
</html>