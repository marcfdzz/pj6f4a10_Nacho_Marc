<?php
namespace Clases;

class Pedido {
    private string $id;
    private string $fecha;
    private array $items;
    private float $total;
    private string $usuarioId;

    public function __construct($usuarioId, $items, $total) {
        $this->usuarioId = $usuarioId;
        $this->items = $items;
        $this->total = $total;
        $this->fecha = date('Y-m-d H:i:s');
        $this->id = uniqid('PED-');
    }

    public function getId() { return $this->id; }

    /**
     * Guarda el pedido en un archivo JSON individual.
     */
    public function guardar() {
        $datos = [
            'id' => $this->id,
            'fecha' => $this->fecha,
            'usuarioId' => $this->usuarioId,
            'total' => $this->total,
            'items' => array_map(function($item) {
                return [
                    'id_producto' => $item['producto']->getId(),
                    'nombre' => $item['producto']->getNombre(),
                    'precio' => $item['producto']->getPrecio(),
                    'cantidad' => $item['cantidad']
                ];
            }, $this->items)
        ];

        // Asegurar que el directorio existe
        if (!file_exists(DIR_PEDIDOS)) {
            mkdir(DIR_PEDIDOS, 0777, true);
        }

        $rutaArchivo = DIR_PEDIDOS . '/' . $this->id . '.json';
        file_put_contents($rutaArchivo, json_encode($datos, JSON_PRETTY_PRINT));
        
        return $this->id;
    }
}
