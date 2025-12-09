<?php
require_once 'IGuardable.php';

class Comanda implements IGuardable {
    public $id;
    public $data;
    public $client;
    public $productes;
    public $total;

    public function __construct($id, $data, $client, $productes, $total) {
        $this->id = $id;
        $this->data = $data;
        $this->client = $client;
        $this->productes = $productes;
        $this->total = $total;
    }

    public function obtenirDades() {
        return [
            'id' => $this->id,
            'data' => $this->data,
            'client' => $this->client,
            'productes' => $this->productes,
            'total' => $this->total
        ];
    }
}
?>
