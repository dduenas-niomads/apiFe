$(function(){
  	inicializar_controles();
  	inicializar_tabla_detalle();
    $('.select').select2({
        minimumResultsForSearch: -1
    });

    $(".search_document").click(search_document);
    $('input[type=radio][name=tipo_comprobante]').change(function() {
        rellenar_tipo_documento(this.value);
    });

    $('#cliente_tipodocumento').on('change', function() {
        var cliente_tipodocumento = this.value;
        $(".search_document").hide();
        if(cliente_tipodocumento == '1') {
            $("#titulo_nombrecliente").html('Nombre del Cliente');
            $("#titulo_numerodocumento").html('N° de DNI');
            $(".search_document").show();
        } else if (cliente_tipodocumento == '5') {
            $("#titulo_nombrecliente").html('Razón Social');
            $("#titulo_numerodocumento").html('N° de RUC');
            $(".search_document").show();
        } else if (cliente_tipodocumento == '2') {
            $("#titulo_nombrecliente").html('Nombre del Cliente');
            $("#titulo_numerodocumento").html('N° de Carné de Extranjería');
        } else if (cliente_tipodocumento == '3') {
            $("#titulo_nombrecliente").html('Nombre del Cliente');
            $("#titulo_numerodocumento").html('N° de Pasaporte');
        } else if (cliente_tipodocumento == '4') {
            $("#titulo_nombrecliente").html('Nombre del Cliente');
            $("#titulo_numerodocumento").html('N° de Cédula');
        } else {
            $("#titulo_nombrecliente").html('Nombre del Cliente');
            $("#titulo_numerodocumento").html('N° de Documento');
        }
    });

    var fecha_actual = new Date().toJSON().slice(0, 10);
    var numaleatorio = Math.floor(100000 + Math.random() * 900000);

    $("#fecha_comprobante").val(fecha_actual);
    $("#numero_comprobante").val(numaleatorio);
    var num_comprobante_modificado = 'F001-' + (numaleatorio + 10);
    $("#num_comprobante_modificado").val(num_comprobante_modificado);

    $(".btn_agregarproducto").click(lanzar_popup_agregar_articulo);
    
    calcular_totales_producto();

    $("#producto_preciounidad").on('input', function() {
        calcular_totales_producto();
    }).on('change', function() {
        calcular_totales_producto();
    });

    $("#producto_cantidad").on('input', function() {
        calcular_totales_producto();
    }).on('change', function() {
        calcular_totales_producto();
    });

    $(".btn_agregarproducto_detalle").click(add_to_detalle);
    $(".btn_eliminarproducto").click(eliminar_producto_detalle);
});

function lanzar_popup_agregar_articulo() {
    $("#frm_producto")[0].reset();
    $("#vm_agregar_articulo").modal("show");
}

function add_to_detalle() {
    var idarticulo = $('#producto_codigo').val();
    var data = {
        idarticulo: idarticulo,
        codigo: $('#producto_codigo').val(),
        descripcion: $('#producto_descripcion').val(),
        idunidadmedida: $('#producto_unidadmedida').val(),
        unidadmedida: $('#producto_unidadmedida').val(),
        precio: $('#producto_preciounidad').val(),
        cantidad: $('#producto_cantidad').val(),
        subtotal: $('#producto_subtotal').val(),
        igv: $('#producto_igv').val(),
        importe: $('#producto_total').val(),
        estado: 'V'
    };

    var su = jQuery('#detalle_documento').addRowData(idarticulo, data, 'last');
    calcular_totales_documento();
    $("#vm_agregar_articulo").modal("hide");
}

function calcular_totales_producto() {
    var igv_percent = parseFloat((18/100) + 1);
    var precioarticulo = 0;
    var cantidad = 0;

    if($('#producto_preciounidad').val() == '' || $('#producto_preciounidad').val() <= 0 || isNaN($('#producto_preciounidad').val())) {
        precioarticulo = 0;
    } else {
        precioarticulo = parseFloat($('#producto_preciounidad').val());
    }

    if($('#producto_cantidad').val() == '' || $('#producto_cantidad').val() <= 0 || isNaN($('#producto_cantidad').val())) {
        cantidad = 0;
    } else {
        cantidad = parseFloat($('#producto_cantidad').val());
    }

    var total = round_math(parseFloat(precioarticulo) * parseFloat(cantidad), 2);
    var subtotal = parseFloat(total) / parseFloat(igv_percent);
    var igv = parseFloat(total) - parseFloat(subtotal);
    
    $('#producto_subtotal').val(round_math(subtotal, 2));
    $('#producto_igv').val(round_math(igv, 2));
    $('#producto_total').val(round_math(total, 2));
}

