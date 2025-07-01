<script type="text/javascript" src="<?php echo base_url(); ?>public/js/jquery.js?=<?=JS;?>"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>public/js/jquery-ui-1.8.17.custom.min.js?=<?=JS;?>"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>public/js/ventas/oservicio.js?=<?=JS;?>"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>public/js/funciones.js?=<?=JS;?>"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>public/js/fancybox/jquery.mousewheel-3.0.4.pack.js?=<?=JS;?>"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>public/js/fancybox/jquery.fancybox-1.3.4.pack.js?=<?=JS;?>"></script>

<script src="<?php echo base_url(); ?>bootstrap/js/bootstrap.min.js?=<?=JS;?>"></script>
<script src="<?php echo base_url(); ?>bootstrap/js/bootstrap.js?=<?=JS;?>"></script>

<link href="<?php echo base_url(); ?>bootstrap/css/bootstrap.css?=<?=CSS;?>" rel="stylesheet">
<link href="<?php echo base_url(); ?>bootstrap/css/bootstrap-theme.css?=<?=CSS;?>" rel="stylesheet">

<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>public/js/fancybox/jquery.fancybox-1.3.4.css?=<?=CSS;?>"
      media="screen"/>
<script type="text/javascript">
    $(document).ready(function () {
        /**ejecutar mostrar orden de compra vista si existe**/
            <?php if(isset($ordenventa) && $ordenventa!=0 &&  trim($ordenventa)!="" && $ordenventa!=null){ ?>
                
                <?php echo "mostrarOrdenVentaVista($ordenventa, $serie, $OCOMC_Numero, 2);" ?>
            <?php } ?>
        almacen = $("#cboCompania").val();
        $("a#linkVerPersona").fancybox({
            'width': 750,
            'height': 335,
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
        $("a#linkVerCliente, a#linkSelecCliente, a#linkVerProveedor, a#linkSelecProveedor").fancybox({
            'width': 800,
            'height': 550,
            'autoScale': false,
            'transitionIn': 'none',
            'transitionOut': 'none',
            'showCloseButton': true,
            'modal': true,
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

        $(".verDocuRefe").fancybox({
            'width': 800,
            'height': 500,
            'autoScale': false,
            'transitionIn': 'none',
            'transitionOut': 'none',
            'showCloseButton': true,
            'modal': false,
            'type': 'iframe',
            'onStart': function () {

                tipo_oper = '<?php echo $tipo_oper; ?>';

                if (tipo_oper == 'V') {
                    if ($('#cliente').val() == '') {
                        alert('Debe seleccionar el cliente.');
                        $('#nombre_cliente').focus();
                        return false;
                    } else {

                        if ($('.verDocuRefe::checked').val() == 'P')
                            baseurl = base_url + 'index.php/ventas/presupuesto/ventana_muestra_presupuestoCom/' + tipo_oper + '/' + $('#cliente').val() + '/SELECT_HEADER/F/' + almacen + '/P/OC';
                        else if ($('.verDocuRefe::checked').val() == 'O')
                            baseurl = base_url + 'index.php/compras/ocompra/ventana_muestra_ocompra/' + tipo_oper + '/' + $('#cliente').val() + '/SELECT_HEADER/F/' + almacen + '/O/OC';
                        else if ($('.verDocuRefe::checked').val() == 'OV')
                            baseurl = base_url + 'index.php/compras/ocompra/ventana_muestra_oventa';


                        $('.verDocuRefe::checked').attr('href', baseurl);

                    }
                } else {

                    if ($('#proveedor').val() == '' && $('.verDocuRefe::checked').val() != 'OV') {
                        alert('Debe seleccionar el proveedor.');
                        $('#nombre_proveedor').focus();
                        return false;
                    } else {
                        if ($('.verDocuRefe::checked').val() == 'P')
                            baseurl = base_url + 'index.php/compras/presupuesto/ventana_muestra_presupuestoCom/' + tipo_oper + '/' + $('#proveedor').val() + '/SELECT_HEADER/F/' + almacen + '/P/OC';
                        else if ($('.verDocuRefe::checked').val() == 'O')
                            baseurl = base_url + 'index.php/compras/ocompra/ventana_muestra_ocompra/' + tipo_oper + '/' + $('#proveedor').val() + '/SELECT_HEADER/F/' + almacen + '/O/OC';
                        else if ($('.verDocuRefe::checked').val() == 'OV')
                            baseurl = base_url + 'index.php/compras/ocompra/ventana_muestra_oventa';

                        $('.verDocuRefe::checked').attr('href', baseurl);
                    }
                }

            }
        });

    });

    $(function () {
        $("#nombre_cliente").autocomplete({
            //flag = $("#flagBS").val();
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
                //$("#nombre_cliente").val(ui.item.codinterno);
                $("#buscar_cliente").val(ui.item.ruc);
                $("#cliente").val(ui.item.codigo);
                $("#ruc_cliente").val(ui.item.ruc);
                $("#buscar_producto").focus();
                codigo=ui.item.codigo;
                    get_obra(codigo);
            },
            minLength: 3
        });

        //****** nuevo para ruc
        $("#buscar_cliente").autocomplete({
            //flag = $("#flagBS").val();
            source: function (request, response) {
                $.ajax({
                    url: "<?php echo base_url(); ?>index.php/empresa/cliente/autocomplete_ruc/",
                    type: "POST",
                    data: {
                        term: $("#buscar_cliente").val()
                    },
                    dataType: "json",
                    success: function (data) {
                        response(data);
                    }
                });
            },
            select: function (event, ui) {
                //$("#nombre_cliente").val(ui.item.codinterno);
                $("#nombre_cliente").val(ui.item.nombre);
                $("#cliente").val(ui.item.codigo);
                $("#ruc_cliente").val(ui.item.ruc);
                $("#buscar_producto").focus();
                codigo=ui.item.codigo;
                    get_obra(codigo);
            },
            minLength: 4
        });

        $("#nombre_proveedor").autocomplete({
            //flag = $("#flagBS").val();
            source: function (request, response) {
                $.ajax({
                    url: "<?php echo base_url(); ?>index.php/empresa/proveedor/autocompletado/",
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
                //$("#nombre_proveedor").val(ui.item.codinterno);
                $("#buscar_proveedor").val(ui.item.ruc);
                $("#proveedor").val(ui.item.codigo);
                $("#ruc_proveedor").val(ui.item.ruc);
                $("#buscar_producto").focus();
            },

            minLength: 3

        });

        //****** nuevo para ruc PROVEEDOR
        $("#buscar_proveedor").autocomplete({
            //flag = $("#flagBS").val();
            source: function (request, response) {
                $.ajax({
                    url: "<?php echo base_url(); ?>index.php/empresa/proveedor/autocompletado_ruc/",
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
                //$("#nombre_cliente").val(ui.item.codinterno);
                $("#nombre_proveedor").val(ui.item.nombre);
                $("#proveedor").val(ui.item.codigo);
                $("#ruc_proveedor").val(ui.item.ruc);
                $("#buscar_producto").focus();
            },
            minLength: 4
        });

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
                if(tipo_oper == 'C'){
                    $("#buscar_producto").val(ui.item.codinterno);
                    $("#producto").val(ui.item.codigo);
                    $("#codproducto").val(ui.item.codinterno);
                    $("#costo").val(ui.item.pcosto);
                    $("#stock").val(ui.item.stock);
                    $("#flagGenInd").val(ui.item.flagGenInd);
                    $("#almacenProducto").val(ui.item.almacenProducto);
                    $("#cantidad").focus();
                    
                    listar_unidad_medida_producto(ui.item.codigo);   
                    
                    if(ui.item.PROD_FlagBienServicio == 'B') verificar_Inventariado_producto();
                }else{
                    var isEncuentra = false;
                    if(ui.item.PROD_FlagBienServicio == 'B') isEncuentra = verificarProductoDetalle(ui.item.codigo,ui.item.almacenProducto);

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
                            if(ui.item.PROD_FlagBienServicio == 'B') verificar_Inventariado_producto();
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
                }

            },
            minLength: 2
        });

        $('#buscar_proveedor').click(function () {
            limpiar_campos_necesarios();
        });
        $('#nombre_proveedor').click(function () {
            limpiar_campos_necesarios();
        });
        $('#buscar_cliente').click(function () {
            limpiar_campos_necesarios();
        });
        $('#nombre_cliente').click(function () {
            limpiar_campos_necesarios();
        });
$('#close').click(function(){
        $('#popup').fadeOut('slow');
        $('.popup-overlay').fadeOut('slow');
        return false;
    });
    });

    function limpiar_campos_necesarios() {
        // Proveedor
        $('#buscar_proveedor').val("");
        $('#proveedor').val("");
        $('#ruc_proveedor').val("");
        $('#nombre_proveedor').val("");
        // Cliente
        $('#cliente').val("");
        $('#buscar_cliente').val("");
        $('#ruc_cliente').val("");
        $('#nombre_cliente').val("");
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

}function ejecutarModal(){
    $("#buscar_producto").val("").focus();
    $('#popup').fadeOut('slow');
    $('.popup-overlay').fadeOut('slow');
    return false;
}
    function seleccionar_cliente(codigo, ruc, razon_social, empresa, persona) {
        $("#cliente").val(codigo);
        $("#ruc_cliente").val(ruc);
        $("#nombre_cliente").val(razon_social);
        get_obra(codigo);
        if (empresa != '0') {
            if (empresa != $('#empresa').val()) {
                limpiar_combobox('contacto');
                $('#empresa').val(empresa);
                $('#persona').val(0);
                listar_contactos(empresa);
            }
        }
        else {
            limpiar_combobox('contacto');
            $('#linkVerPersona').hide();
            if (persona != $('#persona').val()) {
                $('#empresa').val(0);
                $('#persona').val(persona);
            }
        }
    }
    function seleccionar_proveedor(codigo, ruc, razon_social, empresa, persona, ctactesoles, ctactedolares) {
        $("#proveedor").val(codigo);
        $("#ruc_proveedor").val(ruc);
        $("#buscar_proveedor").val(ruc);
        $("#nombre_proveedor").val(razon_social);

        if (empresa != '0') {
            if (empresa != $('#empresa').val()) {
                $('#empresa').val(empresa);
                $('#persona').val(0);
                listar_contactos(empresa);
            }
        }
        else {
            if (persona != $('#persona').val()) {
                $('#empresa').val(0);
                $('#persona').val(persona);
            }
        }
        $('#ctactesoles').val(ctactesoles);
        $('#ctactedolares').val(ctactedolares);
    }
    function seleccionar_producto(codigo, interno, familia, stock, costo, flagGenInd) {
        $("#producto").val(codigo);
        $("#codproducto").val(interno);
        $("#cantidad").focus();
        $("#stock").val(stock);
        $("#costo").val(costo);
        $("#flagGenInd").val(flagGenInd);
        listar_unidad_medida_producto(codigo);
    }

    function seleccionar_ocompra(guia, serie, numero) {
        agregar_todoocompra(guia);
        tipo_oper = '<?php echo $tipo_oper; ?>';
        serienumero = "Numero de ocompra :" + serie + " - " + numero;
        $("#serieguiaverOC").html(serienumero);
        $("#serieguiaverOC").show(200);
        $("#serieguiaverPre").hide(200);
        if (tipo_oper == 'V'){
            codigoPresupuesto=$("#presupuesto").val();
            if(codigoPresupuesto!="" && codigoPresupuesto!=0){
                modificarTipoSeleccionPrersupuesto(codigoPresupuesto,0);
            }
            $("#presupuesto").val(0);
        }    
        //else
        //$("#cotizacion").val(0);

        

    }

    function seleccionar_oventa(guia, serie, numero) {
        agregar_todooventa(guia, serie, numero);

        tipo_oper = '<?php echo $tipo_oper; ?>';
        serienumero = "Numero de venta : " + serie + " - " + numero;
        $("#serieguiaverOC").html(serienumero);
        $("#serieguiaverOC").show(200);
        $("#serieguiaverPre").hide(200);
        $('#venta').val(guia);
    }

    function seleccionar_presupuesto(guia, serieguia, numeroguia) {
        isRealizado=modificarTipoSeleccionPrersupuesto(guia,1);
        if(isRealizado){
            tipo_oper = '<?php echo $tipo_oper; ?>';
            agregar_todopresupuesto(guia, tipo_oper);
            serienumero = "Numero de PRESUPUESTO :" + serieguia + " - " + numeroguia;
            $("#serieguiaverPre").html(serienumero);
            $("#serieguiaverPre").show(200);
            $("#serieguiaverOC").hide(200);
            if (tipo_oper == 'V'){
                codigoPresupuesto=$("#presupuesto").val();
                if(codigoPresupuesto!="" && codigoPresupuesto!=0){
                    modificarTipoSeleccionPrersupuesto(codigoPresupuesto,0);
                }
                $("#presupuesto").val(guia);
            }    
            //else
            //$("#cotizacion").val(guia);
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
        });
    }
</script>

<form id="frmOcompra" id="<?php echo $formulario; ?>" method="post" action="<?php echo $url_action; ?>"
      onsubmit="return valida_ocompra();">
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
    <input name="compania" type="hidden" id="compania" value="<?php echo $compania; ?>">
    <input name="compania" type="hidden" id="tipo_oper" value="<?php echo $tipo_oper; ?>">

    <div id="zonaContenido" align="center">
        <div id="tituloForm" class="header"><?php echo $titulo; ?></div>
        <div id="frmBusqueda">
            <table class="fuente8" width="100%" cellspacing="0" cellpadding="5" border="0">
                <tr>
                    <td width="10%">N&uacute;mero</td>


                    <td width="48%" valign="middle">

                        <input name="codigo_usuario" id="codigo_usuario" type="hidden"
                               class="cajaGeneral cajaSoloLectura" size="5" maxlength="50"
                               value="<?php echo $codigo_usuario; ?>"/>
                        <input name="serie" id="serie" type="text" class="cajaGeneral cajaSoloLectura" size="5"
                               maxlength="50" value="<?php echo $serie; ?>"/>
                        <input name="numero" id="numero" type="text" class="cajaGeneral cajaSoloLectura" size="10"
                               maxlength="10" readonly="readonly" value="<?php echo $numero; ?>"/>
                        <input name="centro_costo" type="hidden" id="centro_costo" size="10" maxlength="10" value="1"/>


                        <input name="pedido" type="hidden" class="cajaPequena2" id="pedido" size="10" maxlength="10"
                               readonly="readonly" value="<?php echo $pedido; ?>"/>
                        <?php ?>
                        <a href="javascript:;" id="linkVerSerieNum">

                            <p class="factura"
                               style="display:none"><?php echo $serie_suger_oc . '-' . '00' . $numero_suger_oc ?>
                            </p>
                            <?php if ($modo == '') { ?>
                                <img src="<?php echo base_url(); ?>public/images/icons/flecha.png?=<?=IMG;?>" border="0"
                                     alt="Serie y número sugerido" title="Serie y número sugerido"/>
                            <?php } ?>
                        </a>
                        <?php ?>

                        <?php if($tipo_oper == 'C'): ?>
                        <span style="margin-left: 20px;" hidden><label><input <?php echo $igv == 0 ? 'checked' : '' ?> data-igv="<?php echo $igv == 0 ? $igv_db : $igv ?>" type="checkbox" id="chkImportacion"> <b>Importación</b></label></span>
                        <script>
                            function reasign_igv(igv) {
                                $.each($("#tblDetalleOcompra tbody tr"), function(i, elm) {
                                    document.getElementById("prodigv100["+i+"]").value = igv;
                                    document.getElementById("prodpu["+i+"]").focus();
                                    //document.getElementById("prodpu["+i+"]").blur();
                                });
                            }
                            $(document).ready(function () {
                                $("#chkImportacion").change(function() {
                                    var check = $(this),
                                        igv = check.data('igv'),
                                        isCheck = check.attr('checked');

                                    if(isCheck){
                                        $("#igv").val(0);
                                        reasign_igv(0);
                                    }else{
                                        $("#igv").val(igv);
                                        reasign_igv(igv);
                                    }

                                    $("#montoDescuento").css('display', isCheck ? '' : 'none');
                                }).trigger('change');
                            });
                        </script>
                    <?php endif; ?>

                    </td>


                    <td width="10%" valign="middle">
                        <?php if ($tipo_oper == 'V') { ?>
                            <label for="P" hidden><img src="<?php echo base_url() ?>public/images/icons/presupuesto.png?=<?=IMG;?>"
                                                style="cursor:pointer;" class="imgBoton"/></label>
                            <input type="hidden" name="presupuesto" id="presupuesto">

                        <?php } else { ?>
                            <label for="P" hidden><img src="<?php echo base_url() ?>public/images/icons/cotizacion.png?=<?=IMG;?>"
                                                style="cursor:pointer;"
                                                class="imgBoton"/></label>
                            <!--<input type="hidden" name="cotizacion" id="cotizacion" >-->

                        <?php } ?>
                        <input type="radio" name="referenciar" id="P" value="P" href="javascript:;" class="verDocuRefe"
                               style="display:none;">
                        <div id="serieguiaverOC" name="serieguiaverOC" style="background-color: #cc7700; color:#fff; padding:5px;display:none"></div>
                        <div id="serieguiaverPre" name="serieguiaverPre"
                             style="background-color: #cc7700; color:#fff; padding:5px;display:none"></div>

                    </td>
                    <td width="5%">
                        <?php if ($tipo_oper == 'V') { ?>
                            <label for="O" hidden><img src="<?php echo base_url() ?>public/images/icons/docrecurrente.png?=<?=IMG;?>"
                                                style="cursor:pointer;"
                                                class="imgBoton"/></label>
                        <?php } else { ?>
                            <label for="O" hidden><img src="<?php echo base_url() ?>public/images/icons/docrecurrente.png?=<?=IMG;?>"
                                                style="cursor:pointer;"
                                                class="imgBoton"/></label>
                        <?php } ?>

                        <input type="radio" name="referenciar" id="O" value="O" href="javascript:;" class="verDocuRefe"
                               style="display:none;">
                    </td>
                    <td width="20%">Fecha
                        <input NAME="fecha" id="fecha" type="text" class="cajaGeneral cajaSoloLectura"
                               value="<?php echo $hoy; ?>" size="10" maxlength="10" readonly="readonly"/>
                        <img height="16" border="0" width="16" id="Calendario1" name="Calendario1"
                             src="<?php echo base_url(); ?>public/images/icons/calendario.png?=<?=IMG;?>"/>
                        <script type="text/javascript">
                            Calendar.setup({
                                inputField: "fecha",      // id del campo de texto
                                ifFormat: "%d/%m/%Y",       // formaClienteto de la fecha, cuando se escriba en el campo de texto
                                button: "Calendario1"   // el id del botón que lanzará el calendario
                            });
                        </script>
                    </td>
                </tr>
                <tr>
                    <?php if ($tipo_oper == 'V') { ?>
                        <td>Cliente *</td>
                        <td valign="middle">
                            <input type="hidden" name="cliente" id="cliente" size="5" value="<?php echo $cliente ?>"/>
                            <input name="buscar_cliente" type="text" class="cajaGeneral" id="buscar_cliente" size="10"
                                   placeholder="Ruc" value="<?php echo $ruc_cliente; ?>"
                                   title="Ingrese parte del nombre o el nro. de documento, luego presione ENTER."/>&nbsp;
                            <input type="hidden" name="ruc_cliente" class="cajaGeneral" id="ruc_cliente" size="10"
                                   maxlength="11" onblur="obtener_cliente();" value="<?php echo $ruc_cliente; ?>"
                                   onkeypress="return numbersonly(this,event,'.');"/>
                            <input type="text" name="nombre_cliente" class="cajaGeneral" id="nombre_cliente" size="40"
                                   placeholder="Nombre cliente"
                                   maxlength="50" value="<?php echo $nombre_cliente; ?>"/>
                          
                            <a href="<?php echo base_url(); ?>index.php/empresa/cliente/ventana_selecciona_cliente/"
                               id="linkSelecCliente"></a>
                        </td>
                    <?php } else { ?>
                        <td>Proveedor *</td>
                        <td valign="middle">
                            <input type="hidden" name="proveedor" id="proveedor" size="5"
                                   value="<?php echo $proveedor ?>"/>
                            <input name="buscar_proveedor" type="text" class="cajaGeneral" id="buscar_proveedor"
                                   placeholder="Ruc"
                                   size="10" value="<?php echo $ruc_proveedor; ?>"
                                   title="Ingrese parte del nombre o el nro. de documento, luego presione ENTER."/>&nbsp;
                            <input type="hidden" name="ruc_proveedor" class="cajaGeneral" id="ruc_proveedor" size="10"
                                   maxlength="11" onblur="obtener_proveedor();" value="<?php echo $ruc_proveedor; ?>"
                                   onkeypress="return numbersonly(this,event,'.');"/>
                            <input type="text" name="nombre_proveedor" class="cajaGeneral" id="nombre_proveedor"
                                   placeholder="Nombre proveedor"
                                   size="35" maxlength="50" value="<?php echo $nombre_proveedor; ?>"/>
                         
                            <a href="<?php echo base_url(); ?>index.php/empresa/proveedor/ventana_selecciona_proveedor/"
                               id="linkSelecProveedor"></a>
                        </td>
                    <?php } ?>
                    <td>
                        <?php if($tipo_oper == 'C'): ?>
                        <label for="OV">
                            <img src="<?php echo base_url() ?>public/images/icons/oventa.png?=<?=IMG;?>" style="cursor:pointer;" class="imgBoton"/>
                            <input type="radio" name="referenciar" id="OV" value="OV" href="javascript:;" class="verDocuRefe"
                               style="display:none;">
                        </label>
                    <?php endif; ?>
                    </td>
                    <td align="right">Moneda *</td>
                    <td>
                        <select name="moneda" id="moneda" class="comboMedio" onchange="seleccionarMoneda(event)">
                            <?php echo $cboMoneda; ?>
                        </select>
                        <script>
                            function seleccionarMoneda(event) {
                                var id = event.target.value;

                                $("#tdc-opcional").css('display', id == 4 ? '' : 'none');
                            }

                            $(function() {
                                $("#moneda").trigger('change');
                            });

                        </script>
                    </td>
                </tr>
                <tr>
                    <td>TDC</td>
                    <td colspan="2">
                        <input type="text" name="tdcDolar" class="cajaMinima cajaSoloLectura" readonly value="<?php echo $tdcDolar ?>">

                        <span id="tdc-opcional">
                            TDC Euro &nbsp;&nbsp;
                            <input type="text" name="tdcEuro" class="cajaMinima" value="<?php echo $tdcEuro ?>">
                        </span>
                    </td>
                  <!--td valign="middle"><?php //if ($tipo_oper == 'V') echo  'Comprador'; else echo ''; ?>
                      
                  </td> 
                    <td>
                        <?php
                        if ($tipo_oper == 'V') {
                            ?>
                            <select name="contacto" id="contacto"
                                    class="comboGrande"><?php //echo $cboContacto; ?></select>
                            <a href="<?php //echo base_url(); ?>index.php/maestros/persona/persona_ventana_mostrar/0/<?php echo $contacto; ?>" <?php //if ($contacto == '') echo 'style="display:none;"'; ?>
                               id="linkVerPersona"><img height='16' id="" width='16'
                                                        src='<?php //echo base_url(); ?>/images/ver.png?=<?=IMG;?>'
                                                        title='Más Información' border='0'/></a>
                        <?php
                        } else {
                            echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
                        }
                        ?>

                    </td>
                    <td></td-->
                    <td align="right">Forma Pago</td>
                    <td><?php echo $cboFormapago; ?></td>
                </tr>
                <!--tr>
                    <td>
                        
                    </td>
                    <?php if($tipo_oper == "C"): ?>
                        <td></td>
                    <td align="right">Descuento</td>
                    <td><input type="text" class="cajaGeneral" id="montoDescuento" onKeyPress="return numbersonly(this,event,'.');"></td>
                <?php endif; ?>
                </tr-->
                <tr>
                   
                    <td align="left">Almacen *
                    </td>
                    <td colspan="2">
                        <?php if ($tipo_oper == 'C'); ?>
                        <?php echo $cboAlmacen; ?>

                        <?php  if($tipo_oper == 'V') : ?>
                            Proyecto * : 
                            <select class='comboGrande'  id='obra' name="proyecto"><?php echo $cboObra;?></select>
                        <?php endif; ?>
                    </td>
                    <td>
                        I.G.V.
                        <input name="igv" type="text" class="cajaGeneral cajaSoloLectura" readonly="readonly" size="2"
                               maxlength="2" id="igv" maxlength="10" value="<?php echo $igv; ?>"
                               onKeyPress="return numbersonly(this,event,'.');" onBlur="modifica_igv_total();"/>
                    </td>
                    <td width="8%">
                        %
                        Dscto
                        <input name="descuento" type="text" class="cajaGeneral" id="descuento" size="2" maxlength="2"
                               value="<?php echo $descuento; ?>" onKeyPress="return numbersonly(this,event,'.');"
                               onBlur="modifica_descuento_total();"/> %
                    </td>
                </tr>
            </table>
        </div>
        <div id="frmBusqueda" class="divBusqueda">
            <table class="fuente8" width="100%" cellspacing='0' cellpadding='3' border='0'>
                <tr>
                    <td width="8%">
                        <select name="flagBS" id="flagBS" style="width:68px;" class="comboMedio"
                                onchange="limpiar_campos_producto()">
                            <option value="S" title="Servicio" selected>S</option>
                        </select>
                    </td>
                    <td width="37%">
                        <input name="producto" type="hidden" class="cajaGeneral" id="producto"/>
                        <input name="buscar_producto" type="text" class="cajaGeneral" id="buscar_producto" size="10"
                               placeholder="producto"/>&nbsp;
                        <input name="codproducto" type="hidden" class="cajaGeneral" id="codproducto" size="10"
                               maxlength="20" onblur="obtener_producto();"/>
                        <input NAME="nombre_producto" type="text" class="cajaGeneral cajaSoloLectura"
                               placeholder="Descripcion producto"
                               id="nombre_producto" size="40" readonly="readonly"/>
                        <input name="stock" type="hidden" id="stock"/>
                        <input name="costo" type="hidden" id="costo"/>
                        <input name="flagGenInd" type="hidden" id="flagGenInd"/>
                        <input name="almacenProducto" type="hidden" id="almacenProducto"/>
                        
                        <a href="<?php echo base_url(); ?>index.php/almacen/producto/ventana_selecciona_producto/"
                           id="linkSelecProducto"></a>
                    </td>
                    <td width="6%">Cantidad</td>
                    <td width="24%">
                        <input NAME="cantidad" type="text" class="cajaGeneral" id="cantidad" value="" size="3"
                               maxlength="5" onKeyPress="return numbersonly(this,event,'.');"/>
                        <select name="unidad_medida" id="unidad_medida"
                                class="comboMedio"  <?php if ($tipo_oper == 'V') echo 'onchange="obtener_precio_producto();"'; ?>>
                            <option value="0">::Seleccione::</option>
                        </select>
                    </td>
                    <td width="16%">
                        <select name="precioProducto" id="precioProducto" class="comboPequeno"
                                onchange="mostrar_precio();" style="width:84px;">
                            <option value="0">::Seleccion::</option>
                        </select>
                        <input NAME="precio" type="text" class="cajaGeneral" id="precio" size="5" maxlength="10"
                               onkeypress="return numbersonly(this,event,'.');" title="Precio con IGV"/>
                    </td>
                    <td width="10%">
                        <div align="right"><a href="javascript:;" onClick="agregar_producto_ocompra();"><img
                                    src="<?php echo base_url(); ?>public/images/icons/botonagregar.jpg?=<?=IMG;?>" class="imgBoton"
                                    align="absbottom"></a></div>
                    </td>
                </tr>
            </table>
        </div>
        <div id="frmBusqueda" style="height:250px; overflow: auto">
            <table class="fuente8" width="100%" cellspacing="0" cellpadding="3" border="1" ID="Table1">
                <tr class="cabeceraTabla">
                    <td width="3%">
                        <div align="center">&nbsp;</div>
                    </td>
                    <td width="4%">
                        <div align="center">ITEM</div>
                    </td>
                    <td width="10%">
                        <div align="center">C&Oacute;DIGO</div>
                    </td>
                    <td>
                        <div align="center">DESCRIPCI&Oacute;N</div>
                    </td>
                    <td width="10%">
                        <div align="center">CANTIDAD</div>
                    </td>
                    <td width="6%">
                        <div align="center">PU C/IGV</div>
                    </td>
                    <td width="6%">
                        <div align="center">PU S/IGV</div>
                    </td>
                    <td width="6%">
                        <div align="center">PRECIO</div>
                    </td>
                    <td width="6%">
                        <div align="center">I.G.V.</div>
                    </td>
                    <td width="6%">
                        <div align="center">IMPORTE</div>
                    </td>
                </tr>
            </table>

            <div>
                <table id="tblDetalleOcompra" class="fuente8" width="100%" border="0">
                    <?php
                    if (count($detalle_oservicio) > 0) {
                        $colors = array("#FFFFFF");
                        foreach ($detalle_oservicio as $indice => $valor) {
                            /*if(!is_null($valor->OCOMP_Codigo_referencia) && !isset($colors[$valor->OCOMP_Codigo_referencia])) {
                                $colors[$valor->OCOMP_Codigo_referencia] = '#' . str_pad(dechex(mt_rand(0, 0xFFFFFF)), 6, '0', STR_PAD_LEFT);
                            }*/

                            $detacodi = $valor->OCOMDEP_Codigo;
                            $flagBS = $valor->flagBS;
                            $prodproducto = $valor->PROD_Codigo;
                            $unidad_medida = $valor->UNDMED_Codigo;
                            $codigo_interno = $valor->PROD_CodigoInterno;
                            $nombre_producto = $valor->PROD_Nombre;
                            $nombre_unidad = $valor->UNDMED_Simbolo;
                            $flagGenInd = $valor->OCOMDEC_GenInd;
                            $costo = $valor->OCOMDEC_Costo;
                            $prodcantidad = $valor->OCOMDEC_Cantidad;
                            $prodpu = $valor->OCOMDEC_Pu;
                            $prodsubtotal = $valor->OCOMDEC_Subtotal;
                            $prodpu_conigv = $valor->OCOMDEC_Pu_ConIgv;
                            $proddescuento = $valor->OCOMDEC_Descuento;
                            $proddescuento2 = $valor->OCOMDEC_Descuento2;
                            $prodigv = $valor->OCOMDEC_Igv;
                            $prodtotal = $valor->OCOMDEC_Total;
                            $pendiente = $valor->OCOMDEC_Pendiente;
                            if (($indice + 1) % 2 == 0) {
                                $clase = "itemParTabla";
                            } else {
                                $clase = "itemImparTabla";
                            }
                            ?>
                            <tr id="ov_prod_id_<?php echo $valor->OCOMP_Codigo_venta ?>" class="tooltiped <?php echo $clase; ?> det_prod_id_<?php echo $prodproducto ?>" data-toggle="tooltip" data-placement="top" title="<?php echo $valor->PROYP_Codigo != 0  ? ("Proyecto : " . $valor->PROYC_Nombre) : (($tipo_oper == 'C' ? 'Cliente : ' : 'Proveedor : ') . $valor->RazonSocial) ?>">
                                <td width="3%">
                                    <div align="center">
                                    <?php if($pendiente == $prodcantidad && $prodcantidad == $valor->OCOMDEC_Pendiente_pago): ?>
                                    <font color="red">
                                        <strong>
                                            <a href="javascript:;" onClick="eliminar_producto_oservicio(<?php echo $indice ?>)">
                                                <span style="border:1px solid red;background: #ffffff;">&nbsp;X&nbsp;</span>
                                            </a>
                                        </strong>
                                    </font>
                                    <?php endif; ?>
                                    </div>
                                </td>
                                <td width="4%">
                                    <div align="center"><?php echo $indice + 1; ?></div>
                                </td>
                                <td width="10%" style="border-left: 10px solid transparent">
                                    <div align="center">
                                        <?php echo $codigo_interno; ?>
                                        <input type="hidden" class="cajaMinima prodcodigo"
                                               name="prodcodigo[<?php echo $indice; ?>]"
                                               id="prodcodigo[<?php echo $indice; ?>]"
                                               value="<?php echo $prodproducto; ?>"/>
                                        <input type="hidden" class="cajaMinima"
                                               name="produnidad[<?php echo $indice; ?>]"
                                               id="produnidad[<?php echo $indice; ?>]"
                                               value="<?php echo $unidad_medida; ?>"/>

                                        <input type="hidden" name="prodobservacion[<?php echo $indice ?>]" id="prodobservacion_<?php echo $indice ?>" value="<?php echo $valor->OCOMDEC_Observacion ?>">
                                       
                                    </div>
                                </td>
                                <td>
                                    <div align="left">
                                        <img src="<?php echo base_url() ?>public/images/icons/ver_detalle.png?=<?=IMG;?>" style="cursor:pointer;margin-right: 5px;" onclick="llenarObservacion(<?php echo $indice ?>)">
                                        <input type="text" class="cajaGeneral cajaSoloLectura" style="width:369px;" maxlength="250"
                                               name="proddescri[<?php echo $indice; ?>]"
                                               id="proddescri[<?php echo $indice; ?>]"
                                               value="<?php echo $nombre_producto; ?>" readonly/>
                                    </div>
                                </td>
                                <td width="10%">
                                    <div align="left">

                                            <?php if ($tipo_oper == 'V'): ?>
                                            <input type="text" class="cajaGeneral" size="1" maxlength="5" style="text-align:right"
                                               name="prodcantidad[<?php echo $indice; ?>]"
                                               id="prodcantidad[<?php echo $indice; ?>]"
                                               value="<?php echo $prodcantidad; ?>"
                                             
                                               onchange="modificar_cantidad('<?php echo $indice; ?>');"
                                               onblur="calcula_importe('<?php echo $indice; ?>');calcula_totales();"
                                               onKeyPress="return numbersonly(this,event,'.');"/> <?php echo $nombre_unidad; ?>
                                            <input type="hidden" size="1"
                                               name="cantreal[<?php echo $indice; ?>]"
                                               id="cantreal[<?php echo $indice; ?>]"
                                               value="<?php echo $prodcantidad; ?>"/>   
                                            <?php else: ?>
                                                 <input type="text" class="cajaGeneral" size="1" maxlength="5" style="text-align:right"
                                               name="prodcantidad[<?php echo $indice; ?>]"
                                               id="prodcantidad[<?php echo $indice; ?>]"
                                               value="<?php echo $prodcantidad; ?>"
                                             
                                               onchange="calcula_cantidad(<?php echo $indice; ?>);"
                                               onblur="calcula_importe('<?php echo $indice; ?>');calcula_totales();"
                                               onKeyPress="return numbersonly(this,event,'.');"/> <?php echo $nombre_unidad; ?>
                                               <?php  
                                               endif;
                                               ?>  

                                        
                                    </div>
                        <input type="hidden" name="pendiente[<?php echo $indice; ?>]" id="pendiente[<?php echo $indice; ?>]" 
                        value="<?php echo $pendiente; ?>" >
                                </td>
                                <td width="6%">
                                    <div align="center"><input type="text" size="5" maxlength="10" class="cajaGeneral cajaSoloLectura" style="text-align:right"
                                                               name="prodpu_conigv[<?php echo $indice; ?>]"
                                                               id="prodpu_conigv[<?php echo $indice; ?>]"
                                                               value="<?php echo str_replace(",", "", number_format($prodpu_conigv, 2)); ?>"
                                                               onblur="modifica_pu_conigv(<?php echo $indice; ?>);"
                                                               onkeypress="return numbersonly(this,event,'.');" readonly/></div>
                                </td>
                                <td width="6%">
                                    <div align="center"><input type="text" size="5" maxlength="10" class="cajaGeneral pu" style="text-align:right"
                                                               name="prodpu[<?php echo $indice; ?>]"
                                                               id="prodpu[<?php echo $indice; ?>]"
                                                               value="<?php echo str_replace(",", "", number_format($prodpu, 2)); ?>"
                                                               onblur="modifica_pu(<?php echo $indice; ?>);"
                                                               onkeypress="return numbersonly(this,event,'.');"
                                                               onchange="igualarPrecioUnitario()" />
                                        <td width="6%">
                                            <div align="center"><input type="text" size="5" maxlength="10" style="text-align:right"
                                                                       class="cajaGeneral cajaSoloLectura"
                                                                       name="prodprecio[<?php echo $indice; ?>]"
                                                                       id="prodprecio[<?php echo $indice; ?>]"
                                                                       value="<?php echo str_replace(",", "", number_format($prodsubtotal, 2)); ?>"
                                                                       readonly="readonly"/></div>
                                        </td>
                                        <td width="6%"> 
                                            <div align="center"><input type="text" size="5" maxlength="10" style="text-align:right"
                                                                       class="cajaGeneral cajaSoloLectura"
                                                                       name="prodigv[<?php echo $indice; ?>]"
                                                                       id="prodigv[<?php echo $indice; ?>]"
                                                                       readonly="readonly"
                                                                       value="<?php echo str_replace(",", "", number_format($prodigv, 2)); ?>"/></div>
                                        </td>
                                        <td width="6%">
                                            <div align="center">
                                                <input type="hidden" class="cajaMinima" 
                                                name="flagGenIndDet[<?php echo $indice; ?>]" id="flagGenIndDet[<?php echo $indice; ?>]" 
                                                value="<?php echo $flagGenInd;?>">
                                                <input type="hidden" name="detaccion[<?php echo $indice; ?>]"
                                                       id="detaccion[<?php echo $indice; ?>]" value="m"/>
                                                <input type="hidden" name="prodigv100[<?php echo $indice; ?>]"
                                                       id="prodigv100[<?php echo $indice; ?>]"
                                                       value="<?php echo $igv; ?>"/>
                                                <input type="hidden" name="detacodi[<?php echo $indice; ?>]"
                                                       id="detacodi[<?php echo $indice; ?>]"
                                                       value="<?php echo $detacodi; ?>"/>
                                                <input type="hidden" name="prodstock[<?php echo $indice; ?>]"
                                                       id="prodstock[<?php echo $indice; ?>]" value=""/>
                                                <input type="hidden" name="prodcosto[<?php echo $indice; ?>]"
                                                       id="prodcosto[<?php echo $indice; ?>]"
                                                       value="<?php echo $costo; ?>"/>
                                                <input type="hidden" name="proddescuento100[<?php echo $indice; ?>]"
                                                       id="proddescuento100[<?php echo $indice; ?>]"
                                                       value="<?php echo $descuento; ?>"/>
                                                <input type="hidden" name="oventacod[<?php echo $indice; ?>]"
                                                       id="oventacod[<?php echo $indice; ?>]"
                                                       value="<?php echo $valor->OCOMP_Codigo_venta; ?>"/>
                                                <input type="hidden" name="proddescuento[<?php echo $indice; ?>]"
                                                       id="proddescuento[<?php echo $indice; ?>]"
                                                       value="<?php echo $proddescuento; ?>"
                                                       onblur="calcula_importe2(<?php echo $indice; ?>);"/>
                                                <input type="text" size="5" maxlength="10" style="text-align:right"
                                                       class="cajaGeneral cajaSoloLectura"
                                                       name="prodimporte[<?php echo $indice; ?>]"
                                                       id="prodimporte[<?php echo $indice; ?>]" readonly="readonly"
                                                       value="<?php echo str_replace(",", "", number_format($prodtotal, 2)); ?>"/>
                                                <input type="hidden" name="almacenProducto[<?php echo $indice ?>]" id="almacenProducto[<?php echo $indice ?>]" value="0"/>
                                            </div>
                                        </td>
                            </tr>
                        <?php
                        }
                    }
                    ?>
                </table>
            </div>
        </div>
        <div id="frmBusqueda3">
            <table border="0" align="center" cellpadding='3' cellspacing='0' class="fuente8" style="position: relative">
                <tr>
                    <td width="90%">
                        <table border="0" align="left" cellpadding='3' cellspacing='0' style="font: 8pt helvetica;"
                               width="100%">
                            <tr>
                                <td colspan="2" height="25"><b>INFORMACION DE LA ENTREGA </b></td>
                            </tr>
                            <tr>
                                <td width="10%">Lugar de entrega</td>
                                <td width="50%">
                                    <input type="text" id="envio_direccion" value="<?php echo $envio_direccion; ?>"
                                           name="envio_direccion" class="cajaGeneral" size="56" maxlength="250"/>
                                    <a href="javascript:;" id="linkVerDirecciones">
                                        <img src="<?php echo base_url(); ?>public/images/icons/ver.png?=<?=IMG;?>" border="0"/>
                                    </a>

                                    <div id="lista_direcciones" class="cuadro_flotante"
                                         style="width:305px; height:100px;">
                                        <ul></ul>
                                    </div>
                                </td>
                                <td>Fecha límite entrega</td>
                                <td><input NAME="fechaentrega" id="fechaentrega" type="text" class="cajaGeneral"
                                           value="<?php echo $fechaentrega; ?>" size="10" maxlength="10"/>
                                    <img height="16" border="0" width="16" id="Calendario2" name="Calendario2"
                                         src="<?php echo base_url(); ?>public/images/icons/calendario.png?=<?=IMG;?>"/>
                                    <script type="text/javascript">
                                        Calendar.setup({
                                            inputField: "fechaentrega",      // id del campo de texto
                                            ifFormat: "%d/%m/%Y",       // formato de la fecha, cuando se escriba en el campo de texto
                                            button: "Calendario2"   // el id del botón que lanzará el calendario
                                        });
                                    </script>
                                </td>
                            </tr>
                            <tr>
                                <td>Facturar en</td>
                                <td><input type="text" id="fact_direccion" value="<?php echo $fact_direccion; ?>"
                                           name="fact_direccion" class="cajaGeneral" size="56" maxlength="250"/>
                                    <a href="javascript:;" id="linkVerDirecciones_fact">
                                        <img src="<?php echo base_url(); ?>public/images/icons/ver.png?=<?=IMG;?>" border="0"/>
                                    </a>

                                    <div id="lista_direcciones_fact" class="cuadro_flotante"
                                         style="width:305px; height:100px;">
                                        <ul></ul>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>Plazo de entrega</td>
                               <td> <textarea name="tiempo_entrega" id="tiempo_entrega" class="cajaTextArea"><?php echo$tiempo_entrega?></textarea>
                                </td>
                            </tr>
                            <tr>
                                <td height="25"><b>ESTADO</b></td>
                                <td><select name="estado" id="estado" class="comboPequeno">
                                        <option <?php if ($estado == '1') echo 'selected="selected"'; ?> value="1">
                                            Activo
                                        </option>
                                        <option <?php if ($estado == '0') echo 'selected="selected"'; ?> value="0">
                                            Anulado
                                        </option>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td><b>CTA. CTE.</b></td>
                                <td>&nbsp;</td>
                            </tr>
                            <tr>
                                <td>Cta. Cte. S/.</td>
                                <td><input name="ctactesoles" type="text" class="cajaGeneral" size="18" maxlength="50"
                                           id="ctactesoles" value="<?php echo $ctactesoles; ?>"/>
                                    Cta. Cte. US$ <input name="ctactedolares" type="text" class="cajaGeneral" size="18"
                                                         maxlength="50" id="ctactedolares"
                                                         value="<?php echo $ctactedolares; ?>"/></td>
                            </tr>
                            <tr>
                                <td height="25" colspan="4"><b>OBSERVACION</b></td>
                            </tr>
                            <tr>
                                <td colspan="4" valign="top"><textarea id="observacion" name="observacion"
                                                                       class="cajaTextArea" style="width:97%"
                                                                       rows="4"><?php echo $observacion; ?></textarea>
                                </td>
                            </tr>
                        </table>
                    </td>
                    <td>
                        <table width="100%" border="0" align="top" cellpadding='3' cellspacing='0' class=""
                               style="margin-top:-100px;">
                            <tr>
                                <td>Sub-total</td>
                                <td width="10%" align="top">
                                    <div align="right"><input class="cajaTotales" name="preciototal" type="text"
                                                              id="preciototal" size="12" align="right"
                                                              readonly="readonly"
                                                              value="<?php echo round($preciototal, 2); ?>"/></div>
                                </td>
                            </tr>
                            <tr>
                                <td class="busqueda">Descuento</td>
                                <td align="right">
                                    <div align="right"><input class="cajaTotales" name="descuentotal" type="text"
                                                              id="descuentotal" size="12" align="right"
                                                              readonly="readonly"
                                                              value="<?php echo round($descuentotal, 2); ?>"/></div>
                                </td>
                            </tr>
                            <tr>
                                <td class="busqueda">IGV</td>
                                <td align="right">
                                    <div align="right"><input class="cajaTotales" name="igvtotal" type="text"
                                                              id="igvtotal" size="12" align="right" readonly="readonly"
                                                              value="<?php echo round($igvtotal, 2); ?>"/></div>
                                </td>
                            </tr>
                            <tr>
                                <td class="busqueda">Precio Total</td>
                                <td align="right">
                                    <div align="right"><input class="cajaTotales" name="importetotal" type="text"
                                                              id="importetotal" size="12" align="right"
                                                              readonly="readonly"
                                                              value="<?php echo round($importetotal, 2); ?>"/></div>
                                </td>
                            </tr>
                            <tr>
                                <td class="busqueda">Percepci&oacute;n</td>
                                <td align="right">
                                    <div align="right"><input class="cajaTotales" name="percepciontotal" type="text"
                                                              id="percepciontotal" size="12" align="right"
                                                              readonly="readonly"
                                                              value="<?php echo round($percepciontotal, 2); ?>"/></div>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>

        </div>
        <br/>
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
    <div style="margin:10px 0 10px 0; clear:both">
        <img id="loading" src="<?php echo base_url(); ?>public/images/icons/loading.gif?=<?=IMG;?>" style="visibility: hidden"/>
        <?php if(($tipo_oper == 'C' && $terminado != '1')): ?>
        <a href="javascript:;" id="grabarOcompra"><img src="<?php echo base_url(); ?>public/images/icons/botonaceptar.jpg?=<?=IMG;?>"
     width="85" height="22" class="imgBoton"></a>
        <?php endif; ?>
        <a href="javascript:;" id="limpiarOcompra"><img src="<?php echo base_url(); ?>public/images/icons/botonlimpiar.jpg?=<?=IMG;?>"
        width="69" height="22" class="imgBoton"></a>
        <a href="javascript:;" id="cancelarOcompra"><img src="<?php echo base_url(); ?>public/images/icons/botoncancelar.jpg?=<?=IMG;?>"
           width="85" height="22" class="imgBoton"></a>
        <?php echo $oculto ?>
        <input type="hidden" name="ordenventa" id="ordenventa" value="<?php if(isset($ordenventa)) echo $ordenventa ?>">
        <input type="hidden" name="ordencompraventa" id="ordencompraventa" value="<?php //echo $ordencompraventa?>">
       </div>
    </div>
</form>

<div class="modal fade" id="descripcion-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Observación</h4>
      </div>
      <div class="modal-body">
        <textarea id="descripcion-producto" style="width: 100%" rows="10"></textarea>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-primary" id="save" onclick="guardarDescripcion()">Aceptar</button>
      </div>
    </div>
  </div>
</div>

<script>
    var colors = [];
    $(function() {
        <?php if($tipo_oper == 'C'): ?>
            $(".tooltiped").tooltip();

            colors = <?php echo json_encode(isset($colors) ? $colors : array()) ?>;
        <?php endif; ?>
    });
</script>