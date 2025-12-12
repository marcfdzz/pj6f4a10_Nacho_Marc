<?php
require_once __DIR__ . '/Persona.php';

class Client extends Persona {
    private $adreca;
    private $telefon;
    private $id;

    public function __construct($usuari, $contrasenya, $nom, $email, $adreca, $telefon, $id = null) {
        parent::__construct($usuari, $contrasenya, 'client', $nom, $email);
        $this->adreca = $adreca;
        $this->telefon = $telefon;
        $this->id = $id;
    }

    public function obtenirAdreca() { 
        return $this->adreca; 
    }
    
    public function obtenirTelefon() { 
        return $this->telefon; 
    }
    
    public function obtenirId() { 
        return $this->id; 
    }

    public function obtenirDades() {
        $dades = parent::obtenirDades();
        $dades['adreca'] = $this->adreca;
        $dades['telefon'] = $this->telefon;
        $dades['id'] = $this->id;
        $dades['contrasenya'] = $this->contrasenya;
        return $dades;
    }
}
?>
