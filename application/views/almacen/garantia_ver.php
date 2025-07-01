<script type="text/javascript" src="<?=$base_url;?>public/js/almacen/garantia.js?=<?=JS;?>"></script>
<div id="pagina">
    <div id="zonaContenido">
        <div align="center">
        <div id="tituloForm" class="header"><?php echo $titulo;?></div>
        <div id="frmBusqueda">
            <table class="fuente8" width="98%" cellspacing="0" cellpadding="3" border="0">
                <tr>
                    <td width="16%"><b>CODIGO DE GARANTIA:</b> </td>
                    <td width="30%"><?php echo $garantia;?></td>
                    <td width="12%"><b>CLIENTE:</b></td>
                    <td width="42%" colspan="2"><?php echo $cliente;?></td>
                </tr>
                <tr>
                    <td width="16%"><b>PRODUCTO:</b></td>
                    <td width="30%"><?php echo $producto;?></td>
                    <td width="12%"><b>NUMERO DE COMPROBANTE:</b></td>
                    <td width="42%" colspan="2"><?php echo $comprobante;?></td>
                </tr>
                <tr>
                    <td width="16%"><b>DESCRIPCION DE GARANTIA:</b></td>
                    <td width="30%"><?php echo $descripcion;?></td>
                    <td width="12%"><b>CONTACTO:</b></td>
                    <td width="42%" colspan="2"><?php echo $contacto;?></td>
                </tr>
                <tr>
                    <td width="16%"><b>NEXTEL:</b></td>
                    <td width="30%"><?php echo $nextel;?></td>
                    <td width="12%"><b>TELEFONO:</b></td>
                    <td width="42%" colspan="2"><?php echo $telefono;?></td>
                </tr>
                
                
                <tr>
                    <td width="16%"><b>CELULAR:</b></td>
                    <td width="30%"><?php echo $celular;?></td>
                    <td width="12%"><b>E-MAIL:</b></td>
                    <td width="42%" colspan="2"><?php echo $email;?></td>
                </tr>
                <tr>
                    <td width="16%"><b>DESCRIPCION DE ACCESORIOS:</b></td>
                    <td width="30%"><?php echo $accesorio;?></td>
                    <td width="12%"><b>DESCRIPCION DE FALLAS:</b></td>
                    <td width="42%" colspan="2"><?php echo $falla;?></td>
                </tr>
                <tr>
                    <td width="16%"><b>COMENTARIOS:</b></td>
                    <td width="30%"><?php echo $comentario;?></td>
                    <td width="12%"><b>FECHA DE REGISTRO:</b></td>
                    <td width="42%" colspan="2"><?php echo $fecha_registro;?></td>
                </tr>
                
                <tr>
                    <td width="16%"><b>ESTADO ACTUAL:</b></td>
                    <td colspan="4"><?php echo $estado;?></td>
                </tr>
           
            </table>
            <?php echo $oculto;?>
        </div>
        <div id="botonBusqueda">
          <a href="#" onclick="atras_garantia();"><img src="<?=$base_url;?>public/images/icons/botonaceptar.jpg?=<?=IMG;?>" width="85" height="22" border="1"></a>
        </div>
      </div>
    </div>
</div>