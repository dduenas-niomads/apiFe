<?php
include "printpdf.php";
$print = new Printpdf();
$tipo_cpe = $_GET['tipo'];
$id = $_GET['id'];

if($tipo_cpe == 'factura') {
	$print->get_factura('');
}

if($tipo_cpe == 'boleta') {
	$print->get_boleta('');
}

if($tipo_cpe == 'notadebito') {
	$print->get_notadebito('');
}

if($tipo_cpe == 'notacredito') {
	$print->get_notacredito('');
}
?>