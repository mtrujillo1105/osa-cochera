<script type="text/javascript" src="<?=$base_url;?>public/js/tesoreria/cheque.js?=<?=JS;?>"></script>
<script language="javascript">
    $(document).ready(function(){
        $('#cobro').click(function(){
           $('#fecha, #observacion').attr('disabled', false);
           if($(this).attr('checked')==false){
               $('#fecha, #observacion').val('').attr('disabled', true);
           }
        });
    });
</script>
<div id="pagina">
    <div id="zonaContenido">
        <div align="center">
            <div id="tituloForm" class="header"><?php echo $titulo;?></div>
            <div id="frmBusqueda" style="background-color:#E2E2E2 " >
                <table class="fuente8" width="98%" cellspacing=0 cellpadding="3" border=0>
                    <tr>
                        <td width="13%">Nro:</td>
                        <td><?php echo $nro; ?></td>
                    </tr>
                    <tr>
                        <td width="13%">F.Emisión:</td>
                        <td><?php echo $femis; ?></td>
                    </tr>
                    <tr>
                        <td width="13%">F.Vencimiento:</td>
                        <td><?php echo $fvenc; ?></td>
                    </tr>
                    <tr>
                        <td width="13%">Cliente:</td>
                        <td><?php echo $nombre; ?></td>
                    </tr>
                </table>
        </div>
            <div id="frmBusqueda">
                <?php echo validation_errors("<div class='error'>",'</div>');?>
                <form id="<?php echo $formulario;?>" method="post" action="<?=$base_url;?>index.php/tesoreria/insertar_cobro">
                <div id="datosGenerales">
                    <table class="fuente8" width="98%" cellspacing="0" cellpadding="6" border="0">
                            <tr>
                              <td width="16%">Cobro</td>
                              <td colspan="3"><input type="checkbox" name="cobro" id="cobro" value="1" <?php if($cobro=='1') echo 'checked="checked"'; ?> /></td>
                            </tr>
                            <tr>
                              <td>Fecha</td>
                              <td>
                                <input name="fecha" id="fecha" type="text" <?php if($cobro!='1') echo 'disabled="disabled"'; ?> class="cajaGeneral" value="<?php echo $fecha; ?>" size="10" maxlength="10" readonly="readonly" />
                                <img src="<?=$base_url;?>public/images/icons/calendario.png?=<?=IMG;?>" name="Calendario1" id="Calendario1" width="16" height="16" border="0" onMouseOver="this.style.cursor='pointer'" title="Calendario"/>
                                <script type="text/javascript">
                                    Calendar.setup({
                                        inputField     :    "fecha",      // id del campo de texto
                                        ifFormat       :    "%d/%m/%Y",       // formato de la fecha, cuando se escriba en el campo de texto
                                        button         :    "Calendario1"   // el id del botón que lanzará el calendario
                                    });
                                </script>
                              </td>
                            </tr>
                            <tr>
                              <td>Observación</td>
                              <td>
                                <textarea id="observacion" name="observacion" class="cajaTextArea" <?php if($cobro!='1') echo 'disabled="disabled"'; ?> style="width:100%" rows="3"><?php echo $observacion; ?></textarea>
                              </td>
                            </tr>
                    </table>
                </div>
                <div style="margin-top:20px; text-align: center">
                    <img id="loading" src="<?=$base_url;?>public/images/icons/loading.gif?=<?=IMG;?>"  style="visibility: hidden" />
                    <a href="javascript:;" id="grabarCobro"><img src="<?=$base_url;?>public/images/icons/botonaceptar.jpg?=<?=IMG;?>" width="85" height="22" class="imgBoton" ></a>
                    <a href="javascript:;" id="limpiarCobro"><img src="<?=$base_url;?>public/images/icons/botonlimpiar.jpg?=<?=IMG;?>" width="69" height="22" class="imgBoton" ></a>
                    <a href="javascript:;" id="cancelarCobro"><img src="<?=$base_url;?>public/images/icons/botoncancelar.jpg?=<?=IMG;?>" width="85" height="22" class="imgBoton" ></a>
                    <?php echo $oculto?>
                </div>
            </form>
         </div>
        </div>
    </div>
</div>