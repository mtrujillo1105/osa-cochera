<?php
$nombre_persona = $this->session->userdata('nombre_persona');
$persona = $this->session->userdata('persona');
$usuario = $this->session->userdata('usuario');
$url = base_url() . "index.php";
if (empty($persona)) header("location:$url");
$CI = get_instance();
?>
<html>
<head>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js" integrity="sha512-bLT0Qm9VnAYZDflyKcBaQ2gg0hSYNQrJ8RilYldYQ1FxQYoCLtUjuuRuZo+fjqhx/qtq/1itJ0C2ejDxltZVFg==" crossorigin="anonymous"></script>
    <!--script type="text/javascript" src="< ?php echo base_url(); ?>public/js/ventas/notacredito.js?=< ?=JS;?>"></script-->
    <link href="<?=base_url();?>public/js/fancybox/dist/jquery.fancybox.css?=<?=CSS;?>" rel="stylesheet">
    <script src="<?=base_url();?>public/js/fancybox/dist/jquery.fancybox.js?=<?=JS;?>"></script>

    <script type="text/javascript">
        var base_url = '<?=base_url();?>';
    </script>
    <script type="text/javascript" src="<?=base_url();?>public/js/nicEdit/nicEdit.js?=<?=JS;?>"></script>


    <script type="text/javascript">
        bkLib.onDomLoaded(function() {
            new nicEditor({fullPanel : true}).panelInstance('mensaje');
        });

        $(document).ready(function () {

            $('#ruc_cliente').click(function () {
                $('#nombre_cliente').val("");
                $('#ruc_cliente').val("");
            });

            $('#nombre_cliente').click(function () {
                $('#ruc_cliente').val("");
                $('#nombre_cliente').val("");
            });

            $('#codproducto').click(function () {
                $('#codproducto').val("");
                $('#nombre_producto').val("");
            });

            $("a#linkVerCliente, a#linkVerProveedor").fancybox({
                'width': 800,
                'height': 500,
                'autoScale': false,
                'transitionIn': 'none',
                'transitionOut': 'none',
                'showCloseButton': false,
                'modal': true,
                'type': 'iframe'
            });

            $("a#linkVerProducto").fancybox({
                'width': 800,
                'height': 500,
                'autoScale': false,
                'transitionIn': 'none',
                'transitionOut': 'none',
                'showCloseButton': false,
                'modal': true,
                'type': 'iframe'
            });

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
                    $("#buscar_cliente").val(ui.item.ruc)
                    $("#cliente").val(ui.item.codigo);
                    $("#ruc_cliente").val(ui.item.ruc);
                },

                minLength: 2

            });

            $("#nombre_proveedor").autocomplete({
                source: function (request, response) {
                    $.ajax({
                        url: "<?php echo base_url(); ?>index.php/empresa/proveedor/autocomplete/",
                        type: "POST",
                        data: {term: $("#nombre_proveedor").val()},
                        dataType: "json",
                        success: function (data) {
                            response(data);
                        }

                    });

                },
                select: function (event, ui) {
                    $("#buscar_proveedor").val(ui.item.ruc)
                    $("#proveedor").val(ui.item.codigo);
                    $("#ruc_proveedor").val(ui.item.ruc);
                },

                minLength: 2

            });

            $("#nombre_producto").autocomplete({
                //flag = $("#flagBS").val();
                source: function (request, response) {
                    $.ajax({
                        url: "<?php echo base_url(); ?>index.php/almacen/producto/autocomplete/B/" + $("#compania").val(),
                        type: "POST",
                        data: {
                            term: $("#nombre_producto").val()
                        },
                        dataType: "json",
                        success: function (data) {
                            response(data);
                        }
                    });
                },
                select: function (event, ui) {
                    $("#nombre_producto").val(ui.item.codinterno);
                    $("#producto").val(ui.item.codigo);
                    $("#codproducto").val(ui.item.codinterno);
                },
                minLength: 2
            });

        });
        function seleccionar_cliente(codigo, ruc, razon_social, empresa, persona) {
            $("#cliente").val(codigo);
            $("#ruc_cliente").val(ruc);
            $("#nombre_cliente").val(razon_social);
        }
        function seleccionar_proveedor(codigo, ruc, razon_social) {
            $("#proveedor").val(codigo);
            $("#ruc_proveedor").val(ruc);
            $("#nombre_proveedor").val(razon_social);
        }
        function seleccionar_producto(codigo, interno, familia, stock, costo) {
            $("#producto").val(codigo);
            $("#codproducto").val(interno);

            base_url = $("#base_url").val();
            url = base_url + "index.php/almacen/producto/listar_unidad_medida_producto/" + codigo;
            $.getJSON(url, function (data) {
                $.each(data, function (i, item) {
                    nombre_producto = item.PROD_Nombre;
                });
                $("#nombre_producto").val(nombre_producto);
            });
        }

        var cursor;
        if (document.all) {
            // Está utilizando EXPLORER
            cursor = 'hand';
        } else {
            // Está utilizando MOZILLA/NETSCAPE
            cursor = 'pointer';
        }   
    </script>
    
