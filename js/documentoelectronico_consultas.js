$(function(){
	$("#btn_guardar_doc_electronico").click(procesar_documento_electronico);
});

function procesar_documento_electronico() {

    var light = $('#cuerpo_comprobante').parent();

    $(light).block({
        message: '<div class="loader"></div> <p><br />Enviando data, espera un momento!...</p>',
        overlayCSS: {
            backgroundColor: '#fff',
            opacity: 0.8,
            cursor: 'wait'
        },
        css: {
            border: 0,
            padding: 0,
            backgroundColor: 'none'
        }
    });

	var datastring = $("#frm_comprobanteelectronico").serializeArray();
	var i = 0;
    var rowData;
    var datadetalle = [];

    var lista_productos = jQuery('#detalle_documento').getDataIDs();

    if(lista_productos.length <= 0) {
        swal({
            title: 'ERROR',
            text: 'Usted debe agregar un producto como mínimo!...',
            html: true,
            type: "error",
            confirmButtonText: "Ok",
            confirmButtonColor: "#2196F3"
        }, function(){
            $(light).unblock();
        });
        return false;
    }
    
    for (i = 0; i < lista_productos.length; i++) {
        detalle = {};
        rowData = jQuery('#detalle_documento').getRowData(lista_productos[i]);
        detalle["txtITEM"] = i + 1;
        detalle["txtUNIDAD_MEDIDA_DET"] = rowData.idunidadmedida;
        detalle["txtCANTIDAD_DET"] = rowData.cantidad;
        detalle["txtPRECIO_DET"] = rowData.precio;
        detalle["txtSUB_TOTAL_DET"] = rowData.subtotal;
        detalle["txtPRECIO_TIPO_CODIGO"] = "01";
        detalle["txtIGV"] = rowData.igv;
        detalle["txtISC"] = "0";
        detalle["txtIMPORTE_DET"] = rowData.subtotal;//rowData.IMPORTE; //SUB_TOTAL + IGV
        detalle["txtCOD_TIPO_OPERACION"] = "10";
        detalle["txtCODIGO_DET"] = rowData.codigo;
        detalle["txtDESCRIPCION_DET"] = rowData.descripcion;
        detalle["txtPRECIO_SIN_IGV_DET"] = round_math(parseFloat(rowData.precio) / parseFloat(1.18),2);
        
        datadetalle.push(detalle);
    }

    datastring.push({name: 'datadetalle', value: JSON.stringify(datadetalle)});    

	$.ajax({
        url : 'controllers/procesar_data.php',
        method :  'POST',
        dataType : "json",
        data: datastring
    }).then(function(data){
        if(data.respuesta == 'ok') {
            swal({
                title: 'Resultado',
                text: 'Su comprobante se ha procesado correctamente!',
                html: true,
                type: "success",
                confirmButtonText: "Ok",
                confirmButtonColor: "#2196F3"
            }, function(){
                $("#respuesta_proceso").html('<div class="alert alert-success alert-styled-left alert-arrow-left alert-bordered">\
                                                <button type="button" class="close" data-dismiss="alert"><span>×</span><span class="sr-only">Close</span></button>\
                                                Su Documento se ha procesado correctamente...<br><br>\
                                                HASH: '+ data.hash_cpe +' \
                                                <br /><br /> \
                                                <div class="btn-group">\
                                                    <a target="_blank" href="'+ data.ruta_xml +'" class="btn btn-primary legitRipple">xml</a>\
                                                    <a target="_blank" href="'+ data.ruta_cdr +'" class="btn btn-primary legitRipple">cdr</a>\
                                                    <a target="_blank" href="'+ data.ruta_pdf +'" class="btn btn-primary legitRipple">pdf</a>\
                                                </div>\
                                            </div>');
                $(light).unblock();
            });
        } else {
            swal({
                title: 'ERROR',
                text: data.mensaje,
                html: true,
                type: "error",
                confirmButtonText: "Ok",
                confirmButtonColor: "#2196F3"
            }, function(){
                $(light).unblock();
                $("#respuesta_proceso").html('<div class="alert alert-danger alert-styled-left alert-bordered">\
                                                <button type="button" class="close" data-dismiss="alert"><span>×</span><span class="sr-only">Close</span></button>\
                                                ' + data.mensaje + '.\
                                            </div>');
                $(light).unblock();
            });
        }
    }, function(reason){
    	console.log(reason);
    });
}
