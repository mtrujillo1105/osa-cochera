jQuery(document).ready(function () {
    base_url = $("#base_url").val();
    tipo_cuenta = $("#tipo_cuenta").val();

    /*if($('#ruc_cliente').val()!=''){
     $('#estado_pago2').attr("onchange","BuscarxEstadoPago()");
     }
     */
    /*function BuscarxEstadoPago(){
     document.forms['frmCuenta'].action = base_url+"index.php/tesoreria/cuentas/nuevo/"+tipo_cuenta;
     $("#frmCuenta").submit();
     }*/
     $("#linkVerSerieNum").click(function () {
        var temp = $("#linkVerSerieNum p").html();
        var serienum = temp.split('-');
        $("#compra_serie").val(serienum[0]);
        $("#compra_numero").val(serienum[1]);
    });
    $("#grabarCuenta").click(function () {

        if (confirm('¿Está seguro de grabar este pago?')) {
            $('img#loading').css('visibility', 'visible');
            url = base_url + "index.php/tesoreria/cuentas/grabar";

            dataString = $('#frmCuenta').serialize();

            $.post(url, dataString, function (data) {
                $('img#loading').css('visibility', 'hidden');
                switch (data.result) {
                    case 'ok':
                        $('#monto').val('');
                        $('#forma_pago').val('1');
                        $('.formaPago').hide();
                        
                        $("#idmonedacaja").hide();
                        $("#tdCuentaCaja").hide();
                        $("#tdBancoCaja").hide();
                        $("#idcajadiaria").val("");
                        mostrar_cuentas();
                        break;
                    case 'error':
                        $('input[type="text"][readonly!="readonly"], select, textarea').css('background-color', '#FFFFFF');
                        $('#' + data.campo).css('background-color', '#FFC1C1').focus();
                        break;
                }
            }, 'json');
        }
    });


    $("#limpiarCuenta").click(function () {
        url = base_url + "index.php/tesoreria/cuentas/listar/" + tipo_cuenta + "/0/1";
        top.location = url;
    });

    /*

     $("#limpiarCuenta").click(function(){
     url = base_url+"index.php/tesoreria/cuentas/listar/"+tipo_cuenta;
     top.location=url;
     });*/
    $("#cancelarCuenta").click(function () {
        url = base_url + "index.php/tesoreria/cuentas/listar/" + tipo_cuenta;
        location.href = url;
    });
    $("#buscarCuenta").click(function () {
        //document.forms['form_busquedaCuenta'].action = base_url+"index.php/tesoreria/cuentas/listar/"+tipo_cuenta;
        $("#form_busqueda").submit();
    });

    $('#forma_pago').change(function (event) {
        var name_pago = event.target.options[event.target.options.selectedIndex].text.toLowerCase();

        $('.formaPago').hide();

        switch(name_pago) {
            case "efectivo":
                $('#formaPago' + $(this).val()).show();
                $("#idcajadiaria").val("");
                $("#banco").val("");
                $("#ctacte").val("");
                break;
            case "deposito":
                $(".deposito").show();
                break;
            case "transferencia":
                $("#formaPago7").show();
                break;
            case "cheque":
                $("#formaPago2").show();
                $("#formaPago3").show();
                break;
            case "nota de credito":
                $("#formaPago5").show();
                break;
            case "canje por factura":
                $("#formaPago4").show();
                break;
            case "descuento":
                $("#formaPago6").show();
                break;
            default:
                $("#tdBancoCaja").hide();
                $('#tdCuentaCaja').hide();
                $('#idmonedacaja').hide();
                
                
                $('#idbancoscaja').val("");
                $("#idcuentacaja").val("");
                $("#idcajadiaria").val("");
                $("#divtxtmoneda").hide();
        }         
            
    });

    $('#aplicarpago').click(function () {
        if ($('#tdc').val() == '') {
            alert('Debe ingresar el Tipo de Cambio del dia');
            top.location = base_url + "index.php/index/inicio";
        }
        if ($(this).attr('name') == 'aplica') {
            mostrar_cuentas('1');
        }
        else {
            mostrar_cuentas('0');
        }
    });

    $('#moneda').change(function () {
        mostrar_cuentas();
    });

    $('#verpagos').click(function () {
        if (tipo_cuenta == '1') {
            if ($('#cliente').val() == '') {
                alert('Seleccione el cliente.');
                $('#cliente').focus();
            } else {
                url = base_url + "index.php/tesoreria/pago/listar_ultimos/" + tipo_cuenta + "/" + $('#cliente').val();
                location.href = url;
            }
        } else {
            if ($('#proveedor').val() == '') {
                alert('Seleccione el proveedor.');
                $('#proveedor').focus();
            } else {
                url = base_url + "index.php/tesoreria/pago/listar_ultimos/" + tipo_cuenta + "/" + $('#proveedor').val();
                location.href = url;
            }
        }


    });

    $('#tipo_docu').change(function () {
        tipo_doc = $('#tipo_docu').val();
        //mostrar_cuentas($('#aplica_pago').val(),estadopago)
        if (tipo_doc == 'FACTURA') {
            $('.BOLETA').hide();
            $('.FACTURA').show();
        } else if (tipo_doc == 'BOLETA') {
            $('.BOLETA').show();
            $('.FACTURA').hide();
        } else if (tipo_doc == 'T') {
            $('.BOLETA').show();
            $('.FACTURA').show();
        }
        //mostrar_cuentas(TRUE,estadopago)
    });

    $('#estado_pago2').change(function () {
        estadopago = $('#estado_pago2').val();
        //mostrar_cuentas($('#aplica_pago').val(),estadopago)
        if (estadopago == 'C') {
            $('.Pendiente').hide();
            $('.Cancelado').show();
        } else if (estadopago == 'P') {
            $('.Pendiente').show();
            $('.Cancelado').hide();
        } else if (estadopago == 'T') {
            $('.Pendiente').show();
            $('.Cancelado').show();
        }
        //mostrar_cuentas(TRUE,estadopago)
    });

    /*$('#ruc_cliente').keyup(function (e) {
        var key = e.keyCode || e.which;
        if (key == 13) {
            if ($(this).val() != '') {
                $('#linkSelecCliente').attr('href', base_url + 'index.php/empresa/cliente/ventana_selecciona_cliente/' + $('#cliente').val()).click();
            }
        }
    });

    $('#nombre_cliente').keyup(function (e) {
        var key = e.keyCode || e.which;
        if (key == 13) {
            if ($(this).val() != '') {
                $('#linkSelecCliente').attr('href', base_url + 'index.php/empresa/cliente/ventana_selecciona_cliente/' + $('#nombre_cliente').val()).click();
            }
        }
    });

    $('#ruc_proveedor').keyup(function (e) {
        var key = e.keyCode || e.which;
        if (key == 13) {
            if ($(this).val() != '') {
                $('#linkSelecProveedor').attr('href', base_url + 'index.php/empresa/proveedor/ventana_selecciona_proveedor/' + $('#ruc_proveedor').val()).click();
            }
        }
    });

    $('#nombre_proveedor').keyup(function (e) {
        var key = e.keyCode || e.which;
        if (key == 13) {
            if ($(this).val() != '') {
                $('#linkSelecProveedor').attr('href', base_url + 'index.php/empresa/proveedor/ventana_selecciona_proveedor/' + $('#nombre_proveedor').val()).click();
            }
        }
    });

    $('#ruc_cliente').keyup(function (e) {
        var key = e.keyCode || e.which;
        if (key == 13) {
            if ($(this).val() != '') {
                obtener_cliente()
            }
        }
    });

    $('#ruc_proveedor').keyup(function (e) {
        var key = e.keyCode || e.which;
        if (key == 13) {
            if ($(this).val() != '') {
                obtener_proveedor()
            }
        }
    });*/

});


