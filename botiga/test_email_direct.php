<?php
// Test directo del sistema de correo
require_once 'vendor/autoload.php';
require_once 'classes/Mailer.php';
require_once 'classes/GestorFitxers.php';

echo "=== TEST COMPLETO DEL SISTEMA DE CORREO ===\n\n";

// TEST 1: Configuración
echo "[TEST 1] Verificando configuración...\n";
$adminEmail = Mailer::getAdminEmail();
echo "Email admin configurado: $adminEmail\n";

// TEST 2: Simulación de "Sol·licitud de canvi de dades" (Cliente)
echo "\n[TEST 2] Simulando envío de sol·licitud de cliente...\n";
$clienteUsuario = "test";
$camps = [
    'Nom i Cognoms' => 'Juan Pérez García',
    'Adreça física' => 'Calle Nueva 456',
    'Correu electrònic' => 'nuevo@email.com',
    'Telèfon' => '666888999',
    'Número de targeta' => '1234567890123456',
    'CCV' => '123'
];

$canvis = "";
foreach ($camps as $camp => $valor) {
    if (!empty($valor)) {
        $canvis .= "<strong>$camp:</strong> " . htmlspecialchars($valor) . "<br>";
    }
}

$assumpte = "Sol·licitud de canvi de dades - Client: $clienteUsuario";
$cos = "<h2>Petició de canvi de dades</h2>";
$cos .= "<p>L'usuari <strong>$clienteUsuario</strong> ha sol·licitat canviar les següents dades:</p>";
$cos .= $canvis;
$cos .= "<br><p>Si us plau, revisa la sol·licitud.</p>";

echo "Destinatari: $adminEmail\n";
echo "Asunto: $assumpte\n";
echo "Enviando...\n";

if (Mailer::send($adminEmail, $assumpte, $cos)) {
    echo "✓ [OK] Correo de solicitud enviado correctamente!\n";
} else {
    echo "✗ [ERROR] No se pudo enviar el correo. Revisa mail_log.txt\n";
}

// TEST 3: Simulación de "Notificación de pedido procesado" (Admin)
echo "\n[TEST 3] Simulando envío de notificación de pedido...\n";

// Buscar un cliente real
$clientsFile = __DIR__ . '/gestio/clients/clients.json';
$clients = GestorFitxers::llegirTot($clientsFile);
$clientEmail = '';
$nomClient = '';

foreach ($clients as $c) {
    $u = $c['usuari'] ?? '';
    if ($u === 'test') {
        $clientEmail = $c['email'] ?? '';
        $nomClient = $u;
        break;
    }
}

if ($clientEmail) {
    $idComanda = "TEST_" . date('Ymd_His');
    $subject = "Comanda Processada - ID: $idComanda";
    $body = "<h2>Hola $nomClient,</h2>";
    $body .= "<p>Ens complau informar-te que la teva comanda amb ID <strong>$idComanda</strong> ha estat processada correctament.</p>";
    $body .= "<p>Gràcies per confiar en nosaltres.</p>";
    
    echo "Destinatari: $clientEmail\n";
    echo "Asunto: $subject\n";
    echo "Enviando...\n";
    
    if (Mailer::send($clientEmail, $subject, $body)) {
        echo "✓ [OK] Correo de pedido enviado correctamente!\n";
    } else {
        echo "✗ [ERROR] No se pudo enviar el correo. Revisa mail_log.txt\n";
    }
} else {
    echo "✗ [ERROR] No se encontró el email del cliente test\n";
}

// Mostrar log
echo "\n[LOG] Últimas líneas del mail_log.txt:\n";
echo "-------------------------------------------\n";
if (file_exists('mail_log.txt')) {
    $log = file_get_contents('mail_log.txt');
    $lines = explode("\n", trim($log));
    $lastLines = array_slice($lines, -5);
    echo implode("\n", $lastLines);
} else {
    echo "(No hay log todavía)\n";
}

echo "\n\n=== FIN DEL TEST ===\n";
?>
