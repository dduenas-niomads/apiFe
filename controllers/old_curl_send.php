// curl
        // $headers = array(
        //     "Content-type: text/xml;charset=\"utf-8\"",
        //     "Accept: text/xml",
        //     "Cache-Control: no-cache",
        //     "Pragma: no-cache",
        //     "SOAPAction: urn:sendSummary",
        //     "Content-length: " . strlen($xml_post_string),
        // ); //SOAPAction: your op URL
        // // PHP cURL  for https connection with auth
        // $ch = curl_init();
        // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
        // curl_setopt($ch, CURLOPT_URL, $url);
        // curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        // //curl_setopt($ch, CURLOPT_USERPWD, $soapUser.":".$soapPassword); // username and password - declared at the top of the doc
        // curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
        // curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        // curl_setopt($ch, CURLOPT_POST, true);
        // curl_setopt($ch, CURLOPT_POSTFIELDS, $xml_post_string); // the SOAP request
        // curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        // // converting
        // $response = curl_exec($ch);
        // $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        // curl_close($ch);
        // if ($httpcode == 200) {//======LA PAGINA SI RESPONDE
        //     //convertimos de base 64 a archivo fisico
        //     $doc = new DOMDocument();
        //     $doc->loadXML($response);
        //     // RESPUESTA XML
        //     //===================VERIFICAMOS SI HA ENVIADO CORRECTAMENTE EL COMPROBANTE=====================
        //     if (isset($doc->getElementsByTagName('ticket')->item(0)->nodeValue)) {
        //         $ticket = $doc->getElementsByTagName('ticket')->item(0)->nodeValue;
        //         unlink($ruta_archivo . '.zip');
        //         $mensaje['respuesta'] = 'ok';
        //         $mensaje['cod_ticket'] = $ticket;
        //         $mensaje['extra'] = $doc->getElementsByTagName('faultcode')->item(0)->nodeValue.' - '.$doc->getElementsByTagName('faultstring')->item(0)->nodeValue;
        //     } else {
        //         $mensaje['respuesta'] = 'error';
        //         $mensaje['cod_sunat'] = $doc->getElementsByTagName('faultcode')->item(0)->nodeValue;
        //         $mensaje['mensaje'] = $doc->getElementsByTagName('faultstring')->item(0)->nodeValue;
        //         $mensaje['hash_cdr'] = "";
        //     }
        // } else {
        //     //echo "no responde web";
        //     $mensaje['respuesta'] = 'error';
        //     $mensaje['cod_sunat']="0000";
        //     $mensaje['mensaje']="SINCRONIZACIÓN CORRECTA";
        //     $mensaje['response_xml'] = $response;
        //     $mensaje['mensaje_original']="SUNAT ESTA FUERA SERVICIO: " . $httpcode;
	    // 	$mensaje['url_envio'] = $url;
		//     $mensaje['hash_cdr'] = "";
        // }