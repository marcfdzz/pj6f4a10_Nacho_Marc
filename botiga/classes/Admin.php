<?php
require_once 'Treballador.php';

class Admin extends Treballador {
    public function __construct($nombreUsuario, $contrasena, $correo, $nombre) {
        parent::__construct($nombreUsuario, $contrasena, $correo, $nombre);
        $this->rol = 'admin';
    }
}
?>
