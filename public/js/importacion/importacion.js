
function verificar_Inventariado_producto(){
    base_url = $("#base_url").val();
    tipo_oper = $("#tipo_oper").val();
    url = base_url + "index.php/ventas/comprobante/verificar_inventariado/";
    producto=$("#producto").val();
    prodNombre=$("#nombre_producto").val();
    dataEnviar="enviarCodigo="+producto;  
       $.ajax({url: url,
        data:dataEnviar,
        type:'POST', 
        success: function(result){
            if (result=="0") {
                prodNombre="<p>"+$("#nombre_producto").val()+"</p>";
                $('#popup').fadeIn('slow');
                $('.popup-overlay').fadeIn('slow');
                $('.popup-overlay').height($(window).height());
                $("#contendio").html(prodNombre);
                return false;
            }
        
    }}); 

}
function comprobante_conmenbrete(comprobante) {
   tipo_oper = $("#tipo_oper").val();
   // alert(tipo_oper);
  // var url = base_url + "index.php/maestros/configuracionimpresion/impresionDocumento/"+comprobante+"/8/1";
  //  window.open(url, '', "width=800,height=600,menubars=no,resizable=no;");
    var url = base_url + "index.php/ventas/comprobante/comprobante_ver_pdf_conmenbrete1/" + tipo_oper + "/" + comprobante + "/" + tipo_docu + "/0";
    window.open(url, '', "width=800,height=600,menubars=no,resizable=no;");
}
function ejecutarModal(){
    $("#buscar_producto").val("").focus();
    $('#popup').fadeOut('slow');
    $('.popup-overlay').fadeOut('slow');
    return false;
}

jQuery(document).ready(function () {

    base_url = $("#base_url").val();
    tipo_oper = $("#tipo_oper").val();
    tipo_docu = $("#tipo_docu").val();
    contiene_igv = $("#contiene_igv").val();
    $(document).ready(function(){
  $('#open').click(function(){
        $('#popup').fadeIn('slow');
        $('.popup-overlay').fadeIn('slow');
        $('.popup-overlay').height($(window).height());
        return false;
    });
    
    $('#close').click(function(){
        $('#popup').fadeOut('slow');
        $('.popup-overlay').fadeOut('slow');
        return false;
    });

});

    $("#nuevaComprobante").click(function () {
        url = base_url + "index.php/ventas/comprobante/comprobante_nueva" + "/" + tipo_oper + "/" + tipo_docu;
        location.href = url;
    });

    //cambir de Tipo de Comprobante(Factura,Boleta,Comprobante)
    $("#cboTipoDocu").change(function () {
        tipo_docu = $("#cboTipoDocu").val();
        document.forms['frmComprobante'].action = base_url + "index.php/ventas/comprobante/comprobante_nueva" + "/" + tipo_oper + "/" + tipo_docu;
        $("#frmComprobante").submit();
    });
    //Guardar el comprobante
  
    $("#grabarComprobante").click(function () {
        alert("hola aqui estas");
        var n = document.getElementById('tblDetalleComprobante').rows.length;
        if (n == 0) {
            alert("Ingrese un producto.");
            return false;
        } else if ($("#serie").val() == "") {
            $("#serie").focus();
            alert("Ingrese la serie.");
            return false;
        }/*else if ($('#observacion').val() == "") {
            $("#observacion").focus();
            alert("Ingrese una observacion.");
            return false;
        }*/ else if ($("#moneda").val() == '') {
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
                url = base_url + "index.php/ventas/importacion/comprobante_insertar";
            }
            else {
                url = base_url + "index.php/ventas/importacion/comprobante_modificar";
            }

            dataString = $('#frmComprobante').serialize();

            $.post(url, dataString, function (data) {
                    $('img#loading').css('visibility', 'hidden');
                    $('#grabarComprobante').hide('fast');
                    //console.log(data.mensaje);

                    if (data.mensaje == "SUCCESS") {
                        location.href = base_url + "index.php/ventas/importacion/comprobantes" + "/" + tipo_oper + "/" + tipo_docu;
                    } else {
                        $('#notaMensaje').html(data.descripcion);
                        $('#grabarComprobante').show('fast');
                    }
                }, 'json'
            );
        }
    });
    $("#limpiarComprobante").click(function () {
        url = base_url + "index.php/ventas/comprobante/comprobantes" + "/" + tipo_oper + "/" + tipo_docu + "/0/1";
        location.href = url;
    });
    $("#cancelarComprobante, #cancelarImprimirComprobante").click(function () {
        $.fancybox.close();
        url = base_url + "index.php/ventas/comprobante/comprobantes" + "/" + tipo_oper + "/" + tipo_docu;
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
        url = base_url + "index.php/ventas/comprobante/estadisticas";
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

        verPdf();
        /*if ($('#codigo').val() == '') {
            alert('Ha ocurrido un error, no se puede realizar la impresión');
            return false;
        }
        ver_comprobante_pdf($('#codigo').val());
        $("#cancelarComprobante").click();
        return true;*/
    });

    function activarBusqueda()
    {
        var action = base_url + "index.php/ventas/comprobante/buscar/"+tipo_oper+"/"+tipo_docu;
        var datos = $('#form_busqueda').serialize();
        $.ajax({
         url : action,
         data : datos,
         type: "POST",
         beforeSend: function(data){
             $('#cargando_datos').show();
         },
         success: function(data){
             $('#cargando_datos').hide();
            $('#contenedor-busqueda').html(data);
         },
         error: function(XHR, error){
             $('#cargando_datos').hide();
            console.log("Error");
         }
         });
    }

    $('#seriei, #numero, #nombre_cliente, #ruc_cliente, #ruc_proveedor, #nombre_proveedor').keyup(function(e){
        var key=e.keyCode || e.which;
        if (key==13){
            activarBusqueda();
        }
    });

    $("#buscarComprobante").click(function () {
        /*document.forms['form_busqueda'].action = base_url + "index.php/ventas/comprobante/comprobantes" + "/" + tipo_oper + "/" + tipo_docu + "/";
        $("#form_busqueda").submit();*/
        activarBusqueda();

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

    $('#buscar_cliente').keyup(function (e) {
        var key = e.keyCode || e.which;
        if (key == 13) {
            if ($(this).val() != '') {
                $('#linkSelecCliente').attr('href', base_url + 'index.php/empresa/cliente/ventana_selecciona_cliente/' + $('#buscar_cliente').val()).click();
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
                //url=base_url+'index.php/almacen/producto/ventana_selecciona_producto/'+tipo_oper+'/'+$('#flagBS').val()+'/'+$('#buscar_producto').val();
                //alert(url);
                $('#linkSelecProducto').attr('href', base_url + 'index.php/almacen/producto/ventana_selecciona_producto/'+tipo_oper+'/'+$('#flagBS').val()+'/'+$('#buscar_producto').val()+"/"+$("#almacen").val()).click();

            }
        }
    });
    //
    $('#docurefe_codigo').keyup(function (e) {
        var key = e.keyCode || e.which;
        if (key == 13) {
            if ($(this).val()!= '') {
            	
            	 if (tipo_oper == 'V') {
                     if ($('#cliente').val() == '') {
                         alert('Debe seleccionar el cliente.');
                         $('#nombre_cliente').focus();
                         return false;
                     }
            	 }else{
	            	 if ($('#proveedor').val() == '') {
	            		 alert('Debe seleccionar el proveedor.');
	                     $('#nombre_proveedor').focus();
	                     return false;
	            	 }
            	 }
            	
	                $.ajax({
	                    url: base_url + "index.php/ventas/comprobante/obtener_id_docuref",
	                    type: "POST",
	                    data: {
	                        serie_numero: $(this).val()
	                    },
	                    success: function (data) {
	                    	if(data!=""){
	                    		realizado=agregar_todo(data);
	                			if(realizado!=false){
	                	            $("#serieguiaverPre").hide(200);
	                	            $("#serieguiaverOC").hide(200);
	                	            $("#serieguiaverRecu").hide(200);
	                	            $('#ordencompra').val('');
	                			}
	                			$("#presupuesto_codigo").val("");
	                    	}else{
	                    		alert("No se encontro ninguna guia de remisión.");
	                    		
	                    	}
	                    }
	                });
            	
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
                        var stock = parseInt($('#stock').val());
                        if (cantidad > stock) {
                            alert('La cantidad no debe ser mayor al stock.');
                            $(this).val('').focus();
                            return false;
                        }

                        ventana_producto_serie_1();
                    }
                } else if (tipo_oper == 'C') {
                    ventana_producto_serie_1();
                }
        }
    });
})

var limite_detalle = 15;
function getLimite() {
    return limite_detalle;
}

function setLimite(limite) {
    limite_detalle = limite;
}

function ver_reporte_pdf() {
    var fechai = $('#fechai').val();
    var fechaf = $('#fechaf').val();
    var cliente = $('#cliente').val();//ruc_proveedor
    var producto = $('#producto').val();
    var aprobado = $('#aprobado').val();
    var ingreso = $('#ingreso').val();

    var tipo_oper = $("#tipo_oper").val();
    tipo_oper="V";

    url = base_url + "index.php/ventas/comprobante/ver_reporte_pdf/" + fechai + '_' + fechaf + '_' + cliente + '_' + producto + '_' + aprobado + '_' + ingreso+"/"+tipo_oper;
    window.open(url, '', "width=800,height=600,menubars=no,resizable=no;");
}

function ver_reporte_pdf_ventas() {
    
    var prod = $("#productoDescripcion").val();

    var anio = $("#anioVenta").val();
    var mes = $("#mesventa").val();
    var fech1 = $("#fech1").val();
    var fech2 = $("#fech2").val();
    //var depar = $("#cboDepartamento").val();
    //var prov = $("#cboProvincia").val();
    //var dist = $("#cboDistrito").val();
    var tipodocumento = $("#tipodocumento").val();
    var Prodcod="";
    //var Prodcod = $("#reporteProducto").val();
    if(anio=="0") {anio="--";} 
    if(mes=="")   {mes="--";} 
    //if(depar=="00")  {depar="--";}
    //if(prov=="00")  {prov="--";}
    //if(dist=="00")  {dist="--";}
    if(Prodcod==""|| prod =="")  {Prodcod="--";}

    if(tipodocumento=="")  {tipodocumento="--";}

    var datafechaIni="";var datafechafin="";

    if(fech1=="") {
        fech1="--";
    }else{
        fechai=$("#fech1").val().split("/"); 
        fech1=fechai[2]+"-"+fechai[1]+"-"+fechai[0];
    }

    if(fech2=="") {
        fech2="--";
    }else{
        fechaf=$("#fech2").val().split("/");
        fech2=fechaf[2]+"-"+fechaf[1]+"-"+fechaf[0];

    }

    url = base_url + "index.php/ventas/comprobante/ver_reporte_pdf_ventas/" + anio+"/" + mes+"/" + fech1+"/" + fech2+"/"+tipodocumento;
    window.open(url, '', "width=800,height=600,menubars=no,resizable=no;");
 }

function estadisticas_compras_ventas(tipo) {
    var anio = $("#anioVenta2").val();
    url = base_url + "index.php/ventas/comprobante/estadisticas_compras_ventas/" + tipo + "/" + anio;
    window.open(url, '', "width=800,height=600,menubars=no,resizable=no;");
}

function estadisticas_compras_ventas_mensual(tipo) {
    var anio = $("#anioVenta3").val();
    var mes = $("#mesVenta3").val();
    url = base_url + "index.php/ventas/comprobante/estadisticas_compras_ventas_mensual/" + tipo + "/" + anio + "/" + mes + "";
    window.open(url, '', "width=800,height=600,menubars=no,resizable=no;");
}

function estadisticas_compras_ventas_mensual_excel(tipo) {
    var anio = $("#anioVenta3").val();
    var mes = $("#mesVenta3").val();
    url = base_url + "index.php/ventas/comprobante/estadisticas_compras_ventas_mensual_excel/" + tipo + "/" + anio + "/" + mes + "";
    window.open(url, '', "width=800,height=600,menubars=no,resizable=no;");
}

function editar_comprobante(comprobante) {
    //alert(base_url)
    location.href = base_url + "index.php/ventas/comprobante/comprobante_editar/" + comprobante + "/" + tipo_oper + "/" + tipo_docu;
}

function eliminar_comprobante(comprobante) {
    if (confirm('Esta seguro que desea eliminar este comprobante?')) {
        dataString = "comprobante=" + comprobante;
        url = base_url + "index.php/ventas/comprobante/comprobante_eliminar";
        $.post(url, dataString, function (data) {
            location.href = base_url + "index.php/ventas/comprobante/comprobantes" + "/" + tipo_oper + "/" + tipo_docu;
        });
    }
}

/**
 * Metodo para enlazar el boton imprimir
 * Se le manda como parametro img "0" para no mostrar la imagen
 * @param comprobante
 */
function ver_comprobante_pdf(comprobante) {
    tipo_oper = $("#tipo_oper").val();
    var url = base_url + "index.php/ventas/comprobante/comprobante_ver_pdf_conmenbrete/" + tipo_oper + "/" + comprobante + "/" + tipo_docu + "/1";
    window.open(url, '', "width=800,height=600,menubars=no,resizable=no;");
}

function disparador(comprobante) {

    tipo_oper = $("#tipo_oper").val();
    location.href = base_url + "index.php/ventas/comprobante/disparador/" + tipo_oper + "/" + comprobante + "/" + tipo_docu;


}

