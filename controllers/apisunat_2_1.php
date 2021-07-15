<?php
class Apisunat {

    public function crear_xml_factura($cabecera, $detalle, $bank, $ruta) {
        $validacion = new validaciondedatos();
        $doc = new DOMDocument();
        $doc->formatOutput = FALSE;
        $doc->preserveWhiteSpace = TRUE;
		$doc->encoding = 'utf-8';

        $xmlCPE = '<s:Envelope xmlns:s="http://schemas.xmlsoap.org/soap/envelope/">
        <s:Body>
            <Registrar xmlns="http://tempuri.org/">
                <oGeneral xmlns:a="http://schemas.datacontract.org/2004/07/Libreria.XML.Facturacion" xmlns:i="http://www.w3.org/2001/XMLSchema-instance">
                    <a:Autenticacion>
                        <a:Clave>'.$cabecera["EMISOR_PASS_SOL"].'</a:Clave>
                        <a:Ruc>'.$cabecera["EMISOR_RUC"].'</a:Ruc>
                    </a:Autenticacion>
                    <a:oENComprobante>
                        <a:CodigoCliente>'.$cabecera["NRO_DOCUMENTO_CLIENTE"].'</a:CodigoCliente>
                            <a:ComprobanteDetalle>';
                                for ($i = 0; $i < count($detalle); $i++) {
                                    $xmlCPE = $xmlCPE .'
                                <a:ENComprobanteDetalle>
                                    <a:Cantidad>'.$detalle[$i]["txtCANTIDAD_DET"].'</a:Cantidad>
                                    <a:CodigoProductoSunat>'.$detalle[$i]["txtCODIGO_DET"].'</a:CodigoProductoSunat>
                                    <a:CodigoTipoPrecio>'.$detalle[$i]["txtPRECIO_TIPO_CODIGO"].'</a:CodigoTipoPrecio>
                                    <a:ComprobanteDetalleImpuestos>
                                        <a:ENComprobanteDetalleImpuestos>
                                            <a:AfectacionIGV>'.$detalle[$i]["txtCOD_TIPO_OPERACION"].'</a:AfectacionIGV>
                                            <a:CodigoTributo>1000</a:CodigoTributo>
                                            <a:CodigoUN>VAT</a:CodigoUN>
                                            <a:DesTributo>IGV</a:DesTributo>
                                            <a:ImporteExplicito>'.$detalle[$i]["txtIGV"].'</a:ImporteExplicito>
                                            <a:ImporteTributo>'.$detalle[$i]["txtIGV"].'</a:ImporteTributo>
                                            <a:MontoBase>'.$detalle[$i]["txtIMPORTE_DET"].'</a:MontoBase>
                                            <a:TasaAplicada>18</a:TasaAplicada>
                                        </a:ENComprobanteDetalleImpuestos>
                                    </a:ComprobanteDetalleImpuestos>
                                    <a:Descripcion>'.$detalle[$i]["txtDESCRIPCION_DET"].'</a:Descripcion>
                                    <a:Determinante>'.$detalle[$i]["txtCANTIDAD_DET"].'</a:Determinante>
                                    <a:ImpuestoTotal>'.$detalle[$i]["txtIGV"].'</a:ImpuestoTotal>
                                    <a:Item>' . $detalle[$i]["txtITEM"] . '</a:Item>
                                    <a:PrecioVentaItem>'.$detalle[$i]["txtPRECIO_DET"].'</a:PrecioVentaItem>
                                    <a:Total>'.$detalle[$i]["txtSUB_TOTAL_DET"].'</a:Total>
                                    <a:UnidadComercial>'.$detalle[$i]["txtUNIDAD_MEDIDA_DET"].'</a:UnidadComercial>
                                    <a:ValorVentaUnitario>'.$detalle[$i]["txtIMPORTE_DET"].'</a:ValorVentaUnitario>
                                    <a:ValorVentaUnitarioIncIgv>'.$detalle[$i]["txtPRECIO_DET"].'</a:ValorVentaUnitarioIncIgv>
                                </a:ENComprobanteDetalle>';
                                }
                                $xmlCPE = $xmlCPE .'
                            </a:ComprobanteDetalle>
                        <a:ComprobanteGrillaCuenta>';
                            for ($i = 0; $i < count($bank); $i++) {
                                $xmlCPE = $xmlCPE .'
                            <a:ENComprobanteGrillaCuenta>
                                <a:Descripcion>'.$bank[$i]["txtDESCRIPCION_CUENTA"].'</a:Descripcion>
                                <a:Valor1>'.$bank[$i]["txtDESCRIPCION_MONEDA"].'</a:Valor1>
                                <a:Valor2>'.$bank[$i]["txtNRO_CUENTA"].'</a:Valor2>
                                <a:Valor3>'.$bank[$i]["txtCCI"].'</a:Valor3>
                            </a:ENComprobanteGrillaCuenta>';
                            }
                            $xmlCPE = $xmlCPE .'
                        </a:ComprobanteGrillaCuenta>
                        <a:ComprobantePropiedadesAdicionales>
                            <a:ENComprobantePropiedadesAdicionales>
                                <a:Codigo>1000</a:Codigo>
                                <a:Valor>'.$cabecera["TOTAL_LETRAS"].'</a:Valor>
                            </a:ENComprobantePropiedadesAdicionales>';
                        if (intval($cabecera["DETRACCION"]) > 0) {
                        $xmlCPE = $xmlCPE . '
                            <a:ENComprobantePropiedadesAdicionales>
                                <a:Codigo>2006</a:Codigo>
                                <a:Valor>OPERACION SUJETA A DETRACCION</a:Valor>
                            </a:ENComprobantePropiedadesAdicionales>
                        </a:ComprobantePropiedadesAdicionales>
                        <a:Detraccion>
                            <a:ENDetraccion>
                                <a:BienesServicios>
                                    <a:ENBienesServicios>
                                        <a:Codigo>'.$cabecera["CODIGO_BS_DETRACCION"].'</a:Codigo>
                                        <a:Valor>'.$cabecera["VALOR_BS_DETRACCION"].'</a:Valor>
                                    </a:ENBienesServicios>
                                </a:BienesServicios> 
                                <a:Monto>                                      
                                    <a:ENMonto>
                                        <a:Codigo>'.$cabecera["CODIGO_MONTO_DETRACCION"].'</a:Codigo>
                                        <a:Valor>'.$cabecera["MONTO_DETRACCION"].'</a:Valor>
                                    </a:ENMonto>
                                </a:Monto>  
                                <a:NumeroCuenta>
                                    <a:ENNumeroCuenta>
                                        <a:Codigo>3001</a:Codigo>
                                        <a:CodigoFormaPago>001</a:CodigoFormaPago>
                                        <a:Valor>'.$cabecera["NRO_CUENTA_BN"].'</a:Valor>
                                    </a:ENNumeroCuenta>
                                </a:NumeroCuenta> 
                                <a:Porcentaje>                      
                                    <a:ENPorcentaje>
                                        <a:Codigo>'.$cabecera["CODIGO_PORCENTAJE_DETRACCION"].'</a:Codigo>
                                        <a:Valor>'.$cabecera["VALOR_PORCENTAJE_DETRACCION"].'</a:Valor>
                                    </a:ENPorcentaje>
                                </a:Porcentaje>
                            </a:ENDetraccion> 
                        </a:Detraccion>';
                        }
                        else {
                            $xmlCPE = $xmlCPE . '
                        </a:ComprobantePropiedadesAdicionales>';
                        }
                        $xmlCPE = $xmlCPE . '                        
                        <a:FechaEmision>'.$cabecera["FECHA_DOCUMENTO"].'</a:FechaEmision>
                        <a:FormaPago>
                            <a:ENFormaPago>
                                <a:CodigoFormaPago>001</a:CodigoFormaPago>
                                <a:DiasVencimiento>'.$cabecera["DIAS_VENCIMIENTO"].'</a:DiasVencimiento>
                                <a:FechaVencimiento>'.$cabecera["FECHA_VTO"].'</a:FechaVencimiento>
                                <a:NotaInstruccion>'.$cabecera["INSTRUCCION"].'</a:NotaInstruccion>
                            </a:ENFormaPago>
                        </a:FormaPago>
                        <a:FormaPagoSunat>';
                        if ($cabecera["INSTRUCCION"]=="CREDITO") {
                            $xmlCPE = $xmlCPE . '
                            <a:CuotaPago>
                                <a:ENCuotaPago>
                                    <a:FechaPago>'.$cabecera["FECHA_PAGO_CUOTA"].'</a:FechaPago>
                                    <a:Monto>'.$cabecera["MONTO_PAGO_CUOTA"].'</a:Monto>
                                </a:ENCuotaPago>
                            </a:CuotaPago>
                            <a:MontoPendientePago>'.$cabecera["MONTO_PAGO_CUOTA"].'</a:MontoPendientePago>';
                        }
                        $xmlCPE = $xmlCPE . '
                            <a:TipoFormaPago>'.$cabecera["FORMA_PAGO_SUNAT"].'</a:TipoFormaPago>
                        </a:FormaPagoSunat>
                        <a:HoraEmision>'.$cabecera["HORA_DOCUMENTO"].'</a:HoraEmision>
                        <a:ImporteTotal>'.$cabecera["TOTAL"].'</a:ImporteTotal>
                        <a:Moneda>'.$cabecera["COD_MONEDA"].'</a:Moneda>
                        <a:MontosTotales>
                            <a:Gravado>
                                <a:GravadoIGV>
                                    <a:Base>'.$cabecera["SUB_TOTAL"].'</a:Base>
                                    <a:Porcentaje>'.$cabecera["POR_IGV"].'</a:Porcentaje>
                                    <a:ValorImpuesto>'.$cabecera["TOTAL_IGV"].'</a:ValorImpuesto>
                                </a:GravadoIGV>
                            <a:Total>'.$cabecera["SUB_TOTAL"].'</a:Total>
                            </a:Gravado>
                        </a:MontosTotales>  
                        <a:NrodePedido>'.$cabecera["NRO_GUIA_REMISION"].'</a:NrodePedido>                           
                        <a:Numero>'.$cabecera["NRO_COMPROBANTE"].'</a:Numero>               
                        <a:RazonSocial>'.$cabecera["RAZON_SOCIAL_CLIENTE"].'</a:RazonSocial>
                        <a:Receptor>
                            <a:ENReceptor>
                                <a:Calle>'.$cabecera["DIRECCION_CLIENTE"].'</a:Calle>
                                <a:Codigo>'.$cabecera["COD_UBIGEO_CLIENTE"].'</a:Codigo>
                                <a:CodPais>'.$cabecera["COD_PAIS_CLIENTE"].'</a:CodPais>
                                <a:Departamento>'.$cabecera["DEPARTAMENTO_CLIENTE"].'</a:Departamento>
                                <a:Distrito>'.$cabecera["DISTRITO_CLIENTE"].'</a:Distrito>
                                <a:Provincia>'.$cabecera["PROVINCIA_CLIENTE"].'</a:Provincia>
                            </a:ENReceptor>
                        </a:Receptor>';
                        if (intval($cabecera["RETENCION"]) > 0) {
                        $xmlCPE = $xmlCPE . '
                        <a:Retencion>
                            <a:Monto>'.$cabecera["MONTO_RETENCION"].'</a:Monto>
                            <a:MontoBase>'.$cabecera["MONTO_BASE"].'</a:MontoBase>
                            <a:Porcentaje>'.$cabecera["PORCENTAJE_RETENCION"].'</a:Porcentaje>
                        </a:Retencion>';
                        }
                        $xmlCPE = $xmlCPE . '
                        <a:Ruc>'.$cabecera["NRO_DOCUMENTO_CLIENTE"].'</a:Ruc>
                        <a:Serie>'.$cabecera["SERIE_COMPROBANTE"].'</a:Serie>
                        <a:Sucursal>
                            <a:ENSucursal>
                                <a:Direccion>'.$cabecera["DIRECCION_EMPRESA"].'</a:Direccion>
                                <a:Distrito>'.$cabecera["DISTRITO_EMPRESA"].'</a:Distrito>
                                <a:Provincia>'.$cabecera["PROVINCIA_EMPRESA"].'</a:Provincia>
                                <a:Departamento>'.$cabecera["DEPARTAMENTO_EMPRESA"].'</a:Departamento>
                            </a:ENSucursal>
                        </a:Sucursal>
                        <a:Texto>
                            <a:ENTexto>
                                <a:Texto1>'.$cabecera["TEXTO_ENCABEZADO"].'</a:Texto1>
                                <a:Texto10>'.$cabecera["TEXTO_TARIFA"].'</a:Texto10>               
                                <a:Texto11>'.$cabecera["TEXTO_DESCRIPCION"].'</a:Texto11>                     
                                <a:Texto12>'.$cabecera["TEXTO_12"].'</a:Texto12>                     
                                <a:Texto13>'.$cabecera["TEXTO_13"].'</a:Texto13>                     
                                <a:Texto14>'.$cabecera["TEXTO_14"].'</a:Texto14> 
                                <a:Texto2>'.$cabecera["TEXTO_REFERENCIA"].'</a:Texto2>                     
                                <a:Texto3>'.$cabecera["TEXTO_NRO_ORDEN"].'</a:Texto3>                     
                                <a:Texto4>'.$cabecera["TEXTO_DEL"].'</a:Texto4>                     
                                <a:Texto5>'.$cabecera["TEXTO_AL"].'</a:Texto5>                     
                                <a:Texto6>'.$cabecera["TEXTO_DIAS"].'</a:Texto6>                     
                                <a:Texto7>'.$cabecera["TEXTO_CANTIDAD"].'</a:Texto7>                     
                                <a:Texto8>'.$cabecera["TEXTO_BULTOS"].'</a:Texto8>                     
                                <a:Texto9>'.$cabecera["TEXTO_VALOR"].'</a:Texto9>                  
                            </a:ENTexto>
                        </a:Texto>
                        <a:TipoComprobante>'.$cabecera["COD_TIPO_DOCUMENTO"].'</a:TipoComprobante>
                        <a:TipoDocumentoIdentidad>'.$cabecera["TIPO_DOCUMENTO_CLIENTE"].'</a:TipoDocumentoIdentidad>
                        <a:TipoOperacion>'.$cabecera["TIPO_OPERACION"].'</a:TipoOperacion>
                        <a:TipoPlantilla>ST1</a:TipoPlantilla>
                        <a:TotalImpuesto>'.$cabecera["TOTAL_IGV"].'</a:TotalImpuesto>
                        <a:TotalPrecioVenta>'.$cabecera["TOTAL"].'</a:TotalPrecioVenta>
                        <a:TotalValorVenta>'.$cabecera["TOTAL_GRAVADAS"].'</a:TotalValorVenta>
                        <a:VersionUbl>2.1</a:VersionUbl>
                    </a:oENComprobante>
                    <a:oENEmpresa>
                        <a:Calle>'.$cabecera["DIRECCION_EMPRESA"].'</a:Calle>
                        <a:CodDistrito>'.$cabecera["CODIGO_UBIGEO_EMPRESA"].'</a:CodDistrito>
                        <a:CodPais>'.$cabecera["CODIGO_PAIS_EMPRESA"].'</a:CodPais>
                        <a:CodigoEstablecimientoSUNAT>0000</a:CodigoEstablecimientoSUNAT>
                        <a:CodigoTipoDocumento>'.$cabecera["TIPO_DOCUMENTO_EMPRESA"].'</a:CodigoTipoDocumento>
                        <a:Correo>'.$cabecera["CORREO_EMPRESA"].'</a:Correo>
                        <a:Departamento>'.$cabecera["DEPARTAMENTO_EMPRESA"].'</a:Departamento>
                        <a:Distrito>'.$cabecera["DISTRITO_EMPRESA"].'</a:Distrito>
                        <a:NombreComercial>'.$cabecera["NOMBRE_COMERCIAL_EMPRESA"].'</a:NombreComercial>
                        <a:Provincia>'.$cabecera["PROVINCIA_EMPRESA"].'</a:Provincia>
                        <a:RazonSocial>'.$cabecera["RAZON_SOCIAL_EMPRESA"].'</a:RazonSocial>
                        <a:Ruc>'.$cabecera["NRO_DOCUMENTO_EMPRESA"].'</a:Ruc>
                        <a:Telefono>'.$cabecera["TELEFONO_EMPRESA"].'</a:Telefono>
                        <a:Web>'.$cabecera["WEB_EMPRESA"].'</a:Web>
                    </a:oENEmpresa>
                </oGeneral>
                <oTipoComprobante>'.$cabecera["TIPO_DOCUMENTO"].'</oTipoComprobante>
                <TipoCodigo>0</TipoCodigo>
                <Otorgar>1</Otorgar>
            </Registrar>
        </s:Body>
        </s:Envelope>';
        $doc->loadXML($xmlCPE);
        $doc->save($ruta . '.xml');
        $resp['respuesta'] = 'ok';
        $resp['url_xml'] = $ruta . '.xml';
        return $resp;
    }

