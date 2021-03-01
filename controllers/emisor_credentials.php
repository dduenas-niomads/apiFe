<?php

class EmisorCredentials {
    public function getClaveSol($processType = 1) {
        if ((int)$processType === 1) {
            # SUNAT
            return 'sss';
        } else {
            # SUNAT
            return 'hamedulan';
        }
    }
    public function getUsuarioSol($processType = 1) {
        if ((int)$processType === 1) {
            # SUNAT
            return 'sss';
        } else {
            # SUNAT
            return 'RTICATON';
        }
    }
    public function getPassFirma($processType = 1) {
        if ((int)$processType === 1) {
            # SUNAT
            return 'sss';
        } else {
            # SUNAT
            return 'niomads2021';
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