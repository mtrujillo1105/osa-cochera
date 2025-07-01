<script type="text/javascript" src="<?=$base_url;?>public/js/almacen/almacen.js?=<?=JS;?>"></script>
<div id="pagina">
    <div id="zonaContenido">
        <div align="center">
        <div id="tituloForm" class="header"><?php echo $titulo;?></div>
        <div id="frmBusqueda">
            <table class="fuente8" width="98%" cellspacing="0" cellpadding="3" border="0">
                <tr>
                    <td width="15%">NOMBRE ALMACEN</td>
                    <td width="85%" colspan="2"><?php echo $nombre_almacen;?></td>
                </tr>
                <tr>
                    <td width="15%">DIRECCIÓN ALMACEN</td>
                    <td width="85%" colspan="2"><?=$direccion_almacen;?></td>
                </tr>
                <tr>
                    <td width="15%">TIPO ALMACEN</td>
                <td width="85%" colspan="2"><?php echo $nombre_tipoalmacen;?></td>
                    <?php echo $oculto;?>
                </tr>
            </table>
        </div>
        <div id="botonBusqueda">
                <a href="#" onclick="atras_almacen();"><img src="<?=$base_url;?>public/images/icons/botonaceptar.jpg?=<?=IMG;?>" width="85" height="22" border="1"></a>
        </div>
        </div>
    </div>
</div>
