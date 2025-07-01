<?php
$nombre_persona = $this->session->userdata('nombre_persona');
$persona = $this->session->userdata('persona');
$usuario = $this->session->userdata('usuario');
$url = base_url() . "index.php";
if (empty($persona))
    header("location:$url");
?>
<html>
<head><meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
    <script type="text/javascript" src="<?php echo base_url(); ?>public/js/jquery.js?=<?=JS;?>"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>public/js/jquery-ui-1.8.17.custom.min.js?=<?=JS;?>"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>public/js/funciones.js?=<?=JS;?>"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>public/js/ventas/comprobante.js?=<?=JS;?>"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>public/js/almacen/producto.js?=<?=JS;?>"></script>
    <script src="<?php echo base_url(); ?>public/js/jquery.columns.min.js?=<?=JS;?>"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>public/js/fancybox/jquery.mousewheel-3.0.4.pack.js?=<?=JS;?>"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>public/js/fancybox/jquery.fancybox-1.3.4.pack.js?=<?=JS;?>"></script>
    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>public/js/fancybox/jquery.fancybox-1.3.4.css?=<?=CSS;?>" media="screen"/>

    <script src="<?php echo base_url(); ?>bootstrap/js/bootstrap.min.js?=<?=JS;?>"></script>
    <script src="<?php echo base_url(); ?>bootstrap/js/bootstrap.js?=<?=JS;?>"></script>

    <link href="<?php echo base_url(); ?>bootstrap/css/bootstrap.css?=<?=CSS;?>" rel="stylesheet">
    <link href="<?php echo base_url(); ?>bootstrap/css/bootstrap-theme.css?=<?=CSS;?>" rel="stylesheet">

