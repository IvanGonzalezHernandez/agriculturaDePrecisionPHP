<?php

class Maquina {
    // Atributos
    private $idMaquina;
    private $idMaquinista;
    private $modelo;
    private $capacidad;
    private $anho;

    // Constructor
    public function __construct($idMaquina, $modelo, $capacidad, $anho) {
        $this->idMaquina = $idMaquina;
        $this->modelo = $modelo;
        $this->capacidad = $capacidad;
        $this->anho = $anho;
    }

    // Getters y Setters
    public function getIdMaquina() {
        return $this->idMaquina;
    }

    public function setIdMaquina($idMaquina) {
        $this->idMaquina = $idMaquina;
    }

    public function getModelo() {
        return $this->modelo;
    }

    public function setModelo($modelo) {
        $this->modelo = $modelo;
    }

    public function getCapacidad() {
        return $this->capacidad;
    }

    public function setCapacidad($capacidad) {
        $this->capacidad = $capacidad;
    }

    public function getAnho() {
        return $this->anho;
    }

    public function setAnho($anho) {
        $this->anho = $anho;
    }
}

?>