function calcular_totales_documento() {
    var sub_total = 0;
    var igv = 0;
    var importe = 0;
    var grid = jQuery("#detalle_documento");
    var ids = grid.jqGrid('getDataIDs');
    for (var i = 0; i < ids.length; i++) {
        var id = ids[i];
        sub_total = parseFloat(sub_total) + parseFloat(grid.jqGrid('getCell', id, 'subtotal'));
        igv = parseFloat(igv) + parseFloat(grid.jqGrid('getCell', id, 'igv'));
        importe = parseFloat(importe) + parseFloat(grid.jqGrid('getCell', id, 'importe'));
    }
    $("#subtotal_documento").html(round_math(sub_total, 2));
    $("#txt_subtotal_comprobante").val(round_math(sub_total, 2));

    $("#igv_documento").html(round_math(igv, 2));
    $("#txt_igv_comprobante").val(round_math(igv, 2));

    $("#total_documento").html(round_math(importe, 2));
    $("#txt_total_comprobante").val(round_math(importe, 2));

    var numletras = NumeroALetras(importe);
    $("#txt_total_letras").val(numletras);
}

function eliminar_producto_detalle() {
    var rowid = jQuery('#detalle_documento').jqGrid('getGridParam', 'selrow');
    var $grid = jQuery("#detalle_documento");
    $grid.jqGrid('delRowData', rowid);
    calcular_totales_documento();
}

function round_math(numero, decimales) {
    var flotante = parseFloat(numero);
    var resultado = Math.round(flotante * Math.pow(10, decimales)) / Math.pow(10, decimales);
    return resultado;
}

function rellenar_tipo_documento(tipocomprobante) {
    $("#cliente_tipodocumento").empty();
    $(".content_debito_credito").hide();

    var fecha_actual = new Date().toJSON().slice(0, 10);
    var numaleatorio = Math.floor(100000 + Math.random() * 900000);

    $("#fecha_comprobante").val(fecha_actual);
    $("#numero_comprobante").val(numaleatorio);
    var num_comprobante_modificado = 'F001-' + (numaleatorio + 10);
    $("#num_comprobante_modificado").val(num_comprobante_modificado);

    if(tipocomprobante == '01') { //Factura Electrónica
        $('#cliente_tipodocumento').append($("<option></option>")
            .attr("value",'6')
            .text('RUC'));
        $('#cliente_tipodocumento').trigger("change");        

        $("#serie_comprobante").val('F001');
        $(".search_document").show();
    }

    if(tipocomprobante == '07') { //Nota de Crédito
        $(".content_debito_credito").show();
        $(".notadebito_motivo").hide();
        $(".notacredito_motivo").show();

        $('#cliente_tipodocumento').append($("<option></option>")
            .attr("value",'6')
            .text('RUC'));
        $('#cliente_tipodocumento').trigger("change");

        $("#serie_comprobante").val('F001');
        $(".search_document").show();
    }

    if(tipocomprobante == '08') { //Nota de Débito
       $(".content_debito_credito").show();
        $(".notadebito_motivo").show();
        $(".notacredito_motivo").hide();

        $('#cliente_tipodocumento').append($("<option></option>")
            .attr("value",'6')
            .text('RUC'));
        $('#cliente_tipodocumento').trigger("change");

        $("#serie_comprobante").val('F001');
        $(".search_document").show();
    }

    if(tipocomprobante == '03') { //Boleta
        $('#cliente_tipodocumento').append($("<option></option>")
            .attr("value",'1')
            .text('DNI'));

        $('#cliente_tipodocumento').append($("<option></option>")
            .attr("value",'0')
            .text('Doc.Trib.NO.Dom.Sin.RUC'));

        $('#cliente_tipodocumento').append($("<option></option>")
            .attr("value",'4')
            .text('Carné de Extranjería'));

        $('#cliente_tipodocumento').append($("<option></option>")
            .attr("value",'7')
            .text('Pasaporte'));

        $('#cliente_tipodocumento').append($("<option></option>")
            .attr("value",'A')
            .text('Ced. Diplomática de Identidad'));

        $('#cliente_tipodocumento').trigger("change");

        $("#serie_comprobante").val('B001');
        $(".search_document").show();
    }
}

