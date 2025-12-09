<?php
session_start();
require_once __DIR__ . '/../classes/Mailer.php';

if (empty($_SESSION['usuari']) || ($_SESSION['rol'] ?? '') !== 'client') {
    header('Location: inici_sessio.php');
    exit;
}

$missatge = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tipus = $_POST['tipus'] ?? 'info';
    $comentaris = $_POST['comentaris'] ?? '';
    
    $assumpte = "Sol·licitud de canvi de dades - " . $_SESSION['usuari'];
    $cos = "Tipus: $tipus\n\nMissatge:\n$comentaris";
    
    // Simulating sending email
    // if (Mailer::send('nachocastellocastillo@gmail.com', $assumpte, $cos)) {
    // For now simple success message as Mailer might need config
    $missatge = "Sol·licitud enviada correctament (Simulació).";
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
        
        <?php if ($missatge): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($missatge); ?></div>
        <?php endif; ?>
        
        <form method="post">
            <div class="form-group">
                <label>Tipus de sol·licitud:</label>
                <select name="tipus">
                    <option value="canvi_contrasenya">Canvi de contrasenya</option>
                    <option value="canvi_adreça">Canvi d'adreça</option>
                    <option value="canvi_telefon">Canvi de telèfon</option>
                    <option value="altres">Altres</option>
                </select>
            </div>
            
            <div class="form-group">
                <label>Detalls:</label>
                <textarea name="comentaris" rows="5" required placeholder="Explica quines dades vols canviar..."></textarea>
            </div>
            
            <div class="mt-20">
                <button type="submit" class="btn btn-success">Enviar Sol·licitud</button>
                <a href="taulell.php" class="btn btn-secondary">Tornar</a>
            </div>
        </form>
    </div>
</body>
</html>
