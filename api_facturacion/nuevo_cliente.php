<?php
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);

	error_reporting(E_ALL ^ E_NOTICE);
	// para aceptar la conexión desde cualquier origen
	header("Access-Control-Allow-Origin: *");

	// Permite los métodos GET, POST, PUT, DELETE
	header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");

	//obtenemos la data de la solicitud
	$bodyRequest = file_get_contents("php://input");

	// Decodificamos y lo guardamos en un array
    $data = json_decode($bodyRequest, true);
    // fichero existe
    if (!is_dir("../archivos_xml_sunat")) {
        mkdir("../archivos_xml_sunat", 0777, true);
        mkdir("../archivos_xml_sunat/cpe_xml", 0777, true);
        mkdir("../archivos_xml_sunat/cpe_xml/beta", 0777, true);
        mkdir("../archivos_xml_sunat/cpe_xml/produccion", 0777, true);
        mkdir("../archivos_xml_sunat/certificados", 0777, true);
        mkdir("../archivos_xml_sunat/certificados/beta", 0777, true);
        mkdir("../archivos_xml_sunat/certificados/produccion", 0777, true);
    }
    // copiar certificados
    if (isset($data["ruc"])) {
        // crear carpetas
        $estructura_beta = "../archivos_xml_sunat/cpe_xml/beta/" . $data["ruc"];
        if(!mkdir($estructura_beta, 0777, true)) {
            echo "Fallo al crear carpeta beta ... " . $data["ruc"] . "\n";
        } else {
            chmod($estructura_beta,0777);
            echo "Carpeta beta creada ... " . $data["ruc"] . "\n";
        }
        $estructura_prod = "../archivos_xml_sunat/cpe_xml/produccion/" . $data["ruc"];
        if(!mkdir($estructura_prod, 0777, true)) {
            echo "Fallo al crear carpeta prod ... " . $data["ruc"] . "\n";
        } else {
            chmod($estructura_prod,0777);
            echo "Carpeta prod copiada ... " . $data["ruc"] . "\n";
        }
        // copiar certificado pfx
        $fichero = "beta.pfx";
        $nuevo_fichero_beta = "../archivos_xml_sunat/certificados/beta/" . $data["ruc"] . ".pfx";
        $nuevo_fichero_prod = "../archivos_xml_sunat/certificados/produccion/" . $data["ruc"] . ".pfx";
        if (!copy($fichero, $nuevo_fichero_beta)) {
            echo "Error al copiar certificado beta ... " . $data["ruc"] . "\n";
        } else {
            chmod($nuevo_fichero_beta,0777);
            echo "Certificado beta copiado ... " . $data["ruc"] . "\n";
        }
        if (!copy($fichero, $nuevo_fichero_prod)) {
            echo "Error al copiar certificado prod ... " . $data["ruc"] . "\n";
        } else {
            chmod($nuevo_fichero_prod,0777);
            echo "Certificado prod copiado ... " . $data["ruc"] . "\n";
        }
    }
?>