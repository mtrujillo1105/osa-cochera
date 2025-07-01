var base_url;
jQuery(document).ready(function () {

    base_url = $("#base_url").val();

    tipo_codificacion = $("#tipo_codificacion").val();

    $("#nuevaGuiatrans").click(function () {
        url = base_url + "index.php/almacen/guiatrans/nueva" + "/";
        location.href = url;
    });
    $("#grabarGuiatrans").click(function () {
        codigo = $("#codigo_guiatrans").val();
        
        /**validacion de que exista ma misma cantidad de productos con serie ingresada**/
        //n = document.getElementById('tblDetalleGuiaTrans').rows.length; OLD
        n = document.getElementById('tempde_tbl').rows.length;
        if(n!=0){
         var  isSalir=false;
            for ( x=0; x<n; x++){
                valor= "flagGenIndDet["+x+"]"; 
                var  valor_flagGenIndDet = document.getElementById(valor).value ;
                valorAccion="detaccion["+x+"]"; 
                var  valorAccionReal = document.getElementById(valorAccion).value ;
                if(valor_flagGenIndDet=='I'  && (valorAccionReal!=null  &&  valorAccionReal!='e')){
                    valor= "prodcodigo["+x+"]"; 
                    var  valorProducto = document.getElementById(valor).value ;
                       
                    valor= "prodcantidad["+x+"]"; 
                    var  valorCantidad = document.getElementById(valor).value ;
                    valorAlmacen= "almacenProducto["+x+"]"; 
                    var  valorAlmacen = document.getElementById(valorAlmacen).value ;
                    
                    
                    /**verifico si ese producto seriado del almacen origen esta inventariado en el almacen destino***/     
                    
                    almacenDestino = $('#almacen_destino').val();
                    urlVerificacion = base_url + "index.php/almacen/producto/verificarInventariadoAlmacen/"+valorProducto+"/"+almacenDestino+"/"+true;
                    $.ajax({
                        async: false,
                        url: urlVerificacion,
                        beforeSend: function (data) {
                        },  
                        error: function (data) {
                            $('img#loading').css('visibility', 'hidden');
                            console.log(data);
                            alert('No se puedo completar la operación - Revise los campos ingresados.');
                            isSalir=true;
                            return false;
                        },
                        success: function (data) {
                            $('img#loading').css('visibility', 'hidden');
                            if(data==0){
                               valorPD= "proddescri["+x+"]"; 
                               var  valorPDVA = document.getElementById(valorPD).value ;
                               alert("producto : "+valorPDVA+", no se encuentra inventariado en Almacen de Destino.");
                               trTabla=x;
                               document.getElementById(trTabla).style.background = "#Eec0000";
                               isSalir=true;
                               return false;
                            }
                            
                        }
                     });
                    
                    /**fin de verificacion**/
                    if(isSalir==true){
                        break;
                    }   
                    /**fin de verificacion**/

                       /**verificar si existe la misma cantidad por producto y seria**/
                       urlVerificacion = base_url + "index.php/ventas/comprobante/verificacionCantidadJson";
                       $.ajax({
                           type: "POST",
                           async: false,
                           url: urlVerificacion,
                           data: {valorProductoJ:valorProducto,valorCantidadJ:valorCantidad,almacen:valorAlmacen},
                           beforeSend: function (data) {
                           },  
                           error: function (data) {
                               $('img#loading').css('visibility', 'hidden');
                               console.log(data);
                               alert('No se puedo completar la operación - Revise los campos ingresados.')
                           },
                           success: function (data) {
                               $('img#loading').css('visibility', 'hidden');
                               if(data==0){
                                    valorPD= "proddescri["+x+"]"; 
                                    var  valorPDVA = document.getElementById(valorPD).value ;
                                    if (confirm("cantidad por producto y serie no coinciden - "+valorPDVA+"<br>Si esta seguro/a de haber cargado todos los imeis ignore este mensaje y presione aceptar.")){
                                        trTabla=x;
                                        document.getElementById(trTabla).style.background = "#ffadad";
                                        isSalir=false;
                                        return false;
                                    }
                                    else{
                                        trTabla=x;
                                        document.getElementById(trTabla).style.background = "#ffadad";
                                        isSalir=true;
                                        return false;
                                    }
                               }
                           }
                        });
               
                       /**fin de verificacion**/
                       if(isSalir==true){
                        break;
                       }   
                   }
                   
               }
            
            
            if(isSalir==true){
                //  $('#grabarComprobante').css('visibility', 'visible');
                $('img#loading').css('visibility', 'hidden');
                return false;
            }
       }
        
        /**fin de validacion**/
        
        $('img#loading').css('visibility', 'visible');
        // Sirve para editar y insertar
        url = base_url + "index.php/almacen/guiatrans/grabar";

        dataString = $('#frmGuiatrans').serialize();
        $.post(url, dataString, function (data) {
            $('img#loading').css('visibility', 'hidden');
            switch (data.result) {
                case 'ok':
                    location.href = base_url + "index.php/almacen/guiatrans/listar";
                    break;
                case 'error':
                    $('input[type="text"][readonly!="readonly"], select, textarea').css('background-color', '#FFFFFF');
                    $('#' + data.campo).css('background-color', '#FFC1C1').focus();
                    break;
                case 'error2':
                    $('input[type="text"][readonly!="readonly"], select, textarea').css('background-color', '#FFFFFF');
                    var element = document.getElementById(data.campo);
                    element.style.backgroundColor = '#FFC1C1';
                    break;
            }
        }, 'json');
    });
    $("#limpiarGuiatrans").click(function () {

        var  serieCodigo = $('#prodcodigo[1]').val();
                    
        if (serieCodigo != null)
            alert('Antes de salir de este comprobante debe regresar todas las series/productos agregados.\nPresione el icono "Flag o bandera" verde para regresar las series/productos .');
        else{
            url = base_url + "index.php/almacen/guiatrans/listar/";
            location.href = url;
        }
    });
    $("#cancelarGuiatrans").click(function () {
        var  serieCodigo = $('#prodcodigo[1]').val();
                    
        if (serieCodigo != null)
            alert('Antes de salir de este comprobante debe regresar todas las series/productos agregados.\nPresione el icono "Flag o bandera" verde para regresar las series/productos .');
        else{
            url = base_url + "index.php/almacen/guiatrans/listar/";
            location.href = url;
        }
    });

    $("#linkVerSerieNum").click(function () {
        var temp = $("#linkVerSerieNum p").html();
        var serienum = temp.split('-');
        switch (tipo_codificacion) {
            case '1':
                $("#numero").val(serienum[1]);
                break;
            case '2':
                $("#serie").val(serienum[0]);
                $("#numero").val(serienum[1]);
                break;
        }
    });

    $('#buscar_producto').keyup(function (e) {
        var key = e.keyCode || e.which;
        if (key == 13) {
            if ($(this).val() != '') {
                $('#linkSelecProducto').attr('href', base_url + 'index.php/almacen/producto/ventana_selecciona_producto/V/' + $('#flagBS').val() + '/' + $('#buscar_producto').val()).click();
            }
        }
    });

      
    $('#cantidad').bind('blur', function (e) {
        tipo_oper = $("#tipo_oper").val();
        flagGenInd = $("#flagGenInd").val();
        
        if (flagGenInd == 'I') {
                if (tipo_oper == 'V') {
                    if ($(this).val() != '') {
                        var cantidad = parseInt($(this).val());
                        var stock = parseInt($('#sotckGeneral').val());
                        if (cantidad > stock) {
                            alert('La cantidad no debe ser mayor al stock.');
                            $(this).val('').focus();
                            return false;
                        }
                        ventana_producto_serie_1();
                    }
                } 
        }
    });
    
    

    $('#almacen_destino').change(function () {
        if ($('#almacen_destino').val() != '' && $('#almacen_destino').val() == $('#almacen').val()) {
            alert('El ALMACEN DESTINO debe ser diferente al ALMACEN ORIGEN.');
            $('#almacen_destino').val('').focus();
            return false;
        }
        return true;
    });

    $('#linkEnviarProhibido').click(function () {
        alert('Aun no se ah confirmado la transferencia!');
    });

    $('#idRecibido').click(function () {
        alert('Transferencia realizada correctamente!');
    });
    $('#idRecibido2').click(function () {
        alert('Transferencia realizada correctamente!');
    });

    $('#idDevolucion').click(function () {
        alert('La transferencia fue devuelta a su ORIGEN!');
    });

    $('#linkAnulado').click(function(){
        alert('Transferencia anulada por el origen');
    });

});

