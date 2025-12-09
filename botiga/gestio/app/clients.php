<?php
session_start();
require_once __DIR__ . '/../../classes/FileManager.php';
require_once __DIR__ . '/../../classes/Client.php';

// Check role: admin or treballador
if (empty($_SESSION['usuario']) || ($_SESSION['rol'] !== 'admin' && $_SESSION['rol'] !== 'treballador')) {
    header('Location: login.php');
    exit;
}

$clientsFile = __DIR__ . '/../clients/clients.json';
$clientsData = FileManager::readJson($clientsFile);

// Handle delete
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['_method'] ?? '') === 'DELETE') {
    $deleteId = $_POST['delete_id'];
    
    // Find client to get username for folder deletion
    $usernameToDelete = null;
    foreach ($clientsData as $c) {
        if (($c['id'] ?? '') === $deleteId) {
            $usernameToDelete = $c['nombreUsuario'] ?? $c['username'] ?? null;
            break;
        }
    }

    $clientsData = array_filter($clientsData, function($c) use ($deleteId) {
        return ($c['id'] ?? '') !== $deleteId;
    });
    $clientsData = array_values($clientsData);
    FileManager::saveJson($clientsFile, $clientsData);

    // Optional: Remove individual folder if it exists
    if ($usernameToDelete) {
        $userDir = __DIR__ . '/../../compra/area_clients/' . $usernameToDelete;
        if (file_exists($userDir . '/dades')) {
            unlink($userDir . '/dades');
        }
    }

    header('Location: clients.php');
    exit;
}

// Handle add/edit
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save'])) {
    $id = $_POST['id'] ?? '';
    
    // Determine ID
    if (!$id) {
        $maxId = 0;
        foreach ($clientsData as $c) {
            if (intval($c['id'] ?? 0) > $maxId) $maxId = intval($c['id']);
        }
        $id = str_pad($maxId + 1, 4, '0', STR_PAD_LEFT);
    }

    // Password handling
    $passwordHash = $_POST['existing_hash'] ?? '';
    if (!empty($_POST['contrasena'])) {
        $passwordHash = password_hash($_POST['contrasena'], PASSWORD_DEFAULT);
    }

    // Instantiate Client object
    $client = new Client(
        $_POST['nombreUsuario'],
        $passwordHash,
        $_POST['correo'],
        $_POST['nombre'],
        $_POST['direccion'],
        $_POST['telefono'],
        $id
    );

    // Additional fields not in constructor but in array
    $clientArray = $client->toArray();
    $clientArray['card_encrypted'] = $_POST['card_encrypted'] ?? '';
    $clientArray['card_exp'] = $_POST['card_exp'] ?? '';
    $clientArray['created_at'] = $_POST['created_at'] ?? date('c');

    // Update list
    $found = false;
    foreach ($clientsData as $key => $c) {
        if (($c['id'] ?? '') === $id) {
            $clientsData[$key] = $clientArray;
            $found = true;
            break;
        }
    }
    if (!$found) {
        $clientsData[] = $clientArray;
    }

    // Save main list
    FileManager::saveJson($clientsFile, $clientsData);

    // Save individual client data
    $userDir = __DIR__ . '/../../compra/area_clients/' . $client->obtenerNombreUsuario();
    if (!is_dir($userDir)) {
        mkdir($userDir, 0777, true);
    }
    FileManager::saveJson($userDir . '/dades', $clientArray);

    header('Location: clients.php');
    exit;
}

// Get client for editing
$editClient = null;
if (isset($_GET['edit'])) {
    foreach ($clientsData as $c) {
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
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="container">
        <div style="display:flex; justify-content:space-between; align-items:center;">
            <h1>Gestió de Clients</h1>
            <a href="dashboard.php" class="btn btn-secondary">← Tornar al Dashboard</a>
        </div>
        
        <?php if ($editClient || isset($_GET['add'])): ?>
        <div class="form-container">
            <h2><?php echo $editClient ? 'Editar Client' : 'Afegir Nou Client'; ?></h2>
            <form method="post">
                <input type="hidden" name="id" value="<?php echo htmlspecialchars($editClient['id'] ?? ''); ?>">
                <input type="hidden" name="existing_hash" value="<?php echo htmlspecialchars($editClient['contrasena'] ?? $editClient['password'] ?? ''); ?>">
                <input type="hidden" name="created_at" value="<?php echo htmlspecialchars($editClient['created_at'] ?? ''); ?>">
                
                <div class="form-group">
                    <label>Nom d'usuari:</label>
                    <input type="text" name="nombreUsuario" value="<?php echo htmlspecialchars($editClient['nombreUsuario'] ?? $editClient['username'] ?? ''); ?>" required>
                </div>
                
                <div class="form-group">
                    <label>Contrasenya <?php echo $editClient ? '(deixar buit per no canviar)' : ''; ?>:</label>
                    <input type="password" name="contrasena" <?php echo $editClient ? '' : 'required'; ?>>
                </div>
                
                <div class="form-group">
                    <label>Nom complet:</label>
                    <input type="text" name="nombre" value="<?php echo htmlspecialchars($editClient['nombre'] ?? $editClient['name'] ?? ''); ?>" required>
                </div>
                
                <div class="form-group">
                    <label>Email:</label>
                    <input type="email" name="correo" value="<?php echo htmlspecialchars($editClient['correo'] ?? $editClient['email'] ?? ''); ?>" required>
                </div>
                
                <div class="form-group">
                    <label>Telèfon:</label>
                    <input type="text" name="telefono" value="<?php echo htmlspecialchars($editClient['telefono'] ?? $editClient['phone'] ?? ''); ?>">
                </div>
                
                <div class="form-group">
                    <label>Adreça:</label>
                    <input type="text" name="direccion" value="<?php echo htmlspecialchars($editClient['direccion'] ?? $editClient['address'] ?? ''); ?>">
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
                <?php foreach($clientsData as $c): ?>
                <tr>
                    <td><?php echo htmlspecialchars($c['id'] ?? ''); ?></td>
                    <td><?php echo htmlspecialchars($c['nombreUsuario'] ?? $c['username'] ?? ''); ?></td>
                    <td><?php echo htmlspecialchars($c['nombre'] ?? $c['name'] ?? ''); ?></td>
                    <td><?php echo htmlspecialchars($c['correo'] ?? $c['email'] ?? ''); ?></td>
                    <td><?php echo htmlspecialchars($c['telefono'] ?? $c['phone'] ?? ''); ?></td>
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