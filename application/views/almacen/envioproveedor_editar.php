<script type="text/javascript" src="<?=$base_url;?>public/js/jquery.metadata.js?=<?=JS;?>"></script>
<script type="text/javascript" src="<?=$base_url;?>public/js/jquery.validate.js?=<?=JS;?>"></script>		
<script type="text/javascript" src="<?=$base_url;?>public/js/almacen/envioproveedor.js?=<?=JS;?>"></script>		
<div id="pagina">
    <div id="zonaContenido">
        <div align="center">
        <div id="tituloForm" class="header"><?php echo $titulo;?></div>
        <div id="frmBusqueda">
            <?php echo validation_errors("<div class='error'>",'</div>');?>
            <?php echo $form_open;?>
                <table width="250" cellspacing="0" cellpadding="6" border="0">
                    <tr>
                        <td>Descripci&oacute;n : </td>
                        <td><?php echo $campo;?></td>
                    </tr>
                    <tr>
                        <td><img id="guardar" src="<?=$base_url;?>public/images/icons/botonaceptar.jpg?=<?=IMG;?>" width="85" height="22" border="1"></td>
                        <td><img id="cancelar" src="<?=$base_url;?>public/images/icons/botoncancelar.jpg?=<?=IMG;?>" width="85" height="22" border="1"></td>
                    </tr>
                </table>
                <?php echo $oculto; ?>
                <input type="hidden" name="cod" id="cod" value="<?php echo $cod; ?>" >
            <?php echo $form_close;?>
            <br/>
        </div>
    </div>
  </div>
</div>