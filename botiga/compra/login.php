<?php
session_start();
require_once __DIR__ . '/../classes/FileManager.php';
require_once __DIR__ . '/../classes/Client.php';

if($_SERVER['REQUEST_METHOD']==='POST'){
  $u = $_POST['username'] ?? '';
  $p = $_POST['password'] ?? '';

  $clientsData = FileManager::readJson(__DIR__ . '/../gestio/clients/clients.json');
  $found = null;

  foreach ($clientsData as $cData) {
      if ($cData['username'] === $u && password_verify($p, $cData['password'])) {
          $found = new Client(
              $cData['username'],
              $cData['password'],
              $cData['email'] ?? '',
              $cData['name'] ?? '',
              $cData['address'] ?? '',
              $cData['phone'] ?? '',
              $cData['id'] ?? null
          );
          break;
      }
  }

  if ($found) {
      $_SESSION['user'] = $found->getUsername();
      $_SESSION['role'] = 'client';
      header('Location: dashboard.php');
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
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container" style="max-width: 400px;">
        <h1 style="text-align: center;">Login Compra</h1>
        <form method="post">
            <div class="form-group">
                <label>Usuari:</label>
                <input type="text" name="username" required>
            </div>
            <div class="form-group">
                <label>Contrasenya:</label>
                <input name="password" type="password" required>
            </div>
            <button class="btn btn-primary" style="width: 100%;">Entrar</button>
        </form>
        <?php if(!empty($error)) echo "<div class='alert alert-danger mt-20'>".htmlspecialchars($error)."</div>"; ?>
        <p style="text-align: center; margin-top: 20px;"><a href="../index.php">← Tornar a l'inici</a></p>
    </div>
</body>
</html>