/********************************************************************************************/

// Esto de prueba por si acaso existe un error
function cargarTransferencia2(estado, guiaTrans){
    var codUsuario = $('#codUsuario').val();
    location.href = base_url + 'index.php/almacen/guiatrans/cargarTransferencia/'+codUsuario+"/"+guiaTrans+"/"+estado;
}

function cargarTransferencia(estado, guiaTrans) {
    
    var mensajeConfirmacion = "";
    switch (estado) {
        case '0':
        case 0:
            mensajeConfirmacion = "¿Estas seguro(a) de realizar la transferencia?";
            break;
        case '1':
        case 1:
            mensajeConfirmacion = "¿Estas seguro(a) de confirmar el transito del envio?";
            break;
        case '2':
        case 2:
            mensajeConfirmacion = "¿Estas seguro(a) de cancelar la transferencia?";
            break;
    }

    var confirmacion = confirm(mensajeConfirmacion);

    if (confirmacion == true) {
        var codUsuario = $('#codUsuario').val();
        if (estado <= -1) {
            alert('Existe un error con la transferencia');
            return false;
        } else if (guiaTrans <= 0) {
            alert('Existe un error con la transferencia');
            return false;
        } else {
            $("#trans"+guiaTrans).hide();
            url = base_url + 'index.php/almacen/guiatrans/cargarTransferencia';

            $.ajax({
                url: url,
                type: "POST",
                data: {
                    estado: estado,
                    guiaTrans: guiaTrans,
                    usuario: codUsuario
                },
                dataType: "json",
                beforeSend: function (data) {

                },
                success: function (data) {
                    console.log(data);
                    // Sirve para verificar si el movimiento ya fue ejecutado
                    if(typeof(data.movimiento) != "undefined" && data.movimiento == "Movimiento ya realizado por el DESTINO") {
                        alert('EL movimiento ya fue ejecutado por el DESTINO! \nVUELVA A RECARGAR LA PAGINA');
                    }else {

                        var redirect_url = "";

                        flag = data.flagEstado;
                        usuario_guia = data.usuario_guia;
                        guia_trans = data.guia_trans;
                        estado_trans = data.estado_trans;
                        updateGuiaInySa = data.updateGuiaInySa;
                        switch (flag) {
                            case '0':
                            case 0:
                                if (updateGuiaInySa == true) {
                                    redirect_url = base_url + "index.php/almacen/guiatrans/listar/";
                                } else {
                                    $('#mensajeTransferencia').val("<img src='" + base_url + "images/verguenza.gif' alt='Lo siento' > ¡Lo siento! Ah ocurrido un error al realizar la transferencia!");
                                }
                                break;
                            case '1':
                            case 1:
                                if (updateGuiaInySa == true) {
                                    redirect_url = base_url + "index.php/almacen/guiatrans/listar/";
                                } else {
                                    $('#mensajeTransferencia').val("<img src='" + base_url + "images/verguenza.gif' alt='Lo siento' > ¡Lo siento! Ah ocurrido un error al confirmar la transferencia!");
                                }
                                break;
                            case '2':
                            case 2:
                                if (updateGuiaInySa == true) {
                                    redirect_url = base_url + "index.php/almacen/guiatrans/listar/";
                                } else {
                                    $('#mensajeTransferencia').val("<img src='" + base_url + "images/verguenza.gif' alt='Lo siento' > Ah ocurrido un error al transitar la transferencia!");
                                }
                                break;
                            case '3':
                            case 3:
                                if (updateGuiaInySa == true) {
                                    redirect_url = base_url + "index.php/almacen/guiatrans/listar/";
                                } else {
                                    $('#mensajeTransferencia').val("<img src='" + base_url + "images/verguenza.gif' alt='Lo siento' > Ah ocurrido un error al cancelar la transferencia!");
                                }
                                break;
                        }

                        location.href = redirect_url;
                    }
                },
                error: function (HXR, error, xd) {
                    console.log('errorr');
                }
            });

        }
    } else {
        return false;
    }

}