</head>

<!-- Cabecera -->
<div class="row">
    <div class="col-md-12">
        <div class="card card-light">
            
            <div class="card-header">
              <input name="compania" type="hidden" id="compania" value="<?php echo $compania; ?>">
              <h3 class="card-title"><?=$titulo_busqueda;?></h3>
              <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse" data-toggle="tooltip" title="Collapse">
                        <i class="fas fa-minus"></i>
                </button>
              </div>
            </div>   
            
            <div class="card-body">
                <form id="form_busqueda" name="form_busqueda" method="post" action="<?php echo base_url(); ?>index.php/ventas/notacredito/comprobantes/<?php echo $tipo_oper; ?>/<?php echo $tipo_docu; ?>">
                    <div class="row">
                        <div class="col-md-2 form-group">
                            <label for="fechai">Fecha inicial:</label>
                            <input id="fechai" name="fechai" type="date" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-2 form-group">
                            <label for="fechaf">Fecha final:</label>
                            <input id="fechaf" name="fechaf" type="date" class="form-control form-control-sm">
                        </div>
                        <div class="col-lg-1 form-group">
                            <label for="search_documento">Número</label>
                            <input type="text" name="numero" id="numero" value="" placeholder="Número" class="form-control form-control-sm" autocomplete="off"/>
                        </div>	
                        <div class="col-md-2 form-group">
                                <?php if ($tipo_oper == 'V') { ?>
                                        <label for="ruc_cliente">Ruc Cliente:</label>
                                        <input type="hidden" name="cliente" id="cliente" size="5"/>
                                        <input type="text" name="ruc_cliente" id="ruc_cliente" placeholder="Ruc" class="form-control form-control-sm" autocomplete="off" maxlength="11" onkeypress="return numbersonly(this, event, '.');"/>
                                <?php } else { ?>
                                        <label for="ruc_proveedor">Ruc Proveedor:</label>
                                        <input type="hidden" name="proveedor" id="proveedor" size="5"/>
                                        <input type="text" name="ruc_proveedor" id="ruc_proveedor" placeholder="Ruc" class="form-control form-control-sm" autocomplete="off" maxlength="11" onblur="obtener_proveedor();" onkeypress="return numbersonly(this, event, '.');"/>
                                <?php } ?>
                        </div>	
                        <div class="col-md-4 form-group">
                                <?php if ($tipo_oper == 'V') { ?>
                                        <label for="nombre_cliente">Razón Social:</label>
                                        <input type="text" name="nombre_cliente" id="nombre_cliente" placeholder="Nombre cliente" class="form-control form-control-sm" autocomplete="off" size="40"/>
                                <?php } else { ?>
                                        <label for="nombre_proveedor">Razón Social:</label>
                                        <input type="text" name="nombre_proveedor" id="nombre_proveedor" placeholder="Nombre proveedor" class="form-control form-control-sm" autocomplete="off" size="40"/>							
                                <?php } ?>
                        </div>	                        
                    </div>
                    <div class="row justify-content-end">
                        <div class="col-lg-5 form-group align-self-end text-right">						
                            <button type="button" class="btn btn-info" id="buscarC">Buscar</button>
                            <button type="button" class="btn btn-dark" id="limpiarC">Limpiar</button>
                            <button type="button" class="btn btn-success" id="nuevaComprobante" data-toggle='modal' data-target='#modal-envmail'>Nuevo</button>
                        </div>
                    </div>
                    <input type="hidden" id="iniciopagina" name="iniciopagina">
                    <?php echo $oculto ?>
                </form>
            </div>
            
        </div>
    </div>
</div>

