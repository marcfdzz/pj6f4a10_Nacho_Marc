<?php
session_start();
require_once __DIR__ . '/../classes/GestorFitxers.php';
require_once __DIR__ . '/../classes/Client.php';

if($_SERVER['REQUEST_METHOD']==='POST'){
  $usuariInput = $_POST['usuari'] ?? '';
  $contrasenyaInput = $_POST['contrasenya'] ?? '';

  $fitxerClients = __DIR__ . '/../gestio/clients/clients.json';
  $dadesClients = GestorFitxers::llegirTot($fitxerClients);
  $trobat = null;

  foreach ($dadesClients as $d) {
      // Suportar claus antigues i noves per si de cas, però preferir les noves
      $dbUsuari = $d['usuari'] ?? $d['username'] ?? $d['nombreUsuario'] ?? '';
      $dbPass = $d['contrasenya'] ?? $d['password'] ?? '';
      
      if ($dbUsuari === $usuariInput && password_verify($contrasenyaInput, $dbPass)) {
          // Mapejar al constructor Client: $usuari, $contrasenya, $nom, $email, $adreca, $telefon, $id
          $trobat = new Client(
              $dbUsuari,
              $dbPass, 
              $d['nom'] ?? $d['name'] ?? $d['nombre'] ?? '',
              $d['email'] ?? $d['correo'] ?? '',
              $d['adreca'] ?? $d['address'] ?? $d['direccion'] ?? '',
              $d['telefon'] ?? $d['phone'] ?? $d['telefono'] ?? '',
              $d['id'] ?? null
          );
          break;
      }
  }

  if ($trobat) {
      $_SESSION['usuari'] = $trobat->obtenirUsuari();
      $_SESSION['rol'] = 'client';
      header('Location: taulell.php');
      exit;
  }

  $error='Credencials incorrectes';
}
?>
<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <title>Login Compra</title>
    <link rel="stylesheet" href="../css/compra.css">
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <h1>Iniciar Sesión</h1>
        </div>
        <form method="post" class="login-form">
            <div class="form-group">
                <label for="usuari">Usuari</label>
                <input type="text" id="usuari" name="usuari" required>
            </div>
            <div class="form-group">
                <label for="contrasenya">Contrasenya</label>
                <input type="password" id="contrasenya" name="contrasenya" required>
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