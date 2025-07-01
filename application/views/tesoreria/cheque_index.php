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
        <script type="text/javascript" src="<?php echo base_url(); ?>public/js/tesoreria/cheque.js?=<?=JS;?>"></script>	
        <script type="text/javascript" src="<?php echo base_url(); ?>public/js/fancybox/jquery.mousewheel-3.0.4.pack.js?=<?=JS;?>"></script>
        <script type="text/javascript" src="<?php echo base_url(); ?>public/js/fancybox/jquery.fancybox-1.3.4.pack.js?=<?=JS;?>"></script>
        <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>public/js/fancybox/jquery.fancybox-1.3.4.css?=<?=CSS;?>" media="screen" />
        <script language="javascript">
            $(document).ready(function(){
                $("a#linkVerCliente").fancybox({
                    'width'          : 700,
                    'height'         : 550,
                    'autoScale'	 : false,
                    'transitionIn'   : 'none',
                    'transitionOut'  : 'none',
                    'showCloseButton': false,
                    'modal'          : true,
                    'type'	     : 'iframe'
                });  
								
            });
            function seleccionar_cliente(codigo,ruc,razon_social, empresa, persona){
                $("#cliente").val(codigo);
                $("#ruc_cliente").val(ruc);
                $("#nombre_cliente").val(razon_social);
            }

        </script>		
    </head>
    <body>
        <div id="pagina">
            <div id="zonaContenido">
                <div align="center">
                    <div id="tituloForm" class="header"><?php echo $titulo_busqueda; ?></div>
                    <form id="form_busqueda" name="form_busqueda" method="post" action="<?php echo base_url(); ?>index.php/tesoreria/cheque/listar">
                        <div id="frmBusqueda" >
                            <table class="fuente8" width="98%" cellspacing="0" cellpadding="3" border="0">
                                <tr>
                                    <td align='left' width="10%">Fecha inicial</td>
                                    <td align='left' width="90%" colspan="3">
                                        <input name="fechai" id="fechai" value="<?php echo $fechai; ?>" type="text" class="cajaGeneral" size="10" maxlength="10"/>
                                        <img src="<?php echo base_url(); ?>public/images/icons/calendario.png?=<?=IMG;?>" name="Calendario1" id="Calendario1" width="16" height="16" border="0" onMouseOver="this.style.cursor='pointer'" title="Calendario"/>
                                        <script type="text/javascript">
                                            Calendar.setup({
                                                inputField     :    "fechai",      // id del campo de texto
                                                ifFormat       :    "%d/%m/%Y",       // formato de la fecha, cuando se escriba en el campo de texto
                                                button         :    "Calendario1"   // el id del botón que lanzará el calendario
                                            });
                                        </script>
                                        <label style="margin-left: 90px;">Fecha final</label>
                                        <input name="fechaf" id="fechaf" value="<?php echo $fechaf; ?>" type="text" class="cajaGeneral" size="10" maxlength="10" />
                                        <img src="<?php echo base_url(); ?>public/images/icons/calendario.png?=<?=IMG;?>" name="Calendario2" id="Calendario2" width="16" height="16" border="0" onMouseOver="this.style.cursor='pointer'" title="Calendario2"/>
                                        <script type="text/javascript">
                                            Calendar.setup({
                                                inputField     :    "fechaf",      // id del campo de texto
                                                ifFormat       :    "%d/%m/%Y",       // formato de la fecha, cuando se escriba en el campo de texto
                                                button         :    "Calendario2"   // el id del botón que lanzará el calendario
                                            });
                                        </script>
                                    </td>

                                </tr>
                                <tr>
                                    <td align='left'>Número</td>
                                    <td align='left'><input type="text" name="numero" id="numero" value="<?php echo $numero; ?>"  class="cajaGeneral" size="10" maxlength="10"  />
                                        <label style="margin-left: 108px;">Tipo Cheque </label>
                                        <?php
                                        if ($tipo_cheque == '') {
                                            ?>
                                            <select id="tipo_cheque" name="tipo_cheque">
                                                <option value="">::Seleccione::</option>                                            
                                                <option value="1">Recibidos</option>
                                                <option value="2">Emitidos</option>
                                            </select>
                                            <?php
                                        }else if ($tipo_cheque == 1) {
                                            ?>
                                            <select id="tipo_cheque" name="tipo_cheque">
                                                <option value="">::Seleccione::</option>                                            
                                                <option value="1" selected="selected">Recibidos</option>
                                                <option value="2">Emitidos</option>
                                            </select>
                                            <?php
                                        }else if ($tipo_cheque == 2) {
                                            ?>
                                            <select id="tipo_cheque" name="tipo_cheque">
                                                <option value="">::Seleccione::</option>                                            
                                                <option value="1">Recibidos</option>
                                                <option value="2" selected="selected">Emitidos</option>
                                            </select>
                                            <?php
                                        }
                                        ?>
                                    </td>
                                </tr>
                                <!--<tr>
                                    <td align='left'>Cliente</td>
                                    <td align='left'>
                                        <input type="hidden" name="cliente" value="<?php echo $cliente; ?>" id="cliente" size="5" />
                                        <input type="text" name="ruc_cliente" value="<?php echo $ruc_cliente; ?>" class="cajaGeneral" id="ruc_cliente" size="10" maxlength="11" onblur="obtener_cliente();" onkeypress="return numbersonly(this,event,'.');" />
                                        <input type="text" name="nombre_cliente" value="<?php echo $nombre_cliente; ?>"  class="cajaGrande cajaSoloLectura" id="nombre_cliente" size="40" readonly="readonly" />
                                        <a href="<?php echo base_url(); ?>index.php/empresa/cliente/ventana_busqueda_cliente/" id="linkVerCliente"><img height='16' width='16' src='<?php echo base_url(); ?>/images/ver.png?=<?=IMG;?>' title='Buscar' border='0' /></a>
                                    </td>
                                </tr>-->
                            </table>
                        </div>
