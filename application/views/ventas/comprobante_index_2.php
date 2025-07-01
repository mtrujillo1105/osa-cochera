<?php
$nombre_persona = $this->session->userdata('nombre_persona');
$persona = $this->session->userdata('persona');
$usuario = $this->session->userdata('usuario');
$url = base_url() . "index.php";
if (empty($persona))
    header("location:$url");
$CI = get_instance();
?>
<html>
<head>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js" integrity="sha512-bLT0Qm9VnAYZDflyKcBaQ2gg0hSYNQrJ8RilYldYQ1FxQYoCLtUjuuRuZo+fjqhx/qtq/1itJ0C2ejDxltZVFg==" crossorigin="anonymous"></script>
<script
  src="https://code.jquery.com/ui/1.10.3/jquery-ui.min.js"
  integrity="sha256-lnH4vnCtlKU2LmD0ZW1dU7ohTTKrcKP50WA9fa350cE="
  crossorigin="anonymous"></script>
    <script type="text/javascript">
        var base_url = '<?php echo base_url();?>';
        $("#base_url").val(base_url);
    </script>

    <script type="text/javascript" src="<?php echo base_url(); ?>public/js/calendario/calendar.js"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>public/js/calendario/calendar-setup.js"></script>

    <script type="text/javascript" src="<?php echo base_url(); ?>public/js/ventas/comprobante.js?=<?=JS;?>"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>public/js/funciones.js?=<?=JS;?>"></script>
    <link href="<?=base_url();?>public/js/fancybox/dist/jquery.fancybox.css?=<?=CSS;?>" rel="stylesheet">
    <script src="<?=base_url();?>public/js/fancybox/dist/jquery.fancybox.js?=<?=JS;?>"></script>


    <script type="text/javascript" src="<?=base_url();?>public/js/nicEdit/nicEdit.js?=<?=JS;?>"></script>


    <script type="text/javascript">
        bkLib.onDomLoaded(function() {
            new nicEditor({fullPanel : true}).panelInstance('mensaje');
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
<body>

<div id="pagina">
    <div id="zonaContenido">
        <div align="center">
            <div id="tituloForm" class="header"><?php echo $titulo_busqueda; ?></div>

            <form id="form_busqueda" name="form_busqueda" method="post" action="<?php echo base_url(); ?>index.php/ventas/comprobante/comprobantes">
                <div id="frmBusqueda">
                    <table class="fuente8" width="98%" cellspacing="0" cellpadding="3" border="0">
                        <tr>
                            <td align='left' width="10%">Fecha inicial</td>
                            <td align='left' width="70%">
                                <input name="fechai" id="fechai" value="" type="text" class="cajaGeneral cajaSoloLectura" size="10" maxlength="10"/>
                                <img src="<?php echo base_url(); ?>public/images/icons/calendario.png?=<?=IMG;?>" name="Calendario1" id="Calendario1" width="16" height="16" border="0" onMouseOver="this.style.cursor = 'pointer'" title="Calendario"/>
                                <script type="text/javascript">
                                    Calendar.setup({
                                        inputField: "fechai", // id del campo de texto
                                        ifFormat: "%Y-%m-%d", // formato de la fecha, cuando se escriba en el campo de texto
                                        button: "Calendario1"   // el id del botón que lanzará el calendario
                                    });
                                </script>
                                <label style="margin-left: 90px;">Fecha final</label>
                                <input name="fechaf" id="fechaf" value="" type="text" class="cajaGeneral cajaSoloLectura" size="10" maxlength="10"/>
                                <img src="<?php echo base_url(); ?>public/images/icons/calendario.png?=<?=IMG;?>" name="Calendario2" id="Calendario2" width="16" height="16" border="0" onMouseOver="this.style.cursor = 'pointer'" title="Calendario2"/>
                                <script type="text/javascript">
                                    Calendar.setup({
                                        inputField: "fechaf", // id del campo de texto
                                        ifFormat: "%Y-%m-%d", // formato de la fecha, cuando se escriba en el campo de texto
                                        button: "Calendario2"   // el id del botón que lanzará el calendario
                                    });
                                </script>
                            </td>
                            <td style="text-align: right">
                                <label for="">Número Inicio</label> &nbsp;&nbsp;&nbsp;
                                <input type="number" step="1" min="1" name="numeroI" id="numeroI" size="3" class="cajaPequena"/>
                            </td>
                        </tr>
                        <tr>
                            <td align='left'>Número</td>
                            <td align='left'> <?php
                            		if ($tipo_oper == 'V'){ ?>
                            			<select id="seriei" name="seriei" class="cajaPequena h2"> <?php
		                            		if ($series_emitidas != NULL){
				                            	foreach ($series_emitidas as $i => $val){ ?>
				                            		<option value="<?=$val->CPC_Serie;?>" <?=($val->serie_actual == $val->CPC_Serie) ? "selected" : "";?>><?=$val->CPC_Serie;?></option> <?php
				                            	}
				                            } ?>
	                            		</select> <?php
				                        }
			                          else{ ?>
		                            		<input type="text" id="seriei" name="seriei" class="cajaPequena" value="" > <?php
			                          } ?>
                              <input type="text" name="numero" id="numero" value="" placeholder="Numero" class="cajaGeneral" size="10" maxlength="6"/>
                            </td>
                            <td style="text-align: right">
                                <label for="">Número Fin</label> &nbsp;&nbsp;&nbsp;
                                <input type="number" step="1" min="1" name="numeroF" id="numeroF" size="3" class="cajaPequena"/>
                            </td>
                        </tr>
                        <tr>
                            <?php if ($tipo_oper == 'V') { ?>
                                <td align='left'>Cliente</td>
                                <td align='left'>
                                    <input type="hidden" name="cliente" value="" id="cliente" size="5"/>
                                    <input type="text" name="ruc_cliente" value="" class="cajaGeneral" id="ruc_cliente" size="10" maxlength="11" placeholder="Ruc" onkeypress="return numbersonly(this, event, '.');" />
                                    <input type="text" name="nombre_cliente" value="" placeholder="Nombre cliente" class="cajaGrande" id="nombre_cliente" size="40"/>
                                    <!-- <a href="<?php echo base_url(); ?>index.php/empresa/cliente/ventana_busqueda_cliente/"
                                       id="linkVerCliente"><img height='16' width='16'
                                                                src='<?php echo base_url(); ?>/images/ver.png?=<?=IMG;?>'
                                                                title='Buscar' border='0'/></a>
                                                                -->
                                </td>
                            <?php } else { ?>
                                <td align='left'>Proveedor</td>
                                <td align='left'>
                                    <input type="hidden" name="proveedor" value="" id="proveedor" size="5"/>
                                    <input type="text" name="ruc_proveedor" value="" placeholder="Ruc" class="cajaGeneral" id="ruc_proveedor" size="10" maxlength="11" onblur="obtener_proveedor();" onkeypress="return numbersonly(this, event, '.');" />
                                    <input type="text" name="nombre_proveedor" value="" placeholder="Nombre proveedor" class="cajaGrande" id="nombre_proveedor" size="40"/>
                                    <!--<a href="<?php echo base_url(); ?>index.php/empresa/proveedor/ventana_busqueda_proveedor/"
                                       id="linkVerProveedor"><img height='16' width='16'
                                                                  src='<?php echo base_url(); ?>/images/ver.png?=<?=IMG;?>'
                                                                  title='Buscar' border='0'/></a>-->
                                </td>
                            <?php } ?>

                            <td style="text-align: right">
                                <a href="javascript:;" id="imprimirRango" name="imprimirRango"><h3>Imprimir Rango</h3></a>
                            </td>
                        </tr>
                        <!--<tr>
                            <td align='left'>Artículo</td>
                            <td align='left'>
                                <input name="compania" type="hidden" id="compania" value="<?php echo $compania; ?>">
                                <input name="producto" type="hidden" class="cajaPequena" id="producto" size="10"
                                       maxlength="11"/>
                                <input name="codproducto" type="text" class="cajaGeneral" id="codproducto"
                                       value="" size="10" maxlength="20" placeholder="Codigo"
                                       onblur="obtener_producto();" readonly="readonly"/>
                                <input name="buscar_producto" type="hidden" class="cajaGeneral" id="buscar_producto"
                                       size="40"/>
                                <input name="nombre_producto" type="text" value="" placeholder="Nombre producto"
                                       class="cajaGrande" id="nombre_producto" size="40"/>
                                <a href="<?php //echo base_url(); ?>index.php/almacen/producto/ventana_busqueda_producto/"
                                   id="linkVerProducto"><img height='16' width='16'
                                                             src='<?php //echo base_url(); ?>/images/ver.png?=<?=IMG;?>'
                                                             title='Buscar' border='0'/></a>
                            </td>
                        </tr>-->
                    </table>
                </div>
                <div class="acciones">
                    <div id="botonBusqueda">
                   <?php #if ($tipo_oper == 'V' && $tipo_docu != 'N'){ ?>
                        <ul id="imprimirComprobante" class="lista_botones">
                            <li id="imprimir">Imprimir</li>
                            <input type="hidden" name="Rtipo_docu" id="Rtipo_docu" value="<?=$tipo_docu;?>"/>
                            <input type="hidden" name="Rtipo_oper" id="Rtipo_oper" value="<?=$tipo_oper;?>"/>
                        </ul>
                        <?php #} ?>
                        <ul id="nuevaComprobante" class="lista_botones">
                            <li id="nuevo">Nueva <?php 
                            echo ucwords($CI->obtener_tipo_documento($tipo_docu)); ?></li>
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
                                <td width="100%" align="left">N de <?php echo $CI->obtener_tipo_documento($tipo_docu); ?>s encontrados:&nbsp;<?php echo $registros; ?> </td>
                                <td width="50%" align="right">&nbsp;</td>
                        </table>
                    </div>
                </div>
                <div id="cabeceraResultado" class="header">
                    <?php echo $titulo_tabla; ?>
                    <?php echo $oculto; # ESTA VARIABLE CONTIENE EL TIPO DE OPERACION Y TIPO DE DOCUMENTO ?>
                </div>
            </form>
        </div>
        <div>
            <table class="fuente8 display" id="table-comprobante">
                <div id="cargando_datos" class="loading-table">
                    <img src="<?=base_url().'images/loading.gif?='.IMG;?>">
                </div>
                <thead>
                    <tr class="cabeceraTabla">
                        <th style="width: 07%" data-orderable="true">F. REGISTRO</th>
                        <th style="width: 07%" data-orderable="true">FECHA</th>
                        <th style="width: 05%" data-orderable="true">SERIE</th>
                        <th style="width: 07%" data-orderable="true">NÚMERO</th>
                        <th style="width: 05%" data-orderable="false">COTIZ.</th>
                        <th style="width: 05%" data-orderable="false">GUIA</th>
                        <th style="width: 05%" data-orderable="false"><?=($tipo_docu == "N") ? "COMPR." : "CANJE";?></th>
                        <th style="width: 05%" data-orderable="true"><?=($tipo_oper == "V") ? "CLIENTE" : "";?></th>
                        <th style="width: 30%" data-orderable="true">RAZON SOCIAL</th>
                        <th style="width: 08%" data-orderable="true">TOTAL</th>
                        <th style="width: 01%" data-orderable="false">&nbsp;</th>
                        <th style="width: 01%" data-orderable="false">&nbsp;</th>
                        <th style="width: 01%" data-orderable="false">&nbsp;</th>
                        <th style="width: 01%" data-orderable="false">&nbsp;</th>
                        <th style="width: 01%" data-orderable="false">&nbsp;</th>
                        <th style="width: 01%" data-orderable="false">&nbsp;</th>
                        <th style="width: 01%" data-orderable="false">&nbsp;</th>
                        <th style="width: 01%" data-orderable="false">&nbsp;</th>
                        <th style="width: 01%" data-orderable="false">&nbsp;</th>
                        <th style="width: 07%" data-orderable="false">&nbsp;</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>

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

<div class="modal fade modal-canje" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" style="width: 800px; height: auto; margin: auto; font-family: Trebuchet MS, sans-serif; font-size: 10pt;">
            <form method="post" id="form-canje">
                <div class="contenido" style="width: 100%; margin: auto; height: auto; overflow: auto;">
                    <div class="tempde_head">

                        <div class="row">
                            <div class="col-sm-1 col-md-1 col-lg-1"></div>
                            <div class="col-sm-7 col-md-7 col-lg-7" style="text-align: center;">
                                <h3>CANJE DE DOCUMENTO</h3>
                            </div>
                        </div>

                       
                    </div>

                    <div class="tempde_body">
                        <input type="hidden" name="cod_cliente" id="cod_cliente" class="">
                        <input type="hidden" name="cod_comprobante" id="cod_comprobante" class="">
                            
                        <div class="row">
                            <div class="col-sm-1 col-md-1 col-lg-1"></div>
                            <div class="col-sm-2 col-md-2 col-lg-2">
                                <label for="placa_mail">TIPO DE OPERACION:</label>
                            </div>
                            <div class="col-sm-3 col-md-3 col-lg-3">
                                <label for="kilometraje_mail">SERIE-NUMERO:</label>
                            </div>
                            <div class="col-sm-3 col-md-3 col-lg-3">
                                <label for="kilometraje_mail">TOTAL COMPROBANTE:</label>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-sm-1 col-md-1 col-lg-1"></div>
                            <div class="col-sm-2 col-md-2 col-lg-2">
                                <input type="hidden" name="tipo_operacion" id="tipo_operacion" class="comboPequeno" value="<?=$tipo_oper;?>">
                                <input type="text" name="operacion" id="operacion" class="comboPequeno" readonly>

                            </div>
                            <div class="col-sm-3 col-md-3 col-lg-3">
                                <input type="text" name="serie_numero" id="serie_numero" class="comboMedio" readonly>
                               
                            </div>
                             <div class="col-sm-3 col-md-3 col-lg-3">
                                <input type="text" name="total_comprobante" id="total_comprobante" class="comboMedio" readonly>
                               
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-1 col-md-1 col-lg-1"></div>
                            <div class="col-sm-5 col-md-5 col-lg-5">
                                <label for="cli_prov">CLIENTE/PROVEEDOR:</label>
                            </div>
                            <div class="col-sm-4 col-md-4 col-lg-4">
                                <label for="kilometraje_mail">RUC/DNI:</label>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-1 col-md-1 col-lg-1"></div>
                            <div class="col-sm-5 col-md-5 col-lg-5">
                                <input type="text" name="nombre_cliente_canje" id="nombre_cliente_canje" class="comboGrande">

                            </div>
                            <div class="col-sm-4 col-md-4 col-lg-4">
                                <input type="text" name="ruc_cliente_canje" id="ruc_cliente_canje" class="comboGrande">
                               
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-sm-1 col-md-1 col-lg-1"></div>
                            <div class="col-sm-7 col-md-7 col-lg-7">
                                <label for="destinatario">DIRECCION:</label>
                               
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-sm-1 col-md-1 col-lg-1"></div>
                            <div class="col-sm-9 col-md-9 col-lg-9">
                                <input type="text" name="direccion_cliente" id="direccion_cliente" class="cajaGrande" style="width: 100%">
                                
                            </div>
                        </div>
                       <div class="form-group" align="center"><label>DOCUMENTO FINAL</label></div>
                        <div class="row">
                            <div class="col-sm-1 col-md-1 col-lg-1"></div>
                            <div class="col-sm-2 col-md-2 col-lg-2">
                                <label for="destinatario">Tipo Documento:</label>
                               
                            </div>
                            <div class="col-sm-2 col-md-2 col-lg-2">
                                <label for="destinatario">SERIE:</label>
                            </div>
                            <div class="col-sm-2 col-md-2 col-lg-2">
                                <label for="destinatario">NUMERO:</label>
                               
                            </div>
                            <div class="col-sm-2 col-md-2 col-lg-2">
                                <label for="destinatario">FECHA:</label>
                               
                            </div>
                        </div>

                        <div class="row form-group">

                            <div class="col-sm-1 col-md-1 col-lg-1"></div>
                            <div class="col-sm-2 col-md-2 col-lg-2">
                                <select name="cmbDocumento" class="cajaPadding" id="cmbDocumento" onchange="obtenerSerieNumero()">
                                    <option value="F">FACTURA</option>
                                    <option value="B">BOLETA</option>
                                </select>
                            </div>
                            <div class="col-sm-2 col-md-2 col-lg-2">
                                <input type="text" name="serie_suger_b" id="serie_suger_b" class="cajaPequena">
                            </div>
                            <div class="col-sm-2 col-md-2 col-lg-2">
                                <input type="text" name="numero_suger_b" id="numero_suger_b" class="cajaPequena">
                            </div>
                            <div class="col-sm-2 col-md-2 col-lg-2">
                                <input type="date" name="fecha_comprobante" id="fecha_comprobante" class="cajaMediana">
                            </div>

                        </div>
                        
                        

                        <div class="row ">
                            <div class="col-sm-1 col-md-1 col-lg-1"></div>
                            <div class="col-sm-7 col-md-7 col-lg-7">
                                <label for="asunto">OBSERVACIONES:</label>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-sm-1 col-md-1 col-lg-1"></div>
                            <div class="col-sm-9 col-md-9 col-lg-9">
                               <textarea type="text" name="observaciones" id="observaciones"  style="width: 100%"></textarea>
                            </div>
                        </div>
                        <br>

                        
                        
                        <br>
                    </div>
                    <div class="tempde_footer">
                        <div class="row">
                            <div class="col-sm-6 col-md-6 col-lg-6"></div>
                            <div class="col-sm-4 col-md-4 col-lg-4">
                                <span class="icon-loading-md"></span>
                                <div style="float: right">
                                    <span class="btn btn-success btn-canjear_docu">Canjear</span>
                                    &nbsp;
                                    <span class="btn btn-danger btn-close-canje">Cerrar</span>
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
     $(function () {
            // BUSQUEDA POR RAZON SOCIAL O CODIGO
            
         if($("#tipo_operacion").val()=="V"){
            $("#ruc_cliente_canje").autocomplete({
                source: function (request, response) {
                    $.ajax({
                        url: "<?php echo base_url(); ?>index.php/empresa/cliente/autocomplete_ruc/",
                        type: "POST",
                        data: {
                            term: $("#ruc_cliente_canje").val()
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
                    $("#nombre_cliente_canje").val(ui.item.nombre);
                    $("#ruc_cliente_canje").val(ui.item.ruc);
                    $("#cod_cliente").val(ui.item.codigo);
                    $("#direccion_cliente").val(ui.item.direccion);
                    $("#codigoEmpresa").val(ui.item.codigoEmpresa);
                    
                },
                minLength: 2
            });
                
            $("#nombre_cliente_canje").autocomplete({
                source: function (request, response) {
                    $.ajax({
                        url: "<?php echo base_url(); ?>index.php/empresa/cliente/autocomplete/",
                        type: "POST",
                        data: {term: $("#nombre_cliente_canje").val()},
                        dataType: "json",
                        success: function (data) {
                            response(data);
                        }
                    });
                },

                select: function (event, ui) {
                    $("#nombre_cliente_canje").val(ui.item.nombre);
                    $("#ruc_cliente_canje").val(ui.item.ruc);
                    $("#cod_cliente").val(ui.item.codigo);
                    $("#ruc_cliente_canje").val(ui.item.ruc);
                    $("#direccion_cliente").val(ui.item.direccion);
                    $("#codigoEmpresa").val(ui.item.codigoEmpresa);
                    
                },
                minLength: 2
            });
            }else{

                // BUSQUEDA POR RUC PROVEEDOR
            $("#ruc_cliente_canje").autocomplete({
                source: function (request, response) {
                    $.ajax({
                        url: "<?php echo base_url(); ?>index.php/empresa/proveedor/autocomplete_ruc/",
                        type: "POST",
                        data: {
                            term: $("#ruc_cliente_canje").val()
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
                    $("#ruc_cliente_canje").val(ui.item.ruc);
                    $("#nombre_cliente_canje").val(ui.item.nombre);
                    $("#cod_cliente").val(ui.item.codigo);
                    $("#ruc_cliente_canje").val(ui.item.ruc);
                    $("#codigoEmpresa").val(ui.item.codigoEmpresa);
                    $("#direccion_cliente").val(ui.item.direccion);
                    
                },
                minLength:2 
            });


            $("#nombre_cliente_canje").autocomplete({
                source: function (request, response) {
                    $.ajax({
                        url: "<?php echo base_url(); ?>index.php/empresa/proveedor/autocomplete/",
                        type: "POST",
                        data: { term: $("#nombre_cliente_canje").val() },
                        dataType: "json",
                        success: function (data) {
                            response(data);
                        }
                    });
                },
                select: function (event, ui) {
                    $("#buscar_proveedor").val(ui.item.ruc);
                    $("#nombre_cliente_canje").val(ui.item.nombre);
                    $("#cod_cliente").val(ui.item.codigo);
                    $("#ruc_cliente_canje").val(ui.item.ruc);
                    $("#codigoEmpresa").val(ui.item.codigoEmpresa);
                    $("#direccion_cliente").val(ui.item.direccion);
                },
                minLength: 2
            });
            }

            // BUSQUEDA POR RUC
            
            
          
    });

    $(document).ready(function(){

 

 




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

            var url = "<?=base_url();?>index.php/ventas/comprobante/sendDocMail";
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

        $(".btn-canjear_docu").click(function(){

           var cod_cliente      = $("#cod_cliente").val();
           var cod_comprobante  = $("#cod_comprobante").val();
           var cmbDocumento     = $("#cmbDocumento").val();
           var serie_suger_b    = $("#serie_suger_b").val();
           var numero_suger_b   = $("#numero_suger_b").val();
           var observaciones    = $("#observaciones").val();
           var fecha_comprobante= $("#fecha_comprobante").val();

           if(serie_suger_b==""){
                Swal.fire({
                    icon: "error",
                    title: "Debe ingresar Serie.",
                    showConfirmButton: false,
                    timer: 2000
                });
                $("#serie_suger_b").focus();
                return false;
           }
           if(numero_suger_b==""){
                Swal.fire({
                    icon: "error",
                    title: "Debe ingresar Numero.",
                    showConfirmButton: false,
                    timer: 2000
                });
                $("#numero_suger_b").focus();
                return false;
           }

           var url = "<?=base_url();?>index.php/ventas/comprobante/canjear_documento";
           $.ajax({
              url:url,
              type:"POST",
              data:{ cod_cliente: cod_cliente, fecha_comprobante: fecha_comprobante, cod_comprobante: cod_comprobante, cmbDocumento: cmbDocumento, serie_suger_b: serie_suger_b, numero_suger_b: numero_suger_b, observaciones: observaciones },
              dataType:"json",
              error:function(data){
                    Swal.fire(
                        'Error de comunicacion, debe ir a Factura/Boleta e intentar aprobar el documento nuevamente.',
                        'Presione OK para continuar',
                        'warning'
                    );
                    $(".modal-canje").modal("hide");
                },
                beforeSend: function(){
                    $(".tempde_footer .icon-loading-md").show();
                    $(".btn-canjear_docu").hide();
                },
                success:function(data){
                    
                    if (data.result == "success"){
                        Swal.fire({
                            icon: "success",
                            title: "Documento Canjeado. "+data.serie+" - "+data.numero,
                            showConfirmButton: false,
                            timer: 2000
                        });
                        $(".modal-canje").modal("hide");

                    }
                    else{
                        Swal.fire({
                            icon: "error",
                            title: "No se puedo completar el canje.",
                            showConfirmButton: false,
                            timer: 2000
                        });
                    }
                },
                complete: function(){
                    $(".tempde_footer .icon-loading-md").hide();
                    $(".btn-canjear_docu").show();
                    $("#limpiarC").click();
                }
            });
        });

        $(".btn-close-canje").click(function(){
            $(".modal-canje").modal("hide");
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

        url = "<?=base_url();?>index.php/ventas/comprobante/getInfoSendMail";

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