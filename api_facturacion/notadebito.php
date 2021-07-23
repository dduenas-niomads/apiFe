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

	$array_emisor = get_array_emisor($data);
	$array_detalle = get_array_detalle($data);
	$array_bank = get_array_bank($data);
	$tipodeproceso = (isset($data['tipo_proceso'])) ? $data['tipo_proceso'] : "3"; //(el número 3 es para prueba, el número 1 es para producción)

	//rutas y nombres de archivos_xml_sunat
	$url_base = '../archivos_xml_sunat/';
    $content_folder_xml = 'cpe_xml/';
	$content_firmas = 'certificados/';
	
	$nombre_archivo = $array_emisor['ruc'] . '-' . $data['cod_tipo_documento'] . '-' . $data['serie_comprobante'].'-'.$data['numero_comprobante'];
	$emisorCredential = new EmisorCredentials();
	$array_emisor['clavesol']   = $emisorCredential->getClaveSol($tipodeproceso);
	$array_emisor['usuariosol'] = $emisorCredential->getUsuarioSol($tipodeproceso);
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
	$procesarcomprobante = new Procesarcomprobante("Nota de débito", $rutas);
	$resp = $procesarcomprobante->procesar_nota_de_debito($array_cabecera, $array_detalle, $array_bank, $rutas);
	
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
			'TOTAL_GRAVADAS' => (isset($data['total_gravadas'])) ? $data['total_gravadas'] : "0",
	        'POR_IGV' => (isset($data['porcentaje_igv'])) ? $data['porcentaje_igv'] : "0", //Porcentaje del impuesto
	        'TOTAL_IGV' => (isset($data['total_igv'])) ? $data['total_igv'] : "0",
			'SUB_TOTAL' => (isset($data['sub_total'])) ? $data['sub_total'] : "0",
	        'TOTAL' => (isset($data['total'])) ? $data['total'] : "0",
			'TOTAL_LETRAS' => $data['total_letras'],
	        //==============================================
	        'TIPO_COMPROBANTE_MODIFICA' => $data['tipo_comprobante_modifica'],
	        'FECHA_COMPROBANTE_MODIFICA' => $data['fecha_comprobante_modifica'],
	        'NRO_DOCUMENTO_MODIFICA' => $data['nro_documento_modifica'],
	        'SERIE_DOCUMENTO_MODIFICA' => $data['serie_documento_modifica'],
	        'COD_TIPO_MOTIVO' => $data['cod_tipo_motivo'],
	        'DESCRIPCION_MOTIVO' => $data['descripcion_motivo'],
	        //===============================================
	        'NRO_COMPROBANTE' => $data['numero_comprobante'],
	        'SERIE_COMPROBANTE' => $data['serie_comprobante'],
			'FECHA_DOCUMENTO' => $data['fecha_comprobante'],
			'MULTIGLOSA' => (isset($data['multiglosa'])) ? $data['multiglosa'] : "",
			'HORA_DOCUMENTO' => (isset($data['hora_comprobante'])) ? $data['hora_comprobante'] : "",
			'FECHA_VTO' => (isset($data['fecha_vto_comprobante'])) ? $data['fecha_vto_comprobante'] : $data['fecha_comprobante'],
			'COD_TIPO_DOCUMENTO' => $data['cod_tipo_documento'],
	        'COD_MONEDA' => $data['cod_moneda'],
	        //==============================================
	        'NRO_GUIA_REMISION' => (isset($data['nro_guia_remision'])) ? $data['nro_guia_remision'] : "",
	        'COD_GUIA_REMISION' => (isset($data['cod_guia_remision'])) ? $data['cod_guia_remision'] : "",
	        'NRO_OTR_COMPROBANTE' => (isset($data['nro_otr_comprobante'])) ? $data['nro_otr_comprobante'] : "",
	        //======= DATOS DEL CLIENTE ===================================
	        'NRO_DOCUMENTO_CLIENTE' => $data['cliente_numerodocumento'],
			'RAZON_SOCIAL_CLIENTE' => $data['cliente_nombre'],
			'TIPO_DOCUMENTO_CLIENTE' => $data['cliente_tipodocumento'], //RUC
			'DIRECCION_CLIENTE' => $data['cliente_direccion'],
			'COD_PAIS_CLIENTE' => $data['cliente_pais'],
			'COD_UBIGEO_CLIENTE' => (isset($data['cliente_codigoubigeo'])) ? $data['cliente_codigoubigeo'] : "",
			'DEPARTAMENTO_CLIENTE' => (isset($data['cliente_departamento'])) ? $data['cliente_departamento'] : "",
			'PROVINCIA_CLIENTE' => (isset($data['cliente_provincia'])) ? $data['cliente_provincia'] : "",
			'DISTRITO_CLIENTE' => (isset($data['cliente_distrito'])) ? $data['cliente_distrito'] : "",
			'CIUDAD_CLIENTE' => (isset($data['cliente_ciudad'])) ? $data['cliente_ciudad'] : "",
	        //===============================================
			'NRO_DOCUMENTO_EMPRESA' => $emisor['ruc'],
			'TIPO_DOCUMENTO_EMPRESA' => $emisor['tipo_doc'], //RUC
			'NOMBRE_COMERCIAL_EMPRESA' => $emisor['nom_comercial'],
			'CODIGO_UBIGEO_EMPRESA' => $emisor['codigo_ubigeo'],
	        'DIRECCION_EMPRESA' => $emisor['direccion'],
	        'DEPARTAMENTO_EMPRESA' => $emisor['direccion_departamento'],
	        'PROVINCIA_EMPRESA' => $emisor['direccion_provincia'],
	        'DISTRITO_EMPRESA' => $emisor['direccion_distrito'],
			'CODIGO_PAIS_EMPRESA' => $emisor['direccion_codigopais'],
			'RAZON_SOCIAL_EMPRESA' => $emisor['razon_social'],
			'CONTACTO_EMPRESA' => "",
			'CORREO_EMPRESA' => $emisor['correo'],
			'TELEFONO_EMPRESA' => $emisor['telefono'],
			'WEB_EMPRESA' => $emisor['web'],
	        //===================CLAVES SOL EMISOR====================//
	        'EMISOR_RUC' => $emisor['ruc'],
	        'EMISOR_USUARIO_SOL' => $emisor['usuariosol'],
			'EMISOR_PASS_SOL' => $emisor['clavesol'],

			//======== TEXTOS =================================
	        'TEXTO_ENCABEZADO' => $data['texto_encabezado'],
	        'TEXTO_13' => $data['texto_13'],
	        'TEXTO_14' => $data['texto_14'],
	        'TEXTO_REFERENCIA' => $data['texto_referencia']
		);
		
		return $cabecera;
	}

	function get_array_detalle($data) {
		
		$detalle_documento = $data['detalle'];
		return $detalle_documento;
	}

	function get_array_bank($data) {

		/* la estructura del array con los items debe tener la siguiente estructura!
		"detalle" => [
                    {
                        "txtITEM"          			=> 1,
                        "txtUNIDAD_MEDIDA_DET"      => "NIU",
                        "txtCANTIDAD_DET"           => "1",
                        "txtPRECIO_DET"             => "100",
                        "txtSUB_TOTAL_DET"          => "84.75",
                        "txtPRECIO_TIPO_CODIGO"     => "01",
                        "txtIGV"                 	=> "15.25",
                        "txtISC"                  	=> "0",
                        "txtIMPORTE_DET"            => "84.75",
                        "txtCOD_TIPO_OPERACION"     => "10",
                        "txtCODIGO_DET"             => "DSDFG",
                        "txtDESCRIPCION_DET"   		=> "Producto 01",
                        "txtPRECIO_SIN_IGV_DET"  	=> 84.75
					}
				]
		*/
		$detalle_bank = $data['bank'];
		return $detalle_bank;
	}

	function get_array_emisor($data) {
		$data_emisor = $data['emisor'];

		//si estamos ofreciendo un servicio de facturación electrónica, aquí podemos recibir el ruc, y el resto de datos podemos extraerlos desde nuestra base de datos.
		//en este caso, asumimos que todos los datos llegan desde la petición.

		$emisor['ruc'] 						= (isset($data_emisor['ruc'])) ? $data_emisor['ruc'] : '';
		$emisor['tipo_doc'] 				= (isset($data_emisor['tipo_doc'])) ? $data_emisor['tipo_doc'] : '6';
		$emisor['nom_comercial'] 			= (isset($data_emisor['nom_comercial'])) ? $data_emisor['nom_comercial'] : '';
		$emisor['razon_social'] 			= (isset($data_emisor['razon_social'])) ? str_replace("&", "y", $data_emisor['razon_social']) : '';
		$emisor['codigo_ubigeo'] 			= (isset($data_emisor['codigo_ubigeo'])) ? $data_emisor['codigo_ubigeo'] : '';
		$emisor['direccion'] 				= (isset($data_emisor['direccion'])) ? $data_emisor['direccion'] : '';
		$emisor['direccion_departamento'] 	= (isset($data_emisor['direccion_departamento'])) ? $data_emisor['direccion_departamento'] : '';
		$emisor['direccion_provincia'] 		= (isset($data_emisor['direccion_provincia'])) ? $data_emisor['direccion_provincia'] : '';
		$emisor['direccion_distrito'] 		= (isset($data_emisor['direccion_distrito'])) ? $data_emisor['direccion_distrito'] : '';
		$emisor['correo'] 					= (isset($data_emisor['correo'])) ? $data_emisor['correo'] : '';
		$emisor['web'] 						= (isset($data_emisor['web'])) ? $data_emisor['web'] : '';
		$emisor['telefono'] 				= (isset($data_emisor['telefono'])) ? $data_emisor['telefono'] : '';
		$emisor['direccion_codigopais'] 	= (isset($data_emisor['direccion_codigopais'])) ? $data_emisor['direccion_codigopais'] : '';
		$emisor['pass_firma'] 				= (isset($data_emisor['pass_firma'])) ? $data_emisor['pass_firma'] : '';

		//Todos los campos anteriores son obligatorios
		//Aquí se pueden generar todas las validaciones que se necesiten.
		//por ejemplo: si ruc está vacio, retornar un error

		return $emisor;
	}
?>