<!-- Detalle -->
<div class="row">
    <div class="col-lg-12">
        <div class="card card-light">
            <div class="card-header">
              <h3 class="card-title"><?=$titulo_tabla;?></h3>
              <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse" data-toggle="tooltip" 
                        title="Collapse">
                        <i class="fas fa-minus"></i>
                </button>
              </div>
            </div>
            <div class="card-body">
                <table class="table table-striped table-bordered" id="table-notas">
                    <div id="cargando_datos" class="loading-table">
                        <img src="<?=base_url().'public/images/loading.gif';?>">
                    </div>                    
                    <thead>
                        <tr>                            
                            <th style="width:08%" data-orderable="true">FECHA</th>
                            <th style="width:05%" data-orderable="true">SERIE</th>
                            <th style="width:07%" data-orderable="true">NUMERO</th>
                            <th style="width:05%" data-orderable="true">ID CLIENTE</th>
                            <th style="width:25%" data-orderable="true">RAZON SOCIAL</th>
                            <th style="width:10%" data-orderable="true">CARGA</th>
                            <th style="width:07%" data-orderable="false">Doc. Origen</th>
                            <th style="width:07%" data-orderable="false">Doc. Destino</th>
                            <th style="width:08%" data-orderable="true">TOTAL</th>
                            <th style="width:2.2%" data-orderable="true"></th>
                            <th style="width:2.2%" data-orderable="false"></th>
                            <th style="width:2.2%" data-orderable="false"></th>
                            <th style="width:2.2%" data-orderable="false"></th>
                            <th style="width:2.2%" data-orderable="false"></th>
                            <th style="width:2.2%" data-orderable="false"></th>
                            <th style="width:4.8%" data-orderable="false"></th>                            
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<body>
<!--div id="pagina">
    <div id="zonaContenido">
        <div align="center">
            <div id="tituloForm" class="header">< ?php echo $titulo_busqueda; ?></div>


            <!--form id="form_busqueda" name="form_busqueda" method="post" action="<?php echo base_url(); ?>index.php/ventas/notacredito/comprobantes/< ?php echo $tipo_oper; ?>/<?php echo $tipo_docu; ?>">
                <!--div id="frmBusqueda">
                    <table class="fuente8" width="98%" cellspacing="3" cellpadding="3" border="0">
                        <!--tr>
                            <td align='left' width="10%">Fecha inicial</td>
                            <td align='left' width="90%">
                                <input name="fechai" id="fechai" value="" type="text" class="cajaGeneral" size="10" maxlength="10"/>
                                <img src="< ?php echo base_url(); ?>public/images/icons/calendario.png?=<?=IMG;?>" name="Calendario1" id="Calendario1" width="16" height="16" border="0" onMouseOver="this.style.cursor='pointer'" title="Calendario"/>
                                <script type="text/javascript">
                                    Calendar.setup({
                                        inputField: "fechai",      // id del campo de texto
                                        ifFormat: "%Y-%m-%d",       // formato de la fecha, cuando se escriba en el campo de texto
                                        button: "Calendario1"   // el id del botón que lanzará el calendario
                                    });
                                </script>
                                <label style="margin-left: 90px;">Fecha final</label>
                                <input name="fechaf" id="fechaf" value="" type="text" class="cajaGeneral" size="10" maxlength="10"/>
                                <img src="< ?php echo base_url(); ?>public/images/icons/calendario.png?=<?=IMG;?>" name="Calendario2" id="Calendario2" width="16" height="16" border="0" onMouseOver="this.style.cursor='pointer'" title="Calendario2"/>
                                <script type="text/javascript">
                                    Calendar.setup({
                                        inputField: "fechaf",      // id del campo de texto
                                        ifFormat: "%Y-%m-%d",       // formato de la fecha, cuando se escriba en el campo de texto
                                        button: "Calendario2"   // el id del botón que lanzará el calendario
                                    });
                                </script>
                            </td>
                        </tr-->
                        <!--tr>
                            <td align='left'>Número</td>
                            <td align='left'>
                                <input type="text" name="serie" id="serie" value="" class="cajaGeneral" size="3" maxlength="3" placeholder="Serie"/>
                                <input type="text" name="numero" id="numero" value="" class="cajaGeneral" size="10" maxlength="6" placeholder="Numero"/>
                            </td>
                        </tr-->
                        <!--tr>
                            < ?php if ($tipo_oper == 'V') { ?>
                                <td align='left'>Cliente</td>
                                <td align='left'>
                                    <input type="hidden" name="cliente" value="" id="cliente" size="5"/>
                                    <input type="text" name="ruc_cliente" value="" class="cajaGeneral" id="ruc_cliente" size="10" maxlength="11" onKeyPress="return numbersonly(this,event,'.');" placeholder="Ruc"/>
                                    <input type="text" name="nombre_cliente" value="" class="cajaGrande cajaSoloLectura" id="nombre_cliente" size="40" placeholder="Nombre cliente"/>
                                    <!--<a href="< ?php echo base_url(); ?>index.php/empresa/cliente/ventana_busqueda_cliente/" id="linkVerCliente">
                                        <img height='16' width='16' src='<?php echo base_url(); ?>/images/ver.png?=<?=IMG;?>' title='Buscar' border='0'/>
                                    </a>-->
                                <!--/td>
                            < ? php } else { ?>
                                <td align='left'>Proveedor</td>
                                <td align='left'>
                                    <input type="hidden" name="proveedor" value="" id="proveedor" size="5"/>
                                    <input type="text" name="ruc_proveedor" value="" class="cajaGeneral" id="ruc_proveedor" size="10" maxlength="11" placeholder="Ruc" onBlur="obtener_proveedor();" onKeyPress="return numbersonly(this,event,'.');"/>
                                    <input type="text" name="nombre_proveedor" value="" class="cajaGrande cajaSoloLectura" id="nombre_proveedor" size="40" placeholder="Nombre proveedor"/>
                                    <!--<a href="< ?php echo base_url(); ?>index.php/empresa/proveedor/ventana_busqueda_proveedor/" id="linkVerProveedor"><img height='16' width='16' src='<?php echo base_url(); ?>/images/ver.png?=<?=IMG;?>' title='Buscar' border='0'/></a>-->
                                <!--/td>
                            < ?php } ?>
                        </tr>
                        <!--<tr>
                            <td align='left'>Artículo</td>
                            <td align='left'>
                                <input name="producto" type="hidden" class="cajaPequena" id="producto" size="10" maxlength="11"/>
                                <input name="codproducto" type="text" value="" class="cajaPequena" id="codproducto" size="10" maxlength="11" placeholder="Codigo" onBlur="obtener_producto();" onKeyPress="return numbersonly(this,event,'.');"/>
                                <input NAME="nombre_producto" type="text" value="" class="cajaGrande cajaSoloLectura" id="nombre_producto" size="40" placeholder="Nombre producto"/>
                                <a href="< ?php echo base_url(); ?>index.php/almacen/producto/ventana_busqueda_producto/" id="linkVerProducto"><img height='16' width='16' src='<?php echo base_url(); ?>/images/ver.png?=<?=IMG;?>' title='Buscar' border='0'/></a>
                            </td>
                        </tr>-->
                    <!--/table>
                </div>
                <!--div class="acciones">
                    <div id="botonBusqueda">
                        Hola
                        <ul id="nuevaComprobante" class="lista_botones">
                            <li id="nuevo">Nota de < ?php echo ucwords($CI->obtener_tipo_documento($tipo_docu)); ?></li>
                        </ul>
                        <ul id="limpiarC" class="lista_botones">
                            <li id="limpiar">Limpiar</li>
                        </ul>
                        <ul id="buscarC" class="lista_botones">
                            <li id="buscar">Buscar</li>
                        </ul>
                    </div>
                    <div id="lineaResultado">
                        <table class="fuente7" width="100%" cellspacing="0" cellpadding="3" border="0">
                            <tr>
                                <td width="50%" align="left">N de notas de <?php echo $CI->obtener_tipo_documento($tipo_docu); ?>s encontrados:&nbsp;<?php echo $registros; ?> </td>
                        </table>
                    </div>
                </div-->
                <!--div id="cabeceraResultado" class="header">< ?php echo $titulo_tabla; ?></div-->
                <!--div id="frmResultado">
                    <table class="fuente8 display" width="100%" cellspacing="0" cellpadding="3" border="0">
                        <!--div id="cargando_datos" class="loading-table">
                            <img src="< ?=base_url().'images/loading.gif?='.IMG;?>">
                        </div-->
                        <!--
                            00 -> $item++
                            01 -> $fecha
                            02 -> $serie
                            03 -> $numero
                            04 -> $guiarem_codigo
                            05 -> $docurefe_codigo
                            06 -> $nombre
                            07 -> $total
                            08 -> $img_estado
                            09 -> $editar
                            10 -> $ver
                            11 -> $ver2
                            12 -> $carga
                            13 -> $docInicio
                            14 -> $compInicio
                            15 -> $docFin
                            16 -> $compFin
                            17 -> $numero_inicio
                            18 -> $numero_fin
                            19 -> $codigo
                            20 -> $enviarSunat
                            21 -> $estado_programacion
                            22 -> $enviarcorreo
                            23 -> $idCliente
                            24 -> $pdfSunat
                        -->
                        <!--thead>
                            <tr class="cabeceraTabla">
                                <th style="width:08%" data-orderable="true">FECHA</th>
                                <th style="width:05%" data-orderable="true">SERIE</th>
                                <th style="width:07%" data-orderable="true">NUMERO</th>
                                <th style="width:05%" data-orderable="true">ID CLIENTE</th>
                                <th style="width:25%" data-orderable="true">RAZON SOCIAL</th>
                                <th style="width:10%" data-orderable="true">CARGA</th>
                                <th style="width:07%" data-orderable="false">Doc. Origen</th>
                                <th style="width:07%" data-orderable="false">Doc. Destino</th>
                                <th style="width:08%" data-orderable="true">TOTAL</th>
                                <th style="width:2.2%" data-orderable="true"></th>
                                <th style="width:2.2%" data-orderable="false"></th>
                                <th style="width:2.2%" data-orderable="false"></th>
                                <th style="width:2.2%" data-orderable="false"></th>
                                <th style="width:2.2%" data-orderable="false"></th>
                                <th style="width:2.2%" data-orderable="false"></th>
                                <th style="width:4.8%" data-orderable="false"></th>
                            </tr>
                        </thead-->
                        <!--tbody></tbody>
                    </table>
                </div>
                <!--input type="hidden" id="iniciopagina" name="iniciopagina"-->
                <?php //echo $oculto ?>
            <!--/form-->
        <!--/div>
    </div>
