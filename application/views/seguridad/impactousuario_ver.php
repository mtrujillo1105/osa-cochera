<script type="text/javascript" src="<?=$base_url;?>public/js/jquery.metadata.js?=<?=JS;?>"></script>
<script type="text/javascript" src="<?=$base_url;?>public/js/jquery.validate.js?=<?=JS;?>"></script>		
<script type="text/javascript" src="<?=$base_url;?>public/js/seguridad/impactousuario.js?=<?=JS;?>"></script>		
<div id="pagina">
    <div id="zonaContenido">
        <div align="center">
        <div id="tituloForm" class="header"><?php echo $titulo;?></div>
        <div id="formBusqueda">
            <table width="250" cellspacing="10" cellpadding="6" border="0">
                <?php 
                    foreach($lista as $indice=>$valor){
                ?>
                <tr>
                    <td>Usuario : </td>
                    <td><?php echo $valor[2];?></td>
                </tr>
                
                <tr>
                    <td>Fecha de Registro : </td>
                    <td><?php echo $valor[3];?></td>
                </tr>
                <?php
                    }
                ?>
            </table>
            <br/>
            <?php echo $oculto;?>
        </div>
        <div id="botonBusqueda">
            <a href="#" onclick="atras_recepcionproveedor();"><img src="<?=$base_url;?>public/images/icons/botonaceptar.jpg?=<?=IMG;?>" width="85" height="22" border="1"></a>
        </div>
    </div>
  </div>
</div>