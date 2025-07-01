<script type="text/javascript" src="<?=$base_url;?>public/js/jquery.js?=<?=JS;?>"></script>
<script type="text/javascript" src="<?=$base_url;?>public/js/almacen/producto.js?=<?=JS;?>"></script>
<script type="text/javascript">
jQuery(document).ready(function(){
    modo = $("#modo").val();
    tipo = $("#tipo").val();
    if(modo=='insertar'){
        $("#nombres").val('&nbsp;');
        $("#paterno").val('&nbsp;');
        $("#ruc").focus();
        $("#cboSexo").val('0');
    }
    else if(modo=='modificar'){
        if(tipo=='0'){
            $("#ruc").val('11111111111');
        }
        else if(tipo=='1'){
            $("#nombres").val('&nbsp;');
            $("#paterno").val('&nbsp;');
            $("#cboSexo").val('0');
        }
    }
});
function cargar_familia(familia,nombre){
    document.getElementById('familia').value = familia;
    document.getElementById('nombre_familia').value = nombre;
}
</script>
<br>
<div id="pagina">
    <div id="zonaContenido">
        <div align="center">
        <div id="tituloForm" class="header"><?php echo $titulo;?></div>
        <div id="frmBusqueda">
        <?php echo validation_errors("<div class='error'>",'</div>');?>
        <form id="frmProducto" name="frmProducto" method="post" enctype="multipart/form-data" action="">
    <div id="nuevoRegistro" style="display:none;float:right;width:150px;height:20px;border:0px solid #000;margin-top:7px;"><a href="#">Nuevo</a></div><br><br>
                <div id="divPrincipales">
                        <table class="fuente8" width="98%" cellspacing="0" cellpadding="6" border="0">
                                <tr>
                                        <td width="16%">Familia (*)</td>
                                        <td width="42%"><?php echo $nombre_familia;?></td>
                                        <td width="16%">C&oacute;digo Producto</td>
                                        <td width="42%"><?php echo $producto;?></td>
                                </tr>
                                <tr>
                                        <td width="16%">Nombre Producto</td>
                                        <td width="42%"><?php echo $nombre_producto;?></td>
                                    <td width="16%">Descripci&oacute;n Producto</td>
                                    <td width="42%"><?php echo $descripcion_breve;?></td>
                                </tr>

                                <tr>
                                        <td width="16%">Fabricante (*)</td>
                                        <td width="42%"><?php echo $nombre_fabricante;?> </td>
                                        <td width="16%">&nbsp;</td>
                                        <td width="42%">&nbsp;</td>
                                </tr>

                                <tr>
                                        <td colspan="4"><div align="left"><?php echo $filaunidad;?></div></td>
                                </tr>
    <tr>
                                        <td width="16%">Tipo Producto</td>
                                        <td width="42%"><?php echo $nombre_tipo_producto;?></td>
                                        <td width="16%">&nbsp;</td>
                                        <td width="42%">&nbsp;</td>
    </tr>
                        </table>
                        <hr width="98%">
                                <div id="divAtributos"><?php echo $fila;?></div>
                        <hr width="98%">
    <div id="divGenerales">
    <table class="fuente8" width="98%" cellspacing="0" cellpadding="6" border="0">
    <tr>
      <td width="16%" valign="top">Comentario</td>
      <td width="42%">
                                        <?php echo $comentario;?>
                                  </td>
                                  <td width="16%" valign="top">Estado</td>
                                  <td width="42%" valign="top"><?php echo $estado;?></td>
    </tr>
    <tr style="display:none;">
        <td width="16%">Imagen</td>
        <td colspan="3">
                                                <input name="imagen" type="file" class="cajaMedia" id="imagen"/>
                                        </td>
    </tr>
    </table>
    </div>
         </div>
      
    </form>
    </div>
          <div id="botonBusqueda">
        <a href="javascript:;" onClick="atras_articulo();"><img src="<?=$base_url;?>public/images/icons/botonaceptar.jpg?=<?=IMG;?>" width="85" height="22" border="1"></a>
        <?php echo $oculto;?>
        </div>
    </div>
    </div>
</div>