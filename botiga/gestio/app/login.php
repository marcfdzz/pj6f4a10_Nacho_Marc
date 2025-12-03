<?php
session_start();
require_once __DIR__ . '/../../classes/DataRepository.php';

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $u = $_POST['username'] ?? '';
    $p = $_POST['password'] ?? '';

    $worker = DataRepository::findWorkerByUsername($u);

    // Verify username AND password
    if ($worker && $worker->password === $p) {
        $_SESSION['worker'] = $worker->username;
        $_SESSION['role'] = $worker->role;
        header('Location: dashboard.php');
        exit;
    }

    $error = 'Credencials incorrectes';
}
?>
<form method="post">
Usuari: <input name="username"><br>
Contrasenya: <input name="password" type="password"><br>
<button>Entrar</button>
</form>
<?php if(!empty($error)) echo '<p style="color:red">'.htmlspecialchars($error).'</p>'; ?>