    public function crear_xml_nota_credito($cabecera, $detalle, $ruta) {
        $validacion = new validaciondedatos();
        $doc = new DOMDocument();
        $doc->formatOutput = FALSE;
        $doc->preserveWhiteSpace = TRUE;
        //$doc->encoding = 'ISO-8859-1';
        $doc->encoding = 'utf-8';
        
		$xmlCPE = '<s:Envelope xmlns:s="http://schemas.xmlsoap.org/soap/envelope/">
        <s:Body>
            <Registrar xmlns="http://tempuri.org/">
                <oGeneral xmlns:a="http://schemas.datacontract.org/2004/07/Libreria.XML.Facturacion" xmlns:i="http://www.w3.org/2001/XMLSchema-instance">
                    <a:Autenticacion>
                        <a:Clave>'.$cabecera["EMISOR_PASS_SOL"].'</a:Clave>
                        <a:Ruc>'.$cabecera["EMISOR_RUC"].'</a:Ruc>
                    </a:Autenticacion>
                    <a:oENComprobante>
                        <a:CodigoCliente>'.$cabecera["NRO_DOCUMENTO_CLIENTE"].'</a:CodigoCliente>
                        <a:ComprobanteDetalle>';
                        for ($i = 0; $i < count($detalle); $i++) {
                            $xmlCPE = $xmlCPE .'
                            <a:ENComprobanteDetalle>
                                <a:Cantidad>'.$detalle[$i]["txtCANTIDAD_DET"].'</a:Cantidad>
                                <a:CodigoProductoSunat>'.$detalle[$i]["txtCODIGO_DET"].'</a:CodigoProductoSunat>
                                <a:CodigoTipoPrecio>'.$detalle[$i]["txtPRECIO_TIPO_CODIGO"].'</a:CodigoTipoPrecio>
                                <a:ComprobanteDetalleImpuestos>
                                    <a:ENComprobanteDetalleImpuestos>
                                        <a:AfectacionIGV>'.$detalle[$i]["txtCOD_TIPO_OPERACION"].'</a:AfectacionIGV>
                                        <a:CodigoTributo>1000</a:CodigoTributo>
                                        <a:CodigoUN>VAT</a:CodigoUN>
                                        <a:DesTributo>IGV</a:DesTributo>
                                        <a:ImporteExplicito>'.$detalle[$i]["txtIGV"].'</a:ImporteExplicito>
                                        <a:ImporteTributo>'.$detalle[$i]["txtIGV"].'</a:ImporteTributo>
                                        <a:MontoBase>'.$detalle[$i]["txtIMPORTE_DET"].'</a:MontoBase>
                                        <a:TasaAplicada>18</a:TasaAplicada>
                                    </a:ENComprobanteDetalleImpuestos>
                                </a:ComprobanteDetalleImpuestos>
                                <a:Descripcion>'.$detalle[$i]["txtDESCRIPCION_DET"].'</a:Descripcion>
                                <a:Determinante>'.$detalle[$i]["txtCANTIDAD_DET"].'</a:Determinante>
                                <a:ImpuestoTotal>'.$detalle[$i]["txtIGV"].'</a:ImpuestoTotal>
                                <a:Item>' . $detalle[$i]["txtITEM"] . '</a:Item>
                                <a:PrecioVentaItem>'.$detalle[$i]["txtPRECIO_DET"].'</a:PrecioVentaItem>
                                <a:Total>'.$detalle[$i]["txtSUB_TOTAL_DET"].'</a:Total>
                                <a:UnidadComercial>'.$detalle[$i]["txtUNIDAD_MEDIDA_DET"].'</a:UnidadComercial>
                                <a:ValorVentaUnitario>'.$detalle[$i]["txtIMPORTE_DET"].'</a:ValorVentaUnitario>
                                <a:ValorVentaUnitarioIncIgv>'.$detalle[$i]["txtPRECIO_DET"].'</a:ValorVentaUnitarioIncIgv>
                            </a:ENComprobanteDetalle>';
                            }
                            $xmlCPE = $xmlCPE .'
                        </a:ComprobanteDetalle>
                        <a:ComprobanteGrillaCuenta>
                            <a:ENComprobanteGrillaCuenta>
                                <a:Descripcion>BCP SOLES</a:Descripcion>
                                <a:Valor1>SOLES</a:Valor1>
                                <a:Valor2>194-1333331-0-30</a:Valor2>
                                <a:Valor3>002-194-006666661030-93</a:Valor3>
                            </a:ENComprobanteGrillaCuenta>
                            <a:ENComprobanteGrillaCuenta>
                                <a:Descripcion>BCP DOLAR</a:Descripcion>
                                <a:Valor1>DÓLARES AMERICANOS</a:Valor1>
                                <a:Valor2>194-1444417-1-20</a:Valor2>
                                <a:Valor3>002-194-001643555555-95</a:Valor3>
                            </a:ENComprobanteGrillaCuenta>
                        </a:ComprobanteGrillaCuenta>
                    <a:ComprobanteMotivosDocumentos>
                       <a:ENComprobanteMotivoDocumento>
                          <a:CodigoMotivoEmision>'.$cabecera["COD_TIPO_MOTIVO"].'</a:CodigoMotivoEmision>
                          <a:NumeroDocRef>'.$cabecera["NRO_DOCUMENTO_MODIFICA"].'</a:NumeroDocRef>
                          <a:SerieDocRef>'.$cabecera["SERIE_DOCUMENTO_MODIFICA"].'</a:SerieDocRef>
                         <a:Sustentos>
                            <a:ENComprobanteMotivoDocumentoSustento>
                              <a:Sustento>'.$cabecera["DESCRIPCION_MOTIVO"].'</a:Sustento>
                            </a:ENComprobanteMotivoDocumentoSustento>
                         </a:Sustentos>
                      </a:ENComprobanteMotivoDocumento>                                         
                    </a:ComprobanteMotivosDocumentos>               
                    <a:ComprobanteNotaCreditoDocRef>
                       <a:ENComprobanteNotaDocRef>
                          <a:FechaDocRef>'.$cabecera["FECHA_COMPROBANTE_MODIFICA"].'</a:FechaDocRef>
                          <a:Numero>'.$cabecera["NRO_DOCUMENTO_MODIFICA"].'</a:Numero>
                          <a:Serie>'.$cabecera["SERIE_DOCUMENTO_MODIFICA"].'</a:Serie>
                          <a:TipoComprobante>'.$cabecera["TIPO_COMPROBANTE_MODIFICA"].'</a:TipoComprobante>
                       </a:ENComprobanteNotaDocRef>
                    </a:ComprobanteNotaCreditoDocRef>               
                        <a:ComprobantePropiedadesAdicionales>
                            <a:ENComprobantePropiedadesAdicionales>
                                <a:Codigo>1000</a:Codigo>
                                <a:Valor>'.$cabecera["TOTAL_LETRAS"].'</a:Valor>
                            </a:ENComprobantePropiedadesAdicionales>
                        </a:ComprobantePropiedadesAdicionales>
                        <a:FechaEmision>'.$cabecera["FECHA_DOCUMENTO"].'</a:FechaEmision>
                        <a:FormaPago>
                            <a:ENFormaPago>
                                <a:CodigoFormaPago>001</a:CodigoFormaPago>
                                <a:DiasVencimiento>30</a:DiasVencimiento>
                                <a:FechaVencimiento>'.$cabecera["FECHA_VTO"].'</a:FechaVencimiento>
                                <a:NotaInstruccion>Contado</a:NotaInstruccion>
                            </a:ENFormaPago>
                        </a:FormaPago>
                        <a:FormaPagoSunat>
                            <a:TipoFormaPago>1</a:TipoFormaPago>
                        </a:FormaPagoSunat>
                        <a:HoraEmision>'.$cabecera["HORA_DOCUMENTO"].'</a:HoraEmision>
                        <a:ImporteTotal>'.$cabecera["TOTAL"].'</a:ImporteTotal>
                        <a:Moneda>'.$cabecera["COD_MONEDA"].'</a:Moneda>
                        <a:MontosTotales>
                            <a:Gravado>
                                <a:GravadoIGV>
                                    <a:Base>'.$cabecera["SUB_TOTAL"].'</a:Base>
                                    <a:Porcentaje>'.$cabecera["POR_IGV"].'</a:Porcentaje>
                                    <a:ValorImpuesto>'.$cabecera["TOTAL_IGV"].'</a:ValorImpuesto>
                                </a:GravadoIGV>
                            <a:Total>'.$cabecera["SUB_TOTAL"].'</a:Total>
                            </a:Gravado>
                        </a:MontosTotales>  
                        <a:NrodePedido>'.$cabecera["NRO_GUIA_REMISION"].'</a:NrodePedido>                           
                        <a:Numero>'.$cabecera["NRO_COMPROBANTE"].'</a:Numero>               
                        <a:RazonSocial>'.$cabecera["RAZON_SOCIAL_CLIENTE"].'</a:RazonSocial>
                        <a:Receptor>
                            <a:ENReceptor>
                                <a:Calle>'.$cabecera["DIRECCION_CLIENTE"].'</a:Calle>
                                <a:Codigo>'.$cabecera["COD_UBIGEO_CLIENTE"].'</a:Codigo>
                                <a:CodPais>'.$cabecera["COD_PAIS_CLIENTE"].'</a:CodPais>
                                <a:Departamento>'.$cabecera["DEPARTAMENTO_CLIENTE"].'</a:Departamento>
                                <a:Distrito>'.$cabecera["DISTRITO_CLIENTE"].'</a:Distrito>
                                <a:Provincia>'.$cabecera["PROVINCIA_CLIENTE"].'</a:Provincia>
                            </a:ENReceptor>
                        </a:Receptor>
                        <a:Ruc>'.$cabecera["NRO_DOCUMENTO_EMPRESA"].'</a:Ruc>
                        <a:Serie>'.$cabecera["SERIE_COMPROBANTE"].'</a:Serie>
                        <a:Sucursal>
                            <a:ENSucursal>
                                <a:Direccion>'.$cabecera["DIRECCION_EMPRESA"].'</a:Direccion>
                                <a:Distrito>'.$cabecera["DISTRITO_EMPRESA"].'</a:Distrito>
                                <a:Provincia>'.$cabecera["PROVINCIA_EMPRESA"].'</a:Provincia>
                                <a:Departamento>'.$cabecera["DEPARTAMENTO_EMPRESA"].'</a:Departamento>
                            </a:ENSucursal>
                        </a:Sucursal>
                        <a:Texto>
                            <a:ENTexto>
                                <a:Texto1>'.$cabecera["TEXTO_ENCABEZADO"].'</a:Texto1>
                                <a:Texto10>'.$cabecera["TEXTO_TARIFA"].'</a:Texto10>               
                                <a:Texto11>'.$cabecera["TEXTO_DESCRIPCION"].'</a:Texto11>                     
                                <a:Texto12>'.$cabecera["TEXTO_12"].'</a:Texto12>                     
                                <a:Texto13>'.$cabecera["TEXTO_13"].'</a:Texto13>                     
                                <a:Texto14>'.$cabecera["TEXTO_14"].'</a:Texto14> 
                                <a:Texto2>'.$cabecera["TEXTO_REFERENCIA"].'</a:Texto2>                     
                                <a:Texto3>'.$cabecera["TEXTO_NRO_ORDEN"].'</a:Texto3>                     
                                <a:Texto4>'.$cabecera["TEXTO_DEL"].'</a:Texto4>                     
                                <a:Texto5>'.$cabecera["TEXTO_AL"].'</a:Texto5>                     
                                <a:Texto6>'.$cabecera["TEXTO_DIAS"].'</a:Texto6>                     
                                <a:Texto7>'.$cabecera["TEXTO_CANTIDAD"].'</a:Texto7>                     
                                <a:Texto8>'.$cabecera["TEXTO_BULTOS"].'</a:Texto8>                     
                                <a:Texto9>'.$cabecera["TEXTO_VALOR"].'</a:Texto9>                  
                            </a:ENTexto>
                        </a:Texto>
                        <a:TipoComprobante>'.$cabecera["COD_TIPO_DOCUMENTO"].'</a:TipoComprobante>
                        <a:TipoDocumentoIdentidad>'.$cabecera["TIPO_DOCUMENTO_CLIENTE"].'</a:TipoDocumentoIdentidad>
                        <a:TipoOperacion>'.$cabecera["TIPO_OPERACION"].'</a:TipoOperacion>
                        <a:TipoPlantilla>ST1</a:TipoPlantilla>
                        <a:TotalImpuesto>'.$cabecera["TOTAL_IGV"].'</a:TotalImpuesto>
                        <a:TotalPrecioVenta>'.$cabecera["TOTAL"].'</a:TotalPrecioVenta>
                        <a:TotalValorVenta>'.$cabecera["TOTAL_GRAVADAS"].'</a:TotalValorVenta>
                        <a:VersionUbl>2.1</a:VersionUbl>
                    </a:oENComprobante>
                    <a:oENEmpresa>
                        <a:Calle>'.$cabecera["DIRECCION_EMPRESA"].'</a:Calle>
                        <a:CodDistrito>'.$cabecera["CODIGO_UBIGEO_EMPRESA"].'</a:CodDistrito>
                        <a:CodPais>'.$cabecera["CODIGO_PAIS_EMPRESA"].'</a:CodPais>
                        <a:CodigoEstablecimientoSUNAT>0000</a:CodigoEstablecimientoSUNAT>
                        <a:CodigoTipoDocumento>'.$cabecera["TIPO_DOCUMENTO_EMPRESA"].'</a:CodigoTipoDocumento>
                        <a:Correo>'.$cabecera["CORREO_EMPRESA"].'</a:Correo>
                        <a:Departamento>'.$cabecera["DEPARTAMENTO_EMPRESA"].'</a:Departamento>
                        <a:Distrito>'.$cabecera["DISTRITO_EMPRESA"].'</a:Distrito>
                        <a:NombreComercial>'.$cabecera["NOMBRE_COMERCIAL_EMPRESA"].'</a:NombreComercial>
                        <a:Provincia>'.$cabecera["PROVINCIA_EMPRESA"].'</a:Provincia>
                        <a:RazonSocial>'.$cabecera["RAZON_SOCIAL_EMPRESA"].'</a:RazonSocial>
                        <a:Ruc>'.$cabecera["NRO_DOCUMENTO_EMPRESA"].'</a:Ruc>
                        <a:Telefono>'.$cabecera["TELEFONO_EMPRESA"].'</a:Telefono>
                        <a:Web>'.$cabecera["WEB_EMPRESA"].'</a:Web>
                    </a:oENEmpresa>
                </oGeneral>
                <oTipoComprobante>NotaCredito</oTipoComprobante>
                <TipoCodigo>0</TipoCodigo>
                <Otorgar>1</Otorgar>
           </Registrar>
        </s:Body>
     </s:Envelope>';
        $doc->loadXML($xmlCPE);
        $doc->save($ruta . '.xml');
        $resp['respuesta'] = 'ok';
        $resp['url_xml'] = $ruta . '.xml';
        return $resp;
    }