<div class="acciones">
                        <div id="botonBusqueda">                                       
                            <ul id="imprimirCheque" class="lista_botones"><li id="imprimir">Imprimir</li></ul>
                            <ul id="limpiarCheque" class="lista_botones"><li id="limpiar">Limpiar</li></ul>
                            <ul id="buscarCheque" class="lista_botones"><li id="buscar">Buscar</li></ul> 
                        </div>
                        <div id="lineaResultado">
                            <table class="fuente7" width="100%" cellspacing="0" cellpadding="3" border="0">
                                <tr>
                                    <td width="50%" align="left">N de cuentas por encontrados:&nbsp;<?php echo $registros; ?> </td>
                            </table>
                        </div>
</div>
                        <div id="cabeceraResultado" class="header">
                            <?php
                            echo $titulo_tabla;
                            ?>
                        </div>
                        <div id="frmResultado">
                            <table class="fuente8" width="100%" cellspacing="0" cellpadding="3" border="0" ID="Table1">
                                <tr class="cabeceraTabla">
                                    <td width="3%">ITEM</td>
                                    <td width="5%">NRO</td>
                                    <td width="5%">F.EMIS</td>
                                    <td width="5%">F.VENC</td>
                                    <td width="10%">TIPO CUENTA</td>
                                    <td>CLIENTE / PROVEEDOR</td>
                                    <td width="5%">MONTO</td>
                                    <td width="5%">MONEDA</td>
                                    <td width="5%">COBRO</td>
                                    <td width="5%">F.COBRO</td>
                                    <td width="5%">DEPOS</td>
                                    <td width="5%">F.DEPOS</td>
                                    <td width="4%" colspan="2">&nbsp;</td>
                                </tr>					
                                <?php
                                if (count($lista) > 0) {
                                    foreach ($lista as $indice => $valor) {
                                        $class = $indice % 2 == 0 ? 'itemParTabla' : 'itemImparTabla';
                                        ?>
                                        <tr class="<?php echo $class; ?>">
                                            <td><div align="center"><?php echo $valor[0]; ?></div></td>
                                            <td><div align="center"><?php echo $valor[1]; ?></div></td>
                                            <td><div align="center"><?php echo $valor[2]; ?></div></td>
                                            <td><div align="center"><?php echo $valor[3]; ?></div></td>
                                            <td><div align="center"><?php echo $valor[4] != '' ? "RECIBIDO" : "EMITIDO"; ?></div></td>
                                            <td><div align="center"><?php echo $valor[4] != '' ? $valor[4] : $valor[13]; ?></div></td>
                                            <td><div align="center"><?php echo $valor[5]; ?></div></td>
                                            <td><div align="center"><?php echo $valor[6]; ?></div></td>
                                            <td><div align="center"><?php if ($valor[7] == '1') { ?><img height='14' width='14' src='<?php echo base_url(); ?>/images/icono_aprobar.png?=<?=IMG;?>' title='Cobro Realizado' border='0' /><?php } ?></div></td>
                                            <td><div align="center"><?php echo $valor[8]; ?></div></td>
                                            <td><div align="center"><?php if ($valor[9] == '1') { ?><img height='14' width='14' src='<?php echo base_url(); ?>/images/icono_aprobar.png?=<?=IMG;?>' title='Deposito Realizado' border='0' /><?php } ?></div></td>
                                            <td><div align="center"><?php echo $valor[10]; ?></div></td>
                                            <td><div align="center"><?php echo $valor[11]; ?></div></td>
                                            <td><div align="center"><?php echo $valor[12]; ?></div></td>
                                        </tr>
                                        <?php
                                    }
                                } else {
                                    ?>
                                    <table width="100%" cellspacing="0" cellpadding="3" border="0" class="fuente8">
                                        <tbody>
                                            <tr>
                                                <td width="100%" class="mensaje">No hay ning&uacute;n registro que cumpla con los criterios de b&uacute;squeda</td>
                                            </tr>
                                        </tbody>
                                    </table>	
                                    <?php
                                }
                                ?>
                            </table>
                        </div>
                        <div style="margin-top: 15px;"><?php echo $paginacion; ?></div>
                        <input type="hidden" id="iniciopagina" name="iniciopagina">
                        <?php echo $oculto ?>
                    </form>
                </div>
            </div>			
        </div>
    </body>
</html>