<?php
$nombre_persona = $this->session->userdata('nombre_persona');
$persona = $this->session->userdata('persona');
$usuario = $this->session->userdata('usuario');
$url = base_url() . "index.php";
if (empty($persona))
    header("location:$url");
    
?>
<html>
<head>
   
    <meta charset="utf-8">
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js" integrity="sha512-bLT0Qm9VnAYZDflyKcBaQ2gg0hSYNQrJ8RilYldYQ1FxQYoCLtUjuuRuZo+fjqhx/qtq/1itJ0C2ejDxltZVFg==" crossorigin="anonymous"></script>

    <!--script type="text/javascript" src="< ?php echo base_url(); ?>public/js/jquery-ui-1.8.17.custom.min.js?=< ?=JS;?>"></script-->
    <script type="text/javascript" src="<?php echo base_url(); ?>public/js/funciones.js?=<?=JS;?>"></script>
    <script src="<?php echo base_url(); ?>public/js/jquery.columns.min.js?=<?=JS;?>"></script>

    <!-- Latest compiled and minified JavaScript -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js" integrity="sha384-aJ21OjlMXNL5UyIl/XNwTMqvzeRMZH2w8c5cRVpzpU8Y5bApTppSuUkhZXN0VxHd" crossorigin="anonymous"></script>    
    
    <!--<script type="text/javascript" src="<?php echo base_url(); ?>public/js/fancybox/jquery.mousewheel-3.0.4.pack.js?=<?=JS;?>"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>public/js/fancybox/jquery.fancybox-1.3.4.pack.js?=<?=JS;?>"></script>
    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>public/js/fancybox/jquery.fancybox-1.3.4.css?=<?=CSS;?>" media="screen"/>-->

    <link href="<?=base_url();?>public/js/fancybox/dist/jquery.fancybox.css?=<?=CSS;?>" rel="stylesheet">
    <script src="<?=base_url();?>public/js/fancybox/dist/jquery.fancybox.js?=<?=JS;?>"></script>

    <!--script src="<?php echo base_url(); ?>resources/assets/bootstrap/js/bootstrap.min.js?=< ?=JS;?>"></script-->
    <!--script src="< ?php echo base_url(); ?>resources/assets/bootstrap/js/bootstrap.js?=< ?=JS;?>"></script-->

    <!--link href="< ?php echo base_url(); ?>resources/assets/bootstrap/css/bootstrap.min.css?=< ?=CSS;?>" rel="stylesheet"-->
    <!--link href="< ?php echo base_url(); ?>resources/assets/bootstrap/css/bootstrap-theme.css?=< ?=CSS;?>" rel="stylesheet"-->
    
<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css" integrity="sha384-HSMxcRTRxnN+Bdg0JdbxYKrThecOKuH5zCYotlSAcp1+c8xmyTe9GYg1l9a69psu" crossorigin="anonymous">