<script type="text/javascript">
    $(document).ready(function () { <?php
        if ($tipo_oper == 'V'){
            switch ($tipo_docu) {
                case 'F': ?> setLimite(<?php echo VENTAS_FACTURA; ?>); <?php
                    break;
                case 'B': ?> setLimite(<?php echo VENTAS_BOLETA; ?>); <?php
                    break;
                case 'N': ?> setLimite(<?php echo VENTAS_COMPROBANTE; ?>); <?php
                    break;
                default:
                    break;
            }
        }
        else
            if ($tipo_oper == 'C') {
                switch ($tipo_docu) {
                    case 'F': ?> setLimite(<?php echo COMPRAS_FACTURA; ?>); <?php
                        break;
                    case 'B': ?>
                        setLimite(<?php echo COMPRAS_BOLETA; ?>); <?php
                    break;
                    default:
                        break;
                }
            } ?>
            
        /**dialogo series asosicadas**/
        $("#dialogSeriesAsociadas").dialog({
            resizable: false,
            height: "auto",
            width: 400,
            autoOpen: false,
            show: {
              effect: "blind",
              duration: 500
            },
            hide: {
              effect: "blind",
              duration: 500
            }
        });
        /**fin **/
        /**dialogo series asosicadas**/
        $("#dialogoSeleccionarALmacenProducto").dialog({
            resizable: false,
            height: "auto",
            width: 400,
            autoOpen: false,
            show: {
              effect: "blind",
              duration: 500
            },
            hide: {
              effect: "blind",
              duration: 500
            },
            buttons: {
                "Aceptar": function() {
                    grabarSeleccionarAlmacen();
                },
                Cancel: function() {
                  $(this).dialog( "close" );
                }
              }
        });
        /**fin **/
        /***verificacion de si es editar y esta relacionada con otras guias **/
        <?php   if(count($listaGuiaremAsociados)>0){  ?>
            document.getElementById("tempde_producto").readOnly = true;
            $("#addItems").hide(200);
        <?php } ?>
        /***fin de realizar verificacion**/
        
        /**ejecutar mostrar orden de compra vista si existe**/
        <?php if($ordencompra!=0 &&  trim($ordencompra)!="" && $ordencompra!=null){   ?>
        mostrarOdenCompraVista(<?php echo $ordencompra.",".$serieOC.",".$numeroOC.",". $valorOC; ?>);
        <?php } ?>
        /**no mostrar**/
        /**ejecutar mostrar PRESUPUESTO vista si existe**/
        <?php if($presupuesto_codigo!=0 &&  trim($presupuesto_codigo)!="" && $presupuesto_codigo!=null){   ?>
        mostrarPresupuestoVista(<?php echo $presupuesto_codigo.",'".$seriePre."',".$numeroPre.",'". $tipo_oper."'"; ?>);7
        <?php } ?>
        /**no mostrar**/
        
        /*if ($('#tdcDolar').val() == '') {
            alert("Antes de registrar comprobantes debe ingresar Tipo de Cambio");
            top.location = "<?php echo base_url(); ?>index.php/index/inicio";
        }*/
        base_url = $("#base_url").val();
        tipo_oper = $("#tipo_oper").val();
        almacen = $("#cboCompania").val();
        $("a#linkVerCliente, a#linkSelecCliente, a#linkVerProveedor, a#linkSelecProveedor").fancybox({
            'width': 800,
            'height': 550,
            'autoScale': false,
            'transitionIn': 'none',
            'transitionOut': 'none',
            'showCloseButton': true,
            'modal': false,
            'type': 'iframe'
        });
        $(" #linkSelecProducto").fancybox({
            'width': 800,
            'height': 500,
            'autoScale': false,
            'transitionIn': 'none',
            'transitionOut': 'none',
            'showCloseButton': true,
            'modal': false,
            'type': 'iframe'
        });
        $("a#linkVerProducto").fancybox({
            'width': 800,
            'height': 650,
            'autoScale': false,
            'transitionIn': 'none',
            'transitionOut': 'none',
            'showCloseButton': true,
            'modal': true,
            'type': 'iframe'
        });
        $("#linkVerImpresion").fancybox({
            'width': 300,
            'height': 450,
            'autoScale': false,
            'transitionIn': 'none',
            'transitionOut': 'none',
            'showCloseButton': true,
            'modal': true
        });

        $(".verDocuRefe").fancybox({
            'width': 770,
            'height': 520,
            'autoScale': false,
            'transitionIn': 'none',
            'transitionOut': 'none',
            'showCloseButton': true,
            'modal': false,
            'type': 'iframe',
            'onStart': function () {
                tipoMoneda=$("#moneda").val();
                almacen=$("#almacen").val();
                if (tipo_oper == 'V') {
                    if ($('.verDocuRefe::checked').val() == 'PR')
                        baseurl = base_url + 'index.php/compras/pedido/ventana_muestra_pedido/' + tipo_oper + '/' + $('#cliente').val() + '/SELECT_HEADER/<?=$tipo_docu;?>/' + almacen + '/PR';
                     
                    $('.verDocuRefe::checked').attr('href', baseurl);
                    
                } else {

                    if ($('#proveedor').val() == '') {
                        alert('Debe seleccionar el proveedor.');
                        $('#nombre_proveedor').focus();
                        return false;
                    } else {
                        if ($('.verDocuRefe::checked').val() == 'G') {
                            baseurl = base_url + 'index.php/almacen/guiarem/ventana_muestra_guiarem/' + tipo_oper + '/' + $('#proveedor').val() + '/SELECT_HEADER/F/' + almacen + '/G/'+tipoMoneda;
                        }
                        else if ($('.verDocuRefe::checked').val() == 'P') {
                            if (tipo_oper == 'V')
                                baseurl = base_url + 'index.php/ventas/presupuesto/ventana_muestra_presupuestoCom/' + tipo_oper + '/' + $('#proveedor').val() + '/SELECT_HEADER/<?php echo $tipo_docu; ?>/' + almacen + '/P';
                            else
                                baseurl = base_url + 'index.php/compras/presupuesto/ventana_muestra_presupuestoCom/' + tipo_oper + '/' + $('#proveedor').val() + '/SELECT_HEADER/<?php echo $tipo_docu; ?>/' + almacen + '/P';
                        }
                        else if ($('.verDocuRefe::checked').val() == 'O') {
                            baseurl = base_url + 'index.php/compras/ocompra/ventana_muestra_ocompraCom/' + tipo_oper + '/' + $('#proveedor').val() + '/SELECT_HEADER/<?php echo $tipo_docu; ?>/' + almacen + '/O';
                        }
                        else if ($('.verDocuRefe::checked').val() == 'R') {
                            baseurl = base_url + 'index.php/ventas/comprobante/ventana_muestra_recurrentes/' + tipo_oper + '/' + $('#proveedor').val() + '/SELECT_HEADER/<?php echo $tipo_docu; ?>/' + almacen + '/R';
                        }
                        //alert(baseurl);

                        $('.verDocuRefe::checked').attr('href', baseurl);
                    }
                }
            }
        });

    });

    //AUTOCOMPLETO DE PRODUCTOS
    $(function () {
        $("#buscar_producto").autocomplete({
            source: function (request, response) {
                $.ajax({
                    url: "<?php echo base_url(); ?>index.php/almacen/producto/autocomplete/" + $("#flagBS").val() + "/" + $("#compania").val()+"/"+$("#almacen").val(),
                    type: "POST",
                    data: {
                        term: $("#buscar_producto").val()
                    },
                    dataType: "json",
                    success: function (data) {
                        response(data);
                    }
                });
            },
            select: function (event, ui) {
                /**si el producto tiene almacen : es que no esta inventariado en ese almacen , se le asigna el almacen general de cabecera**/
                if(ui.item.almacenProducto==0){
                    ui.item.almacenProducto=$("#almacen").val();
                }
                /**fin de asignacion**/
                isEncuentra=verificarProductoDetalle(ui.item.codigo,ui.item.almacenProducto);
                if(!isEncuentra){
                       $("#buscar_producto").val(ui.item.codinterno);
                       $("#producto").val(ui.item.codigo);
                       $("#codproducto").val(ui.item.codinterno);
                       $("#costo").val(ui.item.pcosto);
                       $("#stock").val(ui.item.stock);
                       $("#flagGenInd").val(ui.item.flagGenInd);
                       $("#almacenProducto").val(ui.item.almacenProducto);
                       $("#cantidad").focus();
                       listar_unidad_medida_producto(ui.item.codigo);
                    //verificar_Inventariado_producto();
                }else{
                    $("#buscar_producto").val("");
                      $("#producto").val("");
                      $("#codproducto").val("");
                      $("#costo").val("");
                      $("#stock").val("");
                      $("#flagGenInd").val("");
                        $("#nombre_producto").val("");
                      $("#almacenProducto").val("");
                    $("#buscar_producto").val("");
                    alert("El producto ya se encuentra ingresado en la lista de detalles.");
                    return !isEncuentra;
                }
            },
            minLength: 1
        });


        //****** nuevo para ruc
            $("#buscar_cliente").autocomplete({
                source: function (request, response) {
                    $.ajax({
                        url: "<?php echo base_url(); ?>index.php/empresa/cliente/autocomplete_ruc/",
                        type: "POST",
                        data: {
                            term: $("#buscar_cliente").val()
                        },
                        dataType: "json",
                        success: function (data) {
                            if (data.length == 0)
                                $(".input-group-btn").css("opacity",1);
                            else{
                                $(".input-group-btn").css("opacity",0);
                                response(data);
                            }
                        }
                    });
                },
                select: function (event, ui) {
                    $("#nombre_cliente").val(ui.item.nombre);
                    $("#cliente").val(ui.item.codigo);
                    $("#ruc_cliente").val(ui.item.ruc);
                    $("#TipCli").val(ui.item.TIPCLIP_Codigo); // Codigo del cliente para el precio del producto - 
                    $("#buscar_producto").focus();
                    codigo=ui.item.codigo;
                    get_obra(codigo);
                },
                minLength: 2
            });

            $("#buscar_cliente").change(function(){
                if ($("#buscar_cliente").val().length == 0)
                    $(".input-group-btn").css("opacity",0);
            });

        /* Descativado hasta corregir vico 22082013 - quien es vico? (fixed) - pregunto lo mismo que es vicio(ABAc). */

        //AUTOCOMENTADO EN CLIENTE BUSCAR
        $("#nombre_cliente").autocomplete({
            //flag = $("#flagBS").val();
            source: function (request, response) {
                $.ajax({
                    url: "<?php echo base_url(); ?>index.php/empresa/cliente/autocomplete/",
                    type: "POST",
                    data: {
                        term: $("#nombre_cliente").val()
                    },
                    dataType: "json",
                    success: function (data) {
                        response(data);
                    }
                });
            },
            select: function (event, ui) {
                $("#buscar_cliente").val(ui.item.ruc);
                $("#cliente").val(ui.item.codigo);
                $("#ruc_cliente").val(ui.item.ruc);
                $("#TipCli").val(ui.item.TIPCLIP_Codigo); // Codigo del cliente para el precio del producto - 
                $("#buscar_producto").focus();
                $("#linkVerDirecciones").trigger('click');
                codigo=ui.item.codigo;
                get_obra(codigo);
            },
            minLength: 2
        });


        /* Descativado hasta corregir vico 22082013  */
        nombreProveedorAnterior="";
        $("#nombre_proveedor").autocomplete({
            //flag = $("#flagBS").val();
            source: function (request, response) {
                $.ajax({
                    url: "<?php echo base_url(); ?>index.php/empresa/proveedor/autocomplete/",
                    type: "POST",
                    data: {
                        term: $("#nombre_proveedor").val()
                    },
                    dataType: "json",
                    success: function (data) {
                        response(data);
                    }
                });
            },
            select: function (event, ui) {
                /**verificamos si tiene un proveedor agregadoiy si se modifica debe eliminar todo los demas**/
                n = document.getElementById('idTableGuiaRelacion').rows.length;
                if(n>1){
                    if(confirm("¿Desea cambiar de proveedor, se eliminaran las guias relacionadas?")){
                            
                    }else{
                        //$("#nombre_proveedor").val(nombreProveedorAnterior);
                    }
                }else{
                    $("#buscar_proveedor").val(ui.item.ruc);
                    $("#proveedor").val(ui.item.codigo);
                    $("#ruc_proveedor").val(ui.item.ruc);
                    $("#buscar_producto").focus();
                }   
            },
            minLength: 1
        });

        //****** nuevo para ruc PROVEEDOR
        $("#buscar_proveedor").autocomplete({
            source: function (request, response) {
                $.ajax({
                    url: "<?php echo base_url(); ?>index.php/empresa/proveedor/autocomplete_ruc/",
                    type: "POST",
                    data: {
                        term: $("#buscar_proveedor").val()
                    },
                    dataType: "json",
                    success: function (data) {
                        response(data);
                    }
                });
            },
            select: function (event, ui) {
                $("#nombre_proveedor").val(ui.item.nombre);
                $("#proveedor").val(ui.item.codigo);
                $("#ruc_proveedor").val(ui.item.ruc);
                $("#buscar_producto").focus();
            },
            minLength:2 
        });
    });

    $("#linkVerproyectoss").click(function () {
        if (tipo_oper == 'V')
            var url = base_url + "index.php/maestros/proyecto/JSON_listar_proyectos/" +$("#cliente").val();
        $("#lista_proyecto ul").html('');
        $("#lista_proyecto").slideToggle("fast", function () {
            $.getJSON(url, function (data) {
                $.each(data, function (i, item) {
                    fila = '';
                        fila += '<li><a href="javascript:;">';
                        if (item.nombre != '')
                            fila += ' ' + item.nombre;
                        if (item.descripcion != '')
                            fila += ' - ' + item.descripcion;
                        fila += '</a></li>';
                  $("#lista_proyecto  ul").append(fila);
                });
            });
        });
    }); 

    $('a').on('click', function(){
      window.last_clicked_time = new Date().getTime();
      window.last_clicked = $(this);
    });

    $(window).bind('beforeunload', function() {
        if ( $("#salir").val() == 0 ){
          var time_now = new Date().getTime();
          var link_clicked = window.last_clicked != undefined;
          var within_click_offset = (time_now - window.last_clicked_time) < 100;
          if (link_clicked && within_click_offset) {
            return 'You clicked a link to '+window.last_clicked[0].href+'!';
          } else {
            return 'Estas abandonando la página!';
          }
        }
    });

    function seleccionar_cliente(codigo, ruc, razon_social) {
        $("#cliente").val(codigo);
        $("#buscar_cliente").val(ruc);
        $("#nombre_cliente").val(razon_social);
        get_obra(codigo);
    }

    function seleccionar_proveedor(codigo, ruc, razon_social) {
        $("#proveedor").val(codigo);
        $("#buscar_proveedor").val(ruc);
        $("#nombre_proveedor").val(razon_social);
    }

    function seleccionar_producto(producto, cod_interno, familia, stock, costo, flagGenInd,codigoAlmacenProducto) {
        /**si el producto tiene almacen : es que no esta inventariado en ese almacen , se le asigna el almacen general de cabecera**/
        if(codigoAlmacenProducto==0){
            codigoAlmacenProducto=$("#almacen").val();
         }
        /**fin de asignacion**/
        /**verificamos si se e3ncuentra en la lista**/
        isEncuentra=verificarProductoDetalle(producto,codigoAlmacenProducto);
        if(!isEncuentra){
               $("#codproducto").val(cod_interno);
               $("#producto").val(producto);
               $("#cantidad").focus();
               $("#stock").val(stock);
               $("#costo").val(costo);
               $("#flagGenInd").val(flagGenInd);
               $("#almacenProducto").val(codigoAlmacenProducto);
               listar_unidad_medida_producto(producto);
        }else{
            $("#buscar_producto").val("");
            $("#producto").val("");
            $("#codproducto").val("");
            $("#costo").val("");
            $("#stock").val("");
            $("#flagGenInd").val("");
            $("#nombre_producto").val("");
            $("#almacenProducto").val("");
            $("#buscar_producto").val("");
            $("#buscar_producto").focus();
            alert("El producto ya se encuentra ingresado en la lista de detalles.");
      }
    }

    function seleccionar_documento_detalle(producto, codproducto, nombre_producto, cantidad, flagBS, flagGenInd, unidad_medida, nombre_medida, precio_conigv, precio_sinigv, precio, igv, importe, stock, costo) {
        agregar_fila(producto, codproducto, nombre_producto, cantidad, flagBS, flagGenInd, unidad_medida, nombre_medida, precio_conigv, precio_sinigv, precio, igv, importe, stock, costo);
    }

    function seleccionarOdenCompra(oCompra, serie, numero, valor){
        mostrarOdenCompraVista(oCompra,serie, numero, valor);
        //obtener_detalle_ocompra(oCompra);
        obtener_comprobantes_temproductos(oCompra,'ocompras');
        /**quitamos lista de guiarem **/
        listadoGuiaremEstadoDeseleccionado();
        verificarOcultarListadoGuiaremAsociado();
    }
        

    function mostrarOdenCompraVista(oCompra,serie, numero, valor){
        if(valor == 1){
            serienumero = "Numero de Orden Compra. :" + serie + " - " + numero;
        }else{
            serienumero = "Numero de Orden Venta. :" + serie + " - " + numero;
        }
        $("#serieguiaverOC").html(serienumero);
        $("#serieguiaverOC").show(200);
        $("#serieguiaverPre").hide(200);
        $("#serieguiaver").hide(200);
        $("#serieguiaverRecu").hide(200);
        $('#ordencompra').val(oCompra);
        codigoPresupuesto=$("#presupuesto_codigo").val();
        if(codigoPresupuesto!="" && codigoPresupuesto!=0){
                modificarTipoSeleccionPrersupuesto(codigoPresupuesto,0);
        }
        $("#presupuesto_codigo").val("");
    }

    function seleccionar_pedido(guia, serie, numero) {
        realizado = verificar_agregar_pedido(guia);
        if(realizado!=false){
            obtener_comprobantes_temproductos(guia,'pedido');
            $('#docReferencia').val(guia);
            $('#serieDocRelacionado').html('Pedido: ' + serie + ' - ' + numero);
            $('#serieDocRelacionado').show();
        }
    }

    function verificar_agregar_pedido(guia){
        n = document.getElementById('idTableGuiaRelacion').rows.length;
        /**limpiamos los articulos agregados si seleccionamos una guia de remision**/
        if(n==1){
            if(confirm("¿Desea agregar la orden de pedido?")){
                $("#tblDetalleComprobante").html("");   
                /**bloqueamos los opcion de obtener articulos y ocultamnos agregar producto verificamos si es servicio para que no lo oculte**/
                valorFlagBS=$("#flagBS").val();
                if(valorFlagBS=='B'){
                    document.getElementById("tempde_producto").readOnly = true;
                    $("#idDivAgregarProducto").hide(200);
                }
                
                $("#moneda").hide(200);
                textoMoneda = $("#moneda option:selected").text();
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
                codGuia = document.getElementById('codigoGuiaremAsociada['+ x +']').value;
                if( codGuia == guia ){
                    alert("La orden de pedido se encuentra seleccionada."); 
                    return false;
                }
            }
        }
    }
 
    function seleccionar_presupuesto(guia, serieguia, numeroguia) {
        isRealizado=modificarTipoSeleccionPrersupuesto(guia,1);
        if(isRealizado){
            tipo_oper = $("#tipo_oper").val();
            //agregar_todopresupuesto(guia, tipo_oper);
            obtener_comprobantes_temproductos(guia,'presupuesto');
            mostrarPresupuestoVista(guia, serieguia, numeroguia,tipo_oper);
            /**quitamos lista de guiarem **/
            listadoGuiaremEstadoDeseleccionado();
            verificarOcultarListadoGuiaremAsociado();
        }
        
    }

    function mostrarPresupuestoVista(guia, serieguia, numeroguia,tipo_oper){
        if(tipo_oper=="V")
            serienumero = "Numero de PRESUPUESTO :" + serieguia + " - " + numeroguia;
        else
            serienumero = "Numero de COTIZACIÓN :" + serieguia + " - " + numeroguia;
            
        $("#serieguiaverPre").html(serienumero);
        $("#serieguiaverPre").show(200);
        $("#serieguiaver").hide(200);
        $("#serieguiaverOC").hide(200);
        $("#serieguiaverRecu").hide(200);
        $("#docurefe_codigo").val('');
        $("#dRef").val('');
        $('#ordencompra').val('');
        $("#numero_ref").val('');
        $("#presupuesto_codigo").val(guia);
    }
        
    function seleccionar_comprobante_recu(guia, serieguia, numeroguia) {
        //agregar_todo_recu(guia);
        obtener_comprobantes_temproductos(guia,'comprobantes');
        serienumero = "NÂ° de Comprobante: <br>" + serieguia + " - " + numeroguia;
        $("#serieguiaverRecu").html('<span style="font-size:15px" >Doc. Recurrente: <br>' + serienumero + '</span>');
        $("#serieguiaverRecu").show(200);
        $("#serieguiaver").hide(200);
        $("#serieguiaverRecuFlecha").show(400);
        $("#serieguiaverPre").hide(200);
        $("#serieguiaverOC").hide(200);
        $("#numero_ref").val('');
        $("#dRef").val('');
        $('#ordencompra').val('');
        $("#docurefe_codigo").val('');
        codigoPresupuesto=$("#presupuesto_codigo").val();
        if(codigoPresupuesto!="" && codigoPresupuesto!=0){
            modificarTipoSeleccionPrersupuesto(codigoPresupuesto,0);
        }
        $("#presupuesto_codigo").val("");
        
        /**quitamos lista de guiarem **/
        listadoGuiaremEstadoDeseleccionado();
        verificarOcultarListadoGuiaremAsociado();
    }

    function valida() {
        if (document.forms[0].seriep.value.length > 2) {
            document.forms[0].presupuesto.focus();
            return false;
        }
        else
            return true;
    }

    function tdc_cambiar() {
        $.ajax({
            url: "<?php echo base_url(); ?>index.php/maestros/tipocambio/buscar_json",
            type: "POST",
            data: {
                fecha: $('#fecha').val()
            },
            success: function (data) {
                if (data == 0) {
                    //alert('error Tipo de cambio en esta fecha no ingresada');
                    $('#fecha').val('<?=date('d/m/Y');?>');
                    //tdc_cambiar();
                } else {
                    $('#tdc').val(data);
                }
            }
        });
    }

    function cambiarAlmacenProductoCodigo(almacen){
        posicionSeleccionado=$("#posicionSeleccionadaSerie").val();
        if(posicionSeleccionado!=null && posicionSeleccionado!=''){
        a="almacenProducto["+posicionSeleccionado+"]";
        document.getElementById(a).value=almacen;

        }
    }

    /**seleccionamos un almacen para el producto agregaod po o.vc cotizacioon, recurrentes**/
    function mostrarPopUpSeleccionarAlmacen(posicionSeleccionado, isView){
        var isView = isView == undefined ? true : isView;
        var a="almacenProducto["+posicionSeleccionado+"]";
        var b="prodcodigo["+posicionSeleccionado+"]";
        $("#posicionSeleccionadaSerie").val(posicionSeleccionado);
        var almacenProducto=document.getElementById(a).value;
        var codigoProducto=document.getElementById(b).value;
        var url="<?php echo base_url(); ?>index.php/almacen/producto/buscarAlmacenProducto/"+codigoProducto;

        var n = document.getElementById('idTblAlmacen').rows.length;
        if(n!=null && n!='' && n>1){
            for(var x=1;x<n;x++){
                document.getElementById("idTblAlmacen").deleteRow(1);
            }
        }
        
        $.ajax({
                url: url,
                dataType: 'json',
                //async: false,
                success: function (data) {
                    $.each(data, function (i, item) {
                        var codigoAlmacen=item.codigo;
                        var nombreAlmacen=item.nombreAlmacen;
                        var stock=item.stock;
                        var j=i+1;
                        var fila="<tr id='idTr_"+j+"' >";
                        fila+="<td>";
                        fila+="<input type='radio' name='almacenListado' id='idRdAlmacen"+j+"' value='"+codigoAlmacen+"'>"; 
                        fila+="</td>";
                        fila+="<td>";
                        fila+="<label for='idRdAlmacen"+j+"' >"+nombreAlmacen+"</label>";   
                        fila+="</td>";
                        fila+="<td>";
                        fila+="<label>"+stock+"</label>";   
                        fila+="</td>";
                        fila+="</tr>";
                        $("#idTblAlmacen").append(fila);

                        if($("#almacen").val() == codigoAlmacen) document.getElementById("prodstock["+posicionSeleccionado+"]").value = stock;
                    });
                    if(isView) $("#dialogoSeleccionarALmacenProducto").dialog("open");
                }
        });
    }

    function grabarSeleccionarAlmacen(){
        almacen=$('input:radio[name=almacenListado]:checked').val();
        if(almacen!=null && almacen!=""){
            cambiarAlmacenProductoCodigo(almacen);
            $("#dialogoSeleccionarALmacenProducto").dialog("close");
        }else{
            alert("Debe de seleccionar un almacen para el producto.");
        }
    }

    function get_obra(codigo) {
        //alert(codigo);
        $.post("<?php echo base_url(); ?>index.php/compras/pedido/obra", {
                        "codigoempre" : codigo
            }, function(data) {
                //alert("hola"+data);
                var c = JSON.parse(data);
                $('#obra').html('');
                $('#obra').append("<option value='0'>::Seleccione::</option>");
                $.each(c,function(i,item){
                    $('#obra').append("<option value='"+item.PROYP_Codigo+"'>"+item.proyecto+"</option>");
                });

                var idProyecto = $("#id-proyecto").val();
                if(idProyecto != "") $("#obra").val(idProyecto).trigger('change');
        });
    }