function editar_guiatrans(guiatrans) {
    location.href = base_url + "index.php/almacen/guiatrans/editar/" + guiatrans;
}
function listar_unidad_medida_producto(producto) {
    limpiar_combobox('unidad_medida');

    base_url = $("#base_url").val();
    url = base_url + "index.php/almacen/producto/listar_unidad_medida_producto/" + producto;
    select = document.getElementById('unidad_medida');
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
            select.appendChild(opt);
        });
        var nombre;
        if (nombrecorto_producto)
            nombre = nombrecorto_producto;
        else
            nombre = nombre_producto;
        if (marca)
            nombre += ' / Marca:' + marca;
        if (modelo)
            nombre += ' / Modelo: ' + modelo;
        if (presentacion)
            nombre += ' / Prest: ' + presentacion;
        $("#nombre_producto").val(nombre);
    });
}
function agregar_producto_guiatrans() {
    flagBS = $("#flagBS").val();

    if ($("#producto").val() != '') {
        $("#codproducto").val($("#producto").val());
    }

    if ($("#codproducto").val() == '') {
        alert('Ingrese el producto.');
        $("#codproducto").focus();
        return false;
    }
    if ( $("#cantidad").val() == '' || $("#cantidad").val() == 0 ) {
        alert('Ingrese una cantidad.');
        $("#cantidad").focus();
        return false;
    }

    var ccantidad = $("#cantidad").val();
    var csotckGeneral = $("#sotckGeneral").val();
    
    ccantidad = parseInt(ccantidad);
    csotckGeneral = parseInt(csotckGeneral);
    
    if ( ccantidad > csotckGeneral ) {
        alert('La cantidad ingresada supera el stock del almacen Origen.');
        $("#cantidad").focus();
        return false;
    }

    if ($("#unidad_medida").val() == '') {
        $("#unidad_medida").focus();
        alert('Seleccione una unidad de medida.');
        return false;
    }
    if($("#buscar_producto").val()==''){
        $("#buscar_producto").focus();
        alert('Seleccine el producto.');
        return false;
    }
    if($("#nombre_producto").val()==''){
        $("#nombre_producto").focus();
        alert('Seleccione el producto.');
        return false;
    }
    if($('#almacen').val() == 0 || $('#almacen') == '0'){
        $("#almacen").focus();
        alert('Seleccione el almacen de origen');
        return false;
    }

    codproducto = $("#codproducto").val();
    producto = $("#producto").val();
    nombre_producto = $("#nombre_producto").val();
    cantidad = $("#cantidad").val();
    almacenProducto=$("#almacenProducto").val();
    
    costo = parseFloat($("#costo").val());
    unidad_medida = '';
    nombre_unidad = '';
    if (flagBS == 'B') {
        unidad_medida = $("#unidad_medida").val();
        nombre_unidad = $('#unidad_medida option:selected').html()
    }

    flagGenInd = $("#flagGenInd").val();
    n = document.getElementById('tblDetalleGuiaTrans').rows.length;
    j = n + 1;
    if (j % 2 == 0) {
        clase = "itemParTabla";
    } else {
        clase = "itemImparTabla";
    }

    fila = '<tr id="' + n + '" class="' + clase + '">';
    fila += '<td width="3%"><div align="center"><font color="red"><strong><a href="javascript:;" onclick="eliminar_producto_guiatrans(' + n + ');">';
    fila += '<span style="border:1px solid red;background: #ffffff;">&nbsp;X&nbsp;</span>';
    fila += '</a></strong></font></div></td>';
    fila += '<td width="4%"><div align="center">' + j + '</div></td>';
    fila += '<td width="10%"><div align="center">';
    fila += '<input type="hidden" class="cajaMinima" name="prodcodigo[' + n + ']" id="prodcodigo[' + n + ']" value="' + producto + '">' + codproducto;
    fila += '<input type="hidden" class="cajaMinima" name="produnidad[' + n + ']" id="produnidad[' + n + ']" value="' + unidad_medida + '">';
    fila += '<input type="hidden" class="cajaMinima" name="flagGenIndDet[' + n + ']" id="flagGenIndDet[' + n + ']" value="' + flagGenInd + '">';
    fila += '</div></td>';
    fila += '<td><div align="left">';
    fila += '<input type="text" class="cajaGeneral" style="width:667px;" maxlength="250" name="proddescri[' + n + ']" id="proddescri[' + n + ']" value="' + nombre_producto + '">';
    fila += '</div></td>';
    fila += '<td width="10%"><div align="left">';
    fila += '<input type="text" class="cajaGeneral" size="1" maxlength="5" name="prodcantidad[' + n + ']" id="prodcantidad[' + n + ']" value="' + cantidad + '" onkeypress="return numbersonly(this,event,\'.\');"> ' + nombre_unidad;

    if(flagGenInd!=null && flagGenInd=='I'){
        fila +='<a href="javascript:;" id="imgEditarSeries' + n + '" onclick="ventana_producto_serie('+ n +')" ><img src="'+base_url+'images/flag-green_icon.png" width="20" height="20" class="imgBoton"></a>';
        /**vamos al metodo de producto serie para eliminar el de la secciontemporal y agregar el de la seccion Real**/
        var url = base_url+"index.php/almacen/producto/agregarSeriesProductoSessionReal/"+producto+"/"+almacenProducto;
         $.get(url,function(data){});
   }
    
    
    fila += '<input type="hidden" class="cajaMinima" name="detacodi[' + n + ']" id="detacodi[' + n + ']">';
    fila += '<input type="hidden" class="cajaMinima" name="detaccion[' + n + ']" id="detaccion[' + n + ']" value="n">';
    fila += '<input type="hidden" name="almacenProducto[' + n + ']" id="almacenProducto[' + n + ']" value="' + almacenProducto + '"/>';
    fila += '<input type="hidden" class="cajaPequena2" name="prodcosto[' + n + ']" id="prodcosto[' + n + ']" value="' + costo + '" readonly="readonly">';
    fila += '</div></td>';
    fila += '</tr>';
    $("#tblDetalleGuiaTrans").append(fila);

    inicializar_cabecera_item();
    return true;
}
function eliminar_producto_guiatrans(n) {
    if (confirm('¿Esta seguro que desea eliminar este producto?')) {
        var Tserie = true;
        
        if ( $("#flagGenIndDet[" + n + "]").length > 0 && $("#flagGenIndDet[" + n + "]").val() == "I" || $("#imgEditarSeries" + n).length > 0 )
            Tserie = confirm('Antes de eliminar este producto asegurese de regresar todas las series/productos.\nPresione el icono "Flag o bandera" verde y regrese las series.\nSi ha verificado que no hay series asignadas presione aceptar para continuar.');
            
        if ( Tserie == true ){
            tabla = document.getElementById('tblDetalleGuiaTrans');
            a = "detacodi[" + n + "]";
            b = "detaccion[" + n + "]";
            fila = document.getElementById(a).parentNode.parentNode.parentNode;
            fila.style.display = "none";
            document.getElementById(b).value = "e";
        }
    }
}

function obtener_producto_DEL_FUNCTION() {
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

function inicializar_cabecera_item() {
    $("#producto").val('');
    $("#buscar_producto").val('');
    $("#codproducto").val('');
    $("#nombre_producto").val('');
    $("#cantidad").val('');
    $("#costo").val('0');
    $("#nombre_unidad").val('');
    $("#unidad_medida").val('0');
    $("#flagGenInd").val('');
    $('#sotckGeneral').val("");
    limpiar_combobox('unidad_medida');
}
function guiatrans_ver_pdf(guiatrans, img = 0) {
    url = base_url + "index.php/almacen/guiatrans/guiatrans_ver_pdf/" + guiatrans + "/pdf/" + img;
    window.open(url, '', "width=800,height=600,menubars=no,resizable=no;")
}
function guiatrans_ver_pdf_conmenbrete(guiatrans) {
    tipo_oper = $("#tipo_oper").val();
    url = base_url + "index.php/almacen/guiatrans/guiatrans_ver_pdf_conmenbrete/" + guiarem + "/0";
    window.open(url, '', "width=800,height=600,menubars=no,resizable=no;")
}
