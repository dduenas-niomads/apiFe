<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include "apisunat_2_1.php"; //ubl 2.1
include "signature.php";
class Procesarcomprobante {
	
	function __construct($entidad, $rutas) {
		if (!file_exists('../archivos_xml_sunat')) {
			// crear archivos_xml_sunat
				mkdir("../archivos_xml_sunat", 0777);
			// crear cpe_xml
				mkdir("../archivos_xml_sunat/cpe_xml", 0777);
				mkdir("../archivos_xml_sunat/cpe_xml/beta", 0777);
				mkdir("../archivos_xml_sunat/cpe_xml/produccion", 0777);
			// crear certificados
				mkdir("../archivos_xml_sunat/certificados", 0777);
				mkdir("../archivos_xml_sunat/certificados/beta", 0777);
				mkdir("../archivos_xml_sunat/certificados/produccion", 0777);
		}
		$estructura_beta = "../archivos_xml_sunat/cpe_xml/beta/" . $rutas["ruc"];
		$estructura_prod = "../archivos_xml_sunat/cpe_xml/produccion/" . $rutas["ruc"];
		if (!file_exists($estructura_beta) || !file_exists($estructura_prod)) {
			mkdir($estructura_beta, 0777, true);
			chmod($estructura_beta,0777);
			mkdir($estructura_prod, 0777, true);
			chmod($estructura_prod,0777);
			// copiar certificado pfx
			$fichero = "beta.pfx";
			$nuevo_fichero_beta = "../archivos_xml_sunat/certificados/beta/" . $rutas["ruc"] . ".pfx";
			$nuevo_fichero_prod = "../archivos_xml_sunat/certificados/produccion/" . $rutas["ruc"] . ".pfx";
			copy($fichero, $nuevo_fichero_beta);
			chmod($nuevo_fichero_beta,0777);
			copy($fichero, $nuevo_fichero_prod);
			chmod($nuevo_fichero_prod,0777);
		}
	}

	public function procesar_factura($data_comprobante, $items_detalle, $rutas, $idDocumento = null, $appId = null) {
		$apisunat = new apisunat();
		$resp = $apisunat->crear_xml_factura($data_comprobante, $items_detalle, $rutas['ruta_xml']);
		$signature = new Signature();
		$flg_firma = "0";
		
		$resp_firma = $signature->signature_xml($flg_firma, $rutas['ruta_xml'], $rutas['ruta_firma'], $rutas['pass_firma'], $idDocumento, $appId);

		if($resp_firma['respuesta'] == 'error') {
			return $resp_firma;
		}

		$resp_envio = $apisunat->enviar_documento($data_comprobante['EMISOR_RUC'], $data_comprobante['EMISOR_USUARIO_SOL'], $data_comprobante['EMISOR_PASS_SOL'], $rutas['ruta_xml'], $rutas['ruta_cdr'], $rutas['nombre_archivo'], $rutas['ruta_ws']);
		if($resp_envio['respuesta'] == 'error') {
			return $resp_envio;
		}
		
		
		$resp['respuesta'] = 'ok';
		$resp['hash_cpe'] = $resp_firma['hash_cpe'];
		$resp['hash_cdr'] = $resp_envio['hash_cdr'];
		$resp['cod_sunat'] = $resp_envio['cod_sunat'];
		$resp['msj_sunat'] = $resp_envio['mensaje'];
		return $resp;
	}

	public function procesar_boleta($data_comprobante, $items_detalle, $rutas, $idDocumento = null, $appId = null) {
		$apisunat = new apisunat();
		//El xml para factura y boleta es prácticamente el mismo
		$resp = $apisunat->crear_xml_factura($data_comprobante, $items_detalle, $rutas['ruta_xml']);

		$signature = new Signature();
		$flg_firma = "0";
		$resp_firma = $signature->signature_xml($flg_firma, $rutas['ruta_xml'], $rutas['ruta_firma'], $rutas['pass_firma'], $idDocumento, $appId);

		if($resp_firma['respuesta'] == 'error') {
			return $resp_firma;
		}

		$resp_envio = $apisunat->enviar_documento($data_comprobante['EMISOR_RUC'], $data_comprobante['EMISOR_USUARIO_SOL'], $data_comprobante['EMISOR_PASS_SOL'], $rutas['ruta_xml'], $rutas['ruta_cdr'], $rutas['nombre_archivo'], $rutas['ruta_ws']);
		if($resp_envio['respuesta'] == 'error') {
			return $resp_envio;
		}
		
		$resp['respuesta'] = 'ok';
		$resp['hash_cpe'] = $resp_firma['hash_cpe'];
		$resp['hash_cdr'] = $resp_envio['hash_cdr'];
		$resp['cod_sunat'] = $resp_envio['cod_sunat'];
		$resp['msj_sunat'] = $resp_envio['mensaje'];
		return $resp;
	}