<!-- Optional theme -->
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap-theme.min.css" integrity="sha384-6pzBo3FDv/PJ8r2KRkGHifhEocL+1X2rVCTTkUfGk7/0pbek5mMa1upzvWbrUbOZ" crossorigin="anonymous">
    
    <!--script type="text/javascript" src="< ?php echo base_url(); ?>public/js/ventas/notacredito.js?=< ?=JS;?>"></script-->

    <script type="text/javascript">
        $(document).ready(function () { <?php
            if ($tipo_oper == 'V'){
                switch ($tipo_docu) {
                    case 'F': ?>
                        setLimite(<?php echo VENTAS_FACTURA; ?>); <?php
                        break;
                    case 'B': ?>
                        setLimite(<?php echo VENTAS_BOLETA; ?>); <?php
                        break;
                    case 'N': ?>
                        setLimite(<?php echo VENTAS_COMPROBANTE; ?>); <?php
                        break;
                    default:
                        break;
                }
            }
            else
                if ($tipo_oper == 'C') {
                    switch ($tipo_docu) {
                        case 'F': ?>
                            setLimite(<?php echo COMPRAS_FACTURA; ?>); <?php
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
            document.getElementById("buscar_producto").readOnly = true;
            $("#idDivAgregarProducto").hide(200);
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
            
            if ($('#tdcDolar').val() == '') {
                alert("Antes de registrar comprobantes debe ingresar Tipo de Cambio");
                top.location = "<?php echo base_url(); ?>index.php/index/inicio";
            }

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

            $(".verDocuRefe").click(function(){
                tipoMoneda = $("#moneda").val();;
                almacen = $("#almacen").val();
                
                if (tipo_oper == 'V') {
                    if ($('#cliente').val() == '') {
                        alert('Debe seleccionar el cliente.');
                        $('#nombre_cliente').focus();
                        return false;
                    } else {
                        
                        if ($(".verDocuRefe:checked").val() == 'G')
                            baseurl = base_url + 'index.php/almacen/guiarem/ventana_muestra_guiarem/' + tipo_oper + '/' + $('#cliente').val() + '/SELECT_HEADER/F/' + almacen + '/G/'+tipoMoneda;
                        else if ($('.verDocuRefe:checked').val() == 'P')
                            baseurl = base_url + 'index.php/ventas/presupuesto/ventana_muestra_presupuestoCom/' + tipo_oper + '/' + $('#cliente').val() + '/SELECT_HEADER/<?php echo $tipo_docu; ?>/' + almacen + '/P';
                        else if ($('.verDocuRefe:checked').val() == 'O')
                            baseurl = base_url + 'index.php/compras/ocompra/ventana_muestra_ocompraCom/' + tipo_oper + '/' + $('#cliente').val() + '/SELECT_HEADER/<?php echo $tipo_docu; ?>/' + almacen + '/O';
                        else if ($('.verDocuRefe:checked').val() == 'R')
                            baseurl = base_url + 'index.php/ventas/comprobante/ventana_muestra_recurrentes/' + tipo_oper + '/' + $('#cliente').val() + '/SELECT_HEADER/<?php echo $tipo_docu; ?>/' + almacen + '/R';
                        else if ($('.verDocuRefe:checked').val() == 'F')
                            baseurl = base_url + 'index.php/ventas/notacredito/ventana_muestra_notadecredito/' + tipo_oper + '/' + $('#cliente').val() + '/SELECT_HEADER/F/' + almacen + '/F';
                        //alert(baseurl);
                        $('.verDocuRefe:checked').attr('href', baseurl);
                    }
                }
                else {

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
                        else if ($('.verDocuRefe::checked').val() == 'F')
                            baseurl = base_url + 'index.php/ventas/notacredito/ventana_muestra_notadecredito/' + tipo_oper + '/' + $('#proveedor').val() + '/SELECT_HEADER/F/' + almacen + '/F';
                        //alert(baseurl);

                        $('.verDocuRefe::checked').attr('href', baseurl);
                    }
                }
            });

        });

    $(function () {
        // BUSQUEDA POR RAZON SOCIAL O CODIGO
        $("#nombre_cliente").autocomplete({
                source: function (request, response) {
                    $.ajax({
                        url: "<?php echo base_url(); ?>index.php/empresa/cliente/autocomplete/",
                        type: "POST",
                        data: {term: $("#nombre_cliente").val()},
                        dataType: "json",
                        success: function (data) {
                            response(data);
                        }
                    });
                },

                select: function (event, ui) {
                    $("#nombre_cliente").val(ui.item.nombre);
                    $("#buscar_cliente").val(ui.item.ruc);
                    $("#cliente").val(ui.item.codigo);
                    $("#ruc_cliente").val(ui.item.ruc);
                    $("#codigoEmpresa").val(ui.item.codigoEmpresa);
                    $("#TipCli").val(ui.item.TIPCLIP_Codigo);
                    $("#cboVendedor > option[value="+ ui.item.vendedor +"]").attr("selected",true) // Selecciona el vendedor asociado al cliente - 

                    if ( ui.item.contactos != null ){
                        var size = ui.item.contactos.length;
                        $('#contacto option').remove();

                        for (x = 0; x < size; x++){
                            $('#contacto').append("<option value='"+ui.item.contactos[x].PERSP_Codigo+"'>"+ui.item.contactos[x].PERSC_Nombre+"</option>");
                        }
                    }
                    get_obra(ui.item.codigo);
                },
                minLength: 2
        });

        // BUSQUEDA POR RUC
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
                    $("#buscar_cliente").val(ui.item.ruc);
                    $("#cliente").val(ui.item.codigo);
                    $("#ruc_cliente").val(ui.item.ruc);
                    $("#codigoEmpresa").val(ui.item.codigoEmpresa);
                    $("#TipCli").val(ui.item.TIPCLIP_Codigo); // Codigo del cliente para el precio del producto - 
                    $("#cboVendedor > option[value="+ ui.item.vendedor +"]").attr("selected",true) // Selecciona el vendedor asociado al cliente - 

                    if ( ui.item.contactos != null ){
                        var size = ui.item.contactos.length;
                        $('#contacto option').remove();

                        for (x = 0; x < size; x++){
                            $('#contacto').append("<option value='"+ui.item.contactos[x].PERSP_Codigo+"'>"+ui.item.contactos[x].PERSC_Nombre+"</option>");
                        }
                    }
                    get_obra(ui.item.codigo);
                    $("#addItems").click();
                },
                minLength: 2
        });
        
        $("#buscar_cliente").change(function(){
                if ($("#buscar_cliente").val().length == 0)
                    $(".input-group-btn").css("opacity",0);
        });

        // BUSQUEDA POR RAZON SOCIAL PROVEEDOR
        $("#nombre_proveedor").autocomplete({
                source: function (request, response) {
                    $.ajax({
                        url: "<?php echo base_url(); ?>index.php/empresa/proveedor/autocomplete/",
                        type: "POST",
                        data: { term: $("#nombre_proveedor").val() },
                        dataType: "json",
                        success: function (data) {
                            response(data);
                        }
                    });
                },
                select: function (event, ui) {
                    $("#buscar_proveedor").val(ui.item.ruc);
                    $("#nombre_proveedor").val(ui.item.nombre);
                    $("#proveedor").val(ui.item.codigo);
                    $("#ruc_proveedor").val(ui.item.ruc);
                    $("#codigoEmpresa").val(ui.item.codigoEmpresa);
                },
                minLength: 2
        });

        // BUSQUEDA POR RUC PROVEEDOR
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
                    $("#buscar_proveedor").val(ui.item.ruc);
                    $("#nombre_proveedor").val(ui.item.nombre);
                    $("#proveedor").val(ui.item.codigo);
                    $("#ruc_proveedor").val(ui.item.ruc);
                    $("#codigoEmpresa").val(ui.item.codigoEmpresa);

                    $("#addItems").click();
                },
                minLength:2 
        });

        $('#close').click(function(){
            $('#popup').fadeOut('slow');
            $('.popup-overlay').fadeOut('slow');
            return false;
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
        /*--------------------------------------------------*/

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

        function seleccionarOdenCompra(oCompra, serie, numero, valor)
        {
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

        function seleccionar_guiarem(guia, serieguia, numeroguia) {
            //realizado=agregar_todo(guia);
            realizado = verificar_agregar();
            obtener_comprobantes_temproductos(guia,'guiarem');

            if(realizado!=false){
                $("#serieguiaverPre").hide(200);
                $("#serieguiaverOC").hide(200);
                $("#serieguiaverRecu").hide(200);
                $('#ordencompra').val('');
            }

            codigoPresupuesto=$("#presupuesto_codigo").val();
            if(codigoPresupuesto!="" && codigoPresupuesto!=0){
                    modificarTipoSeleccionPrersupuesto(codigoPresupuesto,0);
            }
            $("#presupuesto_codigo").val("");
            
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
            serienumero = "N de Comprobante: <br>" + serieguia + " - " + numeroguia;
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
                        alert('error Tipo de cambio en esta fecha no ingresada');
                        $('#fecha').val('<?php echo date('d/m/Y');?>');
                        tdc_cambiar();
                    } else {
                        $('#tdc').val(data);
                    }
                }
            });
        }
        // End -->

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

<input type="hidden" name="codigoguia" id="codigoguia" value="<?php echo $guia; ?>"/>

<?php

//echo date("Y-m-d H:i:s");
// stylo para ocultar botones combos, etc

$style = "";
if (FORMATO_IMPRESION == 8) {
    $style = "display:none;";

}

?>

<!-- Inicio -->
<input value='<?php echo $compania; ?>' name="compania" type="hidden" id="compania"/>

