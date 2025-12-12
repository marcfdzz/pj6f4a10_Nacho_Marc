<?php
session_start();
require_once __DIR__ . '/../../classes/GestorFitxers.php';
require_once __DIR__ . '/../../classes/Treballador.php';

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $usuariInput = $_POST['usuari'] ?? '';
    $contrasenyaInput = $_POST['contrasenya'] ?? '';

    $fitxerTreballadors = __DIR__ . '/../treballadors/treballadors.json';
    $dadesTreballadors = GestorFitxers::llegirTot($fitxerTreballadors);
    $trobat = null;

    foreach ($dadesTreballadors as $d) {
        $usuariFitxer = $d['usuari'] ?? $d['username'] ?? $d['nombreUsuario'] ?? '';
        $passFitxer = $d['contrasenya'] ?? $d['password'] ?? '';
        $rolFitxer = $d['rol'] ?? $d['role'] ?? 'treballador';

        if ($usuariFitxer === $usuariInput && password_verify($contrasenyaInput, $passFitxer)) {
             $trobat = new Treballador(
                $usuariFitxer, 
                $passFitxer, 
                $d['nom'] ?? $d['name'] ?? $d['nombre'] ?? '',
                $d['email'] ?? $d['correo'] ?? '',
                $rolFitxer
             );
             break;
        }
    }

    if ($trobat) {
        $_SESSION['usuari'] = $trobat->obtenirUsuari();
        $_SESSION['rol'] = $trobat->obtenirRol();
        header('Location: taulell.php');
        exit;
    }

    $error = 'Credencials incorrectes';
}
?>
<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="../../css/gestio.css">
</head>
<body>
    <div class="container" style="max-width: 400px;">
        <h1 style="text-align: center;">Login Gestió</h1>
        <form method="post">
            <div class="form-group">
                <label>Usuari:</label>
                <input type="text" name="usuari" required>
            </div>
            <div class="form-group">
                <label>Contrasenya:</label>
                <input type="password" name="contrasenya" required>
            </div>
            <button class="btn btn-primary" style="width: 100%;">Entrar</button>
        </form>
        <?php if(!empty($error)) echo '<div class="alert alert-danger mt-20">'.htmlspecialchars($error).'</div>'; ?>
        <p style="text-align: center; margin-top: 20px;"><a href="../../index.php">← Volver al inicio</a></p>
    </div>
</body>
</html>