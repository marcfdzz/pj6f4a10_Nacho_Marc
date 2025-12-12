<?php
session_start();
require_once __DIR__ . '/../../classes/GestorFitxers.php';
require_once __DIR__ . '/../../classes/Mailer.php';

if (empty($_SESSION['usuari']) || ($_SESSION['rol'] !== 'admin' && $_SESSION['rol'] !== 'treballador')) {
    header('Location: inici_sessio.php');
    exit;
}

$missatge = '';
$tipus = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $file = $_POST['file'] ?? '';
    if (!$file) {
        header('Location: comandes.php');
        exit;
    }

    $pendingDir = __DIR__ . '/../../comandes_copia';
    $processedDir = __DIR__ . '/../comandes_gestionades';
    $source = $pendingDir . '/' . $file;
    $dest = $processedDir . '/' . $file;

    if (isset($_POST['process'])) {
        if (file_exists($source)) {
            if (!is_dir($processedDir)) mkdir($processedDir, 0777, true);
            if (rename($source, $dest)) {
                $missatge = "Comanda processada i moguda correctament.";
                $tipus = 'success';
            } else {
                $missatge = "Error al moure l'arxiu.";
                $tipus = 'error';
            }
        } else {
            $missatge = "L'arxiu no existeix.";
            $tipus = 'error';
        }
    } elseif (isset($_POST['email'])) {
        if (file_exists($source)) {
            $comanda = GestorFitxers::llegirTot($source);
            $nomClient = $comanda['usuari'] ?? $comanda['client'] ?? '';

            $clientsFile = __DIR__ . '/../clients/clients.json';
            $clients = GestorFitxers::llegirTot($clientsFile);
            $clientEmail = '';
            
            foreach ($clients as $c) {
                $u = $c['usuari'] ?? $c['nombreUsuario'] ?? $c['username'] ?? '';
                if ($u === $nomClient) {
                    $clientEmail = $c['email'] ?? $c['correo'] ?? '';
                    break;
                }
            }

            if ($clientEmail) {
                $idComanda = $comanda['id'] ?? 'Desconegut';
                $subject = "Comanda Processada - ID: $idComanda";
                $body = "<h2>Hola $nomClient,</h2>";
                $body .= "<p>La teva comanda amb ID <strong>$idComanda</strong> ha estat processada correctament.</p>";
                $body .= "<p>Gràcies!</p>";

                if (Mailer::send($clientEmail, $subject, $body)) {
                    $missatge = "Correu enviat a $clientEmail.";
                    $tipus = 'success';
                } else {
                    $missatge = "Error enviant correu.";
                    $tipus = 'error';
                }
            } else {
                $missatge = "No s'ha trobat el correu del client ($nomClient).";
                $tipus = 'error';
            }
        } else {
            $missatge = "L'arxiu de comanda no existeix.";
            $tipus = 'error';
        }
    }
} else {
    header('Location: comandes.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <title>Resultat</title>
    <link rel="stylesheet" href="../../css/gestio.css">
</head>
<body>
    <div class="container" style="text-align: center; margin-top: 50px;">
        <div class="alert alert-<?php echo ($tipus == 'success') ? 'success' : 'danger'; ?>">
            <?php echo htmlspecialchars($missatge); ?>
        </div>
        <br>
        <a href="comandes.php" class="btn btn-secondary">Tornar a Gestió de Comandes</a>
    </div>
</body>
</html>