	public function procesar_nota_de_credito($data_comprobante, $items_detalle, $rutas, $idDocumento = null, $appId = null) {
		$apisunat = new apisunat();
		$resp = $apisunat->crear_xml_nota_credito($data_comprobante, $items_detalle, $rutas['ruta_xml']);

		$signature = new Signature();
		$flg_firma = "0";
		$resp_firma = $signature->signature_xml($flg_firma, $rutas['ruta_xml'], $rutas['ruta_firma'], $rutas['pass_firma'], $idDocumento, $appId);

		if($resp_firma['respuesta'] == 'error') {
			return $resp_firma;
		}
		$resp_envio = $apisunat->enviar_documento($data_comprobante['EMISOR_RUC'], $data_comprobante['EMISOR_USUARIO_SOL'], $data_comprobante['EMISOR_PASS_SOL'], $rutas['ruta_xml'], $rutas['ruta_cdr'], $rutas['nombre_archivo'], $rutas['ruta_ws']);
		if($resp_envio['respuesta'] == 'error') {
			return $resp_envio;
		}
		
		$resp['respuesta'] = 'ok';
		$resp['hash_cpe'] = $resp_firma['hash_cpe'];
		$resp['hash_cdr'] = $resp_envio['hash_cdr'];
		$resp['cod_sunat'] = $resp_envio['cod_sunat'];
		$resp['msj_sunat'] = $resp_envio['mensaje'];
		return $resp;
	}

	public function procesar_nota_de_debito($data_comprobante, $items_detalle, $rutas, $idDocumento = null, $appId = null) {
		$apisunat = new apisunat();
		$resp = $apisunat->crear_xml_nota_debito($data_comprobante, $items_detalle, $rutas['ruta_xml']);

		$signature = new Signature();
		$flg_firma = "0";
		$resp_firma = $signature->signature_xml($flg_firma, $rutas['ruta_xml'], $rutas['ruta_firma'], $rutas['pass_firma'], $idDocumento, $appId);

		if($resp_firma['respuesta'] == 'error') {
			return $resp_firma;
		}

		$resp_envio = $apisunat->enviar_documento($data_comprobante['EMISOR_RUC'], $data_comprobante['EMISOR_USUARIO_SOL'], $data_comprobante['EMISOR_PASS_SOL'], $rutas['ruta_xml'], $rutas['ruta_cdr'], $rutas['nombre_archivo'], $rutas['ruta_ws']);
		if($resp_envio['respuesta'] == 'error') {
			return $resp_envio;
		}
		
		$resp['respuesta'] = 'ok';
		$resp['hash_cpe'] = $resp_firma['hash_cpe'];
		$resp['hash_cdr'] = $resp_envio['hash_cdr'];
		$resp['cod_sunat'] = $resp_envio['cod_sunat'];
		$resp['msj_sunat'] = $resp_envio['mensaje'];
		return $resp;
	}

