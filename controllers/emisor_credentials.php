<?php

class EmisorCredentials {
    public function getClaveSol($processType = 1) {
        if ((int)$processType === 1) {
            // # THE FACTORY HKA
            // return 'Y/!-a$?-QKYc';
            # SUNAT
            return 'tumipos2020';
        } else {
            // # THE FACTORY HKA
            // return '20601446686';
            # SUNAT
            return 'tumipos2020';
        }
    }
    public function getUsuarioSol($processType = 1) {
        if ((int)$processType === 1) {
            // # THE FACTORY HKA
            // return '20601446686_INTOSE';
            # SUNAT
            return '20601446686TUMIPOS1';
        } else {
            // # THE FACTORY HKA
            // return '20601446686';
            # SUNAT
            return '20601446686TUMIPOS1';
        }
    }
    public function getPassFirma($processType = 1) {
        if ((int)$processType === 1) {
            // # THE FACTORY HKA
            // return 'Tumifactura2019';
            # SUNAT
            return 'Tumifactura2019';
        } else {
            // # THE FACTORY HKA
            // return 'Tumifactura2019';
            # SUNAT
            return 'Tumifactura2019';
        }
    }
    public function getRutaWS($processType = 1) {
        if ((int)$processType === 1) {
            // # THE FACTORY HKA
            // return 'https://ose.thefactoryhka.com.pe/ol-ti-itcpfegem/billService';
            # SUNAT
            return 'https://e-factura.sunat.gob.pe/ol-ti-itcpfegem/billService';
        } else {
            // # THE FACTORY HKA
            // return 'https://demoose.thefactoryhka.com.pe/ol-ti-itcpfegem/billService';
            # SUNAT
            return 'https://e-beta.sunat.gob.pe/ol-ti-itcpfegem-beta/billService';
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