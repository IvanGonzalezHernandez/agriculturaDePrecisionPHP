<?php

class Usuario {
    // Atributos
    private int $idUsuario;
    private string $email;
    private DateTime $fechaCreacion;

    // Constructor
    public function __construct(string $email, DateTime $fechaCreacion) {
        $this->email = $email;
        $this->fechaCreacion = $fechaCreacion;
    }

    // Getters y Setters
    public function getIdUsuario(): int {
        return $this->idUsuario;
    }

    public function setIdUsuario(int $idUsuario): void {
        $this->idUsuario = $idUsuario;
    }

    public function getEmail(): string {
        return $this->email;
    }

    public function setEmail(string $email): void {
        $this->email = $email;
    }

    public function getFechaCreacion(): DateTime {
        return $this->fechaCreacion;
    }

    public function setFechaCreacion(DateTime $fechaCreacion): void {
        $this->fechaCreacion = $fechaCreacion;
    }
}

