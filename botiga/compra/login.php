<?php
session_start();
require_once __DIR__ . '/../classes/DataRepository.php';

if($_SERVER['REQUEST_METHOD']==='POST'){
  $u = $_POST['username'] ?? '';
  $p = $_POST['password'] ?? '';

  // Try to find as a User (Client)
  $user = DataRepository::findUserByUsername($u);
  
  if ($user && $user->password === $p) {
      $_SESSION['user'] = $user->username;
      $_SESSION['role'] = 'client';
      header('Location: dashboard.php');
      exit;
  }

  // Optional: Check if it's a worker trying to login here (or separate login)
  // For now, let's assume this login is for clients. 
  // If you want workers to login here too:
  /*
  $worker = DataRepository::findWorkerByUsername($u);
  if ($worker && $worker->password === $p) {
      $_SESSION['user'] = $worker->username;
      $_SESSION['role'] = $worker->role;
      header('Location: ../gestio/app/dashboard.php'); // Redirect to admin dashboard
      exit;
  }
  */

  $error='Credencials incorrectes';
}
?>
<form method="post">
  Usuari: <input name="username"><br>
  Contrasenya: <input name="password" type="password"><br>
  <button>Entrar</button>
</form>
<?php if(!empty($error)) echo "<p style='color:red'>".htmlspecialchars($error)."</p>"; ?>