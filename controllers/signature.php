<?php
require_once('../api_signature/XMLSecurityKey.php');
require_once('../api_signature/XMLSecurityDSig.php');
require_once('../api_signature/XMLSecEnc.php');

class Signature {
    public function signature_xml($flg_firma, $ruta, $ruta_firma, $pass_firma, $idDocumento = null, $appId = null) {
        //flg_firma:
        //          01, 03, 07, 08: Firmar en el nodo uno.
        //          00: Firmar en el Nodo Cero (para comprobantes de Percepción o Retención)
        
        $doc = new DOMDocument();

        $doc->formatOutput = FALSE;
        $doc->preserveWhiteSpace = TRUE;
        $doc->load($ruta . '.xml');

        $objDSig = new XMLSecurityDSig(FALSE);
        $objDSig->setCanonicalMethod(XMLSecurityDSig::C14N);
        $options['force_uri'] = TRUE;
        $options['id_name'] = 'ID';
        $options['overwrite'] = FALSE;

        $objDSig->addReference($doc, XMLSecurityDSig::SHA1, array('http://www.w3.org/2000/09/xmldsig#enveloped-signature'), $options);
        $objKey = new XMLSecurityKey(XMLSecurityKey::RSA_SHA1, array('type' => 'private'));

        $pfx = file_get_contents($ruta_firma);
        $key = array();

        openssl_pkcs12_read($pfx, $key, $pass_firma);
        $objKey->loadKey($key["pkey"]);
        $objDSig->add509Cert($key["cert"], TRUE, FALSE);
        $objDSig->sign($objKey, $doc->documentElement->getElementsByTagName("ExtensionContent")->item($flg_firma));

        $atributo = $doc->getElementsByTagName('Signature')->item(0);
        $atributo->setAttribute('Id', 'SignatureSP');
        
        //===================rescatamos Codigo(HASH_CPE)==================
        $hash_cpe = $doc->getElementsByTagName('DigestValue')->item(0)->nodeValue;

        $doc->save($ruta . '.xml');
        $resp['respuesta'] = 'ok';
        $resp['hash_cpe'] = $hash_cpe;
        // ACTUALIZAR HASH POR ID DE DOCUMENTO
        if (!is_null($idDocumento)) {
            switch ($appId) {
                case 7:
                    # TUMIFACTURA
                    $conn = new mysqli("172.19.0.34", 
                        "tumifactura",
                        "tumipos2020.",
                        "tumifactura_prod");
                    if ($conn->connect_error) {
                    //   die("ERROR: Unable to connect: " . $conn->connect_error);
                    } else {
                        // echo 'Connected to the database.<br>';
                        $sunatLog = [
                            "ticket" => [
                                "hash_cdr" => $hash_cpe
                            ],
                            "url_xml" => $ruta,
                            "hash_cdr" => $hash_cpe,
                            "hash_cpe" => $hash_cpe,
                            "ruta_cdr" => null,
                            "ruta_pdf" => "",
                            "ruta_xml" => str_replace('..', '', $ruta),
                            "cod_sunat" => "0",
                            "msj_sunat" => "El documento ha sido aceptado",
                            "respuesta" => "ok"
                        ];
                        $ruta_ = str_replace('..', '', $ruta);
                        $string = "UPDATE sal_sales SET sunat_log = '" . json_encode($sunatLog) . "' WHERE id = " . $idDocumento . ";";
                        // $string = 'UPDATE sal_sales SET sunat_log = ' . json_encode($sunatLog) . ' WHERE id = ' . $idDocumento . ';';
                        $result = $conn->query($string);
                        // echo "Number of rows: $result->num_rows";
                        // $result->close();
                        $conn->close();
                    }
                    break;
                case 8:
                    # TUMIFACTURA
                    $conn = new mysqli("172.19.0.34", 
                        "tumifactura",
                        "tumipos2020.",
                        "tumifactura_prod");
                    if ($conn->connect_error) {
                    //   die("ERROR: Unable to connect: " . $conn->connect_error);
                    } else {
                        // echo 'Connected to the database.<br>';
                        $sunatLog = [
                            "url_xml" => $ruta,
                            "hash_cdr" => $hash_cpe,
                            "hash_cpe" => $hash_cpe,
                            "ruta_cdr" => null,
                            "ruta_pdf" => "",
                            "ruta_xml" => str_replace('..', '', $ruta),
                            "cod_sunat" => "0",
                            "msj_sunat" => "El documento ha sido aceptado",
                            "respuesta" => "ok"
                        ];
                        $ruta_ = str_replace('..', '', $ruta);
                        $string = "UPDATE sal_remission_guide SET sunat_log = '" . json_encode($sunatLog) . "' WHERE id = " . $idDocumento . ";";
                        // $string = 'UPDATE sal_remission_guide SET sunat_log = ' . json_encode($sunatLog) . ' WHERE id = ' . $idDocumento . ';';
                        $result = $conn->query($string);
                        // echo "Number of rows: $result->num_rows";
                        // $result->close();
                        $conn->close();
                    }
                    break;
                default:
                    # WEB, POS, FOOD, STYLISH
                    $conn = new mysqli("172.19.0.34", 
                        "tumipos",
                        "tumipos2020.",
                        "dp6_tumipos_prod");
                    if ($conn->connect_error) {
                    //   die("ERROR: Unable to connect: " . $conn->connect_error);
                    } else {
                        // echo 'Connected to the database.<br>';
                        $ruta_ = str_replace('..', '', $ruta);
                        if (is_array($idDocumento)) {
                            $string = 'UPDATE sal_sale_documents SET hash = "' . $hash_cpe . '", url_xml = "'. $ruta_ . '.xml" , url_invoice = "https://consulta-fe.tumi-soft.com" WHERE id in (' . implode(',', $idDocumento) . ');';
                        } else {
                            $string = 'UPDATE sal_sale_documents SET hash = "' . $hash_cpe . '", url_xml = "'. $ruta_ . '.xml" , url_invoice = "https://consulta-fe.tumi-soft.com" WHERE id = ' . $idDocumento . ';';
                        }
                        $result = $conn->query($string);
                        // echo "Number of rows: $result->num_rows";
                        // $result->close();
                        $conn->close();
                    }
                    break;
            }
            // $appId
            // SQL ACTION
        }
        return $resp;
    }
}
?>