//function comprobante_ver_pdf_conmenbrete(comprobante) {
//    tipo_oper = $("#tipo_oper").val();
//    alert(tipo_oper);
//    var url = base_url + "index.php/maestros/configuracionimpresion/impresionDocumento/"+comprobante+"/8/1";
//    window.open(url, '', "width=800,height=600,menubars=no,resizable=no;");
//    var url = base_url + "index.php/ventas/comprobante/comprobante_ver_pdf_conmenbrete1/" + tipo_oper + "/" + comprobante + "/" + tipo_docu + "/0";
//    window.open(url, '', "width=800,height=600,menubars=no,resizable=no;");
//}

function atras_comprobante() {
    location.href = base_url + "index.php/ventas/comprobante/comprobantes";
}
function agregar_producto_comprobante() {
    flagBS = $("#flagBS").val();

    if ($("#producto").val() == '') {
        $("#producto").focus();
        alert('Ingrese el producto.');
        return false;
    }
    if ($("#cantidad").val() == '') {
        $("#cantidad").focus();
        alert('Ingrese una cantidad.');
        return false;
    }
    if (flagBS == 'B' && $("#unidad_medida").val() == '0') {
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
    almacenProducto=$("#almacenProducto").val();
    if ($("#precio").val() != '')
        precio_conigv = $("#precio").val();
    else
        precio_conigv = 0;
    if (tipo_docu != 'B' && tipo_docu != 'N' && contiene_igv == '1')
        precio = money_format(precio_conigv * 100 / (igv + 100))
    else {
        precio=precio_conigv;
        precio_conigv = money_format(precio_conigv*(100+igv)/100);
        precio = money_format(precio_conigv * 100 / (igv + 100));
    }
    stock = parseFloat($("#stock").val());
    costo = parseFloat($("#costo").val());
    unidad_medida = '';
    nombre_unidad = '';
    if (flagBS == 'B') {
        unidad_medida = $("#unidad_medida").val();
        nombre_unidad = $('#unidad_medida option:selected').html()
    }
    flagGenInd = $("#flagGenInd").val();
    n = document.getElementById('tblDetalleComprobante').rows.length;
    var limit = getLimite();
    if (n >= limit) {

        alert('Limite del detalle de Documento');
        return false
    }
    j = n + 1;
    if (j % 2 == 0) {
        clase = "itemParTabla";
    } else {
        clase = "itemImparTabla";
    }


    fila = '<tr id="' + n + '" class="' + clase + '" t-doc="' + tipo_docu + '" >';
    fila += '<td width="3%"><div align="center"><font color="red"><strong><a href="#" onclick="eliminar_producto_comprobante(' + n + ');">';
    fila += '<span style="border:1px solid red;background: #ffffff;">&nbsp;X&nbsp;</span>';
    fila += '</a></strong></font></div></td>';
    fila += '<td width="4%"><div align="center">' + j + '</div></td>';
    fila += '<td width="8%"><div align="center">' + codproducto + '</div></td>';
    fila += '<td width="36%"><div align="left"><input type="text" class="cajaGeneral" size="60" maxlength="250" name="proddescri[' + n + ']" id="proddescri[' + n + ']" value="' + nombre_producto + '" />';
    fila += '</div></td>';
    fila += '<td width="15%"><div align="left">';
    if (tipo_docu != 'B' && tipo_docu != 'N')
        fila += '<input type="text" size="10" maxlength="10" class="cajaGeneral" name="prodcantidad[' + n + ']" id="prodcantidad[' + n + ']" value="' + cantidad + '" onblur="calcula_importe(' + n + ');" onkeypress="return numbersonly(this,event,\'.\');">' + nombre_unidad;
    else
        fila += '<input type="text" size="10" maxlength="10" class="cajaGeneral" name="prodcantidad[' + n + ']" id="prodcantidad[' + n + ']" value="' + cantidad + '" onblur="calcula_importe(' + n + ');" onkeypress="return numbersonly(this,event,\'.\');">' + nombre_unidad;

    if(flagGenInd!=null && flagGenInd=='I'){
    	fila +='<a href="javascript:;" id="imgEditarSeries' + n + '" onclick="ventana_producto_serie('+ n +')" ><img src="'+base_url+'images/flag-green_icon.png" width="20" height="20" class="imgBoton"></a>';
    	/**vamos al metodo de producto serie para eliminar el de la secciontemporal y agregar el de la seccion Real**/
        var url = base_url+"index.php/almacen/producto/agregarSeriesProductoSessionReal/"+producto+"/"+almacenProducto;
         $.get(url,function(data){});
         
   }
    fila += '</div></td>';
    if (tipo_docu != 'B' && tipo_docu != 'N') {
        fila += '<td width="6%"><div align="center"><input type="text" size="5" maxlength="10" class="cajaGeneral" value="' + precio_conigv + '" name="prodpu_conigv[' + n + ']" id="prodpu_conigv[' + n + ']" onblur="modifica_pu_conigv(' + n + ');" onkeypress="return numbersonly(this,event,\'.\');" /></div></td>'
        fila += '<td width="6%"><div align="center"><input type="text" size="5" maxlength="10" class="cajaGeneral" value="' + precio + '" name="prodpu[' + n + ']" id="prodpu[' + n + ']" value="0" onblur="modifica_pu(' + n + ');" onkeypress="return numbersonly(this,event,\'.\');" ></div></td>';
        fila += '<td width="6%"><div align="center"><input type="text" size="5" maxlength="10" class="cajaGeneral cajaSoloLectura" name="prodprecio[' + n + ']" id="prodprecio[' + n + ']" value="0" readonly="readonly">';
    }
    else {
        fila += '<td width="6%"><div align="center"><input type="text" size="5" maxlength="10" class="cajaGeneral" value="' + precio_conigv + '" name="prodpu_conigv[' + n + ']" id="prodpu_conigv[' + n + ']" onblur="modifica_pu_conigv(' + n + ');" onkeypress="return numbersonly(this,event,\'.\');" /></div></td>'
        fila += '<td width="6%"><div align="center"><input type="text" size="5" maxlength="10" class="cajaGeneral" value="' + precio + '" name="prodpu[' + n + ']" id="prodpu[' + n + ']" value="0" onblur="modifica_pu(' + n + ');" onkeypress="return numbersonly(this,event,\'.\');" ></div></td>';
        fila += '<td width="6%"><div align="center"><input type="text" size="5" maxlength="10" class="cajaGeneral cajaSoloLectura" name="prodprecio[' + n + ']" id="prodprecio[' + n + ']" value="0" readonly="readonly">';
    }
    
    fila += '<td width="6%" style="display:none;" ><div align="center"><input type="text" size="5" class="cajaGeneral cajaSoloLectura" name="prodigv[' + n + ']" id="prodigv[' + n + ']" readonly="readonly"></div></td>';
    fila += '<td width="6%" style="display:none;" ><div align="center">';
    fila += '<input type="hidden" value="n" name="detaccion[' + n + ']" id="detaccion[' + n + ']">';
    fila += '<input type="hidden" name="prodigv100[' + n + ']" id="prodigv100[' + n + ']" value="' + igv + '">';
    fila += '<input type="hidden" value="" name="detacodi[' + n + ']" id="detacodi[' + n + ']">';
    fila += '<input type="hidden" name="proddescuento100[' + n + ']" id="proddescuento100[' + n + ']" value="' + descuento + '">';
    if (tipo_docu != 'B' && tipo_docu != 'N') {
        if (tipo_oper == 'C')
            fila += '<input type="text" size="1" name="proddescuento[' + n + ']" class="proddescuento" id="proddescuento[' + n + ']" onblur="calcula_importe2(' + n + ');" />';
        else
            fila += '<input type="hidden" size="1" name="proddescuento[' + n + ']" class="proddescuento" id="proddescuento[' + n + ']" onblur="calcula_importe2(' + n + ');" />';

    } else {
        fila += '<input type="hidden" name="proddescuento[' + n + ']" id="proddescuento[' + n + ']" onblur="calcula_importe2(' + n + ');" />';
    }
    fila += '<input type="hidden" name="flagBS[' + n + ']" id="flagBS[' + n + ']" value="' + flagBS + '"/>';
    fila += '<input type="hidden" name="prodcodigo[' + n + ']" id="prodcodigo[' + n + ']" value="' + producto + '"/>';
    fila += '<input type="hidden" name="produnidad[' + n + ']" id="produnidad[' + n + ']" value="' + unidad_medida + '"/>';
    fila += '<input type="hidden" name="flagGenIndDet[' + n + ']" id="flagGenIndDet[' + n + ']" value="' + flagGenInd + '"/>';
    fila += '<input type="hidden" name="prodstock[' + n + ']" id="prodstock[' + n + ']" value="' + stock + '"/>';
    fila += '<input type="hidden" name="prodcosto[' + n + ']" id="prodcosto[' + n + ']" value="' + costo + '"/>';
    fila += '<input type="hidden" name="almacenProducto[' + n + ']" id="almacenProducto[' + n + ']" value="' + almacenProducto + '"/>';
    fila += '<input type="text" size="5" class="cajaGeneral cajaSoloLectura" name="prodimporte[' + n + ']" id="prodimporte[' + n + ']" value="0" readonly="readonly">';
    
    /**verificamos si es un servicio y a la vez verificampos si contiene guais de remiisiones asociadas**/
    var total=$('input[id^="accionAsociacionGuiarem"][value!="0"]').length;
    if(flagBS=='S'){
    	if(total>0){
    		/**es servicio se le asigna codigo de guiaremision 0***/
    		codigoGuiarem=0;
            /**se agrega la guia de remision asociada***/
            fila += '<input type="hidden" name="codigoGuiarem[' + n + ']" id="codigoGuiarem[' + n + ']" value="' +codigoGuiarem + '">';
            /**fin de agregar la guia de remision**/
    	}
    }
    /**fin de verificacion **/
    
    fila += '</div></td>';
    fila += '</tr>';
    $("#tblDetalleComprobante").append(fila);

    if (tipo_docu != 'B' && tipo_docu != 'N')
        calcula_importe(n); //Para facturas o comprobantes
    else
        calcula_importe(n); //Para boletas

    inicializar_cabecera_item();
    $("#buscar_producto").focus();
    return true;
}
function agregar_fila(producto, codproducto, nombre_producto, cantidad, flagBS, flagGenInd, unidad_medida, nombre_unidad, precio_conigv, precio_sinigv, precio, igv, importe, stock, costo) {
    /*xxx
     *producto          = codigo del producto
     *codproducto       = codigo interno del producto
     *nombre_producto   = nombre del producto
     *descuento
     *flagBS            = B -> Bien S->Servicio
     */

    //igv = parseInt($("#igv").val());

    if (tipo_docu != 'B' && tipo_docu != 'N' && contiene_igv == '1')
        precio = money_format(precio_conigv * 100 / (igv + 100))
    else {
        precio = precio_conigv;
        precio_conigv = money_format(precio_conigv * (100 + igv) / 100);
    }
    /*if(flagBS=='B'){
     unidad_medida = $("#unidad_medida").val();
     nombre_unidad = $('#unidad_medida option:selected').html()
     }*/
    n = document.getElementById('tblDetalleComprobante').rows.length;
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
    if (tipo_docu != 'B' && tipo_docu != 'N')
        fila += '<input type="text" size="1" maxlength="10" class="cajaGeneral" name="prodcantidad[' + n + ']" id="prodcantidad[' + n + ']" value="' + cantidad + '" onblur="calcula_importe(' + n + ');" onkeypress="return numbersonly(this,event,\'.\');">' + nombre_unidad;
    else
        fila += '<input type="text" size="5" maxlength="10" class="cajaGeneral" name="prodcantidad[' + n + ']" id="prodcantidad[' + n + ']" value="' + cantidad + '" onblur="calcula_importe_conigv(' + n + ');" onkeypress="return numbersonly(this,event,\'.\');">' + nombre_unidad;

    fila += '</div></td>';
    if (tipo_docu != 'B' && tipo_docu != 'N') {
        fila += '<td width="6%"><div align="center"><input type="text" size="5" maxlength="10" class="cajaGeneral" value="' + precio_conigv + '" name="prodpu_conigv[' + n + ']" id="prodpu_conigv[' + n + ']" onblur="modifica_pu_conigv(' + n + ');" onkeypress="return numbersonly(this,event,\'.\');" /></div></td>'
        fila += '<td width="6%"><div align="center"><input type="text" size="5" maxlength="10" class="cajaGeneral" value="' + precio_sinigv + '" name="prodpu[' + n + ']" id="prodpu[' + n + ']" value="0" onblur="modifica_pu(' + n + ');" onkeypress="return numbersonly(this,event,\'.\');" ></div></td>';
        fila += '<td width="6%"><div align="center"><input type="text" size="5" maxlength="10" class="cajaGeneral cajaSoloLectura" name="prodprecio[' + n + ']" id="prodprecio[' + n + ']" value="' + precio + '" readonly="readonly">';
    }
    else {
        fila += '<td width="6%"><div align="center"><input type="text" size="5" maxlength="10" class="cajaGeneral" value="' + precio + '" name="prodpu_conigv[' + n + ']" id="prodpu_conigv[' + n + ']" value="0" onblur="calcula_importe_conigv(' + n + ');" onkeypress="return numbersonly(this,event,\'.\');"></div></td>';
        fila += '<td width="6%"><div align="center"><input type="text" size="5" maxlength="10" class="cajaGeneral" name="prodprecio_conigv[' + n + ']" id="prodprecio_conigv[' + n + ']" value="0" readonly="readonly"></div></td>';
    }
    if (tipo_docu != 'B' && tipo_docu != 'N')
        fila += '<td width="6%" style="display:none;" ><div align="center"><input type="text" size="5" class="cajaGeneral cajaSoloLectura" name="prodigv[' + n + ']" id="prodigv[' + n + ']" value="' + igv + '" readonly="readonly"></div></td>';
    fila += '<td width="6%" style="display:none;" ><div align="center">';
    fila += '<input type="hidden" value="n" name="detaccion[' + n + ']" id="detaccion[' + n + ']">';
    fila += '<input type="hidden" name="prodigv100[' + n + ']" id="prodigv100[' + n + ']" value="' + igv + '">';
    fila += '<input type="hidden" value="" name="detacodi[' + n + ']" id="detacodi[' + n + ']">';
    fila += '<input type="hidden" name="proddescuento100[' + n + ']" id="proddescuento100[' + n + ']" value="0">';
    if (tipo_docu != 'B' && tipo_docu != 'N')
        fila += '<input type="hidden" name="proddescuento[' + n + ']" class="proddescuento" id="proddescuento[' + n + ']" onblur="calcula_importe2(' + n + ');" />';
    else
        fila += '<input type="hidden" name="proddescuento_conigv[' + n + ']" id="proddescuento_conigv[' + n + ']" onblur="calcula_importe2_conigv(' + n + ');" />';
    fila += '<input type="hidden" name="flagBS[' + n + ']" id="flagBS[' + n + ']" value="' + flagBS + '"/>';
    fila += '<input type="hidden" name="prodcodigo[' + n + ']" id="prodcodigo[' + n + ']" value="' + producto + '"/>';
    fila += '<input type="hidden" name="produnidad[' + n + ']" id="produnidad[' + n + ']" value="' + unidad_medida + '"/>';
    fila += '<input type="hidden" name="flagGenIndDet[' + n + ']" id="flagGenIndDet[' + n + ']" value="' + flagGenInd + '"/>';
    fila += '<input type="hidden" name="prodstock[' + n + ']" id="prodstock[' + n + ']" value="' + stock + '"/>';
    fila += '<input type="hidden" name="prodcosto[' + n + ']" id="prodcosto[' + n + ']" value="' + costo + '"/>';
    fila += '<input type="text" size="5" class="cajaGeneral cajaSoloLectura" name="prodimporte[' + n + ']" id="prodimporte[' + n + ']" value="' + importe + '" readonly="readonly">';
    fila += '</div></td>';
    fila += '</tr>';
    $("#tblDetalleComprobante").append(fila);

    if (tipo_docu != 'B' && tipo_docu != 'N')
        calcula_importe(n); //Para facturas o comprobantes
    else
        calcula_importe(n); //Para boletas

    inicializar_cabecera_item();

    return true;
}
function eliminar_producto_comprobante(n) {
    if (confirm('Esta seguro que desea eliminar este producto?')) {
        a = "detacodi[" + n + "]";
        b = "detaccion[" + n + "]";
        fila = document.getElementById(a).parentNode.parentNode.parentNode;
        fila.style.display = "none";
        document.getElementById(b).value = "e";
        if (tipo_docu != 'B' && tipo_docu != 'N')
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
    preciodescuento= money_format(pu_conigv*cantidad);
    total_dscto = money_format(preciodescuento * descuento100 / 100);
    precio2 = money_format(precio - parseFloat(total_dscto));
    if (pu_conigv == '')
        total_igv = money_format(precio2 * igv100 / 100);
    else
        total_igv = money_format((pu_conigv - pu) * cantidad);

    importe = money_format(precio - parseFloat(total_dscto) + parseFloat(total_igv));

    document.getElementById(c).value = total_dscto;
    document.getElementById(d).value = total_igv;
    document.getElementById(e).value = precio;
    document.getElementById(f).value = importe.toFixed(2);

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

    var t_doc = $('#' + n).attr('t-doc');
    if (t_doc === 'F') {
        a = "prodpu[" + n + "]";
        b = "prodcantidad[" + n + "]";
        e = "prodigv[" + n + "]";
        f = "prodprecio[" + n + "]";
        g = "prodimporte[" + n + "]";
        h = "prodpu_conigv[" + n + "]";

        valor_igv = $("#igv").val();
        pu = parseFloat(document.getElementById(a).value);
        cantidad = parseFloat(document.getElementById(b).value);

        descuento = $('#' + n).find('.proddescuento').val();

        total_igv = parseFloat(document.getElementById(e).value);
        precio_u = parseFloat(document.getElementById(h).value);


        dsc = parseFloat(descuento / 100);
        importe = money_format((pu * cantidad) - ((pu * cantidad) * dsc));

        t_igv = money_format((importe * valor_igv) / 100);
        importe_total = money_format(importe + t_igv);
        document.getElementById(g).value = importe_total.toFixed(2);
        document.getElementById(e).value = t_igv.toFixed(2);
        document.getElementById(f).value = importe.toFixed(2);
        calcula_totales();
    } else {

        a = "prodimporte[" + n + "]";

        importe = parseFloat(document.getElementById(a).value);

        descuento = $('#' + n).find('.proddescuento').val();

        dsc = (parseFloat(descuento / 100));

        t_importe = money_format(importe - (importe * dsc));

        document.getElementById(a).value = t_importe.toFixed(2);
        calcula_totales3();
    }
}

function calcula_totales3() {
    var lht = $('#tblDetalleComprobante tr').length;
    var i = 0;
    var igv = $('#igv').val();

    var importe_total = 0;

    for (i; i < lht; i++) {
        a = "prodimporte[" + i + "]";
        importe_total += parseFloat(document.getElementById(a).value);
    }
    $('#porcentaje').val('0.00');
    $('#descuentotal_conigv').val('0.00');

    $('#importetotal').val(importe_total.toFixed(2));
    $('#preciototal_conigv').val((importe_total / (1 + (parseFloat(igv) / 100))).toFixed(2));

}
function descuento_porcentaje() {

    porcentaje = $('#porcentaje').val();
    if (isNaN(porcentaje)) {
        porcentaje = 0;
    }

    sub_total = $('#preciototal').val();

    if (isNaN(sub_total)) {
        sub_total = 0;
    }

    igv = parseInt($("#igv").val());
    descuento = money_format((sub_total * porcentaje) / 100);
    total = sub_total - descuento;
    valor = (total * igv) / 100;
    importe_total = total + valor;
    $('#igvtotal').val(valor.toFixed(2));
    $('#descuentotal').val(descuento.toFixed(2));
    $('#importetotal').val(importe_total.toFixed(2));

}
function incremento_visa() {
    calcula_totales();
    importe = $('#importetotal').val();
    // sub = $('#importetotal').val();

    igv = parseInt($('#igv').val()) + parseInt(100);
    visa = parseInt($('#visa').val()) + parseInt(100);

    if (isNaN(visa)) {
        visa = 0;
    }
    total = (importe * visa / 100);
    //importe_total= importe+total;
    sub_total = (total / (igv / 100));
    igv_total = total - sub_total;
    $('#importetotal').val(total.toFixed(2));
    $('#igvtotal').val(igv_total.toFixed(2));
    $('#preciototal').val(sub_total.toFixed(2));

    //if($('#visa').val()== 0 ){ calcula_totales();}


}
function cargar_provincia(obj){
    departamento = obj.value;
    provincia    = "01";
   
        url = base_url+"index.php/maestros/ubigeo/cargar_ubigeo/"+departamento+"/"+provincia;
        $("#divUbigeo").load(url);
   
}
function cargar_distrito(obj){
    departamento = $("#cboDepartamento").val();
    provincia    = obj.value;
   
        url = base_url+"index.php/maestros/ubigeo/cargar_ubigeo/"+departamento+"/"+provincia;
        $("#divUbigeo").load(url);
   
}
function descuento_nuevo() {
    alert('aki');
    descuento = $('descuentototal').val();
    sub_total = $('preciototal').val();
    igv = parseInt($("#igv").val());

    total = sub_total - descuento;
    valor = (total * igv) / 100;

    $('igvtotal').val(valor);
    importe_total = total + valor;
    $('importetotal').val(importe_total);
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
    n = document.getElementById('tblDetalleComprobante').rows.length;
    importe_total = 0;
    igv_total = 0;
    descuento_total = 0;
    precio_total = 0;

    for (i = 0; i < n; i++) {
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


    $("#importetotal").val(importe_total.toFixed(2));  //val(importe_total.toFixed(2))
    $("#igvtotal").val(igv_total.toFixed(2));  //val(igv_total.toFixed(2))
    $("#descuentotal").val(descuento_total.toFixed(2));

    if (tipo_oper == 'C')
        $("#preciototal").val(precio_total.toFixed(2));  //val(precio_total.toFixed(2))
    else
        $("#preciototal").val(precio_total.toFixed(2));  //val(precio_total.toFixed(2))
}
function calcula_totales_conigv() {
    n = document.getElementById('tblDetalleComprobante').rows.length;
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
                    n = document.getElementById('tblDetalleComprobante').rows.length;
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
                    $("#tblDetalleComprobante").append(fila);
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
                    stock = '0'
                    costo = '0';
                    n = document.getElementById('tblDetalleComprobante').rows.length;
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
                    fila += '<input type="text" class="cajaGeneral" size="1" maxlength="10" name="prodcantidad[' + n + ']" id="prodcantidad[' + n + ']" value="' + cantidad + '" onblur="calcula_importe(' + n + ');" onkeypress="return numbersonly(this,event,\'.\');"> ' + nombre_unidad;
                    fila += '</div></td>';
                    fila += '<td width="6%"><div align="center"><input type text" size="5" maxlength="10" class="cajaGeneral" value="' + precio + '" name="prodpu[' + n + ']" id="prodpu[' + n + ']" onblur="modifica_pu(' + n + ');" onkeypress="return numbersonly(this,event,\'.\');">'
                    fila += '<input type="hidden"  value="' + precio_conigv + '" name="prodpu_conigv[' + n + ']" id="prodpu_conigv[' + n + ']"></div></td>';
                    fila += '<td width="6%"><div align="center"><input type="text" size="5" maxlength="10" class="cajaGeneral cajaSoloLectura" name="prodprecio[' + n + ']" id="prodprecio[' + n + ']" value="0" readonly="readonly"></div></td>';
                    fila += '<td width="6%"><div align="center">';
                    fila += '<input type="hidden" name="proddescuento100[' + n + ']" id="proddescuento100[' + n + ']" value="0">';
                    fila += '<input type="text" size="5" maxlength="10" class="cajaGeneral" name="proddescuento[' + n + ']" class="proddescuento" id="proddescuento[' + n + ']" onblur="calcula_importe2(' + n + ');" />';
                    fila += '</div></td>';
                    fila += '<td width="6%" style="display:none;" ><div align="center"><input type="text" size="5" maxlength="10" class="cajaGeneral cajaSoloLectura" name="prodigv[' + n + ']" id="prodigv[' + n + ']" readonly></div></td>';
                    fila += '<td width="6%" style="display:none;"><div align="center">';
                    fila += '<input type="hidden" class="cajaMinima" name="detacodi[' + n + ']" id="detacodi[' + n + ']">';
                    fila += '<input type="hidden" class="cajaMinima" name="detaccion[' + n + ']" id="detaccion[' + n + ']" value="n">';
                    fila += '<input type="hidden" name="prodigv100[' + n + ']" id="prodigv100[' + n + ']" value="' + igv + '">';
                    fila += '<input type="hidden" class="cajaPequena2" name="prodcosto[' + n + ']" id="prodcosto[' + n + ']" value="' + costo + '" readonly="readonly">';
                    fila += '<input type="hidden" class="cajaPequena2" name="prodventa[' + n + ']" id="prodventa[' + n + ']" value="0" readonly="readonly">';
                    fila += '<input type="text" size="5" maxlength="10" class="cajaGeneral cajaSoloLectura" name="prodimporte[' + n + ']" id="prodimporte[' + n + ']" value="0" readonly="readonly">';
                    fila += '</div></td>';
                    fila += '</tr>';
                    $("#tblDetalleComprobante").append(fila);
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

    if (isNaN(pu_conigv)) {
        pu_conigv = 0;
    }
    if (isNaN(igv100)) {
        igv100 = 0;
    }
    if (isNaN(pu)) {
        pu = 0;
    }
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

    if (isNaN(pu_conigv)) {
        pu_conigv = 0;
    }
    if (isNaN(igv100)) {
        igv100 = 0;
    }
    if (isNaN(pu)) {
        pu = 0;
    }

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
            simbolo = item.UNDMED_Simbolo;
            nombre_producto = item.PROD_Nombre;
            nombrecorto_producto= item.PROD_NombreCorto; //Como se obtiene este campo
            marca = item.MARCC_Descripcion;
            modelo = item.PROD_Modelo;
            presentacion = item.PROD_Presentacion;
            opt = document.createElement('option');
            texto = document.createTextNode(descripcion);
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
          if(marca)
             nombre+=' / '+marca;
             if(modelo)
             nombre+=' /  '+modelo;
             if(presentacion)
             nombre+=' /  '+presentacion;
        }
        $("#nombre_producto").val(nombre);
        listar_precios_x_producto_unidad();
    });
}

