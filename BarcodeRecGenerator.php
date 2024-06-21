<?php

class BarcodeRecGenerator
{
    private $empresaServicio;
    private $identificadorUsuario;
    private $comprobanteInterno;
    private $fechaPrimerVencimiento;
    private $importePrimerVencimiento;
    private $diasHastaSegundoVencimiento;
    private $importeSegundoVencimiento;
    private $variableAdicional;
    private $identificadorCuenta;

    public function __construct($empresaServicio, $identificadorUsuario, $comprobanteInterno, $fechaPrimerVencimiento, $importePrimerVencimiento, $diasHastaSegundoVencimiento, $importeSegundoVencimiento, $variableAdicional, $identificadorCuenta)
    {
        $this->empresaServicio = str_pad($empresaServicio, 4, '0', STR_PAD_LEFT);
        $this->identificadorUsuario = str_pad($identificadorUsuario, 8, '0', STR_PAD_LEFT);
        $this->comprobanteInterno = str_pad($comprobanteInterno, 7, '0', STR_PAD_LEFT);
        $this->fechaPrimerVencimiento = str_pad($fechaPrimerVencimiento, 6, '0', STR_PAD_LEFT);
        $this->importePrimerVencimiento = str_pad($importePrimerVencimiento, 10, '0', STR_PAD_LEFT);
        $this->diasHastaSegundoVencimiento = str_pad($diasHastaSegundoVencimiento, 2, '0', STR_PAD_LEFT);
        $this->importeSegundoVencimiento = str_pad($importeSegundoVencimiento, 10, '0', STR_PAD_LEFT);
        $this->variableAdicional = str_pad($variableAdicional, 3, '0', STR_PAD_LEFT);
        $this->identificadorCuenta = str_pad($identificadorCuenta, 7, '0', STR_PAD_LEFT);
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
            . $this->identificadorUsuario
            . $this->comprobanteInterno
            . $this->fechaPrimerVencimiento
            . $this->importePrimerVencimiento
            . $this->diasHastaSegundoVencimiento
            . $this->importeSegundoVencimiento
            . $this->variableAdicional
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
            'Identificador de usuario' => 8,
            'comprobanteInterno' => 7,
            'fechaPrimerVencimiento' => 6,
            'importePrimerVencimiento' => 10,
            'diasHastaSegundoVencimiento' => 2,
            'importeSegundoVencimiento' => 10,
            'variableAdicional' => 3,
            'identificadorCuenta' => 7,
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