	public function procesar_guia_de_remision($data_comprobante, $items_detalle, $rutas, $idDocumento = null, $appId = null) {
		$apisunat = new apisunat();
		$resp = $apisunat->crear_xml_guia_remision($data_comprobante, $items_detalle, $rutas['ruta_xml']);

		$signature = new Signature();
		$flg_firma = "0";
		$resp_firma = $signature->signature_xml($flg_firma, $rutas['ruta_xml'], $rutas['ruta_firma'], $rutas['pass_firma'], $idDocumento, $appId);

		if($resp_firma['respuesta'] == 'error') {
			return $resp_firma;
		}
		
		$resp_envio = $apisunat->enviar_documento($data_comprobante['EMISOR_RUC'], $data_comprobante['EMISOR_USUARIO_SOL'], $data_comprobante['EMISOR_PASS_SOL'], $rutas['ruta_xml'], $rutas['ruta_cdr'], $rutas['nombre_archivo'], $rutas['ruta_ws']);

		if($resp_envio['respuesta'] == 'error') {
			return $resp_envio;
		}
		
		$resp['respuesta'] = 'ok';
		$resp['hash_cpe']  = $resp_firma['hash_cpe'];
		$resp['hash_cdr']  = $resp_envio['hash_cdr'];
		$resp['cod_sunat'] = $resp_envio['cod_sunat'];
		$resp['msj_sunat'] = $resp_envio['mensaje'];
		return $resp;
	}

	public function procesar_resumen_boletas($data_comprobante, $items_detalle, $rutas, $idDocumento = null, $appId = null) {
		$apisunat = new apisunat();
		$resp = $apisunat->crear_xml_resumen_documentos($data_comprobante, $items_detalle, $rutas['ruta_xml']);

		$signature = new Signature();
		$flg_firma = "0";
		$resp_firma = $signature->signature_xml($flg_firma, $rutas['ruta_xml'], $rutas['ruta_firma'], $rutas['pass_firma'], $idDocumento, $appId);

		if($resp_firma['respuesta'] == 'error') {
			return $resp_firma;
		}

		$resp_envio = $apisunat->enviar_resumen_boletas($data_comprobante['EMISOR_RUC'], $data_comprobante['EMISOR_USUARIO_SOL'], $data_comprobante['EMISOR_PASS_SOL'], $rutas['ruta_xml'], $rutas['ruta_cdr'], $rutas['nombre_archivo'], $rutas['ruta_ws']);
		if($resp_envio['respuesta'] == 'error') {
			return $resp_envio;
		}
		
		$resp_ticket = $apisunat->consultar_envio_ticket($data_comprobante['EMISOR_RUC'], $data_comprobante['EMISOR_USUARIO_SOL'], $data_comprobante['EMISOR_PASS_SOL'], $resp_envio['cod_ticket'], $rutas['nombre_archivo'], $rutas['ruta_cdr'], $rutas['ruta_ws']);
		$resp_ticket['cod_ticket'] = $resp_envio['cod_ticket'];
		
		// $resp['respuesta'] 				   = 'ok';
		// $resp['resp_envio_doc'] 		   = $resp_envio['respuesta'];
		// $resp['resp_consulta_ticket']  	   = $resp_ticket['respuesta'];
		// $resp['resp_error_consult_ticket'] = 'Cod: '.$resp_ticket['cod_sunat'].' Mensaje: '.$resp_ticket['cod_sunat'];
		// $resp['ticket']   				   = $resp_ticket;
		// $resp['hash_cpe'] 				   = $resp_firma['hash_cpe'];
		// $resp['hash_cdr'] 				   = $resp_ticket['hash_cdr'];
		// $resp['msj_sunat'] 				   = $resp_ticket['mensaje'];

		$resp_ticket['cod_ticket'] = $resp_envio['cod_ticket'];
		$resp_ticket['cod_sunat'] = 0;
		$resp_ticket['respuesta'] = 'ok';
		$resp_ticket['mensaje'] = isset($resp_envio['msj_sunat']) ? $resp_envio['msj_sunat'] : $resp_envio['respuesta'];
		$resp_ticket['msj_sunat'] = isset($resp_envio['msj_sunat']) ? $resp_envio['msj_sunat'] : $resp_envio['respuesta'];
		$resp_ticket['hash_cdr'] = $resp_firma['hash_cpe'];

		$resp['respuesta'] 				   = 'ok';
		$resp['resp_envio_doc'] 		   = $resp_envio['respuesta'];
		$resp['resp_consulta_ticket']  	   = null;
		$resp['resp_error_consult_ticket'] = 'Cod: 0000';
		$resp['ticket']   				   = $resp_ticket;
		$resp['hash_cpe'] 				   = $resp_firma['hash_cpe'];
		$resp['hash_cdr'] 				   = $resp_firma['hash_cpe'];
		$resp['msj_sunat'] 				   = isset($resp_envio['msj_sunat']) ? $resp_envio['msj_sunat'] : $resp_envio['respuesta'];
		return $resp;
	}

