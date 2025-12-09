<?php
require_once 'User.php';

class Client extends User {
    private $direccion;
    private $telefono;
    private $id;

    public function __construct($nombreUsuario, $contrasena, $correo, $nombre, $direccion, $telefono, $id = null) {
        parent::__construct($nombreUsuario, $contrasena, 'client', $correo, $nombre);
        $this->direccion = $direccion;
        $this->telefono = $telefono;
        $this->id = $id;
    }

    public function obtenerDireccion() { return $this->direccion; }
    public function obtenerTelefono() { return $this->telefono; }
    public function obtenerId() { return $this->id; }

    public function establecerDireccion($direccion) { $this->direccion = $direccion; }
    public function establecerTelefono($telefono) { $this->telefono = $telefono; }

    public function toArray() {
        $data = parent::toArray();
        $data['direccion'] = $this->direccion;
        $data['telefono'] = $this->telefono;
        $data['id'] = $this->id;
        return $data;
    }
}
?>
