<?php
session_start();
require_once __DIR__ . '/../../classes/FileManager.php';
require_once __DIR__ . '/../../classes/Treballador.php';
require_once __DIR__ . '/../../classes/Admin.php';

// Check role: admin only
if (empty($_SESSION['user']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

$workersFile = __DIR__ . '/../treballadors/treballadors.json';
$workersData = FileManager::readJson($workersFile);

// Handle delete
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['_method'] ?? '') === 'DELETE') {
    $deleteUser = $_POST['delete_username'];
    // Prevent deleting self
    if ($deleteUser === $_SESSION['user']) {
        echo "<script>alert('No pots esborrar-te a tu mateix!'); window.location.href='treballadors.php';</script>";
        exit;
    }

    $workersData = array_filter($workersData, function($w) use ($deleteUser) {
        return $w['username'] !== $deleteUser;
    });
    $workersData = array_values($workersData);
    FileManager::saveJson($workersFile, $workersData);
    header('Location: treballadors.php');
    exit;
}

// Handle add/edit
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save'])) {
    $username = $_POST['username'];
    $isEdit = isset($_POST['is_edit']) && $_POST['is_edit'] == '1';

    // Password handling
    $passwordHash = $_POST['existing_hash'] ?? '';
    if (!empty($_POST['password'])) {
        $passwordHash = password_hash($_POST['password'], PASSWORD_DEFAULT);
    }

    // Role
    $role = $_POST['role'];
    $worker = null;
    if ($role === 'admin') {
        $worker = new Admin($username, $passwordHash, $_POST['email'], $_POST['name']);
    } else {
        $worker = new Treballador($username, $passwordHash, $_POST['email'], $_POST['name']);
    }

    $workerArray = $worker->toArray();
    // Ensure role is saved (User toArray includes role)

    // Update list
    $found = false;
    foreach ($workersData as $key => $w) {
        if ($w['username'] === $username) {
            if ($isEdit) {
                $workersData[$key] = $workerArray;
                $found = true;
            } else {
                echo "<script>alert('L\'usuari ja existeix!'); window.location.href='treballadors.php';</script>";
                exit;
            }
            break;
        }
    }
    if (!$found && !$isEdit) {
        $workersData[] = $workerArray;
    }

    FileManager::saveJson($workersFile, $workersData);
    header('Location: treballadors.php');
    exit;
}

// Get worker for editing
$editWorker = null;
if (isset($_GET['edit'])) {
    foreach ($workersData as $w) {
        if ($w['username'] === $_GET['edit']) {
            $editWorker = $w;
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
            <a href="dashboard.php" class="btn btn-secondary">← Tornar al Dashboard</a>
        </div>
        
        <?php if ($editWorker || isset($_GET['add'])): ?>
        <div class="form-container">
            <h2><?php echo $editWorker ? 'Editar Treballador' : 'Afegir Nou Treballador'; ?></h2>
            <form method="post">
                <input type="hidden" name="is_edit" value="<?php echo $editWorker ? '1' : '0'; ?>">
                <input type="hidden" name="existing_hash" value="<?php echo htmlspecialchars($editWorker['password'] ?? ''); ?>">
                
                <div class="form-group">
                    <label>Nom d'usuari:</label>
                    <input type="text" name="username" value="<?php echo htmlspecialchars($editWorker['username'] ?? ''); ?>" <?php echo $editWorker ? 'readonly' : 'required'; ?>>
                </div>
                
                <div class="form-group">
                    <label>Contrasenya <?php echo $editWorker ? '(deixar buit per no canviar)' : ''; ?>:</label>
                    <input type="password" name="password" <?php echo $editWorker ? '' : 'required'; ?>>
                </div>
                
                <div class="form-group">
                    <label>Nom complet:</label>
                    <input type="text" name="name" value="<?php echo htmlspecialchars($editWorker['name'] ?? ''); ?>" required>
                </div>
                
                <div class="form-group">
                    <label>Email:</label>
                    <input type="email" name="email" value="<?php echo htmlspecialchars($editWorker['email'] ?? ''); ?>" required>
                </div>

                <div class="form-group">
                    <label>Rol:</label>
                    <select name="role">
                        <option value="treballador" <?php echo ($editWorker && $editWorker['role'] === 'treballador') ? 'selected' : ''; ?>>Treballador</option>
                        <option value="admin" <?php echo ($editWorker && $editWorker['role'] === 'admin') ? 'selected' : ''; ?>>Admin</option>
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
                <?php foreach($workersData as $w): ?>
                <tr>
                    <td><?php echo htmlspecialchars($w['username'] ?? ''); ?></td>
                    <td><?php echo htmlspecialchars($w['name'] ?? ''); ?></td>
                    <td><?php echo htmlspecialchars($w['email'] ?? ''); ?></td>
                    <td><?php echo htmlspecialchars($w['role'] ?? ''); ?></td>
                    <td>
                        <a href="?edit=<?php echo urlencode($w['username'] ?? ''); ?>" class="btn btn-primary">Editar</a>
                        <?php if(($w['username'] ?? '') !== $_SESSION['user']): ?>
                        <form method="post" style="display:inline;" onsubmit="return confirm('Segur que vols eliminar aquest treballador?');">
                            <input type="hidden" name="_method" value="DELETE">
                            <input type="hidden" name="delete_username" value="<?php echo htmlspecialchars($w['username'] ?? ''); ?>">
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