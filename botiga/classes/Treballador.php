<?php
require_once __DIR__ . '/Persona.php';

class Treballador extends Persona {
    private $id;

    public function __construct($usuari, $contrasenya, $nom, $email, $rol = 'treballador') {
        parent::__construct($usuari, $contrasenya, $rol, $nom, $email);
    }
    
    // Sobreescrivim per afegir la contrasenya a les dades
    public function obtenirDades() {
        $dades = parent::obtenirDades();
        $dades['contrasenya'] = $this->contrasenya;
        return $dades;
    }
}
?>
