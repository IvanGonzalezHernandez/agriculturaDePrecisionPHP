<?php

require_once 'Usuario.php'; // Asegúrate de incluir la clase Usuario

class Agricultor extends Usuario {
    // Atributos
    private int $idAgricultor;
    private string $nombre;
    private string $password;
    private int $telefono;
    private array $parcelaUsuario; // PHP no tiene 'Lista<T>' como en Java, usamos arrays

    // Constructor
    public function __construct(
        int $idAgricultor,
        string $nombre,
        string $password,
        int $telefono,
        string $email,
        DateTime $fechaCreacion
    ) {
        parent::__construct($email, $fechaCreacion); // Llamar al constructor de la clase padre
        $this->idAgricultor = $idAgricultor;
        $this->nombre = $nombre;
        $this->password = $password;
        $this->telefono = $telefono;
        $this->parcelaUsuario = [];
    }

    // Getters y Setters
    public function getIdAgricultor(): int {
        return $this->idAgricultor;
    }

    public function setIdAgricultor(int $idAgricultor): void {
        $this->idAgricultor = $idAgricultor;
    }

    public function getNombre(): string {
        return $this->nombre;
    }

    public function setNombre(string $nombre): void {
        $this->nombre = $nombre;
    }

    public function getPassword(): string {
        return $this->password;
    }

    public function setPassword(string $password): void {
        $this->password = $password;
    }

    public function getTelefono(): int {
        return $this->telefono;
    }

    public function setTelefono(int $telefono): void {
        $this->telefono = $telefono;
    }

    public function getParcelaUsuario(): array {
        return $this->parcelaUsuario;
    }

    public function setParcelaUsuario(array $parcelaUsuario): void {
        $this->parcelaUsuario = $parcelaUsuario;
    }

    public function agregarParcela($parcela): void {
        $this->parcelaUsuario[] = $parcela;
    }
}
