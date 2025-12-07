<?php
session_start();
require_once __DIR__ . '/../classes/Mailer.php';

if (empty($_SESSION['user']) || ($_SESSION['role'] ?? '') !== 'client') {
    header('Location: login.php');
    exit;
}

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $type = $_POST['type'] ?? 'info';
    $comments = $_POST['comments'] ?? '';
    
    $subject = "Sol·licitud de canvi de dades - " . $_SESSION['user'];
    $body = "Tipus: $type\n\nMissatge:\n$comments";
    
    // Send to admin/worker email (mock)
    if (Mailer::send('admin@botiga.local', $subject, $body)) {
        $message = "Sol·licitud enviada correctament.";
    } else {
        $message = "Error en enviar la sol·licitud.";
    }
}
?>
<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <title>Sol·licitud Canvi Dades</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <h1>Sol·licitar Canvi de Dades</h1>
        
        <?php if ($message): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>
        
        <form method="post">
            <div class="form-group">
                <label>Tipus de sol·licitud:</label>
                <select name="type">
                    <option value="change_password">Canvi de contrasenya</option>
                    <option value="change_address">Canvi d'adreça</option>
                    <option value="change_phone">Canvi de telèfon</option>
                    <option value="other">Altres</option>
                </select>
            </div>
            
            <div class="form-group">
                <label>Detalls:</label>
                <textarea name="comments" rows="5" required placeholder="Explica quines dades vols canviar..."></textarea>
            </div>
            
            <div class="mt-20">
                <button type="submit" class="btn btn-success">Enviar Sol·licitud</button>
                <a href="dashboard.php" class="btn btn-secondary">Tornar</a>
            </div>
        </form>
    </div>
</body>
</html>
