<?php
session_start();
require_once __DIR__ . '/../../classes/GestorFitxers.php';
require_once __DIR__ . '/../../classes/Client.php';

// Validar rol
if (empty($_SESSION['usuari']) || ($_SESSION['rol'] !== 'admin' && $_SESSION['rol'] !== 'treballador')) {
    header('Location: inici_sessio.php');
    exit;
}

$fitxerClients = __DIR__ . '/../clients/clients.json';
$dadesClients = GestorFitxers::llegirTot($fitxerClients);

// Funció per validar requisits mínims de contrasenya
function validarContrasenya($contrasenya) {
    if (strlen($contrasenya) < 8) {
        return "La contrasenya ha de tenir com a mínim 8 caràcters";
    }
    if (!preg_match('/[A-Z]/', $contrasenya)) {
        return "La contrasenya ha de contenir almenys una lletra majúscula";
    }
    if (!preg_match('/[a-z]/', $contrasenya)) {
        return "La contrasenya ha de contenir almenys una lletra minúscula";
    }
    if (!preg_match('/[0-9]/', $contrasenya)) {
        return "La contrasenya ha de contenir almenys un número";
    }
    return true;
}

// Gestionar esborrat
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['_method'] ?? '') === 'DELETE') {
    $deleteId = $_POST['delete_id'];
    
    // Trobar client per obtenir nom d'usuari per esborrar carpeta
    $usuariEsborrar = null;
    foreach ($dadesClients as $c) {
        if (($c['id'] ?? '') === $deleteId) {
            $usuariEsborrar = $c['usuari'] ?? $c['nombreUsuario'] ?? $c['username'] ?? null;
            break;
        }
    }

    $dadesClients = array_filter($dadesClients, function($c) use ($deleteId) {
        return ($c['id'] ?? '') !== $deleteId;
    });
    $dadesClients = array_values($dadesClients);
    GestorFitxers::guardarTot($fitxerClients, $dadesClients);

    // Esborrar carpeta individual
    if ($usuariEsborrar) {
        $dirUsuari = __DIR__ . '/../../compra/area_clients/' . $usuariEsborrar;
        if (file_exists($dirUsuari . '/dades')) {
            unlink($dirUsuari . '/dades');
        }
    }

    header('Location: clients.php');
    exit;
}

// Gestionar afegir (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save']) && ($_POST['_method'] ?? '') !== 'PUT') {
    $id = '';
    
    // Determinar ID nou
    $maxId = 0;
    foreach ($dadesClients as $c) {
        if (intval($c['id'] ?? 0) > $maxId) $maxId = intval($c['id']);
    }
    $id = str_pad($maxId + 1, 4, '0', STR_PAD_LEFT);

    // Gestionar contrasenya amb validació
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
        // Instanciar objecte Client ($usuari, $contrasenya, $nom, $email, $adreca, $telefon, $id)
        $client = new Client(
            $_POST['usuari'],
            $passHash,
            $_POST['nom'],
            $_POST['email'],
            $_POST['adreca'],
            $_POST['telefon'],
            $id
        );

        // Obtenir array i afegir camps extres
        $arrayClient = $client->obtenirDades();
        $arrayClient['card_encrypted'] = $_POST['card_encrypted'] ?? '';
        $arrayClient['card_exp'] = $_POST['card_exp'] ?? '';
        $arrayClient['created_at'] = date('c');

        // Afegir nou client
        $dadesClients[] = $arrayClient;

        // Guardar llista principal
        GestorFitxers::guardarTot($fitxerClients, $dadesClients);

        // Guardar dades individuals del client
        $dirUsuari = __DIR__ . '/../../compra/area_clients/' . $client->obtenirUsuari();
        if (!is_dir($dirUsuari)) {
            mkdir($dirUsuari, 0777, true);
        }
        GestorFitxers::guardarTot($dirUsuari . '/dades', $arrayClient);

        header('Location: clients.php');
        exit;
    }
}

// Gestionar editar (PUT)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save']) && ($_POST['_method'] ?? '') === 'PUT') {
    $id = $_POST['id'] ?? '';
    
    if (!$id) {
        header('Location: clients.php');
        exit;
    }

    // Gestionar contrasenya amb validació
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
        // Instanciar objecte Client
        $client = new Client(
            $_POST['usuari'],
            $passHash,
            $_POST['nom'],
            $_POST['email'],
            $_POST['adreca'],
            $_POST['telefon'],
            $id
        );

        // Obtenir array i afegir camps extres
        $arrayClient = $client->obtenirDades();
        $arrayClient['card_encrypted'] = $_POST['card_encrypted'] ?? '';
        $arrayClient['card_exp'] = $_POST['card_exp'] ?? '';

        // Actualitzar llista
        foreach ($dadesClients as $key => $c) {
            if (($c['id'] ?? '') === $id) {
                $arrayClient['created_at'] = $c['created_at'] ?? date('c');
                $dadesClients[$key] = $arrayClient;
                break;
            }
        }

        // Guardar llista principal
        GestorFitxers::guardarTot($fitxerClients, $dadesClients);

        // Guardar dades individuals del client
        $dirUsuari = __DIR__ . '/../../compra/area_clients/' . $client->obtenirUsuari();
        if (!is_dir($dirUsuari)) {
            mkdir($dirUsuari, 0777, true);
        }
        GestorFitxers::guardarTot($dirUsuari . '/dades', $arrayClient);

        header('Location: clients.php');
        exit;
    }
}