</script>

</head>

<body>


    <!-- Inicio -->
    <div id="VentanaTransparente" style="display:none;">
        <div class="overlay_absolute"></div>
        <div id="cargador" style="z-index:2000">
            <table width="100%" height="100%" border="0" class="fuente8">
                <tr valign="middle">
                    <td> Por Favor Espere</td>
                    <td>
                        <img src="<?php echo base_url(); ?>public/images/icons/cargando.gif" border="0" title="CARGANDO"/>
                        <a href="#" id="hider2"></a>
                    </td>
                </tr>
            </table>
        </div>
    </div>
    <!-- Fin -->

<form id="<?php echo $formulario; ?>" name="<?php echo $formulario; ?>" method="post" action="<?php echo $url_action; ?>">
    <input type="hidden" name="idProduccion" id="idProduccion" value="<?=$produccion;?>"/>
    <input value='<?php echo $compania; ?>' name="compania" type="hidden" id="compania"/>
    <input value='<?=$url_action;?>' name="url" id="url" type="hidden"/>

    <div id="zonaContenido" align="center">
        <?php echo validation_errors("<div class='error'>", '</div>'); ?>
        <div id="tituloForm" class="header" style="height: 20px">
            <?php echo $titulo;?>
            <select id="cboTipoDocu" name="cboTipoDocu" class="comboMedio">
                <option value="PR">PRODUCCIÓN</option>
            </select>
        </div>

        <div id="idDivGuiaRelacion" style="<?php echo (count($listaGuiaremAsociados)>0)?'':'display:none'; ?>">
            <div id="dialogSeriesAsociadas" title="Series Ingresadas">
                <div id="mostrarDetallesSeriesAsociadas">
                    <div id="detallesSeriesAsociadas"></div>
                </div>
            </div>
            
            <!-- dialogo para mostrarse que sleccionar el almacen de un producto -->
            
            <div id="dialogoSeleccionarALmacenProducto" title="Seleccionar Almacen">
                <div id="mostrarDetallesSeleecionarALmacen">    
                    <table id="idTblAlmacen" >
                        <tr id="idTr_0">
                            <td></td>
                            <td width="200px" >Descripci&oacute;n</td>
                            <td width="50px">Stock</td>             
                        </tr>
                    </table>
                </div>
            </div>
            <!-- fin de dialogo -->
                <div id="tituloForm" class="header" style="height: 30px">
                    <h3>GUIAS RELACIONADAS</h3>
                </div>
                <table class="fuente8" id="idTableGuiaRelacion">
                    <tr id="idTrDetalleRelacion_0" >
                        <td></td>
                        <td>ITEM</td>
                        <td>SERIE</td>
                        <td>NUMERO</td>
                        <td>BACKGROUND</td>
                    </tr> <?php
                    if(count($listaGuiaremAsociados)>0){ 
                        foreach ($listaGuiaremAsociados as $indice=>$valorGuiarem){
                            $codigoGuiarem=$valorGuiarem->codigoGuiarem;
                            $serieGuiarem=$valorGuiarem->serie;
                            $numeroGuiarem=$valorGuiarem->numero;
                            $j=$indice+1;
                            $colorGuiar[$codigoGuiarem]="#".dechex(rand(0,10000000)); ?>
                
                            <tr id="idTrDetalleRelacion_<?php echo $j; ?>"> 
                                <td> 
                                    <a href="javascript:void(0);" onclick="deseleccionarGuiaremision(<?php echo $codigoGuiarem; ?>,<?php echo $j; ?>)" title="Deseleccionar Guia de remision"> x</a> 
                                </td> 
                                <td><?php echo $j; ?></td> 
                                <td><?php echo $serieGuiarem; ?></td> 
                                <td><?php echo $numeroGuiarem; ?></td> 
                                <td>
                                    <div style="width:10px;height:10px;background-color:<?php echo $colorGuiar[$codigoGuiarem] ?>; border:1px solid black"></div> 
                                    <input type="hidden" id="codigoGuiaremAsociada[<?php echo $j; ?>]"  name="codigoGuiaremAsociada[<?php echo $j; ?>]" value="<?php echo $codigoGuiarem; ?>" /> 
                                    <input type="hidden" id="accionAsociacionGuiarem[<?php echo $j; ?>]"  name="accionAsociacionGuiarem[<?php echo $j; ?>]" value="2" />
                                    <input type="hidden" id="proveedorRelacionGuiarem[<?php echo $j; ?>]"  name="proveedorRelacionGuiarem[<?php echo $j; ?>]" value="<?php echo $proveedor; ?>" />
                                </td>
                            </tr> <?php
                        }
                    } ?>
                </table>
        </div>
        
        <div id="frmBusqueda">
            <table class="fuente8" width="100%" cellspacing="0" cellpadding="5" border="0">
                <tr>
                    <!--iNDEX DE FACTURA Y BOTELA-->
                    <td width="8%">Número*</td>
                    <td width="25%" valign="middle">
                        <input type="hidden" id="tipo_oper" value="<?php echo $tipo_oper; ?>"/>
                        <input type="hidden" id="guiaremision" value="<?php echo $guiaremision; ?>"/>
                        <input type="hidden" id="posicionSeleccionadaSerie" value="" />
                                        
                        <input class="cajaGeneral" name="serie" type="text" id="serie" size="4" maxlength="4" value="<?=$serie_suger_f;?>"/>&nbsp;
                        <input class="cajaGeneral" name="numero" type="text" id="numero" size="6" maxlength="6" value="<?=$numero_suger_f;?>"/>

                        <label style="margin-left:20px; display:none">IGV
                        <input NAME="igv" type="hidden" class="cajaGeneral cajaSoloLectura" id="igv" size="2" maxlength="2" value="<?php echo $igv; ?>" onkeypress="return numbersonly(this,event,'.');" onblur="modifica_igv_total();" readonly="readonly"/> %</label>
                        <label hidden>
                            <input id="chk-exonera-igv" type="checkbox" <?php if($igv == 0) echo "checked";?>>Exonerar
                        </label>
                        <script>
                            $(document).ready(function () {
                                $("#chk-exonera-igv").change(function(event) {
                                    var isCheck = $(this).attr('checked'),
                                        igv = <?php echo $igv != 0 ? $igv : $igv_default ?>;
                                    $("#igv").val(isCheck ? 0 : igv);
                                }).trigger('change');
                            });
                        </script>
                    </td>
                    <td width="15%">
                        <?php if ($pedido == 0){ ?>
                            <label for="PR" style="cursor: pointer;"><img src="<?php echo base_url() ?>public/images/icons/opedido.png" class="imgBoton"/></label>
                            <input type="radio" name="referenciar" id="PR" value="PR" href="javascript:;" class="verDocuRefe" style="display:none;">
                        <?php } ?>
                        <input type="hidden" name="docReferencia" id="docReferencia" value="<?=$pedido;?>"/>
                            <div id="serieDocRelacionado" name="serieDocRelacionado" style="background-color: #cc7700; color:#fff; padding:5px; <?=($pedido > 0 ) ? 'width: 100%' : 'display: none';?>"><?="$establecimiento <br>Pedido: $serieNumeroPedido"?></div>
                    </td>
                    <td valign="middle" style="text-align: right">Fecha de Inicio</td>
                    <td valign="middle"><input name="fecha" type="text" class="cajaGeneral cajaSoloLectura" id="fecha" value="<?php echo $fechaI; ?>" size="10" maxlength="10" readonly="readonly"/>
                        <img height="16" border="0" width="16" id="Calendario1" name="Calendario1" src="<?php echo base_url(); ?>public/images/icons/calendario.png"/>
                        <script type="text/javascript">
                            Calendar.setup({
                                inputField: "fecha",  // id del campo de texto
                                ifFormat: "%d/%m/%Y", // formato de la fecha, cuando se escriba en el campo de texto
                                button: "Calendario1" // el id del boton que lanzara el calendario
                            });
                        </script>
                    </td>
                    <td valign="middle" style="text-align: right">Fecha de Entrega</td>
                    <td valign="middle"><input name="fechaF" type="text" class="cajaGeneral cajaSoloLectura" id="fechaF" value="<?php echo $fechaF; ?>" size="10" maxlength="10" readonly="readonly"/>
                        <img height="16" border="0" width="16" id="Calendario2" name="Calendario2" src="<?php echo base_url(); ?>public/images/icons/calendario.png"/>
                        <script type="text/javascript">
                            Calendar.setup({
                                inputField: "fechaF",
                                ifFormat: "%d/%m/%Y",
                                button: "Calendario2"
                            });
                        </script>
                    </td>
                </tr>
                <tr style="display: none">
                    <td>Almacen*</td>
                    <td><?php echo $cboAlmacen; ?></td>
                    <td style="display:none" valign="middle">Moneda*</td>
                    <td style="display:none" valign="middle" id="idTdMoneda">
                        <select name="moneda" id="moneda" class="comboPequeno" style="width:150px;">
                            <?php echo $cboMoneda; ?>
                        </select>
                       <label id="textoMoneda"></label> 
                        
                        <?php if(count($listaGuiaremAsociados)>0){  ?> 
                            <script type="text/javascript">
                                $("#moneda").hide(200);
                                textoMoneda=$("#moneda option:selected").text();
                                $("#textoMoneda").html(textoMoneda);
                                $("#textoMoneda").show(200);
                            </script> <?php
                        } ?>
                    </td>
                    <td colspan="2" style="display:none">
                        TDC Dolar : &nbsp;
                        <input NAME="tdcDolar" type="text" class="cajaGeneral cajaSoloLectura" style="width: 28px" id="tdcDolar" size="3" value="<?php echo $tdcDolar; ?>" onkeypress="return numbersonly(this,event,'.');" readonly="readonly"/>&nbsp;
                        <span id="tdcOpcional">
                            Euro : &nbsp;
                            <input NAME="tdcEuro" type="text" class="cajaGeneral cajaSoloLectura" style="width: 28px" id="tdcEuro" size="3" value="<?php echo $tdcEuro; ?>" onkeypress="return numbersonly(this,event,'.');"/>
                        </span>
                    </td>
                    <script>
                        $("#moneda").change(function(event) {
                            var combo = $(this),
                                codigo = combo.val();

                            $("#tdcOpcional").css('display', codigo > 2 ? '' : 'none');

                            if(codigo > 2) $("#tdcEuro").focus();
                        });

                        $(document).ready(function () {
                            $("#moneda").trigger('change');
                        });

                        descuentoPercent = <?php echo isset($descuento) ? $descuento : 0 ?>;
                    </script>
                </tr>
                <?php if ($tipo_oper == 'V'){?>
                 <tr style="display:none">
                    <td >Proyecto*</td>
                    <td > <?php echo $cboObra;?>    </td>
                    <td colspan="2" style="text-align: left;">Descuento % &nbsp;&nbsp;&nbsp;
                        <input type="text" class="cajaMinima" id="descuento" name="descuento" size="2" maxlength="2" value="<?php echo $descuento; ?>" onKeyPress="return numbersonly(this,event,'.');" onblur="calcular_totales_tempdetalle();">
                        &nbsp;
                        <span style="display: <?php if(!$usa_adelanto == 1) echo 'none' ?>;" id="box-adelantos">
                            Adelanto : 
                            &nbsp;
                            <input type="hidden" class="cajaGeneral cajaSoloLectura" id="proyecto-adelanto" readonly style="width: 100px;text-align: right;">
                            <input type="text" class="cajaGeneral cajaSoloLectura" id="saldo-adelanto" readonly style="width: 100px;text-align: right;">
                            &nbsp;
                            <label><input type="checkbox" id="usa-adelanto" name="adelanto" <?php if($usa_adelanto == 1) echo "checked" ?>> Usar adelanto</label>
                        </span>
                    </td>
                    <td colspan="2">Vendedor: &nbsp;&nbsp;
                        <select  class="cajaGeneral" id="cmbVendedor" name="cmbVendedor">
                            <?=$cboVendedor?>
                        </select>
                    </td>
                </tr>

                <tr>
                    <td>Estado</td>
                    <td colspan="6">
                        <?php
                            $disabled = ( isset($produccion) && $produccion != '' && $produccion > 0 ) ? "" : "disabled";
                            $Clase = ( isset($produccion) && $produccion != '' && $produccion > 0 ) ? "class='cajaGeneral'" : "class='cajaGeneral cajaSoloLectura'" ; ?>

                                <select <?=$Clase;?> id="estatus" name="estatus">
                                    <option value="3" <?=(!isset($flagTerminado) || $flagTerminado == '') ? 'selected' : '';?>>EN ESPERA</option>
                                    <option value="2" <?=($flagTerminado != '' && $flagTerminado == '2') ? 'selected' : '';?>>EN PROCESO</option>
                                    <option value="1" <?=($flagTerminado != '' && $flagTerminado == '1') ? 'selected' : '';?> <?=$disabled;?>>TERMINADO</option>
                                </select>
                    </td>
                </tr>
                <?php } else { ?>
                    <tr>                   
                        <td>Importacion*</td>
                        <td> <select name="importacion" id="importacion" class="comboGrande" style="width:150px;">
                            <?php echo $cboimportacion;?>
                        </select></td>
                        <td colspan="2" style="text-align: right;">Descuento %</td>
                        <td colspan="2">
                            <input type="text" class="cajaMinima" id="descuento" name="descuento" size="2" maxlength="2" value="<?php echo $descuento; ?>" onKeyPress="return numbersonly(this,event,'.');" onblur="calcular_totales_tempdetalle();">
                            &nbsp;
                            <span style="display: <?php if(!$usa_adelanto == 1) echo 'none' ?>;" id="box-adelantos">
                                Adelanto : 
                                &nbsp;
                                <input type="hidden" class="cajaGeneral cajaSoloLectura" id="proyecto-adelanto" readonly style="width: 100px;text-align: right;">
                                <input type="text" class="cajaGeneral cajaSoloLectura" id="saldo-adelanto" readonly style="width: 100px;text-align: right;">
                                &nbsp;
                                <label><input type="checkbox" id="usa-adelanto" name="adelanto" <?php if($usa_adelanto == 1) echo "checked" ?>> Usar adelanto</label>
                            </span>
                        </td>
                    </tr>
                <?php } ?>
            </table>
        </div>
        <div id="frmBusqueda" <?=$hidden;?> class="box-add-product" style="text-align: right;">
            <a href="#" id="modalStock" style="color:#ffffff;" class="btn btn-primary">Insumos</a>
            <a href="#" id="addItems" name="addItems" style="color:#ffffff;" class="btn btn-primary" data-toggle="modal" data-target=".bd-example-modal-lg" onclick="limpiar_campos_modal(); ">Agregar Items</a>
        </div>
        
       <!-- LISTADO DE GUIAS ASOCIADAS  -->
        
       <!-- FIN DE LISTADO DE GUIAS ASOCIADAS --> 
       <!-- TABLA DETALLE DE TEMPORAL -->
