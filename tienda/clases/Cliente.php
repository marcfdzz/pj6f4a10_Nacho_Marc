<?php
namespace Clases;

class Cliente extends Usuario {
    private string $numeroTarjeta;
    private string $ccv;

    public function __construct($nombreUsuario, $identificador, $contrasena, $nombreCompleto, $direccion, $correo, $telefono, $numeroTarjeta, $ccv) {
        parent::__construct($nombreUsuario, $identificador, $contrasena, $nombreCompleto, $direccion, $correo, $telefono);
        $this->numeroTarjeta = $numeroTarjeta;
        $this->ccv = $ccv;
    }

    public function getTipo(): string { return 'cliente'; }

    public function obtenerDatos(): array {
        $datos = parent::obtenerDatos();
        $datos['numeroTarjeta'] = $this->numeroTarjeta;
        $datos['ccv'] = $this->ccv;
        return $datos;
    }

    // --- PERSISTENCIA JSON ---

    public static function obtenerTodos(): array {
        if (!file_exists(ARCHIVO_CLIENTES)) return [];
        $json = file_get_contents(ARCHIVO_CLIENTES);
        $datos = json_decode($json, true) ?? [];
        
        $lista = [];
        foreach ($datos as $d) {
            $lista[] = new Cliente(
                $d['nombreUsuario'], $d['identificador'], $d['contrasenaHash'], 
                $d['nombreCompleto'], $d['direccion'], $d['correo'], 
                $d['telefono'], $d['numeroTarjeta'], $d['ccv']
            );
        }
        return $lista;
    }

    public static function buscarPorUsuario($nombreUsuario): ?Cliente {
        $todos = self::obtenerTodos();
        foreach ($todos as $c) {
            if ($c->getNombreUsuario() === $nombreUsuario) return $c;
        }
        return null;
    }

    public function guardar() {
        $todos = self::obtenerTodos();
        $nuevo = true;
        foreach ($todos as $k => $c) {
            if ($c->getNombreUsuario() === $this->nombreUsuario) {
                $todos[$k] = $this;
                $nuevo = false;
                break;
            }
        }
        if ($nuevo) $todos[] = $this;
        
        $datos = array_map(fn($c) => $c->obtenerDatos(), $todos);
        file_put_contents(ARCHIVO_CLIENTES, json_encode($datos, JSON_PRETTY_PRINT));
    }
}
