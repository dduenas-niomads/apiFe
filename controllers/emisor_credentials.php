<?php

class EmisorCredentials {
    public function getClaveSol($processType = 1) {
        if ((int)$processType === 1) {
            # SUNAT
            return '20100392403De';
        } else {
            # SUNAT
            return '20100392403De';
        }
    }
    public function getUsuarioSol($processType = 1) {
        if ((int)$processType === 1) {
            # SUNAT
            return '20100392403';
        } else {
            # SUNAT
            return '20100392403';
        }
    }
    public function getPassFirma($processType = 1) {
        if ((int)$processType === 1) {
            # SUNAT
            return '20100392403';
        } else {
            # SUNAT
            return '20100392403';
        }
    }
    public function getRutaWS($processType = 1) {
        if ((int)$processType === 1) {
            // # THE FACTORY HKA
            // return 'https://ose.thefactoryhka.com.pe/ol-ti-itcpfegem/billService';
            # SUNAT
            return 'https://e-beta.sunat.gob.pe/ol-ti-itcpfegem-beta/billService';
        } else {
            # WEB SERVICE
            // return 'http://egestor.dev.efacturacion.pe/ws_tci/service.asmx?wsdl';
            # WCF
            // return 'https://egestor.dev.efacturacion.pe/ws_tci/service.svc?wsdl';
            return 'http://egestor.dev.efacturacion.pe/wcf_tci/service.svc?wsdl';
        }
    }
    public function getRutaWSRG($processType = 1) {
        if ((int)$processType === 1) {
            // # THE FACTORY HKA
            // return 'https://ose.thefactoryhka.com.pe/ol-ti-itcpfegem/billService';
            # SUNAT
            return 'https://e-beta.sunat.gob.pe/ol-ti-itemision-guia-gem-beta/billService';
        } else {
            // # THE FACTORY HKA
            // return 'https://demoose.thefactoryhka.com.pe/ol-ti-itcpfegem/billService';
            # SUNAT
            return 'https://e-guiaremision.sunat.gob.pe/ol-ti-itemision-guia-gem/billService';
        }
    }
}