<?php $this->load->view('maestros/temporal_subdetalles_second'); ?>
       <!-- FIN DE TABLA TEMPORAL DETALLE -->

<div id="frmBusqueda3">
            <table width="100%" border="0" align="right" cellpadding=3 cellspacing=0 class="fuente8">
                <tr>
                    <td width="100%" rowspan="5" align="left">
                        <table width="100%" border="0" align="right" cellpadding=3 cellspacing=0 class="fuente8">
                            <tr style="display: none">
                                <td width="14%" height="30">Modo de impresión</td>
                                <td width="50%">
                                    <select name="modo_impresion" <?php if ($tipo_docu == 'B' || $tipo_docu == 'N') echo 'disabled="disabled"'; ?> id="modo_impresion" class="comboGrande" style="width:307px">
                                        <option <?php if ($modo_impresion == '1') echo 'selected="selected"'; ?> value="1">LOS PRECIOS DE LOS PRODUCTOS DEBEN INCLUIR IGV</option>
                                        <option <?php if ($modo_impresion == '2') echo 'selected="selected"'; ?> value="2">LOS PRECIOS DE LOS PRODUCTOS NO DEBEN INCLUIR IGV
                                        </option>
                                    </select>
                                    <input hidden class="cajaGeneral" name="docurefe_codigo" type="text" id="docurefe_codigo" size="14" maxlength="26" value="<?php echo $docurefe_codigo; ?>"/>
                                </td>
                                <td width="7%" style="display: none;">Estado</td>
                                <td style="display: none;">
                                     <input type="hidden" name="estado" id="estado"  value="<?php echo $estado; ?>" />
                                </td>
                            </tr>

                            <tr>
                                <td colspan="4"></td>
                            </tr>

                            <tr style="display: none">
                                <td>Forma de Pago: </td>
                                <td>
                                    <select name="forma_pago" id="forma_pago" class="comboMedio" onchange="cambioFormaPago(event)"><?php echo $cboFormaPago; ?></select></td>
                                <td>
                                    <span class="btn btn-primary" id="btn-cuotas" onclick="$('#modal-cuotas').modal({backdrop : 'static'})" style="display: none;">Cuotas (<span id="cuotas-count">0</span>)</span>
                                </td>
                                <td><input id="cuotas-check" type="checkbox" name="cuotas" hidden=""></td>
                            </tr>

                            <tr>
                                <td colspan="8">Observación
                                    <textarea id="observacion" name="observacion" class="cajaTextArea" style="width:97%; height:70px;"><?php echo $observacion; ?></textarea>
                                </td>
                            </tr>
                        </table>
                    </td>

                    <td width="10%" style="display: none">Sub-total</td> <?php
                    if ($tipo_docu != 'B' && $tipo_docu != 'N') { ?>
                        <td style="display: none" width="10%" align="right">
                            <div align="right">Precio &nbsp;
                                <input class="cajaTotales" name="preciototal" type="text" id="preciototal" size="12" align="right" <?php
                                if ($tipo_oper == 'V') {
                                    echo 'readonly="readonly"';
                                } ?> value="<?php echo str_replace(",", "", number_format(round($preciototal, 2), 2)); ?>" onKeyPress="return numbersonly(this,event,'.');"></div>
                        </td> <?php
                    }
                    else { ?>

                        <td style="display: none" width="10%" align="right">
                            <div align="right"><input class="cajaTotales" name="preciototal" type="text" id="preciototal" size="12" align="right" <?php
                                if ($tipo_oper == 'V') {
                                    echo 'readonly="readonly"';
                                } ?> value="<?php echo str_replace(",", "", number_format(round($preciototal, 2), 2)); ?>" onKeyPress="return numbersonly(this,event,'.');">
                            </div>
                        </td> <?php
                    } ?>
                </tr> <?php
                if ($tipo_oper == 'C') { ?>
                    <tr style="display: none">
                        <td class="busqueda">Descto %</td>
                        <td align="right" width="10%"><input type="text" onchange="descuento_porcentaje()" name="porcentaje" id="porcentaje" class="cajaTotales" value="0" <?php
                            if ($tipo_oper == 'V') {
                                echo 'readonly="readonly"';
                            } ?>  onKeyPress="return numbersonly(this,event,'.');"></td>
                    </tr> <?php
                } ?>
                <tr style="display: none">
                    <td class="busqueda">Descuento</td> <?php
                    if ($tipo_docu != 'B' && $tipo_docu != 'N') { ?>
                        <td align="right">
                            <div align="right"><input class="cajaTotales" name="descuentotal" type="text" id="descuentotal" readonly="" size="12" align="right" value="<?php echo str_replace(",", "", number_format(round($descuentotal, 2), 2)); ?>"></div>
                        </td> <?php
                    }
                    else { ?>
                        <td align="right">
                        <div align="right"><input class="cajaTotales" name="descuentotal_conigv" type="text" readonly="" id="descuentotal_conigv" size="12" align="right" value="<?php echo str_replace(",", "", number_format(round($descuentotal_conigv, 2), 2)); ?>"></div>
                        </td> <?php
                    } ?>
                </tr>
                
                <tr style="display: none">
                    <td class="busqueda">IGV</td>
                    <td align="right">
                        <div align="right"><input class="cajaTotales" name="igvtotal" type="text" id="igvtotal" size="12" align="right" <?php
                            if ($tipo_oper == 'V') {
                                echo 'readonly="readonly"';
                            }
                            ?> value="<?php echo str_replace(",", "", number_format(round($igvtotal, 2), 2)); ?>"/></div>
                    </td>
                </tr>
                <tr style="display: none">
                    <td class="busqueda">Precio Total</td>
                    <td align="right">
                        <div align="right"><input class="cajaTotales" name="importetotal" type="text" id="importetotal" size="12" align="right" <?php
                            if ($tipo_oper == 'V') {
                                echo 'readonly="readonly"';
                            } ?> value="<?php echo str_replace(",", "", number_format(round($importetotal, 2), 2)); ?>" onKeyPress="return numbersonly(this,event,'.');"/></div>
                    </td>
                </tr>
            </table>
        </div>

        <br/>

        <div id="botonBusqueda2" style="padding-top:20px;">
            <img id="loading" src="<?php echo base_url(); ?>public/images/icons/loading.gif" style="visibility: hidden"/>
            <?php if($estado != 0): ?>
            <a href="javascript:;" id="imgGuardarProduccion"><img src="<?php echo base_url(); ?>public/images/icons/botonaceptar.jpg" width="85" height="22" class="imgBoton"></a>
            <?php endif; ?>
            <a href="javascript:;" id="limpiarProduccion"><img src="<?php echo base_url(); ?>public/images/icons/botonlimpiar.jpg" width="69" height="22" class="imgBoton"></a>
            <a href="javascript:;" id="cancelarProduccion"><img src="<?php echo base_url(); ?>public/images/icons/botoncancelar.jpg" width="85" height="22" class="imgBoton"></a>
            <input type="hidden" name="salir" id="salir" value="0"/>
            <?php echo $oculto ?>

        </div>

    </div>
