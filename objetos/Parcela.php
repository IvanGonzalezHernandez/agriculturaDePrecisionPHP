<?php

class Parcela {
    // Atributos
    private $idParcela;
    private $idAgricultor; // FK
    private $catastro;  // Identificación catastral de la parcela
    private $superficie;   // Superficie en metros cuadrados

    // Constructor
    public function __construct($idParcela, $idAgricultor, $catastro, $superficie) {
        $this->idParcela = $idParcela;
        $this->idAgricultor = $idAgricultor;
        $this->catastro = $catastro;
        $this->setSuperficie($superficie);  // Usar el setter para validar superficie
    }

    // Getters y Setters
    public function getIdParcela() {
        return $this->idParcela;
    }

    public function setIdParcela($idParcela) {
        $this->idParcela = $idParcela;
    }

    public function getIdAgricultor() {
        return $this->idAgricultor;
    }

    public function setIdAgricultor($idAgricultor) {
        $this->idAgricultor = $idAgricultor;
    }

    public function getCatastro() {
        return $this->catastro;
    }

    public function setCatastro($catastro) {
        $this->catastro = $catastro;
    }

    public function getSuperficie() {
        return $this->superficie;
    }

    public function setSuperficie($superficie) {
        // Validación simple para superficie positiva
        if ($superficie > 0) {
            $this->superficie = $superficie;
        } else {
            echo "La superficie debe ser un valor positivo.\n";
        }
    }
}

?>
