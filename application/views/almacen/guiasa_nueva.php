<script type="text/javascript" src="<?=$base_url;?>public/js/almacen/guiasa.js?=<?=JS;?>"></script>
<script type="text/javascript" src="<?=$base_url;?>public/js/fancybox/jquery.fancybox-1.3.4.pack.js?=<?=JS;?>"></script>
<link rel="stylesheet" type="text/css" href="<?=$base_url;?>public/js/fancybox/jquery.fancybox-1.3.4.css?=<?=CSS;?>" media="screen" />
<script type="text/javascript">
    $(document).ready(function() {
    $("a#linkVerCliente, a#linkVerProducto").fancybox({
            'width'	     : 700,
            'height'         : 450,
            'autoScale'	     : false,
            'transitionIn'   : 'none',
            'transitionOut'  : 'none',
            'showCloseButton': false,
            'modal'          : true,
            'type'	     : 'iframe'
    });
});
function seleccionar_cliente(codigo,ruc,razon_social){
    $("#cliente").val(codigo);
    $("#ruc").val(ruc);
    $("#nombre_cliente").val(razon_social);
}
function escribe_nombre_unidad_medida(){
    index     = document.getElementById("unidad_medida").selectedIndex;
    nombre = document.getElementById("unidad_medida").options[index].text;
    $("#nombre_unidad_medida").val(nombre);
}
function seleccionar_producto(codigo,interno,familia,stock,costo,flagGenInd){
    $("#producto").val(codigo);
    $("#codproducto").val(interno);
    $("#nombre_familia").val(familia);
    $("#stock").val(stock);
    $("#costo").val(costo);
    $("#cantidad").select();
    $("#flagGenInd").val(flagGenInd);
    listar_unidad_medida_producto(codigo);
}
</script>	
<?php echo $form_open;?>
<div id="zonaContenido" align="center">
    <?php echo validation_errors("<div class='error'>",'</div>');?>
    <div id="tituloForm" class="header"><?php echo $titulo;?></div>
    <div id="frmBusqueda">
        <table class="fuente8" width="98%" cellspacing="0" cellpadding="5" border="0">
            <tr>
                <td width="8%" >N&uacute;mero</td>
                <td width="29%">
                    <?php echo $numero;?>
                <td width="10%">Almacen</td>
                <td width="23%"><?php echo $cboAlmacen;?></td>
                <td width="10%">Fecha</td>
                <td width="23%">
                    <?php echo $fecha;?>
                    <a href="javascript:;" style="display:none;"><img height="16" border="0" width="16" id="Image1" name="Image1" src="<?=$base_url;?>public/images/icons/calendario.png?=<?=IMG;?>"></a>
                </td>
            </tr>
            <tr>
                <td>Personal</td>
                <td><?php echo $nombre_usuario;?></td>
                <td>Cliente </td>
                <td>
                    <?php echo $cliente;?>
                    <?php echo $ruc;?>
                    &nbsp;<?php echo $nombre_cliente;?>
                    <a href="<?=$base_url;?>index.php/empresa/cliente/ventana_busqueda_cliente/" id="linkVerCliente"><img height='16' width='16' src='<?php echo base_url(); ?>/images/ver.png?=<?=IMG;?>' title='Buscar' border='0' /></a>
                </td>
                <td>Motivo mov.</td>
                <td>
                    <?php echo $cboTipoMov;?>
                </td>
            </tr>
        </table>
    </div>
    <div id="frmBusqueda"  <?php echo $hidden;?>>
        <table class="fuente8" width="98%" cellspacing=0 cellpadding=3 border=0>
            <tr>
                <td width="9%">Art&iacute;culo</td>
                <td width="45%">
                    <input name="producto" type="hidden" class="cajaPequena2" id="producto" size="10" maxlength="11">
                    <input name="codproducto" type="text" class="cajaPequena2" id="codproducto" size="10" maxlength="11" onBlur="obtener_producto();" onKeyPress="return numbersonly(this,event,'.');">&nbsp;
                    <input NAME="nombre_producto" type="text" class="cajaGrande" id="nombre_producto" size="15" maxlength="15" readonly="readonly">
                    <a href="<?=$base_url;?>index.php/almacen/producto/ventana_busqueda_producto_x_almacen/<?php if($almacen!='') echo $almacen; else '1'; ?>" <?php if($almacen=='') echo 'style="display:none;"'; ?> id="linkVerProducto"><img height='16' width='16' src='<?php echo base_url(); ?>/images/ver.png?=<?=IMG;?>' title='Buscar' border='0' /></a>
                    <?php //echo $verproducto;?>
                    <input name="stock" type="hidden" id="stock">
                    <input name="costo" type="hidden" id="costo">
                    <input name="simbolo" type="hidden" id="simbolo">
                    <input name="nombre_familia" type="hidden" id="nombre_familia">
                    <input name="flagGenInd" type="hidden" id="flagGenInd">
                </td>
                <td width="6%">Cantidad</td>
                <td width="28%">
                    <input NAME="cantidad" type="text" class="cajaPequena2" id="cantidad" value="0" size="5" maxlength="10" onKeyPress="return numbersonly(this,event,'.');">
                    <select name="unidad_medida" id="unidad_medida" class="comboMedio" onChange="escribe_nombre_unidad_medida();"><option value="0">::Seleccione::</option></select>
                </td>
                <td width="2%"><input type="hidden" name="nombre_unidad_medida" id="nombre_unidad_medida" class="cajaMedia"></td>
                <td width="15%"><div align="right"><a href="javascript:;" onClick="agregar_producto_guiasa();"><img src="<?=$base_url;?>public/images/icons/botonagregar.jpg?=<?=IMG;?>" class="imgBoton" align="absbottom"></a></div></td>
            </tr>
        </table>
    </div>
    <div id="frmBusqueda">
        <table class="fuente8" width="100%" cellspacing="0" cellpadding="3" border="1" id="Table1">
            <tr class="cabeceraTabla">
                <td width="3%"><div align="center">&nbsp;</div></td>
                <td width="5%"><div align="center">ITEM</div></td>
                <td width="10%"><div align="center">C&Oacute;DIGO</div></td>
                <td width="66%"><div align="center">DESCRIPCI&Oacute;N</div></td>
                <td width="8%"><div align="center">CANTIDAD</div></td>
                <td width="8%"><div align="center">UNIDAD</div></td>
            </tr>
        </table>
    </div>
    <div id="lineaResultado2">
        <table width="100%" border="0" cellpadding="0" cellspacing="0">
            <tr>
                <td valign="top">
                    <table id="tblDetalleOcompra" class="fuente8" width="100%" border="0">
                    <?php
                    if(count($detalle)>0){
                        foreach($detalle as $indice=>$valor){
                        $detguiasa          = $valor->GUIASADETP_Codigo;
                        $prodproducto       = $valor->PRODCTOP_Codigo;
                        $unidad_medida      = $valor->UNDMED_Codigo;
                        $codigo_interno     = $valor->PROD_CodigoInterno;
                        $prodcantidad       = $valor->GUIASADETC_Cantidad;
                        $nombre_producto    = $valor->GUIASADETC_Descripcion;
                        $nombre_unidad      =  $valor->UNDMED_Simbolo;
                        $prodcosto          = $valor->GUIASADETC_Costo;
                        $GenInd             = $valor->GenInd;
                         if(($indice+1)%2==0){$clase="itemParTabla";}else{$clase="itemImparTabla";}
                    ?>
                      <tr class="<?php echo $clase;?>">
                        <td width="3%"><div align="center"><font color="red"><strong><a href="#" onclick="eliminar_producto_guiasa(this);"><span style="border:1px solid red;background: #ffffff;">&nbsp;X&nbsp;</span></a></strong></font></div></td>
                        <td width="5%"><div align="center"><?php echo $indice+1;?></div></td>
                        <td width="10%">
                            <div align="center">
                                <?php echo $codigo_interno;?>
                                <input type="hidden" class="cajaMinima" name="prodcodigo[<?php echo $indice;?>]" id="prodcodigo[<?php echo $indice;?>]" value="<?php echo $prodproducto;?>">
                                <input type="hidden" class="cajaMinima" name="produnidad[<?php echo $indice;?>]" id="produnidad[<?php echo $indice;?>]" value="<?php echo $unidad_medida;?>">                            
                                <input type="hidden" class="cajaMinima" name="flagGenIndDet[<?php echo $indice;?>]" id="flagGenInd[<?php echo $indice;?>]" value="<?php echo $GenInd;?>">
                            </div>
                        </td>
                        <td width="66%"><div align="left">      
                            <input type="text" class="cajaSuperGrande" name="proddescri[<?php echo $indice;?>]" id="proddescri[<?php echo $indice;?>]" value="<?php echo $nombre_producto;?>">   
                        </div></td>
                        <td width="8%">
                            <div align="center">
                                <?php if($GenInd=="I"):?>
                                <a href="javascript:;" onclick="ventana_producto_serie2(<?php echo $indice;?>)"><img src="<?=$base_url;?>public/images/icons/flag-green_icon.png?=<?=IMG;?>" width="20" height="20" border="0"/></a>
                                <?php endif;?>
                                <input type="text" class="cajaPequena2" name="prodcantidad[<?php echo $indice;?>]" id="prodcantidad[<?php echo $indice;?>]" value="<?php echo $prodcantidad;?>" onKeyPress="return numbersonly(this,event,'.');">&nbsp;</div></td>
                        <td width="8%">
                            <div align="center">
                                <?php echo $nombre_unidad;?>
                                <input type="hidden" class="cajaMinima" name="detaccion[<?php echo $indice;?>]" id="detaccion[<?php echo $indice;?>]" value="m">
                                <input type="hidden" class="cajaMinima" name="detguiasa[<?php echo $indice;?>]" id="detguiasa[<?php echo $indice;?>]" value="<?php echo $detguiasa;?>">
                                <input type="hidden" class="cajaPequena2" name="prodcosto[<?php echo $indice;?>]" id="prodcosto[<?php echo $indice;?>]" readonly="readonly" value="<?php echo $prodcosto;?>">
                            </div>
                        </td>
                      </tr>
                        <?php
                    }
                    }
                    ?>
                    </table>
                </td>
            </tr>
        </table>
    </div>
    <div id="frmBusqueda">
        <table class="fuente8" width="100%" border="0" cellpadding="3" cellspacing="5">
            <tr>
                <td valign="top">Observaci&oacute;n</td>
                <td colspan="5" align="left"><?php echo $observacion;?></td>
            </tr>
        </table>
    </div>
    <div style="position: relative;top:25px">
        <a href="javascript:;" id="grabarGuiasa"><img src="<?=$base_url;?>public/images/icons/botonaceptar.jpg?=<?=IMG;?>" width="85" height="22" class="imgBoton" ></a>
        <a href="javascript:;" id="limpiarGuiasa"><img src="<?=$base_url;?>public/images/icons/botonlimpiar.jpg?=<?=IMG;?>" width="69" height="22" class="imgBoton" ></a>
        <a href="javascript:;" id="cancelarGuiasa"><img src="<?=$base_url;?>public/images/icons/botoncancelar.jpg?=<?=IMG;?>" width="85" height="22" class="imgBoton" ></a>
        <?php echo $oculto;?>
    </div>
    </div>
<?php echo $form_close;?>