<style type="text/css">
    #popup {
        left: 0;
        position: absolute;
        top: 0;
        width: 100%;
        z-index: 1001;
    }

    .content-popup {
        margin:0px auto;
        margin-top:150px;
        position:relative;
        padding:10px;
        width:300px;
        min-height:150px;
        border-radius:4px;
        background-color:#FFFFFF;
        box-shadow: 0 2px 5px #666666;
    }

    .content-popup h2 {
        color:#48484B;
        border-bottom: 1px solid #48484B;
        margin-top: 0;
        padding-bottom: 4px;
    }

    .popup-overlay {
        left: 0;
        position: absolute;
        top: 0;
        width: 100%;
        z-index: 999;
        display:none;
        background-color: #777777;
        cursor: pointer;
        opacity: 0.7;
    }

    .close {
        position: absolute;
        right: 15px;
    }
    #btnInventario{
        size: 20px;
    width: 200px;
    height: 50px;
        border-radius: 33px 33px 33px 33px;
    -moz-border-radius: 33px 33px 33px 33px;
    -webkit-border-radius: 33px 33px 33px 33px;
    border: 0px solid #000000;
    background-color:rgba(199, 255, 206, 1);

    }

    .gray{
        background: #E9E9E9;
    }
</style>

</form>


<a id="linkVerImpresion" href="#ventana"></a>
<div id="ventana" style="display: none; width: 350px">
    <div id="imprimir" style="padding:20px; text-align: center">
        <span style="font-weight: bold;">
          <?php if ($tipo_docu == 'PR') echo 'PRODUCCIÓN'; else echo 'PRODUCCIÓN'; ?>
      </span>
      <div style="padding-left: 100px" >
          <input type="text" name="ser_imp" id="ser_imp" readonly="readonly" style="border: 0px; font: bold 10pt helvetica;" value="fsd" class="cajaMinima" maxlength="3">
          - <input type="text" name="num_imp" id="num_imp" readonly="readonly" style="border: 0px; font: bold 10pt helvetica;" value="lknmlk" class="cajaMedia" maxlength="10">
      </div>  <br/>
      <a href="javascript:;" id="imprimirPedido"><img src="<?php echo base_url(); ?>public/images/icons/impresora.jpg" class="imgBoton" alt="Imprimir"></a>
      <br/> <br/>
      <a href="javascript:;" id="cancelarImprimirPedido"><img src="<?php echo base_url(); ?>public/images/icons/botoncancelar.jpg" width="85" height="22" class="imgBoton"></a>
  </div>
