<?php
namespace Clases;

/**
 * Clase Cesta (Carrito de Compra)
 * Gestiona los productos que el cliente desea comprar.
 */
class Cesta {
    private array $items = []; // Array asociativo [id_producto => ['producto' => obj, 'cantidad' => int]]

    /**
     * Agrega un producto a la cesta.
     * Si ya existe, incrementa la cantidad.
     */
    public function agregarProducto(Producto $producto, int $cantidad = 1) {
        $id = $producto->getId();
        if (isset($this->items[$id])) {
            $this->items[$id]['cantidad'] += $cantidad;
        } else {
            $this->items[$id] = [
                'producto' => $producto,
                'cantidad' => $cantidad
            ];
        }
    }

    /**
     * Elimina un producto de la cesta por su ID.
     */
    public function eliminarProducto($idProducto) {
        if (isset($this->items[$idProducto])) {
            unset($this->items[$idProducto]);
        }
    }

    /**
     * Vacía completamente la cesta.
     */
    public function vaciar() {
        $this->items = [];
    }

    /**
     * Calcula el precio total de los productos en la cesta.
     * @return float Total sin impuestos
     */
    public function calcularTotal(): float {
        $total = 0;
        foreach ($this->items as $item) {
            $total += $item['producto']->getPrecio() * $item['cantidad'];
        }
        return $total;
    }

    /**
     * Devuelve los items actuales de la cesta.
     */
    public function getItems(): array {
        return $this->items;
    }
}
