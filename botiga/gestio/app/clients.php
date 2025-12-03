<?php
session_start();
if(empty($_SESSION['worker'])){ header('Location: login.php'); exit;}

$clientsFile = __DIR__.'/../../data/clients.json';
$clients = json_decode(file_get_contents($clientsFile), true);

// Handle delete
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    $clients = array_filter($clients, function($c) {
        return $c['id'] !== $_POST['delete_id'];
    });
    $clients = array_values($clients);
    file_put_contents($clientsFile, json_encode($clients, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE));
    header('Location: clients.php');
    exit;
}

// Handle add/edit
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save'])) {
    $id = $_POST['id'] ?? '';
    $newClient = [
        'id' => $id ?: str_pad(count($clients) + 1, 4, '0', STR_PAD_LEFT),
        'username' => $_POST['username'],
        'password_hash' => !empty($_POST['password']) ? password_hash($_POST['password'], PASSWORD_DEFAULT) : ($_POST['existing_hash'] ?? ''),
        'fullname' => $_POST['fullname'],
        'email' => $_POST['email'],
        'phone' => $_POST['phone'],
        'address' => $_POST['address'],
        'card_encrypted' => $_POST['card_encrypted'] ?? '',
        'card_exp' => $_POST['card_exp'] ?? '',
        'created_at' => $_POST['created_at'] ?? date('c')
    ];
    
    if ($id) {
        // Edit existing
        foreach ($clients as $key => $c) {
            if ($c['id'] === $id) {
                $clients[$key] = $newClient;
                break;
            }
        }
    } else {
        // Add new
        $clients[] = $newClient;
    }
    
    file_put_contents($clientsFile, json_encode($clients, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE));
    header('Location: clients.php');
    exit;
}

// Get client for editing
$editClient = null;
if (isset($_GET['edit'])) {
    foreach ($clients as $c) {
        if ($c['id'] === $_GET['edit']) {
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
    <style>
        body { font-family: sans-serif; padding: 20px; background: #f5f5f5; }
        .container { max-width: 1200px; margin: 0 auto; }
        h1 { color: #333; }
        .btn { padding: 8px 16px; border: none; border-radius: 4px; cursor: pointer; text-decoration: none; display: inline-block; margin: 2px; }
        .btn-primary { background: #007bff; color: white; }
        .btn-danger { background: #dc3545; color: white; }
        .btn-success { background: #28a745; color: white; }
        .btn-secondary { background: #6c757d; color: white; }
        table { width: 100%; background: white; border-collapse: collapse; margin: 20px 0; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #f8f9fa; font-weight: bold; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input[type="text"], input[type="email"], input[type="password"] { width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; }
        .form-container { background: white; padding: 20px; border-radius: 8px; margin-bottom: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Gestió de Clients</h1>
        <a href="dashboard.php" class="btn btn-secondary">← Tornar al Dashboard</a>
        
        <?php if ($editClient || isset($_GET['add'])): ?>
        <div class="form-container">
            <h2><?php echo $editClient ? 'Editar Client' : 'Afegir Nou Client'; ?></h2>
            <form method="post">
                <input type="hidden" name="id" value="<?php echo htmlspecialchars($editClient['id'] ?? ''); ?>">
                <input type="hidden" name="existing_hash" value="<?php echo htmlspecialchars($editClient['password_hash'] ?? ''); ?>">
                <input type="hidden" name="created_at" value="<?php echo htmlspecialchars($editClient['created_at'] ?? ''); ?>">
                
                <div class="form-group">
                    <label>Nom d'usuari:</label>
                    <input type="text" name="username" value="<?php echo htmlspecialchars($editClient['username'] ?? ''); ?>" required>
                </div>
                
                <div class="form-group">
                    <label>Contrasenya <?php echo $editClient ? '(deixar buit per no canviar)' : ''; ?>:</label>
                    <input type="password" name="password" <?php echo $editClient ? '' : 'required'; ?>>
                </div>
                
                <div class="form-group">
                    <label>Nom complet:</label>
                    <input type="text" name="fullname" value="<?php echo htmlspecialchars($editClient['fullname'] ?? ''); ?>" required>
                </div>
                
                <div class="form-group">
                    <label>Email:</label>
                    <input type="email" name="email" value="<?php echo htmlspecialchars($editClient['email'] ?? ''); ?>" required>
                </div>
                
                <div class="form-group">
                    <label>Telèfon:</label>
                    <input type="text" name="phone" value="<?php echo htmlspecialchars($editClient['phone'] ?? ''); ?>">
                </div>
                
                <div class="form-group">
                    <label>Adreça:</label>
                    <input type="text" name="address" value="<?php echo htmlspecialchars($editClient['address'] ?? ''); ?>">
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
        <a href="?add=1" class="btn btn-primary" style="margin: 20px 0;">+ Afegir Client</a>
        
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
                <?php foreach($clients as $c): ?>
                <tr>
                    <td><?php echo htmlspecialchars($c['id']); ?></td>
                    <td><?php echo htmlspecialchars($c['username']); ?></td>
                    <td><?php echo htmlspecialchars($c['fullname']); ?></td>
                    <td><?php echo htmlspecialchars($c['email']); ?></td>
                    <td><?php echo htmlspecialchars($c['phone']); ?></td>
                    <td>
                        <a href="?edit=<?php echo urlencode($c['id']); ?>" class="btn btn-primary">Editar</a>
                        <form method="post" style="display:inline;" onsubmit="return confirm('Segur que vols eliminar aquest client?');">
                            <input type="hidden" name="delete_id" value="<?php echo htmlspecialchars($c['id']); ?>">
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