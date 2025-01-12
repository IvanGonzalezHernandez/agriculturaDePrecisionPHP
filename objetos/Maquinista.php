<?php

require_once 'Usuario.php'; // Asegúrate de incluir la clase Usuario

class Maquinista extends Usuario {
    // Atributos
    private $idMaquinista;
    private $certificacion; // Se usará una enumeración
    private $nombre;

    // Enumeración para Certificación (simulada con constantes en PHP)
    const CERTIFICADO_A = 'CERTIFICADO_A';
    const CERTIFICADO_B = 'CERTIFICADO_B';
    const CERTIFICADO_C = 'CERTIFICADO_C';

    // Constructor
    public function __construct($idMaquinista, $nombre, $certificacion, $email, $fechaCreacion) {
        parent::__construct($email, $fechaCreacion); // Llama al constructor de la clase padre (Usuario)
        $this->idMaquinista = $idMaquinista;
        $this->nombre = $nombre;
        $this->certificacion = $certificacion;
    }

    // Getters y Setters
    public function getIdMaquinista() {
        return $this->idMaquinista;
    }

    public function setIdMaquinista($idMaquinista) {
        $this->idMaquinista = $idMaquinista;
    }

    public function getCertificacion() {
        return $this->certificacion;
    }

    public function setCertificacion($certificacion) {
        if (!in_array($certificacion, [self::CERTIFICADO_A, self::CERTIFICADO_B, self::CERTIFICADO_C])) {
            throw new InvalidArgumentException("Certificación no válida");
        }
        $this->certificacion = $certificacion;
    }

    public function getNombre() {
        return $this->nombre;
    }

    public function setNombre($nombre) {
        $this->nombre = $nombre;
    }
}

?>
