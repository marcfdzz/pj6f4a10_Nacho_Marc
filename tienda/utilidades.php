<?php
// Funciones de utilidad y helpers

/**
 * Inicia una sesión segura si no está iniciada.
 */
function iniciarSesionSegura() {
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
}

/**
 * Verifica si el usuario está logueado.
 * Redirige al login si no hay sesión activa.
 */
function verificarSesion() {
    iniciarSesionSegura();
    if (!isset($_SESSION['usuario'])) {
        // TODO: Ajustar la ruta de redirección al login
        header('Location: /login.php');
        exit;
    }
}

/**
 * Verifica si el usuario actual es administrador.
 * Detiene la ejecución si no tiene permisos.
 */
function verificarAdmin() {
    verificarSesion();
    // TODO: Implementar lógica de roles basada en la clase Trabajador
    if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
        die("Acceso denegado: Se requieren permisos de administrador.");
    }
}

/**
 * Sanitiza datos de entrada para prevenir XSS.
 * @param string $dato Dato a limpiar
 * @return string Dato limpio
 */
function sanitizar($dato) {
    return htmlspecialchars(trim($dato), ENT_QUOTES, 'UTF-8');
}

/**
 * Valida si una contraseña cumple con los requisitos de seguridad.
 * @param string $pass Contraseña a validar
 * @return bool True si es válida
 */
function validarContrasena($pass) {
    // Mínimo 8 caracteres, 1 mayúscula, 1 minúscula, 1 número
    // TODO: Ajustar regex según requerimientos específicos
    return strlen($pass) >= 8; 
}
