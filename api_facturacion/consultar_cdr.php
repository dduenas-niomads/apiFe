<?php
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);
	include "../controllers/validaciondedatos.php";
	include "../controllers/procesarcomprobante.php";
	include "../controllers/emisor_credentials.php";
	error_reporting(E_ALL ^ E_NOTICE);
	// para aceptar la conexión desde cualquier origen
	header("Access-Control-Allow-Origin: *");
	// Permite los métodos GET, POST, PUT, DELETE
	header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
	//obtenemos la data de la solicitud
	$bodyRequest = file_get_contents("php://input");
	// Decodificamos y lo guardamos en un array
	$data = json_decode($bodyRequest, true);
	$tipodeproceso = (isset($data['tipo_proceso'])) ? $data['tipo_proceso'] : "3";
	//rutas y nombres de archivos_xml_sunat
	$url_base = '../archivos_xml_sunat/';
    $content_folder_xml = 'cpe_xml/';
	$content_firmas = 'certificados/';
	$emisorCredential = new EmisorCredentials();
	$array_emisor = [ "ruc" => (isset($data['ruc'])) ? $data['ruc'] : null ];
	$array_emisor['clavesol']   = $emisorCredential->getClaveSol($tipodeproceso);
	$array_emisor['usuariosol'] = $emisorCredential->getUsuarioSol($tipodeproceso);
	$nombre_archivo = $array_emisor['ruc'] . '-' . $data['tipo_documento'] . '-' . $data['serie'].'-'.$data['correlativo'];
	$pass_firma = $emisorCredential->getPassFirma($tipodeproceso);
	$ruta_ws = $emisorCredential->getRutaWS($tipodeproceso);

	if ($tipodeproceso == '1') {
		$ruta = $url_base . $content_folder_xml . 'produccion/' . $array_emisor['ruc'] . "/" . $nombre_archivo;
		$ruta_cdr = $url_base . $content_folder_xml . 'produccion/' . $array_emisor['ruc'] . "/";
		$ruta_firma = $url_base . $content_firmas . 'produccion/' . $array_emisor['ruc'] . '.p12';
		if (!file_exists($ruta_firma)) {
			$ruta_firma = $url_base . $content_firmas . 'produccion/' . $array_emisor['ruc'] . '.pfx';
		}
	}
	if ($tipodeproceso == '3') {
		$ruta = $url_base . $content_folder_xml . 'beta/' . $array_emisor['ruc'] . "/" . $nombre_archivo;
		$ruta_cdr = $url_base . $content_folder_xml . 'beta/' . $array_emisor['ruc'] . "/";
		$ruta_firma = $url_base . $content_firmas . 'beta/' . $array_emisor['ruc'] . '.p12';
		if (!file_exists($ruta_firma)) {
			$ruta_firma = $url_base . $content_firmas . 'beta/' . $array_emisor['ruc'] . '.pfx';
		}
	}

	$rutas = array();
    $rutas['nombre_archivo'] = $nombre_archivo;
    $rutas['ruta_xml'] = $ruta;
    $rutas['ruta_cdr'] = $ruta_cdr;
    $rutas['ruta_firma'] = $ruta_firma;
    $rutas['pass_firma'] = $pass_firma;
	$rutas['ruta_ws'] = $ruta_ws;
    $rutas['ruc'] = $array_emisor['ruc'];
	
	$array_cabecera = get_array_cabecera($data, $array_emisor);
	$procesarcomprobante = new Procesarcomprobante("consultar documento", $rutas);
	$resp = $procesarcomprobante->procesar_consultar_cdr($array_cabecera, $rutas);
	
	$folder = 'beta';
	if ($tipodeproceso == '1') {
		$folder = 'produccion';
	}

	$resp['ruta_xml'] = '/archivos_xml_sunat/cpe_xml/' . $folder . '/' . $array_emisor['ruc'] . '/' . $nombre_archivo . '.xml';
	$resp['ruta_cdr'] = '/archivos_xml_sunat/cpe_xml/' . $folder . '/' . $array_emisor['ruc'] . '/R-' . $nombre_archivo . '.xml';
	$resp['ruta_pdf'] = '/controllers/prueba.php?tipo=boleta&id=' . $nombre_archivo;

	header('Content-Type: application/json');
	echo json_encode($resp);
    exit();
    
    function get_array_cabecera($data, $emisor) {
		$cabecera = array(
            'TIPO_DOCUMENTO'          => $data['tipo_documento'],
            'SERIE'                     => $data['serie'],
            'CORRELATIVO'               => $data['correlativo'],
	        //===================CLAVES SOL EMISOR====================//
	        'EMISOR_RUC' 				=> $emisor['ruc'],
	        'EMISOR_USUARIO_SOL' 		=> $emisor['usuariosol'],
			'EMISOR_PASS_SOL' 			=> $emisor['clavesol']
		);		
		return $cabecera;
    }
?>