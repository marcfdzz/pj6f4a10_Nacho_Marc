<?php
session_start();
require_once __DIR__ . '/../classes/Mailer.php';

if (empty($_SESSION['usuari']) || ($_SESSION['rol'] ?? '') !== 'client') {
    header('Location: inici_sessio.php');
    exit;
}

$missatge = '';
$error = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $camps = [
        'Nom i Cognoms' => $_POST['nom'] ?? '',
        'Adreça física' => $_POST['adreca'] ?? '',
        'Correu electrònic' => $_POST['email'] ?? '',
        'Telèfon' => $_POST['telefon'] ?? '',
        'Número de targeta' => $_POST['targeta'] ?? '',
        'CCV' => $_POST['ccv'] ?? ''
    ];

    $canvis = "";
    foreach ($camps as $camp => $valor) {
        if (!empty($valor)) {
            $canvis .= "<strong>$camp:</strong> " . htmlspecialchars($valor) . "<br>";
        }
    }

    if (empty($canvis)) {
        $missatge = "No has indicat cap dada per canviar.";
        $error = true;
    } else {
        $adminEmail = Mailer::getAdminEmail();
        $assumpte = "Sol·licitud de canvi de dades - Client: " . $_SESSION['usuari'];
        $cos = "<h2>Petició de canvi de dades</h2>";
        $cos .= "<p>L'usuari <strong>{$_SESSION['usuari']}</strong> ha sol·licitat canviar les següents dades:</p>";
        $cos .= $canvis;
        $cos .= "<br><p>Si us plau, revisa la sol·licitud.</p>";

        if (Mailer::send($adminEmail, $assumpte, $cos)) {
            $missatge = "La teva sol·licitud s'ha enviat correctament.";
        } else {
           // Error page logic as requested
           // We will display a special error view below based on this flag
           $fatal_error = true;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <title>Sol·licitud Canvi Dades</title>
    <link rel="stylesheet" href="../css/compra.css">
</head>
<body>
    <div class="container">
        <?php if (isset($fatal_error) && $fatal_error): ?>
            <div style="text-align: center; color: #dc3545;">
                <h1>Error en l'enviament</h1>
                <p>No s'ha pogut enviar el correu de sol·licitud. Si us plau, intenta-ho més tard.</p>
                <a href="taulell.php" class="btn btn-primary">Tornar al Dashboard</a>
            </div>
        <?php else: ?>
        
        <h1>Sol·licitar Canvi de Dades</h1>
        
        <?php if ($missatge): ?>
            <div class="alert <?php echo $error ? 'alert-danger' : 'alert-success'; ?>">
                <?php echo htmlspecialchars($missatge); ?>
            </div>
        <?php endif; ?>
        
        <p>Indica només les dades que vols modificar:</p>
        
        <form method="post">
            <div class="form-group">
                <label>Nom i Cognoms:</label>
                <input type="text" name="nom" placeholder="Nou nom i cognoms">
            </div>
            
            <div class="form-group">
                <label>Adreça física:</label>
                <input type="text" name="adreca" placeholder="Nova adreça">
            </div>

            <div class="form-group">
                <label>Correu electrònic:</label>
                <input type="email" name="email" placeholder="Nou correu">
            </div>

            <div class="form-group">
                <label>Telèfon:</label>
                <input type="text" name="telefon" placeholder="Nou telèfon">
            </div>

            <div class="form-group">
                <label>Número de targeta (16 dígits):</label>
                <input type="text" name="targeta" pattern="\d{16}" placeholder="xxxxxxxxxxxxxxxx" title="16 digits">
            </div>

            <div class="form-group">
                <label>CCV (3 dígits):</label>
                <input type="text" name="ccv" pattern="\d{3}" placeholder="xxx" title="3 digits">
            </div>
            
            <div class="mt-20">
                <button type="submit" class="btn btn-success">Enviar Sol·licitud</button>
                <a href="taulell.php" class="btn btn-secondary">Tornar</a>
            </div>
        </form>
        <?php endif; ?>
    </div>
</body>
</html>
