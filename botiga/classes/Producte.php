<?php
require_once 'IGuardable.php';

class Producte implements IGuardable {
    private $id;
    private $nom;
    private $descripcio;
    private $imatge;

    public function __construct($id, $nom, $descripcio, $preu, $imatge = '') {
        $this->id = $id;
        $this->nom = $nom;
        $this->descripcio = $descripcio;
        $this->preu = $preu;
        $this->imatge = $imatge;
    }

    public function obtenirId() { return $this->id; }
    public function obtenirNom() { return $this->nom; }
    public function obtenirPreu() { return $this->preu; }

    public function obtenirDades() {
        return [
            'id' => $this->id,
            'nom' => $this->nom,
            'descripcio' => $this->descripcio,
            'preu' => $this->preu,
            'imatge' => $this->imatge
        ];
    }
}
?>