function obtener_cliente() {
    var numdoc = $("#ruc_cliente").val();
    $('#cliente, #nombre_cliente').val('');

    if (numdoc == '')
        return false;

    var url = base_url + "index.php/empresa/cliente/JSON_buscar_cliente/" + numdoc;
    $.getJSON(url, function (data) {
        $.each(data, function (i, item) {
            if (item.EMPRC_RazonSocial != '') {
                $('#nombre_cliente').val(item.EMPRC_RazonSocial);
                $('#cliente').val(item.CLIP_Codigo);
                $('#codproducto').focus();
                $('#aplicarpago').attr('name', 'aplica');
                mostrar_cuentas();
                //datos_cliente(/*$('#cliente').val(),$('#ruc_cliente').val(),$('#nombre_cliente').val()*/);
            }
            else {
                $('#nombre_cliente').val('No se encontró ningún registro');
                $('#linkVerCliente').focus();
            }
        });
    });

    return true;
}


function obtener_proveedor() {
    var numdoc = $("#ruc_proveedor").val();
    $("#proveedor, #nombre_proveedor").val('');

    if (numdoc == '')
        return false;

    var url = base_url + "index.php/empresa/proveedor/obtener_nombre_proveedor/" + numdoc;
    $.getJSON(url, function (data) {
        $.each(data, function (i, item) {
            if (item.EMPRC_RazonSocial != '') {
                $('#nombre_proveedor').val(item.EMPRC_RazonSocial);
                $('#proveedor').val(item.PROVP_Codigo);
                $('#codproducto').focus();
                $('#aplicarpago').attr('name', 'aplica');
                mostrar_cuentas();
            }
            else {
                $('#nombre_proveedor').val('No se encontró ningún registro');
                $('#linkVerProveedor').focus();
            }
        });
    });
    return true;
}

