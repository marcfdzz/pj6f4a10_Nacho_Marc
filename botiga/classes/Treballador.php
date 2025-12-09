<?php
require_once 'Persona.php';

class Treballador extends Persona {
    private $id;

    public function __construct($usuari, $contrasenya, $nom, $email, $rol = 'treballador') {
        parent::__construct($usuari, $contrasenya, $rol, $nom, $email);
    }
    
    // Si necesitamos guardar info especifica
    public function obtenirDades() {
        $dades = parent::obtenirDades();
        $dades['contrasenya'] = $this->contrasenya;
        return $dades;
    }
}
?>
