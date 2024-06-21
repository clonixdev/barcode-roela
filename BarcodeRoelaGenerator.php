<?php

class BarcodeRoelaGenerator
{
    private $empresaServicio;
    private $identificadorConcepto;
    private $identificadorUsuario;
    private $fechaPrimerVencimiento;
    private $importePrimerVencimiento;
    private $diasHastaSegundoVencimiento;
    private $importeSegundoVencimiento;
    private $diasHastaTercerVencimiento;
    private $importeTercerVencimiento;
    private $identificadorCuenta;

    public function __construct(
        $empresaServicio, 
        $identificadorConcepto, 
        $identificadorUsuario, 
        $fechaPrimerVencimiento, 
        $importePrimerVencimiento, 
        $diasHastaSegundoVencimiento, 
        $importeSegundoVencimiento, 
        $diasHastaTercerVencimiento, 
        $importeTercerVencimiento, 
        $identificadorCuenta
    ) {
        $this->empresaServicio = str_pad($empresaServicio, 4, '0', STR_PAD_LEFT);
        $this->identificadorConcepto = str_pad($identificadorConcepto, 1, '0', STR_PAD_LEFT);
        $this->identificadorUsuario = str_pad($identificadorUsuario, 8, '0', STR_PAD_LEFT);
        $this->fechaPrimerVencimiento = str_pad($fechaPrimerVencimiento, 6, '0', STR_PAD_LEFT);
        $this->importePrimerVencimiento = str_pad($importePrimerVencimiento, 7, '0', STR_PAD_LEFT);
        $this->diasHastaSegundoVencimiento = str_pad($diasHastaSegundoVencimiento, 2, '0', STR_PAD_LEFT);
        $this->importeSegundoVencimiento = str_pad($importeSegundoVencimiento, 7, '0', STR_PAD_LEFT);
        $this->diasHastaTercerVencimiento = str_pad($diasHastaTercerVencimiento, 2, '0', STR_PAD_LEFT);
        $this->importeTercerVencimiento = str_pad($importeTercerVencimiento, 7, '0', STR_PAD_LEFT);
        $this->identificadorCuenta = str_pad($identificadorCuenta, 10, '0', STR_PAD_LEFT);
    }

    private function calculateChecksum($tcCadena)
    {
		$tn = 2;

          $tcCadena = trim($tcCadena);
    $lnLen = strlen($tcCadena);
    $lcSeq = "1" . str_repeat("3579", ceil($lnLen / 4));
    $lnSum = 0;

    for ($lnIni = 0; $lnIni < $lnLen; $lnIni++) {
        $lnSum += intval(substr($tcCadena, $lnIni, 1)) * intval(substr($lcSeq, $lnIni, 1));
    }

    $term = $lnSum / 2;
    $term_int = (int) $term;
    $check = $term_int % 10;

    $lcRet = $tcCadena . $check;

    $check1 = $check;
    $check2 = null;

    if ($tn == 2) {
        $tcCadena = trim($lcRet);
        $lnLen = strlen($tcCadena);
        $lcSeq = "1" . str_repeat("3579", ceil($lnLen / 4));
        $lnSum = 0;

        for ($lnIni = 0; $lnIni < $lnLen; $lnIni++) {
            $lnSum += intval(substr($tcCadena, $lnIni, 1)) * intval(substr($lcSeq, $lnIni, 1));
        }
		
		$lnSum = intval($lnSum);
		$term = $lnSum / 2;
		$term_int = (int) $term;
		$check = $term_int % 10;

        $lcRet = $tcCadena . $check;
        $check2 = $check;
    }

    return $check1.$check2;
    }

    public function generateBarcode()
    {
        $baseData = $this->empresaServicio
            . $this->identificadorConcepto
            . $this->identificadorUsuario
            . $this->fechaPrimerVencimiento
            . $this->importePrimerVencimiento
            . $this->diasHastaSegundoVencimiento
            . $this->importeSegundoVencimiento
            . $this->diasHastaTercerVencimiento
            . $this->importeTercerVencimiento
            . $this->identificadorCuenta;

            return  $baseData.$this->calculateChecksum($baseData);
    }

    public function verifyBarcode($barcode)
    {
        $baseData = substr($barcode, 0, -2);
        $verifier1 = substr($barcode, -2, 1);
        $verifier2 = substr($barcode, -1);

        $checksum = $this->calculateChecksum($baseData);
        return $verifier1.$verifier2  == $checksum;
    }


    public function extractComponents($barcode)
    {
        $lengths = [
            'Empresa de Servicio' => 4,
            'Identificador de concepto' => 1,
            'Identificador de usuario' => 8,
            'Fecha 1er Vto' => 6,
            'Importe 1er Vto' => 7,
            'Días hasta 2do Vto' => 2,
            'Importe 2do Vto' => 7,
            'Días hasta 3er Vto' => 2,
            'Importe 3er Vto' => 7,
            'Identificador de Cuenta' => 10,
            'Dígito Verificador 1' => 1,
            'Dígito Verificador 2' => 1,
        ];

        $components = [];
        $start = 0;

        foreach ($lengths as $key => $length) {
            $components[$key] = substr($barcode, $start, $length);
            $start += $length;
        }

        return $components;
    }
}
?>
