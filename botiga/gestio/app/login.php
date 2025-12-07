<?php
session_start();
require_once __DIR__ . '/../../classes/FileManager.php';
require_once __DIR__ . '/../../classes/Treballador.php';
require_once __DIR__ . '/../../classes/Admin.php';

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $u = $_POST['username'] ?? '';
    $p = $_POST['password'] ?? '';

    $workersData = FileManager::readJson(__DIR__ . '/../treballadors/treballadors.json');
    $found = null;

    foreach ($workersData as $wData) {
        if ($wData['username'] === $u && password_verify($p, $wData['password'])) {
             // Instantiate object
             if ($wData['role'] === 'admin') {
                 $found = new Admin($wData['username'], $wData['password'], $wData['email'] ?? '', $wData['name'] ?? '');
             } else {
                 $found = new Treballador($wData['username'], $wData['password'], $wData['email'] ?? '', $wData['name'] ?? '');
             }
             break;
        }
    }

    if ($found) {
        $_SESSION['user'] = $found->getUsername();
        $_SESSION['role'] = $found->getRole();
        header('Location: dashboard.php');
        exit;
    }

    $error = 'Credencials incorrectes';
}
?>
<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="container" style="max-width: 400px;">
        <h1 style="text-align: center;">Login Gestió</h1>
        <form method="post">
            <div class="form-group">
                <label>Usuari:</label>
                <input type="text" name="username" required>
            </div>
            <div class="form-group">
                <label>Contrasenya:</label>
                <input type="password" name="password" required>
            </div>
            <button class="btn btn-primary" style="width: 100%;">Entrar</button>
        </form>
        <?php if(!empty($error)) echo '<div class="alert alert-danger mt-20">'.htmlspecialchars($error).'</div>'; ?>
        <p style="text-align: center; margin-top: 20px;"><a href="../../index.php">← Tornar a l'inici</a></p>
    </div>
</body>
</html>