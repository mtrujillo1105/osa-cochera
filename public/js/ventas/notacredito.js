var limite_detalle = 15;
function getLimite() {
    return limite_detalle;
}

function setLimite(limite) {
    limite_detalle = limite;
}

jQuery(document).ready(function () {
    base_url = $("#base_url").val();
    tipo_oper = $("#tipo_oper").val();
    tipo_docu = $("#tipo_docu").val();
    contiene_igv = $("#contiene_igv").val();

    $("#nuevaComprobante").click(function () {
        url = base_url + "index.php/ventas/notacredito/comprobante_nueva" + "/" + tipo_oper + "/" + tipo_docu;
        location.href = url;
    });

    /*$("#grabarComprobante").click(function(){
     $('#frmComprobante').submit();
     });*/

    $("#grabarComprobante").click(function () {

        if ( $("#origenDocumento").val() == "" || $("#guiaReferente").val() == "" ){
            alert("Debe asociar una factura, boleta o comprobante primero.");
            return false;
        }

        var n = document.getElementById('tempde_tbl').rows.length;
        if (n == 0) {
            alert("Ingrese un producto.");
            return false;
        } else if ($("#serie").val() == "") {
            $("#serie").focus();
            alert("Ingrese la serie.");
            return false;
        } else if ($('#observacion').val() == "") {
            $("#observacion").focus();
            alert("Ingrese una observacion.");
            return false;
        } else if ($("#moneda").val() == '') {
            alert("Seleccione tipo moneda.");
            $("#moneda option[value=1]").attr("selected", true);
            return false;
        } else if($('#nombre_cliente').val() == "" || $('#ruc_cliente').val() == ""){
            alert('Seleccione un cliente');
            return false;
        } else if($('#nombre_proveedor').val() == "" || $('#ruc_proveedor').val() == "") {
            alert('Seleccione un cliente');
            return false;
        }else if($('#moneda').val() == ""){
            alert('Seleccione un tipo de moneda');
            return false;
        } else {
            $('img#loading').css('visibility', 'visible');
            var codigo = $('#codigo').val();

            if (codigo == '') {
                url = base_url + "index.php/ventas/notacredito/comprobante_insertar";
            }
            else {
                url = base_url + "index.php/ventas/notacredito/comprobante_modificar";
            }

            dataString = $('#frmComprobante').serialize();

            $.post(url, dataString, function (data) {
                    $('img#loading').css('visibility', 'hidden');
                    $('#grabarComprobante').hide('fast');
                    //console.log(data.mensaje);

                    if (data.mensaje == "SUCCESS") {
                        location.href = base_url + "index.php/ventas/notacredito/comprobantes" + "/" + tipo_oper + "/" + tipo_docu;
                    } else {
                        $('#notaMensaje').html(data.descripcion);
                        $('#grabarComprobante').show('fast');
                    }
                }, 'json'
            );
        }
    });
    $("#limpiarComprobante").click(function () {
        url = base_url + "index.php/ventas/notacredito/comprobantes" + "/" + tipo_oper + "/" + tipo_docu  + "/0/1";
        location.href = url;
    });
    $("#cancelarComprobante, #cancelarImprimirComprobante").click(function () {
        $.fancybox.close();
        url = base_url + "index.php/ventas/notacredito/comprobantes" + "/" + tipo_oper + "/" + tipo_docu;
        location.href = url;
    });

    $("#repo1").click(function () {
        $("#divRepo1").show();
        $("#divRepo2").hide();
        $("#divRepo3").hide();
        $("#divRepo4").hide();
        $("#divRepo5").hide();
        $("#divRepo6").hide();
    });

    $("#repo6").click(function () {
        $("#divRepo1").hide();
        $("#divRepo2").hide();
        $("#divRepo3").hide();
        $("#divRepo4").hide();
        $("#divRepo5").hide();
        $("#divRepo6").show();
    });

    $("#repo2").click(function () {
        $("#divRepo1").hide();
        $("#divRepo3").hide();
        $("#divRepo4").hide();
        $("#divRepo5").hide();
        $("#divRepo6").hide();
        url = base_url + "index.php/ventas/notacredito/estadisticas";
        $.post(url, '', function (data) {
            $('#divRepo2').html(data).show();
        });
    });

    $("#repo3").click(function () {
        $("#divRepo1").hide();
        $("#divRepo2").hide();
        $("#divRepo4").hide();
        $("#divRepo5").hide();
        $("#divRepo3").show();
        $("#divRepo6").hide();
    });

    $("#repo4").click(function () {
        $("#divRepo1").hide();
        $("#divRepo2").hide();
        $("#divRepo3").hide();
        $("#divRepo5").hide();
        $("#divRepo4").show();
        $("#divRepo6").hide();
    });

    $("#repo5").click(function () {
        $("#divRepo1").hide();
        $("#divRepo2").hide();
        $("#divRepo3").hide();
        $("#divRepo4").hide();
        $("#divRepo5").show();
        $("#divRepo6").hide();
    });

    $("#imprimirComprobante").click(function () {
        if ($('#codigo').val() == '') {
            alert('Ha ocurrido un error, no se puede realizar la impresión')
            return false;
        }
        ver_comprobante_pdf($('#codigo').val());
        $("#cancelarComprobante").click();
        return true;
    });

    $("#buscarComprobante").click(function () {
        $("#form_busqueda").submit();
    });
    $("#presupuesto").change(function () {
        if (this.value != '')
            $("#ordencompra").val('');
    });
    $("#ordencompra").change(function () {
        if (this.value != '')
            $("#presupuesto").val('');
    });
    $("#linkVerSerieNum").click(function () {
        var temp = $("#linkVerSerieNum p").html();
        var serienum = temp.split('-');
        $("#serie").val(serienum[0]);
        $("#numero").val(serienum[1]);
    });

    $('#buscar_cliente').keyup(function(e){
        var key=e.keyCode || e.which;
        if (key==20){
            if($(this).val()!=''){
                $('#linkSelecCliente').attr('href', base_url+'index.php/empresa/cliente/ventana_selecciona_cliente/'+$('#buscar_cliente').val()).click();
            }
        } 
    });
    
    $('#nombre_cliente').keyup(function(e){
        var key=e.keyCode || e.which;
        if (key==20){
            if($(this).val()!=''){
                $('#linkSelecCliente').attr('href', base_url+'index.php/empresa/cliente/ventana_selecciona_cliente/'+$('#nombre_cliente').val()).click();
            }
        }
    });

    $('#buscar_proveedor').keyup(function (e) {
        var key = e.keyCode || e.which;
        if (key == 13) {
            if ($(this).val() != '') {
                $('#linkSelecProveedor').attr('href', base_url + 'index.php/empresa/proveedor/ventana_selecciona_proveedor/' + $('#buscar_proveedor').val()).click();
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
    $('#buscar_producto').keyup(function (e) {
        var key = e.keyCode || e.which;
        if (key == 13) {
            if ($(this).val() != '') {
                $('#linkSelecProducto').attr('href', base_url + 'index.php/almacen/producto/ventana_selecciona_producto/' + tipo_oper + '/' + $('#flagBS').val() + '/' + $('#buscar_producto').val()).click();
            }
        }
    });

    $('#cantidad').bind('keypress', function (e) {
        tipo_oper = $("#tipo_oper").val();
        flagGenInd = $("#flagGenInd").val();
        if (tipo_oper == 'V' && flagGenInd == 'I') {
            if (e.keyCode == 9 || e.keyCode == 13) {
                if (tipo_oper == 'V') {
                    ventana_producto_serie2_2();
                } else if (tipo_oper == 'C') {
                    ventana_producto_serie_1();
                }
            }
        }
    });
})

function ver_reporte_pdf() {
    var fechai = $('#fechai').val();
    var fechaf = $('#fechaf').val();
    var cliente = $('#cliente').val();
    var producto = $('#producto').val();
    var aprobado = $('#aprobado').val();
    var ingreso = $('#ingreso').val();

    url = base_url + "index.php/ventas/notacredito/ver_reporte_pdf/" + fechai + '_' + fechaf + '_' + cliente + '_' + producto + '_' + aprobado + '_' + ingreso;
    window.open(url, '', "width=800,height=600,menubars=no,resizable=no;")
}

function abrir_envioSunat(codigo){
    url = base_url+"/index.php/ventas/notacredito/ventana_osafact_correos/"+codigo;
    $.ajax({
        type: "POST",
        url: url,
        data: codigo,
        dataType: 'json',
        async: false,
        beforeSend: function (data) {
            console.log(data);
        },
        error: function (data) {
             console.log(data);
        },
        success: function (data) {
        }
    });
    window.open(url,'Formulario Ubigeo','menubar=no,resizable=no,width=800,height=700');
}

function ver_reporte_pdf_ventas() {
    var anio = $("#anioVenta").val();
    url = base_url + "index.php/ventas/notacredito/ver_reporte_pdf_ventas/" + anio;
    window.open(url, '', "width=800,height=600,menubars=no,resizable=no;")
}

function estadisticas_compras_ventas(tipo) {
    var anio = $("#anioVenta2").val();
    url = base_url + "index.php/ventas/notacredito/estadisticas_compras_ventas/" + tipo + "/" + anio;
    window.open(url, '', "width=800,height=600,menubars=no,resizable=no;")
}

function estadisticas_compras_ventas_mensual(tipo) {
    var anio = $("#anioVenta3").val();
    var mes = $("#mesVenta3").val();
    url = base_url + "index.php/ventas/notacredito/estadisticas_compras_ventas_mensual/" + tipo + "/" + anio + "/" + mes + "";
    window.open(url, '', "width=800,height=600,menubars=no,resizable=no;")
}

function editar_comprobante(comprobante, tipo_oper, tipo_docu) {
    //alert(base_url)
    location.href = base_url + "index.php/ventas/notacredito/comprobante_editar/" + comprobante + "/" + tipo_oper + "/" + tipo_docu;
}
function eliminar_comprobante(comprobante) {
    if (confirm('Esta seguro que desea eliminar este comprobante?')) {
        dataString = "comprobante=" + comprobante;
        url = base_url + "index.php/ventas/notacredito/comprobante_eliminar";
        $.post(url, dataString, function (data) {
            location.href = base_url + "index.php/ventas/notacredito/comprobantes" + "/" + tipo_oper + "/" + tipo_docu;
        });
    }
}
function ver_comprobante_pdf(comprobante) {
    var url = base_url + "index.php/ventas/notacredito/comprobante_ver_html/" + comprobante + "/" + tipo_docu;
    window.open(url, '', "width=800,height=600,menubars=no,resizable=no;")
}

function ver_pdf(idNota, formato = 'A4') {
    var url = base_url + "index.php/ventas/notacredito/ver_pdf/" + idNota + "/" + formato;
    window.open(url, '', "width=800,height=600,menubars=no,resizable=no;");
}

function ver_pdf_comprobante(idComprobante) {
    var url = base_url + "index.php/ventas/comprobante/comprobante_ver_pdf/" + idComprobante + "/A4";
    window.open(url, '', "width=800,height=600,menubars=no,resizable=no;");
}

function atras_comprobante() {
    location.href = base_url + "index.php/ventas/notacredito/comprobantes";
}
function agregar_producto_comprobante() {
    flagBS = $("#flagBS").val();

    if ($("#codproducto").val() == '') {
        $("#codproducto").focus();
        alert('Ingrese el producto.');
        return false;
    }
    if ($("#cantidad").val() == '') {
        $("#cantidad").focus();
        alert('Ingrese una cantidad.');
        return false;
    }
    if (flagBS == 'B' && $("#unidad_medida") == '0') {
        $("#unidad_medida").focus();
        alert('Seleccine una unidad de medida.');
        return false;
    }

    codproducto = $("#codproducto").val();
    producto = $("#producto").val();
    nombre_producto = $("#nombre_producto").val();
    descuento = $("#descuento").val();
    igv = parseInt($("#igv").val());
    cantidad = $("#cantidad").val();
    if ($("#precio").val() != '') {
        precio_conigv = $("#precio").val();
    } else {
        precio_conigv = 0;
    }
    if (tipo_docu != 'B' && contiene_igv == '1') {
        precio = money_format(precio_conigv * 100 / (igv + 100));
    } else {
        precio = money_format(precio_conigv * 100 / (igv + 100));
        //precio=precio_conigv;
        //precio_conigv = money_format(precio_conigv*(100+igv)/100);
    }
    costo = parseFloat($("#costo").val());
    unidad_medida = '';
    nombre_unidad = '';
    /* if(flagBS=='B'){
     unidad_medida = $("#unidad_medida").val();
     nombre_unidad = $('#unidad_medida option:selected').html()
     }*/
    flagGenInd = $("#flagGenInd").val();
    n = document.getElementById('tempde_tbl').rows.length;
    j = n + 1;
    if (j % 2 == 0) {
        clase = "itemParTabla";
    } else {
        clase = "itemImparTabla";
    }


    fila = '<tr class="' + clase + '">';
    fila += '<td width="3%"><div align="center"><font color="red"><strong><a href="#" onclick="eliminar_producto_comprobante(' + n + ');">';
    fila += '<span style="border:1px solid red;background: #ffffff;">&nbsp;X&nbsp;</span>';
    fila += '</a></strong></font></div></td>';
    fila += '<td width="4%"><div align="center">' + j + '</div></td>';
    fila += '<td width="10%"><div align="center">' + codproducto + '</div></td>';
    fila += '<td><div align="left"><input type="text" class="cajaGeneral" size="73" maxlength="250" name="proddescri[' + n + ']" id="proddescri[' + n + ']" value="' + nombre_producto + '" /></div></td>';
    fila += '<td width="10%"><div align="left">';
    if (tipo_docu != 'B')
        fila += '<input type="text" size="1" maxlength="5" class="cajaGeneral" name="prodcantidad[' + n + ']" id="prodcantidad[' + n + ']" value="' + cantidad + '" onblur="calcula_importe(' + n + ');" onkeypress="return numbersonly(this,event,\'.\');">' + nombre_unidad;
    else
        fila += '<input type="text" size="1" maxlength="5" class="cajaGeneral" name="prodcantidad[' + n + ']" id="prodcantidad[' + n + ']" value="' + cantidad + '" onblur="calcula_importe(' + n + ');" onkeypress="return numbersonly(this,event,\'.\');">' + nombre_unidad;

    fila += '</div></td>';
    if (tipo_docu != 'B') {
        fila += '<td width="6%"><div align="center"><input type="text" size="5" maxlength="10" class="cajaGeneral" value="' + precio_conigv + '" name="prodpu_conigv[' + n + ']" id="prodpu_conigv[' + n + ']" onblur="modifica_pu_conigv(' + n + ');" onkeypress="return numbersonly(this,event,\'.\');" /></div></td>';
        fila += '<td width="6%"><div align="center"><input type="text" size="5" maxlength="10" class="cajaGeneral" value="' + precio + '" name="prodpu[' + n + ']" id="prodpu[' + n + ']" value="0" onblur="modifica_pu(' + n + ');" onkeypress="return numbersonly(this,event,\'.\');" ></div></td>';
        fila += '<td width="6%"><div align="center"><input type="text" size="5" maxlength="10" class="cajaGeneral cajaSoloLectura" name="prodprecio[' + n + ']" id="prodprecio[' + n + ']" value="0" readonly="readonly">';
    }
    else {
        fila += '<td width="6%"><div align="center"><input type="text" size="5" maxlength="10" class="cajaGeneral" value="' + precio_conigv + '" name="prodpu_conigv[' + n + ']" id="prodpu_conigv[' + n + ']" onblur="modifica_pu_conigv(' + n + ');" onkeypress="return numbersonly(this,event,\'.\');" /></div></td>';
        fila += '<td width="6%"><div align="center"><input type="text" size="5" maxlength="10" class="cajaGeneral" value="' + precio + '" name="prodpu[' + n + ']" id="prodpu[' + n + ']" value="0" onblur="modifica_pu(' + n + ');" onkeypress="return numbersonly(this,event,\'.\');" ></div></td>';
        fila += '<td width="6%"><div align="center"><input type="text" size="5" maxlength="10" class="cajaGeneral cajaSoloLectura" name="prodprecio[' + n + ']" id="prodprecio[' + n + ']" value="0" readonly="readonly">';
    }

    fila += '<td width="6%" style="display:none"><div align="center"><input type="hidden" size="5" class="cajaGeneral cajaSoloLectura" name="prodigv[' + n + ']" id="prodigv[' + n + ']" readonly="readonly"></div></td>';
    fila += '<td width="6%" style="display:none"><div align="center">';
    fila += '<input type="hidden" value="n" name="detaccion['+n+']" id="detaccion['+n+']">';
    fila += '<input type="hidden" name="prodigv100['+n+']" id="prodigv100[' + n + ']" value="' + igv + '">';
    fila += '<input type="hidden" value="" name="detacodi['+n+']" id="detacodi[' + n + ']">';
    fila += '<input type="hidden" name="proddescuento100['+n+']" id="proddescuento100[' + n + ']" value="' + descuento + '">';


    if (tipo_oper == 'C')
        fila += '<input type="hidden" size="1" name="proddescuento[' + n + ']" id="proddescuento[' + n + ']" onblur="calcula_importe2(' + n + ');" />';
    else
        fila += '<input type="hidden" size="1" name="proddescuento[' + n + ']" id="proddescuento[' + n + ']" onblur="calcula_importe2(' + n + ');" />';

    fila += '<input type="hidden" name="flagBS[' + n + ']" id="flagBS[' + n + ']" value="' + flagBS + '">';
    fila += '<input type="hidden" class="cajaMinima" name="prodcodigo[' + n + ']" id="prodcodigo[' + n + ']" value="' + producto + '">';
    fila += '<input type="hidden" class="cajaMinima" name="produnidad[' + n + ']" id="produnidad[' + n + ']" value="' + unidad_medida + '">';
    fila += '<input type="hidden" class="cajaMinima" name="flagGenIndDet[' + n + ']" id="flagGenIndDet[' + n + ']" value="' + flagGenInd + '">';
    fila += '<input type="hidden" class="cajaPequena2" name="prodcosto[' + n + ']" id="prodcosto[' + n + ']" value="' + costo + '" readonly="readonly">';
    fila += '<input type="hidden" size="5" class="cajaGeneral cajaSoloLectura" name="prodimporte[' + n + ']" id="prodimporte[' + n + ']" value="0" readonly="readonly">';
    fila += '</div></td>';
    fila += '</tr>';
    $("#tempde_tbl").append(fila);

    if (tipo_docu != 'B')
        calcula_importe(n);
    else
        calcula_importe(n);

    inicializar_cabecera_item();

    return true;
}

function eliminar_producto_comprobante(n) {
    if (confirm('Esta seguro que desea eliminar este producto?')) {
        a = "detacodi["+n+"]";
        b = "detaccion["+n+"]";
        fila = document.getElementById(a).parentNode.parentNode.parentNode;
        fila.style.display = "none";
        document.getElementById(b).value = "e";
        if (tipo_docu != 'B')
            calcula_totales();
        else
            calcula_totales_conigv();
    }
}
function calcula_importe(n) {
    a = "prodpu[" + n + "]";
    b = "prodcantidad[" + n + "]";
    c = "proddescuento[" + n + "]";
    d = "prodigv[" + n + "]";
    e = "prodprecio[" + n + "]";
    f = "prodimporte[" + n + "]";
    g = "prodigv100[" + n + "]";
    h = "proddescuento100[" + n + "]";
    i = "prodpu_conigv[" + n + "]";
    pu = document.getElementById(a).value;
    pu_conigv = document.getElementById(i).value;
    cantidad = document.getElementById(b).value;
    igv100 = document.getElementById(g).value;
    descuento100 = document.getElementById(h).value;
    precio = money_format(pu * cantidad);
    total_dscto = money_format(precio * descuento100 / 100);
    precio2 = money_format(precio - parseFloat(total_dscto));
    if (pu_conigv == '')
        total_igv = money_format(precio2 * igv100 / 100);
    else
        total_igv = money_format((pu_conigv - pu) * cantidad);

    importe = money_format(precio - parseFloat(total_dscto) + parseFloat(total_igv));

    document.getElementById(c).value = total_dscto;
    document.getElementById(d).value = total_igv;
    document.getElementById(e).value = precio;
    document.getElementById(f).value = importe;

    calcula_totales();
}
function calcula_importe_conigv(n) {
    a = "prodpu_conigv[" + n + "]";
    b = "prodcantidad[" + n + "]";
    c = "proddescuento_conigv[" + n + "]";
    e = "prodprecio_conigv[" + n + "]";
    f = "prodimporte[" + n + "]";
    g = "prodigv100[" + n + "]";
    h = "proddescuento100[" + n + "]";
    pu_conigv = document.getElementById(a).value;
    cantidad = document.getElementById(b).value;
    igv100 = document.getElementById(g).value;
    descuento100 = document.getElementById(h).value;
    precio_conigv = money_format(pu_conigv * cantidad);
    total_dscto_conigv = money_format(precio_conigv * descuento100 / 100);
    precio2 = money_format(precio_conigv - parseFloat(total_dscto_conigv));

    importe = money_format(precio_conigv - parseFloat(total_dscto_conigv));
    document.getElementById(c).value = total_dscto_conigv;
    document.getElementById(e).value = precio_conigv;
    document.getElementById(f).value = importe;

    calcula_totales_conigv();
}
function calcula_importe2(n) {
    a = "prodpu[" + n + "]";
    b = "prodcantidad[" + n + "]";
    c = "proddescuento[" + n + "]";
    e = "prodigv[" + n + "]";
    f = "prodprecio[" + n + "]";
    g = "prodimporte[" + n + "]";
    h = "prodpu_conigv[" + n + "]";
    valor_igv = $("#igv").val();
    pu = parseFloat(document.getElementById(a).value);
    cantidad = parseFloat(document.getElementById(b).value);
    descuento = parseFloat(document.getElementById(c).value);
    total_igv = parseFloat(document.getElementById(e).value);
    precio_u = parseFloat(document.getElementById(h).value);
    descuento_total = ((precio_u * cantidad) * (descuento / 100));

    importe = money_format((precio_u * cantidad) - descuento_total);
    t_igv = money_format((importe * valor_igv) / 100);
    importe_total = money_format(importe + t_igv);
    document.getElementById(g).value = importe_total;
    document.getElementById(e).value = t_igv;
    document.getElementById(f).value = importe;
    calcula_totales();
}
function calcula_importe2_conigv(n) {
    a = "prodpu_conigv[" + n + "]";
    b = "prodcantidad[" + n + "]";
    c = "proddescuento_conigv[" + n + "]";
    f = "prodprecio_conigv[" + n + "]";
    g = "prodimporte[" + n + "]";
    pu_conigv = parseFloat(document.getElementById(a).value);
    cantidad = parseFloat(document.getElementById(b).value);
    descuento_conigv = parseFloat(document.getElementById(c).value);
    importe = money_format((pu_conigv * cantidad) - descuento_conigv);
    document.getElementById(g).value = importe;

    calcula_totales_conigv();
}
function calcula_totales() {
    n = document.getElementById('tempde_tbl').rows.length;
    importe_total = 0;
    igv_total = 0;
    descuento_total = 0;
    precio_total = 0;
    for (i = 0; i < n; i++) {//Estanb al reves los campos
        a = "prodimporte[" + i + "]";
        b = "prodigv[" + i + "]";
        c = "proddescuento[" + i + "]";
        d = "prodprecio[" + i + "]";
        e = "detaccion[" + i + "]";
        if (document.getElementById(e).value != 'e') {
            //importe = parseFloat(document.getElementById(a).value);
            //igv = parseFloat(document.getElementById(b).value);
            descuento = parseFloat(document.getElementById(c).value);
            precio = parseFloat(document.getElementById(d).value);
            //importe_total = money_format(importe + importe_total);
            //igv_total = money_format(igv + igv_total);
            descuento_total = money_format(descuento + descuento_total);
            precio_total = money_format(precio + precio_total);
        }
    }
    igv100 = parseInt($("#igv").val());
    igv_total = money_format(precio_total * igv100 / 100);
    importe_total = money_format(precio_total + igv_total);
    //preciototal=importe_total.toFixed(2)+igv_total.toFixed(2);

    $("#importetotal").val(importe_total.toFixed(2));  //val(importe_total.toFixed(2))
    $("#igvtotal").val(igv_total.toFixed(2));  //val(igv_total.toFixed(2))
    $("#descuentotal").val(descuento_total.toFixed(2));
    $("#preciototal").val(precio_total.toFixed(2));  //val(precio_total.toFixed(2))


}
function calcula_totales_conigv() {
    n = document.getElementById('$valor[13]').rows.length;
    importe_total = 0;

    descuento_total_conigv = 0;
    precio_total_conigv = 0;
    for (i = 0; i < n; i++) {//Estanb al reves los campos
        a = "prodimporte[" + i + "]"
        c = "proddescuento_conigv[" + i + "]";
        d = "prodprecio_conigv[" + i + "]";
        e = "detaccion[" + i + "]";
        if (document.getElementById(e).value != 'e') {
            importe = parseFloat(document.getElementById(a).value);
            descuento_conigv = parseFloat(document.getElementById(c).value);
            precio_conigv = parseFloat(document.getElementById(d).value);
            importe_total = money_format(importe + importe_total);
            descuento_total_conigv = money_format(descuento_conigv + descuento_total_conigv);
            precio_total_conigv = money_format(precio_conigv + precio_total_conigv);
        }
    }
    $("#importetotal").val(importe_total.toFixed(2));
    $("#descuentotal_conigv").val(descuento_total_conigv.toFixed(2));
    $("#preciototal_conigv").val(precio_total_conigv.toFixed(2));
}
function mostrar_productos_factura(guias) {
    for (i = 0; i < guias.length; i++) {
        var codigo_guia = guias[i];
        url = base_url + "index.php/almacen/guiarem/obtener_detalle_guiarem/" + codigo_guia + "/C",
            $.getJSON(url, function (data) {
                $.each(data, function (i, item) {
                    n = document.getElementById('tempde_tbl').rows.length;
                    id_tr_dguia = n;
                    producto = item.PROD_Codigo;
                    codigo = item.PROD_CodigoInterno;
                    nombre = item.PROD_Nombre;
                    cantidad = item.GUIAREMDETC_Cantidad;
                    pu = item.GUIAREMDETC_Pu;
                    importe = item.GUIAREMDETC_Pu_ConIgv;
                    fila = '<tr id="dguia_' + id_tr_dguia + '">';
                    fila += '<td>';
                    fila += '<input type="hidden" name="producto[' + n + ']" id="producto[' + n + ']" value="' + producto + '"/>';
                    fila += codigo;
                    fila += '</td>';
                    fila += '<td>' + nombre + '</td>';
                    fila += '<td>' + cantidad + '</td>';
                    fila += '<td>' + pu + '</td>';
                    fila += '<td>' + importe + '</td>';
                    fila += '</tr>';
                    $("#tempde_tbl").append(fila);
                });
            });
    }
}
// para agregar productos cuando ingreso por el seguimiento de orden
function mostrar_productos_factura(guias) {
    for (i = 0; i < guias.length; i++) {
        var codigo_guia = guias[i];
        url = base_url + "index.php/almacen/guiarem/obtener_detalle_guiarem/" + codigo_guia + "/C",
            $.getJSON(url, function (data) {
                $.each(data, function (i, item) {
                    var igv = 18;
                    flagBS = $("#flagBS").val();
                    precio_conigv = parseFloat(item.GUIAREMDETC_Pu_ConIgv);
                    precio = parseFloat(item.GUIAREMDETC_Subtotal);
                    codproducto = item.PROD_Codigo;
                    producto = item.PROD_CodigoInterno;
                    unidad_medida = item.UNDMED_Codigo;
                    nombre_unidad = item.UNDMED_Simbolo;
                    nombre_producto = item.PROD_Nombre;
                    cantidad = item.GUIAREMDETC_Cantidad;
                    stock = '0';
                    costo = '0';
                    n = document.getElementById('tempde_tbl').rows.length;
                    j = n + 1;
                    if (j % 2 == 0) {
                        clase = "itemParTabla";
                    } else {
                        clase = "itemImparTabla";
                    }
                    fila = '<tr class="' + clase + '">';
                    fila += '<td width="3%"><div align="center"><font color="red"><strong><a href="javascript:;" onclick="eliminar_producto_ocompra(' + n + ');">';
                    fila += '<span style="border:1px solid red;background: #ffffff;">&nbsp;X&nbsp;</span>';
                    fila += '</a></strong></font></div></td>';
                    fila += '<td width="4%"><div align="center">' + j + '</div></td>';
                    fila += '<td width="10%"><div align="center">';
                    fila += '<input type="hidden" name="flagBS[' + n + ']" id="flagBS[' + n + ']" value="' + flagBS + '">';
                    fila += '<input type="hidden" class="cajaMinima" name="prodcodigo[' + n + ']" id="prodcodigo[' + n + ']" value="' + codproducto + '">' + producto;
                    fila += '<input type="hidden" class="cajaMinima" name="produnidad[' + n + ']" id="produnidad[' + n + ']" value="' + unidad_medida + '">';
                    fila += '</div></td>';
                    fila += '<td><div align="left">';
                    fila += '<input type="text" class="cajaGeneral" style="width:395px;" maxlength="250" name="proddescri[' + n + ']" id="proddescri[' + n + ']" value="' + nombre_producto + '">';
                    fila += '</div></td>';
                    fila += '<td width="10%"><div align="left">';
                    fila += '<input type="text" class="cajaGeneral" size="1" maxlength="5" name="prodcantidad[' + n + ']" id="prodcantidad[' + n + ']" value="' + cantidad + '" onblur="calcula_importe(' + n + ');" onkeypress="return numbersonly(this,event,\'.\');"> ' + nombre_unidad;
                    fila += '</div></td>';
                    fila += '<td width="6%"><div align="center"><input type text" size="5" maxlength="10" class="cajaGeneral" value="' + precio + '" name="prodpu[' + n + ']" id="prodpu[' + n + ']" onblur="modifica_pu(' + n + ');" onkeypress="return numbersonly(this,event,\'.\');">'
                    fila += '<input type="hidden"  value="' + precio_conigv + '" name="prodpu_conigv[' + n + ']" id="prodpu_conigv[' + n + ']"></div></td>';
                    fila += '<td width="6%"><div align="center"><input type="text" size="5" maxlength="10" class="cajaGeneral cajaSoloLectura" name="prodprecio[' + n + ']" id="prodprecio[' + n + ']" value="0" readonly="readonly"></div></td>';
                    fila += '<td width="6%"><div align="center">';
                    fila += '<input type="hidden" name="proddescuento100[' + n + ']" id="proddescuento100[' + n + ']" value="0">';
                    fila += '<input type="text" size="5" maxlength="10" class="cajaGeneral" name="proddescuento[' + n + ']" id="proddescuento[' + n + ']" onblur="calcula_importe2(' + n + ');" />';
                    fila += '</div></td>';
                    fila += '<td width="6%"><div align="center"><input type="text" size="5" maxlength="10" class="cajaGeneral cajaSoloLectura" name="prodigv[' + n + ']" id="prodigv[' + n + ']" readonly></div></td>';
                    fila += '<td width="6%"><div align="center">';
                    fila += '<input type="hidden" class="cajaMinima" name="detacodi[' + n + ']" id="detacodi[' + n + ']">';
                    fila += '<input type="hidden" class="cajaMinima" name="detaccion[' + n + ']" id="detaccion[' + n + ']" value="n">';
                    fila += '<input type="hidden" name="prodigv100[' + n + ']" id="prodigv100[' + n + ']" value="' + igv + '">';
                    fila += '<input type="hidden" class="cajaPequena2" name="prodcosto[' + n + ']" id="prodcosto[' + n + ']" value="' + costo + '" readonly="readonly">';
                    fila += '<input type="hidden" class="cajaPequena2" name="prodventa[' + n + ']" id="prodventa[' + n + ']" value="0" readonly="readonly">';
                    fila += '<input type="text" size="5" maxlength="10" class="cajaGeneral cajaSoloLectura" name="prodimporte[' + n + ']" id="prodimporte[' + n + ']" value="0" readonly="readonly">';
                    fila += '</div></td>';
                    fila += '</tr>';
                    $("#tempde_tbl").append(fila);
                    calcula_importe(n);
                });
            });
    }
    return true;
}
function modifica_pu_conigv(n) {
    a = "prodpu_conigv[" + n + "]";
    g = "prodigv100[" + n + "]";
    i = "prodpu[" + n + "]";
    pu_conigv = parseFloat(document.getElementById(a).value);
    igv100 = parseFloat(document.getElementById(g).value);

    pu = money_format(100 * pu_conigv / (100 + igv100));
    document.getElementById(i).value = pu;

    calcula_importe(n);
}
function modifica_pu(n) {
    a = "prodpu[" + n + "]";
    g = "prodigv100[" + n + "]";
    i = "prodpu_conigv[" + n + "]";
    pu = parseFloat(document.getElementById(a).value);
    igv100 = parseFloat(document.getElementById(g).value);

    pu_conigv = money_format(pu * (100 + igv100) / 100);
    document.getElementById(i).value = pu_conigv;

    calcula_importe(n);
}
function modifica_descuento_total() {
    descuento = $('#descuento').val();
    n = document.getElementById('tblDetalleOcompra').rows.length;
    for (i = 0; i < n; i++) {
        a = "proddescuento100[" + i + "]";
        document.getElementById(a).value = descuento;
    }
    for (i = 0; i < n; i++) {
        calcula_importe(i);
    }
    calcula_totales();
}
function modifica_igv_total() {
    igv = $('#igv').val();
    n = document.getElementById('tblDetalleOcompra').rows.length;
    for (i = 0; i < n; i++) {
        a = "prodigv100[" + i + "]";
        document.getElementById(a).value = igv;
    }
    for (i = 0; i < n; i++) {
        calcula_importe(i);
    }
    calcula_totales();
}
function listar_unidad_medida_producto(producto) {
    base_url = $("#base_url").val();
    flagBS = $("#flagBS").val();
    url = base_url + "index.php/almacen/producto/listar_unidad_medida_producto/" + producto;
    select_umedida = document.getElementById('unidad_medida');
    options_umedida = select_umedida.getElementsByTagName("option");

    var num_option = options_umedida.length;
    for (i = 1; i <= num_option; i++) {
        select_umedida.remove(0)
    }
    opt = document.createElement("option");
    texto = document.createTextNode(":: Seleccione ::");
    opt.appendChild(texto);
    opt.value = "0";
    select_umedida.appendChild(opt);
    $("#cantidad").val('');
    $("#precio").val('');

    $.getJSON(url, function (data) {
        $.each(data, function (i, item) {
            codigo = item.UNDMED_Codigo;
            descripcion = item.UNDMED_Descripcion;
            simbolo = item.UNDMED_Descripcion;
            nombre_producto = item.PROD_Nombre;
            nombrecorto_producto = item.PROD_NombreCorto;
            marca = item.MARCC_Descripcion;
            modelo = item.PROD_Modelo;
            presentacion = item.PROD_Presentacion;
            opt = document.createElement('option');
            texto = document.createTextNode(simbolo);
            opt.appendChild(texto);
            opt.value = codigo;
            if (i == 0)
                opt.selected = true;
            select_umedida.appendChild(opt);
        });
        var nombre;
        if (nombrecorto_producto)
            nombre = nombrecorto_producto;
        else
            nombre = nombre_producto;
        if (flagBS == 'B') {
            if (marca)
                nombre += ' ';
            if (modelo)
                nombre += ' ';
            if (presentacion)
                nombre += ' ';
        }
        $("#nombre_producto").val(nombre);
        listar_precios_x_producto_unidad();
    });
}

function listar_precios_x_producto_unidad() {
    unidad = $("#unidad_medida").val();
    moneda = $("#moneda").val();
    base_url = $("#base_url").val();
    flagBS = $("#flagBS").val();
    url = base_url + "index.php/almacen/producto/listar_precios_x_producto_unidad/1/" + unidad + "/" + moneda;
    select_precio = document.getElementById('precioProducto');
    options_umedida = select_precio.getElementsByTagName("option");

    var num_option = options_umedida.length;
    for (i = 1; i <= num_option; i++) {
        select_precio.remove(0)
    }
    opt = document.createElement("option");
    texto = document.createTextNode("::Seleccion::");
    opt.appendChild(texto);
    opt.value = "";
    select_precio.appendChild(opt);
    $.getJSON(url, function (data) {
        $.each(data, function (i, item) {
            codigo = item.codigo;
            moneda = item.moneda;
            precio = item.precio;
            opt = document.createElement('option');
            texto = document.createTextNode(moneda + " " + precio);
            opt.appendChild(texto);
            opt.value = precio;
            select_precio.appendChild(opt);
        });
    });
}

function mostrar_precio() {
    precio = $("#precioProducto").val();
    $("#precio").val(precio);
}

function obtener_precio_producto() {
    var producto = $("#producto").val();
    $('#precio').val("");
    if (producto == '' || producto == '0')
        return false;
    var moneda = $("#moneda").val();
    if (moneda == '' || moneda == '0')
        return false;
    var unidad_medida = $("#unidad_medida").val();
    if (unidad_medida == '' || unidad_medida == '0')
        return false;
    var cliente = $("#cliente").val();
    if (cliente == '')
        cliente = '0';
    var igv;
    if (contiene_igv == '1')
        igv = 0;
    else if (tipo_docu != 'B')
        igv = 0;
    else
        igv = $("#igv").val();

    var url = base_url + "index.php/almacen/producto/JSON_precio_producto/" + producto + "/" + moneda + "/" + cliente + "/" + unidad_medida + "/" + igv;
    $.getJSON(url, function (data) {
        $.each(data, function (i, item) {
            $('#precio').val(item.PRODPREC_Precio);

        });
    });
    return false;
}
function inicializar_cabecera_item() {
    $("#producto").val('');
    $("#buscar_producto").val('');
    $("#codproducto").val('');
    $("#nombre_producto").val('');
    $("#cantidad").val('');
    $("#costo").val('');
    $("#unidad_medida").val('0');
    $("#precioProducto").val('');
    $("#precio").val('');
    limpiar_combobox('unidad_medida');
}
function obtener_detalle_presupuesto() {
    presupuesto = $("#presupuesto").val();
    descuento100 = $("#descuento").val();
    igv100 = $("#igv").val();
    url = base_url + "index.php/ventas/presupuesto/obtener_detalle_presupuesto/" + presupuesto;
    n = document.getElementById('tempde_tbl').rows.length;
    $.getJSON(url, function (data) {
        limpiar_datos();
        $.each(data, function (i, item) {
            cliente = item.CLIP_Codigo;
            ruc = item.Ruc;
            razon_social = item.RazonSocial;
            moneda = item.MONED_Codigo;
            formapago = item.FORPAP_Codigo;
            serie = item.PRESUC_Serie;
            numero = item.PRESUC_Numero;
            codigo_usuario = item.PRESUC_CodigoUsuario;

            if (item.PRESDEP_Codigo != '') {
                j = n + 1
                producto = item.PROD_Codigo;
                codproducto = item.PROD_CodigoInterno;
                unidad_medida = item.UNDMED_Codigo;
                nombre_unidad = item.UNDMED_Simbolo;
                nombre_producto = item.PROD_Nombre;
                cantidad = item.PRESDEC_Cantidad;
                pu = item.PRESDEC_Pu;
                subtotal = item.PRESDEC_Subtotal;
                descuento = item.PRESDEC_Descuento;
                igv = item.PRESDEC_Igv;
                total = item.PRESDEC_Total
                pu_conigv = item.PRESDEC_Pu_ConIgv;
                subtotal_conigv = item.PRESDEC_Subtotal_ConIgv;

                descuento_conigv = item.PRESDEC_Descuento_ConIgv;

                if (j % 2 == 0) {
                    clase = "itemParTabla";
                } else {
                    clase = "itemImparTabla";
                }
                fila = '<tr class="' + clase + '">';
                fila += '<td width="3%"><div align="center"><font color="red"><strong><a href="#" onclick="eliminar_producto_comprobante(' + n + ');">';
                fila += '<span style="border:1px solid red;background: #ffffff;">&nbsp;X&nbsp;</span>';
                fila += '</a></strong></font></div></td>';
                fila += '<td width="4%"><div align="center">' + j + '</div></td>';
                fila += '<td width="10%"><div align="center">';
                fila += '<input type="hidden" class="cajaGeneral" name="prodcodigo[' + n + ']" id="prodcodigo[' + n + ']" value="' + producto + '">' + codproducto;
                fila += '<input type="hidden" class="cajaGeneral" name="produnidad[' + n + ']" id="produnidad[' + n + ']" value="' + unidad_medida + '">';
                fila += '</div></td>';
                fila += '<td><div align="left"><input type="text" class="cajaGeneral" size="73" maxlength="250" name="proddescri[' + n + ']" id="proddescri[' + n + ']" value="' + nombre_producto + '" /></div></td>';
                if (tipo_docu != 'B')
                    fila += '<td width="10%"><div align="left"><input type="text" size="1" maxlength="5" class="cajaGeneral" name="prodcantidad[' + n + ']" id="prodcantidad[' + n + ']" value="' + cantidad + '" onblur="calcula_importe(' + n + ');calcula_totales();" onkeypress="return numbersonly(this,event,\'.\');">' + nombre_unidad + '</div></td>';
                else
                    fila += '<td width="10%"><div align="left"><input type="text" size="1" maxlength="5" class="cajaGeneral" name="prodcantidad[' + n + ']" id="prodcantidad[' + n + ']" value="' + cantidad + '" onblur="calcula_importe_conigv(' + n + ');" onkeypress="return numbersonly(this,event,\'.\');">' + nombre_unidad + '</div></td>';
                if (tipo_docu != 'B') {
                    fila += '<td width="6%"><div align="center"><input type="text" size="5" maxlength="10" class="cajaGeneral" name="prodpu[' + n + ']" id="prodpu[' + n + ']" value="' + pu + '" onblur="modifica_pu(' + n + ');" onkeypress="return numbersonly(this,event,\'.\');">';
                    fila += '<div align="center"><input type="hidden" name="prodpu_conigv[' + n + ']" id="prodpu_conigv[' + n + ']" value="' + pu_conigv + '"></div></td>';
                    fila += '<td width="6%"><input type="text" class="cajaGeneral cajaSoloLectura" size="5" maxlength="10" name="prodprecio[' + n + ']" id="prodprecio[' + n + ']" value="' + subtotal + '" readonly="readonly"></div></td>';
                } else {
                    fila += '<td width="6%"><div align="center"><input type="text" size="5" maxlength="10" class="cajaGeneral" name="prodpu_conigv[' + n + ']" id="prodpu_conigv[' + n + ']" value="' + pu_conigv + '" onblur="calcula_importe_conigv(' + n + ');" onkeypress="return numbersonly(this,event,\'.\');"></div></td>';
                    fila += '<td width="6%"><input type="text" size="5" maxlength="10" class="cajaGeneral" name="prodprecio_conigv[' + n + ']" id="prodprecio_conigv[' + n + ']" value="' + subtotal_conigv + '" readonly="readonly"></div></td>';
                }
                fila += '<td width="6%"><div align="center">';
                fila += '<input type="hidden" readonly name="proddescuento100[' + n + ']" id="proddescuento100[' + n + ']" value="' + descuento100 + '">';
                if (tipo_docu != 'B')
                    fila += '<input type="text" size="5" maxlength="10" readonly class="cajaGeneral" name="proddescuento[' + n + ']" id="proddescuento[' + n + ']" value="' + descuento + '" onblur="calcula_importe2(' + n + ');calcula_totales();">';
                else
                    fila += '<input type="text" size="5" maxlength="10" readonly class="cajaGeneral" name="proddescuento_conigv[' + n + ']" id="proddescuento_conigv[' + n + ']" value="' + descuento_conigv + '" onblur="calcula_importe2_conigv(' + n + ');calcula_totales_conigv();">';
                fila += '</div></td>';
                if (tipo_docu != 'B')
                    fila += '<td width="6%"><div align="center"><input type="text" size="5" class="cajaGeneral cajaSoloLectura" name="prodigv[' + n + ']" value="' + igv + '" id="prodigv[' + n + ']" readonly></div></td>';
                fila += '<td width="6%"><div align="center">';
                fila += '<input type="hidden" name="detacodi[' + n + ']" id="detacodi[' + n + ']">';
                fila += '<input type="hidden" name="prodigv100[' + n + ']" id="prodigv100[' + n + ']" value="' + igv100 + '">';
                fila += '<input type="hidden" name="detaccion[' + n + ']" id="detaccion[' + n + ']" value="n">';
                fila += '<input type="text" size="5" class="cajaGeneral cajaSoloLectura" name="prodimporte[' + n + ']" id="prodimporte[' + n + ']" value="' + total + '" readonly="readonly" value="0">';
                fila += '</div></td>';
                fila += '</tr>';
                $("#tempde_tbl").append(fila);
            }

            $('#ruc_cliente').val(ruc);
            $('#cliente').val(cliente);
            $('#nombre_cliente').val(razon_social);
            $('#forma_pago').val(formapago);
            $('#moneda').val(moneda);
            if (codigo_usuario)
                $("#docurefe_codigo").val(codigo_usuario);
            else if (serie)
                $("#docurefe_codigo").val('PR: ' + serie + ' / ' + numero);
            else
                $("#docurefe_codigo").val('PR: ' + numero);

            n++;
        })
        if (n >= 0) {
            if (tipo_docu != 'B')
                calcula_totales();
            else
                calcula_totales_conigv();
        }
        else {
            alert('El presupuesto no tiene elementos.');
        }
    });
}
function obtener_detalle_guiarem() {
    guiarem = $("#guiaremision").val();
    descuento100 = $("#descuento").val();
    igv100 = $("#igv").val();
    url = base_url + "index.php/almacen/guiarem/obtener_detalle_guiarem/" + guiarem;
    n = document.getElementById('tempde_tbl').rows.length;
    $.getJSON(url, function (data) {
        limpiar_datos();
        $.each(data, function (i, item) {
            cliente = item.CLIP_Codigo;
            ruc = item.Ruc;
            razon_social = item.RazonSocial;
            moneda = item.MONED_Codigo;
            serie = item.GUIAREMC_Serie;
            numero = item.GUIAREMC_Numero;
            codigo_usuario = item.GUIAREMC_CodigoUsuario;

            if (item.GUIAREMP_Codigo != '') {
                j = n + 1
                producto = item.PROD_Codigo;
                codproducto = item.PROD_CodigoInterno;
                unidad_medida = item.UNDMED_Codigo;
                nombre_unidad = item.UNDMED_Simbolo;
                nombre_producto = item.PROD_Nombre;
                cantidad = item.GUIAREMDETC_Cantidad;
                pu = item.GUIAREMDETC_Pu;
                subtotal = item.GUIAREMDETC_Subtotal;
                descuento = item.GUIAREMDETC_Descuento;
                igv = item.GUIAREMDETC_Igv;
                total = item.GUIAREMDETC_Total
                pu_conigv = item.GUIAREMDETC_Pu_ConIgv;


                if (j % 2 == 0) {
                    clase = "itemParTabla";
                } else {
                    clase = "itemImparTabla";
                }
                fila = '<tr class="' + clase + '">';
                fila += '<td width="3%"><div align="center"><font color="red"><strong><a href="#" onclick="eliminar_producto_comprobante(' + n + ');">';
                fila += '<span style="border:1px solid red;background: #ffffff;">&nbsp;X&nbsp;</span>';
                fila += '</a></strong></font></div></td>';
                fila += '<td width="4%"><div align="center">' + j + '</div></td>';
                fila += '<td width="10%"><div align="center">';
                fila += '<input type="hidden" class="cajaGeneral" name="prodcodigo[' + n + ']" id="prodcodigo[' + n + ']" value="' + producto + '">' + codproducto;
                fila += '<input type="hidden" class="cajaGeneral" name="produnidad[' + n + ']" id="produnidad[' + n + ']" value="' + unidad_medida + '">';
                fila += '</div></td>';
                fila += '<td><div align="left"><input type="text" class="cajaGeneral" style="width:395px;" maxlength="250" name="proddescri[' + n + ']" id="proddescri[' + n + ']" value="' + nombre_producto + '" /></div></td>';
                fila += '<td width="10%"><div align="left"><input type="text" size="1" maxlength="5" class="cajaGeneral" name="prodcantidad[' + n + ']" id="prodcantidad[' + n + ']" value="' + cantidad + '" onblur="calcula_importe(' + n + ');calcula_totales();" onkeypress="return numbersonly(this,event,\'.\');">' + nombre_unidad + '</div></td>';
                fila += '<td width="6%"><div align="center"><input type="text" size="5" maxlength="10" class="cajaGeneral" name="prodpu[' + n + ']" id="prodpu[' + n + ']" value="' + pu + '" onblur="modifica_pu(' + n + ');" onkeypress="return numbersonly(this,event,\'.\');">';
                fila += '<div align="center"><input type="hidden" name="prodpu_conigv[' + n + ']" id="prodpu_conigv[' + n + ']" value="' + pu_conigv + '"></div></td>';
                fila += '<td width="6%"><input type="text" class="cajaGeneral cajaSoloLectura" size="5" maxlength="10" name="prodprecio[' + n + ']" id="prodprecio[' + n + ']" value="' + subtotal + '" readonly="readonly"></div></td>';
                fila += '<td width="6%"><div align="center">';
                fila += '<input type="hidden" readonly name="proddescuento100[' + n + ']" id="proddescuento100[' + n + ']" value="' + descuento100 + '">';
                fila += '<input type="text" size="5" readonly maxlength="10" class="cajaGeneral" name="proddescuento[' + n + ']" id="proddescuento[' + n + ']" value="' + descuento + '" onblur="calcula_importe2(' + n + ');calcula_totales();">';
                fila += '</div></td>';
                fila += '<td width="6%"><div align="center"><input type="text" size="5" class="cajaGeneral cajaSoloLectura" name="prodigv[' + n + ']" value="' + igv + '" id="prodigv[' + n + ']" readonly></div></td>';
                fila += '<td width="6%"><div align="center">';
                fila += '<input type="hidden" name="detacodi[' + n + ']" id="detacodi[' + n + ']">';
                fila += '<input type="hidden" name="prodigv100[' + n + ']" id="prodigv100[' + n + ']" value="' + igv100 + '">';
                fila += '<input type="hidden" name="detaccion[' + n + ']" id="detaccion[' + n + ']" value="n">';
                fila += '<input type="text" size="5" class="cajaGeneral cajaSoloLectura" name="prodimporte[' + n + ']" id="prodimporte[' + n + ']" value="' + total + '" readonly="readonly" value="0">';
                fila += '</div></td>';
                fila += '</tr>';
                $("#tempde_tbl").append(fila);
            }

            $('#ruc_cliente').val(ruc);
            $('#cliente').val(cliente);
            $('#nombre_cliente').val(razon_social);
            $('#moneda').val(moneda);
            if (codigo_usuario)
                $("#guiaremision_codigo").val(codigo_usuario);
            else if (serie)
                $("#guiaremision_codigo").val(serie + '-' + numero);
            else
                $("#guiaremision_codigo").val(numero);

            n++;
        })
        if (n >= 0)
            calcula_totales();
        else
            alert('La guía de remisión no tiene elementos.');
    });
}
function limpiar_datos() {
    $('#formapago').val('');
    $('#moneda').val('1');

    n = document.getElementById('tempde_tbl').rows.length;
    for (i = 0; i < n; i++) {
        a = "detacodi[" + i + "]";
        b = "detaccion[" + i + "]";
        fila = document.getElementById(a).parentNode.parentNode.parentNode;
        fila.style.display = "none";
        document.getElementById(b).value = "e";
    }
}
function obtener_cliente() {
    var numdoc = $("#ruc_cliente").val();
    $('#cliente,#nombre_cliente').val('');

    if (numdoc == '')
        return false;

    var url = base_url + "index.php/empresa/cliente/JSON_buscar_cliente/" + numdoc;
    $.getJSON(url, function (data) {
        $.each(data, function (i, item) {
            if (item.EMPRC_RazonSocial != '') {
                $('#nombre_cliente').val(item.EMPRC_RazonSocial);
                $('#cliente').val(item.CLIP_Codigo);
                $('#codproducto').focus();
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
            }
            else {
                $('#nombre_proveedor').val('No se encontró ningún registro');
                $('#linkVerProveedor').focus();
            }
        });
    });
    return true;
}
function obtener_producto() {
    var flagBS = $("#flagBS").val();
    var codproducto = $("#codproducto").val();
    $("#producto, #nombre_producto").val('');
    if (codproducto == '')
        return false;

    var url = base_url + "index.php/almacen/producto/obtener_nombre_producto/" + flagBS + "/" + codproducto;
    $.getJSON(url, function (data) {
        $.each(data, function (i, item) {
            if (item.PROD_Nombre != '') {
                $("#producto").val(item.PROD_Codigo);
                $("#nombre_producto").val(item.PROD_Nombre);
                listar_unidad_medida_producto($("#producto").val());
                $('#cantidad').focus();
            }
            else {
                $('#nombre_producto').val('No se encontró ningún registro');
                $('#linkVerProdcuto').focus();
            }

        });
    });
    return true;
}

function limpiar_campos_producto() {
    $("#producto,  #codproducto, #nombre_producto, #cantidad, #precio").val('');
    limpiar_combobox('unidad_medida');
    if ($('#flagBS').val() == 'B')
        $('#unidad_medida').show();
    else
        $('#unidad_medida').hide();
    $('#linkVerProducto').attr('href', '' + base_url + 'index.php/almacen/producto/ventana_busqueda_producto/' + $('#flagBS').val());
}
function agregar_todonotadecredito(guia) {
    descuento100 = $("#descuento").val();
    igv100 = $("#igv").val();
    flagGenInd = $("#flagGenInd").val();

    url = base_url + "index.php/ventas/notacredito/obtener_detalle_comprobante/" + guia;
    n = document.getElementById('tempde_tbl').rows.length;
    $.getJSON(url, function (data) {
        limpiar_datos();

        $.each(data, function (i, item) {
            cliente = item.CLIP_Codigo;
            proveedor = item.PROVP_Codigo;
            ruc = item.Ruc;
            razon_social = item.RazonSocial;
            moneda = item.MONED_Codigo;
            formapago = item.FORPAP_Codigo;
            serie = item.CRED_Serie;
            numero = item.CRED_Numero;
            codigo_usuario = item.PRESUC_CodigoUsuario;


            if (item.CREDET_Codigo != '') {
                j = n + 1;
                producto = item.PROD_Codigo;
                codproducto = item.PROD_CodigoInterno;
                unidad_medida = item.UNDMED_Codigo;
                nombre_unidad = item.UNDMED_Descripcion;
                nombre_producto = item.PROD_Nombre;
                //flagGenInd      = item.PROD_GenericoIndividual;
                costo = item.CREDET_Costo;
                cantidad = item.CREDET_Cantidad;
                pu = item.CREDET_Pu;
                subtotal = item.CREDET_Subtotal;
                descuento = item.CREDET_Descuento;
                igv = item.CREDET_Igv;
                total = item.CREDET_Total;
                pu_conigv = item.CREDET_Pu_ConIgv;
                subtotal_conigv = item.CREDET_Subtotal_ConIgv;
                descuento_conigv = item.CREDET_Descuento_ConIgv;

                //alert(item.UNDMED_Codigo);

                if (j % 2 == 0) {
                    clase = "itemParTabla";
                } else {
                    clase = "itemImparTabla";
                }

                fila = '<tr class="' + clase + '">';
                fila += '<td width="3%"><div align="center"><font color="red"><strong><a href="#" onclick="eliminar_producto_comprobante(' + n + ');">';
                fila += '<span style="border:1px solid red;background: #ffffff;">&nbsp;X&nbsp;</span>';
                fila += '</a></strong></font></div></td>';
                fila += '<td width="4%"><div align="center">' + j + '</div></td>';
                fila += '<td width="10%"><div align="center">' + codproducto + '</div></td>';
                fila += '<td><div align="left"><input type="text" class="cajaGeneral" size="73" maxlength="250" name="proddescri[' + n + ']" id="proddescri[' + n + ']" value="' + nombre_producto + '" /></div></td>';
                fila += '<td width="10%"><div align="left">';
                if (tipo_docu != 'B')
                    fila += '<input type="text" size="1" maxlength="5" class="cajaGeneral" name="prodcantidad[' + n + ']" id="prodcantidad[' + n + ']" value="' + cantidad + '" onblur="calcula_importe(' + n + ');" onkeypress="return numbersonly(this,event,\'.\');">' + nombre_unidad;
                else
                    fila += '<input type="text" size="5" maxlength="10" class="cajaGeneral" name="prodcantidad[' + n + ']" id="prodcantidad[' + n + ']" value="' + cantidad + '" onblur="calcula_importe_conigv(' + n + ');" onkeypress="return numbersonly(this,event,\'.\');">' + nombre_unidad;

                fila += '</div></td>';
                if (tipo_docu != 'B') {
                    fila += '<td width="6%"><div align="center"><input type="text" size="5" maxlength="10" class="cajaGeneral" value="' + pu_conigv + '" name="prodpu_conigv[' + n + ']" id="prodpu_conigv[' + n + ']" onblur="modifica_pu_conigv(' + n + ');" onkeypress="return numbersonly(this,event,\'.\');" /></div></td>'
                    fila += '<td width="6%"><div align="center"><input type="text" size="5" maxlength="10" class="cajaGeneral" value="' + pu + '" name="prodpu[' + n + ']" id="prodpu[' + n + ']" value="0" onblur="modifica_pu(' + n + ');" onkeypress="return numbersonly(this,event,\'.\');" ></div></td>';
                    fila += '<td width="6%"><div align="center"><input type="text" size="5" maxlength="10" class="cajaGeneral cajaSoloLectura" name="prodprecio[' + n + ']" id="prodprecio[' + n + ']" value="' + subtotal + '" readonly="readonly">';
                }
                else {
                    fila += '<td width="6%"><div align="center"><input type="text" size="5" maxlength="10" class="cajaGeneral" value="' + pu + '" name="prodpu_conigv[' + n + ']" id="prodpu_conigv[' + n + ']" value="0" onblur="calcula_importe_conigv(' + n + ');" onkeypress="return numbersonly(this,event,\'.\');"></div></td>';
                    fila += '<td width="6%"><div align="center"><input type="text" size="5" maxlength="10" class="cajaGeneral" name="prodprecio_conigv[' + n + ']" id="prodprecio_conigv[' + n + ']" value="0" readonly="readonly"></div></td>';
                }
                if (tipo_docu != 'B')
                    fila += '<td width="6%" style="display:none"><div align="center"><input type="hidden" size="5" class="cajaGeneral cajaSoloLectura" name="prodigv[' + n + ']" id="prodigv[' + n + ']" readonly="readonly"></div></td>';
                fila += '<td width="6%" style="display:none"><div align="center">';
                fila += '<input type="hidden" value="n" name="detaccion[' + n + ']" id="detaccion[' + n + ']">';
                fila += '<input type="hidden" name="prodigv100[' + n + ']" id="prodigv100[' + n + ']" value="' + igv + '">';
                fila += '<input type="hidden" value="" name="detacodi[' + n + ']" id="detacodi[' + n + ']">';
                fila += '<input type="hidden" name="proddescuento100[' + n + ']" id="proddescuento100[' + n + ']" value="' + descuento + '">';

                if (tipo_docu != 'B') {
                    if (tipo_oper == 'C')
                        fila += '<input type="hidden" size="1" name="proddescuento[' + n + ']" id="proddescuento[' + n + ']" onblur="calcula_importe2(' + n + ');" />';
                    else
                        fila += '<input type="hidden" size="1" name="proddescuento[' + n + ']" id="proddescuento[' + n + ']" onblur="calcula_importe2(' + n + ');" />';


                }
                else {
                    fila += '<input type="hidden" name="proddescuento_conigv[' + n + ']" id="proddescuento_conigv[' + n + ']" onblur="calcula_importe2_conigv(' + n + ');" />';
                }
                fila += '<input type="hidden" name="flagBS[' + n + ']" id="flagBS[' + n + ']" value="' + flagBS + '">';
                fila += '<input type="hidden" class="cajaMinima" name="prodcodigo[' + n + ']" id="prodcodigo[' + n + ']" value="' + producto + '">';
                fila += '<input type="hidden" class="cajaMinima" name="produnidad[' + n + ']" id="produnidad[' + n + ']" value="' + unidad_medida + '">';
                fila += '<input type="hidden" class="cajaMinima" name="flagGenIndDet[' + n + ']" id="flagGenIndDet[' + n + ']" value="' + flagGenInd + '">';
                fila += '<input type="hidden" class="cajaPequena2" name="prodcosto[' + n + ']" id="prodcosto[' + n + ']" value="' + costo + '" readonly="readonly">';
                fila += '<input type="hidden" size="5" class="cajaGeneral cajaSoloLectura" name="prodimporte[' + n + ']" id="prodimporte[' + n + ']" value="' + total + '" readonly="readonly">';
                fila += '</div></td>';
                fila += '</tr>';

                $("#tempde_tbl").append(fila);

            }


            $('#moneda').val(moneda);

            n++;
        })
        if (n >= 0)
            if (tipo_docu != 'B' && tipo_docu != 'N')
                calcula_totales();
            else
                calcula_totales();

    });
}

function enviarsunat_old(comprobante){
    $(".disparador_"+comprobante).css("visibility","hidden");
    $(".loading_"+comprobante).show();
    location.href = base_url + "index.php/ventas/notacredito/envioSunatFac/" + comprobante;
}

function enviarsunat(comprobante, pos) {
    tipo_oper = $("#tipo_oper").val();
    var url = base_url + "index.php/ventas/notacredito/envioSunatFac/" + comprobante;

    //var disparadorHtml = $(".disparador_data_"+pos).html();
    var sendHtml = $(".enviarcorreo_data_"+pos).html("");
    var editarHtml = $(".editar_data_"+pos).html();

    $(".editar_data_"+pos).html("");
    $(".disparador_data_"+pos).html("");

    $.ajax({
        type: "POST",
        url: url,
        data: { comprobante: comprobante },
        dataType: 'json',
        beforeSend: function (data) {
            $(".disparador_"+pos+" .icon-loading").show();
        },
        error: function (data) {
            $(".disparador_"+pos+" .icon-loading").hide();
        },
        success: function (data) {
            switch (data.result){
                case 'success':
                    editarHtml = '<img src="' + base_url + 'images/icono_aprobar.png" width="16" height="16" border="0" title="Completado">';
                    $(".editar_data_"+pos).html(editarHtml);

                    if (tipo_oper == "V"){
                        sendHtml = '<a onclick="open_mail(' + comprobante + ')" href="#" class="enviarcorreo"><img src="' + base_url + 'images/send.png" width="16" height="16" border="0" title="Enviar via correo"></a>';
                        $(".pdfSunat_data_"+pos).html(data.pdf);
                        $(".docDestino_data_"+pos).html(data.docDestino);

                        $(".enviarcorreo_data_"+pos).html(sendHtml);
                    }
                    break;
                case 'error':
                    $(".editar_data_"+pos).html(editarHtml);
                    disparadorHtml = '<a href=javascript:; onclick="enviarsunat(' + comprobante + ', ' + pos +')">Aprobar</a> <br> <span class="detallesWrong">Denegado <span class="detallesWrong2"> ' + data.msg + ' </span> </span>';
                    $(".disparador_"+pos+" .disparador_data_"+pos).html(disparadorHtml);
                    break;
            }
            $(".disparador_"+pos+" .icon-loading").hide();
        }
    });
}

    function seleccionar_comprobante(comprobante){
        var url = base_url + "index.php/ventas/notacredito/relacionar_comprobante";

        $.ajax({
                url: url,
                type: "POST",
                data: {
                    comprobante: comprobante
                },
                dataType: "json",
                beforeSend: function(data){
                    /*$("#cboVendedor option:selected").each(function () {
                        $(this).removeAttr('selected'); 
                    });*/
                    $("#moneda option:selected").each(function () {
                        $(this).removeAttr('selected'); 
                    });
                    $("#almacen option:selected").each(function () {
                        $(this).removeAttr('selected'); 
                    });
                },
                success: function (data) {
                    if (data.result == "success"){

                        $("#guiaReferente").val(data.info.comprobante);
                        $("#serieguiaver").html("Documento Relacionado: " + data.info.serie + " - " + data.info.numero);
                        $("#serieguiaver").show();

                        $('#idSerie').val(data.info.serie);
                        $('#idNumero').val(data.info.numero);
                        $('#origenDocumento').val(data.info.tipo_documento);
                        $('#descuento').val(data.info.descuento);
                        
                        if (data.info.moneda != "" && data.info.moneda != null)
                            $("#moneda > option[value="+data.info.moneda+"]").attr("selected",true);

                        if (data.info.almacen != "" && data.info.almacen != null)
                            $("#almacen > option[value="+data.info.almacen+"]").attr("selected",true);

                        /**** PRODUCTOS ****/
                        var url_temp = base_url + "index.php/maestros/temporaldetalle/obtener_comprobantes_temproductos";
                        var tempSession = $("#tempSession").val();
                        $.ajax({
                            url: url_temp,
                            type:"POST",
                            data:{comprobante:comprobante,tabla:'notas',tempSession:tempSession},
                            dataType:"json",
                            success:function(data){
                                if (data.message == '1') {
                                    var color = "rgba(9,149,239,0.7)";
                                    array_temporal_producto(data.datos,'n',color);
                                    tipo_afectacion_temproductos();
                                }
                            }
                        });
                    }
                },
                complete: function(data){
                    calcular_totales_tempdetalle();
                }
            });
    }

function calcular_totales_tempdetalle(){
    
    n = document.getElementById('tempde_tbl').rows.length;
    importe_total = 0;
    igv_total = 0;
    descuento_total = 0;
    precio_total = 0;

    igvtotal = 0;

    gravada_total = 0;
    exonerado_total = 0;
    inafecto_total = 0;
    gratuito_total = 0;
    
    preciototal = 0;
    importetotal = 0;
    tbolsa = 0;

    igv = $("#igv").val();
    descuento = $("#descuento").val(); 
    if (igv == null || igv == undefined) { igv = 18;}
    if (descuento == null || descuento == undefined ) { descuento = 0;}
    for(i=0;i<n;i++){//Estanb al reves los campos
        a = "prodimporte["+i+"]";
        b = "prodigv["+i+"]";
        c = "proddescuento["+i+"]";
        d = "prodprecio["+i+"]";
        e = "detaccion["+i+"]";
        f = "tafectacion["+i+"]";
        g = "icbper["+i+"]";

        if(document.getElementById(e) != null && document.getElementById(e).value != 'e' && document.getElementById(e).value != 'EE'){
            importeBolsa = parseFloat(document.getElementById(a).value);
            precio = parseFloat(document.getElementById(d).value); //subTotal || cantidad * precio sin igv
            afectacion = document.getElementById(f).value; // SUMA DE IGV
            icbper = document.getElementById(g).value; // IMPUESTO POR BOLSA

            if (afectacion == "1"){ // GRAVADA
                gravada_total += precio;

                //igvTo = parseFloat(document.getElementById(b).value); // SUMA DE IGV
                //igvtotal = (igvTo + igvtotal);
            }
            else
                if (afectacion == "8"){ // EXONERADO
                    exonerado_total += precio;
                }
            else
                if (afectacion == "9" || afectacion == '16'){ // INAFECTO O EXPORTACION
                    inafecto_total += precio;
                }
            else{ // GRATUITA
                gratuito_total += precio;
            }

            if ( icbper == "1" ){
                tbolsa += importeBolsa;
            }
        }
    }
    
    descuento_gravada = gravada_total * parseFloat(descuento/100);
    descuento_exonerado = exonerado_total * parseFloat(descuento/100);
    descuento_inafecto = inafecto_total * parseFloat(descuento/100);

    // AL GRATUITO NO SE LE APLICA EL DESCUENTO, "DE POR SI YA ES GRATUITO ;) "
    descuento_total = descuento_gravada + descuento_exonerado + descuento_inafecto;
    
    gravada_total = gravada_total - descuento_gravada;
    exonerado_total = exonerado_total - descuento_exonerado;
    inafecto_total = inafecto_total - descuento_inafecto;

    precio_total = gravada_total + exonerado_total + inafecto_total;
    igvtotal = parseFloat( (gravada_total*igv) / 100 );

    // IMPORTE TOTAL NO INCLUYE DESCUENTO TOTAL. EL DESCUENTO YA FUE RESTADO DE LAS AFECTACIONES
    importetotal = parseFloat(precio_total + igvtotal + tbolsa);

    /*$("#gravadatotal").val(gravada_total.format(false));
    $("#exoneradototal").val(exonerado_total.format(false));
    $("#inafectototal").val(inafecto_total.format(false));
    $("#gratuitatotal").val(gratuito_total.format(false));*/

    $("#gravadatotal").val(gravada_total);
    $("#exoneradototal").val(exonerado_total);
    $("#inafectototal").val(inafecto_total);
    $("#gratuitatotal").val(gratuito_total);    

    //php # AHORA SI INCLUIMOS EL DESCUENTO TOTAL, PARA MOSTRARLO CORRECTAMENTE EN LA VISTA Y GUARDAR EN LA DB 
    
    /*$("#preciototal").val(precio_total.format(false));  //val(precio_total)
    $("#descuentotal").val(descuento_total.format(false));
    $("#igvtotal").val(igvtotal.format(false));  //val(igv_total)
    $("#importetotal").val(importetotal.format(false));  //val(importe_total)
    $("#importeBolsa").val(tbolsa.format(false));*/

    $("#preciototal").val(precio_total);  //val(precio_total)
    $("#descuentotal").val(descuento_total);
    $("#igvtotal").val(igvtotal);  //val(igv_total)
    $("#importetotal").val(importetotal);  //val(importe_total)
    $("#importeBolsa").val(tbolsa);    


    // SI TIENE RETENCIÓN

    if ( $("#applyRetencion").is(":checked") == true ){
        importeR = ( gravada_total * $("#retencion_porc").val() / 100 );
        importeTMR = importetotal - importeR;

        $(".importe_retencion_span").html( importeR.toFixed(2) );
        $(".importe_retencion").val( importeTMR.toFixed(2) );
    }
}


function abrir_pdf_envioSunat(codigo){
    url = base_url+"index.php/ventas/notacredito/consutarRespuestaPdfsunat/"+codigo;
    $.ajax({
        type: "POST",
        url: url,
        data: codigo,
        dataType: 'json',
        async: false,
        beforeSend: function (data) {
            console.log(data);
        },
        error: function (data) {
             console.log(data);
        },
        success: function (data) {
           url = data.respuestas_enlacepdf;
        }
    });
    
    //url= "https://www.pse.pe/cpe/da44582d-cfae-4d52-bd80-4ad30d84ae2f-d0a96cae-fa1a-40cc-8ae4-1f244459b524.pdf"
    window.open(url,'Formulario Ubigeo','menubar=no,resizable=no,width=800,height=700');
}