<?php
session_start();
require_once __DIR__ . '/../../classes/GestorFitxers.php';
require_once __DIR__ . '/../../classes/Treballador.php';

function validarContrasenya($contrasenya) {
    if (strlen($contrasenya) < 8) {
        return "La contrasenya ha de tenir com a mínim 8 caràcters";
    }
    if (!preg_match('/[A-Z]/', $contrasenya)) {
        return "La contrasenya ha de contenir almenys una majúscula";
    }
    if (!preg_match('/[a-z]/', $contrasenya)) {
        return "La contrasenya ha de contenir almenys una minúscula";
    }
    if (!preg_match('/[0-9]/', $contrasenya)) {
        return "La contrasenya ha de contenir almenys un número";
    }
    return true;
}

if (empty($_SESSION['usuari']) || ($_SESSION['rol'] ?? '') !== 'admin') {
    header('Location: inici_sessio.php');
    exit;
}

$fitxerTreballadors = __DIR__ . '/../treballadors/treballadors.json';
$dadesTreballadors = GestorFitxers::llegirTot($fitxerTreballadors);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['_method'] ?? '') === 'DELETE') {
    $usuariEsborrar = $_POST['usuari_esborrar'];
    if ($usuariEsborrar === $_SESSION['usuari']) {
        echo "<script>alert('No pots esborrar-te a tu mateix!'); window.location.href='treballadors.php';</script>";
        exit;
    }

    $dadesTreballadors = array_filter($dadesTreballadors, function($w) use ($usuariEsborrar) {
        return ($w['usuari'] ?? '') !== $usuariEsborrar;
    });
    $dadesTreballadors = array_values($dadesTreballadors);
    GestorFitxers::guardarTot($fitxerTreballadors, $dadesTreballadors);
    header('Location: treballadors.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save']) && ($_POST['_method'] ?? '') !== 'PUT') {
    $usuari = $_POST['usuari'];

    $passHash = $_POST['hash_existent'] ?? '';
    if (!empty($_POST['contrasenya'])) {
        $validacio = validarContrasenya($_POST['contrasenya']);
        if ($validacio !== true) {
            $error = $validacio;
        } else {
            $passHash = password_hash($_POST['contrasenya'], PASSWORD_DEFAULT);
        }
    }

    if (!isset($error)) {
        $rol = $_POST['rol'];
        
        $treballador = new Treballador($usuari, $passHash, $_POST['nom'], $_POST['email'], $rol);

        $arrayTreballador = $treballador->obtenirDades();

        foreach ($dadesTreballadors as $w) {
            if (($w['usuari'] ?? '') === $usuari) {
                echo "<script>alert('L\'usuari ja existeix!'); window.location.href='treballadors.php';</script>";
                exit;
            }
        }

        $arrayTreballador['created_at'] = date('c');
        $dadesTreballadors[] = $arrayTreballador;

        GestorFitxers::guardarTot($fitxerTreballadors, $dadesTreballadors);
        header('Location: treballadors.php');
        exit;
    }
}

// Gestionar editar (PUT)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save']) && ($_POST['_method'] ?? '') === 'PUT') {
    $usuari = $_POST['usuari'];
    
    if (!$usuari) {
        header('Location: treballadors.php');
        exit;
    }

    $passHash = $_POST['hash_existent'] ?? '';
    if (!empty($_POST['contrasenya'])) {
        $validacio = validarContrasenya($_POST['contrasenya']);
        if ($validacio !== true) {
            $error = $validacio;
        } else {
            $passHash = password_hash($_POST['contrasenya'], PASSWORD_DEFAULT);
        }
    }

    if (!isset($error)) {
        $rol = $_POST['rol'];
        
        $treballador = new Treballador($usuari, $passHash, $_POST['nom'], $_POST['email'], $rol);

        $arrayTreballador = $treballador->obtenirDades();

        foreach ($dadesTreballadors as $key => $w) {
            if (($w['usuari'] ?? '') === $usuari) {
                $arrayTreballador['id'] = $w['id'] ?? $arrayTreballador['id'] ?? null;
                $arrayTreballador['created_at'] = $w['created_at'] ?? date('c');
                $dadesTreballadors[$key] = $arrayTreballador;
                break;
            }
        }

        GestorFitxers::guardarTot($fitxerTreballadors, $dadesTreballadors);
        header('Location: treballadors.php');
        exit;
    }
}