function ver_pagos(cuenta) {
    location.href = base_url + "index.php/tesoreria/pago/listar/" + cuenta;
}

function comprobante_ver_pdf_conmenbrete2(fondo, comprobante) {
    tipo_docu = "V";
    var url = base_url + "index.php/ventas/notacredito/comprobante_ver_pdf_conmenbrete/" + fondo + "/" + comprobante + "/" + tipo_docu;
    window.open(url, '', "width=800,height=600,menubars=no,resizable=no;");
}

function ver_comprobante_pdf(comprobante, tipo_docu) {
    tipo_oper = $("#tipo_oper").val();
    var url = base_url + "index.php/ventas/comprobante/comprobante_ver_pdf/" + comprobante + "/" + tipo_docu;
    window.open(url, '', "width=800,height=600,menubars=no,resizable=no;")
}

function ver_cuentas_pdf(codigo, tipoPersona = null, tipo_cuenta = 'V-A'){
    
    var url = base_url + "index.php/tesoreria/cuentas/generarPdfCuentas/" + codigo + "/" + tipoPersona + "/" + tipo_cuenta;
    window.open(url, '', "width=800,height=600,menubars=no,resizable=no;")
}

function comprobante_ver_pdf_conmenbrete(comprobante, tipo_docu, tipo_cuenta) {
    tipo_oper = $("#tipo_oper").val();
    var url = base_url + "index.php/ventas/comprobante/comprobante_ver_pdf/"+comprobante+"/a4";
    window.open(url, '', "width=800,height=600,menubars=no,resizable=no;");
}