    public function crear_xml_nota_debito($cabecera, $detalle, $ruta) {
        $validacion = new validaciondedatos();
        $doc = new DOMDocument();
        $doc->formatOutput = FALSE;
        $doc->preserveWhiteSpace = TRUE;
        //$doc->encoding = 'ISO-8859-1';
		$doc->encoding = 'utf-8';
		$xmlCPE = '<s:Envelope xmlns:s="http://schemas.xmlsoap.org/soap/envelope/">
        <s:Body>
            <Registrar xmlns="http://tempuri.org/">
                <oGeneral xmlns:a="http://schemas.datacontract.org/2004/07/Libreria.XML.Facturacion" xmlns:i="http://www.w3.org/2001/XMLSchema-instance">
                    <a:Autenticacion>
                        <a:Clave>'.$cabecera["EMISOR_PASS_SOL"].'</a:Clave>
                        <a:Ruc>'.$cabecera["EMISOR_RUC"].'</a:Ruc>
                    </a:Autenticacion>
                    <a:oENComprobante>
                        <a:CodigoCliente>'.$cabecera["NRO_DOCUMENTO_CLIENTE"].'</a:CodigoCliente>
                        <a:ComprobanteDetalle>';
                        for ($i = 0; $i < count($detalle); $i++) {
                            $xmlCPE = $xmlCPE .'
                            <a:ENComprobanteDetalle>
                                <a:Cantidad>'.$detalle[$i]["txtCANTIDAD_DET"].'</a:Cantidad>
                                <a:CodigoProductoSunat>'.$detalle[$i]["txtCODIGO_DET"].'</a:CodigoProductoSunat>
                                <a:CodigoTipoPrecio>'.$detalle[$i]["txtPRECIO_TIPO_CODIGO"].'</a:CodigoTipoPrecio>
                                <a:ComprobanteDetalleImpuestos>
                                    <a:ENComprobanteDetalleImpuestos>
                                        <a:AfectacionIGV>'.$detalle[$i]["txtCOD_TIPO_OPERACION"].'</a:AfectacionIGV>
                                        <a:CodigoTributo>1000</a:CodigoTributo>
                                        <a:CodigoUN>VAT</a:CodigoUN>
                                        <a:DesTributo>IGV</a:DesTributo>
                                        <a:ImporteExplicito>'.$detalle[$i]["txtIGV"].'</a:ImporteExplicito>
                                        <a:ImporteTributo>'.$detalle[$i]["txtIGV"].'</a:ImporteTributo>
                                        <a:MontoBase>'.$detalle[$i]["txtIMPORTE_DET"].'</a:MontoBase>
                                        <a:TasaAplicada>18</a:TasaAplicada>
                                    </a:ENComprobanteDetalleImpuestos>
                                </a:ComprobanteDetalleImpuestos>
                                <a:Descripcion>'.$detalle[$i]["txtDESCRIPCION_DET"].'</a:Descripcion>
                                <a:Determinante>'.$detalle[$i]["txtCANTIDAD_DET"].'</a:Determinante>
                                <a:ImpuestoTotal>'.$detalle[$i]["txtIGV"].'</a:ImpuestoTotal>
                                <a:Item>' . $detalle[$i]["txtITEM"] . '</a:Item>
                                <a:PrecioVentaItem>'.$detalle[$i]["txtPRECIO_DET"].'</a:PrecioVentaItem>
                                <a:Total>'.$detalle[$i]["txtSUB_TOTAL_DET"].'</a:Total>
                                <a:UnidadComercial>'.$detalle[$i]["txtUNIDAD_MEDIDA_DET"].'</a:UnidadComercial>
                                <a:ValorVentaUnitario>'.$detalle[$i]["txtIMPORTE_DET"].'</a:ValorVentaUnitario>
                                <a:ValorVentaUnitarioIncIgv>'.$detalle[$i]["txtPRECIO_DET"].'</a:ValorVentaUnitarioIncIgv>
                            </a:ENComprobanteDetalle>';
                            }
                            $xmlCPE = $xmlCPE .'
                        </a:ComprobanteDetalle>
                        <a:ComprobanteGrillaCuenta>
                            <a:ENComprobanteGrillaCuenta>
                                <a:Descripcion>BCP SOLES</a:Descripcion>
                                <a:Valor1>SOLES</a:Valor1>
                                <a:Valor2>194-1333331-0-30</a:Valor2>
                                <a:Valor3>002-194-006666661030-93</a:Valor3>
                            </a:ENComprobanteGrillaCuenta>
                            <a:ENComprobanteGrillaCuenta>
                                <a:Descripcion>BCP DOLAR</a:Descripcion>
                                <a:Valor1>DÓLARES AMERICANOS</a:Valor1>
                                <a:Valor2>194-1444417-1-20</a:Valor2>
                                <a:Valor3>002-194-001643555555-95</a:Valor3>
                            </a:ENComprobanteGrillaCuenta>
                        </a:ComprobanteGrillaCuenta>
                    <a:ComprobanteMotivosDocumentos>
                       <a:ENComprobanteMotivoDocumento>
                          <a:CodigoMotivoEmision>'.$cabecera["COD_TIPO_MOTIVO"].'</a:CodigoMotivoEmision>
                          <a:NumeroDocRef>'.$cabecera["NRO_DOCUMENTO_MODIFICA"].'</a:NumeroDocRef>
                          <a:SerieDocRef>'.$cabecera["SERIE_DOCUMENTO_MODIFICA"].'</a:SerieDocRef>
                         <a:Sustentos>
                            <a:ENComprobanteMotivoDocumentoSustento>
                              <a:Sustento>'.$cabecera["DESCRIPCION_MOTIVO"].'</a:Sustento>
                            </a:ENComprobanteMotivoDocumentoSustento>
                         </a:Sustentos>
                      </a:ENComprobanteMotivoDocumento>                                         
                    </a:ComprobanteMotivosDocumentos>               
                    <a:ComprobanteNotaCreditoDocRef>
                       <a:ENComprobanteNotaDocRef>
                          <a:FechaDocRef>'.$cabecera["FECHA_COMPROBANTE_MODIFICA"].'</a:FechaDocRef>
                          <a:Numero>'.$cabecera["NRO_DOCUMENTO_MODIFICA"].'</a:Numero>
                          <a:Serie>'.$cabecera["SERIE_DOCUMENTO_MODIFICA"].'</a:Serie>
                          <a:TipoComprobante>'.$cabecera["TIPO_COMPROBANTE_MODIFICA"].'</a:TipoComprobante>
                       </a:ENComprobanteNotaDocRef>
                    </a:ComprobanteNotaCreditoDocRef>               
                        <a:ComprobantePropiedadesAdicionales>
                            <a:ENComprobantePropiedadesAdicionales>
                                <a:Codigo>1000</a:Codigo>
                                <a:Valor>'.$cabecera["TOTAL_LETRAS"].'</a:Valor>
                            </a:ENComprobantePropiedadesAdicionales>
                        </a:ComprobantePropiedadesAdicionales>
                        <a:FechaEmision>'.$cabecera["FECHA_DOCUMENTO"].'</a:FechaEmision>
                        <a:FormaPago>
                            <a:ENFormaPago>
                                <a:CodigoFormaPago>001</a:CodigoFormaPago>
                                <a:DiasVencimiento>30</a:DiasVencimiento>
                                <a:FechaVencimiento>'.$cabecera["FECHA_VTO"].'</a:FechaVencimiento>
                                <a:NotaInstruccion>Contado</a:NotaInstruccion>
                            </a:ENFormaPago>
                        </a:FormaPago>
                        <a:FormaPagoSunat>
                            <a:TipoFormaPago>1</a:TipoFormaPago>
                        </a:FormaPagoSunat>
                        <a:HoraEmision>'.$cabecera["HORA_DOCUMENTO"].'</a:HoraEmision>
                        <a:ImporteTotal>'.$cabecera["TOTAL"].'</a:ImporteTotal>
                        <a:Moneda>'.$cabecera["COD_MONEDA"].'</a:Moneda>
                        <a:MontosTotales>
                            <a:Gravado>
                                <a:GravadoIGV>
                                    <a:Base>'.$cabecera["SUB_TOTAL"].'</a:Base>
                                    <a:Porcentaje>'.$cabecera["POR_IGV"].'</a:Porcentaje>
                                    <a:ValorImpuesto>'.$cabecera["TOTAL_IGV"].'</a:ValorImpuesto>
                                </a:GravadoIGV>
                            <a:Total>'.$cabecera["SUB_TOTAL"].'</a:Total>
                            </a:Gravado>
                        </a:MontosTotales>  
                        <a:NrodePedido>'.$cabecera["NRO_GUIA_REMISION"].'</a:NrodePedido>                           
                        <a:Numero>'.$cabecera["NRO_COMPROBANTE"].'</a:Numero>               
                        <a:RazonSocial>'.$cabecera["RAZON_SOCIAL_CLIENTE"].'</a:RazonSocial>
                        <a:Receptor>
                            <a:ENReceptor>
                                <a:Calle>'.$cabecera["DIRECCION_CLIENTE"].'</a:Calle>
                                <a:Codigo>'.$cabecera["COD_UBIGEO_CLIENTE"].'</a:Codigo>
                                <a:CodPais>'.$cabecera["COD_PAIS_CLIENTE"].'</a:CodPais>
                                <a:Departamento>'.$cabecera["DEPARTAMENTO_CLIENTE"].'</a:Departamento>
                                <a:Distrito>'.$cabecera["DISTRITO_CLIENTE"].'</a:Distrito>
                                <a:Provincia>'.$cabecera["PROVINCIA_CLIENTE"].'</a:Provincia>
                            </a:ENReceptor>
                        </a:Receptor>
                        <a:Ruc>'.$cabecera["NRO_DOCUMENTO_EMPRESA"].'</a:Ruc>
                        <a:Serie>'.$cabecera["SERIE_COMPROBANTE"].'</a:Serie>
                        <a:Sucursal>
                            <a:ENSucursal>
                                <a:Direccion>'.$cabecera["DIRECCION_EMPRESA"].'</a:Direccion>
                                <a:Distrito>'.$cabecera["DISTRITO_EMPRESA"].'</a:Distrito>
                                <a:Provincia>'.$cabecera["PROVINCIA_EMPRESA"].'</a:Provincia>
                                <a:Departamento>'.$cabecera["DEPARTAMENTO_EMPRESA"].'</a:Departamento>
                            </a:ENSucursal>
                        </a:Sucursal>
                        <a:Texto>
                            <a:ENTexto>
                                <a:Texto1>'.$cabecera["TEXTO_ENCABEZADO"].'</a:Texto1>
                                <a:Texto10>'.$cabecera["TEXTO_TARIFA"].'</a:Texto10>               
                                <a:Texto11>'.$cabecera["TEXTO_DESCRIPCION"].'</a:Texto11>                     
                                <a:Texto12>'.$cabecera["TEXTO_12"].'</a:Texto12>                     
                                <a:Texto13>'.$cabecera["TEXTO_13"].'</a:Texto13>                     
                                <a:Texto14>'.$cabecera["TEXTO_14"].'</a:Texto14> 
                                <a:Texto2>'.$cabecera["TEXTO_REFERENCIA"].'</a:Texto2>                     
                                <a:Texto3>'.$cabecera["TEXTO_NRO_ORDEN"].'</a:Texto3>                     
                                <a:Texto4>'.$cabecera["TEXTO_DEL"].'</a:Texto4>                     
                                <a:Texto5>'.$cabecera["TEXTO_AL"].'</a:Texto5>                     
                                <a:Texto6>'.$cabecera["TEXTO_DIAS"].'</a:Texto6>                     
                                <a:Texto7>'.$cabecera["TEXTO_CANTIDAD"].'</a:Texto7>                     
                                <a:Texto8>'.$cabecera["TEXTO_BULTOS"].'</a:Texto8>                     
                                <a:Texto9>'.$cabecera["TEXTO_VALOR"].'</a:Texto9>                  
                            </a:ENTexto>
                        </a:Texto>
                        <a:TipoComprobante>'.$cabecera["COD_TIPO_DOCUMENTO"].'</a:TipoComprobante>
                        <a:TipoDocumentoIdentidad>'.$cabecera["TIPO_DOCUMENTO_CLIENTE"].'</a:TipoDocumentoIdentidad>
                        <a:TipoOperacion>'.$cabecera["TIPO_OPERACION"].'</a:TipoOperacion>
                        <a:TipoPlantilla>ST1</a:TipoPlantilla>
                        <a:TotalImpuesto>'.$cabecera["TOTAL_IGV"].'</a:TotalImpuesto>
                        <a:TotalPrecioVenta>'.$cabecera["TOTAL"].'</a:TotalPrecioVenta>
                        <a:TotalValorVenta>'.$cabecera["TOTAL_GRAVADAS"].'</a:TotalValorVenta>
                        <a:VersionUbl>2.1</a:VersionUbl>
                    </a:oENComprobante>
                    <a:oENEmpresa>
                        <a:Calle>'.$cabecera["DIRECCION_EMPRESA"].'</a:Calle>
                        <a:CodDistrito>'.$cabecera["CODIGO_UBIGEO_EMPRESA"].'</a:CodDistrito>
                        <a:CodPais>'.$cabecera["CODIGO_PAIS_EMPRESA"].'</a:CodPais>
                        <a:CodigoEstablecimientoSUNAT>0000</a:CodigoEstablecimientoSUNAT>
                        <a:CodigoTipoDocumento>'.$cabecera["TIPO_DOCUMENTO_EMPRESA"].'</a:CodigoTipoDocumento>
                        <a:Correo>'.$cabecera["CORREO_EMPRESA"].'</a:Correo>
                        <a:Departamento>'.$cabecera["DEPARTAMENTO_EMPRESA"].'</a:Departamento>
                        <a:Distrito>'.$cabecera["DISTRITO_EMPRESA"].'</a:Distrito>
                        <a:NombreComercial>'.$cabecera["NOMBRE_COMERCIAL_EMPRESA"].'</a:NombreComercial>
                        <a:Provincia>'.$cabecera["PROVINCIA_EMPRESA"].'</a:Provincia>
                        <a:RazonSocial>'.$cabecera["RAZON_SOCIAL_EMPRESA"].'</a:RazonSocial>
                        <a:Ruc>'.$cabecera["NRO_DOCUMENTO_EMPRESA"].'</a:Ruc>
                        <a:Telefono>'.$cabecera["TELEFONO_EMPRESA"].'</a:Telefono>
                        <a:Web>'.$cabecera["WEB_EMPRESA"].'</a:Web>
                    </a:oENEmpresa>
                </oGeneral>
                <oTipoComprobante>NotaDebito</oTipoComprobante>
                <TipoCodigo>0</TipoCodigo>
                <Otorgar>1</Otorgar>
           </Registrar>
        </s:Body>
     </s:Envelope>';
        $doc->loadXML($xmlCPE);
        $doc->save($ruta . '.xml');
        $resp['respuesta'] = 'ok';
        $resp['url_xml'] = $ruta . '.xml';
        return $resp;
    }

