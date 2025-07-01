<?php
$nombre_persona = $this->session->userdata('nombre_persona');
$persona = $this->session->userdata('persona');
$usuario = $this->session->userdata('usuario');
$url = base_url() . "index.php";
if (empty($persona)) header("location:$url");
?>
<html>
<head>
    <script type="text/javascript" src="<?php echo base_url(); ?>public/js/jquery.js?=<?=JS;?>"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>public/js/empresa/cliente.js?=<?=JS;?>"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>public/js/jquery.metadata.js?=<?=JS;?>"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>public/js/jquery.validate.js?=<?=JS;?>"></script>
    <script language="javascript">
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
            <div id="tituloForm" class="header">BUSCAR CLIENTE</div>
            <div id="frmBusqueda">
                <form id="form_busqueda" name="form_busqueda" method="post" action="<?php echo $action; ?>">
                    <table class="fuente8" width="98%" cellspacing=0 cellpadding=3 border=0>
                        <tr>
                            <td width="16%">Código cliente</td>
                            <td width="68%">
                                <input id="codigoU" type="text" class="cajaPequena" name="codigoU" maxlength="15" value="<?=$codigoU;?>">
                            </td>
                            <td width="5%">&nbsp;</td>
                            <td width="5%">&nbsp;</td>
                            <td width="6%" align="right"></td>
                        </tr>
                        <tr>
                            <td width="16%">N. de Documento</td>
                            <td width="68%">
                                <input id="txtNumDoc" type="text" class="cajaPequena" NAME="txtNumDoc" onkeypress="return numbersonly(this,event,'.');" maxlength="15" value="<?=$numdoc;?>">
                            </td>
                            <td width="5%">&nbsp;</td>
                            <td width="5%">&nbsp;</td>
                            <td width="6%" align="right"></td>
                        </tr>
                        <tr>
                            <td>Nombre o Raz&oacute;n Social</td>
                            <td><input id="txtNombre" name="txtNombre" list="list_grupo" type="text" class="cajaGrande" maxlength="45" value="<?php echo $nombre; ?>"></td>
                            <td>
                                <datalist id="list_grupo"> <?php
                                    $grupos = $this->Global_model->get('cji_empresa');
                                    #innerjoin aqui
                                    if ($grupos) {
                                        foreach ($grupos as $grupo) : ?>
                                            <option value="<?php echo $grupo->EMPRC_RazonSocial ?>"></option> <?php
                                        endforeach;
                                    } ?>
                                </datalist>
                            </td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                        </tr>
                        <tr>
                            <td>Tel&eacute;fono/Celular</td>
                            <td><input id="txtTelefono" type="text" class="cajaPequena" NAME="txtTelefono" onkeypress="return numbersonly(this,event,'.');" maxlength="15" value="<?php echo $telefono; ?>"></td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                        </tr>
                        <tr>
                            <td>Tipo Cliente</td>
                            <td>
                                <select id="cboTipoProveedor" name="cboTipoProveedor" class="comboMedio">
                                    <option value="" selected>::Seleccionar::</option>
                                    <option value="N" <?php if ($tipo == 'N') echo 'selected="selected"'; ?> > P.Natural </option>
                                    <option value="J" <?php if ($tipo == 'J') echo 'selected="selected"'; ?>> P.Juridica </option>
                                </select>
                            </td>
                        </tr>
                    </table>
                </form>
            </div>
            <div class="acciones">
                <div id="botonBusqueda">
                    <ul id="imprimirCliente" class="lista_botones">
                        <li id="imprimir">Imprimir</li>
                    </ul>
                    <ul id="nuevoCliente" class="lista_botones">
                        <li id="nuevo">Nuevo Cliente</li>
                    </ul>
                    <ul id="limpiarCliente" class="lista_botones">
                        <li id="limpiar">Limpiar</li>
                    </ul>
                    <ul id="buscarCliente" class="lista_botones">
                        <li id="buscar">Buscar</li>
                    </ul>
                </div>
                <div id="lineaResultado">
                    <table class="fuente7" width="100%" cellspacing=0 cellpadding=3 border=0>
                        <tr>
                            <td width="50%" align="left">N de clientes encontrados:&nbsp;<?php echo $registros; ?></td>
                    </table>
                </div>
            </div>
            <div id="cabeceraResultado" class="header">
                <?php echo $titulo_tabla; ?> </div>
            <div id="frmResultado">
                <table class="fuente8" width="100%" cellspacing="0" cellpadding="3" border="0" ID="Table1">
                    <tr class="cabeceraTabla">
                        <td width="8%">Código</td>
                        <td width="5%">RUC</td>
                        <td width="15%">DNI</td>
                        <td width="34%">NOMBRE O RAZ&Oacute;N SOCIAL</td>
                          <td width="40%">DIRECCI&Oacute;N </td>
                        <td width="10%">TIPO CLIENTE</td>
                       <!-- <td width="19%">FAX</td>-->
                        <td width="5%">&nbsp;</td>
                        <td width="5%">&nbsp;</td>
                        <td width="6%">&nbsp;</td>
                        <td width="6%">&nbsp;</td>
                    </tr>
                    <?php
                    $i = 1;
                    if (count($lista) > 0) {
                        foreach ($lista as $indice => $valor) {
                            $class = $indice % 2 == 0 ? 'itemParTabla' : 'itemImparTabla';
                            ?>
                            <tr class="<?php echo $class;?>">
                                <td>
                                    <div align="center"><?php echo $valor[11];?></div>
                                </td>
                                <td>
                                    <div align="left"><?php echo $valor[1];?></div>
                                </td>
                                <td>
                                    <div align="center"><?php echo $valor[2];?></div>
                                </td>
                                <td>
                                    <div align="left"><?php echo $valor[3];?></div>
                                </td>
                                  <td>
                                    <div align="left"><?php echo $valor[4];?></div>
                                </td>
                                <td>
                                    <div align="center"><?php echo $valor[5];?></div>
                                </td>
                               
                                <td>
                                    <div align="center"><?php echo $valor[8];?></div>
                                </td>
                                <td>
                                    <div align="left"><?php echo $valor[9];?></div>
                                </td>
                                <td>
                                    <div align="left"><?php echo $valor[10];?></div>
                                </td>
                            </tr>
                            <?php
                            $i++;
                        }
                    } else {
                        ?>
                        <table width="100%" cellspacing="0" cellpadding="3" border="0" class="fuente8">
                            <tbody>
                            <tr>
                                <td width="100%" class="mensaje">No hay ning&uacute;n proveedor que cumpla con los
                                    criterios de b&uacute;squeda
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    <?php
                    }
                    ?>
                </table>
                <input type="hidden" id="iniciopagina" name="iniciopagina">
                <input type="hidden" id="cadena_busqueda" name="cadena_busqueda">
                <input type="hidden" name="base_url" id="base_url" value="<?php echo base_url(); ?>">
            </div>
            <div style="margin-top: 15px;"><?php echo $paginacion; ?></div>
        </div>
    </div>
</div>
</body>
</html>
