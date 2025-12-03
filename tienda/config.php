<?php
// Configuración global del proyecto

// Definición de rutas del sistema
define('RUTA_BASE', __DIR__);
define('RUTA_CLASES', RUTA_BASE . '/clases');
define('RUTA_GESTION', RUTA_BASE . '/gestion');
define('RUTA_COMPRA', RUTA_BASE . '/compra');

// Rutas de Directorios de Datos
define('DIR_TRABAJADORES', RUTA_GESTION . '/trabajadores');
define('DIR_CLIENTES', RUTA_GESTION . '/clientes');
define('DIR_PRODUCTOS', RUTA_GESTION . '/productos');
define('DIR_PEDIDOS', RUTA_GESTION . '/pedidos');

// Archivos JSON específicos para persistencia
define('ARCHIVO_PRODUCTOS', DIR_PRODUCTOS . '/productos.json');
define('ARCHIVO_CLIENTES', DIR_CLIENTES . '/clientes.json');
define('ARCHIVO_TRABAJADORES', DIR_TRABAJADORES . '/trabajadores.json');

// Configuración de correo electrónico
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USER', 'tu_correo@gmail.com');
define('SMTP_PASS', 'tu_contrasena');

// Carga automática de clases (Autoload)
require_once dirname(__DIR__) . '/vendor/autoload.php';

spl_autoload_register(function ($clase) {
    $archivo = RUTA_BASE . '/' . str_replace('\\', '/', lcfirst($clase)) . '.php';
    if (file_exists($archivo)) {
        require_once $archivo;
    }
});
