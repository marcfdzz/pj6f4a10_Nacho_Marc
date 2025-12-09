<?php
require_once 'Persona.php';

class Client extends Persona {
    private $adreca;
    private $telefon;
    private $id;

    public function __construct($usuari, $contrasenya, $nom, $email, $adreca, $telefon, $id = null) {
        // Llamamos al constructor del padre (Persona)
        parent::__construct($usuari, $contrasenya, 'client', $nom, $email);
        $this->adreca = $adreca;
        $this->telefon = $telefon;
        $this->id = $id;
    }

    public function obtenirAdreca() { return $this->adreca; }
    public function obtenirTelefon() { return $this->telefon; }
    public function obtenirId() { return $this->id; }

    // Sobreescribimos para añadir datos extra
    public function obtenirDades() {
        $dades = parent::obtenirDades();
        $dades['adreca'] = $this->adreca;
        $dades['telefon'] = $this->telefon;
        $dades['id'] = $this->id;
        // Importante: password solo si es necesario guardar, pero aqui lo simulamos
        $dades['contrasenya'] = $this->contrasenya; 
        return $dades;
    }
}
?>