function mostrar_cuentas(aplica_pago) {
    var monto = $('#monto').val();

    if (monto == '') {
        monto = '0';
    }
    var moneda = $('#moneda').val();

    var tdc = $('#tdc').val();

    if (!aplica_pago) {
        aplica_pago = '0';
    }

    if (tipo_cuenta == '1') {
        codigo = $('#cliente').val();
        if (codigo == '') {
            $('#ruc_cliente').focus();
            alert('Seleccione el cliente.');
            return false;
        }
    } else {
        codigo = $('#proveedor').val();
        if (codigo == '') {
            $('#ruc_proveedor').focus();
            alert('Seleccione el proveedor.');
            return false;
        }
    }

    if (aplica_pago == '1' && monto == 0) {
        alert('Ingrese el monto a pagar.');
        $('#monto').focus();
        return false;
    }
    if (aplica_pago == '1') {
        $('#aplicarpago img').attr('src', base_url + 'images/botonpagoretirar.png');
        $('#aplicarpago').attr('name', 'retira');
    } else {
        $('#aplicarpago img').attr('src', base_url + 'images/botonpago.png');
        $('#aplicarpago').attr('name', 'aplica');
    }

    if (monto != '') { //gcbq
        i = 0;
        var posiciones = new Array();
        $('#tblDetallePago').each(function () {
            $(this).find('tr').each(function () {

                if ($(this).attr('id') != '') {

                    inputvalor = $(this).attr('id');
                    $('#value' + inputvalor).val();
                    posiciones[i] = $('#value' + inputvalor).val();

                    i++;
                }

            })
        });
    }
    //order
    //alert(order);
    if (typeof(order) === "undefined") {
        order = '';
    } else {
        if (order == 'ASC') {
            $('#ordenar').val('DESC');
        } else {
            $('#ordenar').val('ASC');
        }
    }

    //

    // url = base_url+"index.php/tesoreria/cuentas/JSON_cuentas_pendientes/"+tipo_cuenta+"/"+codigo+"/"+monto+"/"+moneda+"/"+tdc+"/"+aplica_pago+"/"+posiciones;
    url = base_url + "index.php/tesoreria/cuentas/JSON_cuentas_pendientes/";

    $("#tblDetallePago").html("");
    $('#saldo').val('');
    var saldo_total = 0;
    var avance_total = 0;
    var monto_total = 0;
    var saldo_restante = 0;
    var fila = "";
    var fila_total = "";

    $.post(url, {
        tipo_cuenta: tipo_cuenta,
        codigo: codigo,
        monto: monto,
        moneda: moneda,
        tdc: tdc,
        aplica_pago: aplica_pago,
        posiciones: posiciones,
        order: order
    }, function (data) {
        error = data.errores;
        if(error == 'warning'){
            //mostrar_cuentas();
        }else {
            $.each(data, function (i, item) {
                if (i % 2 == 0) {
                    clase = "itemParTabla";
                } else {
                    clase = "itemImparTabla";
                }

                array_estado = obtener_estado_formato(item.avance, item.total).split("_|_");
                fila = '<tr id="tr' + (i + 1) + '"  class="' + clase + ' ' + array_estado[1] + ' ' + item.desc_doc + '" >';
                fila += '<td width="4%" ><div align="center" >' + (i + 1) + '<input type="hidden" name="posiciones_pagos[]" value="' + item.cod_documento + '" /></div></td>';
                fila += '<td width="7%"><div align="center">' + item.fecha + '</div></td>';
                fila += '<td width="15%"><div align="center">' + item.desc_doc + '</div></td>';
                fila += '<td width="13%"><div align="center">' + item.serie + '-' + item.numero + '</div></td>';
                fila += '<td width="7%"><div align="center">' + item.moneda + '</div></td>';
                fila += '<td width="7%"><div align="center">' + item.total + '</div></td>';
                fila += '<td width="7%"><div align="center">' + item.avance + '</div></td>';
                fila += '<td width="10%"><div align="center">' + item.saldo + '</div></td>';
                fila += '<td width="18%">' + array_estado[0] + '</td>';
                fila += '<input type="hidden" id="valuetr' + (i + 1) + '" name="nota_codDocumento'+(i+1)+'"  value="' + item.cod_documento + '" >';
                fila += '<input type="hidden" id="nota_tipoDocumento' + (i + 1) + '" name="nota_tipoDocumento'+(i+1)+'"  value="' + item.tipo_doc + '" >';
                fila += '<td width="10%"><div align="center"><a href="javascript:;" onclick="comprobante_ver_pdf_conmenbrete(' + item.cod_documento;
                fila += ",'" + item.tipo_doc + "'," + tipo_cuenta;
                fila += ')" target="_parent"><img src="' + base_url + 'images/pdf.png" width="16" height="16" border="0" title="Ver PDF"></a></div></td>';

                fila += '<td width="2%">';
                fila += '<img style="cursor:pointer;" src="' + base_url + 'images/flecha_arriba.png" onClick="moverpago(' + (i + 1) + ',0);" width="10" height="10" border="0" title="subir">';
                fila += '</td>';
                fila += '<td width="2%">';
                fila += '<img style="cursor:pointer;" src="' + base_url + 'images/flecha_abajo.png"  onClick="moverpago(' + (i + 1) + ',1);" width="10" height="10" border="0" title="bajar">';
                fila += '</td>';

                fila += '<td width="2%">';
                fila += '<img style="cursor:pointer;" src="' + base_url + 'images/iniciosubir.png" onClick="moverpago(' + (i + 1) + ',2);" width="10" height="10" border="0" title="Al Inicio">';
                fila += '</td>';
                fila += '<td width="2%">';
                fila += '<img style="cursor:pointer;" src="' + base_url + 'images/finalbajar.png"  onClick="moverpago(' + (i + 1) + ',3);" width="10" height="10" border="0" title="Al Final">';
                fila += '</td>';
                fila += '</tr>';
                //$("#tblDetallePago").append(fila);
                /*if(array_estado[1]=='Pendiente'){
                 $('.Cancelado').hide();
                 }*/
                saldo_total = parseFloat(item.saldo_total_int);
                avance_total += parseFloat(item.avance_int);
                monto_total += parseFloat(item.total_int);
                saldo_restante += parseFloat(item.saldo_int);
                $("#tblDetallePago").append(fila);
            });

            fila_total = "<tr id='trfinaltotal' ><td colspan='3'></td><td style='text-align:right;'><b>Total:</b></td><td></td><td style='text-align:center;'><b>" + monto_total + "</b></td><td style='text-align:center;'><b>" + avance_total + "</b></td><td style='text-align:center;'><b>" + saldo_restante + "</b></td><td colspan='2'></td></tr>";
            $("#tblDetallePago").append(fila_total);
            
            fila_reporte = "<tr id='fila_reporte'><td colspan='14' style='text-align:right; background-color:rgba(244,244,244,.8)'><a href='javascript:;' style='font-weight:bold' onclick=ver_cuentas_pdf('"+ codigo +"','"+ tipo_cuenta +"','V-A') target='_parent'><img src='"+ base_url +"images/icono_imprimir.png' width='16' height='16' border='0' title='Imprimir'> Imprimir Reporte</a></td></tr>";
            $("#tblDetallePago").append(fila_reporte);
            $('#saldo').val(saldo_total);
        }
    }, 'json');

    return true;

}