$editTreballador = null;
if (isset($_GET['edit'])) {
    foreach ($dadesTreballadors as $w) {
        if (($w['usuari'] ?? '') === $_GET['edit']) {
            $editTreballador = $w;
            break;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestió de Treballadors</title>
    <link rel="stylesheet" href="../../css/gestio.css">
</head>
<body>
    <div class="container">
        <div style="display:flex; justify-content:space-between; align-items:center;">
            <h1>Gestió de Treballadors</h1>
            <a href="taulell.php" class="btn btn-secondary">← Tornar al Taulell</a>
        </div>
        
        <?php if ($editTreballador || isset($_GET['add'])): ?>
        <div class="form-container">
            <h2><?php echo $editTreballador ? 'Editar Treballador' : 'Afegir Nou Treballador'; ?></h2>
            <?php if (isset($error)): ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            <form method="post">
                <?php if ($editTreballador): ?>
                    <input type="hidden" name="_method" value="PUT">
                <?php endif; ?>
                <input type="hidden" name="hash_existent" value="<?php echo htmlspecialchars($editTreballador['contrasenya'] ?? $editTreballador['password'] ?? ''); ?>">
                
                <div class="form-group">
                    <label>Nom d'usuari:</label>
                    <input type="text" name="usuari" value="<?php echo htmlspecialchars($editTreballador['usuari'] ?? $editTreballador['username'] ?? ''); ?>" <?php echo $editTreballador ? 'readonly' : 'required'; ?>>
                </div>
                
                <div class="form-group">
                    <label>Contrasenya <?php echo $editTreballador ? '(deixar buit per no canviar)' : ''; ?>:</label>
                    <input type="password" name="contrasenya" <?php echo $editTreballador ? '' : 'required'; ?>>
                </div>
                
                <div class="form-group">
                    <label>Nom complet:</label>
                    <input type="text" name="nom" value="<?php echo htmlspecialchars($editTreballador['nom'] ?? $editTreballador['name'] ?? ''); ?>" required>
                </div>
                
                <div class="form-group">
                    <label>Email:</label>
                    <input type="email" name="email" value="<?php echo htmlspecialchars($editTreballador['email'] ?? $editTreballador['email'] ?? ''); ?>" required>
                </div>

                <div class="form-group">
                    <label>Rol:</label>
                    <select name="rol">
                        <option value="treballador" <?php echo ($editTreballador && ($editTreballador['rol'] ?? '') === 'treballador') ? 'selected' : ''; ?>>Treballador</option>
                        <option value="admin" <?php echo ($editTreballador && ($editTreballador['rol'] ?? '') === 'admin') ? 'selected' : ''; ?>>Admin</option>
                    </select>
                </div>
                
                <button type="submit" name="save" class="btn btn-success">Guardar</button>
                <a href="treballadors.php" class="btn btn-secondary">Cancel·lar</a>
            </form>
        </div>
        <?php else: ?>
        <a href="?add=1" class="btn btn-primary" style="margin-bottom: 20px;">+ Afegir Treballador</a>
        
        <table>
            <thead>
                <tr>
                    <th>Usuari</th>
                    <th>Nom Complet</th>
                    <th>Email</th>
                    <th>Rol</th>
                    <th>Accions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($dadesTreballadors as $w): ?>
                <tr>
                    <td><?php echo htmlspecialchars($w['usuari'] ?? $w['username'] ?? ''); ?></td>
                    <td><?php echo htmlspecialchars($w['nom'] ?? $w['name'] ?? ''); ?></td>
                    <td><?php echo htmlspecialchars($w['email'] ?? ''); ?></td>
                    <td><?php echo htmlspecialchars($w['rol'] ?? $w['role'] ?? ''); ?></td>
                    <td>
                        <a href="?edit=<?php echo urlencode($w['usuari'] ?? $w['username'] ?? ''); ?>" class="btn btn-primary">Editar</a>
                        <?php if(($w['usuari'] ?? $w['username'] ?? '') !== $_SESSION['usuari']): ?>
                        <form method="post" style="display:inline;" onsubmit="return confirm('Segur que vols eliminar aquest treballador?');">
                            <input type="hidden" name="_method" value="DELETE">
                            <input type="hidden" name="usuari_esborrar" value="<?php echo htmlspecialchars($w['usuari'] ?? $w['username'] ?? ''); ?>">
                            <button type="submit" class="btn btn-danger">Eliminar</button>
                        </form>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>
</body>
</html>