<input type="hidden" name="idProyecto" id="id-proyecto" value="<?php echo $id_proyecto ?>">

<div id="VentanaTransparente" style="display:none;">
    <div class="overlay_absolute"></div>
    <div id="cargador" style="z-index:2000">
        <table width="100%" height="100%" border="0" class="fuente8">
            <tr valign="middle">
                <td> Por Favor Espere</td>
                <td>
                    <img src="<?php echo base_url(); ?>public/images/icons/cargando.gif?=<?=IMG;?>" border="0" title="CARGANDO"/>
                    <a href="#" id="hider2"></a>
                </td>
            </tr>
        </table>
    </div>
</div>

<!-- Fin -->

<form id="<?php echo $formulario; ?>" method="post" action="<?php echo $url_action; ?>">
<div id="popup" style="display: none;">
    <div class="content-popup">
        <div class="close">
        <a href="#" id="close">
        <img src="<?=base_url()?>public/images/icons/delete.gif?=<?=IMG;?>"/></a></div>
        <div>
           <h2>Falta Ingresar inventario</h2>
           <div id="contendio">
           </div>
           <a onclick="ejecutarModal()" target="_blank" href="<?=base_url()?>index.php/almacen/inventario/listar" id="btnInventario">IR A INGRESAR INVENTARIO </a>
           
        </div>
    </div>
