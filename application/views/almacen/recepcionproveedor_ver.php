<script type="text/javascript" src="<?=$base_url;?>public/js/jquery.metadata.js?=<?=JS;?>"></script>
<script type="text/javascript" src="<?=$base_url;?>public/js/jquery.validate.js?=<?=JS;?>"></script>		
<script type="text/javascript" src="<?=$base_url;?>public/js/almacen/recepcionproveedor.js?=<?=JS;?>"></script>		
<div id="pagina">
    <div id="zonaContenido">
        <div align="center">
        <div id="tituloForm" class="header"><?php echo $titulo;?></div>
        <div id="frmBusqueda">
            <table width="250" cellspacing="0" cellpadding="6" border="0">
                <?php 
                    foreach($lista as $indice=>$valor){
                ?>
                <tr>
                 <td>Proveedor</td>
                  <td><?php echo $valor[5];?></td>
                  
                </tr>
                <tr>
                 <td>Producto</td>
                  <td><?php echo $valor[6];?></td>
                </tr>
                <tr>
                    <td>Observacion : </td>
                    <td><?php echo $valor[2];?></td>
                </tr>
                <tr>
                  <td>Tipo Solucion:</td>
                  <td><?php echo $valor[4];?></td>
                </tr>
                <tr>
                    <td>Fecha de Registro : </td>
                    <td><?php echo substr($valor[3],0,10);?></td>
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