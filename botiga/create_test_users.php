<?php
// Script para crear usuarios de prueba con contraseñas conocidas
require_once 'classes/GestorFitxers.php';

echo "Creando usuarios de prueba...\n\n";

// Cliente de prueba
$clientTest = [
    'id' => '9999',
    'usuari' => 'test',
    'contrasenya' => password_hash('test', PASSWORD_DEFAULT),
    'nom' => 'Usuario Test',
    'email' => 'test@test.com',
    'telefon' => '666777888',
    'adreca' => 'Calle Test 123',
    'card_encrypted' => '1234567890123456',
    'card_exp' => '12/25',
    'created_at' => date('c')
];

// Admin de prueba
$adminTest = [
    'id' => '9998',
    'usuari' => 'testadmin',
    'contrasenya' => password_hash('test', PASSWORD_DEFAULT),
    'nom' => 'Admin Test',
    'email' => 'admin@test.com',
    'telefon' => '666777999',
    'adreca' => 'Calle Admin 456',
    'rol' => 'admin',
    'created_at' => date('c')
];

// Cargar y agregar cliente
$clientsFile = __DIR__ . '/gestio/clients/clients.json';
$clients = GestorFitxers::llegirTot($clientsFile);
$clients[] = $clientTest;
GestorFitxers::guardarTot($clientsFile, $clients);
echo "[OK] Cliente test creado: test / test\n";

// Crear carpeta del cliente
$dirUsuari = __DIR__ . '/compra/area_clients/test';
if (!is_dir($dirUsuari)) mkdir($dirUsuari, 0777, true);
GestorFitxers::guardarTot($dirUsuari . '/dades', $clientTest);

// Cargar y agregar admin
$adminFile = __DIR__ . '/gestio/treballadors/treballadors.json';
$admins = GestorFitxers::llegirTot($adminFile);
$admins[] = $adminTest;
GestorFitxers::guardarTot($adminFile, $admins);
echo "[OK] Admin test creado: testadmin / test\n";

echo "\nUsuarios de prueba creados exitosamente!\n";
echo "Cliente: test / test\n";
echo "Admin: testadmin / test\n";
?>