</div>
    <div id="zonaContenido" align="center">
        <?php echo validation_errors("<div class='error'>", '</div>'); ?>
        <div id="tituloForm" class="header" style="height: 20px">
            <?php echo $titulo; ?>
            <?php
            if ($tipo_docu != 'N') {
                if ($codigo == '') {
                    ?>
                    <select id="cboTipoDocu" name="cboTipoDocu" class="comboMedio" style="visibility: hidden">
                        <option value="F" <?php if ($tipo_docu == 'F') echo 'selected="selected"'; ?>>FACTURA</option>
                        <option value="B" <?php if ($tipo_docu == 'B') echo 'selected="selected"'; ?>>BOLETA</option>
                    </select>
                <?php
                }else{ ?>
                    <input type="hidden" value="N" id="cboTipoDocu" name="cboTipoDocu"/>
               <?php }
            } else {
                ?>
                <input type="hidden" value="N" id="cboTipoDocu" name="cboTipoDocu"/>
            <?php } ?>
        </div>


        <div id="idDivGuiaRelacion" style="<?php echo (count($listaGuiaremAsociados)>0)?'':'display:none'; ?>">
        <div id="dialogSeriesAsociadas" title="Series Ingresadas">
          <div id="mostrarDetallesSeriesAsociadas"> 
           <div id="detallesSeriesAsociadas"></div>
          </div>
        </div>
        
        <!-- dialogo para mostrarse que sleccionar elñ almacen de un producto -->
        
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
            </tr>
            <?php if(count($listaGuiaremAsociados)>0){ 
                    foreach ($listaGuiaremAsociados as $indice=>$valorGuiarem){
                            $codigoGuiarem=$valorGuiarem->codigoGuiarem;
                            $serieGuiarem=$valorGuiarem->serie;
                            $numeroGuiarem=$valorGuiarem->numero;
                            $j=$indice+1;
                            $colorGuiar[$codigoGuiarem]="#".dechex(rand(0,10000000));
                            
                ?>
            
        <tr id="idTrDetalleRelacion_<?php echo $j; ?>"> 
         <td> 
         <a href="javascript:void(0);" onclick="deseleccionarGuiaremision(<?php echo $codigoGuiarem; ?>,<?php echo $j; ?>)" title="Deseleccionar Guia de remision"> 
         x 
         </a> 
         </td> 
         <td><?php echo $j; ?></td> 
         <td><?php echo $serieGuiarem; ?></td> 
         <td><?php echo $numeroGuiarem; ?></td> 
         <td><div style="width:10px;height:10px;background-color:<?php echo $colorGuiar[$codigoGuiarem] ?>; border:1px solid black"></div> 
            <input type="hidden" id="codigoGuiaremAsociada[<?php echo $j; ?>]"  name="codigoGuiaremAsociada[<?php echo $j; ?>]" value="<?php echo $codigoGuiarem; ?>" /> 
            <input type="hidden" id="accionAsociacionGuiarem[<?php echo $j; ?>]"  name="accionAsociacionGuiarem[<?php echo $j; ?>]" value="2" />
            <input type="hidden" id="proveedorRelacionGuiarem[<?php echo $j; ?>]"  name="proveedorRelacionGuiarem[<?php echo $j; ?>]" value="<?php echo $proveedor; ?>" />
         </td> 
        
         </tr> 
            <?php }} ?>
            </table>
        </div>
        <div id="frmBusqueda">
            <table class="fuente8" width="100%" cellspacing="0" cellpadding="5" border="0">
                <tr>
                    <!--iNDEX DE FACTURA Y BOTELA-->
                    <td width="8%">Número* </td>
                    <td width="60%" valign="middle">
                        <!--  <input type="hidden" id="numeroAutomatico" name="numeroAutomatico" value="<?php echo $numeroAutomatico; ?>"/>-->
                        <input type="hidden" id="tipo_oper" value="<?php echo $tipo_oper; ?>"/>
                        <input type="hidden" id="guiaremision" value="<?php echo $guiaremision; ?>"/>
                        <input type="hidden" id="posicionSeleccionadaSerie" value="" />
                        
                        <input class="cajaGeneral" name="serie" type="text" id="serie" size="3" maxlength="3" value="<?php echo $serie; ?>"/>&nbsp;
                        <input class="cajaGeneral" name="numero" type="text" id="numero" size="6" maxlength="6" value="<?php echo $numero; ?>"/>
                        <?php if ($tipo_oper == 'V') { ?>
                            <a href="javascript:;" id="linkVerSerieNum" <?php if ($codigo != '') echo 'style="display:none"' ?>>
                                <p class="boleta" style="display:none"><?php echo $serie_suger_b . '-'. $numero_suger_b ?></p>
                                <p class="factura" style="display:none"><?php echo $serie_suger_f . '-' . $numero_suger_f ?></p>
                                <p class="comprobante" style="display:none"><?php echo $serie_suger_f . '-' . $numero_suger_f ?></p>
                                <img src="<?php echo base_url(); ?>public/images/icons/flecha.png?=<?=IMG;?>" border="0" alt="Serie y nÃºmero sugerido" title="Serie y número sugerido"/>
                            </a>
                            
                            <input type="checkbox" name="numeroAutomatico"  id="numeroAutomatico" <?php echo($numeroAutomatico==1)?'checked=true':''; ?> value="1" title="SERIE-NUMERO AUTOMATICO SI SE SELECCIONA">
                        <?php } ?>
                        <label style="margin-left:20px;">IGV</label>
                        <input name="igv" type="text" class="cajaGeneral cajaSoloLectura" id="igv" size="2" maxlength="2" value="<?php echo $igv; ?>" onkeypress="return numbersonly(this,event,'.');" onblur="modifica_igv_total();" readonly="readonly"/> %
                        <!--input type="hidden" name="descuento" id="descuento" value=""/-->
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
                    <!-- <td width="8%" valign="middle">Presupuesto</td>-->
                    <td width="5%" valign="middle">
                        <?php if ($tipo_oper == 'V') { ?>
                            <!--<label for="P"><img src="<?php echo base_url() ?>public/images/icons/presupuesto.png?=<?=IMG;?>" class="imgBoton"/></label>-->
                        <?php } else { ?>
                            <!--<label for="P" style="cursor: pointer;" ><img src="<?php echo base_url() ?>public/images/icons/cotizacion.png?=<?=IMG;?>" class="imgBoton"/></label>-->
                        <?php } ?>
                        <input type="radio" name="referenciar" id="P" value="P" href="javascript:;" class="verDocuRefe" style="display:none;">
                        <div id="serieguiaverPre" name="serieguiaverPre" style="background-color: #cc7700; color:#fff; padding:5px;display:none"></div>
                        <input type="hidden" name="presupuesto_codigo" id="presupuesto_codigo" size="5" value="<?php echo $presupuesto_codigo; ?>"/>
                    </td>

                    <!--<td width="8%" >O. <?php //if ($tipo_oper == 'C') echo 'Compra'; else echo 'Venta'; ?> </td>-->
                    <td width="5%">
                        <?php if ($tipo_oper == 'V') { ?>
                            <!--<label for="O" style="cursor: pointer;"><img src="<?php echo base_url() ?>public/images/icons/oventa.png?=<?=IMG;?>" class="imgBoton"/></label>-->
                        <?php } else { ?>
                            <!--<label for="O" style="cursor: pointer;"><img src="<?php echo base_url() ?>public/images/icons/ocompra.png?=<?=IMG;?>" class="imgBoton"/></label>-->
                        <?php } ?>
                        <input type="radio" name="referenciar" id="O" value="O" href="javascript:;" class="verDocuRefe" style="display:none;">
                        <div id="serieguiaverOC" name="serieguiaverOC" style="background-color: #cc7700; color: #fff; padding:5px;display:none"></div>
                        <input type="hidden" name="ordencompra" id="ordencompra" size="5" value="<?php echo $ordencompra; ?>"/>
                    </td>
                    <td width="5%" valign="middle">Fecha</td>
                    <td width="30%" valign="middle"><input name="fecha" type="text" class="cajaGeneral cajaSoloLectura" id="fecha" value="<?php echo $hoy; ?>" size="10" maxlength="10" readonly="readonly" onchange="tdc_cambiar()"/>
                        <img height="16" border="0" width="16" id="Calendario1" name="Calendario1" src="<?php echo base_url(); ?>public/images/icons/calendario.png?=<?=IMG;?>"/>
                        <!--script type="text/javascript">
                            Calendar.setup({
                                inputField: "fecha",      // id del campo de texto
                                ifFormat: "%d/%m/%Y",       // formaClienteto de la fecha, cuando se escriba en el campo de texto
                                button: "Calendario1" // el id del botÃ³n que lanzarÃ¡ el calendario
                            });
                        </script-->
                    </td>
                </tr>
                <tr>
                    <td><?=($tipo_oper=="V") ? "Cliente *" : "Proveedor *";?></td>
                    <td valign="middle"> <?php
                        if ($tipo_oper == "V") { ?>
                            <input type="hidden" name="cliente" id="cliente" size="5" value="<?php echo $cliente ?>"/>
                            <input placeholder="ruc" name="buscar_cliente" type="text" class="cajaGeneral" id="buscar_cliente" size="10" value="<?php echo $ruc_cliente; ?>" title="Ingrese parte del nombre o el nro. de documento, luego presione ENTER."/>&nbsp;
                            <input type="hidden" name="ruc_cliente" class="cajaGeneral" id="ruc_cliente" size="10" maxlength="11" onblur="obtener_cliente();" value="<?php echo $ruc_cliente; ?>" onkeypress="return numbersonly(this,event,'.');"/>
                            <input placeholder="razon social" type="text" name="nombre_cliente" class="cajaGeneral" id="nombre_cliente" size="37" maxlength="50" value="<?php echo $nombre_cliente; ?>"/>

                            <!-- Add ingresar precio del cliente - -->
                            <?php if ( !isset($TIPCLIP_Codigo) ) $TIPCLIP_Codigo = ""; ?>
                            <input type="hidden" name="tempde_TipCli" id="TipCli" value="<?php echo $TIPCLIP_Codigo; ?>">
                            <!-- End add ingresar precio del cliente -  -->
                            <a href="<?php echo base_url(); ?>index.php/empresa/cliente/ventana_selecciona_cliente/" id="linkSelecCliente"></a> <?php
                        }
                        else { ?>
                            <input type="hidden" name="proveedor" id="proveedor" size="5" value="<?php echo $proveedor ?>"/>
                            <input name="buscar_proveedor" type="text" class="cajaGeneral" id="buscar_proveedor" size="10" placeholder="ruc" value="<?php echo $ruc_proveedor; ?>" title="Ingrese parte del nombre o el nro. de documento, luego presione ENTER."/>&nbsp;
                            <input type="hidden" name="ruc_proveedor" class="cajaGeneral" id="ruc_proveedor" size="10" maxlength="11" onblur="obtener_proveedor();" value="<?php echo $ruc_proveedor; ?>" placeholder="ruc" onkeypress="return numbersonly(this,event,'.');"/>
                            <input type="text" name="nombre_proveedor" class="cajaGeneral cajaSoloLectura" id="nombre_proveedor" size="25" maxlength="50" placeholder="razon social" value="<?php echo $nombre_proveedor; ?>"/>
                            <a href="<?php echo base_url(); ?>index.php/empresa/proveedor/ventana_selecciona_proveedor/" id="linkSelecProveedor"></a> <?php
                        } ?>
                    </td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <!--<td valign="middle">Guia remision *</td>-->
                    <td valign="middle">
                        <!--<label for="G" style="cursor: pointer;"><img src="<?php echo base_url() ?>public/images/icons/gremision.png?=<?=IMG;?>" class="imgBoton"/></label>-->
                        <input type="radio" name="referenciar" id="G" value="G" href="javascript:;" class="verDocuRefe" style="display:none;">
                        <input type="hidden" id="dRef"  name="dRef" value="<?php echo $dRef; ?>" >
                    </td>
                    <!--<td valign="middle">Doc Pago</td>-->
                    
                    <td valign="middle" style="position: relative" >
                        <label for="F" style="cursor: pointer">
                            <img src="<?php echo base_url() ?>public/images/icons/docpago.png?=<?=IMG;?>" class="imgBoton" id="docRelacionadoF" name="docRelacionadoF"/>
                        </label>
                        <input type="radio" name="referenciar" id="F" value="F" href="javascript:;" class="verDocuRefe" style="display:none;" data-fancybox data-type="iframe">
                        <input type="hidden" id="origenDocumento" value="<?=$ltCompIni;?>" name="origenDocumento"/>
                        <input type="hidden" id="guiaReferente" class="guiaReferente" value="<?=$idCompIni;?>" name="guiaReferente"/>
                        <input type="hidden" id="dRef" name="dRef">
                        <input type="hidden" name="idSerie" id="idSerie" value="<?=$numserref;?>"/>
                        <input type="hidden" name="idNumero" id="idNumero" value="<?=$numdocref;?>"/>
                            <span id="serieguiaverFlecha" class="flecha_izquierda2"></span>
                            <div id="serieguiaver" style="background: #cc7700; color:#fff; padding:5px;">Documento Relacionado:<br><?="$numserref - $numdocref";?></div>
                    </td>
                </tr>
                <tr style="display: none">
                    <td>Almacen*</td>
                    <td><?php echo $cboAlmacen; ?></td>
                    <td valign="middle">Moneda*</td>
                    <td valign="middle" id="idTdMoneda">
                        <select name="moneda" id="moneda" class="comboPequeno"
                                style="width:150px;"><?php echo $cboMoneda; ?>
                        </select>
                       <label id="textoMoneda"></label> 
                        
                        <?php if(count($listaGuiaremAsociados)>0){  ?> 
                        <script type="text/javascript">
                            $("#moneda").hide(200);
                            textoMoneda=$("#moneda option:selected").text();
                            $("#textoMoneda").html(textoMoneda);
                            $("#textoMoneda").show(200);
                        </script>
                        
                        <?php } ?>
                    </td>
                    <td colspan="2">
                        TDC Dolar : &nbsp;
                        <input name="tdcDolar" type="text" class="cajaGeneral cajaSoloLectura" style="width: 28px" id="tdcDolar" size="3" value="<?php echo $tdcDolar; ?>" onkeypress="return numbersonly(this,event,'.');" readonly="readonly"/>&nbsp;
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
                            if(codigo > 2)
                                $("#tdcEuro").focus();
                        });

                        $(document).ready(function () {
                            $("#moneda").trigger('change');
                        });

                        function AgregarRetencion(activado=false) {
                            const   butt = document.getElementById("addretencion");
                                    butt.classList.toggle("btn-primary");
                            var montoTotal = parseFloat($("#importetotal").val())    
                            cant = $("#cant-cuotas").val()
                            if($("#addretencion").hasClass("btn-primary")==false){
                                $("#addretencion").html('Quitar Retencion');
                                retenV= parseFloat((montoTotal*3)/100)
                                $("#CantReten").val(retenV)
                               
                                $( "#barrainf" ).append( `<p id='textretencion'>Recuerde que la retencion es de : ${retenV}</p>` );
                            }else{
                                $("#addretencion").html('Agregar Retencion');
                                $("#CantReten").val(0)
                                $("#textretencion").remove()
                                
                            }
                        }

                        descuentoPercent = <?php echo isset($descuento) ? $descuento : 0 ?>;
                    </script>
                </tr> <?php

                if ($tipo_oper == 'V'){ ?>
                    <tr style="display: none">
                        <td>Descuento</td>
                        <td colspan="5">
                            <input type="text" class="cajaMinima" id="descuento" name="descuento" size="2" maxlength="2" value="<?php echo $descuento; ?>" onKeyPress="return numbersonly(this,event,'.');" onblur="calcular_totales_tempdetalle();"> %
                            &nbsp;
                            <span style="display: <?php if(!$usa_adelanto == 1) echo 'none' ?>;" id="box-adelantos">
                                Adelanto: &nbsp;
                                <input type="hidden" class="cajaGeneral cajaSoloLectura" id="proyecto-adelanto" readonly style="width: 100px;text-align: right;">
                                <input type="text" class="cajaGeneral cajaSoloLectura" id="saldo-adelanto" readonly style="width: 100px;text-align: right;">
                                &nbsp;
                                <label><input type="checkbox" id="usa-adelanto" name="adelanto" <?php if($usa_adelanto == 1) echo "checked" ?>> Usar adelanto</label>
                            </span>
                        </td>
                    </tr> <?php
                }
                else { ?>
                    <tr>                   
                        <td>Importacion*</td>
                        <td> <select name="importacion" id="importacion" class="comboGrande" style="width:150px;">
                            <?php echo $cboimportacion;?>
                        </select></td>
                        <td>Descuento %</td>
                        <td colspan="3">
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
                    </tr> <?php
                } ?>
            </table>
            <script>
                $(function() {
                    $("#obra").change(function (event) {
                        var value = event.target.value;

                        //if(value != 0 && $.trim(value) != "" && $("#forma_pago").val() != 19) consultarAdelantos(value);

                    }).trigger('change');

                    $("#usa-adelanto").change(function (event) {
                        var isCheck = $(event.target).attr('checked');

                        var adelanto = $("#proyecto-adelanto").val();
                        var descuento = isCheck ? $("#descuentotal").val() : 0;

                        $("#saldo-adelanto").val(parseFloat(adelanto - descuento).format());

                        $("#descuento").val(isCheck ? descuentoPercent : 0).trigger("blur");
                    });
                });

                function consultarAdelantos($id) {
                    $.getJSON("<?php echo base_url() ?>index.php/maestros/proyecto/get_adelantos_saldo/"+$id+"/"+tipo_oper, {}, function(json, textStatus) {
                            if(textStatus == 'success' && json.porcentaje > 0) {
                                $("#box-adelantos").show();
                                
                                if("insertar" == "<?php echo $modo ?>") {
                                    $("#usa-adelanto").attr('checked', 'checked');
                                    descuentoPercent = json.porcentaje;
                                    $("#descuento").val(descuentoPercent);
                                }

                                $("#proyecto-adelanto").val(json.saldo_dolares.format(false));
                                //$("#usa-adelanto").trigger('change');
                                $("#descuento").trigger('blur');
                            }
                        });
                }
            </script>
        </div>
        <div id="frmBusqueda"  <?php echo $hidden; ?> class="box-add-product" style="text-align: right;" >
            <a href="#" style="color:#ffffff;" class="btn btn-primary" data-toggle="modal" data-target=".bd-example-modal-lg" onclick="limpiar_campos_modal(); ">Agregar Items</a></td>
        </div>
        
       <!-- LISTADO DE GUIAS ASOCIADAS  -->
        
       <!-- FIN DE LISTADO DE GUIAS ASOCIADAS --> 
       <!-- TABLA DETALLE DE TEMPORAL -->