function search_document() {
    
    $("#icon_search_document").hide();
    $("#icon_searching_document").show();
    $(".search_document").prop('disabled', true);

    var tipodoc = $("#cliente_tipodocumento").val().toString();
    var numdoc = $("#cliente_numerodocumento").val();
    if(tipodoc == '6') { //RUC
        /*var resp = validar_numero_ruc(numdoc);
        if(resp == 1) {
            consultar_ruc(numdoc);
        }*/
        consultar_ruc(numdoc);
    } else if (tipodoc == '1') { //DNI
        consultar_dni(numdoc);
    } else {

    }

}

function consultar_dni(dni) {
    $.ajax({
        url : 'https://sistemadefacturacionelectronicasunat.com/api/persona/'+dni,
        method :  'GET',
        dataType : "json"
    }).then(function(data){
        if(data.respuesta == 'ok') {
            $("#cliente_nombre").val(data.nombre);
        } else {
            $("#cliente_nombre").val(data.mensaje);
        }
        $("#icon_search_document").show();
        $("#icon_searching_document").hide();
        $(".search_document").prop('disabled', false);

        console.log(data);

    }, function(reason){
        swal({
            title: 'ERROR',
            text: 'Error al conectarse a la SUNAT, recarga la página e inténtalo nuevamente!',
            html: true,
            type: "error",
            confirmButtonText: "Ok",
            confirmButtonColor: "#2196F3"
        }, function(){
            $("#icon_search_document").show();
            $("#icon_searching_document").hide();
            $(".search_document").prop('disabled', false);
        });
    });
}

function consultar_ruc(ruc) {
    $.ajax({
        url : 'https://sistemadefacturacionelectronicasunat.com/api/empresa/'+ruc,
        method :  'GET',
        dataType : "json"
    }).then(function(data){
        $("#icon_search_document").show();
        $("#icon_searching_document").hide();
        $(".search_document").prop('disabled', false);

        $("#cliente_nombre").val(data.razon_social);
        $("#cliente_direccion").val(data.direccion);
        console.log(data);
    }, function(reason){
        swal({
            title: 'ERROR',
            text: 'Error al conectarse a la SUNAT, recarga la página e inténtalo nuevamente!',
            html: true,
            type: "error",
            confirmButtonText: "Ok",
            confirmButtonColor: "#2196F3"
        }, function(){
            $("#icon_search_document").show();
            $("#icon_searching_document").hide();
            $(".search_document").prop('disabled', false);
        });
    });
}

function inicializar_tabla_detalle() {
	$.jgrid.no_legacy_api = true;
	$.jgrid.useJSON = true;

	detalle_documento = $('#detalle_documento');

	detalle_documento = detalle_documento.jqGrid({
        url: '',
        datatype: 'json',
        mtype: 'POST',
        colNames: [
            'idarticulo', 
            'Codigo',
            'Descripcion',
            'idunidadmedida',
            'Und/Medida',
            'Precio',
            'Cantidad',
            'Sub.Total',
            'Igv',
            'Importe',
            'Estado'
        ],
        colModel: [
            { name: 'idarticulo', index: '1', hidden: true},
            { name: 'codigo', index: '2', width: 90 },
            { name: 'descripcion', index: '3', width: 360, },
            { name: 'idunidadmedida', index: '4', hidden: true },
            { name: 'unidadmedida', index: '5', width: 120 },
            { name: 'precio', index: '6', width: 80, align: "right", sorttype: 'float' },
            { name: 'cantidad', index: '6', width: 80, align: "right", sorttype: 'float' },
            { name: 'subtotal', index: '6', width: 85, align: "right", sorttype: 'float' },
            { name: 'igv', index: '6', width: 60, align: "right", sorttype: 'float' },
            { name: 'importe', index: '6', width: 90, align: "right", sorttype: 'float' },
            { name: 'estado', index: '8', hidden: true }
        ],
        height: 100,
        shrinkToFit: false,
        rowNum: 0,
        loadOnce: true,
        viewrecords: true,
        gridview: true,
        cellEdit: true,
        sortname: 'codigo',
        cellsubmit: 'clientArray',
        editurl: 'clientArray',
        beforeRequest: function () {
            responsive_table($(".jqGrid"));
        },
        jsonReader: {
            repeatitems: false,
            root: 'lstLista'
        },
        gridComplete: function () {
            //SE EJECUTA CUANDO AGREGAS NUEVO REGISTRO A LA GRILLA
        },
        afterSaveCell: function (rowid, name, val, iRow, iCol) {
            //SE EJECUTA CUANDO MODIFICAMOS UN REGISTRO
            //                        calculateImporte();
            //                        calculateTotal();
        }
    });
}