    public function crear_xml_resumen_documentos($cabecera, $detalle, $ruta){
        $validacion = new validaciondedatos();
        $doc = new DOMDocument();
        $doc->formatOutput = FALSE;
        $doc->preserveWhiteSpace = TRUE;
        $doc->encoding = 'ISO-8859-1';
        $xmlCPE = '<?xml version="1.0" encoding="iso-8859-1" standalone="no"?>
            <SummaryDocuments 
                xmlns="urn:sunat:names:specification:ubl:peru:schema:xsd:SummaryDocuments-1" 
                xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" 
                xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2" 
                xmlns:ds="http://www.w3.org/2000/09/xmldsig#" 
                xmlns:ext="urn:oasis:names:specification:ubl:schema:xsd:CommonExtensionComponents-2" 
                xmlns:sac="urn:sunat:names:specification:ubl:peru:schema:xsd:SunatAggregateComponents-1"
                xmlns:qdt="urn:oasis:names:specification:ubl:schema:xsd:QualifiedDatatypes-2" 
                xmlns:udt="urn:un:unece:uncefact:data:specification:UnqualifiedDataTypesSchemaModule:2">
            <ext:UBLExtensions>
                <ext:UBLExtension>
                    <ext:ExtensionContent>
                    </ext:ExtensionContent>
                </ext:UBLExtension>
            </ext:UBLExtensions>
            <cbc:UBLVersionID>2.0</cbc:UBLVersionID>
            <cbc:CustomizationID>1.1</cbc:CustomizationID>
            <cbc:ID>'.$cabecera["CODIGO"].'-'.$cabecera["SERIE"].'-'.$cabecera["SECUENCIA"].'</cbc:ID>
            <cbc:ReferenceDate>'.$cabecera["FECHA_REFERENCIA"].'</cbc:ReferenceDate>
            <cbc:IssueDate>'.$cabecera["FECHA_DOCUMENTO"].'</cbc:IssueDate>
            <cac:Signature>
                <cbc:ID>'.$cabecera["CODIGO"] . '-' . $cabecera["SERIE"] . '-' . $cabecera["SECUENCIA"] . '</cbc:ID>
                <cac:SignatoryParty>
                    <cac:PartyIdentification>
                        <cbc:ID>' . $cabecera["NRO_DOCUMENTO_EMPRESA"] . '</cbc:ID>
                    </cac:PartyIdentification>
                    <cac:PartyName>
                        <cbc:Name>' . $cabecera["RAZON_SOCIAL_EMPRESA"] . '</cbc:Name>
                    </cac:PartyName>
                </cac:SignatoryParty>
                <cac:DigitalSignatureAttachment>
                    <cac:ExternalReference>
                        <cbc:URI>'.$cabecera["CODIGO"] . '-' . $cabecera["SERIE"] . '-' . $cabecera["SECUENCIA"] . '</cbc:URI>
                    </cac:ExternalReference>
                </cac:DigitalSignatureAttachment>
            </cac:Signature>
            <cac:AccountingSupplierParty>
                <cbc:CustomerAssignedAccountID>' . $cabecera["NRO_DOCUMENTO_EMPRESA"] . '</cbc:CustomerAssignedAccountID>
                <cbc:AdditionalAccountID>' . $cabecera["TIPO_DOCUMENTO_EMPRESA"] . '</cbc:AdditionalAccountID>
                <cac:Party>
                    <cac:PartyLegalEntity>
                        <cbc:RegistrationName>' . $cabecera["RAZON_SOCIAL_EMPRESA"] . '</cbc:RegistrationName>
                    </cac:PartyLegalEntity>
                </cac:Party>
            </cac:AccountingSupplierParty>';
        $j = 1;
        for ($i = 0; $i < count($detalle); $i++) {
            $xmlCPE = $xmlCPE . '<sac:SummaryDocumentsLine>
            <cbc:LineID>' . (string)$j . '</cbc:LineID>
            <cbc:DocumentTypeCode>' . $detalle[$i]["TIPO_COMPROBANTE"] . '</cbc:DocumentTypeCode>
            <cbc:ID>' . $detalle[$i]["NRO_COMPROBANTE"] . '</cbc:ID>
            <cac:AccountingCustomerParty>
                <cbc:CustomerAssignedAccountID>' . $detalle[$i]["NRO_DOCUMENTO"] . '</cbc:CustomerAssignedAccountID>
                <cbc:AdditionalAccountID>' . $detalle[$i]["TIPO_DOCUMENTO"] . '</cbc:AdditionalAccountID>
            </cac:AccountingCustomerParty>';
            if ($detalle[$i]["TIPO_COMPROBANTE"]=="07"||$detalle[$i]["TIPO_COMPROBANTE"]=="08") {
                $xmlCPE = $xmlCPE . '<cac:BillingReference>
                    <cac:InvoiceDocumentReference>
                        <cbc:ID>' . $detalle[$i]["NRO_COMPROBANTE_REF"] . '</cbc:ID>
                        <cbc:DocumentTypeCode>' . $detalle[$i]["TIPO_COMPROBANTE_REF"] . '</cbc:DocumentTypeCode>
                    </cac:InvoiceDocumentReference>
                </cac:BillingReference>';
            }
            $xmlCPE = $xmlCPE . '<cac:Status>
                <cbc:ConditionCode>' . (string)$detalle[$i]["STATUS"] . '</cbc:ConditionCode>
                </cac:Status>                
                <sac:TotalAmount currencyID="' . $detalle[$i]["COD_MONEDA"] . '">' . $detalle[$i]["TOTAL"] . '</sac:TotalAmount>
                <sac:BillingPayment>
                    <cbc:PaidAmount currencyID="' . $detalle[$i]["COD_MONEDA"] . '">' . $detalle[$i]["GRAVADA"] . '</cbc:PaidAmount>
                    <cbc:InstructionID>01</cbc:InstructionID>
                </sac:BillingPayment>';
            if (intval($detalle[$i]["EXONERADO"]) > 0) {
                $xmlCPE = $xmlCPE . '<sac:BillingPayment>
                    <cbc:PaidAmount currencyID="' . $detalle[$i]["COD_MONEDA"] . '">' . $detalle[$i]["EXONERADO"] . '</cbc:PaidAmount>
                    <cbc:InstructionID>02</cbc:InstructionID>
                </sac:BillingPayment>';
            }                    
            if (intval($detalle[$i]["INAFECTO"]) > 0) {
                $xmlCPE = $xmlCPE . '<sac:BillingPayment>
                    <cbc:PaidAmount currencyID="' . $detalle[$i]["COD_MONEDA"] . '">' . $detalle[$i]["INAFECTO"] . '</cbc:PaidAmount>
                    <cbc:InstructionID>03</cbc:InstructionID>
                </sac:BillingPayment>';
            }                    
            if (intval($detalle[$i]["EXPORTACION"]) > 0) {
                $xmlCPE = $xmlCPE . '<sac:BillingPayment>
                    <cbc:PaidAmount currencyID="' . $detalle[$i]["COD_MONEDA"] . '">' . $detalle[$i]["EXPORTACION"] . '</cbc:PaidAmount>
                    <cbc:InstructionID>04</cbc:InstructionID>
                </sac:BillingPayment>';
            }                    
            if (intval($detalle[$i]["GRATUITAS"]) > 0) {
                $xmlCPE = $xmlCPE . '<sac:BillingPayment>
                    <cbc:PaidAmount currencyID="' . $detalle[$i]["COD_MONEDA"] . '">' . $detalle[$i]["GRATUITAS"] . '</cbc:PaidAmount>
                    <cbc:InstructionID>05</cbc:InstructionID>
                </sac:BillingPayment>';
            }                    
            if (intval($detalle[$i]["MONTO_CARGO_X_ASIG"]) > 0) {
                $xmlCPE = $xmlCPE . '<cac:AllowanceCharge>';
                if ($detalle[$i]["CARGO_X_ASIGNACION"] == 1) {
                    $xmlCPE = $xmlCPE . '<cbc:ChargeIndicator>true</cbc:ChargeIndicator>';
                }else{
                    $xmlCPE = $xmlCPE . '<cbc:ChargeIndicator>false</cbc:ChargeIndicator>';
                }
                $xmlCPE = $xmlCPE . '<cbc:Amount currencyID="' . $detalle[$i]["COD_MONEDA"] . '">' . $detalle[$i]["MONTO_CARGO_X_ASIG"] . '</cbc:Amount>
                </cac:AllowanceCharge>';
            }
            if (intval($detalle[$i]["ISC"]) > 0) {
                $xmlCPE = $xmlCPE . '<cac:TaxTotal>
                    <cbc:TaxAmount currencyID="' . $detalle[$i]["COD_MONEDA"] . '">' . $detalle[$i]["ISC"] . '</cbc:TaxAmount>
                        <cac:TaxSubtotal>
                            <cbc:TaxAmount currencyID="' . $detalle[$i]["COD_MONEDA"] . '">' . $detalle[$i]["ISC"] . '</cbc:TaxAmount>
                            <cac:TaxCategory>
                                <cac:TaxScheme>
                                    <cbc:ID>2000</cbc:ID>
                                    <cbc:Name>ISC</cbc:Name>
                                    <cbc:TaxTypeCode>EXC</cbc:TaxTypeCode>
                                </cac:TaxScheme>
                            </cac:TaxCategory>
                        </cac:TaxSubtotal>
                    </cac:TaxTotal>';
            }
            $xmlCPE = $xmlCPE . '<cac:TaxTotal>
                <cbc:TaxAmount currencyID="' . $detalle[$i]["COD_MONEDA"] . '">' . $detalle[$i]["IGV"] . '</cbc:TaxAmount>
                    <cac:TaxSubtotal>
                        <cbc:TaxAmount currencyID="' . $detalle[$i]["COD_MONEDA"] . '">' . $detalle[$i]["IGV"] . '</cbc:TaxAmount>
                        <cac:TaxCategory>
                            <cac:TaxScheme>
                                <cbc:ID>1000</cbc:ID>
                                <cbc:Name>IGV</cbc:Name>
                                <cbc:TaxTypeCode>VAT</cbc:TaxTypeCode>
                            </cac:TaxScheme>
                        </cac:TaxCategory>
                    </cac:TaxSubtotal>
                </cac:TaxTotal>';                    
            if (intval($detalle[$i]["OTROS"]) > 0) {
                $xmlCPE = $xmlCPE . '<cac:TaxTotal>
                    <cbc:TaxAmount currencyID="' . $detalle[$i]["COD_MONEDA"] . '">' . $detalle[$i]["OTROS"] . '</cbc:TaxAmount>
                        <cac:TaxSubtotal>
                            <cbc:TaxAmount currencyID="' . $detalle[$i]["COD_MONEDA"] . '">' . $detalle[$i]["OTROS"] . '</cbc:TaxAmount>
                            <cac:TaxCategory>
                                <cac:TaxScheme>
                                    <cbc:ID>9999</cbc:ID>
                                    <cbc:Name>OTROS</cbc:Name>
                                    <cbc:TaxTypeCode>OTH</cbc:TaxTypeCode>
                                </cac:TaxScheme>
                            </cac:TaxCategory>
                        </cac:TaxSubtotal>
                    </cac:TaxTotal>';
            }
            $xmlCPE = $xmlCPE . '</sac:SummaryDocumentsLine>';
            $j++;
        }
        $xmlCPE = $xmlCPE . '</SummaryDocuments>';
        $doc->loadXML($xmlCPE);
        $doc->save($ruta . '.xml');
        $resp['respuesta'] = 'ok';
        $resp['url_xml'] = $ruta . '.xml';
        return $resp;
    }
	
