<script type="text/javascript" src="<?=$base_url;?>public/js/almacen/tipoproducto.js?=<?=JS;?>"></script>				
<div id="pagina">
    <div id="zonaContenido">
        <div align="center">
        <div id="tituloForm" class="header"><?php echo $titulo;?></div>
        <div id="frmBusqueda">
            <table class="fuente8" width="98%" cellspacing="0" cellpadding="3" border="0">
                <tr>
                    <td width="15%">C&oacute;digo</td>
                    <td width="85%" colspan="2"><?php echo $datos_tipoprod[0]->TIPPROD_Codigo;?></td>
                </tr>
                <tr>
                    <td width="15%">Nombre</td>
                <td width="85%" colspan="2"><?php echo $datos_tipoprod[0]->TIPPROD_Descripcion;?></td>
                    <?php echo $oculto;?>
                </tr>
            </table>
            <p style="text-align: left; width:98%"><b>LISTA DE ATRIBUTOS</b></p>
            <table id="tblPlantilla" width="98%" class="fuente8" width="98%" cellspacing="0" cellpadding="6" border="1">
                <tr align="center" bgcolor="#BBBB20" height="10px;">
                    <td width="25">Nro</td>
                    <td>Atributo</td>
                    <td>Tipo</td>
                </tr>
                <?php 
                $sec=0;
                foreach($lista_atributos as $valor){
                    $sec++;
                ?>
                <tr bgcolor="#ffffff">
                    <td align="center"><?php echo $sec; ?></td>
                    <td align="left"><?php echo $valor->ATRIB_Descripcion;?></td>
                    <td align="left"><?php $temp=array('1'=>'Numérico', '2'=>'Fecha', '3'=>'Texto'); echo $temp[$valor->ATRIB_TipoAtributo];?></td>
                </tr>
                <?php } ?>
            </table>
            <br /><br />
        </div>
        <div id="botonBusqueda">
            <a href="#" onclick="atras_tipoprod();"><img src="<?=$base_url;?>public/images/icons/botonaceptar.jpg?=<?=IMG;?>" width="85" height="22" border="1"></a>
        </div>
        </div>
    </div>
</div>