<?php
require_once 'BarcodeRoelaGenerator.php'; 
require_once 'BarcodeRecGenerator.php'; 

$empresaServicio = "0450";
$identificadorConcepto = "1";
$identificadorUsuario = "00000001";
$fechaPrimerVencimiento = "240610";
$importePrimerVencimiento = "0890008";
$diasHastaSegundoVencimiento = "00";
$importeSegundoVencimiento = "0890008";
$diasHastaTercerVencimiento = "00";
$importeTercerVencimiento = "0890008";
$identificadorCuenta = "5150018487";

$barcodeGen = new BarcodeRoelaGenerator(
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
);

$barcode = $barcodeGen->generateBarcode();
echo "Código generado: " . $barcode . "\n";

$isVerified = $barcodeGen->verifyBarcode($barcode);
echo "Verification: " . ($isVerified ? "Valid" : "Invalid") . "\n";

$importe = 890008.41;

$importeFormateado = number_format($importe, 2, '', ''); 
$importeFormateado = str_pad($importeFormateado, 10, '0', STR_PAD_LEFT); 

// Fecha en formato "YYYY-MM-DD"
$fechaVencimiento = "2024-06-10";


if(!$fechaVencimiento) return null;
// Convertir fecha al formato "DDMMYY"
$fechaVencimientoFormateada = date_create_from_format('Y-m-d', $fechaVencimiento)->format('ymd');



// Ejemplo de uso:
$empresaServicio = "0448";
$identificadorUsuario = "00000000";
$comprobanteInterno = str_pad( "8697852",7,"0",STR_PAD_LEFT);; //"8697852";
$fechaPrimerVencimiento = $fechaVencimientoFormateada;
$importePrimerVencimiento = $importeFormateado;
$diasHastaSegundoVencimiento = "07";
$importeSegundoVencimiento =  $importeFormateado; //"0089000841";
$variableAdicional = "515";  // Variable adicional que antes estaba mal identificada
$identificadorCuenta =  str_pad("0018487",7,"0",STR_PAD_LEFT); //"0018487"

$barcodeGen = new BarcodeRecGenerator(
	$empresaServicio,
	$identificadorUsuario,
	$comprobanteInterno,
	$fechaPrimerVencimiento,
	$importePrimerVencimiento,
	$diasHastaSegundoVencimiento,
	$importeSegundoVencimiento,
	$variableAdicional,
	$identificadorCuenta
);

$barcode = $barcodeGen->generateBarcode();

$barcode = $barcodeGen->generateBarcode();
echo "Código generado: " . $barcode . "\n";

$okcode = "04480000000086978522406100089000841070089000841515001848712";

$isVerified = $okcode == $barcode;

echo "Verification: " . ($isVerified ? "Valid" : "Invalid") . "\n";
