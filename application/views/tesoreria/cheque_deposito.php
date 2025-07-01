<script type="text/javascript" src="<?=$base_url;?>public/js/tesoreria/cheque.js?=<?=JS;?>"></script>			
<script language="javascript">
    $(document).ready(function(){
        $('#banco').change(function(){
            limpiar_combobox("cta");
            select = document.getElementById('cta');
            if($(this).val()!=''){
                url = base_url+"index.php/tesoreria/bancocta/JSON_listar/"+$(this).val();
                $.post(url,'',function(data){
                    $.each(data, function(i,item){
                        codigo      = item.CTAP_Codigo;
                        if(item.CTAC_Tipo=='S')
                            descripcion=item.CTAC_Nro+' - SOLES';
                        else
                            descripcion=item.CTAC_Nro+' - DOLARES';
                        opt         = document.createElement('option');
                        texto   = document.createTextNode(descripcion);
                        opt.appendChild(texto);
                        opt.value = codigo;
                        select.appendChild(opt);
                    });
                    
                },'json');
            }
	}); 
        
        $('#deposito').click(function(){
           $('#fecha, #banco, #cta, #observacion').attr('disabled', false);
           if($(this).attr('checked')==false){
               limpiar_combobox("cta");
               $('#fecha, #banco, #cta, #observacion').val('').attr('disabled', true);
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
                <form id="<?php echo $formulario;?>" method="post" action="<?=$base_url;?>index.php/tesoreria/insertar_deposito">
                <div id="datosGenerales">
                    <table class="fuente8" width="98%" cellspacing="0" cellpadding="6" border="0">
                            <tr>
                              <td width="16%">Deposito</td>
                              <td colspan="3"><input type="checkbox" name="deposito" id="deposito" value="1" <?php if($deposito=='1') echo 'checked="checked"'; ?> /></td>
                            </tr>
                            <tr>
                              <td>Fecha</td>
                              <td>
                                <input name="fecha" id="fecha" type="text" <?php if($deposito!='1') echo 'disabled="disabled"'; ?> class="cajaGeneral" value="<?php echo $fecha; ?>" size="10" maxlength="10" readonly="readonly"/>
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
                              <td>Banco</td>
                              <td>
                                <select name="banco" id="banco" <?php if($deposito!='1') echo 'disabled="disabled"'; ?> class="comboGrande" style="width:210px;">
                                  <?php echo $cboBanco; ?>
                               </select>
                              </td>
                            </tr>
                            <tr>
                              <td>Cta. Cte.</td>
                              <td><?php echo $cboCta; ?></td>
                            </tr>
                            <tr>
                              <td>Observación</td>
                              <td>
                                <textarea id="observacion" name="observacion" <?php if($deposito!='1') echo 'disabled="disabled"'; ?> class="cajaTextArea" style="width:100%" rows="3"><?php echo $observacion; ?></textarea>
                              </td>
                            </tr>
                    </table>
                </div>
                <div style="margin-top:20px; text-align: center">
                    <img id="loading" src="<?=$base_url;?>public/images/icons/loading.gif?=<?=IMG;?>"  style="visibility: hidden" />
                    <a href="javascript:;" id="grabarDeposito"><img src="<?=$base_url;?>public/images/icons/botonaceptar.jpg?=<?=IMG;?>" width="85" height="22" class="imgBoton" ></a>
                    <a href="javascript:;" id="limpiarDeposito"><img src="<?=$base_url;?>public/images/icons/botonlimpiar.jpg?=<?=IMG;?>" width="69" height="22" class="imgBoton" ></a>
                    <a href="javascript:;" id="cancelarDeposito"><img src="<?=$base_url;?>public/images/icons/botoncancelar.jpg?=<?=IMG;?>" width="85" height="22" class="imgBoton" ></a>
                    <?php echo $oculto?>
                </div>
            </form>
         </div>
        </div>
    </div>
</div>