</div>
<?php $this->load->view('maestros/temporal_detalles_second'); ?>

</body>

<div>
    <div class="w3-container" style="font-family: Verdana, sans-serif; font-size: 9pt">
        <div class="sinStock w3-modal">
            <div class="w3-modal-content w3-animate-zoom" style="width: 85%">
                <header class="w3-container w3-teal" style="background-color: orange; opacity: 0.7"> 
                    <span class="sinStockClose w3-button w3-display-topright">&times;</span>
                    <h2 style="text-align: center;">Insumos sin stock suficiente</h2>
                </header>
                <div class="w3-container">
                    <table width="100%" border="0" style="text-align: center;">
                        <tr class="gray">
                            <th rowspan="2" style="text-align: center">Articulo</th>
                            <th rowspan="2" style="text-align: center">Cantidad a producir</th>
                            <th colspan="5" style="text-align: center">Insumos agregados a la receta</th>
                        </tr>
                        <tr class="gray">
                            <th style="width: 10em; text-align: center">Código</th>
                            <th style="width: 30em; text-align: center">Insumo</th>
                            <th style="width: 10em; text-align: center">Cantidad de receta</th>
                            <th style="width: 10em; text-align: center">Total requerido</th>
                            <th style="width: 10em; text-align: center">En stock</th>
                        </tr><?php
                            foreach ($insumos as $indice => $colG) { ?>
                                <tr <?=($indice % 2 == 0) ? '' : 'class="gray"';?>>
                                    <td style="text-align: left"><?=$colG["articulo"];?></td>
                                    <td><?=$colG["produccion"];?></td>
                                    <td colspan="5"> <?php
                                        if ($colG["insumos"] == NULL){ ?>
                                            SIN RECETA ASOCIADA <?php
                                        }
                                        else{ ?>
                                            <table border="0"> <?php
                                                foreach ($colG["insumos"] as $row => $val) { ?>
                                                    <tr>
                                                        <td style="width: 10em;"><?=$val->PROD_CodigoUsuario;?></td>
                                                        <td style="width: 30em; text-align: left;"><?=$val->nombre_producto;?></td>
                                                        <td style="width: 10em;"><?=$val->RECDET_Cantidad;?></td>
                                                        <td style="width: 10em;"><?=$colG["produccion"] * $val->RECDET_Cantidad;?></td>
                                                        <td style="width: 10em;"><?=$val->stock;?></td>
                                                    </tr><?php
                                                } ?>
                                            </table> <?php
                                        } ?>
                                    </td>
                                </tr><?php
                            } ?>
                        <tbody id="sinStock"></tbody>
                    </table>
                </div>
                <br>
            </div>
        </div>
    </div>
</div>

</html>

<script type="text/javascript">
    $("#modalStock").click(function(){
        $(".sinStock").show();
    });

    $(".sinStockClose").click(function(){
        $(".sinStock").hide();
    });

</script>