// Obtenir client per editar
$editClient = null;
if (isset($_GET['edit'])) {
    foreach ($dadesClients as $c) {
        if (($c['id'] ?? '') === $_GET['edit']) {
            $editClient = $c;
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
    <title>Gestió de Clients</title>
    <link rel="stylesheet" href="../../css/gestio.css">
</head>
<body>
    <div class="container">
        <div style="display:flex; justify-content:space-between; align-items:center;">
            <h1>Gestió de Clients</h1>
            <a href="taulell.php" class="btn btn-secondary">← Tornar al Taulell</a>
        </div>
        
        <?php if ($editClient || isset($_GET['add'])): ?>
        <div class="form-container">
            <h2><?php echo $editClient ? 'Editar Client' : 'Afegir Nou Client'; ?></h2>
            <?php if (isset($error)): ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            <form method="post">
                <?php if ($editClient): ?>
                    <input type="hidden" name="_method" value="PUT">
                <?php endif; ?>
                <input type="hidden" name="id" value="<?php echo htmlspecialchars($editClient['id'] ?? ''); ?>">
                <input type="hidden" name="hash_existent" value="<?php echo htmlspecialchars($editClient['contrasenya'] ?? $editClient['password'] ?? ''); ?>">
                <input type="hidden" name="created_at" value="<?php echo htmlspecialchars($editClient['created_at'] ?? ''); ?>">
                
                <div class="form-group">
                    <label>Nom d'usuari:</label>
                    <input type="text" name="usuari" value="<?php echo htmlspecialchars($editClient['usuari'] ?? $editClient['nombreUsuario'] ?? $editClient['username'] ?? ''); ?>" required>
                </div>
                
                <div class="form-group">
                    <label>Contrasenya <?php echo $editClient ? '(deixar buit per no canviar)' : ''; ?>:</label>
                    <input type="password" name="contrasenya" <?php echo $editClient ? '' : 'required'; ?>>
                </div>
                
                <div class="form-group">
                    <label>Nom complet:</label>
                    <input type="text" name="nom" value="<?php echo htmlspecialchars($editClient['nom'] ?? $editClient['nombre'] ?? $editClient['name'] ?? ''); ?>" required>
                </div>
                
                <div class="form-group">
                    <label>Email:</label>
                    <input type="email" name="email" value="<?php echo htmlspecialchars($editClient['email'] ?? $editClient['correo'] ?? ''); ?>" required>
                </div>
                
                <div class="form-group">
                    <label>Telèfon:</label>
                    <input type="text" name="telefon" value="<?php echo htmlspecialchars($editClient['telefon'] ?? $editClient['telefono'] ?? $editClient['phone'] ?? ''); ?>">
                </div>
                
                <div class="form-group">
                    <label>Adreça:</label>
                    <input type="text" name="adreca" value="<?php echo htmlspecialchars($editClient['adreca'] ?? $editClient['direccion'] ?? $editClient['address'] ?? ''); ?>">
                </div>
                
                <div class="form-group">
                    <label>Targeta (encriptada):</label>
                    <input type="text" name="card_encrypted" value="<?php echo htmlspecialchars($editClient['card_encrypted'] ?? ''); ?>">
                </div>
                
                <div class="form-group">
                    <label>Caducitat targeta:</label>
                    <input type="text" name="card_exp" value="<?php echo htmlspecialchars($editClient['card_exp'] ?? ''); ?>" placeholder="MM/YY">
                </div>
                
                <button type="submit" name="save" class="btn btn-success">Guardar</button>
                <a href="clients.php" class="btn btn-secondary">Cancel·lar</a>
            </form>
        </div>
        <?php else: ?>
        <a href="?add=1" class="btn btn-primary" style="margin-bottom: 20px;">+ Afegir Client</a>
        
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Usuari</th>
                    <th>Nom Complet</th>
                    <th>Email</th>
                    <th>Telèfon</th>
                    <th>Accions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($dadesClients as $c): ?>
                <tr>
                    <td><?php echo htmlspecialchars($c['id'] ?? ''); ?></td>
                    <td><?php echo htmlspecialchars($c['usuari'] ?? $c['nombreUsuario'] ?? $c['username'] ?? ''); ?></td>
                    <td><?php echo htmlspecialchars($c['nom'] ?? $c['nombre'] ?? $c['name'] ?? ''); ?></td>
                    <td><?php echo htmlspecialchars($c['email'] ?? $c['correo'] ?? ''); ?></td>
                    <td><?php echo htmlspecialchars($c['telefon'] ?? $c['telefono'] ?? $c['phone'] ?? ''); ?></td>
                    <td>
                        <a href="?edit=<?php echo urlencode($c['id'] ?? ''); ?>" class="btn btn-primary">Editar</a>
                        <form method="post" style="display:inline;" onsubmit="return confirm('Segur que vols eliminar aquest client?');">
                            <input type="hidden" name="_method" value="DELETE">
                            <input type="hidden" name="delete_id" value="<?php echo htmlspecialchars($c['id'] ?? ''); ?>">
                            <button type="submit" class="btn btn-danger">Eliminar</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>
</body>
</html>