<?php
class Cistella {
    private $productes = [];

    public function __construct($productes = []) {
        $this->productes = $productes;
    }

    public function add($producteId, $quantity) {
        if (isset($this->productes[$producteId])) {
            $this->productes[$producteId] += $quantity;
        } else {
            $this->productes[$producteId] = $quantity;
        }
    }

    public function remove($producteId) {
        unset($this->productes[$producteId]);
    }

    public function getProductes() {
        return $this->productes;
    }

    public function setProductes($productes) {
        $this->productes = $productes;
    }
}
?>
