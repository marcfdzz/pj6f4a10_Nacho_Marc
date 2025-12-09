<?php
require_once 'IGuardable.php';

// Clase base simple
class Persona implements IGuardable {
    protected $usuari;
    protected $contrasenya;
    protected $rol;
    protected $nom;
    protected $email;

    public function __construct($usuari, $contrasenya, $rol, $nom, $email) {
        $this->usuari = $usuari;
        $this->contrasenya = $contrasenya;
        $this->rol = $rol;
        $this->nom = $nom;
        $this->email = $email;
    }

    // Getters simples
    public function obtenirUsuari() { return $this->usuari; }
    public function obtenirRol() { return $this->rol; }
    public function obtenirNom() { return $this->nom; }
    
    // Implementacion interfaz
    public function obtenirDades() {
        return [
            'usuari' => $this->usuari,
            'rol' => $this->rol,
            'nom' => $this->nom,
            'email' => $this->email
        ];
    }
}
?>