</div-->

<div class="modal fade modal-envmail" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" style="width: 700px; padding: 1em 3em 1em 3em; height: auto; margin: auto; font-family: Trebuchet MS, sans-serif; font-size: 10pt;">
            <form method="post" id="form-mail">
                <div class="contenido" style="width: 100%; margin: auto; height: auto; overflow: auto;">
                    <div class="tempde_head">

                        <div class="row">
                            <div class="col-sm-11 col-md-11 col-lg-11" style="text-align: center;">
                                <h3>ENVIO DE DOCUMENTOS POR CORREO</h3>
                            </div>
                        </div>

                        <input type="hidden" id="idDocMail" name="idDocMail">
                    </div>

                    <div class="tempde_body">
                        
                        <div class="row">
                            <div class="col-sm-6 col-md-6 col-lg-6">
                                <label for="ncliente">Cliente:</label>
                            </div>
                            <div class="col-sm-3 col-md-3 col-lg-3">
                                <label for="doccliente">Ruc / Dni:</label>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6 col-md-6 col-lg-6">
                                <input type="text" class="form-control" id="ncliente" name="ncliente" value="" placeholder="Razón social" readonly>
                            </div>
                            <div class="col-sm-3 col-md-3 col-lg-3">
                                <input type="text" class="form-control" id="doccliente" name="doccliente" value="" placeholder="N° documento" readonly>
                            </div>
                        </div>
                        <br>

                        <div class="row">
                            <div class="col-sm-9 col-md-9 col-lg-9">
                                <label for="destinatario">Destinatarios:</label>
                                <span class="mail-contactos"></span>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-9 col-md-9 col-lg-9">
                                <input type="text" class="form-control" id="destinatario" name="destinatario" value="" placeholder="Correo">
                            </div>
                        </div>
                        <br>

                        <div class="row">
                            <div class="col-sm-9 col-md-9 col-lg-9">
                                <label for="asunto">Asunto:</label>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-9 col-md-9 col-lg-9">
                                <input type="text" class="form-control" id="asunto" name="asunto" value="" placeholder="Asunto">
                            </div>
                        </div>
                        <br>

                        <div class="row">
                            <div class="col-sm-9 col-md-9 col-lg-9">
                                <label for="mensaje">Mensaje:</label>
                            </div>
                        </div>
    
                        <div class="row">
                            <div class="col-sm-9 col-md-9 col-lg-9">
                                <textarea id="mensaje" name="mensaje" style="width: 650px; height: 300px">
                                    <p><b>SRES.</b> <span class="mail-cliente"></span></p>
                                    <p><span class="mail-empresa-envio"></span>, ENVÍA UN DOCUMENTO ELECTRÓNICO.</p>
                                    <p><b>SERIE Y NÚMERO:</b> <span class="mail-serie-numero"></span></p>
                                    <p><b>FECHA DE EMISIÓN:</b> <span class="mail-fecha"></span></p>
                                    <p><b>IMPORTE:</b> <span class="mail-importe"></span></p>
                                </textarea>
                            </div>
                        </div>
                        <br>
                    </div>

                    <div class="row">
                        <div class="col-sm-3 col-md-3 col-lg-3">
                            <label for="">Documentos adjuntos:</label>
                        </div>
                        
                        <div class="col-sm-2 col-md-2 col-lg-2">
                            <div class="form-group">
                                <img src="<?=base_url();?>public/images/icons/icono_imprimir.png" style="width: 22px"/>
                                <input type="hidden" value="false" name="adj-ticket_hidden">
                                <input class="form-control" id="adj-ticket" name="adj-ticket" type="checkbox" value="1" style="display: none;">
                                <div class="Switch Round On fib" style="vertical-align:top;margin-left:10px;">
                                    <div class="Toggle"></div>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-2 col-md-2 col-lg-2">
                            <div class="form-group">
                                <img src="<?=base_url();?>public/images/icons/pdf.png" style="width: 22px"/>
                                <input type="hidden" value="false" name="adj-a4_hidden">
                                <input class="form-control" id="adj-a4" name="adj-a4" type="checkbox" value="1" style="display: none;">
                                <div class="Switch Round On fib" style="vertical-align:top;margin-left:10px;"><div class="Toggle"></div></div>
                            </div>
                        </div>

                        <div class="col-sm-2 col-md-2 col-lg-2">
                            <div class="form-group">
                                <img src="<?=base_url();?>public/images/icons/xml.png" style="width: 22px"/>
                                <input type="hidden" value="false" name="adj-xml_hidden">
                                <input class="form-control" id="adj-xml" name="adj-xml" type="checkbox" value="1" style="display: none;">
                                <div class="Switch Round On fib" style="vertical-align:top;margin-left:10px;"><div class="Toggle"></div></div>
                            </div>
                        </div>
                    </div>

                    <div class="tempde_footer">
                        <div class="row">
                            <div class="col-sm-6 col-md-6 col-lg-6"></div>
                            <div class="col-sm-4 col-md-4 col-lg-4">
                                <span class="icon-loading-md"></span>
                                <div style="float: right">
                                    <span class="btn btn-success btn-sendMail">Enviar</span>
                                    &nbsp;
                                    <span class="btn btn-danger btn-close-envmail">Cerrar</span>
                                </div>
                            </div>
                        </div>
                        <br>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function(){
        $('#table-notas').DataTable({
            filter: false,
            destroy: true,
            processing: true,
            serverSide: true,
            autoWidth: false,
            ajax:{
                    url : '<?=base_url();?>index.php/ventas/notacredito/datatable_comprobantes/<?="$tipo_oper/$tipo_docu";?>',
                    type: "POST",
                    data: { dataString: "" },
                    beforeSend: function(){
                        $(".loading-table").show();
                    },
                    error: function(){
                    },
                    complete: function(){
                        $(".loading-table").hide();
                    }
            },
            language: spanish,
            order: [[ 0, "desc" ]]
        });

        $("#buscarC").click(function(){

            fechai          = $("#fechai").val();
            fechaf          = $("#fechaf").val();

            serie           = $("#serie").val();
            numero          = $("#numero").val();

            ruc_cliente     = $("#ruc_cliente").val();
            nombre_cliente  = $("#nombre_cliente").val();

            ruc_proveedor   = $("#ruc_proveedor").val();
            nombre_proveedor = $("#nombre_proveedor").val();

            producto        = $("#producto").val();

            $('#table-notas').DataTable({
                filter: false,
                destroy: true,
                processing: true,
                serverSide: true,
                ajax:{
                        url : '<?=base_url();?>index.php/ventas/notacredito/datatable_comprobantes/<?="$tipo_oper/$tipo_docu";?>',
                        type: "POST",
                        data: {
                                fechai: fechai, 
                                fechaf: fechaf,
                                serie: serie,
                                numero: numero,
                                ruc_cliente: ruc_cliente,
                                nombre_cliente: nombre_cliente,
                                ruc_proveedor: ruc_proveedor,
                                nombre_proveedor: nombre_proveedor,
                                producto: producto
                        },
                        beforeSend: function(){
                            $(".loading-table").show();
                        },
                        error: function(){
                        },
                        complete: function(){
                            $(".loading-table").hide();
                        }
                },
                language: spanish,
                order: [[ 0, "desc" ]]
            });
        });

        $("#limpiarC").click(function(){

            $("#fechai").val("");
            $("#fechaf").val("");
            
            $("#serie").val("");
            $("#numero").val("");
            
            $("#cliente").val("");
            $("#ruc_cliente").val("");
            $("#nombre_cliente").val("");

            $("#proveedor").val("");
            $("#ruc_proveedor").val("");
            $("#nombre_proveedor").val("");

            $("#producto").val("");

            fechai = "";
            fechaf = "";
            serie = "";
            numero = "";
            cliente = "";
            ruc_cliente = "";
            nombre_cliente = "";
            proveedor = "";
            ruc_proveedor = "";
            nombre_proveedor = "";
            producto = "";

            $('#table-notas').DataTable({
                filter: false,
                destroy: true,
                processing: true,
                serverSide: true,
                ajax:{
                        url : '<?=base_url();?>index.php/ventas/notacredito/datatable_comprobantes/<?="$tipo_oper/$tipo_docu";?>',
                        type: "POST",
                        data: {
                                fechai: fechai, 
                                fechaf: fechaf,
                                serie: serie,
                                numero: numero,
                                cliente: cliente,
                                ruc_cliente: ruc_cliente,
                                nombre_cliente: nombre_cliente,
                                proveedor: proveedor,
                                ruc_proveedor: ruc_proveedor,
                                nombre_proveedor: nombre_proveedor,
                                producto: producto
                        },
                        beforeSend: function(){
                            $(".loading-table").show();
                        },
                        error: function(){
                        },
                        complete: function(){
                            $(".loading-table").hide();
                        }
                },
                language: spanish,
                order: [[ 0, "desc" ]]
            });
        });

        $(".btn-close-envmail").click(function(){
            $(".modal-envmail").modal("hide");
        });

        $(".btn-sendMail").click(function(){

            documento = '<?=(isset($id_documento)) ? $id_documento : "";?>';
            codigo = $("#idDocMail").val();

            ticket = 1;
            a4 = 1;
            xml = 1;
            
            if ( $("#adj-ticket").is(":checked") == false )
                ticket = 0;
            
            if ( $("#adj-a4").is(":checked") == false )
                a4 = 0;

            if ( $("#adj-xml").is(":checked") == false )
                xml = 0;

            destinatario = $("#destinatario").val();
            asunto = $("#asunto").val();
            mensaje = $(".nicEdit-main").html();

            if ( codigo == ""){
                Swal.fire({
                        icon: "warning",
                        title: "Sin documento. Intentelo nuevamente.",
                        showConfirmButton: true,
                        timer: 2000
                });
                return null;
            }

            if ( destinatario == ""){
                Swal.fire({
                        icon: "warning",
                        title: "Debe ingresar un destinatario.",
                        showConfirmButton: true,
                        timer: 2000
                });
                $("#destinatario").focus();
                return null;
            }
            else{
                correosI = destinatario.split(",");
                /* DEVELOPING
                expr = /^[a-zA-Z0-9@_.\-]{1,3}$/
                
                for (var i = 0; i < correosI.length - 1; i++) {
                    if ( expr.test(correosI[i]) == false ){
                        alert("Verifique que todos los correos indicados sean validos.");
                    }
                }*/
            }

            if ( asunto == ""){
                Swal.fire({
                        icon: "warning",
                        title: "Indique el asunto del correo.",
                        showConfirmButton: true,
                        timer: 2000
                });
                $("#asunto").focus();
                return null;
            }

            var url = "<?=base_url();?>index.php/ventas/notacredito/sendDocMail";
            $.ajax({
                url:url,
                type:"POST",
                data:{ codigo: codigo, destinatario: destinatario, asunto: asunto, mensaje: mensaje, documento: documento, ticket: ticket, a4: a4, xml: xml },
                dataType:"json",
                error:function(data){
                },
                beforeSend: function(){
                    $(".tempde_footer .icon-loading-md").show();
                    $(".btn-sendMail").hide();
                },
                success:function(data){
                    if (data.result == "success"){
                        Swal.fire({
                            icon: "success",
                            title: "Correo electronico enviado.",
                            showConfirmButton: false,
                            timer: 2000
                        });
                        $(".modal-envmail").modal("hide");
                    }
                    else{
                        Swal.fire({
                            icon: "error",
                            title: "Correo no enviado, intentelo nuevamente.",
                            showConfirmButton: false,
                            timer: 2000
                        });
                    }
                },
                complete: function(){
                    $(".tempde_footer .icon-loading-md").hide();
                    $(".btn-sendMail").show();
                }
            });
        });

        function cerrar_ventana_prodtemporal(){
            $('.bd-example-modal-lg').modal('toggle');
            $('.modal-backdrop fade in').hide();
        }

        /* ================= Toggle Switch - Checkbox ================= */
        $(".Switch").click(function() {
            $(this).hasClass("On") ? ($(this).parent().find("input:checkbox").attr("checked", !0), $(this).removeClass("On").addClass("Off")) : ($(this).parent().find("input:checkbox").attr("checked", !1), $(this).removeClass("Off").addClass("On"))
        }), $(".Switch").each(function() {
            $(this).parent().find("input:checkbox").length && ($(this).parent().find("input:checkbox").hasClass("show") || $(this).parent().find("input:checkbox").hide(), $(this).parent().find("input:checkbox").is(":checked") ? $(this).removeClass("On").addClass("Off") : $(this).removeClass("Off").addClass("On"))
        });
    });

    function open_mail( id ){
        $(".modal-envmail").modal("toggle");

        url = "<?=base_url();?>index.php/ventas/notacredito/getInfoSendMail";

        $.ajax({
            url: url,
            type: "POST",
            data:{ id: id },
            dataType: "json",
            error: function(){

            },
            beforeSend: function(){
                $("#idDocMail").val("");
                $("#ncliente").val("");
                $("#doccliente").val("");
                $("#doccliente").val("");
                $("#destinatario").val("");

                $(".mail-cliente").html("");
                $(".mail-empresa-envio").html("");
                $(".mail-serie-numero").html("");
                $(".mail-fecha").html("");
                $(".mail-importe").html("");

                $(".mail-contactos").html("");
            },
            success: function(data){
                var info = data.info[0];

                if (data.match == true){
                    $("#idDocMail").val(info.codigo);
                    $("#ncliente").val(info.nombre);
                    $("#doccliente").val(info.ruc);
                    $("#doccliente").val(info.ruc);

                    $(".mail-cliente").html(info.nombre);
                    $(".mail-empresa-envio").html(info.empresa_envio);
                    $(".mail-serie-numero").html(info.serie + " - " + info.numero );
                    $(".mail-fecha").html(info.fecha);
                    $(".mail-importe").html(info.importe);

                    $.each(info.contactos, function(i, item){
                        var inputsContactos = "&nbsp;<input type='checkbox' class='ncontacto' onclick='ingresar_correo(\"" + item.correo + "\")' value='" + item.correo + "'>" + item.contacto;
                        $(".mail-contactos").append(inputsContactos);
                    });
                }
            }
        });
    }
    
    function ingresar_correo(correo){

        destinatarios = $("#destinatario").val();

        correosI = destinatarios.split(",");
        cantidad = correosI.length;

        add = true;

        for (i = 0; i < cantidad; i++){
            if ( correosI[i] == correo )
                add = false;
        }

        if ( add == true){
            if (destinatarios != "")
                $("#destinatario").val(destinatarios + "," + correo);
            else
                $("#destinatario").val(correo);
        }
        else{
            nvoCorreos = "";
            for (i = 0; i < cantidad; i++){
                if ( correosI[i] != correo ){
                    if ( i > 0 && nvoCorreos != "" ){
                        nvoCorreos += ",";
                    }
                    
                    nvoCorreos += correosI[i];
                }
            }

            $("#destinatario").val(nvoCorreos);
        }
    }
</script>
</body>
</html>