function listar_precios_x_producto_unidad() {
    producto = $("#producto").val();
    unidad = $("#unidad_medida").val();
    moneda = $("#moneda").val();
    base_url = $("#base_url").val();
    flagBS = $("#flagBS").val();
    url = base_url + "index.php/almacen/producto/listar_precios_x_producto_unidad/" + producto + "/" + unidad + "/" + moneda;
    //alert(url);
    select_precio = document.getElementById('precioProducto');
    options_umedida = select_precio.getElementsByTagName("option");

    var num_option = options_umedida.length;
    for (j = 1; j <= num_option; j++) {
        select_precio.remove(0)
    }
    opt = document.createElement("option");
    texto = document.createTextNode("::Seleccion::");
    opt.appendChild(texto);
    opt.value = "";
    select_precio.appendChild(opt);
    var bd = 0
    $.getJSON(url, function (data) {
        $.each(data, function (i, item) {

            codigo = item.codigo;
            moneda = item.moneda;
            precio = item.precio;
            establecimiento = item.establecimiento;
            posicion_precio = item.posicion_precio;
            select = item.posicion;
            opt = document.createElement('option');
            texto = document.createTextNode(moneda + " " + precio + " " + establecimiento);
            opt.appendChild(texto);
            opt.value = precio;
            if (select == true) {
                opt.setAttribute('selected', 'selected')
                $("#precio").val(precio);
                bd = 1
            }
            if (bd == 0) {
                opt.removeAttribute('selected')
                $("#precio").val('');
            }
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
    else if (tipo_docu != 'B' && tipo_docu != 'N')
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
function agregar_todopresupuesto(guia, tipo_oper) {
    descuento100 = $("#descuento").val();
    igv100 = $("#igv").val();
    almacen=$("#almacen").val();
    url = base_url + "index.php/ventas/presupuesto/obtener_detalle_presupuesto/" + tipo_oper + "/" + tipo_docu + "/" + guia;
    n = document.getElementById('tblDetalleComprobante').rows.length;
    
    $.ajax({
        url: url,
        dataType: 'json',
        async: false, 
        success: function (data) {
    	
        limpiar_datos();
        $.each(data, function (i, item) {
            moneda = item.MONED_Codigo;
            formapago = item.FORPAP_Codigo;
            serie = item.PRESUC_Serie;
            numero = item.PRESUC_Numero;
            codigo_usuario = item.PRESUC_CodigoUsuario;

            if (item.PRESDEP_Codigo != '') {
                j = n + 1;
                producto = item.PROD_Codigo;
                codproducto = item.PROD_CodigoInterno;
                unidad_medida = item.UNDMED_Codigo;
                nombre_unidad = item.UNDMED_Descripcion;
                nombre_producto = item.PROD_Nombre;
                flagGenInd = item.PROD_GenericoIndividual
                cantidad = item.PRESDEC_Cantidad;
                pu = item.PRESDEC_Pu;
                subtotal = item.PRESDEC_Subtotal;
                descuento = item.PRESDEC_Descuento;
                igv = item.PRESDEC_Igv;
                total = item.PRESDEC_Total
                pu_conigv = item.PRESDEC_Pu_ConIgv;
                subtotal_conigv = item.PRESDEC_Subtotal_ConIgv;
                descuento_conigv = item.PRESDEC_Descuento_ConIgv;

                
                /**verificamos si el producto esta inventariado ***/
                var url2 = base_url+"index.php/almacen/producto/verificarInventariado/"+producto;
                isMostrarArticulo=true;
                isSeleccionarAlmacen=false;
                $.ajax({
                    url: url2,
                    async: false, 
                    success: function (data2) {
    	            	/***articulos con serie**/
    	            	if(flagGenInd=="I"){
    	            		if(data2.trim()=="1")
    	            		{
    	            			almacenProducto=null;
    	            			isSeleccionarAlmacen=1;
    	            		}else{
    	            			alert("No se puede ingresar este producto Serie, no contiene Inventario");
    	            			isMostrarArticulo=false;
    	            		}
    	            	}else{
    	            		/***articulos sin serie**/
    	            		if(data2.trim()=="1")
    	            		{
    	            			almacenProducto=null;
    	            			isSeleccionarAlmacen=1;
    	            		}else{
    	            			/**no esta inventariado pero se selecciona almacen por default del comprobante**/
    	            			almacenProducto=almacen;
    	            		}
    	            	}
                    }	
                });
                
                /**fin de verificacion**/
                if(isMostrarArticulo){
	                if (j % 2 == 0) {
	                    clase = "itemParTabla";
	                } else {
	                    clase = "itemImparTabla";
	                }
	                fila = '<tr id="' + n + '" class="' + clase + '" t-doc="' + tipo_docu + '" >';
	                fila += '<td width="3%"><div align="center"><font color="red"><strong><a href="#" onclick="eliminar_producto_comprobante(' + n + ');">';
	                fila += '<span style="border:1px solid red;background: #ffffff;">&nbsp;X&nbsp;</span>';
	                fila += '</a></strong></font></div></td>';
	                fila += '<td width="4%"><div align="center">' + j + '</div></td>';
	                fila += '<td width="10%"><div align="center">';
	                fila += '<input type="hidden" class="cajaGeneral" name="prodcodigo[' + n + ']" id="prodcodigo[' + n + ']" value="' + producto + '">' + codproducto;
	                fila += '<input type="hidden" class="cajaGeneral" name="produnidad[' + n + ']" id="produnidad[' + n + ']" value="' + unidad_medida + '">';
	                fila += '</div></td>';
	                fila += '<input type="hidden" name="flagBS[' + n + ']" id="flagBS[' + n + ']" value="B"/>';
	                fila += '<input type="hidden" name="flagGenIndDet[' + n + ']" id="flagGenIndDet[' + n + ']" value="'+flagGenInd+'"/>'
	                
	                fila += '<td><div align="left"><input type="text" class="cajaGeneral" size="73" maxlength="250" name="proddescri[' + n + ']" id="proddescri[' + n + ']" value="' + nombre_producto + '" /></div></td>';
	                fila += '<td width="10%"><div align="left"><input type="text" size="1" maxlength="10" class="cajaGeneral" name="prodcantidad[' + n + ']" id="prodcantidad[' + n + ']" value="' + cantidad + '" onblur="calcula_importe(' + n + ');calcula_totales();" onkeypress="return numbersonly(this,event,\'.\');">' + nombre_unidad + '</div>';
	                
	                if (flagGenInd == "I") {
		            	fila +='<a href="javascript:;" id="imgEditarSeries' + n + '" onclick="ventana_producto_serie('+ n +')" ><img src="'+base_url+'images/flag-green_icon.png" width="20" height="20"  border="0" class="imgBoton"></a>';
		            	fila += '<input type="hidden" value="'+isSeleccionarAlmacen+'" name="isSeleccionarAlmacen[' + n + ']" id="isSeleccionarAlmacen[' + n + ']">';
		 	         }else{
			            /**verificamos si el producto debe de ser selccionar el almacen por dfault no existe y hay en otros almacenes **/
			            if(isSeleccionarAlmacen){
			            	fila +='<a href="javascript:;" id="imgSeleccionarAlmacen' + n + '" onclick="mostrarPopUpSeleccionarAlmacen('+ n +')" ><img src="'+base_url+'images/almacen.png" width="20" height="20"  border="0" class="imgBoton"></a>';
			            } 	
		            }
	                
	                
	                fila += '</td>';
	                
	                if (tipo_docu != 'B' && tipo_docu != 'N') {
	                    fila += '<td width="6%"><div align="center"><input type="text" size="5" maxlength="10" class="cajaGeneral" name="prodpu_conigv[' + n + ']" id="prodpu_conigv[' + n + ']" value="' + pu_conigv + '"  onblur="modifica_pu_conigv(' + n + ');" onkeypress="return numbersonly(this,event,\'.\');" /></div></td>'
	                    fila += '<td width="6%"><div align="center"><input type="text" size="5" maxlength="10" class="cajaGeneral"  name="prodpu[' + n + ']" id="prodpu[' + n + ']" value="' + pu + '" onblur="modifica_pu(' + n + ');" onkeypress="return numbersonly(this,event,\'.\');" ></div></td>';
	                    fila += '<td width="6%"><div align="center"><input type="text" size="5" maxlength="10" class="cajaGeneral cajaSoloLectura" name="prodprecio[' + n + ']" id="prodprecio[' + n + ']" value="' + subtotal + '" readonly="readonly">';
	                } else {
	                    fila += '<td width="6%"><div align="center"><input type="text" size="5" maxlength="10" class="cajaGeneral" name="prodpu_conigv[' + n + ']" id="prodpu_conigv[' + n + ']" value="' + pu_conigv + '"  onblur="modifica_pu_conigv(' + n + ');" onkeypress="return numbersonly(this,event,\'.\');" /></div></td>'
	                    fila += '<td width="6%"><div align="center"><input type="text" size="5" maxlength="10" class="cajaGeneral"  name="prodpu[' + n + ']" id="prodpu[' + n + ']" value="' + pu + '" onblur="modifica_pu(' + n + ');" onkeypress="return numbersonly(this,event,\'.\');" ></div></td>';
	                    fila += '<td width="6%"><div align="center"><input type="text" size="5" maxlength="10" class="cajaGeneral cajaSoloLectura" name="prodprecio[' + n + ']" id="prodprecio[' + n + ']" value="' + subtotal + '" readonly="readonly">';
	                }
	                //fila += '<td width="6%"><div align="center">';
	                fila += '<input type="hidden" size="1" readonly name="proddescuento100[' + n + ']" id="proddescuento100[' + n + ']" value="' + descuento100 + '">';
	                if (tipo_docu != 'B' && tipo_docu != 'N')
	                    fila += '<input type="hidden" size="1" maxlength="10" readonly class="cajaGeneral" name="proddescuento[' + n + ']" class="proddescuento" id="proddescuento[' + n + ']" value="' + descuento + '" onblur="calcula_importe2(' + n + ');calcula_totales();">';
	                else
	                    fila += '<input type="hidden" size="1" maxlength="10" readonly class="cajaGeneral" name="proddescuento[' + n + ']" class="proddescuento" id="proddescuento[' + n + ']" value="' + descuento + '" onblur="calcula_importe2(' + n + ');calcula_totales();">';
	                //fila += '</div></td>';
	
	                fila += '<td width="6%" style="display:none;"><div align="center"><input type="text" size="5" class="cajaGeneral cajaSoloLectura" name="prodigv[' + n + ']" value="' + igv + '" id="prodigv[' + n + ']" readonly></div></td>';
	                fila += '<td width="6%" style="display:none;" ><div align="center">';
	                fila += '<input type="hidden" name="detacodi[' + n + ']" id="detacodi[' + n + ']">';
	                fila += '<input type="hidden" name="proddescuento_conigv[' + n + ']" id="proddescuento_conigv[' + n + ']" onblur="calcula_importe2_conigv(' + n + ');" value="0">';
	                fila += '<input type="hidden" name="prodigv100[' + n + ']" id="prodigv100[' + n + ']" value="' + igv100 + '">';
	                fila += '<input type="hidden" name="detaccion[' + n + ']" id="detaccion[' + n + ']" value="n">';
	                fila += '<input type="text" value="0" size="1" name="proddescuento[' + n + ']" class="proddescuento" id="proddescuento[' + n + ']" onblur="calcula_importe2(' + n + ');" />';
	                fila += '<input type="hidden" name="almacenProducto[' + n + ']" id="almacenProducto[' + n + ']" value="' + almacenProducto + '"/>';
	                fila += '<input type="text" size="5" class="cajaGeneral cajaSoloLectura" name="prodimporte[' + n + ']" id="prodimporte[' + n + ']" value="' + total + '" readonly="readonly" value="0">';
	                fila += '</div></td>';
	                fila += '</tr>';
	                $("#tblDetalleComprobante").append(fila);
	                $('#forma_pago').val(formapago);
	                $('#moneda').val(moneda);
	                n++;
                }
            }

            
        })
        calcula_totales();
        }
    });
    
    
    
    
}
function obtener_detalle_guiarem(guiarem) {
    url = base_url + "index.php/almacen/guiarem/obtener_detalle_guiarem/" + guiarem;

    n = document.getElementById('tblDetalleComprobante').rows.length;
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
                j = n + 1;
                producto = item.PROD_Codigo;
                codproducto = item.PROD_CodigoInterno;
                unidad_medida = item.UNDMED_Codigo;
                nombre_unidad = item.UNDMED_Simbolo;
                nombre_producto = item.PROD_Nombre;
                cantidad = item.GUIAREMDETC_Cantidad;
                precio = item.GUIAREMDETC_Pu;
                subtotal = item.GUIAREMDETC_Subtotal;
                descuento = item.GUIAREMDETC_Descuento100;
                igv = item.GUIAREMDETC_Igv100;
                precio_conigv = item.GUIAREMDETC_Pu_ConIgv;
                flagGenInd = item.GUIAREMDETC_GenInd;
                flagBS = item.PROD_FlagBienServicio;
                costo = item.GUIAREMDETC_Costo
                stock = '';

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
                if (tipo_docu != 'B' && tipo_docu != 'N')
                    fila += '<input type="text" size="1" maxlength="10" class="cajaGeneral" name="prodcantidad[' + n + ']" id="prodcantidad[' + n + ']" value="' + cantidad + '" onblur="calcula_importe(' + n + ');" onkeypress="return numbersonly(this,event,\'.\');">' + nombre_unidad;
                else
                    fila += '<input type="text" size="5" maxlength="10" class="cajaGeneral" name="prodcantidad[' + n + ']" id="prodcantidad[' + n + ']" value="' + cantidad + '" onblur="calcula_importe_conigv(' + n + ');" onkeypress="return numbersonly(this,event,\'.\');">' + nombre_unidad;

                fila += '</div></td>';
                if (tipo_docu != 'B' && tipo_docu != 'N') {
                    fila += '<td width="6%"><div align="center"><input type="text" size="5" maxlength="10" class="cajaGeneral" value="' + precio_conigv + '" name="prodpu_conigv[' + n + ']" id="prodpu_conigv[' + n + ']" onblur="modifica_pu_conigv(' + n + ');" onkeypress="return numbersonly(this,event,\'.\');" /></div></td>'
                    fila += '<td width="6%"><div align="center"><input type="text" size="5" maxlength="10" class="cajaGeneral" value="' + precio + '" name="prodpu[' + n + ']" id="prodpu[' + n + ']" value="0" onblur="modifica_pu(' + n + ');" onkeypress="return numbersonly(this,event,\'.\');" ></div></td>';
                    fila += '<td width="6%"><div align="center"><input type="text" size="5" maxlength="10" class="cajaGeneral cajaSoloLectura" name="prodprecio[' + n + ']" id="prodprecio[' + n + ']" value="0" readonly="readonly">';
                }
                else {
                    fila += '<td width="6%"><div align="center"><input type="text" size="5" maxlength="10" class="cajaGeneral" value="' + precio_conigv + '" name="prodpu_conigv[' + n + ']" id="prodpu_conigv[' + n + ']" onblur="modifica_pu_conigv(' + n + ');" onkeypress="return numbersonly(this,event,\'.\');" /></div></td>'
                    fila += '<td width="6%"><div align="center"><input type="text" size="5" maxlength="10" class="cajaGeneral" value="' + precio + '" name="prodpu_conigv[' + n + ']" id="prodpu_conigv[' + n + ']" value="0" onblur="calcula_importe_conigv(' + n + ');" onkeypress="return numbersonly(this,event,\'.\');"></div></td>';
                    fila += '<td width="6%"><div align="center"><input type="text" size="5" maxlength="10" class="cajaGeneral" name="prodprecio_conigv[' + n + ']" id="prodprecio_conigv[' + n + ']" value="0" readonly="readonly"></div></td>';
                }
                if (tipo_docu != 'B' && tipo_docu != 'N')
                    fila += '<td width="6%" style="display:none;" ><div align="center"><input type="text" size="5" class="cajaGeneral cajaSoloLectura" name="prodigv[' + n + ']" id="prodigv[' + n + ']" readonly="readonly"></div></td>';
                fila += '<td width="6%" style="display:none;" ><div align="center">';
                fila += '<input type="hidden" value="n" name="detaccion[' + n + ']" id="detaccion[' + n + ']">';
                fila += '<input type="hidden" name="prodigv100[' + n + ']" id="prodigv100[' + n + ']" value="' + igv + '">';
                fila += '<input type="hidden" value="" name="detacodi[' + n + ']" id="detacodi[' + n + ']">';
                fila += '<input type="hidden" name="proddescuento100[' + n + ']" id="proddescuento100[' + n + ']" value="' + descuento + '">';
                if (tipo_docu != 'B' && tipo_docu != 'N')
                    fila += '<input type="hidden" name="proddescuento[' + n + ']" class="proddescuento" id="proddescuento[' + n + ']" onblur="calcula_importe2(' + n + ');" />';
                else
                    fila += '<input type="hidden" name="proddescuento_conigv[' + n + ']" id="proddescuento_conigv[' + n + ']" onblur="calcula_importe2_conigv(' + n + ');" />';
                fila += '<input type="hidden" name="flagBS[' + n + ']" id="flagBS[' + n + ']" value="' + flagBS + '"/>';
                fila += '<input type="hidden" name="prodcodigo[' + n + ']" id="prodcodigo[' + n + ']" value="' + producto + '"/>';
                fila += '<input type="hidden" name="produnidad[' + n + ']" id="produnidad[' + n + ']" value="' + unidad_medida + '"/>';
                fila += '<input type="hidden" name="flagGenIndDet[' + n + ']" id="flagGenIndDet[' + n + ']" value="' + flagGenInd + '"/>';
                fila += '<input type="hidden" name="prodstock[' + n + ']" id="prodstock[' + n + ']" value="' + stock + '"/>';
                fila += '<input type="hidden" name="prodcosto[' + n + ']" id="prodcosto[' + n + ']" value="' + costo + '"/>';
                fila += '<input type="text" size="5" class="cajaGeneral cajaSoloLectura" name="prodimporte[' + n + ']" id="prodimporte[' + n + ']" value="0" readonly="readonly">';
                fila += '</div></td>';
                fila += '</tr>';
                $("#tblDetalleComprobante").append(fila);
            }

            $('#ruc_cliente').val(ruc);
            $('#cliente').val(cliente);
            $('#nombre_cliente').val(razon_social);
            $('#moneda').val(moneda);

            calcula_importe(n);
        });
        calcula_totales();
    });
}

	function obtener_detalle_ocompra(ocompra) {
	/***obtenenemos el almacen de la factura**/
	almacen=$("#almacen").val();
	/**fin de obtener el almacen**/

    var moneda;
	
    url = base_url + "index.php/compras/ocompra/obtener_detalle_ocompra2/" + ocompra;
    n = document.getElementById('tblDetalleComprobante').rows.length;
    $.ajax({
        url: url,
        dataType: 'json',
        async: false, 
        success: function (data) {
        limpiar_datos();
        $.each(data, function (i, item) {
            
            if(!moneda && item.MONED_Codigo != 0) moneda = item.MONED_Codigo;

            cliente = item.CLIP_Codigo;
            ruc = item.Ruc;
            razon_social = item.RazonSocial;
            serie = item.OCOMC_Serie;
            numero = item.OCOMC_Numero;
            codigo_usuario = item.OCOMC_CodigoUsuario;
            //if(item.GUIAREMP_Codigo != ''){
            j = n + 1;
            producto = item.PROD_Codigo;
            codproducto = item.PROD_CodigoInterno;
            unidad_medida = item.UNDMED_Codigo;
            nombre_unidad = item.UNDMED_Simbolo;
            nombre_producto = item.PROD_Nombre;
            cantidad = item.OCOMDEC_Cantidad;
            precio = item.OCOMDEC_Pu;
            subtotal = item.OCOMDEC_Subtotal;
            descuento100 = item.OCOMDEC_Descuento100;
            igv = item.OCOMDEC_Igv100;
            precio_conigv = item.OCOMDEC_Pu_ConIgv;
            flagGenInd = item.OCOMDEC_GenInd;
            flagBS = item.PROD_FlagBienServicio;
            costo = item.OCOMDEC_Total;
            descuento = item.OCOMDEC_Descuento;
            stock = '';
           
            
            /**verificamos si el producto esta inventariado ***/
            var url2 = base_url+"index.php/almacen/producto/verificarInventariado/"+producto;
            isMostrarArticulo=true;
            isSeleccionarAlmacen=false;
            $.ajax({
                url: url2,
                async: false, 
                success: function (data2) {
	            	/***articulos con serie**/
	            	if(flagGenInd=="I"){
	            		if(data2.trim()=="1")
	            		{
	            			almacenProducto=null;
	            			isSeleccionarAlmacen=1;
	            		}else{
	            			alert("No se puede ingresar este producto Serie, no contiene Inventario");
	            			isMostrarArticulo=false;
	            		}
	            	}else{
	            		/***articulos sin serie**/
	            		if(data2.trim()=="1")
	            		{
	            			almacenProducto=null;
	            			isSeleccionarAlmacen=1;
	            		}else{
	            			/**no esta inventariado pero se selecciona almacen por default del comprobante**/
	            			almacenProducto=almacen;
	            		}
	            	}
                }	
            });
            
            /**fin de verificacion**/
            if(isMostrarArticulo){
	            if (j % 2 == 0) {
	                clase = "itemParTabla";
	            } else {
	                clase = "itemImparTabla";
	            }
	            fila = '<tr class="' + clase + '" id="'+n+'" >';
	            fila += '<td width="3%"><div align="center"><font color="red"><strong><a href="#" onclick="eliminar_producto_comprobante(' + n + ');">';
	            fila += '<span style="border:1px solid red;background: #ffffff;">&nbsp;X&nbsp;</span>';
	            fila += '</a></strong></font></div></td>';
	            fila += '<td width="4%"><div align="center">' + j + '</div></td>';
	            fila += '<td width="10%"><div align="center">' + codproducto + '</div></td>';
	            fila += '<td width="30%" ><div align="left"><input type="text" class="cajaGeneral" size="60" maxlength="250" name="proddescri[' + n + ']" id="proddescri[' + n + ']" value="' + nombre_producto + '" /></div></td>';
	            fila += '<td width="10%"><div align="left">';
	                       
	            if (tipo_docu == 'F')
	                fila += '<input type="text" size="5" maxlength="10" class="cajaGeneral" name="prodcantidad[' + n + ']" id="prodcantidad[' + n + ']" value="' + cantidad + '" onblur="calcula_importe(' + n + ');" onkeypress="return numbersonly(this,event,\'.\');">' + nombre_unidad;
	            else
	                fila += '<input type="text" size="5" maxlength="10" class="cajaGeneral" name="prodcantidad[' + n + ']" id="prodcantidad[' + n + ']" value="' + cantidad + '" onblur="calcula_importe_conigv(' + n + ');" onkeypress="return numbersonly(this,event,\'.\');">' + nombre_unidad;
	            
	            
	            if (flagGenInd == "I") {
	            	fila +='<a href="javascript:;" id="imgEditarSeries' + n + '" onclick="ventana_producto_serie('+ n +')" ><img src="'+base_url+'images/flag-green_icon.png" width="20" height="20"  border="0" class="imgBoton"></a>';
	            	fila += '<input type="hidden" value="'+isSeleccionarAlmacen+'" name="isSeleccionarAlmacen[' + n + ']" id="isSeleccionarAlmacen[' + n + ']">';
	 	         }else{
		            /**verificamos si el producto debe de ser selccionar el almacen por dfault no existe y hay en otros almacenes **/
		            if(isSeleccionarAlmacen){
		            	fila +='<a href="javascript:;" id="imgSeleccionarAlmacen' + n + '" onclick="mostrarPopUpSeleccionarAlmacen('+ n +')" ><img src="'+base_url+'images/almacen.png" width="20" height="20"  border="0" class="imgBoton"></a>';
		            } 	
	            }
           
	            
	            
	            fila += '</div></td>';
	            if (tipo_docu == 'F') {
	                fila += '<td width="6%"><div align="center"><input type="text" size="5" maxlength="10" class="cajaGeneral" value="' + precio_conigv + '" name="prodpu_conigv[' + n + ']" id="prodpu_conigv[' + n + ']" onblur="modifica_pu_conigv(' + n + ');" onkeypress="return numbersonly(this,event,\'.\');" /></div></td>'
	                fila += '<td width="6%"><div align="center"><input type="text" size="5" maxlength="10" class="cajaGeneral" value="' + precio + '" name="prodpu[' + n + ']" id="prodpu[' + n + ']" onblur="modifica_pu(' + n + ');" onkeypress="return numbersonly(this,event,\'.\');" ></div></td>';
	                fila += '<td width="6%"><div align="center"><input type="text" size="5" maxlength="10" class="cajaGeneral cajaSoloLectura" name="prodprecio[' + n + ']" id="prodprecio[' + n + ']" value="0" readonly="readonly">';
	            }
	            else {
	                fila += '<td width="6%"><div align="center"><input type="text" size="5" maxlength="10" class="cajaGeneral cajaSoloLectura" value="' + precio_conigv + '" name="prodpu_conigv[' + n + ']" id="prodpu_conigv[' + n + ']" onblur="modifica_pu_conigv(' + n + ');" onkeypress="return numbersonly(this,event,\'.\');" /></div></td>'
	                //fila += '<td width="6%"><div align="center"><input type="text" size="5" maxlength="10" class="cajaGeneral" value="'+precio+'" name="prodpu_conigv['+n+']" id="prodpu_conigv['+n+']" value="'+precio_conigv+'" onblur="calcula_importe_conigv('+n+');" onkeypress="return numbersonly(this,event,\'.\');"></div></td>';
	                //fila += '<td width="6%"><div align="center"><input type="text" size="5" maxlength="10" class="cajaGeneral cajaSoloLectura" name="prodprecio_conigv['+n+']" id="prodprecio_conigv['+n+']" value="0" readonly="readonly">';
	                fila += '<td width="6%"><div align="center"><input type="text" size="5" maxlength="10" class="cajaGeneral" value="' + precio + '" name="prodpu[' + n + ']" id="prodpu[' + n + ']" value="' + precio_conigv + '" onblur="calcula_importe_conigv(' + n + ');" onkeypress="return numbersonly(this,event,\'.\');"></div></td>';
	                fila += '<td width="6%"><div align="center"><input type="text" size="5" maxlength="10" class="cajaGeneral cajaSoloLectura" name="prodprecio[' + n + ']" id="prodprecio[' + n + ']" value="0" readonly="readonly">';
	            }
	            if (tipo_docu == 'F') {
	                fila += '<td style="display:none;"><div align="center"><input type="text" size="5" class="cajaGeneral cajaSoloLectura" name="prodigv[' + n + ']" id="prodigv[' + n + ']" readonly="readonly"></div></td>';
	            } else {
	                fila += '<td style="display:none;"><div align="center"><input type="text" size="5" class="cajaGeneral cajaSoloLectura" name="prodigv[' + n + ']" id="prodigv[' + n + ']" value="" readonly="readonly"></div></td>';
	            }
	            fila += '<td style="display:none;" ><div align="center">';
	            fila += '<input type="hidden" value="n" name="detaccion[' + n + ']" id="detaccion[' + n + ']">';
	            fila += '<input type="hidden" name="prodigv100[' + n + ']" id="prodigv100[' + n + ']" value="' + igv + '">';
	            fila += '<input type="hidden" value="" name="detacodi[' + n + ']" id="detacodi[' + n + ']">';
	            fila += '<input type="hidden" name="proddescuento100[' + n + ']" id="proddescuento100[' + n + ']" value="' + descuento100 + '">';
	            if (tipo_docu == 'F') {
	                fila += '<input type="hidden" name="proddescuento[' + n + ']" class="proddescuento" id="proddescuento[' + n + ']" value="' + descuento + '" onblur="calcula_importe2(' + n + ');" />';
	            }
	            else {
	                //fila += '<input type="hidden" name="proddescuento_conigv[' + n + ']" id="proddescuento_conigv[' + n + ']" onblur="calcula_importe2_conigv(' + n + ');" />';
	                fila += '<input type="hidden" name="proddescuento[' + n + ']" id="proddescuento[' + n + ']" value="' + descuento + '" onblur="calcula_importe2(' + n + ');" />';
	            }
	            fila += '<input type="hidden" name="flagBS[' + n + ']" id="flagBS[' + n + ']" value="' + flagBS + '"/>';
	            fila += '<input type="hidden" name="prodcodigo[' + n + ']" id="prodcodigo[' + n + ']" value="' + producto + '"/>';
	            fila += '<input type="hidden" name="produnidad[' + n + ']" id="produnidad[' + n + ']" value="' + unidad_medida + '"/>';
	            fila += '<input type="hidden" name="flagGenIndDet[' + n + ']" id="flagGenIndDet[' + n + ']" value="' + flagGenInd + '"/>';
	            fila += '<input type="hidden" name="prodstock[' + n + ']" id="prodstock[' + n + ']" value="' + stock + '"/>';
	            fila += '<input type="hidden" name="almacenProducto[' + n + ']" id="almacenProducto[' + n + ']" value="' + almacenProducto + '"/>';
	            fila += '<input type="hidden" name="prodcosto[' + n + ']" id="prodcosto[' + n + ']" value="' + costo + '"/>';
	            fila += '<input type="text" size="5" class="cajaGeneral cajaSoloLectura" name="prodimporte[' + n + ']" id="prodimporte[' + n + ']" value="0" readonly="readonly">';
	            fila += '</div></td>';
	            fila += '</tr>';
	            $("#tblDetalleComprobante").append(fila);
	            //}
	            
	            calcula_importe(n);
            }alert(moneda);
        })
        alert(moneda);$('#moneda').val(moneda);
    }

    });

    
}

