<?php
namespace Clases;

class Trabajador extends Usuario {
    private string $rol;

    public function __construct($nombreUsuario, $identificador, $contrasena, $rol, $nombreCompleto, $direccion, $correo, $telefono) {
        parent::__construct($nombreUsuario, $identificador, $contrasena, $nombreCompleto, $direccion, $correo, $telefono);
        $this->rol = $rol;
    }

    public function getRol(): string { return $this->rol; }
    public function esAdmin(): bool { return $this->rol === 'admin'; }

    public function obtenerDatos(): array {
        $datos = parent::obtenerDatos();
        $datos['rol'] = $this->rol;
        return $datos;
    }

    // --- PERSISTENCIA JSON ---

    public static function obtenerTodos(): array {
        if (!file_exists(ARCHIVO_TRABAJADORES)) return [];
        $json = file_get_contents(ARCHIVO_TRABAJADORES);
        $datos = json_decode($json, true) ?? [];
        
        $lista = [];
        foreach ($datos as $d) {
            $lista[] = new Trabajador(
                $d['nombreUsuario'], $d['identificador'], $d['contrasenaHash'], 
                $d['rol'], $d['nombreCompleto'], $d['direccion'], 
                $d['correo'], $d['telefono']
            );
        }
        return $lista;
    }

    public static function buscarPorUsuario($nombreUsuario): ?Trabajador {
        $todos = self::obtenerTodos();
        foreach ($todos as $t) {
            if ($t->getNombreUsuario() === $nombreUsuario) return $t;
        }
        return null;
    }

    public function guardar() {
        $todos = self::obtenerTodos();
        $nuevo = true;
        foreach ($todos as $k => $t) {
            if ($t->getNombreUsuario() === $this->nombreUsuario) {
                $todos[$k] = $this;
                $nuevo = false;
                break;
            }
        }
        if ($nuevo) $todos[] = $this;
        
        $datos = array_map(fn($t) => $t->obtenerDatos(), $todos);
        file_put_contents(ARCHIVO_TRABAJADORES, json_encode($datos, JSON_PRETTY_PRINT));
    }
}
