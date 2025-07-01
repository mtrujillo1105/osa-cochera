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
        <script type="text/javascript" src="<?php echo base_url(); ?>public/js/tesoreria/pago.js?=<?=JS;?>"></script>	
        <script type="text/javascript" src="<?php echo base_url(); ?>public/js/fancybox/jquery.mousewheel-3.0.4.pack.js?=<?=JS;?>"></script>
        <script type="text/javascript" src="<?php echo base_url(); ?>public/js/fancybox/jquery.fancybox-1.3.4.pack.js?=<?=JS;?>"></script>
        <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>public/js/fancybox/jquery.fancybox-1.3.4.css?=<?=CSS;?>" media="screen" />
        <script language="javascript">
        </script>		
    </head>
    <body>
        <br>
        <div id="pagina">
            <div id="zonaContenido">
                <div align="center">
                    <div id="tituloForm" class="header">PAGOS RALIZADOS</div>
                    <form id="form_busqueda" name="form_busqueda" method="post" action="<?php echo base_url(); ?>index.php/tesoreria/pago/buscar_ultimos/<?php echo $tipo_cuenta ?>">
                        <div id="frmBusqueda" >
                            <table class="fuente8" width="98%" cellspacing="0" cellpadding="3" border="0">
                                <tr>
                                    <td align='left' width="13%"><?php if ($tipo_cuenta == '1') echo 'Cliente'; else echo 'Proveedor'; ?></td>
                                    <td align='left' width="50%"><?php echo $nombre; ?></td>
                                    <td width="10%">DNI / RUC</td>
                                    <td><?php echo $ruc; ?></td>
                                </tr>
                                <tr>
                                    <td align='left'>Fecha inicial</td>
                                    <td align='left' colspan="3">
                                        <input name="fechai" id="fechai" value="<?php echo '01/' . date('m/Y'); ?>" type="text" class="cajaGeneral" size="10" maxlength="10"/>
                                        <img src="<?php echo base_url(); ?>public/images/icons/calendario.png?=<?=IMG;?>" name="Calendario1" id="Calendario1" width="16" height="16" border="0" onMouseOver="this.style.cursor='pointer'" title="Calendario"/>
                                        <script type="text/javascript">
                                            Calendar.setup({
                                                inputField     :    "fechai",      // id del campo de texto
                                                ifFormat       :    "%d/%m/%Y",       // formato de la fecha, cuando se escriba en el campo de texto
                                                button         :    "Calendario1"   // el id del botón que lanzará el calendario
                                            });
                                        </script>
                                        <label style="margin-left: 20px;">Fecha final</label>
                                        <input name="fechaf" id="fechaf" value="<?php echo date('d/m/Y') ?>" type="text" class="cajaGeneral" size="10" maxlength="10" />
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
                            </table>
                        </div>
                        <div id="botonBusqueda">                                       
                            <a href="javascript:;" id="atrasPago" style="margin-right:175px;"><img src="<?php echo base_url(); ?>public/images/icons/botonatras.jpg?=<?=IMG;?>" width="85" height="22" class="imgBoton" /></a>
                            <ul id="imprimirPago" class="lista_botones"><li id="imprimir">Imprimir</li></ul>
                            <ul id="limpiarPago" class="lista_botones"><li id="limpiar">Limpiar</li></ul>
                            <ul id="buscarPago" class="lista_botones"><li id="buscar">Buscar</li></ul> 
                        </div>
                        <div id="lineaResultado">
                            <table class="fuente8" width="100%" cellspacing="0" cellpadding="3" border="0">
                                <tr>
                                    <td width="50%" align="left">N de pagos encontrados:&nbsp;<?php echo count($lista); ?> </td>
                                    <td width="50%" align="right">&nbsp;</td>
                            </table>
                        </div>
                        <div id="cabeceraResultado" class="header">RELACION DE PAGOS</div>
                        <div id="frmResultado">
                            <table class="fuente8" width="100%" cellspacing="0" cellpadding="3" border="0" ID="Table1">
                                <tr class="cabeceraTabla">
                                    <td width="4%"><div align="center">ITEM</div></td>
                                    <td width="7%"><div align="center">FECHA</div></td>
                                    <td width="15%"><div align="center">TIPO DOCUMENTO</div></td>
                                    <td width="16%"><div align="center">SERIE - NUMERO</div></td>                                        
                                    <td width="10%"><div align="center">FORMA PAGO</div></td>
                                    <td width="5%"><div align="center">MONEDA</div></td>
                                    <td width="7%"><div align="center">MONTO</div></td>
                                    <td width="40%"><div align="center">OBSERVACION</div></td>
                                    <td width="5%"><div align="center">&nbsp;</div></td>
                                </tr>					
                                <?php
                                if (count($lista) > 0) {
                                    foreach ($lista as $indice => $valor) {
                                        $class = $indice % 2 == 0 ? 'itemParTabla' : 'itemImparTabla';
                                        ?>
                                        <tr class="<?php echo $class; ?>">
                                            <td><div align="center"><?php echo $valor[0]; ?></div></td>
                                            <td><div align="center"><?php echo $valor[1]; ?></div></td>
                                            <td><div align="center"><?php echo $valor[9]; ?></div></td>
                                            <td><div align="center"><?php echo $valor[2] . ' - ' . $valor[3]; ?></div></td>
                                            <td><div align="center"><?php echo $valor[4]; ?></div></td>
                                            <td><div align="center"><?php echo $valor[5]; ?></div></td>
                                            <td><div align="right"><?php echo $valor[6]; ?></div></td>
                                            <td><div align="left"><?php echo $valor[7]; ?></div></td>
                                            <td><div align="center"><?php echo $valor[8]; ?></div></td>
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
                        <?php echo $oculto ?>
                    </form>
                </div>
            </div>			
        </div>
    </body>
</html>