	public function procesar_baja_sunat($data_comprobante, $items_detalle, $rutas, $idDocumento = null, $appId = null) {
		$apisunat = new apisunat();
		$resp = $apisunat->crear_xml_baja_sunat($data_comprobante, $items_detalle, $rutas['ruta_xml']);

		$signature = new Signature();
		$flg_firma = "0";
		$resp_firma = $signature->signature_xml($flg_firma, $rutas['ruta_xml'], $rutas['ruta_firma'], $rutas['pass_firma'], $idDocumento, $appId);

		if($resp_firma['respuesta'] == 'error') {
			return $resp_firma;
		}

		$resp_envio = $apisunat->enviar_documento_para_baja($data_comprobante['EMISOR_RUC'], $data_comprobante['EMISOR_USUARIO_SOL'], $data_comprobante['EMISOR_PASS_SOL'], $rutas['ruta_xml'], $rutas['ruta_cdr'], $rutas['nombre_archivo'], $rutas['ruta_ws']);
		if($resp_envio['respuesta'] == 'error') {
			return $resp_envio;
		}

		$resp_ticket = $apisunat->consultar_envio_ticket($data_comprobante['EMISOR_RUC'], $data_comprobante['EMISOR_USUARIO_SOL'], $data_comprobante['EMISOR_PASS_SOL'], $resp_envio['cod_ticket'], $rutas['nombre_archivo'], $rutas['ruta_cdr'], $rutas['ruta_ws']);
		$resp_ticket['cod_ticket'] = $resp_envio['cod_ticket'];

		$resp['respuesta'] 				   = 'ok';
		$resp['resp_envio_doc'] 	 	   = $resp_envio['respuesta'];
		$resp['resp_consulta_ticket']	   = $resp_ticket['respuesta'];
		$resp['resp_error_consult_ticket'] = 'Cod: '.$resp_ticket['cod_sunat'].' Mensaje: '.$resp_ticket['cod_sunat'];
		$resp['ticket']   				   = $resp_ticket;
		$resp['hash_cpe'] 				   = $resp_firma['hash_cpe'];
		$resp['hash_cdr'] 				   = $resp_ticket['hash_cdr'];
		$resp['msj_sunat'] 				   = $resp_ticket['mensaje'];
		return $resp;
	}

	public function procesar_consultar_document($data_comprobante, $items_detalle, $rutas, $idDocumento = null, $appId = null) {
		$apisunat = new apisunat();
		$resp_ticket = $apisunat->consultar_envio_ticket($data_comprobante['EMISOR_RUC'], $data_comprobante['EMISOR_USUARIO_SOL'], $data_comprobante['EMISOR_PASS_SOL'], $data_comprobante['TICKET'], $rutas['nombre_archivo'], $rutas['ruta_cdr'], $rutas['ruta_ws']);
		return $resp_ticket;
	}

	public function procesar_consultar_ticket($data_comprobante, $rutas, $idDocumento = null, $appId = null) {
		$apisunat = new apisunat();
		$resp_ticket = $apisunat->consultar_ticket($data_comprobante['EMISOR_RUC'], $data_comprobante['EMISOR_USUARIO_SOL'], $data_comprobante['EMISOR_PASS_SOL'], $data_comprobante['TICKET'], $rutas['nombre_archivo'], $rutas['ruta_cdr'], $rutas['ruta_ws']);
		return $resp_ticket;
	}

	public function procesar_consultar_cdr($data_comprobante, $rutas, $idDocumento = null, $appId = null) {
		$apisunat = new apisunat();
		$resp_ticket = $apisunat->consultar_cdr($data_comprobante['EMISOR_RUC'], $data_comprobante['EMISOR_USUARIO_SOL'], $data_comprobante['EMISOR_PASS_SOL'], $data_comprobante, $rutas['nombre_archivo'], $rutas['ruta_cdr'], $rutas['ruta_ws']);
		return $resp_ticket;
	}
}
?>