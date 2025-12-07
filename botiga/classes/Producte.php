<?php
class Producte {
    public $id;
    public $nom;
    public $descripcio;
    public $preu;
    public $imatge;

    public function __construct($id, $nom, $descripcio, $preu, $imatge) {
        $this->id = $id;
        $this->nom = $nom;
        $this->descripcio = $descripcio;
        $this->preu = $preu;
        $this->imatge = $imatge;
    }

    public function toArray() {
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