<?php $this->load->view('maestros/temporal_subdetalles'); ?>
       <!-- FIN DE TABLA TEMPORAL DETALLE -->

<div id="frmBusqueda3">
            <table width="100%" border="0" align="right" cellpadding=3 cellspacing=0 class="fuente8">
                <tr>
                    <td width="80%" rowspan="9" align="left">
                        <table width="100%" border="0" align="right" cellpadding=3 cellspacing=0 class="fuente8" style="width: 736px;">
                            <tr style="display: none">
                                <td width="14%" height="30">Modo de impresión</td>
                                <td width="50%"><select name="modo_impresion" <?php if ($tipo_docu == 'B' || $tipo_docu == 'N') echo 'disabled="disabled"'; ?> id="modo_impresion" class="comboGrande" style="width:307px">
                                        <option <?php if ($modo_impresion == '1') echo 'selected="selected"'; ?>
                                            value="1">LOS PRECIOS DE LOS PRODUCTOS DEBEN INCLUIR IGV
                                        </option>
                                        <option <?php if ($modo_impresion == '2') echo 'selected="selected"'; ?>
                                            value="2">LOS PRECIOS DE LOS PRODUCTOS NO DEBEN INCLUIR IGV
                                        </option>
                                    </select>
                                    <input hidden class="cajaGeneral" name="docurefe_codigo" type="text" id="docurefe_codigo" size="14" maxlength="26" value="<?php echo $docurefe_codigo; ?>"/>
                                </td>
                            </tr>

                            <tr>
                                <td width="150px">Motivo de la nota de <?=($tipo_docu == 'C') ? "credito" : "debito";?></td>
                                    <!--
                                        *CREDITO
                                            1 = ANULACIÓN DE LA OPERACIÓN
                                            2 = ANULACIÓN POR ERROR EN EL RUC
                                            3 = CORRECCIÓN POR ERROR EN LA DESCRIPCIÓN
                                            4 = DESCUENTO GLOBAL
                                            5 = DESCUENTO POR ÍTEM
                                            6 = DEVOLUCIÓN TOTAL
                                            7 = DEVOLUCIÓN POR ÍTEM
                                            8 = BONIFICACIÓN
                                            9 = DISMINUCIÓN EN EL VALOR
                                        *DEBITO
                                            1 = INTERESES POR MORA
                                            2 = AUMENTO DE VALOR
                                            3 = PENALIDADES
                                    -->

                                <td colspan="3">
                                    <select name="motivoNota" id="motivoNota" class="comboGrande"> <?php
                                        if ($tipo_docu == 'C'){ ?>
                                            <option <?=($motivoNota == '1') ? 'selected="selected"' : '';?> value="1"> ANULACIÓN DE LA OPERACIÓN </option>
                                            <option <?=($motivoNota == '2') ? 'selected="selected"' : '';?> value="2"> ANULACIÓN POR ERROR EN EL RUC </option>
                                            <option <?=($motivoNota == '3') ? 'selected="selected"' : '';?> value="3"> CORRECCIÓN POR ERROR EN LA DESCRIPCIÓN </option>
                                            <option <?=($motivoNota == '4') ? 'selected="selected"' : '';?> value="4"> DESCUENTO GLOBAL </option>
                                            <option <?=($motivoNota == '5') ? 'selected="selected"' : '';?> value="5"> DESCUENTO POR ÍTEM </option>
                                            <option <?=($motivoNota == '6') ? 'selected="selected"' : '';?> value="6"> DEVOLUCIÓN TOTAL </option>
                                            <option <?=($motivoNota == '7') ? 'selected="selected"' : '';?> value="7"> DEVOLUCIÓN POR ÍTEM </option>
                                            <option <?=($motivoNota == '8') ? 'selected="selected"' : '';?> value="8"> BONIFICACIÓN </option>
                                            <option <?=($motivoNota == '9') ? 'selected="selected"' : '';?> value="9"> DISMINUCIÓN EN EL VALOR </option> <?php
                                        }
                                        else{ ?>
                                            <option <?=($motivoNota == '1') ? 'selected="selected"' : '';?> value="1"> INTERESES POR MORA </option>
                                            <option <?=($motivoNota == '2') ? 'selected="selected"' : '';?> value="2"> AUMENTO DE VALOR </option>
                                            <option <?=($motivoNota == '3') ? 'selected="selected"' : '';?> value="3"> PENALIDADES </option> <?php
                                        } ?>
                                    </select>
                                </td>
                            </tr>

                            <tr style="display: none">
                                <td>Forma de Pago </td>
                                <td colspan="3">
                                    <select name="forma_pago" id="forma_pago" class="comboMedio">
                                        <option value="1">EFECTIVO</option>
                                    </select>
                                    <!--<select name="forma_pago" id="forma_pago" class="comboMedio" onchange="cambioFormaPago(event)"><?php echo $cboFormaPago; ?></select>-->
                                </td>
                                <td style="display: none">
                                    <span class="btn btn-primary" id="btn-cuotas" onclick="$('#modal-cuotas').modal({backdrop : 'static'})" style="display: none;">Cuotas (<span id="cuotas-count">0</span>)</span>
                                </td>
                                <td style="display: none"><input id="cuotas-check" type="checkbox" name="cuotas" hidden=""></td>
                            </tr>
                            <tr>
                                <td>Observación</td>
                                <td colspan="3">
                                    <textarea id="observacion" name="observacion" class="cajaTextArea" style="width:97%; height:70px;"><?=$observacion;?></textarea>
                                </td>
                            </tr>
                        </table>
                    </td>

                    <tr>
                        <td class="busqueda">Descuento</td>
                        <td align="right">
                            <div align="right"><input class="cajaTotales" name="descuentotal" type="text" id="descuentotal" size="12" align="right" readonly="readonly" value="<?php echo round($descuentotal, 2); ?>"/></div>
                        </td>
                    </tr>
                    <tr>
                        <td class="busqueda">Exonerada</td>
                        <td align="right">
                            <div align="right"><input class="cajaTotales" name="exoneradototal" type="text" id="exoneradototal" size="12" align="right" readonly="readonly" value="<?=(isset($exoneradototal)) ? round($exoneradototal, 2) : '0';?>"/></div>
                        </td>
                    </tr>
                    <tr>
                        <td class="busqueda">Inafecta</td>
                        <td align="right">
                            <div align="right"><input class="cajaTotales" name="inafectototal" type="text" id="inafectototal" size="12" align="right" readonly="readonly" value="<?=(isset($inafectototal)) ? round($inafectototal, 2) : '0';?>"/></div>
                        </td>
                    </tr>
                    <tr>
                        <td class="busqueda">Gratuita</td>
                        <td align="right">
                            <div align="right"><input class="cajaTotales" name="gratuitatotal" type="text" id="gratuitatotal" size="12" align="right" readonly="readonly" value="<?=(isset($gratuitatotal)) ? round($gratuitatotal, 2) : '0';?>"/></div>
                        </td>
                    </tr>
                    <tr style="display: none"> <!--Important-->
                        <td>Sub-total</td>
                        <td width="10%" align="top">
                            <div align="right"><input class="cajaTotales" name="preciototal" type="text" id="preciototal" size="12" align="right" readonly="readonly" value="<?php echo round($preciototal, 2); ?>"/></div>
                        </td>
                    </tr>
                    <tr>
                        <td class="busqueda">Gravada</td>
                        <td align="right">
                            <div align="right"><input class="cajaTotales" name="gravadatotal" type="text" id="gravadatotal" size="12" align="right" readonly="readonly" value="<?=(isset($gravada)) ? round($gravada, 2) : '0';?>"/></div>
                        </td>
                    </tr>
                    <tr>
                        <td class="busqueda">IGV</td>
                        <td align="right">
                            <div align="right"><input class="cajaTotales" name="igvtotal" type="text" id="igvtotal" size="12" align="right" readonly="readonly" value="<?php echo round($igvtotal, 2); ?>"/></div>
                        </td>
                    </tr>
                    <tr>
                        <td class="busqueda">Impuesto a la Bolsa Plástica</td>
                        <td align="right">
                            <div align="right"><input class="cajaTotales" id="importeBolsa" name="importeBolsa" type="text" size="12" align="right" readonly="readonly" value="0"/></div>
                        </td>
                    </tr>
                    <tr>
                        <td class="busqueda">Importe Total</td>
                        <td align="right">
                            <div align="right"><input class="cajaTotales" name="importetotal" type="text" id="importetotal" size="12" align="right" readonly="readonly" value="<?php echo round($importetotal, 2); ?>"/></div>
                        </td>
                    </tr>
            </table>

        <!-- MODAL -->
        <div class="modal fade bootstrap" id="modal-cuotas" style="width: 50%" tabindex="-1" role="dialog">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                              <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            
                                <h3 class="modal-title">Cuotas</h3>
                              </div>
                              <div class="modal-body">
                            <?php if(($estado == 2 && isset($lista_cuotas))): ?>
                                <div align="right">
                                    <span class="btn btn-success btn-sm" onclick="addCuota()">+ Agregar</span>
                                </div>
                            <?php endif; ?>
                                <div <?php echo isset($lista_cuotas) ? 'hidden' : '' ?>>
                                <?php if($estado == 2): ?>
                                    N° Cuotas <input type="number" onchange="if(event.target.value >= 0) calculaCuotas(event.target.value)" id="cant-cuotas" value="<?php echo count($lista_cuotas) ?>">
                                <?php endif; ?>
                                <button type="button" id="addretencion" class="btn btn-primary" onclick="AgregarRetencion()">Agregar Retencion</button> 
                                </div>
                                <div>
                                    <table id="tbl-cuotas" width="100%" value="1" class="table">
                                        <thead>
                                            <tr>
                                                <th></th>
                                                <th align="center">N#</th>
                                                <th align="center">Fecha Inicio</th>
                                                <th align="center">Fecha Fin</th>
                                                <th align="center">Monto</th>
                                                <th align="center">Fisica</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        <?php
                                            $monto_cuotas = 0;
                                            if(isset($lista_cuotas) && count($lista_cuotas) > 0):
                                                foreach ($lista_cuotas as $cuota):
                                                $monto_cuotas += $cuota->CUOT_Monto;
                                        ?>
                                            <tr id="nro-cuota-<?php echo $cuota->CUOT_Numero ?>">
                                                <td>
                                                <?php if($estado == 2): ?>
                                                    <span class="btn btn-xs btn-danger btn-delete" data-numero-cuota="<?php echo $cuota->CUOT_Numero ?>" onclick="deleteCuota()">X</span>
                                                <?php endif; ?>
                                                </td>
                                                <td>
                                                    <input type="hidden" name="nroCuota[<?php echo $cuota->CUOT_Numero ?>]" class="cuota-numero" value="<?php echo $cuota->CUOT_Numero ?>" <?php echo $estado != 2 ? "readonly" : "" ?>>
                                                    <span class="cuota-n"><?php echo $cuota->CUOT_Numero ?></span>
                                                </td>
                                                <td>
                                                    <input type="date" name="fechaCuotaInicio[<?php echo $cuota->CUOT_Numero ?>]" class="cuota-fecha" value="<?php echo date_format(date_create($cuota->CUOT_FechaInicio), 'Y-m-d') ?>" <?php echo $estado != 2 ? "readonly" : "" ?>>
                                                </td>
                                                <td>
                                                    <input type="date" name="fechaCuota[<?php echo $cuota->CUOT_Numero ?>]" class="cuota-fecha" value="<?php echo date_format(date_create($cuota->CUOT_Fecha), 'Y-m-d') ?>" <?php echo $estado != 2 ? "readonly" : "" ?>>
                                                </td>
                                                <td><input type="text" name="montoCuota[<?php echo $cuota->CUOT_Numero ?>]" class="cuota-monto" value="<?php echo number_format($cuota->CUOT_Monto, 2) ?>" <?php echo $estado != 2 ? "readonly" : "" ?> class="monto" onchange="recalcularCuotas(<?php echo $cuota->CUOT_Numero ?>)">
                                                </td>
                                                <td>
                                                    <input type="checkbox" class="cuota-fisica" <?php echo $cuota->CUOT_FlagFisica ? 'checked' : '' ?> <?php echo $estado != 2 ? "disabled" : "" ?> name="cuotaFisica[<?php echo $cuota->CUOT_Numero ?>]">
                                                </td>
                                            </tr>
                                        <?php
                                                endforeach;
                                            endif;
                                        ?>
                                        </tbody>
                                    </table>
                                </div>
                              </div>
                            <?php if($estado == 2) : ?>
                                <div class="modal-footer" id="barrainf">
                              <input hidden id="CantReten" value="0" type="number"> 
                                Total cuotas : <span id="suma-cuotas"><?php echo number_format($monto_cuotas, 2) ?></span>
                                &nbsp;&nbsp;
                            <?php if(!isset($lista_cuotas)): ?>
                                <button type="button" class="btn btn-default" onclick="aceptarCancelarCuota()">Cancelar</button>
                            <?php endif; ?>
                                <button type="button" class="btn btn-primary" onclick="aceptarCancelarCuota(true)">Aceptar</button>
                              </div>
                            <?php endif; ?>
                            </div><!-- /.modal-content -->
                          </div><!-- /.modal-dialog -->
                        </div><!-- /.modal -->
        <!-- FIN MODAL -->
        </div>

        <br/>

        <div id="botonBusqueda2" style="padding-top:20px;">
            <img id="loading" src="<?php echo base_url(); ?>public/images/icons/loading.gif?=<?=IMG;?>" style="visibility: hidden"/>
            <a href="javascript:;" id="grabarComprobante"><img src="<?php echo base_url(); ?>public/images/icons/botonaceptar.jpg?=<?=IMG;?>" width="85" height="22" class="imgBoton"></a>
            <a href="javascript:;" id="limpiarComprobante"><img src="<?php echo base_url(); ?>public/images/icons/botonlimpiar.jpg?=<?=IMG;?>" width="69" height="22" class="imgBoton"></a>
            <a href="javascript:;" id="cancelarComprobante"><img src="<?php echo base_url(); ?>public/images/icons/botoncancelar.jpg?=<?=IMG;?>" width="85" height="22" class="imgBoton"></a>
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
</style>

