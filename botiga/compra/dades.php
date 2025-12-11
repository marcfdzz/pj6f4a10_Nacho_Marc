<?php
session_start();
require_once __DIR__ . '/../classes/GestorFitxers.php';

if (empty($_SESSION['usuari']) || ($_SESSION['rol'] ?? '') !== 'client') {
    header('Location: inici_sessio.php');
    exit;
}

$usuariActual = $_SESSION['usuari'];
$fitxerClients = __DIR__ . '/../gestio/clients/clients.json';
$dadesClients = GestorFitxers::llegirTot($fitxerClients);

$dadesUsuari = null;
foreach ($dadesClients as $c) {
    if (($c['usuari'] ?? '') === $usuariActual) {
        $dadesUsuari = $c;
        break;
    }
}

if (!$dadesUsuari) {
    echo "No s'han trobat les dades.";
    exit;
}

// Generar PDF amb DomPDF
if (isset($_GET['generar_pdf'])) {
    require_once __DIR__ . '/../../vendor/autoload.php';
    
    $dompdf = new \Dompdf\Dompdf();
    
    $html = '<!DOCTYPE html>
    <html lang="ca">
    <head>
        <meta charset="UTF-8">
        <style>
            body { font-family: Arial, sans-serif; padding: 20px; }
            h1 { color: #333; border-bottom: 2px solid #333; padding-bottom: 10px; }
            .field { margin: 15px 0; }
            .label { font-weight: bold; display: inline-block; width: 150px; }
            .value { display: inline-block; }
        </style>
    </head>
    <body>
        <h1>Dades del Client</h1>
        <div class="field">
            <span class="label">Usuari:</span>
            <span class="value">' . htmlspecialchars($dadesUsuari['usuari'] ?? '') . '</span>
        </div>
        <div class="field">
            <span class="label">Nom Complet:</span>
            <span class="value">' . htmlspecialchars($dadesUsuari['nom'] ?? '') . '</span>
        </div>
        <div class="field">
            <span class="label">Email:</span>
            <span class="value">' . htmlspecialchars($dadesUsuari['email'] ?? '') . '</span>
        </div>
        <div class="field">
            <span class="label">Telèfon:</span>
            <span class="value">' . htmlspecialchars($dadesUsuari['telefon'] ?? '') . '</span>
        </div>
        <div class="field">
            <span class="label">Adreça:</span>
            <span class="value">' . htmlspecialchars($dadesUsuari['adreca'] ?? '') . '</span>
        </div>
        <p style="margin-top: 30px; font-size: 12px; color: #666;">Generat el ' . date('d/m/Y H:i') . '</p>
    </body>
    </html>';
    
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();
    $dompdf->stream("dades_client_" . $usuariActual . ".pdf", array("Attachment" => true));
    exit;
}
?>
<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <title>Les meves dades</title>
    <link rel="stylesheet" href="../css/compra.css">
</head>
<body>
    <div class="container">
        <div style="display:flex; justify-content:space-between; align-items:center;">
            <h1>Dades del Client</h1>
            <a href="taulell.php" class="btn btn-secondary no-print">← Tornar al Taulell</a>
        </div>
        
        <div class="field" style="margin-top: 20px;">
            <span class="label">Usuari:</span>
            <span class="value"><?php echo htmlspecialchars($dadesUsuari['usuari'] ?? ''); ?></span>
        </div>
        <div class="field">
            <span class="label">Nom Complet:</span>
            <span class="value"><?php echo htmlspecialchars($dadesUsuari['nom'] ?? ''); ?></span>
        </div>
        <div class="field">
            <span class="label">Email:</span>
            <span class="value"><?php echo htmlspecialchars($dadesUsuari['email'] ?? ''); ?></span>
        </div>
        <div class="field">
            <span class="label">Telèfon:</span>
            <span class="value"><?php echo htmlspecialchars($dadesUsuari['telefon'] ?? ''); ?></span>
        </div>
        <div class="field">
            <span class="label">Adreça:</span>
            <span class="value"><?php echo htmlspecialchars($dadesUsuari['adreca'] ?? ''); ?></span>
        </div>
        
        <div class="no-print mt-20">
            <a href="?generar_pdf=1" class="btn btn-primary">Descarregar PDF</a>
        </div>
    </div>
</body>
</html>
