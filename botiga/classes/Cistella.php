<?php
require_once 'IGuardable.php';

class Cistella implements IGuardable {
    private $productes = [];

    public function __construct($productes = []) {
        $this->productes = $productes;
    }

    public function afegir($producteId, $quantitat) {
        if (isset($this->productes[$producteId])) {
            $this->productes[$producteId] += $quantitat;
        } else {
            $this->productes[$producteId] = $quantitat;
        }
    }

    public function eliminar($producteId) {
        unset($this->productes[$producteId]);
    }

    public function obtenirProductes() {
        return $this->productes;
    }

    public function establirProductes($productes) {
        $this->productes = $productes;
    }

    public function obtenirDades() {
        return $this->productes;
    }
}
?>
