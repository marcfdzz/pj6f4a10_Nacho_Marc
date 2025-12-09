<?php
session_start();
require_once __DIR__ . '/../classes/FileManager.php';
require_once __DIR__ . '/../classes/Client.php';

if($_SERVER['REQUEST_METHOD']==='POST'){
  $usuarioInput = $_POST['nombreUsuario'] ?? '';
  $contrasenaInput = $_POST['contrasena'] ?? '';

  $datosClientes = FileManager::readJson(__DIR__ . '/../gestio/clients/clients.json');
  $encontrado = null;

  foreach ($datosClientes as $datosCliente) {
      $dbUsuario = $datosCliente['nombreUsuario'] ?? $datosCliente['username'] ?? '';
      $dbContrasena = $datosCliente['contrasena'] ?? $datosCliente['password'] ?? '';
      
      if ($dbUsuario === $usuarioInput && password_verify($contrasenaInput, $dbContrasena)) {
          $encontrado = new Client(
              $dbUsuario,
              $dbContrasena,
              $datosCliente['correo'] ?? $datosCliente['email'] ?? '',
              $datosCliente['nombre'] ?? $datosCliente['name'] ?? '',
              $datosCliente['direccion'] ?? $datosCliente['address'] ?? '',
              $datosCliente['telefono'] ?? $datosCliente['phone'] ?? '',
              $datosCliente['id'] ?? null
          );
          break;
      }
  }

  if ($encontrado) {
      $_SESSION['usuario'] = $encontrado->obtenerNombreUsuario();
      $_SESSION['rol'] = 'client';
      header('Location: dashboard.php');
      exit;
  }

  $error='Credenciales incorrectas';
}
?>
<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <title>Login Compra</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <h1>Iniciar Sesión</h1>
        </div>
        <form method="post" class="login-form">
            <div class="form-group">
                <label for="nombreUsuario">Usuario</label>
                <input type="text" id="nombreUsuario" name="nombreUsuario" required>
            </div>
            <div class="form-group">
                <label for="contrasena">Contraseña</label>
                <input type="password" id="contrasena" name="contrasena" required>
            </div>
            <button class="btn btn-primary">Entrar</button>
        </form>
        <?php if(!empty($error)) echo "<div class='alert alert-danger'>".htmlspecialchars($error)."</div>"; ?>
        <div class="login-footer">
            <p><a href="../index.php">← Volver al inicio</a></p>
        </div>
    </div>
</body>
</html>