function limpiar_datos() {
    /*$('#ruc_cliente').val('');
     $('#cliente').val('');
     $('#nombre_cliente').val('');*/
    $('#formapago').val('');
    $('#moneda').val('1');

    n = document.getElementById('tblDetalleComprobante').rows.length;
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

function calcular_importe_todos(total) {
    //Para Factura
    for (var i = 0; i < total; i++) {
        modifica_pu_conigv(i);
    }
}

function modificar_pu_conigv_todos(total) {
    //Para Boleta y Comprobante
    for (var i = 0; i < total; i++) {
        calcula_importe_conigv(i);
    }
}
function agregar_todo(guia) {
	
	/**verificamos si guiaderemision ya se encuentra asociada**/
	n = document.getElementById('idTableGuiaRelacion').rows.length;
	/**limpiamos los articulos agregados si seleccionamos una guia de remision**/
	if(n==1){
		if(confirm("¿Desea agregar guia de remisión se eliminara todos los articulos y/o servicios agregados?")){
			$("#tblDetalleComprobante").html("");	
			/**bloqueamos los opcion de obtener articulos y ocultamnos agregar producto verificamos si es servicio para que no lo oculte**/
            valorFlagBS=$("#flagBS").val();
			if(valorFlagBS=='B'){
            	document.getElementById("buscar_producto").readOnly = true;
            	$("#idDivAgregarProducto").hide(200);
			}
			
            $("#moneda").hide(200);
            textoMoneda=$("#moneda option:selected").text();
            $("#textoMoneda").html(textoMoneda);
            $("#textoMoneda").show(200);
            /**fin de bloquear**/
		}else{
			return false;
		}
	}
	/**fin de limpiar**/
	if(n>1){
		for(x=1;x<n;x++){
			codA = "codigoGuiaremAsociada[" + x + "]";
			accCod = "accionAsociacionGuiarem[" + x + "]";
		    valorSeleccion=document.getElementById(accCod).value;
		    valorcodigoGuiaremAsociada=document.getElementById(codA).value;
			if(valorSeleccion==1 && valorcodigoGuiaremAsociada==guia){
				alert("Guia de Remision se encuentra seleccionada"); 
				return;
				}
		}
	}
	/**fin de verificacion**/
	descuento100 = $("#descuento").val();
    igv100 = $("#igv").val();
    url = base_url + "index.php/almacen/guiarem/obtener_detalle_guiarem/" + guia;
    n = document.getElementById('tblDetalleComprobante').rows.length;
    var randomColor = Math.floor(Math.random()*16777215).toString(16);
    randomColor="#"+randomColor;
    $.getJSON(url, function (data) {
       valorVerificar=true;
        $.each(data, function (i, item) {
            cliente = item.CLIP_Codigo;
            proveedor = item.PROVP_Codigo;
            if (tipo_oper == 'V') {
            	 if ($('#cliente').val()!=cliente) {
                     alert('la serie y numero ingrtesado no corresponden a este cliente');
                     valorVerificar=false;
                     return false;
                 } 
            }else{
            	
            	if ($('#proveedor').val() != proveedor) {
            		 alert('la serie y numero ingrtesado no corresponden a este Proveedor');
            		 valorVerificar=false;
                    return false;
                }
            	
            	
            }
            
            ruc = item.Ruc;
            razon_social = item.RazonSocial;
            moneda = item.MONED_Codigo;
            formapago = item.FORPAP_Codigo;
            serie = item.GUIAREMC_Serie;
            numero = item.GUIAREMC_Numero;
            codigo_usuario = item.GUIAREMC_CodigoUsuario;
            almacenProducto=item.ALMAP_Codigo;
            flagGenInd=item.GUIAREMDETC_GenInd;
            if (item.PRESDEP_Codigo != '') {
                j = n + 1
                producto = item.PROD_Codigo;
                codproducto = item.PROD_CodigoInterno;
                unidad_medida = item.UNDMED_Codigo;
                nombre_unidad = item.UNDMED_Descripcion;
                nombre_producto = item.PROD_Nombre;
                cantidad = item.GUIAREMDETC_Cantidad;
                pu = item.GUIAREMDETC_Pu;
                subtotal = item.GUIAREMDETC_Subtotal;
                descuento = item.GUIAREMDETC_Descuento;
                igv = item.GUIAREMDETC_Igv;
                total = item.GUIAREMDETC_Total;
                pu_conigv = item.GUIAREMDETC_Pu_ConIgv;
                subtotal_conigv = parseFloat(pu_conigv) * parseFloat(cantidad);
                descuento_conigv = '';
                
                if (j % 2 == 0) {
                    clase = "itemParTabla";
                } else {
                    clase = "itemImparTabla";
                }
                
                fila = '<tr id="' + n + '" class="' + clase + '" t-doc="' + tipo_docu + '" style="background-color:'+randomColor+';color:#000000;" >';
                fila += '<td width="3%"><div align="center">';
                fila += '</div></td>';
                fila += '<td width="4%"><div align="center">' + j + '</div></td>';
                fila += '<td width="10%"><div align="center">';
                fila += '<input type="hidden" class="cajaGeneral" name="prodcodigo[' + n + ']" id="prodcodigo[' + n + ']" value="' + producto + '">' + codproducto;
                fila += '<input type="hidden" class="cajaGeneral" name="produnidad[' + n + ']" id="produnidad[' + n + ']" value="' + unidad_medida + '">';
                fila += '</div></td>';
                fila += '<input type="hidden" name="flagBS[' + n + ']" id="flagBS[' + n + ']" value="B"/>';
                fila += '<input type="hidden" name="flagGenIndDet[' + n + ']" id="flagGenIndDet[' + n + ']" value="'+flagGenInd+'"/>'
                fila += '<td><div align="left"><input type="text" class="cajaGeneral" size="73" maxlength="250" name="proddescri[' + n + ']" id="proddescri[' + n + ']" value="' + nombre_producto + '" /></div></td>';
                fila += '<td width="10%"><div align="left">';
                if (tipo_docu != 'B' && tipo_docu != 'N')
                    fila += '<input type="text" size="1" maxlength="10" class="cajaGeneral" name="prodcantidad[' + n + ']" id="prodcantidad[' + n + ']" value="' + cantidad + '" onblur="calcula_importe(' + n + ');calcula_totales();" onkeypress="return numbersonly(this,event,\'.\');" readonly>';
                else
                    fila += '<input type="text" size="1" maxlength="10" class="cajaGeneral" name="prodcantidad[' + n + ']" id="prodcantidad[' + n + ']" value="' + cantidad + '" onblur="calcula_importe(' + n + ');" onkeypress="return numbersonly(this,event,\'.\');" readonly>' + nombre_unidad ;

                if(flagGenInd!=null && flagGenInd=='I'){
                	fila +='<a href="javascript:;" id="imgEditarSeries' + n + '" onclick="ventana_producto_serieMostrar(10,'+guia+','+producto+','+almacenProducto+')" ><img src="'+base_url+'images/flag-green_icon.png" width="20" height="20" class="imgBoton"></a>';
                	/**vamos al metodo de producto serie para eliminar el de la secciontemporal y agregar el de la seccion Real**/
               }
               fila += '</div></td>';
                if (tipo_docu != 'B' && tipo_docu != 'N') {
                    fila += '<td width="6%"><div align="center"><input type="text" size="5" maxlength="10" class="cajaGeneral" name="prodpu_conigv[' + n + ']" id="prodpu_conigv[' + n + ']" value="' + pu_conigv + '"  onblur="modifica_pu_conigv(' + n + ');" onkeypress="return numbersonly(this,event,\'.\');" readonly/></div></td>'
                    fila += '<td width="6%"><div align="center"><input type="text" size="5" maxlength="10" class="cajaGeneral"  name="prodpu[' + n + ']" id="prodpu[' + n + ']" value="' + pu + '" onblur="modifica_pu(' + n + ');" onkeypress="return numbersonly(this,event,\'.\');" readonly></div></td>';
                    fila += '<td width="6%"><div align="center"><input type="text" size="5" maxlength="10" class="cajaGeneral cajaSoloLectura" name="prodprecio[' + n + ']" id="prodprecio[' + n + ']" value="' + subtotal + '" readonly="readonly">';
                } else {
                    fila += '<td width="6%"><div align="center"><input type="text" size="5" maxlength="10" class="cajaGeneral" name="prodpu_conigv[' + n + ']" id="prodpu_conigv[' + n + ']" value="' + pu_conigv + '"  onblur="modifica_pu_conigv(' + n + ');" onkeypress="return numbersonly(this,event,\'.\');" readonly/></div></td>'
                    fila += '<td width="6%"><div align="center"><input type="text" size="5" maxlength="10" class="cajaGeneral"  name="prodpu[' + n + ']" id="prodpu[' + n + ']" value="' + pu + '" onblur="modifica_pu(' + n + ');" onkeypress="return numbersonly(this,event,\'.\');" readonly></div></td>';
                    fila += '<td width="6%"><div align="center"><input type="text" size="5" maxlength="10" class="cajaGeneral cajaSoloLectura" name="prodprecio[' + n + ']" id="prodprecio[' + n + ']" value="' + subtotal + '" readonly="readonly">';
                }
                fila += '<input type="hidden" size="1" readonly name="proddescuento100[' + n + ']" id="proddescuento100[' + n + ']" value="' + descuento100 + '">';
                if (tipo_docu != 'B' && tipo_docu != 'N')
                    fila += '<input type="hidden" size="1" maxlength="10" readonly class="cajaGeneral" name="proddescuento[' + n + ']" class="proddescuento" id="proddescuento[' + n + ']" value="' + descuento + '" onblur="calcula_importe2(' + n + ');calcula_totales();">';
                else
                    fila += '<input type="hidden" size="5" maxlength="10" readonly class="cajaGeneral" name="proddescuento[' + n + ']" id="proddescuento[' + n + ']" value="' + descuento_conigv + '" onblur="calcula_importe2_conigv(' + n + ');calcula_totales_conigv();">';
               

                fila += '<td width="6%" style="display:none;"><div align="center"><input type="text" size="5" class="cajaGeneral cajaSoloLectura" name="prodigv[' + n + ']" value="' + igv + '" id="prodigv[' + n + ']" readonly></div></td>';
                fila += '<td width="6%" style="display:none;" ><div align="center">';
                fila += '<input type="hidden" name="detacodi[' + n + ']" id="detacodi[' + n + ']">';
                fila += '<input type="hidden" name="proddescuento_conigv[' + n + ']" id="proddescuento_conigv[' + n + ']" onblur="calcula_importe2_conigv(' + n + ');" value="0">';
                fila += '<input type="hidden" name="prodigv100[' + n + ']" id="prodigv100[' + n + ']" value="' + igv100 + '">';
                fila += '<input type="hidden" name="detaccion[' + n + ']" id="detaccion[' + n + ']" value="n">';
                fila += '<input type="hidden" name="almacenProducto[' + n + ']" id="almacenProducto[' + n + ']" value="' + almacenProducto + '"/>';
                fila += '<input type="text" value="0" size="1" name="proddescuento[' + n + ']" class="proddescuento" id="proddescuento[' + n + ']" onblur="calcula_importe2(' + n + ');" />';
                fila += '<input type="text" size="5" class="cajaGeneral cajaSoloLectura" name="prodimporte[' + n + ']" id="prodimporte[' + n + ']" value="' + total + '" readonly="readonly" value="0">';
                /**se agrega la guia de remision asociada***/
                fila += '<input type="hidden" name="codigoGuiarem[' + n + ']" id="codigoGuiarem[' + n + ']" value="' + guia + '">';
                /**fin de agregar la guia de remision**/
                fila += '</div></td>';
                fila += '</tr>';
                $("#tblDetalleComprobante").append(fila);
            }
            
            $('#ruc_cliente').val(ruc);
            $('#cliente').val(cliente);
            $('#nombre_cliente').val(razon_social);
            $('#forma_pago').val(formapago);
            $('#moneda').val(moneda);
            n++;
        })
        
        /**verificamos si la comprobacion del documento es del mismo proveedor o cliente**/
        if(!valorVerificar){
        	return false;
        }
    
        agregarGuiasRelacion(guia,serie,numero,randomColor);
        if (n >= 0) {
            if (tipo_docu != 'B' && tipo_docu != 'N')
                calcula_totales();
            else
                calcula_totales();

        }
        else {
            alert('El presupuesto no tiene elementos.');
        }
    });
	}
	/**agregagamos listado de guias relacionadas**/
	function agregarGuiasRelacion(codigoGuiarem,serie,numero,color){
		
		var total=$('input[id^="accionAsociacionGuiarem"][value!="0"]').length;
		n = document.getElementById('idTableGuiaRelacion').rows.length;
		
		if(total==0){
			/***mmostramos el div tr de guias relacionadas**/
			$("#idDivGuiaRelacion").show(200);
		}
		
		
		
		proveedor=$("#proveedor").val();
		j=n;
		fila='<tr id="idTrDetalleRelacion_'+j+'">';
		fila+='<td>';
		fila+='<a href="javascript:void(0);" onclick="deseleccionarGuiaremision('+codigoGuiarem+','+j+')" title="Deseleccionar Guia de remision">';
		fila+='x';
		fila+='</a>';
		fila+='</td>';
		fila+='<td>'+j+'</td>';
		fila+='<td>'+serie+'</td>';
		fila+='<td>'+numero+'</td>';
		/**accionAsociacionGuiarem nuevo:1**/
		fila+='<td><div style="width:10px;height:10px;background-color:'+color+';border:1px solid black"></div>';
		fila+='	<input type="hidden" id="codigoGuiaremAsociada['+j+']"  name="codigoGuiaremAsociada['+j+']" value="'+codigoGuiarem+'" />';
		fila+='<input type="hidden" id="accionAsociacionGuiarem['+j+']"  name="accionAsociacionGuiarem['+j+']" value="1" />';
		fila+='<input type="hidden" id="proveedorRelacionGuiarem['+j+']"  name="proveedorRelacionGuiarem['+j+']" value="'+proveedor+'" />';
		fila+='</td>';
		fila+='</tr>';
		$("#idTableGuiaRelacion").append(fila);
		 
	}

	function deseleccionarGuiaremision(codigoGuiarem,posicion){
		
		/**ocultamos el registro de guiaremision asociadas**/
		 a = "codigoGuiaremAsociada[" + posicion + "]";
	     b = "accionAsociacionGuiarem["+posicion+"]";
	     fila = document.getElementById(a).parentNode.parentNode;
	     fila.style.display = "none";
	     /**0:deselecccionado**/
	     document.getElementById(b).value = "0";
		/**fin de ocultar**/
		
	     /**quitamos de lista detalle los productos asociados segun el codigodeGuiuarem***/
		
	     nDetalle = document.getElementById('tblDetalleComprobante').rows.length;
	     /**recorremos para obtener los productos aqsociados a esa guia de remision y lo deseleccionamos**/
	     for(x=0;x<nDetalle;x++){
	    	 c = "codigoGuiarem["+x+"]";
		     valorCodigoGuiarem = document.getElementById(c).value ;
	    	 if(valorCodigoGuiarem==codigoGuiarem){
	    		 a = "detacodi[" + x + "]";
	    	     b = "detaccion[" + x + "]";
	    	     fila = document.getElementById(a).parentNode.parentNode.parentNode;
	    	     fila.style.display = "none";
	    	     document.getElementById(b).value = "e";
	    	 }
	     }
	     
	     
	     if (tipo_docu != 'B' && tipo_docu != 'N')
	    	 calcula_totales();
	     else
	    	 calcula_totales_conigv();
	     
	     verificarOcultarListadoGuiaremAsociado();
		
	}
	
	
	function verificarOcultarListadoGuiaremAsociado(){
		
		/**fin de**/
	     var total=$('input[id^="accionAsociacionGuiarem"][value!="0"]').length;
		if(total==0){
			/**verificamos si contiene accion:0 lo eliminamos los tr**/
			n = document.getElementById('idTableGuiaRelacion').rows.length;
			if(n>1){
				for(x=1;x<n;x++){
					document.getElementById("idTableGuiaRelacion").deleteRow(1);
				}
			}
			$("#idDivGuiaRelacion").hide(200);
			document.getElementById("buscar_producto").readOnly = false;
			$("#idDivAgregarProducto").show(200);
			$("#moneda").show(200);
			$("#textoMoneda").html("");
			$("#textoMoneda").hide(200);
		}
		
	}
	
	/**desleccionamos todo el listado**/
	function listadoGuiaremEstadoDeseleccionado(){
		 var total=$('input[id^="accionAsociacionGuiarem"][value!="0"]').length;
			if(total!=0){
				n = document.getElementById('idTableGuiaRelacion').rows.length;
				if(n>1){
					for(x=1;x<n;x++){
						aAG="accionAsociacionGuiarem["+x+"]";
						document.getElementById(aAG).value=0;
					}
				}
			}
		
	}
	
	
	

// gcbq
function agregar_producto_guiarem2(codproducto, producto, nombre_producto, cantidad, igv, precio_conigv, unidad_medida, nombre_unidad, codigo_orden, flagGenInd, moneda) {
    igv = parseInt(igv);
    if (contiene_igv == '1')
        precio = money_format(precio_conigv * 100 / (igv + 100))
    else {
        precio = precio_conigv;
        precio_conigv = money_format(precio_conigv * (100 + igv) / 100);
    }
    stock = '0'
    costo = '0';
    n = document.getElementById('tblDetalleComprobante').rows.length;

    if ($("#ordencompra").val() != codigo_orden) {
        limpiar_datos();
    }

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
    fila += '<input type="hidden" class="cajaMinima" name="prodcodigo[' + n + ']" id="prodcodigo[' + n + ']" value="' + codproducto + '">' + producto;
    fila += '<input type="hidden" class="cajaMinima" name="produnidad[' + n + ']" id="produnidad[' + n + ']" value="' + unidad_medida + '">';
    fila += '<input type="hidden" class="cajaMinima" name="flagGenIndDet[' + n + ']" id="flagGenIndDet[' + n + ']" value="' + flagGenInd + '">';
    fila += '</div></td>';
    fila += '<td><div align="left">';
    fila += '<input type="text" class="cajaGeneral" style="width:395px;" maxlength="250" name="proddescri[' + n + ']" id="proddescri[' + n + ']" value="' + nombre_producto + '">';
    fila += '</div></td>';
    fila += '<td width="10%"><div align="left">';
    fila += '<input type="text" class="cajaGeneral" size="1" maxlength="5" name="prodcantidad[' + n + ']" id="prodcantidad[' + n + ']" value="' + cantidad + '" onblur="calcula_importe(' + n + ');" onkeypress="return numbersonly(this,event,\'.\');"> ' + nombre_unidad;
    if (flagGenInd == "I") {
        if (tipo_oper == 'V')
            fila += ' <a href="javascript:;" onclick="ventana_producto_serie2(' + n + ')"><img src="' + base_url + 'images/flag-green_icon.png" width="20" height="20" border="0" align="absmiddle"/></a>';
        else
            fila += ' <a href="javascript:;" onclick="ventana_producto_serie(' + n + ')"><img src="' + base_url + 'images/flag-green_icon.png" width="20" height="20" border="0" align="absmiddle" /></a>';
    }
    fila += '</div></td>';
    fila += '<td width="6%"><div align="center"><input type="text"  size="5" maxlength="10" value="' + precio_conigv + '" name="prodpu_conigv[' + n + ']" id="prodpu_conigv[' + n + ']" onblur="modifica_pu_conigv(' + n + ');"></div></td>';
    fila += '<td width="6%"><div align="center"><input type text" size="5" maxlength="10" class="cajaGeneral" value="' + precio + '" name="prodpu[' + n + ']" id="prodpu[' + n + ']" onblur="modifica_pu(' + n + ');" onkeypress="return numbersonly(this,event,\'.\');">'
    fila += '<td width="6%"><div align="center"><input type="text" size="5" maxlength="10" class="cajaGeneral cajaSoloLectura" name="prodprecio[' + n + ']" id="prodprecio[' + n + ']" value="0" readonly="readonly"></div></td>';
    fila += '<td width="6%" style="display:none"><div align="center">';
    fila += '<input type="hidden" name="proddescuento100[' + n + ']" id="proddescuento100[' + n + ']" value="0">';
    fila += '<input type="hidden" size="5" maxlength="10" class="cajaGeneral" name="proddescuento[' + n + ']" id="proddescuento[' + n + ']" onblur="calcula_importe2(' + n + ');" />';
    fila += '</div></td>';
    fila += '<td width="6%" style="display:none" ><div align="center"><input type="text" size="5" maxlength="10" class="cajaGeneral cajaSoloLectura" name="prodigv[' + n + ']" id="prodigv[' + n + ']" readonly></div></td>';
    fila += '<td width="6%" style="display:none" ><div align="center">';
    fila += '<input type="hidden" class="cajaMinima" name="detacodi[' + n + ']" id="detacodi[' + n + ']">';
    fila += '<input type="hidden" class="cajaMinima" name="detaccion[' + n + ']" id="detaccion[' + n + ']" value="n">';
    fila += '<input type="hidden" name="prodigv100[' + n + ']" id="prodigv100[' + n + ']" value="' + igv + '">';
    fila += '<input type="hidden" class="cajaPequena2" name="prodcosto[' + n + ']" id="prodcosto[' + n + ']" value="' + costo + '" readonly="readonly">';
    fila += '<input type="hidden" class="cajaPequena2" name="prodventa[' + n + ']" id="prodventa[' + n + ']" value="0" readonly="readonly">';
    fila += '<input type="text" size="5" maxlength="10" class="cajaGeneral cajaSoloLectura" name="prodimporte[' + n + ']" id="prodimporte[' + n + ']" value="0" readonly="readonly" >';
    fila += '</div></td>';
    fila += '</tr>';
    $("#tblDetalleComprobante").append(fila);

    calcula_importe(n);
    $('#ordencompra').val(codigo_orden);
    return true;
}
function agregar_ocompra_guiarem2(proveedor, ruc_proveedor, nombre_proveedor, almacen, moneda, numero, codigo_usuario) {
    tipo_oper = $("#tipo_oper").val();

    if (tipo_oper == 'V') {
        $('#cliente').val(proveedor);
        $('#ruc_cliente').val(ruc_proveedor);
        $('#nombre_cliente').val(nombre_proveedor);
    } else {
        $('#proveedor').val(proveedor);
        $('#ruc_proveedor').val(ruc_proveedor);
        $('#nombre_proveedor').val(nombre_proveedor);
    }

    $("#serieguiaverOC").html("O. de compra: " + numero + '-' + codigo_usuario);
    $("#serieguiaverOC").show(2000);
    $("#serieguiaverRecu").hide(2000);
    $("#serieguiaver").hide(2000);
    $("#serieguiaverPre").hide(2000);
    $("#numero_ref").val('');
    $("#dRef").val('');
    $("#docurefe_codigo").val('');

    $('#almacen').val(almacen);
    if (moneda == 'NUEVOS SOLES') {
        $('#moneda').val('1');
    } else {
        $('#moneda').val('2');
    }
}

function agregar_todo_recu(guia) {
    descuento100 = $("#descuento").val();
    igv100 = $("#igv").val();
    /***obtenenemos el almacen de la factura**/
	almacen=$("#almacen").val();
	/**fin de obtener el almacen**/
    url = base_url + "index.php/ventas/comprobante/obtener_detalle_comprobante/" + guia;
    n = document.getElementById('tblDetalleComprobante').rows.length;
    
    $.ajax({
        url: url,
        dataType: 'json',
        async: false, 
        success: function (data) {
        limpiar_datos();
        $.each(data, function (i, item) {
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
                flagGenInd = item.CPDEC_GenInd;
                almacenProducto=item.ALMAP_Codigo;
                costo = item.CPDEC_Costo;
                cantidad = item.CPDEC_Cantidad;
                pu = item.CPDEC_Pu;
                subtotal = item.CPDEC_Subtotal;
                descuento = item.CPDEC_Descuento;
                igv = item.CPDEC_Igv;
                total = item.CPDEC_Total
                pu_conigv = item.CPDEC_Pu_ConIgv;
                subtotal_conigv = item.CPDEC_Subtotal_ConIgv;
                descuento_conigv = item.CPDEC_Descuento_ConIgv;

                /**verificamos si el producto esta inventariado ***/
                var url2 = base_url+"index.php/almacen/producto/verificarInventariado/"+producto;
                isMostrarArticulo=true;
                isSeleccionarAlmacen=false;
                $.ajax({
                    url: url2,
                    async: false, 
                    success: function (data2) {
    	            	/***articulos con serie**/
    	            	if(flagGenInd=="I"){
    	            		if(data2.trim()=="1")
    	            		{
    	            			almacenProducto=null;
    	            			isSeleccionarAlmacen=1;
    	            		}else{
    	            			alert("No se puede ingresar este producto Serie, no contiene Inventario");
    	            			isMostrarArticulo=false;
    	            		}
    	            	}else{
    	            		/***articulos sin serie**/
    	            		if(data2.trim()=="1")
    	            		{
    	            			almacenProducto=null;
    	            			isSeleccionarAlmacen=1;
    	            		}else{
    	            			/**no esta inventariado pero se selecciona almacen por default del comprobante**/
    	            			almacenProducto=almacen;
    	            		}
    	            	}
                    }	
                });
                
                /**fin de verificacion**/
                if(isMostrarArticulo){
	                if (j % 2 == 0) {
	                    clase = "itemParTabla";
	                } else {
	                    clase = "itemImparTabla";
	                }
	                fila = '<tr class="' + clase + '" id="'+n+'">';
	                fila += '<td width="3%"><div align="center"><font color="red"><strong><a href="javascript:;" onclick="eliminar_producto_ocompra(' + n + ');">';
	                fila += '<span style="border:1px solid red;background: #ffffff;">&nbsp;X&nbsp;</span>';
	                fila += '</a></strong></font></div></td>';
	                fila += '<td width="4%"><div align="center">' + j + '</div></td>';
	                fila += '<td width="10%"><div align="center">';
	                fila += '<input type="hidden" class="cajaGeneral" name="prodcodigo[' + n + ']" id="prodcodigo[' + n + ']" value="' + producto + '">' + codproducto;
	                fila += '<input type="hidden" class="cajaGeneral" name="produnidad[' + n + ']" id="produnidad[' + n + ']" value="' + unidad_medida + '">';
	                fila += '<input type="hidden" class="cajaMinima" name="flagGenIndDet[' + n + ']" id="flagGenIndDet[' + n + ']" value="' + flagGenInd + '">';
	                fila += '</div></td>';
	                fila += '<td><div align="left"><input type="text" class="cajaGeneral" size="73" maxlength="250" name="proddescri[' + n + ']" id="proddescri[' + n + ']" value="' + nombre_producto + '" /></div></td>';
	                fila += '<td width="10%"><div align="left"><input type="text" size="1" maxlength="5" class="cajaGeneral" name="prodcantidad[' + n + ']" id="prodcantidad[' + n + ']" value="' + cantidad + '" onblur="calcula_importe(' + n + ');calcula_totales();" onkeypress="return numbersonly(this,event,\'.\');">' + nombre_unidad;
	                
	                if (flagGenInd == "I") {
		            	fila +='<a href="javascript:;" id="imgEditarSeries' + n + '" onclick="ventana_producto_serie('+ n +')" ><img src="'+base_url+'images/flag-green_icon.png" width="20" height="20"  border="0" class="imgBoton"></a>';
		            	fila += '<input type="hidden" value="'+isSeleccionarAlmacen+'" name="isSeleccionarAlmacen[' + n + ']" id="isSeleccionarAlmacen[' + n + ']">';
		 	         }else{
			            /**verificamos si el producto debe de ser selccionar el almacen por dfault no existe y hay en otros almacenes **/
			            if(isSeleccionarAlmacen){
			            	fila +='<a href="javascript:;" id="imgSeleccionarAlmacen' + n + '" onclick="mostrarPopUpSeleccionarAlmacen('+ n +')" ><img src="'+base_url+'images/almacen.png" width="20" height="20"  border="0" class="imgBoton"></a>';
			            } 	
		            }
	                fila += '</div></td>';
	                fila += '<td width="6%"><div align="center"><input type="text" size="5" maxlength="10" class="cajaGeneral" name="prodpu_conigv[' + n + ']" id="prodpu_conigv[' + n + ']" value="' + pu_conigv + '"  onblur="modifica_pu_conigv(' + n + ');" onkeypress="return numbersonly(this,event,\'.\');" /></div></td>'
	                fila += '<td width="6%"><div align="center"><input type="text" size="5" maxlength="10" class="cajaGeneral" name="prodpu[' + n + ']" id="prodpu[' + n + ']" value="' + pu + '" onblur="modifica_pu(' + n + ');" onkeypress="return numbersonly(this,event,\'.\');">';
	                fila += '<td width="6%"><input type="text" class="cajaGeneral cajaSoloLectura" size="5" maxlength="10" name="prodprecio[' + n + ']" id="prodprecio[' + n + ']" value="' + subtotal + '" readonly="readonly"></div></td>';
	                fila += '<td width="6%" style="display:none;"><div align="center">';
	                fila += '<input type="hidden" name="proddescuento100[' + n + ']" id="proddescuento100[' + n + ']" value="' + descuento100 + '">';
	                fila += '<input type="hidden" size="5" maxlength="10" class="cajaGeneral" name="proddescuento[' + n + ']" id="proddescuento[' + n + ']" value="' + descuento + '" onblur="calcula_importe(' + n + ');calcula_totales();">';
	
	                fila += '</div></td>';
	                fila += '<td width="6%" style="display:none;"><div align="center"><input type="text" size="5" class="cajaGeneral cajaSoloLectura" name="prodigv[' + n + ']" value="' + igv + '" id="prodigv[' + n + ']" readonly></div></td>';
	                fila += '<td width="6%" style="display:none;"><div align="center">';
	                fila += '<input type="hidden" name="detacodi[' + n + ']" id="detacodi[' + n + ']">';
	                fila += '<input type="hidden" name="detaccion[' + n + ']" id="detaccion[' + n + ']" value="n">';
	                fila += '<input type="hidden" name="prodigv100[' + n + ']" id="prodigv100[' + n + ']" value="' + igv100 + '">';
	                fila += '<input type="hidden" name="almacenProducto[' + n + ']" id="almacenProducto[' + n + ']" value="' + almacenProducto + '"/>';
	                fila += '<input type="hidden" class="cajaPequena2" name="prodcosto[' + n + ']" id="prodcosto[' + n + ']" value="' + costo + '" readonly="readonly">';
	                fila += '<input type="hidden" class="cajaPequena2" name="prodventa[' + n + ']" id="prodventa[' + n + ']" value="0" readonly="readonly">';
	                fila += '<input type="text" size="5" class="cajaGeneral cajaSoloLectura" name="prodimporte[' + n + ']" id="prodimporte[' + n + ']" value="' + total + '" readonly="readonly" value="0">';
	                fila += '</div></td>';
	                fila += '</tr>';
	                $("#tblDetalleComprobante").append(fila);
	                n++;
                }
            }
            $('#moneda').val(moneda);
           
            
            
            
        })
        if (n >= 0)
            if (tipo_docu != 'B' && tipo_docu != 'N')
                calcula_totales();
            else
                calcula_totales();

       }
    });
    
}

	function verificarProductoDetalle(codigoProducto,codigoAlmacen){
		n = document.getElementById('tblDetalleComprobante').rows.length;	
		isEncuentra=false;
		if(n!=0){
			for(x=0;x<n;x++){
				d="detaccion["+x+"]";
				accionDetalle=document.getElementById(d).value;
				if(accionDetalle!="e"){
					/***verificamos si existe el mismo producto y no lo agregamos**/
					a="almacenProducto["+x+"]";
					c="prodcodigo["+x+"]";
					almacenProducto=document.getElementById(a).value;
					codProducto=document.getElementById(c).value;
					if(codProducto==codigoProducto && almacenProducto==codigoAlmacen){
						isEncuentra=true;	
						break;
					}
				}
			}
		}
		return isEncuentra;
	}
	
	/**mostrar agregar servicio si solamente tiene muchas guia de remision**/
	function verificarServicio(objeto){
		valorBS=$(objeto).val();
		/**OBTENENEMOS CANTIDAD SI EXISTE GUIAS DE REMISION RELACIONADAS**/
		var total=$('input[id^="accionAsociacionGuiarem"][value!="0"]').length;
		if(total>0){
			/**si es servicio***/
			if(valorBS=='S'){
				document.getElementById("buscar_producto").readOnly = false;
				$("#idDivAgregarProducto").show(200);
			}
			/**si es Bien**/
			if(valorBS=='B'){
				document.getElementById("buscar_producto").readOnly = true;
				$("#idDivAgregarProducto").hide(200);
			}
		}
	}
	
	

    function verPdf(){
    var dataEviar="_____";
    base_url2 = $("#base_url").val();
    tipo_oper2 = $("#tipo_oper").val();
    tipo_docu2 = $("#tipo_docu").val();
    fechai2=$("#fechai").val().split("/");
    fechafin=$("#fechaf").val().split("/");
    series=$("#seriei").val();
    numeros=$("#numero").val();
    rucCs=$("#ruc_cliente").val();
    nombreCliente=$("#nombre_cliente").val();
    ruc_prove=$("#ruc_proveedor").val();
    nomb_proveer=$("#nombre_proveedor").val();
    //fechafin=$("#fechaf").val().split("/");
    var datafechaIni="";var datafechafin="";
    if($("#fechai").val()!=""){
     datafechaIni=fechai2[2]+"-"+fechai2[1]+"-"+fechai2[0];
    }
   if($("#fechaf").val()!=""){
   datafechafin=fechafin[2]+"-"+fechafin[1]+"-"+fechafin[0];
  }
  if(tipo_oper2=='V'){
   dataEviar=datafechaIni+"_"+datafechafin+"_"+series+"_"+numeros+"_"+rucCs+"_"+nombreCliente;
     
  }else{
   dataEviar=datafechaIni+"_"+datafechafin+"_"+series+"_"+numeros+"_"+ruc_prove+"_"+nomb_proveer;
     
  }
  
    var url3 =base_url2+ "index.php/ventas/comprobante/verPdf/" + tipo_oper2 + "/" + tipo_docu2+"/"+dataEviar;
    window.open(url3, '', "width=800,height=600,menubars=no,resizable=no;");
 
}
function ver_reporte_productos() {

    var prod = $("#productoDescripcion").val();

    var anio = $("#anioVenta").val();
    var mes = $("#mesventa").val();
    var fech1 = $("#fech1").val();
    var fech2 = $("#fech2").val();
   
    var tipodocumento = $("#tipodocumento").val();
    var Prodcod = $("#reporteProducto").val();
    
    if(anio=="0") {anio="--";} 
    if(mes=="")   {mes="--";} 
  
    if(tipodocumento=="")  {tipodocumento="--";}

    var datafechaIni="";var datafechafin="";

    if(fech1=="") {
        fech1="--";
    }else{
        fechai=$("#fech1").val().split("/"); 
        fech1=fechai[2]+"-"+fechai[1]+"-"+fechai[0];
    }

    if(fech2=="") {
        fech2="--";
    }else{
        fechaf=$("#fech2").val().split("/");
        fech2=fechaf[2]+"-"+fechaf[1]+"-"+fechaf[0];

    }
    

    url = base_url + "index.php/ventas/comprobante/ver_reporte_pdf_productos/" + anio+"/" + mes+"/" + fech1+"/" + fech2+"/"+tipodocumento +"/"+Prodcod;
    if(Prodcod!="" && prod !="")  {
    	 if($("#fech1").val() <= $("#fech2").val())  {
    		 window.open(url, '', "width=800,height=600,menubars=no,resizable=no;");
    	 }else{
    	    	alert("Seleccione un rango de fechas validas");
    	    	$("#fech2").focus();
    	    }
    }else{
    	alert("Seleccione un Producto");
    	$("#productoDescripcion").focus();
    }
 
}