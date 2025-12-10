<?php
// Script de testeig automàtic del sistema
// Simula les accions dels botons i verifica la configuració

require_once 'classes/Mailer.php';
require_once 'classes/GestorFitxers.php';

echo "--- INICI DEL TEST DEL SISTEMA ---\n";

// 1. TEST CONFIGURACIÓ CORREU
echo "\n[1] Verificant configuració de correu...\n";
$adminEmail = Mailer::getAdminEmail();
if ($adminEmail && $adminEmail !== 'admin@example.com') {
    echo "[OK] Correu administrador configurat: $adminEmail\n";
} else {
    echo "[AVIS] El correu d'admin sembla el per defecte o no es llegeix. (Mira mail_config.ini)\n";
}

// 2. TEST ENVIAMENT REAL (Simulació de Botó 'Enviar Sol·licitud')
echo "\n[2] Provant enviament de correu (Simulació Botó Sol·licitud)...\n";
echo "Intentant enviar correu de prova a: $adminEmail...\n";
$subject = "TEST SISTEMA - " . date('Y-m-d H:i:s');
$body = "Això és una prova automàtica per verificar que PHPMailer funciona correctament.";

if (Mailer::send($adminEmail, $subject, $body)) {
    echo "[OK] CRÍTIC: L'enviament de correu ha funcionat! (PHPMailer connectat a Gmail)\n";
} else {
    echo "[ERROR] L'enviament ha fallat. Revisa 'mail_log.txt' per veure l'error exacte.\n";
}

// 3. TEST ESTRUCTURA DADES (Simulació Lògica 'Processar Comanda')
echo "\n[3] Verificant base de dades de clients (necessari per 'Enviar Correu Comanda')...\n";
$clientsFile = __DIR__ . '/gestio/clients/clients.json';
if (file_exists($clientsFile)) {
    $clients = GestorFitxers::llegirTot($clientsFile);
    if (!empty($clients)) {
        $firstClient = $clients[0];
        $email = $firstClient['email'] ?? $firstClient['correo'] ?? null;
        if ($email) {
             echo "[OK] S'han trobat clients i tenen email (Exemple: $email). La lògica de processar funcionarà.\n";
        } else {
             echo "[ERROR] S'han trobat clients però NO tenen camp 'email'. El botó de comanda fallarà.\n";
        }
    } else {
        echo "[AVIS] El fitxer de clients existeix però està buit.\n";
    }
} else {
    echo "[ERROR] No es troba 'gestio/clients/clients.json'.\n";
}

// 4. TEST PRODUCTES
echo "\n[4] Verificant rutes de productes...\n";
$prodFile = __DIR__ . '/gestio/productes/productes.json';
if (file_exists($prodFile)) {
    echo "[OK] Fitxer de productes trobat a la ruta unificada: $prodFile\n";
} else {
    echo "[ERROR] No es troba el fitxer de productes.\n";
}

echo "\n--- FI DEL TEST ---\n";
?>