</form>


<a id="linkVerImpresion" href="#ventana"></a>
<div id="ventana" style="display: none; width: 350px">
    <div id="imprimir" style="padding:20px; text-align: center">
        <span style="font-weight: bold;"><?=($tipo_docu == 'C') ? 'NOTA DE CREDITO' : 'NOTA DE DEBITO'; ?></span>
        <div style="padding-left: 100px" >
            <input type="text" name="ser_imp" id="ser_imp" readonly="readonly" style="border: 0px; font: bold 10pt helvetica;" value="fsd" class="cajaMinima" maxlength="3">- <input type="text" name="num_imp" id="num_imp" readonly="readonly" style="border: 0px; font: bold 10pt helvetica;" value="lknmlk" class="cajaMedia" maxlength="10">
        </div>  <br/>
        <a href="javascript:;" id="imprimirComprobante"><img src="<?php echo base_url(); ?>public/images/icons/impresora.jpg?=<?=IMG;?>" class="imgBoton" alt="Imprimir"></a>
        <br/> <br/>
        <a href="javascript:;" id="cancelarImprimirComprobante"><img src="<?php echo base_url(); ?>public/images/icons/botoncancelar.jpg?=<?=IMG;?>" width="85" height="22" class="imgBoton"></a>
    </div>
</div>
<?php $this->load->view('maestros/temporal_detalles'); ?>
</body>