function ordenar() {
    order = $('#ordenar').attr('value');
//alert(order);
    mostrar_cuentas(0, order);
}

function moverpago(a, op) {
//alert(a);
    if (op == 0) {
        ant = $("#tr" + a).prev().attr('id');
        $("#tr" + a).css('background', 'white');
        $("#tblDetallePago tr ").css('color', 'black');
        $("#tr" + a).css('color', 'red');
        $("#" + ant).before($("#tr" + a));

//$( "#"+ant ).prepend( $( "#"+a ) );
//ant = $( "#"+a ).next().attr('id');
//$( "#"+ant ).after( $( "#"+a ) );
//$( "#tblDetallePago " ).append( $( "#"+a ) );
    }
    if (op == 1) {
//$( "#"+ant ).prepend( $( "#"+a ) );
        ant = $("#tr" + a).next().attr('id');
        $("#tr" + a).css('background', 'white');
        $("#tblDetallePago tr ").css('color', 'black');
        $("#tr" + a).css('color', 'red');
        $("#" + ant).after($("#tr" + a));

    }
    if (op == 2) {
//al inicio
//$( "#"+ant ).prepend( $( "#"+a ) );
        $("#tr" + a).css('background', 'white');
        $("#tblDetallePago tr ").css('color', 'black');
        $("#tr" + a).css('color', 'red');
        $("#tblDetallePago").prepend($("#tr" + a));

    }
    if (op == 3) {
//al final
//$( "#"+ant ).prepend( $( "#"+a ) );
        $("#tr" + a).css('background', 'white');
        $("#tblDetallePago tr ").css('color', 'black');
        $("#tr" + a).css('color', 'red');
        $("#trfinaltotal").before($("#tr" + a));

    }


}

function obtener_estado_formato(avance, total) {
    if (avance == total)
        result = "<div style='width:100px; margin-left:auto; margin-right:auto; height:17px; background-color: #00D269; text-align:center; cursor:help;' title='Cancelado'>Cancelado</div>_|_Cancelado";
    else if (parseFloat(avance) > 0)
        result = "<div style='width:100px; margin-left:auto; margin-right:auto; height:17px; background-color: #FFB648; text-align:center; cursor:help;' title='Pendiente con Avance'>Pendiente (AV)</div>_|_Pendiente";
    else
        result = "<div style='width:100px; margin-left:auto; margin-right:auto; height:17px; background-color: #FF6464; text-align:center; cursor:help;' title='Pendiente'>Pendiente</div>_|_Pendiente";

    return result;
}