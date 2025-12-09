<?php
require_once 'User.php';

class Treballador extends User {
    public function __construct($nombreUsuario, $contrasena, $correo, $nombre) {
        parent::__construct($nombreUsuario, $contrasena, 'treballador', $correo, $nombre);
    }
}
?>