</html>
<script>
    var totalAmountOrden = 0;
    $("#moneda").change(function () {
        var combo = $(this),
            codigo = combo.val();

        $("#tdcOpcional").css('display', codigo > 2 ? '' : 'none');

        if(codigo > 2) $("#tdcOpcional").focus();
    });

    $(document).ready(function () {
        $("#moneda").trigger('change');

        var codigoClienteProveedor = 0;

        if($("#cliente").length == 1) codigoClienteProveedor = $("#cliente").val();

        if($("#proveedor").length == 1) codigoClienteProveedor = $("#proveedor").val();

        if(codigoClienteProveedor != '') get_obra(codigoClienteProveedor);

        $(".tooltiped").tooltip();

        colors = <?php echo json_encode($colors) ?>;
    });

    function verificarPorcentaje(evt) {
        var value = evt.target.value.toLowerCase();

        if(/[0-9\.]{1,}(?=\%)/.test(value)) {

            if(tipo_oper == 'C') {
                var porcentaje = parseFloat(value.match(/[0-9\.]{1,}(?=\%)/));
                var puTag = document.getElementById("prodpu[0]");

                if(porcentaje > 100 || porcentaje < 1) {
                    alert("El porcentaje no es correcto");
                    evt.target.focus();
                }else{
                    puTag.value = (totalAmountOrden * (porcentaje / 100)).toFixed(2);
                    puTag.focus();
                    puTag.blur();
                }
            }
        }else {
            alert("Debe ingresar un porcentaje en la descripcion del adelanto.");
            evt.target.focus();
        }
    }
</script>