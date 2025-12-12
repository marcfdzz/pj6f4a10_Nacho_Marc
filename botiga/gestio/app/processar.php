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
    $fitxer = $_POST['file'] ?? '';
    if (!$fitxer) {
        header('Location: comandes.php');
        exit;
    }

    $pendingDir = __DIR__ . '/../../comandes_copia';
    $processedDir = __DIR__ . '/../comandes_gestionades';
    $origen = $pendingDir . '/' . $fitxer;
    $desti = $processedDir . '/' . $fitxer;

    if (isset($_POST['process'])) {
        if (file_exists($origen)) {
            if (!is_dir($processedDir)) mkdir($processedDir, 0777, true);
            if (rename($origen, $desti)) {
                $missatge = "Comanda processada i moguda correctament.";
                $tipus = 'exit';
            } else {
                $missatge = "Error al moure l'arxiu.";
                $tipus = 'error';
            }
        } else {
            $missatge = "L'arxiu no existeix.";
            $tipus = 'error';
        }
    } elseif (isset($_POST['email'])) {
        if (file_exists($origen)) {
            $comanda = GestorFitxers::llegirTot($origen);
            $nomClient = $comanda['usuari'] ?? $comanda['client'] ?? '';

            $clientsFile = __DIR__ . '/../clients/clients.json';
            $clients = GestorFitxers::llegirTot($clientsFile);
            $correuClient = '';
            
            foreach ($clients as $c) {
                $u = $c['usuari'] ?? $c['nombreUsuario'] ?? $c['username'] ?? '';
                if ($u === $nomClient) {
                    $correuClient = $c['email'] ?? $c['correo'] ?? '';
                    break;
                }
            }

            if ($correuClient) {
                $idComanda = $comanda['id'] ?? 'Desconegut';
                $assumpte = "Comanda Processada - ID: $idComanda";
                $cos = "<h2>Hola $nomClient,</h2>";
                $cos .= "<p>La teva comanda amb ID <strong>$idComanda</strong> ha estat processada correctament.</p>";
                $cos .= "<p>Gràcies!</p>";

                if (Mailer::enviar($correuClient, $assumpte, $cos)) {
                    $missatge = "Correu enviat a $correuClient.";
                    $tipus = 'exit';
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
        <div class="alert alert-<?php echo ($tipus == 'exit') ? 'success' : 'danger'; ?>">
            <?php echo htmlspecialchars($missatge); ?>
        </div>
        <br>
        <a href="comandes.php" class="btn btn-secondary">Tornar a Gestió de Comandes</a>
    </div>
</body>
</html>