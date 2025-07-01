var base_url;
var contiene_igv;
var tipo_docu;
var tipo_codificacion;
jQuery(document).ready(function(){
    base_url   = $("#base_url").val();
    contiene_igv = $("#contiene_igv").val();
    tipo_docu   = $("#tipo_docu").val();
    tipo_codificacion = $("#tipo_codificacion").val();
    
    $("#imprimirPedido").click(function(){
        var numero = $("#txtNumDoc").val();
        var cliente = $("#cliente").val();
        var fechai=$("#fechai").val().split("/");
        var fechaf=$("#fechaf").val().split("/");

        var datafechaIni="";
        var datafechafin="";
    ///
      if(fechai==""){fechai="--";}else{fechai=fechai[2]+"-"+fechai[1]+"-"+fechai[0];}
      if(fechaf==""){fechaf="--";}else{fechaf=fechaf[2]+"-"+fechaf[1]+"-"+fechaf[0];}
      if(numero==""){numero="--";}
      if(cliente==""){cliente="--";}

    url = base_url+"index.php/compras/pedido/registro_pedido_pdf/"+fechai+"/"+ fechaf+"/"+numero+"/"+ cliente;
    window.open(url,'',"width=800,height=600,menubars=no,resizable=no;");
    });

    $("#imgGuardarPedidoOLD").click(function(){
        //validar campos llenados
        serie   = $("#serie").val();
        numero   = $("#numero").val();
        cliente   = $("#cliente").val();
        ruc   = $("#ruc_cliente").val();
        nombre   = $("#nombre_cliente").val();
        
        if(serie!=""){
            if( numero!="" ){
                if(cliente != ""){
                    if(ruc != "" || nombre != ""){
                        
                        n = document.getElementById('tblDetallePedido').rows.length;
                           if(n==0){
                               alert("Ingrese algún Producto antes guardar lista vacia");
                           }else{
                               cont=0;
                               for(i=0;i<n;i++){
                                   e  = "detaccion["+i+"]";
                                   if(document.getElementById(e).value =='e'){
                                    cont++;
                                    }
                                }
                               if(cont==n){
                                   alert("Ingrese algún Producto antes guardar borrado");
                               }
                               else{
                                   dataString = $('#frmPedido').serialize();
                                   var codigo=$('#codigo').val();
                                   
                                    if(codigo==''){ 
                                        url = base_url+"index.php/compras/pedido/insertar_pedido";
                                        var mensaje="Se ha ingresado un pedido.";
                                    }else{
                                        url = base_url+"index.php/compras/pedido/modificar_pedido";
                                        var mensaje="El pedido se modificó correctamente.";
                                    }
                                    
                                    $.post(url,dataString,function(data){
                                        location.href = base_url+"index.php/compras/pedido/pedidos";
                                    });
                               }
                               
                           }
                    }else{
                    alert("Porfavor seleccionar al cliente");}
                }
                else{
                    alert("Porfavor vuelva a seleccionar al cliente");
                }
            }
            else{
                alert("Ingrese numero");
            }
            
        }else{
            alert("Ingrese serie");
        }
        
    });

    //Guardar el comprobante
    $("#imgGuardarPedido").click(function () {
        $( "#salir").val(1);
        $('img#loading').css('visibility', 'visible');

        var tipoOperacion = $('#tipo_oper').val();
       
        $("#imgGuardarPedido").css('visibility', 'hidden');
        var codigo = $('#codigo').val();
        var tipo_d = $("#cboTipoDocu").val();
        if ($("#serie").val() == "") {
            $("#serie").focus();
            alert("Ingrese la serie.");
            $('#imgGuardarPedido').css('visibility', 'visible');
            $('img#loading').css('visibility', 'hidden');
            return false;
        }

        if (tipo_oper == 'C') {
            if ($("#numero").val() == "") {
                $("#numero").focus();
                alert("Ingrese el numero documento.");
                $('#imgGuardarPedido').css('visibility', 'visible');
                $('img#loading').css('visibility', 'hidden');
                return false;
            }
        }

        /*if (tipo_oper == 'V') {

            if ($('#cliente').val() == '') {
                $("#cliente").focus();
                alert("Debe seleccionar Cliente.");
                $('#imgGuardarPedido').css('visibility', 'visible');
                $('img#loading').css('visibility', 'hidden');
                return false;
            }
        }*/

        serie = $("#serie").val();
        numero = $("#numero").val();
        $("#ser_imp").val(serie);
        $("#num_imp").val(numero);
        
        /**verificamos si tiene guias de remision asociadas***/
        cantidadGuiaRemision=$('input[id^="accionAsociacionGuiarem"][value!="0"]').length;
        /*** fin de verificacion*/
        n = document.getElementById('tempde_tbl').rows.length;
        console.log(n);
        /**verificamos si es producto Individual y verifiamos que tenga la misma cantidad de serie**/
        if(cantidadGuiaRemision==0){
            if(n!=0){
                 var  isSalir=false;
                    for(x=0;x<n;x++){
                        valor= "flagGenIndDet["+x+"]"; 
                        //var  valor_flagGenIndDet = document.getElementById(valor).value ;
                        valorAccion="detaccion["+x+"]"; 
                        var  valorAccionReal = document.getElementById(valorAccion).value ;
                        
                        /***verificamos si contiene almacenProducto diferente de null o vacio **/
                        if(valorAccionReal!='e'){
                            if($("#almacen").find('option').length == 1) {
                                document.getElementById("almacenProducto["+x+"]").value = $("#almacen").val();
                            }

                            alm="almacenProducto["+x+"]";
                            var  isExisteAlmacenProducto = document.getElementById(alm).value;
                            var elementImportado = document.getElementById("esImportado["+x+"]");
                            var esImportado = parseInt(!elementImportado ? 0 : elementImportado.value);
                        }
                    }
                    if(isSalir==true){
                        $('#grabarPedido').css('visibility', 'visible');
                        $('img#loading').css('visibility', 'hidden');
                        return false;
                    }
                    
            }else {
                alert("Ingrese un producto.");
                $('#grabarPedido').css('visibility', 'visible');
                $('img#loading').css('visibility', 'hidden');
                return ;
            }
               
        }

        if (codigo == '')
            url = base_url+"index.php/compras/pedido/insertar_pedido";
        else
            url = base_url+"index.php/compras/pedido/modificar_pedido";

        var estatusP = parseFloat( $("#estatus").val() );
        if ( estatusP == 1 ){
            seguro = confirm('LOS ARTICULOS DE LA LISTA SERAN AÑADIDOS AL INVENTARIO. ¿DESEA CONTINUAR?');

            if (seguro == false)
                return false;
        }

        dataString = $('#frmPedido').serialize();
        $.ajax({
            type: "POST",
            url: url,
            data: dataString,
            dataType: 'json',
            async: false,
            beforeSend: function (data) {
            },
            error: function (data) {
                $('img#loading').css('visibility', 'hidden');
                console.log(data);
                alert('No se puedo completar la operación - Revise los campos ingresados.')
            },
            success: function (data) {
                $('img#loading').css('visibility', 'hidden');
                switch (data.result) {
                    case 'ok':

                        $('#codigo').val(data.codigo);
                        $('#ventana').show();

                        if (tipoOperacion == 'C' || tipoOperacion == "C") {
                            $('#cancelarImprimirPedido').click();
                        } else {
                            $('#cancelarImprimirPedido').click();
                        }

                        //location.href = base_url+"index.php/ventas/comprobante/comprobantes"+"/"+tipo_oper+"/"+tipo_docu;

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
                    case 'error3':
                        alert(data.msj);
                        break;
                }
            }


        });
        
    });
    $("#buscarPedido").click(function(){
        $("#form_busqueda").submit();
    }); 
    $("#nuevoPedido").click(function(){
        url = base_url+"index.php/compras/pedido/nuevo_pedido";
        location.href = url;
        //$("#zonaContenido").load(url);
    });
    $("#limpiarPedido").click(function(){
        url = base_url+"index.php/compras/pedido/pedidos";
        location.href=url;
    });
    $("#limpiarnewPedido").click(function(){
        url = base_url+"index.php/compras/pedido/nuevo_pedido";
        location.href=url;
    });
    $("#cancelarPedido, #cancelarImprimirPedido").click(function(){
        base_url = $("#base_url").val();
        location.href = base_url+"index.php/compras/pedido/pedidos";
    });
  //ENTER
    $('#ruc_cliente').keyup(function (e) {
        var key = e.keyCode || e.which;
        if (key == 13) {
            if ($(this).val() != '') {
                $('#linkSelecCliente').attr('href', base_url + 'index.php/empresa/cliente/ventana_selecciona_cliente/' + $('#ruc_cliente').val()).click();
            }
        }
    });
    
 $("#linkVerSerieNum").click(function () {
        var temp = $("#linkVerSerieNum p").html();
        var serienum = temp.split('-');
       
        //switch (tipo_codificacion) {
            //case '1':
                //$("#numero").val(serienum[1]);
                
               // break;
           // case '2':
                $("#serie").val(serienum[0]);
                $("#numero").val(serienum[1]);
               // alert(serienum[1]);
               // break;
        //}
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
   
    
    container = $('div.container');
    /*$("#frmPedido").validate({
        event    : "blur",
        rules    : {
                    'centro_costo' : "required",
                    'responsable_value' : "required",
                    'observacion'  : "required",
                   },
        debug    : true,
        errorContainer      : "container",
        errorLabelContainer : $(".container"),
        wrapper             : 'li',
        submitHandler       : function(form){
                var valor = $('#centro_costo').val();
                if(valor == 0){
                    alert('Elija un centro de costo');
                    return false;
                }
        
        valor = $('#tipo_pedido').val();
                if(valor == 0){
                    alert('Elija un tipo de pedido');
                    return false;
                }
                
                dataString  = $('#frmPedido').serialize();                               
                modo        = $("#modo").val();
                $('#VentanaTransparente').css("display","block");
                if(modo=='insertar'){
                    url = base_url+"index.php/compras/pedido/insertar_pedido";
                    $.post(url,dataString,function(data){
                    $("#VentanaTransparente").css("display","none");
                        alert('Se ha ingresado un pedido.');
                        //location.href = base_url+"index.php/compras/pedido/pedidos";
                    });
                }
                else if(modo=='modificar'){
                    url = base_url+"index.php/compras/pedido/modificar_pedido";
                    $.post(url,dataString,function(data){
                        $("#VentanaTransparente").css("display","none");
                        alert('Su registro ha sido modificado.');
                        //location.href = base_url+"index.php/compras/pedido/pedidos";
                    });
                }
        }
    });*/
   
    container = $('div.container');   
});

function eliminar_producto_pedido(n){
    if(confirm('Esta seguro que desea eliminar este producto?')){
        a                   = "detacodi["+n+"]";
        e                   = "detaccion["+n+"]";
        fila                = document.getElementById(a).parentNode.parentNode.parentNode;
        fila.style.display  ="none";
        document.getElementById(e).value="e";
        
        calcula_totales();
    }
}

function pedido_pdf(comprobante, imagen = 0) {
    var url = base_url + "index.php/compras/pedido/pedido_pdf/"+comprobante+"/"+imagen;
    window.open(url, '', "width=800,height=600,menubars=no,resizable=no;");
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
            $("#idDivGuiaRelacion").hide(200);tempde_producto
            //document.getElementById("buscar_producto").readOnly = false;
            document.getElementById("tempde_producto").readOnly = false;
            $("#idDivAgregarProducto").show(200);
            $("#moneda").show(200);
            $("#textoMoneda").html("");
            $("#textoMoneda").hide(200);
        }
        
    }

function agregar_producto_pedido(){
    flagBS  = $("#flagBS").val();
    
    if($("#producto").val()==''){
        alert('Ingrese el producto.');
        $("#codproducto").focus();
        return false;
    }
    if($("#cantidad").val()==''){
        alert('Ingrese una cantidad.');
        $("#cantidad").focus();
        return false;
    }
    if($("#unidad_medida").val()==0){
        $("#unidad_medida").focus();
        alert('Seleccione una unidad de medida.');
        return false;
    }
    codproducto     = $("#codproducto").val();
    producto        = $("#producto").val();
    nombre_producto = $("#nombre_producto").val();
    descuento = $("#descuento").val();
    cantidad        = $("#cantidad").val();
    igv = parseInt($("#igv").val());
    precio_conigv = $("#precio").val();
    if(contiene_igv=='1'){
        precio=money_format(precio_conigv*100/(igv+100))
} else{
        precio=precio_conigv;
        precio_conigv = money_format(precio_conigv*(100+igv)/100);
    }
    stock           = parseFloat($("#stock").val());
    costo           = parseFloat($("#costo").val());
    unidad_medida   = '';
    nombre_unidad   = '';
    if(flagBS=='B'){
        unidad_medida = $("#unidad_medida").val();
        nombre_unidad = $('#unidad_medida option:selected').html()
    }
    
    flagGenInd      = $("#flagGenInd").val();
    almacenProducto =$("#almacenProducto").val();
    n = document.getElementById('tblDetallePedido').rows.length;
    j = n+1;
    if(j%2==0){
        clase="itemParTabla";
    }else{
        clase="itemImparTabla";
    }
    
    
    fila = '<tr class="'+clase+'">';
    fila+= '<td width="3%"><div align="center"><font color="red"><strong><a href="javascript:;" onclick="eliminar_producto_pedido('+n+');">';
    fila+= '<span style="border:1px solid red;background: #ffffff;">&nbsp;X&nbsp;</span>';
    fila+= '</a></strong></font></div></td>';
    fila+= '<td width="4%"><div align="center">'+j+'</div></td>';
    fila+= '<td width="10%"><div align="center">';
    fila+= '<input type="hidden" class="cajaMinima" name="prodcodigo['+n+']" id="prodcodigo['+n+']" value="'+producto+'">'+codproducto;
    fila+= '<input type="hidden" class="cajaMinima" name="produnidad['+n+']" id="produnidad['+n+']" value="'+unidad_medida+'">';
    fila+= '<input type="hidden" class="cajaMinima" name="flagGenIndDet['+n+']" id="flagGenIndDet['+n+']" value="'+flagGenInd+'">';
    fila+= '</div></td>';
    fila+= '<td><div align="left">';
    fila+= '<input type="text" class="cajaGeneral" style="width:395px;" maxlength="250" name="proddescri['+n+']" id="proddescri['+n+']" value="'+nombre_producto+'">';
    fila+= '</div></td>';
    fila+= '<td width="10%"><div align="left">';
    fila+= '<input type="text" class="cajaGeneral" size="1" maxlength="5" name="prodcantidad['+n+']" id="prodcantidad['+n+']" value="'+cantidad+'" onblur="calcula_importe('+n+');" onkeypress="return numbersonly(this,event,\'.\');"> ' + nombre_unidad;

    fila+= '</div></td>';
    fila += '<td width="6%"><div align="center"><input type="text" size="5" maxlength="10" class="cajaGeneral" value="'+precio_conigv+'" name="prodpu_conigv['+n+']" id="prodpu_conigv['+n+']" onblur="modifica_pu_conigv('+n+');" onkeypress="return numbersonly(this,event,\'.\');" /></div></td>'
    fila += '<td width="6%"><div align="center"><input type text" size="5" maxlength="10" class="cajaGeneral" value="'+precio+'" name="prodpu['+n+']" id="prodpu['+n+']" onblur="modifica_pu('+n+');" onkeypress="return numbersonly(this,event,\'.\');">'
    fila += '<td width="6%"><div align="center"><input type="text" size="5" maxlength="10" class="cajaGeneral cajaSoloLectura" name="prodprecio['+n+']" id="prodprecio['+n+']" value="0" readonly="readonly"></div></td>';
    fila+= '<td width="6%"><div align="center"><input type="text" size="5" maxlength="10" class="cajaGeneral cajaSoloLectura" name="prodigv['+n+']" id="prodigv['+n+']" readonly></div></td>';
    fila+= '<td width="6%"><div align="center">';
    fila+= '<input type="hidden" name="detacodi['+n+']" id="detacodi['+n+']">';
    fila+= '<input type="hidden" name="detaccion['+n+']" id="detaccion['+n+']" value="n">';
    fila+= '<input type="hidden" name="prodigv100['+n+']" id="prodigv100['+n+']" value="'+igv+'">';
    fila+= '<input type="hidden" name="prodstock['+n+']" id="prodstock['+n+']" value="'+stock+'"/>';
    fila+= '<input type="hidden" name="prodcosto['+n+']" id="prodcosto['+n+']" value="'+costo+'" readonly="readonly">';
    fila += '<input type="hidden" name="almacenProducto[' + n + ']" id="almacenProducto[' + n + ']" value="' + almacenProducto + '"/>';
    fila+= '<input type="hidden" name="proddescuento100['+n+']" id="proddescuento100['+n+']" value="'+descuento+'">';
    fila+= '<input type="hidden" name="proddescuento['+n+']" id="proddescuento['+n+']" onblur="calcula_importe2('+n+');" />';
    fila+= '<input type="text" size="5" maxlength="10" class="cajaGeneral cajaSoloLectura" name="prodimporte['+n+']" id="prodimporte['+n+']" value="0" readonly="readonly">';
    fila+= '</div></td>';
    fila+= '</tr>';
    $("#tblDetallePedido").append(fila);
    
    inicializar_cabecera_item();  
    calcula_importe(n);
    return true;  
}

function inicializar_cabecera_item(){
    $("#producto").val('');
    $("#buscar_producto").val('');
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

function calcula_importe(n){
    a  = "prodpu["+n+"]";
    b  = "prodcantidad["+n+"]";
    c  = "proddescuento["+n+"]";
    d  = "prodigv["+n+"]";
    e  = "prodprecio["+n+"]";
    f  = "prodimporte["+n+"]";
    g = "prodigv100["+n+"]";
    h = "proddescuento100["+n+"]";
    i = "prodpu_conigv["+n+"]";
   // k = "preciobruto["+n+"]";
    pu = document.getElementById(a).value;
    pu_conigv = document.getElementById(i).value;
    cantidad = document.getElementById(b).value;
    igv100 = document.getElementById(g).value;
    descuento100 =document.getElementById(h).value;
    precio = money_format(pu*cantidad);
    precio_des=money_format(precio*descuento100/100);
    precio_total=money_format(precio-parseFloat(precio_des));
    preciodescuento=money_format(pu_conigv*cantidad);
    total_dscto = money_format(preciodescuento*descuento100/100);
    precio2 = money_format(precio-parseFloat(total_dscto));
    
    if(pu_conigv=='')
        total_igv = money_format(precio2*igv100/100);
    else{
        total_igv = money_format((pu_conigv-pu)*cantidad);
        igvdes= money_format(total_igv*descuento100/100);
        igv=money_format(total_igv-parseFloat(igvdes));
    }
    importe = money_format(precio-parseFloat(total_dscto)+parseFloat(total_igv));

    document.getElementById(c).value = total_dscto;//proddescuento
    document.getElementById(d).value = igv;//prodigv
    document.getElementById(e).value = precio_total;//prodprecio
    document.getElementById(f).value = importe;//prodimporte
    //document.getElementById(k).value = preciodescuento;//precio_bruto

    calcula_totales();
} 
function calcula_totales(){
    
    n = document.getElementById('tblDetallePedido').rows.length;
    importe_bruto=0;
    descuento_total = 0;
    valor_venta = 0;
    igv_total = 0;
    precio_total = 0;
    bruto_total = 0;
    importe_total =0;
    bruto_desc = 0;
   // descuentoporciento = $("#descuento").val();
   // igvporciento = $("#igv").val();

    for(i=0;i<n;i++){//Estanb al reves los campos
        a = "prodimporte["+i+"]"
        b = "prodigv["+i+"]";
        c = "proddescuento["+i+"]";
        d = "prodprecio["+i+"]";
        e  = "detaccion["+i+"]";
       
        f  = "prodpu["+i+"]";
        g  = "prodcantidad["+i+"]";

        h = "proddescuento100["+i+"]";


       if(document.getElementById(e).value!='e'){
            importe = parseFloat(document.getElementById(a).value);
            descuento = parseFloat(document.getElementById(c).value);
            preciosigv = parseFloat(document.getElementById(d).value);
            igv = parseFloat(document.getElementById(b).value);
            descuento100 = parseFloat(document.getElementById(h).value);
            pu = document.getElementById(f).value;
            cantidad = document.getElementById(g).value;
            precio = money_format(pu*cantidad);
            
            igv_total = money_format(igv + igv_total);
            descuento_total = money_format(descuento + descuento_total);
            bruto_total = money_format(precio + bruto_total);
            bruto_desc = money_format(bruto_total*descuento100/100);
            valor_venta = money_format(preciosigv + valor_venta);
            importe_total=money_format(importe+importe_total);
            
        }
       
    }
    importebruto=money_format(bruto_total);
    descuentotal=money_format(bruto_desc);
    vventa=money_format(valor_venta);
    igvtotal=money_format(igv_total);
    preciototal=money_format(importe_total);
   // desc=(importe_bruto*descuentoporciento)/100;
  //  vventa=importe_bruto-desc;
   // igvtotal=(vventa*igvporciento)/100;
   // preciototal=vventa+igvtotal;
    $("#importebruto").val(importebruto.toFixed(2)); // importe bruto 
    $("#descuentotal").val(descuentotal.toFixed(2));//descuento total
    $("#vventa").val(vventa.toFixed(2)); //valor de venta
    $("#igvtotal").val(igvtotal.toFixed(2));  //valor del igv
    $("#preciototal").val(preciototal.toFixed(2));// importe total
}
function modifica_descuento_total(){
    descuento = $('#descuento').val();
    n     = document.getElementById('tblDetallePedido').rows.length;
    for(i=0;i<n;i++){
        a = "proddescuento100["+i+"]";
        document.getElementById(a).value = descuento;
    }
    for(jj=0;jj<n;jj++){
        calcula_importe(jj);
    }
    calcula_totales();
    modifica_pu_conigv();
    
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
function obtener_precio_producto(){
    var producto = $("#producto").val();
    $('#precio').val("");
    if(producto=='' || producto=='0')
        return false;
    var moneda = $("#moneda").val();
    if(moneda=='' || moneda=='0')
        return false;
    var unidad_medida = $("#unidad_medida").val();
    if(unidad_medida=='' || unidad_medida=='0')
        return false;
    var cliente = $("#cliente").val();
    if(cliente=='')
        cliente='0';
    var igv;
   if(contiene_igv=='1')
        igv=0;
    else
        if(tipo_docu!='B')
            igv=0;
        else
            igv=$("#igv").val();
    
    var url = base_url+"index.php/almacen/producto/JSON_precio_producto/"+producto+"/"+moneda+"/"+cliente+"/"+unidad_medida+"/"+igv;
    $.getJSON(url,function(data){
              $.each(data, function(i,item){
                    $('#precio').val(item.PRODPREC_Precio);
              });
    });
    return true;
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

function editar_pedido(pedido){
    //alert("Opción en mantenimiento");
    var url = base_url+"index.php/compras/pedido/editar_pedido/"+pedido;
    location.href = url;
    //$("#zonaContenido").load(url);
}
function eliminar_pedido(pedido){
    if(confirm('Esta seguro desea eliminar este pedido?')){
        dataString = "pedido="+pedido;
        url = base_url+"index.php/compras/pedido/eliminar_pedido";
        $.post(url,dataString,function(data){
            url = base_url+"index.php/compras/pedido/pedidos";
            location.href = url;
        });
    }
}

function ver_pedido(pedido){
    url = base_url+"index.php/compras/pedido/ver_pedido/"+pedido;
    $("#zonaContenido").load(url);
}
function atras_persona(){
    location.href = base_url+"index.php/compras/pedido/pedidos";
}
//ONBLUR
function onblurclienteruc() {
    ruc= $('#ruc_cliente').val();
    if(ruc != ""){
        ruc = sintilde(ruc);
        $('#linkSelecCliente').attr('href', base_url + 'index.php/empresa/cliente/ventana_selecciona_cliente/' +ruc).click();
    }
}
function onblurclientename() {
    name= $('#nombre_cliente').val();
    if(name != ""){
        name = sintilde(name);
        $('#linkSelecCliente').attr('href', base_url + 'index.php/empresa/cliente/ventana_selecciona_cliente/' + name).click();
    }
}

function sintilde(cadena){
       
       var specialChars = "!@#$^&%*()+=-[]\/{}|:<>?,";

       
       for (var i = 0; i < specialChars.length; i++) {
           cadena= cadena.replace(new RegExp("\\" + specialChars[i], 'gi'), '');
       }   

       // Lo queremos devolver limpio en minusculas
       cadena = cadena.toLowerCase();

       // Quitamos acentos y "ñ". Fijate en que va sin comillas el primer parametro
       cadena = cadena.replace(/á/gi,"a");
       cadena = cadena.replace(/é/gi,"e");
       cadena = cadena.replace(/í/gi,"i");
       cadena = cadena.replace(/ó/gi,"o");
       cadena = cadena.replace(/ú/gi,"u");
       cadena = cadena.replace(/ñ/gi,"n");
       return cadena;
    }

function verificarProductoDetalle(codigoProducto,codigoAlmacen){
    n = document.getElementById('tblDetallePedido').rows.length;    
    isEncuentra=false;
    if(n!=0){
        for(x=0;x<n;x++){
            d="detaccion["+x+"]";
            accionDetalle=document.getElementById(d).value;
            if(accionDetalle!="e"){
                /***verificamos si existe el mismo producto y no lo agregamos**/
                a="almacenProducto["+x+"]";
                c="prodcodigo["+x+"]";
//              almacenProducto=document.getElementById(a).value;
                codProducto=document.getElementById(c).value;
//              alert(codProducto);
//              if(codProducto==codigoProducto && almacenProducto==codigoAlmacen){
                if(codProducto==codigoProducto){
                    isEncuentra=true;   
                    break;
                }
            }
        }
    }
    return isEncuentra;
}

       
function mostrar_precio() {
    precio = $("#precioProducto").val();
    $("#precio").val(precio);
}