function responsive_table(table) {
    table.find('.ui-jqgrid').addClass('clear-margin span12').css('width', '');
    table.find('.ui-jqgrid-view').addClass('clear-margin span12').css('width', '');
    table.find('.ui-jqgrid-view > div').eq(1).addClass('clear-margin span12').css('width', '').css('min-height', '0');
    table.find('.ui-jqgrid-view > div').eq(2).addClass('clear-margin span12').css('width', '').css('min-height', '0');
    table.find('.ui-jqgrid-sdiv').addClass('clear-margin span12').css('width', '');
    table.find('.ui-jqgrid-pager').addClass('clear-margin span12').css('width', '');
}

function inicializar_controles() {

    $('.stepy-step').find('.button-next').addClass('btn btn-primary');
    $('.stepy-step').find('.button-back').addClass('btn btn-default');

    // Primary
    $(".control-primary").uniform({
        radioClass: 'choice',
        wrapperClass: 'border-primary-600 text-primary-800'
    });

    // Danger
    $(".control-danger").uniform({
        radioClass: 'choice',
        wrapperClass: 'border-danger-600 text-danger-800'
    });

    // Success
    $(".control-success").uniform({
        radioClass: 'choice',
        wrapperClass: 'border-success-600 text-success-800'
    });

    // Warning
    $(".control-warning").uniform({
        radioClass: 'choice',
        wrapperClass: 'border-warning-600 text-warning-800'
    });

    // Info
    $(".control-info").uniform({
        radioClass: 'choice',
        wrapperClass: 'border-info-600 text-info-800'
    });

}

function validar_numero_ruc(numruc) {
    var regEx = /\d{11}/;

    var ruc = new String(numruc);

    if (ruc.length != 11) {
        swal({
            title: 'ERROR',
            text: 'El RUC: '+ numruc +' es Inválido.',
            html: true,
            type: "error",
            confirmButtonText: "Ok",
            confirmButtonColor: "#2196F3"
        }, function(){
            $("#icon_search_document").show();
            $("#icon_searching_document").hide();
            $(".search_document").prop('disabled', false);
        });
        return 0;
    }

    if (regEx.test(ruc)) {
        var factores = new String("5432765432");
        var ultimoIndex = ruc.length - 1;
        var sumaTotal = 0, residuo = 0;
        var ultimoDigitoRUC = 0, ultimoDigitoCalc = 0;

        for (var i = 0; i < ultimoIndex; i++) {
            sumaTotal += (parseInt(ruc.charAt(i)) * parseInt(factores.charAt(i)));
        }
        residuo = sumaTotal % 11;

        ultimoDigitoCalc = (residuo == 10) ? 0 : ((residuo == 11) ? 1 : (11 - residuo) % 10);
        ultimoDigitoRUC = parseInt(ruc.charAt(ultimoIndex));

        if (ultimoDigitoRUC == ultimoDigitoCalc) {
            //alert("¡El RUC " + ruc + " SÍ es válido!.");
            return 1;
        } else {
            swal({
                title: 'ERROR',
                text: 'El RUC: '+ numruc +' es Inválido.',
                html: true,
                type: "error",
                confirmButtonText: "Ok",
                confirmButtonColor: "#2196F3"
            }, function(){
                $("#icon_search_document").show();
                $("#icon_searching_document").hide();
                $(".search_document").prop('disabled', false);
            });
            return 0;
        }
    } else {
        swal({
            title: 'ERROR',
            text: 'El RUC debe constar de 11 caracteres numéricos. Si no tiene RUC, por favor selecciona BOLETA.',
            html: true,
            type: "error",
            confirmButtonText: "Ok",
            confirmButtonColor: "#2196F3"
        }, function(){
            $("#icon_search_document").show();
            $("#icon_searching_document").hide();
            $(".search_document").prop('disabled', false);
        });
        $("#cliente_numerodocumento").focus();
        return 0;
    }

    return 1;
}