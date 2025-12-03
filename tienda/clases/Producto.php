<?php
namespace Clases;

class Producto {
    private string $id;
    private string $nombre;
    private float $precio;
    private ?string $imagen; // Opcional

    public function __construct($id, $nombre, $precio, $imagen = null) {
        $this->id = $id;
        $this->nombre = $nombre;
        $this->precio = (float)$precio;
        $this->imagen = $imagen;
    }

    // Getters
    public function getId() { return $this->id; }
    public function getNombre() { return $this->nombre; }
    public function getPrecio() { return $this->precio; }
    public function getImagen() { return $this->imagen; }

    public function obtenerDatos(): array {
        return [
            'id' => $this->id,
            'nombre' => $this->nombre,
            'precio' => $this->precio,
            'imagen' => $this->imagen
        ];
    }

    // --- PERSISTENCIA JSON ---

    /**
     * Obtiene todos los productos del archivo JSON.
     * @return Producto[] Array de objetos Producto
     */
    public static function obtenerTodos(): array {
        if (!file_exists(ARCHIVO_PRODUCTOS)) return [];
        
        $json = file_get_contents(ARCHIVO_PRODUCTOS);
        $datos = json_decode($json, true) ?? [];
        
        $productos = [];
        foreach ($datos as $d) {
            $productos[] = new Producto($d['id'], $d['nombre'], $d['precio'], $d['imagen'] ?? null);
        }
        return $productos;
    }

    /**
     * Busca un producto por ID.
     */
    public static function buscarPorId($id): ?Producto {
        $productos = self::obtenerTodos();
        foreach ($productos as $p) {
            if ($p->getId() == $id) return $p;
        }
        return null;
    }

    /**
     * Guarda (añade o actualiza) este producto en el JSON.
     */
    public function guardar() {
        $productos = self::obtenerTodos();
        $nuevo = true;
        
        // Buscar si ya existe para actualizar
        foreach ($productos as $key => $p) {
            if ($p->getId() == $this->id) {
                $productos[$key] = $this;
                $nuevo = false;
                break;
            }
        }
        
        if ($nuevo) {
            $productos[] = $this;
        }

        // Convertir objetos a arrays para guardar
        $datosGuardar = array_map(fn($p) => $p->obtenerDatos(), $productos);
        
        // Guardar JSON formateado
        file_put_contents(ARCHIVO_PRODUCTOS, json_encode($datosGuardar, JSON_PRETTY_PRINT));
    }
}
