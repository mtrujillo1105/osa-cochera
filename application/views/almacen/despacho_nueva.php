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

    <script type="text/javascript" src="<?php echo base_url(); ?>public/js/calendario/responsive-calendar.js?=<?=JS;?>"></script>

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

                    if ($('.verDocuRefe::checked').val() == 'GT'){
                        baseurl = base_url + 'index.php/almacen/produccion/ventana_muestra_gtrans/V/0/SELECT_HEADER/F/0/GT';
                    }

                    if ($('.verDocuRefe::checked').val() == 'G') {
                        baseurl = base_url + 'index.php/almacen/produccion/ventana_muestra_guiarem/V/0/SELECT_HEADER/F/0/G/';
                    }
                     
                    $('.verDocuRefe::checked').attr('href', baseurl);
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

    function seleccionar_gtrans(guia, serie, numero, tipo, estabc) {
        n = document.getElementById('despachoDescripcionHTML').rows.length;
        j = n + 1;
        if (j % 2 == 0) {
            clase = "itemParTabla";
        } else {
            clase = "itemImparTabla";
        }


        fila = '<tr id="' + n + '" class="' + clase + '" >';
            fila += '<td width="5%"><div align="center">' + j + '</div></td>';

            fila += '<td width="10%"><div align="left"><input type="hidden" name="serieD[' + n + ']" id="serieD[' + n + ']" value="' + serie + '"/><span id="serie_span['+n+']">'+serie+'</span>';
            fila += '</div></td>';

            fila += '<td width="10%"><div align="left"><input type="hidden" name="numeroD[' + n + ']" id="numeroD[' + n + ']" value="' + numero + '"/><span id="serie_span['+n+']">'+numero+'</span>';
            fila += '</div></td>';

            fila += '<td width="10%"><div align="left"><span id="tipo_span['+n+']">GT</span>';
            fila += '</div></td>';

            fila += '<td width="65%"><div align="left"><input type="hidden" name="estabc[' + n + ']" id="estabc[' + n + ']" value=""/><span id="estabc_span['+n+']">'+estabc+'</span>';
            fila += '</div></td>';

            fila += '<td width="5%"><div style="text-align:center;"><a href="#" onclick="eliminar_guia(' + n + ');"><img src="'+base_url+'images/delete.png?=<?=IMG;?>" width="20" height="20"  border="0" tittle="eliminar"></a>';
            fila += '</div></td>';

            fila += '<input type="hidden" name="guia[' + n + ']" id="guia[' + n + ']" value="' + guia + '"/>';
            fila += '<input type="hidden" name="tipo[' + n + ']" id="tipo[' + n + ']" value="' + tipo + '"/>';

            fila += '<input type="hidden" value="n" name="detaccion[' + n + ']" id="detaccion[' + n + ']">';
            fila += '<input type="hidden" value="" name="detacodi[' + n + ']" id="detacodi[' + n + ']">';
        fila += '</tr>';
        $("#despachoDescripcionHTML").append(fila);
    }

    function seleccionar_guiaremD(guia, serie, numero, tipo, estabc) {
        n = document.getElementById('despachoDescripcionHTML').rows.length;
        j = n + 1;
        if (j % 2 == 0) {
            clase = "itemParTabla";
        } else {
            clase = "itemImparTabla";
        }


        fila = '<tr id="' + n + '" class="' + clase + '" >';
            fila += '<td width="5%"><div align="center">' + j + '</div></td>';

            fila += '<td width="10%"><div align="left"><input type="hidden" name="serieD[' + n + ']" id="serieD[' + n + ']" value="' + serie + '"/><span id="serie_span['+n+']">'+serie+'</span>';
            fila += '</div></td>';

            fila += '<td width="10%"><div align="left"><input type="hidden" name="numeroD[' + n + ']" id="numeroD[' + n + ']" value="' + numero + '"/><span id="serie_span['+n+']">'+numero+'</span>';
            fila += '</div></td>';

            fila += '<td width="10%"><div align="left"><span id="tipo_span['+n+']">GR</span>';
            fila += '</div></td>';

            fila += '<td width="65%"><div align="left"><input type="hidden" name="estabc[' + n + ']" id="estabc[' + n + ']" value=""/><span id="estabc_span['+n+']">'+estabc+'</span>';
            fila += '</div></td>';

            fila += '<td width="5%"><div style="text-align:center;"><a href="#" onclick="eliminar_guia(' + n + ');"><img src="'+base_url+'images/delete.png?=<?=IMG;?>" width="20" height="20"  border="0" tittle="eliminar"></a>';
            fila += '</div></td>';



            fila += '<input type="hidden" name="guia[' + n + ']" id="guia[' + n + ']" value="' + guia + '"/>';
            fila += '<input type="hidden" name="tipo[' + n + ']" id="tipo[' + n + ']" value="' + tipo + '"/>';

            fila += '<input type="hidden" value="n" name="detaccion[' + n + ']" id="detaccion[' + n + ']">';
            fila += '<input type="hidden" value="" name="detacodi[' + n + ']" id="detacodi[' + n + ']">';
        fila += '</tr>';
        $("#despachoDescripcionHTML").append(fila);
    }

    function eliminar_guia(fila){
        if(confirm('¿Esta seguro de eliminar esta guia?')){
            var d = "detacodi["+fila+"]";
            var e = "detaccion["+fila+"]";
            detalleId   = document.getElementById(d).value;            
            document.getElementById(e).value='e';
            $("#"+fila).css('display','none');
        }
        return false;
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
            <select id="cboTipoDocu" name="cboTipoDocu" class="comboMedio" hidden>
                <option value="DP">DESPACHO</option>
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
            <table class="fuente8" width="100%" cellspacing="0" cellpadding="5" border="1">
                <tr>
                    <td width="8%">Número*</td>
                    <td width="25%" valign="middle">
                        <input class="cajaGeneral" name="serie" type="text" id="serie" size="4" maxlength="4" value="<?=$serie_suger_f;?>"/>&nbsp;
                        <input class="cajaGeneral" name="numero" type="text" id="numero" size="6" maxlength="6" value="<?=$numero_suger_f;?>"/>
                    </td>
                    <td valign="middle">
                        <select id="estado" name="estado">
                            <option value="4" <?=($estado == 4) ? 'selected' : '';?>>Por despachar</option>
                            <option value="3" <?=($estado == 3) ? 'selected' : '';?>>Despachado</option>
                            <option value="2" <?=($estado == 2) ? 'selected' : '';?>>Transito</option>
                            <option value="1" <?=($estado == 1) ? 'selected' : '';?>>Recibido</option>
                        </select>
                    </td>
                    <td valign="middle" style="text-align: right">Fecha de Despacho</td>
                    <td valign="middle">
                        <input name="fecha" type="text" class="cajaGeneral cajaSoloLectura" id="fecha" value="<?php echo $fechaI; ?>" size="10" maxlength="10" readonly="readonly" onchange="tdc_cambiar()"/>
                        <img height="16" border="0" width="16" id="Calendario1" name="Calendario1" src="<?php echo base_url(); ?>public/images/icons/calendario.png"/>
                        <script type="text/javascript">
                            Calendar.setup({
                                inputField: "fecha",  // id del campo de texto
                                ifFormat: "%d/%m/%Y", // formato de la fecha, cuando se escriba en el campo de texto
                                button: "Calendario1" // el id del boton que lanzara el calendario
                            });
                        </script>
                            <?php if ($pedido == 0){ ?>
                                <label for="GT" style="cursor: pointer; float: right"><img src="<?php echo base_url() ?>public/images/icons/gtransferencia.png?=<?=IMG;?>" class="imgBoton"/></label>
                                <input type="radio" name="referenciar" id="GT" value="GT" href="javascript:;" class="verDocuRefe" style="display:none;">
                            <?php } ?>
                            <input type="hidden" name="docReferencia" id="docReferencia" value="<?=$pedido;?>"/>
                                <div id="serieDocRelacionado" name="serieDocRelacionado" style="background-color: #cc7700; color:#fff; padding:5px; <?=($pedido > 0 ) ? 'width: 100%' : 'display: none';?>"><?="$establecimiento <br>Pedido: $serieNumeroPedido"?></div>

                    </td>
                    <td valign="middle">
                        <label for="G" style="cursor: pointer;"><img src="<?php echo base_url() ?>public/images/icons/gremision.png?=<?=IMG;?>" class="imgBoton"/></label>
                        <input type="radio" name="referenciar" id="G" value="G" href="javascript:;" class="verDocuRefe" style="display:none;">
                    </td>
                    <td valign="middle"></td>
                </tr>
            </table>
        </div>

        <table class="fuente8" cellspacing="0" cellpadding="3" border="1" id="despachoHTML">
            <tr class="cabeceraTabla">
                <td width="5%">
                    <div align="center">ITEM</div>
                </td>
                <td width="10%">
                    <div align="center">SERIE</div>
                </td>
                <td width="10%">
                    <div align="center">NUMERO</div>
                </td>
                <td width="10%">
                    <div align="center">TIPO DOC.</div>
                </td>
                <td width="65%">
                    <div align="center">ESTABLECIMIENTO</div>
                </td>
            </tr>
        </table>
        <div class="frmBusqueda" style="height:250px; overflow: auto;">
            <div>
            <table id="despachoDescripcion" class="table table-hover" style="border: 1px solid #ABA7A6;margin-bottom: 0;">
                <tbody id="despachoDescripcionHTML" class="table-hover"> <?php

                    if ( isset( $detalles_despacho ) && count($detalles_despacho) > 0 ){
                        $i = 0;

                        foreach ($detalles_despacho as $fila => $columna) {
                            $i++;
                            if ( $columna->GUIAREMP_Codigo != NULL && $columna->GUIAREMP_Codigo != '' ){
                                $idGuia = $columna->GUIAREMP_Codigo;
                                $serieD = $columna->GUIAREMC_Serie;
                                $numeroD = $columna->GUIAREMC_Numero;
                                $tipoD = "GR";
                                $emisor = $columna->emisorGuiaRem;
                            }
                            else
                                if ( $columna->GTRANP_Codigo != NULL && $columna->GTRANP_Codigo != '' ){
                                    $idGuia = $columna->GTRANP_Codigo;
                                    $serieD = $columna->GTRANC_Serie;
                                    $numeroD = $columna->GTRANC_Numero;
                                    $tipoD = "GT";
                                    $emisor = $columna->emisorGuiaTrans;
                                } ?>

                            <tr id="<?=$fila?>" class="<?=( $i % 2 == 0) ? 'itemParTabla' : 'itemImparTabla';?>" >
                                <td width="5%">
                                    <div align="center"><?=$i;?></div>
                                </td>
                                <td width="10%">
                                    <div align="left">
                                        <input type="hidden" name="serieD[<?=$fila;?>]" id="serieD[<?=$fila;?>]" value="<?=$serieD;?>"/>
                                        <span id="serie_span[<?=$fila;?>]"><?=$serieD;?></span>
                                    </div>
                                </td>
                                <td width="10%">
                                    <div align="left">
                                        <input type="hidden" name="numeroD[<?=$fila;?>]" id="numeroD[<?=$fila;?>]" value="<?=$numeroD;?>"/>
                                        <span id="serie_span[<?=$fila;?>]"><?=$numeroD;?></span>
                                    </div>
                                </td>
                                <td width="10%">
                                    <div align="left">
                                        <span id="tipo_span[<?=$fila;?>]"><?=$tipoD;?></span>
                                    </div>
                                </td>
                                <td width="65%">
                                    <div align="left">
                                        <input type="hidden" name="estabc[<?=$fila;?>]" id="estabc[<?=$fila;?>]" value=""/>
                                        <span id="estabc_span[<?=$fila;?>]"><?=$emisor;?></span>
                                    </div>
                                </td>
                                <td width="5%">
                                    <div style="text-align:center;">
                                        <a href="#" onclick="eliminar_guia(<?=$fila;?>);">
                                            <img src="<?=base_url();?>public/images/icons/delete.png?=<?=IMG;?>" width="20" height="20"  border="0" tittle="eliminar">
                                        </a>';
                                    </div>
                                </td>
                                    <input type="hidden" name="guia[<?=$fila;?>]" id="guia[<?=$fila;?>]" value="<?=$idGuia;?>"/>
                                    <input type="hidden" name="tipo[<?=$fila;?>]" id="tipo[<?=$fila;?>]" value="<?=$tipoD;?>"/>
                                    <input type="hidden" value="m" name="detaccion[<?=$fila;?>]" id="detaccion[<?=$fila;?>]">
                                    <input type="hidden" value="<?=$columna->DESPD_Codigo;?>" name="detacodi[<?=$fila;?>]" id="detacodi[<?=$fila;?>]">
                            </tr> <?php
                        }
                    }
                ?>
                </tbody>
               </table>
            </div>
        </div>
        <style type="text/css">
            .table-hover tr{
                height: 7px;
            }
        </style>

<div id="frmBusqueda3">
            <table width="100%" border="0" align="right" cellpadding=3 cellspacing=0 class="fuente8">
                <tr>
                    <td width="100%" rowspan="5" align="left">
                        <div id="botonBusqueda2">
                            <img id="loading" src="<?php echo base_url(); ?>public/images/icons/loading.gif" style="visibility: hidden"/>
                            <?php if($estado != 0): ?>
                            <a href="javascript:;" id="imgGuardarDespacho"><img src="<?php echo base_url(); ?>public/images/icons/botonaceptar.jpg" width="85" height="22" class="imgBoton"></a>
                            <?php endif; ?>
                            <a href="javascript:;" id="limpiarDespacho"><img src="<?php echo base_url(); ?>public/images/icons/botonlimpiar.jpg" width="69" height="22" class="imgBoton"></a>
                            <a href="javascript:;" id="cancelarDespacho"><img src="<?php echo base_url(); ?>public/images/icons/botoncancelar.jpg" width="85" height="22" class="imgBoton"></a>
                            <input type="hidden" name="salir" id="salir" value="0"/>
                            <?php echo $oculto ?>
                        </div>
                    </td>
                </tr>
            </table>
        </div>

        <br/>


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
        <span style="font-weight: bold;">
          <?php if ($tipo_docu == 'DP') echo 'DESPACHO'; else echo 'DESPACHO'; ?>
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
<?php //require_once(APPPATH."views/ventas/exportacion_subdetalle_modal.php"); 
        $this->load->view('maestros/temporal_detalles_second');
    ?>

</body>

</html>