	public function enviar_documento($ruc, $usuario_sol, $pass_sol, $ruta_archivo, $ruta_archivo_cdr, $archivo, $ruta_ws) {
        //===================ENVIO FACTURACION=====================
        $soapUrl = $ruta_ws;
        // xml post structure
        $xml_post_string = file_get_contents($ruta_archivo . '.xml');
        $headers = array(
            "Content-type: text/xml;charset=\"utf-8\"",
            "Accept: text/xml",
            "Cache-Control: no-cache",
            "Pragma: no-cache",
            "SOAPAction: http://tempuri.org/IService/Registrar",
            "Content-length: " . strlen($xml_post_string),
        );
        $url = $soapUrl;
        // PHP cURL  for https connection with auth
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $xml_post_string); // the SOAP request
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

            // converting
            $response = curl_exec($ch);
            $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($httpcode == 200) {
                $doc = new DOMDocument();
                $doc->loadXML($response);
                //===================VERIFICAMOS SI HA ENVIADO CORRECTAMENTE EL COMPROBANTE=====================
                if (isset($doc->getElementsByTagName('RegistrarResult')->item(0)->nodeValue)) {
                    $result = $doc->getElementsByTagName('RegistrarResult')->item(0)->nodeValue;
                    if ($result === "true") {
                        $resp['respuesta'] = 'ok';
                        $resp['codigo_barras'] = $doc->getElementsByTagName('CodigoBarras')->item(0)->nodeValue;
                        $resp['mensaje'] = $doc->getElementsByTagName('Cadena')->item(0)->nodeValue;
                        $resp['codigo_hash'] = $doc->getElementsByTagName('CodigoHash')->item(0)->nodeValue;
                        $resp['response_xml'] = $response;
                    } else {
                        $resp['respuesta'] = 'error';
                        $resp['codigo_barras'] = $doc->getElementsByTagName('CodigoBarras')->item(0)->nodeValue;
                        $resp['mensaje'] = $doc->getElementsByTagName('Cadena')->item(0)->nodeValue;
                        $resp['codigo_hash'] = $doc->getElementsByTagName('CodigoHash')->item(0)->nodeValue;
                        $resp['response_xml'] = $response;
                    }
                } else {
                    $resp['respuesta'] = 'error';
                    // $resp['cod_sunat'] = $doc->getElementsByTagName('faultcode')->item(0)->nodeValue;
                    // $resp['mensaje'] = $doc->getElementsByTagName('faultstring')->item(0)->nodeValue;
                    // $resp['hash_cdr'] = "";
                    $resp['response_xml'] = $response;
                }
            } else {
                //echo "no responde web";
                $resp['respuesta'] = 'error';
                $resp['cod_sunat'] = "0000";
                $resp['url'] = $url;
                $resp['response_xml'] = $response;
                $resp['error_code'] = $httpcode;
                $resp['hash_cdr'] = "";
            }
            return $resp;
    }

    public function enviar_documento_prueba($ruc, $usuario_sol, $pass_sol, $ruta_archivo, $ruta_archivo_cdr, $archivo, $ruta_ws) {
        try {
        //=================ZIPEAR ================
        $zip = new ZipArchive();
        $filenameXMLCPE = $ruta_archivo . '.zip';

        if ($zip->open($filenameXMLCPE, ZIPARCHIVE::CREATE) === true) {
            $zip->addFile($ruta_archivo . '.xml', $archivo . '.xml'); //ORIGEN, DESTINO
            $zip->close();
        }

        //===================ENVIO FACTURACION=====================
        $soapUrl = $ruta_ws; //"https://e-beta.sunat.gob.pe:443/ol-ti-itcpfegem-beta/billService"; // asmx URL of WSDL
        $soapUser = "";  //  username
        $soapPassword = ""; // password
        // xml post structure
        $xml_post_string = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" 
            xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ser="http://service.sunat.gob.pe" 
            xmlns:wsse="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd">
            <soapenv:Header>
                <wsse:Security>
                    <wsse:UsernameToken>
                        <wsse:Username>' . $usuario_sol . '</wsse:Username>
                        <wsse:Password>' . $pass_sol . '</wsse:Password>
                    </wsse:UsernameToken>
                </wsse:Security>
            </soapenv:Header>
            <soapenv:Body>
                <ser:sendBill>
                    <fileName>' . $archivo . '.zip</fileName>
                    <contentFile>' . base64_encode(file_get_contents($ruta_archivo . '.zip')) . '</contentFile>
                </ser:sendBill>
            </soapenv:Body>
        </soapenv:Envelope>';

        $headers = array(
            "Content-type: text/xml;charset=\"utf-8\"",
            "Accept: text/xml",
            "Cache-Control: no-cache",
            "Pragma: no-cache",
            "SOAPAction: urn:sendBill",
            "Content-length: " . strlen($xml_post_string),
        );

        $url = $soapUrl;
        
        //echo $xml_post_string;

        // PHP cURL  for https connection with auth
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        //curl_setopt($ch, CURLOPT_USERPWD, $soapUser.":".$soapPassword); // username and password - declared at the top of the doc
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml_post_string); // the SOAP request
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        // converting
        $response = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        //echo $httpcode;
        //echo $response;
        //if ($httpcode == 200) {//======LA PAGINA SI RESPONDE
            //echo $httpcode.'----'.$response;
            //convertimos de base 64 a archivo fisico
            $doc = new DOMDocument();
            $doc->loadXML($response);

            //===================VERIFICAMOS SI HA ENVIADO CORRECTAMENTE EL COMPROBANTE=====================
            if (isset($doc->getElementsByTagName('applicationResponse')->item(0)->nodeValue)) {
                $xmlCDR = $doc->getElementsByTagName('applicationResponse')->item(0)->nodeValue;
                file_put_contents($ruta_archivo_cdr . 'R-' . $archivo . '.zip', base64_decode($xmlCDR));

                //extraemos archivo zip a xml
                $zip = new ZipArchive;
                if ($zip->open($ruta_archivo_cdr . 'R-' . $archivo . '.zip') === TRUE) {
                    $zip->extractTo($ruta_archivo_cdr, 'R-' . $archivo . '.xml');
                    $zip->close();
                }

                //eliminamos los archivos Zipeados
                unlink($ruta_archivo . '.zip');
                unlink($ruta_archivo_cdr . 'R-' . $archivo . '.zip');

                //=============hash CDR=================
                $doc_cdr = new DOMDocument();
                $doc_cdr->load($ruta_archivo_cdr . 'R-' . $archivo . '.xml');
                $resp['respuesta'] = 'ok';
                $resp['cod_sunat'] = $doc_cdr->getElementsByTagName('ResponseCode')->item(0)->nodeValue;
                $resp['mensaje'] = $doc_cdr->getElementsByTagName('Description')->item(0)->nodeValue;
                $resp['hash_cdr'] = $doc_cdr->getElementsByTagName('DigestValue')->item(0)->nodeValue;
                $resp['response_xml'] = $response;
            } else {
                //$mensaje['cod_sunat'] = $doc->getElementsByTagName('faultcode')->item(0)->nodeValue;
                //$mensaje['msj_sunat'] = $doc->getElementsByTagName('faultstring')->item(0)->nodeValue;
                //$mensaje['hash_cdr'] = "";
                $mensaje['cod_sunat'] = $doc->getElementsByTagName('faultstring')->item(0)->nodeValue;
                $mensaje['msj_sunat'] = $doc->getElementsByTagName('message')->item(0)->nodeValue;
                $mensaje['hash_cdr'] = "";
                $resp['response_xml'] = $response;
            }
        } catch (Exception $e) {
            $mensaje['cod_sunat']="0000";
            $mensaje['mensaje']="SINCRONIZACIÓN CORRECTA";
            $mensaje['response_xml'] = $response;
            $mensaje['mensaje_original']="SUNAT ESTA FUERA SERVICIO: " . (!is_null($e)) ? $e->getMessage() : " Respuesta null";
            $mensaje['hash_cdr'] = "";
            $mensaje['url_envio'] = $url;
        }
        //print_r($mensaje); 
        return $mensaje;
        //$xmlCDR = $doc->getElementsByTagName('applicationResponse')->item(0)->nodeValue;
    }

    public function crear_xml_guia_remision($cabecera, $detalle, $ruta) {
        $validacion = new validaciondedatos();
        $doc = new DOMDocument();
        $doc->formatOutput = FALSE;
        $doc->preserveWhiteSpace = TRUE;
        $doc->encoding = 'ISO-8859-1';
        $xmlCPE = '<?xml version="1.0" encoding="UTF-8"?>
            <DespatchAdvice xmlns="urn:oasis:names:specification:ubl:schema:xsd:DespatchAdvice-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2" xmlns:ds="http://www.w3.org/2000/09/xmldsig#" xmlns:ext="urn:oasis:names:specification:ubl:schema:xsd:CommonExtensionComponents-2">
                <ext:UBLExtensions>
                    <ext:UBLExtension>
                        <ext:ExtensionContent>
                        </ext:ExtensionContent>
                    </ext:UBLExtension>
                </ext:UBLExtensions>
                <cbc:UBLVersionID>2.1</cbc:UBLVersionID>
                <cbc:CustomizationID>1.0</cbc:CustomizationID>
                <cbc:ID>'.$cabecera["SERIE"].'-'.$cabecera["SECUENCIA"].'</cbc:ID>
                <cbc:IssueDate>'.$cabecera["FECHA_DOCUMENTO"].'</cbc:IssueDate>
                <cbc:IssueTime>00:00:00</cbc:IssueTime>
                <cbc:DespatchAdviceTypeCode>'.$cabecera["CODIGO"].'</cbc:DespatchAdviceTypeCode>
                <cbc:Note><![CDATA[STIHL'.$cabecera["NOTA"].']]></cbc:Note>
                <cac:Signature>
                    <cbc:ID>IDSignSP</cbc:ID>
                    <cac:SignatoryParty>
                        <cac:PartyIdentification>
                            <cbc:ID>' . $cabecera["NRO_DOCUMENTO_EMPRESA"] . '</cbc:ID>
                        </cac:PartyIdentification>
                        <cac:PartyName>
                            <cbc:Name><![CDATA[' . $cabecera["RAZON_SOCIAL_EMPRESA"] . ']]></cbc:Name>
                        </cac:PartyName>
                    </cac:SignatoryParty>
                    <cac:DigitalSignatureAttachment>
                        <cac:ExternalReference>
                            <cbc:URI>#SignatureSP</cbc:URI>
                        </cac:ExternalReference>
                    </cac:DigitalSignatureAttachment>
                </cac:Signature>
                <cac:DespatchSupplierParty>
                    <cbc:CustomerAssignedAccountID schemeID="' . $cabecera["TIPO_DOCUMENTO_EMPRESA"] . '">'.$cabecera["NRO_DOCUMENTO_EMPRESA"].'</cbc:CustomerAssignedAccountID>
                    <cac:Party>
                        <cac:PartyLegalEntity>
                            <cbc:RegistrationName><![CDATA['.$validacion->replace_invalid_caracters($cabecera["RAZON_SOCIAL_EMPRESA"]).']]></cbc:RegistrationName>
                        </cac:PartyLegalEntity>
                    </cac:Party>
                </cac:DespatchSupplierParty>
                <cac:DeliveryCustomerParty>
                    <cbc:CustomerAssignedAccountID schemeID="' . $cabecera["TIPO_DOCUMENTO_CLIENTE"] . '">'.$cabecera["NRO_DOCUMENTO_CLIENTE"].'</cbc:CustomerAssignedAccountID>
                    <cac:Party>
                        <cac:PartyLegalEntity>
                            <cbc:RegistrationName><![CDATA['.$cabecera["RAZON_SOCIAL_CLIENTE"].']]></cbc:RegistrationName>
                        </cac:PartyLegalEntity>
                    </cac:Party>
                </cac:DeliveryCustomerParty>
                <cac:Shipment>
                    <cbc:ID>1</cbc:ID>
                    <cbc:HandlingCode>'.$cabecera["CODMOTIVO_TRASLADO"].'</cbc:HandlingCode>
                    <cbc:Information><![CDATA['.$cabecera["MOTIVO_TRASLADO"].']]></cbc:Information>
                    <cbc:GrossWeightMeasure unitCode="KGM">'.$cabecera["PESO"].'</cbc:GrossWeightMeasure>
                    <cbc:TotalTransportHandlingUnitQuantity>'.$cabecera["NUMERO_PAQUETES"].'</cbc:TotalTransportHandlingUnitQuantity>
                    <cbc:SplitConsignmentIndicator>false</cbc:SplitConsignmentIndicator>
                    <cac:ShipmentStage>
                        <cbc:TransportModeCode>'.$cabecera["CODTIPO_TRANSPORTISTA"].'</cbc:TransportModeCode>
                        <cac:TransitPeriod>
                            <cbc:StartDate>'.$cabecera["FECHA_DOCUMENTO"].'</cbc:StartDate>
                        </cac:TransitPeriod>
                        <cac:CarrierParty>
                            <cac:PartyIdentification>
                                <cbc:ID schemeID="'.$cabecera["TIPO_DOCUMENTO_TRANSPORTE"].'">'.$cabecera["NRO_DOCUMENTO_TRANSPORTE"].'</cbc:ID>
                            </cac:PartyIdentification>
                            <cac:PartyName>
                                <cbc:Name><![CDATA['.$cabecera["RAZON_SOCIAL_TRANSPORTE"].']]></cbc:Name>
                            </cac:PartyName>
                        </cac:CarrierParty>
                        <cac:DriverPerson>
                            <cbc:ID schemeID="0" />
                        </cac:DriverPerson>
                    </cac:ShipmentStage>
                    <cac:ShipmentStage>
                        <cac:TransitPeriod>
                            <cbc:StartDate>'.$cabecera["FECHA_DOCUMENTO"].'</cbc:StartDate>
                        </cac:TransitPeriod>
                    </cac:ShipmentStage>
                    <cac:Delivery>
                        <cac:DeliveryAddress>
                            <cbc:ID>'.$cabecera["UBIGEO_DESTINO"].'</cbc:ID>
                            <cbc:StreetName>'.$cabecera["DIR_DESTINO"].'</cbc:StreetName>
                        </cac:DeliveryAddress>
                    </cac:Delivery>
                    <cac:OriginAddress>
                        <cbc:ID>'.$cabecera["UBIGEO_PARTIDA"].'</cbc:ID>
                        <cbc:StreetName>'.$cabecera["DIR_PARTIDA"].'</cbc:StreetName>
                    </cac:OriginAddress>
                </cac:Shipment>';
    
        for ($i = 0; $i < count($detalle); $i++) {
            $xmlCPE = $xmlCPE . '<cac:DespatchLine>
                <cbc:ID>'.$detalle[$i]["ITEM"].'</cbc:ID>
                <cbc:DeliveredQuantity unitCode="NIU">'.$detalle[$i]["PESO"].'</cbc:DeliveredQuantity>
                <cac:OrderLineReference>
                    <cbc:LineID>'.$detalle[$i]["NUMERO_ORDEN"].'</cbc:LineID>
                </cac:OrderLineReference>
                <cac:Item>
                    <cbc:Name><![CDATA['.$validacion->replace_invalid_caracters($detalle[$i]["DESCRIPCION"]).']]></cbc:Name>
                    <cac:SellersItemIdentification>
                        <cbc:ID>'.$detalle[$i]["CODIGO_PRODUCTO"].'</cbc:ID>
                    </cac:SellersItemIdentification>
                </cac:Item>
            </cac:DespatchLine>';
        }
        $xmlCPE = $xmlCPE . '</DespatchAdvice>';
        $doc->loadXML($xmlCPE);
        $doc->save($ruta . '.xml');
        $resp['respuesta'] = 'ok';
        $resp['url_xml'] = $ruta . '.xml';
        return $resp;
    }

    public function crear_xml_baja_sunat($cabecera, $detalle, $ruta) {
        $validacion = new validaciondedatos();
        $doc = new DOMDocument();
        $doc->formatOutput = FALSE;
        $doc->preserveWhiteSpace = TRUE;
        $doc->encoding = 'ISO-8859-1';
        $xmlCPE = '<?xml version="1.0" encoding="ISO-8859-1" standalone="no"?><VoidedDocuments xmlns="urn:sunat:names:specification:ubl:peru:schema:xsd:VoidedDocuments-1" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2" xmlns:ds="http://www.w3.org/2000/09/xmldsig#" xmlns:ext="urn:oasis:names:specification:ubl:schema:xsd:CommonExtensionComponents-2" xmlns:sac="urn:sunat:names:specification:ubl:peru:schema:xsd:SunatAggregateComponents-1" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
            <ext:UBLExtensions>
            <ext:UBLExtension>
            <ext:ExtensionContent>
            </ext:ExtensionContent>
            </ext:UBLExtension>
            </ext:UBLExtensions>
            <cbc:UBLVersionID>2.0</cbc:UBLVersionID>
            <cbc:CustomizationID>1.0</cbc:CustomizationID>
            <cbc:ID>'.$cabecera["CODIGO"].'-'.$cabecera["SERIE"].'-'.$cabecera["SECUENCIA"].'</cbc:ID>
            <cbc:ReferenceDate>'.$cabecera["FECHA_REFERENCIA"].'</cbc:ReferenceDate>
            <cbc:IssueDate>'.$cabecera["FECHA_BAJA"].'</cbc:IssueDate>
            <cac:Signature>
            <cbc:ID>IDSignKG</cbc:ID>
            <cac:SignatoryParty>
            <cac:PartyIdentification>
            <cbc:ID>'.$cabecera["NRO_DOCUMENTO_EMPRESA"].'</cbc:ID>
            </cac:PartyIdentification>
            <cac:PartyName>
            <cbc:Name>'.$validacion->replace_invalid_caracters($cabecera["RAZON_SOCIAL_EMPRESA"]).'</cbc:Name>
            </cac:PartyName>
            </cac:SignatoryParty>
            <cac:DigitalSignatureAttachment>
            <cac:ExternalReference>
            <cbc:URI>#'.$cabecera["SERIE"].'-'.$cabecera["SECUENCIA"].'</cbc:URI>
            </cac:ExternalReference>
            </cac:DigitalSignatureAttachment>
            </cac:Signature>
            <cac:AccountingSupplierParty>
            <cbc:CustomerAssignedAccountID>'.$cabecera["NRO_DOCUMENTO_EMPRESA"].'</cbc:CustomerAssignedAccountID>
            <cbc:AdditionalAccountID>'.$cabecera["TIPO_DOCUMENTO_EMPRESA"].'</cbc:AdditionalAccountID>
            <cac:Party>
            <cac:PartyLegalEntity>
            <cbc:RegistrationName><![CDATA['.$validacion->replace_invalid_caracters($cabecera["RAZON_SOCIAL_EMPRESA"]).']]></cbc:RegistrationName>
            </cac:PartyLegalEntity>
            </cac:Party>
            </cac:AccountingSupplierParty>';
    
        for ($i = 0; $i < count($detalle); $i++) {
            $xmlCPE = $xmlCPE . '<sac:VoidedDocumentsLine>
            <cbc:LineID>'.$detalle[$i]["ITEM"].'</cbc:LineID>
            <cbc:DocumentTypeCode>'.$detalle[$i]["TIPO_COMPROBANTE"].'</cbc:DocumentTypeCode>
            <sac:DocumentSerialID>'.$detalle[$i]["SERIE"].'</sac:DocumentSerialID>
            <sac:DocumentNumberID>'.$detalle[$i]["NUMERO"].'</sac:DocumentNumberID>
            <sac:VoidReasonDescription><![CDATA['.$validacion->replace_invalid_caracters($detalle[$i]["MOTIVO"]).']]></sac:VoidReasonDescription>
            </sac:VoidedDocumentsLine>';
        }

        $xmlCPE = $xmlCPE . '</VoidedDocuments>';
        $doc->loadXML($xmlCPE);
        $doc->save($ruta . '.xml');
        $resp['respuesta'] = 'ok';
        $resp['url_xml'] = $ruta . '.xml';
        return $resp;
    }
    
    public function enviar_documento_para_baja($ruc, $usuario_sol, $pass_sol, $ruta_archivo, $ruta_archivo_cdr, $archivo, $ruta_ws) {
        try {
            //=================ZIPEAR ================
            $zip = new ZipArchive();
            $filenameXMLCPE = $ruta_archivo . '.zip';
            if ($zip->open($filenameXMLCPE, ZIPARCHIVE::CREATE) === true) {
                $zip->addFile($ruta_archivo . '.xml', $archivo . '.xml'); //ORIGEN, DESTINO
                $zip->close();
            }
            //===================ENVIO FACTURACION=====================
            $soapUrl = $ruta_ws; 
            $soapUser = "";  
            $soapPassword = ""; 
            // xml post structure
            $xml_post_string = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" 
                xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ser="http://service.sunat.gob.pe" 
                xmlns:wsse="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd">
                <soapenv:Header>
                    <wsse:Security>
                        <wsse:UsernameToken>
                            <wsse:Username>' . $usuario_sol . '</wsse:Username>
                            <wsse:Password>' . $pass_sol . '</wsse:Password>
                        </wsse:UsernameToken>
                    </wsse:Security>
                </soapenv:Header>
                <soapenv:Body>
                    <ser:sendSummary>
                        <fileName>' . $archivo . '.zip</fileName>
                        <contentFile>' . base64_encode(file_get_contents($ruta_archivo . '.zip')) . '</contentFile>
                    </ser:sendSummary>
                </soapenv:Body>
            </soapenv:Envelope>';
        
            $headers = array(
                "Content-type: text/xml;charset=\"utf-8\"",
                "Accept: text/xml",
                "Cache-Control: no-cache",
                "Pragma: no-cache",
                "SOAPAction: urn:sendSummary",
                "Content-length: " . strlen($xml_post_string),
            );
        
            $url = $soapUrl;
        
            // PHP cURL  for https connection with auth
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            //curl_setopt($ch, CURLOPT_USERPWD, $soapUser.":".$soapPassword); // username and password - declared at the top of the doc
            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $xml_post_string); // the SOAP request
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        
            // converting
            $response = curl_exec($ch);
            $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            //convertimos de base 64 a archivo fisico
            $doc = new DOMDocument();
            $doc->loadXML($response);
    
            //===================VERIFICAMOS SI HA ENVIADO CORRECTAMENTE EL COMPROBANTE=====================
            if (isset($doc->getElementsByTagName('ticket')->item(0)->nodeValue)) {
                $ticket = $doc->getElementsByTagName('ticket')->item(0)->nodeValue;
                
                unlink($ruta_archivo . '.zip');
                $mensaje['respuesta'] = 'ok';
                $mensaje['cod_ticket'] = $ticket;
                $mensaje['extra'] = $doc->getElementsByTagName('faultcode')->item(0)->nodeValue.' - '.$doc->getElementsByTagName('faultstring')->item(0)->nodeValue;
            } else {
                $mensaje['respuesta'] = 'error';
                $mensaje['cod_sunat'] = $doc->getElementsByTagName('faultcode')->item(0)->nodeValue;
                $mensaje['mensaje'] = $doc->getElementsByTagName('faultstring')->item(0)->nodeValue;
                $mensaje['hash_cdr'] = "";
            }

        } catch (Exception $e) {
            $mensaje['respuesta'] = 'error';
            $mensaje['cod_sunat']="0000";
            $mensaje['mensaje']="SINCRONIZACIÓN CORRECTA";
            $mensaje['response_xml'] = $response;
            $mensaje['mensaje_original']="SUNAT ESTA FUERA SERVICIO" . (!is_null($e)) ? $e->getMessage() : " Respuesta null";
            $mensaje['url_envio'] = $url;
	        $mensaje['hash_cdr'] = "";
        }
        return $mensaje;
    }

    public function enviar_resumen_boletas($ruc, $usuario_sol, $pass_sol, $ruta_archivo, $ruta_archivo_cdr, $archivo, $ruta_ws) {
        //=================ZIPEAR ================
        $zip = new ZipArchive();
        $filenameXMLCPE = $ruta_archivo . '.zip';
        if ($zip->open($filenameXMLCPE, ZIPARCHIVE::CREATE) === true) {
            $zip->addFile($ruta_archivo . '.xml', $archivo . '.xml'); //ORIGEN, DESTINO
            $zip->close();
        }
        //===================ENVIO FACTURACION=====================
        $soapUrl = $ruta_ws; 
        $soapUser = "";  
        $soapPassword = ""; 
        // xml post structure
        $xml_post_string = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" 
            xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ser="http://service.sunat.gob.pe" 
            xmlns:wsse="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd">
            <soapenv:Header>
                <wsse:Security>
                    <wsse:UsernameToken>
                        <wsse:Username>' . $usuario_sol . '</wsse:Username>
                        <wsse:Password>' . $pass_sol . '</wsse:Password>
                    </wsse:UsernameToken>
                </wsse:Security>
            </soapenv:Header>
            <soapenv:Body>
                <ser:sendSummary>
                    <fileName>' . $archivo . '.zip</fileName>
                    <contentFile>' . base64_encode(file_get_contents($ruta_archivo . '.zip')) . '</contentFile>
                </ser:sendSummary>
            </soapenv:Body>
        </soapenv:Envelope>';

        $url = $soapUrl;
        $messageResponse = $this->soapCall($url, $callFunction = "sendSummary", $xml_post_string, "", $ruc);
        
        if (!is_null($messageResponse[0])) {
            //convertimos de base 64 a archivo fisico
            $response = $messageResponse[0];
            $doc = new DOMDocument();
            $doc->loadXML($response);
            // RESPUESTA XML
            //===================VERIFICAMOS SI HA ENVIADO CORRECTAMENTE EL COMPROBANTE=====================
            if (isset($doc->getElementsByTagName('ticket')->item(0)->nodeValue)) {
                $ticket = $doc->getElementsByTagName('ticket')->item(0)->nodeValue;
                unlink($ruta_archivo . '.zip');
                $mensaje['respuesta'] = 'ok';
                $mensaje['cod_ticket'] = $ticket;
                $mensaje['cod_sunat'] = "0000";
            } else {
                $faultcode = $doc->getElementsByTagName('faultcode')->item(0)->nodeValue;
                $faultstring = $doc->getElementsByTagName('faultstring')->item(0)->nodeValue;
                if ($faultcode === 'S:Server') {
                    $message = $doc->getElementsByTagName('detail')->item(0)->nodeValue;
                    $mensaje['respuesta'] = 'error';
                    $mensaje['cod_sunat'] = $faultstring;
                    $mensaje['mensaje'] = $faultcode . " - " . $message;
                    $mensaje['hash_cdr'] = "";
                    # code...
                } else {
                    $mensaje['respuesta'] = 'error';
                    $mensaje['cod_sunat'] = $faultcode;
                    $mensaje['mensaje'] = $faultstring;
                    $mensaje['hash_cdr'] = "";
                }
            }
        } else {
            //echo "no responde web";
            $mensaje['respuesta'] = 'error';
            $mensaje['cod_sunat']= "0000";
            $mensaje['mensaje']="SINCRONIZACIÓN CORRECTA";
            $mensaje['response_xml'] = $response;
            $mensaje['mensaje_original']="SUNAT ESTA FUERA SERVICIO: " . $httpcode;
	    	$mensaje['url_envio'] = $url;
		    $mensaje['hash_cdr'] = "";
        }
        return $mensaje;
    }

    function consultar_envio_ticket($ruc, $usuario_sol, $pass_sol, $ticket, $archivo, $ruta_archivo_cdr, $ruta_ws) {
        $xml_post_string = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ser="http://service.sunat.gob.pe" xmlns:wsse="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd">
            <soapenv:Header>
            <wsse:Security>
            <wsse:UsernameToken>
            <wsse:Username>' . $usuario_sol . '</wsse:Username>
            <wsse:Password>' . $pass_sol . '</wsse:Password>
            </wsse:UsernameToken>
            </wsse:Security>
            </soapenv:Header>
            <soapenv:Body>
            <ser:getStatus>
            <ticket>' . $ticket . '</ticket>
            </ser:getStatus>
            </soapenv:Body>
        </soapenv:Envelope>';

        $headers = array(
            "Content-type: text/xml;charset=\"utf-8\"",
            "Accept: text/xml",
            "Cache-Control: no-cache",
            "Pragma: no-cache",
            "SOAPAction: urn:getStatus",
            "Content-length: " . strlen($xml_post_string),
        );
        
        // PHP cURL  for https connection with auth
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
        curl_setopt($ch, CURLOPT_URL, $ruta_ws);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        //curl_setopt($ch, CURLOPT_USERPWD, $soapUser.":".$soapPassword); // username and password - declared at the top of the doc
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml_post_string); // the SOAP request
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    
        // converting
        $response = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        if ($httpcode == 200) {//======LA PAGINA SI RESPONDE
            //echo $httpcode.'----'.$response;
            //convertimos de base 64 a archivo fisico
            $doc = new DOMDocument();
            $doc->loadXML($response);    
            //===================VERIFICAMOS SI HA ENVIADO CORRECTAMENTE EL COMPROBANTE=====================
            if (isset($doc->getElementsByTagName('content')->item(0)->nodeValue)) {
                $xmlCDR = $doc->getElementsByTagName('content')->item(0)->nodeValue;
                file_put_contents($ruta_archivo_cdr . 'R-' . $archivo . '.zip', base64_decode($xmlCDR));
    
                //extraemos archivo zip a xml
                $zip = new ZipArchive;
                if ($zip->open($ruta_archivo_cdr . 'R-' . $archivo . '.zip') === TRUE) {
                    $zip->extractTo($ruta_archivo_cdr, 'R-' . $archivo . '.xml');
                    $zip->close();
                }
    
                //eliminamos los archivos Zipeados
                //unlink($ruta_archivo . '.zip');
                unlink($ruta_archivo_cdr . 'R-' . $archivo . '.zip');
    
                //=============hash CDR=================
                $doc_cdr = new DOMDocument();
                $doc_cdr->load(dirname(__FILE__) . '/' . $ruta_archivo_cdr . 'R-' . $archivo . '.xml');
                
                $mensaje['respuesta'] = 'ok';
                $mensaje['cod_sunat'] = $doc_cdr->getElementsByTagName('ResponseCode')->item(0)->nodeValue;
                $mensaje['msj_sunat'] = $doc_cdr->getElementsByTagName('Description')->item(0)->nodeValue;
                $mensaje['mensaje'] = $doc_cdr->getElementsByTagName('Description')->item(0)->nodeValue;
                $mensaje['hash_cdr'] =  $doc_cdr->getElementsByTagName('DigestValue')->item(0)->nodeValue;

            } else {
                $mensaje['cod_sunat'] = $doc->getElementsByTagName('faultcode')->item(0)->nodeValue;
                $mensaje['msj_sunat'] = $doc->getElementsByTagName('faultstring')->item(0)->nodeValue;
                $mensaje['hash_cdr'] = "";

                $mensaje['respuesta'] = 'error';
                $mensaje['cod_sunat'] = $doc->getElementsByTagName('faultcode')->item(0)->nodeValue;
                $mensaje['mensaje'] = $doc->getElementsByTagName('faultstring')->item(0)->nodeValue;
                $mensaje['msj_sunat'] = $doc->getElementsByTagName('faultstring')->item(0)->nodeValue;
                $mensaje['hash_cdr'] = "";

            }
        } else {
            //echo "no responde web";
            $mensaje['respuesta'] = 'error';
            $mensaje['cod_sunat']="0000";
            $mensaje['mensaje']="SINCRONIZACIÓN CORRECTA";
            $mensaje['response_xml'] = $response;
            $mensaje['mensaje_original']="SUNAT ESTA FUERA SERVICIO: " . $httpcode;
            $mensaje['hash_cdr'] = "";
        }
        
        return $mensaje;
    }

    function consultar_ticket($ruc, $usuario_sol, $pass_sol, $ticket, $archivo, $ruta_archivo_cdr, $ruta_ws) {
        $xml_post_string = '<soapenv:Envelope xmlns:ser="http://service.sunat.gob.pe" xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:wsse="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd">
            <soapenv:Header>
                <wsse:Security>
                    <wsse:UsernameToken>
                        <wsse:Username>' .$usuario_sol. '</wsse:Username>
                        <wsse:Password>' .$pass_sol. '</wsse:Password>
                    </wsse:UsernameToken>
                </wsse:Security>
            </soapenv:Header>
            <soapenv:Body>
                <ser:getStatus>
                    <ticket>' . $ticket . '</ticket>
                </ser:getStatus>
            </soapenv:Body>
        </soapenv:Envelope>';
        $headers = array(
            "Content-type: text/xml;charset=\"utf-8\"",
            "Accept: text/xml",
            "Cache-Control: no-cache",
            "Pragma: no-cache",
            "SOAPAction: urn:getStatus",
            "Content-length: " . strlen($xml_post_string),
        );
        // PHP cURL  for https connection with auth
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
        curl_setopt($ch, CURLOPT_URL, $ruta_ws);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        //curl_setopt($ch, CURLOPT_USERPWD, $soapUser.":".$soapPassword); // username and password - declared at the top of the doc
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml_post_string); // the SOAP request
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    
        // converting
        $response = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        if ($httpcode == 200) {//======LA PAGINA SI RESPONDE
            //echo $httpcode.'----'.$response;
            //convertimos de base 64 a archivo fisico
            $doc = new DOMDocument();
            $doc->loadXML($response);    
            //===================VERIFICAMOS SI HA ENVIADO CORRECTAMENTE EL COMPROBANTE=====================
            if (isset($doc->getElementsByTagName('content')->item(0)->nodeValue)) {
                $xmlCDR = $doc->getElementsByTagName('content')->item(0)->nodeValue;
                file_put_contents($ruta_archivo_cdr . 'R-' . $archivo . '.zip', base64_decode($xmlCDR));
    
                //extraemos archivo zip a xml
                $zip = new ZipArchive;
                if ($zip->open($ruta_archivo_cdr . 'R-' . $archivo . '.zip') === TRUE) {
                    $zip->extractTo($ruta_archivo_cdr, 'R-' . $archivo . '.xml');
                    $zip->close();
                }
    
                //eliminamos los archivos Zipeados
                //unlink($ruta_archivo . '.zip');
                unlink($ruta_archivo_cdr . 'R-' . $archivo . '.zip');
    
                //=============hash CDR=================
                $doc_cdr = new DOMDocument();
                $doc_cdr->load(dirname(__FILE__) . '/' . $ruta_archivo_cdr . 'R-' . $archivo . '.xml');
                
                $mensaje['respuesta'] = 'ok';
                $mensaje['cod_sunat'] = $doc_cdr->getElementsByTagName('ResponseCode')->item(0)->nodeValue;
                $mensaje['msj_sunat'] = $doc_cdr->getElementsByTagName('Description')->item(0)->nodeValue;
                $mensaje['mensaje'] = $doc_cdr->getElementsByTagName('Description')->item(0)->nodeValue;
                $mensaje['hash_cdr'] =  $doc_cdr->getElementsByTagName('DigestValue')->item(0)->nodeValue;

            } else {
                $mensaje['cod_sunat'] = $doc->getElementsByTagName('faultcode')->item(0)->nodeValue;
                $mensaje['msj_sunat'] = $doc->getElementsByTagName('faultstring')->item(0)->nodeValue;
                $mensaje['hash_cdr'] = "";

                $mensaje['respuesta'] = 'error';
                $mensaje['cod_sunat'] = $doc->getElementsByTagName('faultcode')->item(0)->nodeValue;
                $mensaje['mensaje'] = $doc->getElementsByTagName('faultstring')->item(0)->nodeValue;
                $mensaje['msj_sunat'] = $doc->getElementsByTagName('faultstring')->item(0)->nodeValue;
                $mensaje['hash_cdr'] = "";

            }
        } else {
            //echo "no responde web";
            $mensaje['respuesta'] = 'error';
            $mensaje['cod_sunat']="0000";
            $mensaje['mensaje']="SINCRONIZACIÓN CORRECTA";
            $mensaje['response_xml'] = $response;
            $mensaje['mensaje_original']="SUNAT ESTA FUERA SERVICIO: " . $httpcode;
            $mensaje['hash_cdr'] = "";
        }
        
        return $mensaje;
    }

    function consultar_cdr($ruc, $usuario_sol, $pass_sol, $dataCdr, $archivo, $ruta_archivo_cdr, $ruta_ws) {
        $xml_post_string = '<soapenv:Envelope xmlns:ser="http://service.sunat.gob.pe" xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:wsse="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd">
            <soapenv:Header>
                <wsse:Security>
                    <wsse:UsernameToken>
                        <wsse:Username>' .$usuario_sol. '</wsse:Username>
                        <wsse:Password>' .$pass_sol. '</wsse:Password>
                    </wsse:UsernameToken>
                </wsse:Security>
            </soapenv:Header>
            <soapenv:Body>
                <ser:getStatusCdr>
                    <rucComprobante>' .$ruc. '</rucComprobante>
                    <tipoComprobante>' .$dataCdr['TIPO_DOCUMENTO']. '</tipoComprobante>
                    <serieComprobante>' .$dataCdr['SERIE']. '</serieComprobante>
                    <numeroComprobante>' .$dataCdr['CORRELATIVO']. '</numeroComprobante>
                </ser:getStatusCdr>
            </soapenv:Body>
        </soapenv:Envelope>';
        $headers = array(
            "Content-type: text/xml;charset=\"utf-8\"",
            "Accept: text/xml",
            "Cache-Control: no-cache",
            "Pragma: no-cache",
            "SOAPAction: urn:getStatus",
            "Content-length: " . strlen($xml_post_string),
        );
        // PHP cURL  for https connection with auth
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
        curl_setopt($ch, CURLOPT_URL, $ruta_ws);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        //curl_setopt($ch, CURLOPT_USERPWD, $soapUser.":".$soapPassword); // username and password - declared at the top of the doc
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml_post_string); // the SOAP request
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    
        // converting
        $response = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        if ($httpcode == 200) {//======LA PAGINA SI RESPONDE
            //echo $httpcode.'----'.$response;
            //convertimos de base 64 a archivo fisico
            $doc = new DOMDocument();
            $doc->loadXML($response);    
            //===================VERIFICAMOS SI HA ENVIADO CORRECTAMENTE EL COMPROBANTE=====================
            if (isset($doc->getElementsByTagName('content')->item(0)->nodeValue)) {
                $xmlCDR = $doc->getElementsByTagName('content')->item(0)->nodeValue;
                file_put_contents($ruta_archivo_cdr . 'R-' . $archivo . '.zip', base64_decode($xmlCDR));
    
                //extraemos archivo zip a xml
                $zip = new ZipArchive;
                if ($zip->open($ruta_archivo_cdr . 'R-' . $archivo . '.zip') === TRUE) {
                    $zip->extractTo($ruta_archivo_cdr, 'R-' . $archivo . '.xml');
                    $zip->close();
                }
    
                //eliminamos los archivos Zipeados
                //unlink($ruta_archivo . '.zip');
                unlink($ruta_archivo_cdr . 'R-' . $archivo . '.zip');
    
                //=============hash CDR=================
                $doc_cdr = new DOMDocument();
                $doc_cdr->load(dirname(__FILE__) . '/' . $ruta_archivo_cdr . 'R-' . $archivo . '.xml');
                
                $mensaje['respuesta'] = 'ok';
                $mensaje['cod_sunat'] = $doc_cdr->getElementsByTagName('ResponseCode')->item(0)->nodeValue;
                $mensaje['msj_sunat'] = $doc_cdr->getElementsByTagName('Description')->item(0)->nodeValue;
                $mensaje['mensaje'] = $doc_cdr->getElementsByTagName('Description')->item(0)->nodeValue;
                $mensaje['hash_cdr'] =  $doc_cdr->getElementsByTagName('DigestValue')->item(0)->nodeValue;

            } else {
                $mensaje['cod_sunat'] = $doc->getElementsByTagName('faultcode')->item(0)->nodeValue;
                $mensaje['msj_sunat'] = $doc->getElementsByTagName('faultstring')->item(0)->nodeValue;
                $mensaje['hash_cdr'] = "";

                $mensaje['respuesta'] = 'error';
                $mensaje['cod_sunat'] = $doc->getElementsByTagName('faultcode')->item(0)->nodeValue;
                $mensaje['mensaje'] = $doc->getElementsByTagName('faultstring')->item(0)->nodeValue;
                $mensaje['msj_sunat'] = $doc->getElementsByTagName('faultstring')->item(0)->nodeValue;
                $mensaje['hash_cdr'] = "";

            }
        } else {
            //echo "no responde web";
            $mensaje['respuesta'] = 'error';
            $mensaje['cod_sunat']="0000";
            $mensaje['mensaje']="SINCRONIZACIÓN CORRECTA";
            $mensaje['response_xml'] = $response;
            $mensaje['mensaje_original']="SUNAT ESTA FUERA SERVICIO: " . $httpcode;
            $mensaje['hash_cdr'] = "";
        }
        
        return $mensaje;
    }

    private function soapCall($wsdlURL, $callFunction = "SendBill", $XMLString, $file_name = "", $dircdr = "/R") {

        // var_dump("soapCall", $wsdlURL, $callFunction, $XMLString, $file_name, $dircdr);
        // exit();

        $result = null;
        $faultcode = '0';
        $endpoint  = $wsdlURL;
        /*descomentar en produccion*/
        $uri            = 'http://service.sunat.gob.pe';
        $options=array(
            'trace'         => true,
            'location'      => $endpoint,
            'uri'           => $uri
        );
        /*$options=array(
            'trace'         => true
        );*/
        try {
            if ($callFunction === 'sendSummary' && (strpos($wsdlURL, 'conose') === false)) {
                $endpoint = null;
            }
            $client = new feedSoap($endpoint, $options);
            $reply = $client->SoapClientCall($XMLString);
            $client->__call("$callFunction", array(), array());
        }catch (SoapFault $f) {
            $array = explode('.', $f->faultcode);
            if ($array[0]=='soap-env:Client') {
                $faultcode =  $array[1];
            } else {
                if ($f->faultcode == 'WSDL') {
                    $faultcode = '-1';
                } else {
                    $faultcode =  $f->faultcode;
                }
            }
        }
        $ticket = '0';
        if($faultcode === '0' || $faultcode === 'S:Server'){
            $result = $client->__getLastResponse();
        }
        return [$result, $faultcode];
    }

    private function isNotData($test_string) {
        return (bool) preg_match('/^[a-zA-Z0-9\/\r\n+]*={0,2}$/', $test_string);
    }
}

class feedSoap extends \SoapClient {
    var $XMLStr = "";
    function setXMLStr ($value){$this->XMLStr = $value; }
    function getXMLStr(){return $this->XMLStr; }

    function __doRequest($request, $location, $action, $version, $one_way = 0) {
        $request = $this -> XMLStr;
        $dom = new \DOMDocument('1.0');
        try {
            $dom->loadXML($request);
        } catch (DOMException $e) {
            die($e->code);
        }
        $request = $dom->saveXML();
        //doRequest
        return parent::__doRequest($request, $location, $action, $version, $one_way = 0);
    }

    function SoapClientCall($SOAPXML) {
        return $this -> setXMLStr ($SOAPXML);
    }
}
?>
