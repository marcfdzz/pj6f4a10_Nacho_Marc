<?php
class User {
    protected $nombreUsuario;
    protected $contrasena;
    protected $rol;
    protected $correo;
    protected $nombre;

    public function __construct($nombreUsuario, $contrasena, $rol, $correo, $nombre) {
        $this->nombreUsuario = $nombreUsuario;
        $this->contrasena = $contrasena;
        $this->rol = $rol;
        $this->correo = $correo;
        $this->nombre = $nombre;
    }

    public function obtenerNombreUsuario() { return $this->nombreUsuario; }
    public function obtenerContrasena() { return $this->contrasena; }
    public function obtenerRol() { return $this->rol; }
    public function obtenerCorreo() { return $this->correo; }
    public function obtenerNombre() { return $this->nombre; }

    public function establecerContrasena($contrasena) { $this->contrasena = $contrasena; }
    public function establecerCorreo($correo) { $this->correo = $correo; }
    public function establecerNombre($nombre) { $this->nombre = $nombre; }

    public function toArray() {
        return [
            'nombreUsuario' => $this->nombreUsuario,
            'contrasena' => $this->contrasena,
            'rol' => $this->rol,
            'correo' => $this->correo,
            'nombre' => $this->nombre
        ];
    }
}
?>
