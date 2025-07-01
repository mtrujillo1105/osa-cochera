<script type="text/javascript" src="<?php echo base_url(); ?>public/js/almacen/guiarem.js?=<?=JS;?>"></script>
<script src="<?php echo base_url(); ?>public/js/jquery.columns.min.js?=<?=JS;?>"></script>

<!--<script type="text/javascript" src="<?php echo base_url(); ?>public/js/fancybox/jquery.mousewheel-3.0.4.pack.js?=<?=JS;?>"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>public/js/fancybox/jquery.fancybox-1.3.4.pack.js?=<?=JS;?>"></script>
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>public/js/fancybox/jquery.fancybox-1.3.4.css?=<?=CSS;?>" media="screen"/>-->

<link href="<?=base_url();?>public/js/fancybox/dist/jquery.fancybox.css?=<?=CSS;?>" rel="stylesheet">
<script src="<?=base_url();?>public/js/fancybox/dist/jquery.fancybox.js?=<?=JS;?>"></script>

<script src="<?php echo base_url(); ?>bootstrap/js/bootstrap.min.js?=<?=JS;?>"></script>
<script src="<?php echo base_url(); ?>bootstrap/js/bootstrap.js?=<?=JS;?>"></script>
<link href="<?php echo base_url(); ?>bootstrap/css/bootstrap.css?=<?=CSS;?>" rel="stylesheet">
<link href="<?php echo base_url(); ?>bootstrap/css/bootstrap-theme.css?=<?=CSS;?>" rel="stylesheet">
<script type="text/javascript">
    $(document).ready(function () { <?php
        if ($tipo_oper == 'V'){ ?>
            setLimite(<?=VENTAS_GUIA;?>); <?php
        }
        else
            if ($tipo_oper == 'C'){ ?>
                setLimite(<?=COMPRAS_GUIA;?>); <?php
            } ?>

        $('#tipo_movimiento').change(function () {
            valor_tipo = $(this).val();
            if (valor_tipo == 9) {
                $('#otro_motivo_oculto').show('slow');
            } else {
                $('#otro_motivo_oculto').hide('slow');
            }
        });

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

        <?php
            if(!$es_importado){
                # ejecutar mostrar orden de compra vista si existe**/
                if($ordencompra!=0 &&  trim($ordencompra)!="" && $ordencompra!=null){ ?>
                  mostrarOdenCompraVista(<?="$ordencompra,'$serieOC','$numeroOC'";?>); <?php
                }
                # no mostrar**/
                # ejecutar mostrar PRESUPUESTO vista si existe**/
                if($presupuesto_codigo!=0 &&  trim($presupuesto_codigo)!="" && $presupuesto_codigo!=null){ ?>
                  mostrarPresupuestoVista(<?="$presupuesto_codigo,'$seriePre','$numeroPre'";?>); <?php
                }
            }
            else{ ?>
                /*mostrar importacion*/
                mostrarImportacionVista(<?="$ordencompra,'$serieOC','$numeroOC'";?>); <?php
            }
        ?>
        
        $("a#linkVerCliente, a#linkSelecCliente, a#linkVerProveedor, a#linkSelecProveedor").fancybox({
            'width': 800,
            'height': 525,
            'autoScale': false,
            'transitionIn': 'none',
            'transitionOut': 'none',
            'showCloseButton': true,
            'modal': true,
            'type': 'iframe'
        });
        $("#linkSelecProducto").fancybox({
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
        $("a#linkVerOrdenCompra").fancybox({
            'width': 970,
            'height': 550,
            'autoScale': false,
            'transitionIn': 'none',
            'transitionOut': 'none',
            'showCloseButton': true,
            'modal': true,
            'type': 'iframe'
        });

        $(".verDocuRefe2").fancybox({
            'width': 800,
            'height': 500,
            'autoScale': false,
            'transitionIn': 'none',
            'transitionOut': 'none',
            'showCloseButton': true,
            'modal': false,
            'type': 'iframe',
            'onStart': function () {

                

                if (tipo_oper == 'V') {
                    if ($('#cliente').val() == '') {
                        alert('Debe seleccionar el cliente.');
                        $('#nombre_cliente').focus();
                        return false;
                    } else {

                        if ($('.verDocuRefe2::checked').val() == 'P')
                            baseurl = base_url + 'index.php/ventas/presupuesto/ventana_muestra_presupuestoCom/' + tipo_oper + '/' + $('#cliente').val() + '/SELECT_HEADER/F/' + almacen + '/P/OC';
                        
                        $('.verDocuRefe2::checked').attr('href', baseurl);

                    }
                } else {

                    if ($('#proveedor').val() == '' && $('.verDocuRefe::checked').val() != 'OV') {
                        alert('Debe seleccionar el proveedor.');
                        $('#nombre_proveedor').focus();
                        return false;
                    } else {
                        if ($('.verDocuRefe::checked').val() == 'P')
                            baseurl = base_url + 'index.php/compras/presupuesto/ventana_muestra_presupuestoCom/' + tipo_oper + '/' + $('#proveedor').val() + '/SELECT_HEADER/F/' + almacen + '/P/OC';
                        

                        $('.verDocuRefe::checked').attr('href', baseurl);
                    }
                }

            }
        });

        $(".verDocuRefe").click(function(){
                if (tipo_oper == 'V') {
                    if ($('#cliente').val() == '') {
                        alert('Debe seleccionar el cliente.');
                        $('#ruc_cliente').focus();
                        return false;
                    } else
                    //alert($('.verDocuRefe::checked').val());
                    if ($('.verDocuRefe::checked').val() == 'F')
                        baseurl = base_url + 'index.php/ventas/comprobante/ventana_muestra_comprobante/' + tipo_oper + '/' + $('#cliente').val() + '/SELECT_HEADER/F/' + almacen + '/F';
                    else if ($('.verDocuRefe::checked').val() == 'P')
                        baseurl = base_url + 'index.php/ventas/presupuesto/ventana_muestra_presupuesto_asoc/' + tipo_oper + '/' + $('#cliente').val() + '/SELECT_HEADER/F/' + almacen + '/P';
                    else if ($('.verDocuRefe::checked').val() == 'O')
                        baseurl = base_url + 'index.php/compras/ocompra/ventana_muestra_ocompra/' + tipo_oper + '/' + $('#cliente').val() + '/SELECT_HEADER/F/' + almacen + '/O';
                    else if ($('.verDocuRefe::checked').val() == 'R')
                        baseurl = base_url + 'index.php/almacen/guiarem/ventana_muestra_recurrentes/' + tipo_oper + '/' + $('#cliente').val() + '/SELECT_HEADER/F/' + almacen + '/R';
                    else if ($('.verDocuRefe::checked').val() == 'R')
                        baseurl = base_url + 'index.php/almacen/guiarem/ventana_muestra_recurrentes/' + tipo_oper + '/' + $('#cliente').val() + '/SELECT_HEADER/F/' + almacen + '/R';
                    else if ($('.verDocuRefe::checked').val() == 'IMP')
                        baseurl = base_url + 'index.php/ventas/importacion/ventana_muestra_importaciones/' + tipo_oper + '/' + $('#proveedor').val() + '/SELECT_HEADER/F/' + almacen + '/IMP';

                    $('.verDocuRefe::checked').attr('href', baseurl);
                }
                else {
                    if ($('#proveedor').val() == '') {
                        alert('Debe seleccionar el proveedor.');
                        $('#buscar_proveedor').focus();
                        return false;
                    } else if ($('.verDocuRefe::checked').val() == 'F')
                        baseurl = base_url + 'index.php/ventas/comprobante/ventana_muestra_comprobante/' + tipo_oper + '/' + $('#proveedor').val() + '/SELECT_HEADER/F/' + almacen + '/F';
                    else if ($('.verDocuRefe::checked').val() == 'P')
                        baseurl = base_url + 'index.php/compras/presupuesto/ventana_muestra_presupuestoCom/' + tipo_oper + '/' + $('#proveedor').val() + '/SELECT_HEADER/F/' + almacen + '/P';
                    else if ($('.verDocuRefe::checked').val() == 'O')
                        baseurl = base_url + 'index.php/compras/ocompra/ventana_muestra_ocompra/' + tipo_oper + '/' + $('#proveedor').val() + '/SELECT_HEADER/F/' + almacen + '/O';
                    else if ($('.verDocuRefe::checked').val() == 'R')
                        baseurl = base_url + 'index.php/almacen/guiarem/ventana_muestra_recurrentes/' + tipo_oper + '/' + $('#proveedor').val() + '/SELECT_HEADER/F/' + almacen + '/R';
                    else if ($('.verDocuRefe::checked').val() == 'R')
                        baseurl = base_url + 'index.php/almacen/guiarem/ventana_muestra_recurrentes/' + tipo_oper + '/' + $('#cliente').val() + '/SELECT_HEADER/F/' + almacen + '/R';
                    else if ($('.verDocuRefe::checked').val() == 'IMP')
                        baseurl = base_url + 'index.php/ventas/importacion/ventana_muestra_importaciones/' + tipo_oper + '/' + $('#proveedor').val() + '/SELECT_HEADER/F/' + almacen + '/IMP';

                    $('.verDocuRefe').attr('href', baseurl);
                }
        });

        /**la guia de remision solo puede pertenecer a un almacenn
        seleccionar combo almacen nos verifica los articulos y nos sentencia***/
        almacenAnterior=$("#almacen").val();
        $("#almacen").change(function(){        
            tipo_oper = $("#tipo_oper").val();    
            if(confirm('Estas seguro de cambiar de almacen, se eliminaran productos que no son de este almacen')){
                almacenSeleccionado=$(this).val();
                
                /**origen cambia si es tripo ventas**/
                if(tipo_oper=='V'){
                      $("#punto_partida").val('');
                }
                /**quitamos los articulos que no son del mismo almacen seleccionado**/
                m = document.getElementById('tblDetalleGuiaRem').rows.length;
                if(m!=0){
                    for(n=0;n<m;n++){
                        c = "almacenProducto[" + n + "]";
                        codigoAlmacen=document.getElementById(c).value;
                        if(codigoAlmacen==almacenAnterior){     
                         a = "detacodi[" + n + "]";
                         b = "detaccion[" + n + "]";
                         fila = document.getElementById(a).parentNode.parentNode.parentNode;
                         fila.style.display = "none";
                         document.getElementById(b).value = "e";
                        }
                    }
                    calcula_totales();
                }
                almacenAnterior=$(this).val();
            }else{
                $("#almacen").val(almacenAnterior);     
            }
            /**fin de ejecucion**/
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

    function ejecutarModal(){
        $("#buscar_producto").val("").focus();
        $('#popup').fadeOut('slow');
        $('.popup-overlay').fadeOut('slow');
        return false;
    }

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

    $("#linkVerproyectoss").click(function(){
        if (tipo_oper == 'V')
            var url = base_url + "index.php/maestros/proyecto/JSON_listar_proyectos/" +$("#cliente").val();
            
        $("#lista_proyecto ul").html('');
        $("#lista_proyecto").slideToggle("fast", function(){
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

    /*-----------------------------------*/
    function seleccionar_cliente(codigo, ruc, razon_social, empresa, persona, direccion) {
        $("#cliente").val(codigo);
        $("#ruc_cliente").val(ruc);
        $("#buscar_cliente").val(ruc);
        $("#nombre_cliente").val(razon_social);
        get_obra(codigo);
        if (tipo_oper == 'V')
            $('#punto_llegada').val(direccion);
    }

    function seleccionar_proveedor(codigo, ruc, razon_social, empresa, persona, ctactesoles, ctactedolares, direccion) {
        $("#proveedor").val(codigo);
        $("#buscar_proveedor").val(ruc);
        $("#nombre_proveedor").val(razon_social);
        $("#buscar_cliente").val(ruc);
        if (tipo_oper == 'C')
            $('#punto_partida').val(direccion);
    }

    function escribe_nombre_unidad_medida() {
        index = document.getElementById("unidad_medida").selectedIndex;
        nombre = document.getElementById("unidad_medida").options[index].text;
        $("#nombre_unidad_medida").val(nombre);
    }

    function seleccionar_producto(codigo, interno, familia, stock, costo, flagGenInd,codigoAlmacenProducto) {
        /**si el producto tiene almacen : es que no esta inventariado en ese almacen , se le asigna el almacen general de cabecera**/
        almacenGeneral=$("#almacen").val();
        if(codigoAlmacenProducto==0){
            codigoAlmacenProducto=almacenGeneral;
         }else{
            if(almacenGeneral!=codigoAlmacenProducto){
                alert("debe de ingresar un producto que se encuentre en este almacen.");
                return;
            }
         }
        /**fin de asignacion**/
        /**verificamos si se e3ncuentra en la lista**/
        isEncuentra=verificarProductoDetalle(codigo,codigoAlmacenProducto);
        if(!isEncuentra){
            $("#producto").val(codigo);
            $("#codproducto").val(interno);
            $("#nombre_familia").val(familia);
            $("#stock").val(stock);
            $("#costo").val(costo);
            $("#cantidad").select();
            $("#flagGenInd").val(flagGenInd);
            $("#almacenProducto").val(codigoAlmacenProducto);
            listar_unidad_medida_producto(codigo);
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
        $("#presupuesto_codigo").val("");
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
                    $("#moneda option:selected").each(function () {
                        $(this).removeAttr('selected'); 
                    });
                    $("#almacen option:selected").each(function () {
                        $(this).removeAttr('selected'); 
                    });
                    $("#obra option:selected").each(function () {
                        $(this).removeAttr('selected'); 
                    });
                },
                success: function (data) {
                    if (data.result == "success"){

                        $("#dRef").val(data.info.comprobante);
                        $("#serieguiaver").html("Documento Relacionado: " + data.info.serie + " - " + data.info.numero);
                        $("#numero_ref").val(data.info.serie + " - " + data.info.numero);
                        $("#serieguiaver").show();

                        $('#descuento').val(data.info.descuento);
                        $('#punto_llegada').val(data.info.direccion);
                        
                        if (data.info.moneda != "" && data.info.moneda != null)
                            $("#moneda > option[value="+data.info.moneda+"]").attr("selected",true);

                        if (data.info.almacen != "" && data.info.almacen != null)
                            $("#almacen > option[value="+data.info.almacen+"]").attr("selected",true);

                        if (data.info.proyecto != "" && data.info.proyecto != null)
                            $("#obra > option[value="+data.info.proyecto+"]").attr("selected",true);


                        $("#serieguiaver").show(200);
                        $("#serieguiaverPre").hide(200);
                        $("#serieguiaverOC").hide(200);
                        $("#serieguiaverRecu").hide(200);
                        $('#ordencompra').val('');


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
        //agregar_todo(guia);
    }

    function seleccionar_presupuesto(guia, serieguia, numeroguia) {
        isRealizado=modificarTipoSeleccionPrersupuesto(guia,1);
        if(isRealizado){
            mostrarPresupuestoVista(guia, serieguia, numeroguia);
            //agregar_todopresupuesto(guia, tipo_oper);
            obtener_comprobantes_temproductos(guia,'presupuesto')
        }
    }

    function mostrarPresupuestoVista(guia, serieguia, numeroguia){
        if(tipo_oper=="V")
            serienumero = "Numero de PRESUPUESTO :" + serieguia + " - " + numeroguia;
        else
            serienumero = "Numero de COTIZACIÓN :" + serieguia + " - " + numeroguia;
        
        $("#serieOrden").hide(200);
         $("#serieguiaverPre").html(serienumero);
         $("#serieguiaverPre").show(200);
         $("#serieguiaver").hide(200);
         $("#serieguiaverOC").hide(200);
         $("#serieguiaverRecu").hide(200);
         $("#numero_ref").val('');
         $("#dRef").val('');
         $('#ordencompra').val('');
         $("#presupuesto_codigo").val(guia);

         
        }   
    
    function seleccionar_guiarem_recu(guia, serieguia, numeroguia) {
        //agregar_todo_recu(guia);
        obtener_comprobantes_temproductos(guia,'guiarem');
        serienumero = "Numero de guia :" + serieguia + " - " + numeroguia;
        $("#serieguiaverRecu").html('documento recurrente:' + serienumero);
        $("#serieguiaverRecu").show(200);
        $("#serieguiaver").hide(200);
        $("#serieguiaverPre").hide(200);
        $("#serieguiaverOC").hide(200);
        $("#serieOrden").hide(200);
        $("#numero_ref").val('');
        $("#dRef").val('');
        $('#ordencompra').val('');
        
        codigoPresupuesto=$("#presupuesto_codigo").val();
        if(codigoPresupuesto!="" && codigoPresupuesto!=0){
            modificarTipoSeleccionPrersupuesto(codigoPresupuesto,0);
        }
        $("#presupuesto_codigo").val("");
    }

    function seleccionar_ocompra(ocompra){
        mostrarOdenCompraVista(ocompra);
        //obtener_detalle_ocompra_origen(ocompra);
        obtener_comprobantes_temproductos(ocompra,'ocompras');
    }

    function mostrarOdenCompraVista(ocompra){

        $.ajax({
                url: "<?=base_url();?>index.php/compras/ocompra/relacionar_oc",
                type: "POST",
                data: {
                    ocompra: ocompra
                },
                dataType: "json",
                beforeSend: function(data){
                    /*$("#cboVendedor option:selected").each(function () {
                        $(this).removeAttr('selected'); 
                    });*/
                    $("#moneda option:selected").each(function () {
                        $(this).removeAttr('selected'); 
                    });
                    $("#obra option:selected").each(function () {
                        $(this).removeAttr('selected'); 
                    });
                },
                success: function (data) {
                    if (data.result == "success"){

                        $('#ordencompra').val(data.info.ocompra);
                        $('#punto_llegada').val(data.info.direccion_env);
                        //$('#oc_cliente').val(data.info.OCcliente);
                        $('#descuento').val(data.info.descuento);
                        
                        if(data.info.operacion == 1)
                            $("#serieOrden").html("Orden de compra número: " + data.info.serie + " - " + data.info.numero);
                        else
                            $("#serieOrden").html("Orden de venta número: " + data.info.serie + " - " + data.info.numero);

                        /*if (data.info.vendedor != "" && data.info.vendedor != null)
                            $("#cboVendedor > option[value="+data.info.vendedor+"]").attr("selected",true);*/

                        if (data.info.moneda != "" && data.info.moneda != null)
                            $("#moneda > option[value="+data.info.moneda+"]").attr("selected",true);

                        if (data.info.proyecto != "" && data.info.proyecto != null)
                            $("#obra > option[value="+data.info.proyecto+"]").attr("selected",true);

                        $('#numeroOrden').val(data.info.serie + '-' + data.info.numero);
                        $("#serieOrden").show(200);

                        $("#serieguiaver").hide(200);
                        $("#serieguiaverPre").hide(200);
                        $("#serieguiaverOC").hide(200);
                        $("#numero_ref").val('');
                        $("#dRef").val('');
                        $("#serieguiaverRecu").hide(200);
                    }
                },
                complete: function(data){
                    codigoPresupuesto=$("#presupuesto_codigo").val();
                    if(codigoPresupuesto!="" && codigoPresupuesto!=0){
                        modificarTipoSeleccionPrersupuesto(codigoPresupuesto,0);
                    }
                    $("#presupuesto_codigo").val("");
                }
        });
    }

    /*function mostrarImportacionVista(ocompra,serie, numero){
        serienumero = "Numero de Importación <br>" + serie + " - " + numero;
        $('#numeroOrden').val(serie + '-' + numero);
        $('#ordencompra').val(ocompra);
        $("#serieOrden").html(serienumero);
        $("#serieOrden").show(200);
        $("#serieguiaver").hide(200);
        $("#serieguiaverPre").hide(200);
        $("#serieguiaverOC").hide(200);
        $("#numero_ref").val('');
        $("#dRef").val('');
         
        codigoPresupuesto=$("#presupuesto_codigo").val();
        if(codigoPresupuesto!="" && codigoPresupuesto!=0){
            modificarTipoSeleccionPrersupuesto(codigoPresupuesto,0);
        }
        $("#presupuesto_codigo").val("");
    }*/

    function seleccionar_importacion(ocompra, serie, numero)
    {
        mostrarImportacionVista(ocompra, serie, numero);
       // obtener_detalle_importacion_origen(ocompra);
        obtener_comprobantes_temproductos(ocompra,'comprobantes');
    }
    
    function mostrarImportacionVista(ocompra,serie, numero) {
        serienumero = "Numero de Importacion <br>" + serie + " - " + numero;
        $('#numeroOrden').val(serie + '-' + numero);
        $('#ordencompra').val(ocompra);
        $("#serieOrden").html(serienumero);
        $("#serieOrden").show(200);
        $("#serieguiaver").hide(200);
        $("#serieguiaverPre").hide(200);
        $("#serieguiaverOC").hide(200);
        $("#numero_ref").val('');
        $("#dRef").val('');
        $("#igv").val(0);
         
        codigoPresupuesto=$("#presupuesto_codigo").val();
        if(codigoPresupuesto!="" && codigoPresupuesto!=0){
            modificarTipoSeleccionPrersupuesto(codigoPresupuesto,0);
        }
        $("#presupuesto_codigo").val("");
    }

    /**seleccionamos un almacen para el producto agregaod po o.vc cotizacioon, recurrentes**/
    function mostrarPopUpSeleccionarAlmacen(posicionSeleccionado){
        a="almacenProducto["+posicionSeleccionado+"]";
        b="prodcodigo["+posicionSeleccionado+"]";
        $("#posicionSeleccionadaSerie").val(posicionSeleccionado);
        almacenProducto=document.getElementById(a).value;
        codigoProducto=document.getElementById(b).value;
        url="<?php echo base_url(); ?>index.php/almacen/producto/buscarAlmacenProducto/"+codigoProducto;

        n = document.getElementById('idTblAlmacen').rows.length;
        if(n!=null && n!='' && n>1){
            for(x=1;x<n;x++){
                document.getElementById("idTblAlmacen").deleteRow(1);
            }
        }
        
        $.ajax({
                url: url,
                dataType: 'json',
                async: false, 
                success: function (data) {
                    $.each(data, function (i, item) {
                        codigoAlmacen=item.codigo;
                        nombreAlmacen=item.nombreAlmacen;
                        stock=item.stock;
                        j=i+1;
                        fila="<tr id='idTr_"+j+"' >";
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
                    });
                    $("#dialogoSeleccionarALmacenProducto").dialog("open");
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

    $(function(){
        $("#ubigeo_partida").autocomplete({
            source: function (request, response) {
                $.ajax({
                    url: "<?php echo base_url(); ?>index.php/maestros/ubigeo/autocompleteUbigeo/",
                    type: "POST",
                    data: {
                        term: $("#ubigeo_partida").val()
                    },
                    dataType: "json",
                    success: function (data) {
                        response( $.map(data, function(item) {
                                return {
                                    descripcion: item.descripcion,
                                    label: item.descripcion,
                                    value: item.codigo
                                }})
                            );
                    }
                });
            },
            select: function (event, ui) {
                $("#ubigeo_partida").val(ui.item.codigo);
                $("#ubigeopText").val(ui.item.descripcion);
            },
            minLength: 2
        });      

        $("#ubigeo_llegada").autocomplete({
            source: function (request, response) {
                $.ajax({
                    url: "<?php echo base_url(); ?>index.php/maestros/ubigeo/autocompleteUbigeo/",
                    type: "POST",
                    data: {
                        term: $("#ubigeo_llegada").val()
                    },
                    dataType: "json",
                    success: function (data) {
                        response( $.map(data, function(item) {
                                return {
                                    descripcion: item.descripcion,
                                    label: item.descripcion,
                                    value: item.codigo
                                }})
                            );
                    }
                });
            },
            select: function (event, ui) {
                $("#ubigeo_llegada").val(ui.item.codigo);
                $("#ubigeolText").val(ui.item.descripcion);
            },
            minLength: 2
        });
    });
</script>
<?php echo $form_open; ?>

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
    <input type="hidden" name="codigoguia" id="codigoguia" value="<?php echo $guia; ?>"/>
    <input value='<?php echo $compania; ?>' name="compania" type="hidden" id="compania"/> <!-- Add id compañia -  -->
    <?php echo validation_errors("<div class='error'>", '</div>'); ?>
    <div id="tituloForm" class="header"><?php echo $titulo; ?></div>
    
        <div id="idDivGuiaRelacion" style="<?php echo ($tipoGuia==1)?'':'display:none'; ?>">
        <div id="dialogSeriesAsociadas" title="Series Ingresadas">
          <div id="mostrarDetallesSeriesAsociadas"> 
           <div id="detallesSeriesAsociadas"></div>
          </div>
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
    
    
    <div id="frmBusqueda">
        <table class="fuente8" width="100%" cellspacing="0" cellpadding="5" border="0">
            <tr>
            <!--idex de guiarem-->
                <td width="8%">Número*</td>
                <td width="39%" valign="top">
                    <?php
                    if ($tipo_oper == 'V') {
                        switch ($tipo_codificacion) {
                            case '1':
                                echo '<input type="text" name="numero" id="numero" value="' . ($codigo != '' ? $numero : $numero_suger) . '" class="cajaGeneral cajaSoloLectura" readonly="readonly"  size="10" maxlength="10" placeholder="Numero" />';
                                break;
                                
                            case '2':
                                echo '<input type="text" name="serie" id="serie" value="' . $serie . '" class="cajaGeneral cajaSoloLectura" size="3" maxlength="8" placeholder="Serie" /> ';
                                echo '<input type="text" name="numero" id="numero" value="' . $numero . '" class="cajaGeneral cajaSoloLectura" size="10" maxlength="15" placeholder="Numero"  /> ';
                                echo '<a href="javascript:;" id="linkVerSerieNum"' . ($codigo != '' ? 'style="display:none"' : '') . '><p style="display:none">' . $serie_suger . '-' . $numero_suger . '</p><image src="' . base_url() . 'images/flecha.png?='.IMG.'" border="0" alt="Serie y número sugerido" title="Serie y número sugerido" /></a>';
                                break;
                            case '3':
                                echo '<input type="text" name="codigo_usuario" id="codigo_usuario" value="' . $codigo_usuario . '" class="cajaGeneral" size="20" maxlength="50"  />';
                                break;
                        }
                        ?>
                         <input type="checkbox" name="numeroAutomatico"  id="numeroAutomatico" <?=($numeroAutomatico == 1 ) ? 'checked=true' : '';?> value="1" title="SERIE-NUMERO AUTOMATICO SI SE SELECCIONA">
                            
                        <?php 
                    } else {
                        echo '<input type="text" name="serie" id="serie" value="' . $serie . '" class="cajaGeneral" size="3" maxlength="8" placeholder="Serie"  /> ';
                        echo '<input type="text" name="numero" id="numero" value="' . $numero . '" class="cajaGeneral" size="10" maxlength="15" placeholder="Numero"  /> ';
                       
                        echo '<a href="javascript:;" id="linkVerSerieNum"' . ($codigo != '' ? 'style="display:none"' : '') . '><p style="display:none">' . $serie_suger . '-' . $numero_suger_c . '</p><image src="' . base_url() . 'images/flecha.png?='.IMG.'" border="0" alt="Serie y nÃºmero sugerido" title="Serie y nÃºmero sugerido" /></a>';
                    } ?>
                </td>
                <!--<td width="13%" >Presupuesto</td>-->
                <td width="18%" > 
                    <!--

                   <?php if ($tipo_oper == 'V') { ?>
                            <label for="P"><img src="<?php echo base_url() ?>public/images/icons/presupuesto.png?=<?=IMG;?>" style="cursor:pointer;" class="imgBoton"/></label>
                            <input type="hidden" name="presupuesto" id="presupuesto">
                        <?php } ?>
                        <input type="radio" name="referenciar" id="P" value="P" href="javascript:;" class="verDocuRefe" style="display:none;" data-fancybox data-type="iframe">
                        <div id="serieguiaverPre" name="serieguiaverPre" style="background-color: #cc7700; color:#fff; padding:5px;display:none"></div>
                        -->
                </td>



    

                <!--<td width="13%" >O. <?php //if ($tipo_oper == 'C') echo 'Compra'; else echo 'Venta'; ?> </td>-->
                <td width="18%" style="<?php echo ($tipoGuia==1)?'display:none;':'' ?>">
                    <!-- <select name="ordencompra" id="ordencompra" class="comboMedio" >
                            <?php
                    /*if ($modo == 'insertar') {
                        echo $cboOrdencompra;
                    } else {
                        echo "<option value=''>::Seleccione::</option>";
                        echo $cboOrdencompra;
                    }*/
                    ?>
                    </select>
                       <a href="<?php //echo base_url(); ?>index.php/compras/ocompra/comprobante_nueva_ocompra/" id="linkVerOrdenCompra" ></a>-->
                    <?php if ($tipo_oper == 'V') {
                      $imagenboton="cotizacion.png";
                    }else{
                        $imagenboton="ocompra.png";
                    } ?>
                    <label for="O" style="cursor: pointer"><img src="<?php echo base_url() ?>public/images/icons/<?php echo $imagenboton?>?=<?=IMG;?>" class="imgBoton"/></label>
                    <input type="radio" name="referenciar" id="O" value="O" href="javascript:;" class="verDocuRefe" style="display:none;" data-fancybox data-type="iframe">

                    <div id="serieguiaverOC" name="serieguiaverOC" style="background-color: #cc7700; color:#fff; padding:5px;display:none"></div>
                    <input type="hidden" name="ordencompra" id="ordencompra" size="5" value="<?php echo $ordencompra; ?>"/>
                    <input type="hidden" name="numeroOrden" id="numeroOrden" size="5" value=""/>
                </td>

                <td width="13%">F.Traslado*</td>
                <td width="18%"><?php echo $fecha_traslado; ?>
                    <img src="<?php echo base_url(); ?>public/images/icons/calendario.png?=<?=IMG;?>" name="Calendario2" id="Calendario2" width="16" height="16" border="0" onMouseOver="this.style.cursor='pointer'" title="Calendario">
                    <script type="text/javascript">
                        Calendar.setup({
                            inputField: "fecha_traslado",      // id del campo de texto
                            ifFormat: "%d/%m/%Y",       // formato de la fecha, cuando se escriba en el campo de texto
                            button: "Calendario2"   // el id del botÃ³n que lanzarÃ¡ el calendario
                        });
                    </script>
                </td>
            </tr>
            <tr>
                <td><?=($tipo_oper=="V") ? "Cliente *" : "Proveedor *";?></td>
                <td valign="middle"> <?php
                    if ($tipo_oper == "V") { ?>
                        <input type="hidden" name="cliente" id="cliente" size="5" value="<?php echo $cliente ?>"/>
                        <input placeholder="ruc" name="buscar_cliente" type="text" class="cajaGeneral" id="buscar_cliente" size="10" <?php echo ($tipoGuia==1)?'readonly="readonly"':'' ?> value="<?php echo $ruc_cliente; ?>" title="Ingrese parte del nombre o el nro. de documento, luego presione ENTER."/>&nbsp;
                        <input type="hidden" name="ruc_cliente" class="cajaGeneral" id="ruc_cliente" size="10" maxlength="11" onblur="obtener_cliente();" value="<?php echo $ruc_cliente; ?>" onkeypress="return numbersonly(this,event,'.');"/>
                        <input placeholder="razon social" type="text" name="nombre_cliente" class="cajaGeneral" id="nombre_cliente" <?php echo ($tipoGuia==1)?'readonly="readonly"':'' ?> size="37" maxlength="50" value="<?php echo $nombre_cliente; ?>"/>

                        <!-- Add ingresar precio del cliente - -->
                        <?php if ( !isset($TIPCLIP_Codigo) ) $TIPCLIP_Codigo = ""; ?>
                        <input type="hidden" name="tempde_TipCli" id="TipCli" value="<?php echo $TIPCLIP_Codigo; ?>">
                        <!-- End add ingresar precio del cliente -  -->
                        <a href="<?php echo base_url(); ?>index.php/empresa/cliente/ventana_selecciona_cliente/" id="linkSelecCliente"></a> <?php
                    }
                    else { ?>
                        <input type="hidden" name="proveedor" id="proveedor" size="5" value="<?php echo $proveedor ?>"/>
                        <input name="buscar_proveedor" type="text" class="cajaGeneral" id="buscar_proveedor" size="10" placeholder="ruc" value="<?php echo $ruc_proveedor; ?>" title="Ingrese parte del nombre o el nro. de documento, luego presione ENTER."/>&nbsp;
                        <input type="hidden" name="ruc_proveedor" class="cajaGeneral" id="ruc_proveedor" <?php echo ($tipoGuia==1)?'readonly="readonly"':'' ?> size="10" maxlength="11" onblur="obtener_proveedor();" value="<?php echo $ruc_proveedor; ?>" placeholder="ruc" onkeypress="return numbersonly(this,event,'.');"/>
                        <input type="text" name="nombre_proveedor" class="cajaGeneral cajaSoloLectura" <?php echo ($tipoGuia==1)?'readonly="readonly"':'' ?> placeholder="Nombre proveedor" id="nombre_proveedor" size="25" maxlength="50" placeholder="razon social" value="<?php echo $nombre_proveedor; ?>"/>
                        <a href="<?php echo base_url(); ?>index.php/empresa/proveedor/ventana_selecciona_proveedor/" id="linkSelecProveedor"></a> <?php
                    }
                    $this->load->view('layout/modalClienteNuevo'); ?>
                </td>

                <?php if($tipoGuia==1) {?>
                     <input name="moneda" type="hidden" id="moneda" value="<?=$moneda;?>">
                <?php } ?>
                <!-- <td>Doc. Pago</td>-->
                <td  style="<?php echo ($tipoGuia==1)?'display:none;':'' ?>" >
   
                    <input type="hidden" id="dRef" name="dRef">

                    <div id="serieguiaver" name="serieguiaver" style="background-color: #cc7700; color:#fff; padding:5px;display:none"></div>
                    <label for="IMP" style="visibility: hidden; cursor: pointer"><img src="<?php echo base_url() ?>public/images/icons/importacion.png?=<?=IMG;?>" class="imgBoton"/></label>
                    <input type="radio" name="referenciar" id="IMP" value="IMP" href="javascript:;" class="verDocuRefe" style="display:none;">
                </td>
                <!-- <td width="13%" >DOC. Recurrennte</td>-->
                <td width="18%" style="visibility: hidden;">
                    <label for="R" style="cursor: pointer"><img src="<?php echo base_url() ?>public/images/icons/docrecurrente.png?=<?=IMG;?>" class="imgBoton"/></label>
                    <input type="radio" name="referenciar" id="R" value="R" href="javascript:;" class="verDocuRefe" style="display:none;">

                    <div id="serieguiaverRecu" name="serieguiaverRecu" style="background-color: #cc7700; color:#fff; padding:5px;display:none"></div>
                </td>
                <td>Moneda *</td>
                <td><?php echo $cboMoneda; ?></td>
            </tr>
            <tr>
                <td width="15%">Almacen *</td>
                <td width="18%"><?php echo $cboAlmacen; ?></td>
                <?php if($tipoGuia==1) {?>
                     <input name="almacen" type="hidden" id="almacen" value="<?php echo $almacen; ?>">
                <?php }?>
                
                <td>Motivo del Traslado *</td>
                <td colspan="2"><?php echo $cboTipoMov; ?></td>
                <td>
                    <div id="otro_motivo_oculto" style="display: none">Otro Motivo <?php echo $otro_motivo; ?></div>
                    <input type="hidden" id="igv" name="igv" value="18">
                </td>
            </tr>
        
            <tr>
                <td>Ubigeo Origen *</td>
                <td>
                    <?=$ubigeo_partida;?>
                    <input type="text" name="ubigeopText" id="ubigeopText" readonly class="cajaGeneral" style="width: 18em; font-size: 8pt" value="<?=$ubigeopText;?>">
                </td>
                <td>Dirección Origen *</td>
                <td colspan="2"><?php echo $punto_partida; ?>
                    <a href="javascript:;" id="linkVerMisDirecciones">
                        <img src="<?php echo base_url(); ?>public/images/icons/ver.png?=<?=IMG;?>" border="0"/>
                    </a>

                    <div id="lista_mis_direcciones" class="cuadro_flotante" style="width: auto">
                        <ul></ul>
                    </div>
                </td>
                <td rowspan="2"><div id="serieOrden" name="serieOrden" style="background-color: #cc7700; color:#fff; padding:5px; display: none"></div></td>
            </tr>

            <tr>
                <td>Ubigeo Destino *</td>
                <td>
                    <?=$ubigeo_llegada;?>
                    <input type="text" name="ubigeolText" id="ubigeolText" readonly class="cajaGeneral" style="width: 18em; font-size: 8pt" value="<?=$ubigeolText;?>">
                </td>
                <td>Dirección Destino *</td>
                <td colspan="3"><?php echo $punto_llegada; ?>
                    <a href="javascript:;" id="linkVerDirecciones">
                        <img src="<?php echo base_url(); ?>public/images/icons/ver.png?=<?=IMG;?>" border="0"/>
                    </a>

                    <div id="lista_direcciones" class="cuadro_flotante">
                        <ul>
                        </ul>
                    </div>
                </td>
            </tr>
            <tr>
                <td>Documento relacionado</td>
                <td> <input class="cajaGeneral" name="numero_ref" type="text" id="numero_ref" size="14" maxlength="26" value="<?php echo $numero_ref; ?>"/></td>
                <?php echo $cboObra; ?> 

                <td>Orden de compra</td>
                <td> <input type="text" name="ordencompraempresa" id="ordencompraempresa" value="<?=$ordencompraempresa;?>"> </td>
            </tr>
        </table>
    </div>
    <div id="frmBusqueda" class="box-add-product" style="text-align: right;" >
            <a href="#" id="addItems" name="addItems" style="color:#ffffff;" class="btn btn-primary" data-toggle="modal" data-target=".bd-example-modal-lg" onclick="limpiar_campos_modal(); ">Agregar Items</a></td>
    </div>
    <!-- TABLA DETALLE DE TEMPORAL -->
<?php $this->load->view('maestros/temporal_subdetalles'); ?>
       <!-- FIN DE TABLA TEMPORAL DETALLE -->
    <div id="frmBusqueda" style="margin-top: 5px">
        <table width="100%" border="0" align="center" cellpadding="3" cellspacing="0" class="">
            <tr>
                <td width="80%">
                    <table class="fuente8_2" style="width: 100%" border="0" cellpadding="3" cellspacing="5">


                        <tr>
                            
                            <td><b>DATOS DE TRASLADO</b></td>
                            <td>Modalidad de transporte</td>
                            <td ><select id="mod_transporte" name="mod_transporte" value="<?php echo $mod_transporte?>" class="comboMedio">
                                <option>Seleccionar</option>
                                <option value="2" <?php echo ($mod_transporte==1) ? "selected":""; ?>>TRANSPORTE PRIVADO</option>
                                <option value="1" <?php echo ($mod_transporte==2) ? "selected":""; ?>>TRANSPORTE PUBLICO</option>
                            </select> </td>
                            <td>PESO BRUTO TOTAL (Kg)</td>
                            <td><input type="text" name="peso_total" id="peso_total" class="comboMedio" value="<?php echo $peso_total;?>"> </td>
                            <td>NÚMERO DE BULTOS:</td>
                            <td><input type="text" name="num_bultos" id="num_bultos" class="comboMedio" value="<?php echo $num_bultos;?>"> </td>
                            

                           

                        </tr>
                        <tr>
                            <td><b>DATOS TRANSPORTISTA</b></td>
                            <td>EMPRESA DE TRANSP.</td>
                            <td colspan="2"><input type="text" name="nombre_empresa_transporte" id="nombre_empresa_transporte" class="comboGrande" value="<?php echo $nombre_empresa_transporte;?>">
                                
                                <?php //echo $cboEmpresaTrans; ?></td>
                            <td>RUC EMPRESA TRANSP.</td>
                            <td colspan="2"><input type="text" name="ruc_empresa_transporte" id="ruc_empresa_transporte" class="comboGrande" value="<?php echo $ruc_empresa_transporte;?>"></td>

                           
                        </tr>
                        <tr>
                            <td><b>UNIDAD DE TRANSP.</b></td> <!-- width="30%" -->
                            <td>Marca</td> <!-- width="05%" -->
                            <td><?php echo $marca; ?></td> <!-- width="20%" -->
                            <td>Placa</td> <!-- width="10%" -->
                            <td><?php echo $placa; ?></td> <!-- width="10%" -->
                            <td>Registro MTC</td> <!-- width="15%" -->
                            <td><?php echo $registro_mtc; ?></td> <!-- width="10%" -->
                        </tr>
                        <tr>
                            <td><b>CONDUCTOR</b></td>
                            <td>Nombres</td>
                            <td><?php echo $nombre_conductor; ?></td>
                            <td>Cert. Inscripción</td>
                            <td><?php echo $certificado; ?></td>
                            <td>Licencia de conducir</td>
                            <td><?php echo $licencia; ?></td>
                        </tr>
                        <tr>
                            <td style="visibility: hidden;"><b>PERS. RECEPCIONA</b></td>
                            <td>DNI</td>
                            <td colspan="3"><?php echo $recepciona_dni; ?></td>
                            <td style="visibility: hidden;">Nombres</td>
                            <td style="visibility: hidden;"><?php echo $recepciona_nombres; ?> </td>
                        </tr>
                        <tr>
                            <td style="display: none;"><b>ESTADO</b></td>
                            <td style="display: none;" colspan="6"><?php echo $estado; ?></td>
                        </tr>
                        <tr>
                            <td valign="top"><b>OBSERVACION</b></td>
                            <td colspan="6"><textarea id="observacion" name="observacion" class="cajaTextArea" style="width:100%" rows="3"><?php echo $observacion; ?></textarea>
                            </td>
                        </tr>
                    </table>
                </td>
                <td valign="top" width="20%">
                    <table width="100%" border="0" align="right" cellpadding="3" cellspacing="0" class="" style="margin-top:20px;">
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
                </td>
            </tr>
        </table>
        <?php echo $oculto; ?>
    </div>
    <br/>

    <div id="botonBusqueda2" style="padding-top:20px;">
        <img id="loading" src="<?php echo base_url(); ?>public/images/icons/loading.gif?=<?=IMG;?>" style="visibility: hidden"/>
        <?php if($flagEstado == 2): ?>
        <a href="javascript:;" id="grabarGuiarem"><img src="<?php echo base_url(); ?>public/images/icons/botonaceptar.jpg?=<?=IMG;?>" width="85" height="22" class="imgBoton"/></a>
        <?php endif; ?>
        <a href="javascript:;" id="" onclick="limpiar_guiarem();"><img src="<?php echo base_url(); ?>public/images/icons/botonlimpiar.jpg?=<?=IMG;?>" width="69" height="22" class="imgBoton"/></a>
        <a href="javascript:;" id="cancelarGuiarem"><img src="<?php echo base_url(); ?>public/images/icons/botoncancelar.jpg?=<?=IMG;?>" width="85" height="22" class="imgBoton"/></a>
        <input type="hidden" name="salir" id="salir" value="0"/>
        <input type="hidden" name="codigo_orden" id="codigo_orden" value=""/>
        <input type="hidden" name="codigo_orden2" id="codigo_orden2" value=""/>
        <input type="hidden" name="flagEstado" id="flagEstado" size="5" value="<?php echo $flagEstado; ?>" />
        <input type="hidden" name="tipoGuia" id="tipoGuia" value="<?php echo $tipoGuia;?>"/>
    </div>
</div>
<?php //require_once(APPPATH."views/ventas/exportacion_subdetalle_modal.php"); 
        $this->load->view('maestros/temporal_detalles');
    ?>
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
<?php echo $form_close; ?>

<script>
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

    $(function(){
        $("#obra").change(function (event) {
            var value = event.target.value;
        }).trigger('change');

        $("#usa-adelanto").change(function (event) {
            var isCheck = $(event.target).attr('checked');
            var adelanto = $("#proyecto-adelanto").val();
            var descuento = isCheck ? $("#descuentotal").val() : 0;
            $("#saldo-adelanto").val(parseFloat(adelanto - descuento).format());
            $("#descuento").val(isCheck ? descuentoPercent : 0).trigger("blur");
        });
    });
</script>