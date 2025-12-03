<?php
namespace Clases;

abstract class Usuario {
    protected string $nombreUsuario;
    protected string $identificador;
    protected string $contrasenaHash;
    protected string $nombreCompleto;
    protected string $direccion;
    protected string $correo;
    protected string $telefono;

    public function __construct($nombreUsuario, $identificador, $contrasena, $nombreCompleto, $direccion, $correo, $telefono) {
        $this->nombreUsuario = $nombreUsuario;
        $this->identificador = $identificador;
        
        // Si ya viene hasheada (empieza por $2y$), la dejamos. Si no, hash nuevo.
        if (strpos($contrasena, '$2y$') === 0) {
            $this->contrasenaHash = $contrasena;
        } else {
            $this->contrasenaHash = password_hash($contrasena, PASSWORD_DEFAULT);
        }
        
        $this->nombreCompleto = $nombreCompleto;
        $this->direccion = $direccion;
        $this->correo = $correo;
        $this->telefono = $telefono;
    }

    public function verificarContrasena($contrasena): bool {
        return password_verify($contrasena, $this->contrasenaHash);
    }

    public function obtenerDatos(): array {
        return [
            'nombreUsuario' => $this->nombreUsuario,
            'identificador' => $this->identificador,
            'contrasenaHash' => $this->contrasenaHash,
            'nombreCompleto' => $this->nombreCompleto,
            'direccion' => $this->direccion,
            'correo' => $this->correo,
            'telefono' => $this->telefono
        ];
    }

    // Getters básicos
    public function getNombreUsuario() { return $this->nombreUsuario; }
    public function getNombreCompleto() { return $this->nombreCompleto; }

    // --- PERSISTENCIA ESTÁTICA ---

    /**
     * Busca un usuario en Clientes y Trabajadores.
     * @return Usuario|null Objeto Cliente o Trabajador
     */
    public static function login($usuario, $contrasena) {
        // 1. Buscar en Trabajadores
        $trabajador = Trabajador::buscarPorUsuario($usuario);
        if ($trabajador && $trabajador->verificarContrasena($contrasena)) {
            return $trabajador;
        }

        // 2. Buscar en Clientes
        $cliente = Cliente::buscarPorUsuario($usuario);
        if ($cliente && $cliente->verificarContrasena($contrasena)) {
            return $cliente;
        }

        return null;
    }
}
