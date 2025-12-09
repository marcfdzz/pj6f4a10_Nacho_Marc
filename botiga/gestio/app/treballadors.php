<?php
session_start();
require_once __DIR__ . '/../../classes/GestorFitxers.php';
require_once __DIR__ . '/../../classes/Treballador.php';

// Check role: admin only
if (empty($_SESSION['usuari']) || ($_SESSION['rol'] ?? '') !== 'admin') {
    header('Location: inici_sessio.php');
    exit;
}

$fitxerTreballadors = __DIR__ . '/../treballadors/treballadors.json';
$dadesTreballadors = GestorFitxers::llegirTot($fitxerTreballadors);

// Handle delete
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['_method'] ?? '') === 'DELETE') {
    $usuariEsborrar = $_POST['usuari_esborrar'];
    // Prevent deleting self
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

// Handle add/edit
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save'])) {
    $usuari = $_POST['usuari'];
    $isEdit = isset($_POST['is_edit']) && $_POST['is_edit'] == '1';

    // Password handling
    $passHash = $_POST['hash_existent'] ?? '';
    if (!empty($_POST['contrasenya'])) {
        $passHash = password_hash($_POST['contrasenya'], PASSWORD_DEFAULT);
    }

    // Role
    $rol = $_POST['rol'];
    
    // Treballador constructor: $usuari, $contrasenya, $nom, $email, $rol
    $treballador = new Treballador($usuari, $passHash, $_POST['nom'], $_POST['email'], $rol);

    $arrayTreballador = $treballador->obtenirDades();

    // Update list
    $trobat = false;
    foreach ($dadesTreballadors as $key => $w) {
        if (($w['usuari'] ?? '') === $usuari) {
            if ($isEdit) {
                // Preserve ID if exists (not critical for logic but good practice)
                $arrayTreballador['id'] = $w['id'] ?? $arrayTreballador['id'] ?? null;
                $arrayTreballador['created_at'] = $w['created_at'] ?? date('c');
                
                $dadesTreballadors[$key] = $arrayTreballador;
                $trobat = true;
            } else {
                echo "<script>alert('L\'usuari ja existeix!'); window.location.href='treballadors.php';</script>";
                exit;
            }
            break;
        }
    }
    if (!$trobat && !$isEdit) {
        $arrayTreballador['created_at'] = date('c');
        $dadesTreballadors[] = $arrayTreballador;
    }

    GestorFitxers::guardarTot($fitxerTreballadors, $dadesTreballadors);
    header('Location: treballadors.php');
    exit;
}

// Get worker for editing
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
    <link rel="stylesheet" href="../css/style.css">
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
            <form method="post">
                <input type="hidden" name="is_edit" value="<?php echo $editTreballador ? '1' : '0'; ?>">
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