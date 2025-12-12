<?php
session_start();
require_once __DIR__ . '/../../classes/GestorFitxers.php';
require_once __DIR__ . '/../../classes/Producte.php';

if (empty($_SESSION['usuari']) || ($_SESSION['rol'] !== 'admin' && $_SESSION['rol'] !== 'treballador')) {
    header('Location: inici_sessio.php');
    exit;
}

$fitxerProductes = __DIR__ . '/../productes/productes.json';
$dadesProductes = GestorFitxers::llegirTot($fitxerProductes);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['_method'] ?? '') === 'DELETE') {
    $deleteId = $_POST['delete_id'];
    $dadesProductes = array_filter($dadesProductes, function($p) use ($deleteId) {
        return ($p['id'] ?? '') !== $deleteId;
    });
    $dadesProductes = array_values($dadesProductes);
    GestorFitxers::guardarTot($fitxerProductes, $dadesProductes);

    header('Location: productes.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save']) && ($_POST['_method'] ?? '') !== 'PUT') {
    $id = '';
    
    $maxId = 0;
    foreach ($dadesProductes as $p) {
        if (intval($p['id'] ?? 0) > $maxId) $maxId = intval($p['id']);
    }
    $id = str_pad($maxId + 1, 4, '0', STR_PAD_LEFT);

    $producte = new Producte(
        $id,
        $_POST['nom'],
        $_POST['descripcio'],
        $_POST['preu'],
        $_POST['imatge'] ?? ''
    );

    $arrayProducte = $producte->obtenirDades();
    $arrayProducte['created_at'] = date('c');
    $dadesProductes[] = $arrayProducte;

    GestorFitxers::guardarTot($fitxerProductes, $dadesProductes);
    header('Location: productes.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save']) && ($_POST['_method'] ?? '') === 'PUT') {
    $id = $_POST['id'] ?? '';
    
    if (!$id) {
        header('Location: productes.php');
        exit;
    }

    $producte = new Producte(
        $id,
        $_POST['nom'],
        $_POST['descripcio'],
        $_POST['preu'],
        $_POST['imatge'] ?? ''
    );

    $arrayProducte = $producte->obtenirDades();
    $arrayProducte['updated_at'] = date('c');

    foreach ($dadesProductes as $key => $p) {
        if (($p['id'] ?? '') === $id) {
            $arrayProducte['created_at'] = $p['created_at'] ?? date('c');
            $dadesProductes[$key] = $arrayProducte;
            break;
        }
    }

    GestorFitxers::guardarTot($fitxerProductes, $dadesProductes);
    header('Location: productes.php');
    exit;
}

$editProduct = null;
if (isset($_GET['edit'])) {
    foreach ($dadesProductes as $p) {
        if (($p['id'] ?? '') === $_GET['edit']) {
            $editProduct = $p;
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
    <title>Gestió de Productes</title>
    <link rel="stylesheet" href="../../css/gestio.css">
</head>
<body>
    <div class="container">
        <div style="display:flex; justify-content:space-between; align-items:center;">
            <h1>Gestió de Productes</h1>
            <a href="taulell.php" class="btn btn-secondary">← Tornar al Taulell</a>
        </div>
        
        <?php if ($editProduct || isset($_GET['add'])): ?>
        <div class="form-container">
            <h2><?php echo $editProduct ? 'Editar Producte' : 'Afegir Nou Producte'; ?></h2>
            <form method="post">
                <?php if ($editProduct): ?>
                    <input type="hidden" name="_method" value="PUT">
                <?php endif; ?>
                <input type="hidden" name="id" value="<?php echo htmlspecialchars($editProduct['id'] ?? ''); ?>">
                
                <div class="form-group">
                    <label>Nom del producte:</label>
                    <input type="text" name="nom" value="<?php echo htmlspecialchars($editProduct['nom'] ?? ''); ?>" required>
                </div>
                
                <div class="form-group">
                    <label>Descripció:</label>
                    <textarea name="descripcio" required><?php echo htmlspecialchars($editProduct['descripcio'] ?? ''); ?></textarea>
                </div>
                
                <div class="form-group">
                    <label>Preu (€):</label>
                    <input type="text" name="preu" value="<?php echo htmlspecialchars($editProduct['preu'] ?? ''); ?>" required placeholder="9.99">
                </div>
                


                <div class="form-group">
                    <label>URL Imatge (opcional):</label>
                    <input type="text" name="imatge" value="<?php echo htmlspecialchars($editProduct['imatge'] ?? ''); ?>">
                </div>
                
                <button type="submit" name="save" class="btn btn-success">Guardar</button>
                <a href="productes.php" class="btn btn-secondary">Cancel·lar</a>
            </form>
        </div>
        <?php else: ?>
        <a href="?add=1" class="btn btn-primary" style="margin-bottom: 20px;">+ Afegir Producte</a>
        
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nom</th>
                    <th>Descripció</th>
                    <th>Preu</th>

                    <th>Accions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($dadesProductes as $p): ?>
                <tr>
                    <td><?php echo htmlspecialchars($p['id'] ?? ''); ?></td>
                    <td><?php echo htmlspecialchars($p['nom'] ?? ''); ?></td>
                    <td><?php echo htmlspecialchars($p['descripcio'] ?? ''); ?></td>
                    <td><?php echo htmlspecialchars($p['preu'] ?? ''); ?> €</td>

                    <td>
                        <a href="?edit=<?php echo urlencode($p['id'] ?? ''); ?>" class="btn btn-primary">Editar</a>
                        <form method="post" style="display:inline;" onsubmit="return confirm('Segur que vols eliminar aquest producte?');">
                            <input type="hidden" name="_method" value="DELETE">
                            <input type="hidden" name="delete_id" value="<?php echo htmlspecialchars($p['id'] ?? ''); ?>">
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