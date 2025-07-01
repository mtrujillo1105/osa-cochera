<script type="text/javascript" src="<?=$base_url;?>public/js/jquery.metadata.js?=<?=JS;?>"></script>
<!--<script type="text/javascript" src="<?=$base_url;?>public/js/jquery.validate.min.js?=<?=JS;?>"></script>-->
<script type="text/javascript" src="<?=$base_url;?>public/js/tesoreria/prestamo.js?=<?=JS;?>"></script>
<div id="pagina">
    <div id="zonaContenido">
        <div align="center">
            <div id="tituloForm" class="header"><?php echo $titulo;?></div>
            <div id="frmBusqueda">
                <?php echo validation_errors("<div class='error'>",'</div>');?>
                <?php echo $form_open;?>
                    <div id="datosGenerales">
                        <table class="fuente8" width="98%" cellspacing="0" cellpadding="5" border="0">
                            <tr>
                                <td width="5%">NOMBRE</td>
                                <td colspan="5" ><input type="text" size="60" name="nombre" id="nombre" placeholder="NOMBRES" value="<?php echo $nombre;?>"></td>

                                <td width="9%" valign="middle">FECHA DE DEMBOLSO</td>
                    <td width="20%" valign="middle"><input NAME="fecha" type="text" class="cajaGeneral cajaSoloLectura" id="fecha" value="<?php echo $hoy; ?>" size="10" maxlength="10" readonly="readonly" />
                        <img height="16" border="0" width="16" id="Calendario1" name="Calendario1" src="<?=$base_url;?>public/images/icons/calendario.png?=<?=IMG;?>" />
                        <script type="text/javascript">
                            Calendar.setup({
                                inputField     :    "fecha",      // id del campo de texto
                                ifFormat       :    "%d/%m/%Y",       // formato de la fecha, cuando se escriba en el campo de texto
                                button         :    "Calendario1"   // el id del botón que lanzará el calendario
                            });
                        </script>
                    </td>
                            <tr>
                                <td width="5%">APELLIDOS</td>
                                <td colspan="5" ><input type="text" size="60" name="apellido" id="apellido" placeholder="APELLIDOS" value="<?php echo $apellido;?>"></td>
                            </tr>
                            </tr>
                            <tr>
                                <td width="5%">DNI*</td>
                                <td width="15%"><input type="text" name="dni" id="dni" placeholder="DNI" maxlength="8" value="<?php echo $dni;?>"></td>
                            </tr>
                            <tr>
                                <td width="5%">CARGO</td>
                                <td width="15%"><input type="text" name="cargo" id="cargo" value="<?php echo $cargo;?>"></td>
                            </tr>
                            <tr>
                                <td width="5%">FORMA DE PAGO</td>
                                <td width="15%"><input type="text" name="forma" id="forma" value="<?php echo $forma;?>"></td>

                                <td valign="middle" id="idTdMoneda">MONEDA*
                                    <select name="moneda" id="moneda" class="comboPequeno"
                                            style="width:150px;"><?php echo $cboMoneda; ?>
                                            </select>
                                </td>
                        </select>
                            </tr>
                            <tr>
                                <td width="5%">VALOR DE PRESTAMO</td>
                                <td width="15%"><input type="text" name="valor" id="valor" value="<?php echo $valor;?>"></td>
                            </tr>
                            <tr>
                                <td>OBSERVACION</td>
                                 <td colspan="5" ><textarea style="width:97%; height:70px;" id="observacion" name="observacion" ><?php echo $observacion;?></textarea></td>
                            </tr>
                        </table>
                    </div>
                    <div style="margin-top:20px; text-align: center">
                        <a href="#" id="grabarPrestamo"><img src="<?=$base_url;?>public/images/icons/botonaceptar.jpg?=<?=IMG;?>" width="85" height="22" class="imgBoton" ></a>
                        <a href="#" id="limpiarPrestamo"><img src="<?=$base_url;?>public/images/icons/botonlimpiar.jpg?=<?=IMG;?>" width="69" height="22" class="imgBoton" ></a>
                        <a href="#" id="cancelarPrestamo"><img src="<?=$base_url;?>public/images/icons/botoncancelar.jpg?=<?=IMG;?>" width="85" height="22" class="imgBoton" ></a>
                        <?php echo $oculto?>
                    </div>
                <?php echo $form_close;?>
            </div>
        </div>
    </div>
</div>