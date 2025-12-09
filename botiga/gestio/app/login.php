<?php
session_start();
require_once __DIR__ . '/../../classes/FileManager.php';
require_once __DIR__ . '/../../classes/Treballador.php';
require_once __DIR__ . '/../../classes/Admin.php';

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $usuarioInput = $_POST['nombreUsuario'] ?? '';
    $contrasenaInput = $_POST['contrasena'] ?? '';

    $datosTrabajadores = FileManager::readJson(__DIR__ . '/../treballadors/treballadors.json');
    $encontrado = null;

    foreach ($datosTrabajadores as $datosT) {
        $dbUsuario = $datosT['nombreUsuario'] ?? $datosT['username'] ?? '';
        $dbContrasena = $datosT['contrasena'] ?? $datosT['password'] ?? '';
        $dbRol = $datosT['rol'] ?? $datosT['role'] ?? '';

        if ($dbUsuario === $usuarioInput && password_verify($contrasenaInput, $dbContrasena)) {
             // Instanciar objeto
             if ($dbRol === 'admin') {
                 $encontrado = new Admin(
                    $dbUsuario, 
                    $dbContrasena, 
                    $datosT['correo'] ?? $datosT['email'] ?? '', 
                    $datosT['nombre'] ?? $datosT['name'] ?? ''
                 );
             } else {
                 $encontrado = new Treballador(
                    $dbUsuario, 
                    $dbContrasena, 
                    $datosT['correo'] ?? $datosT['email'] ?? '', 
                    $datosT['nombre'] ?? $datosT['name'] ?? ''
                 );
             }
             break;
        }
    }

    if ($encontrado) {
        $_SESSION['usuario'] = $encontrado->obtenerNombreUsuario();
        $_SESSION['rol'] = $encontrado->obtenerRol();
        header('Location: dashboard.php');
        exit;
    }

    $error = 'Credenciales incorrectas';
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
                <label>Usuario:</label>
                <input type="text" name="nombreUsuario" required>
            </div>
            <div class="form-group">
                <label>Contraseña:</label>
                <input type="password" name="contrasena" required>
            </div>
            <button class="btn btn-primary" style="width: 100%;">Entrar</button>
        </form>
        <?php if(!empty($error)) echo '<div class="alert alert-danger mt-20">'.htmlspecialchars($error).'</div>'; ?>
        <p style="text-align: center; margin-top: 20px;"><a href="../../index.php">← Volver al inicio</a></p>
    </div>
</body>
</html>