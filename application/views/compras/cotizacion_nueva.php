<script type="text/javascript" src="<?=$base_url;?>public/js/jquery.metadata.js?=<?=JS;?>"></script>
<script type="text/javascript" src="<?=$base_url;?>public/js/jquery.validate.js?=<?=JS;?>"></script>
<script type="text/javascript" src="<?=$base_url;?>public/js/compras/cotizacion.js?=<?=JS;?>"></script>
<script type="text/javascript" src="<?=$base_url;?>public/js/empresa/proveedor.js?=<?=JS;?>"></script>
<script type="text/javascript" src="<?=$base_url;?>public/js/almacen/producto.js?=<?=JS;?>"></script>
<script type="text/javascript" src="<?=$base_url;?>public/js/funciones.js?=<?=JS;?>"></script>
<script type="text/javascript">
function seleccionar_proveedor(codigo,ruc,razon_social){
    $("#proveedor").val(codigo);
    $("#ruc").val(ruc);
    $("#nombre_proveedor").val(razon_social);
}
function escribe_nombre_unidad_medida(){
    index     = document.getElementById("unidad_medida").selectedIndex;
    nombre = document.getElementById("unidad_medida").options[index].text;
    $("#nombre_unidad_medida").val(nombre);
}
function seleccionar_producto(codigo,interno,familia,stock,costo)
{
    $("#producto").val(codigo);
    $("#codproducto").val(interno);
    $("#nombre_familia").val(familia);
    $("#stock").val(stock);
    $("#cantidad").select();
    var sel = document.getElementById('unidad_medida');
    var opt = sel.getElementsByTagName("option");
    for(i=1;i<opt.length;i++){
    sel.options[i]=null;
    }
    listar_unidad_medida_producto(codigo);
}
</script>		
<form id="<?php echo $formulario;?>" method="post" action="<?php echo $url_action;?>" onsubmit="return valida_cotizacion();">
<div id="zonaContenido" align="center">
    <?php echo validation_errors("<div class='error'>",'</div>');?>
    <div id="tituloForm" class="header"><?php echo $titulo;?></div>
<div id="frmBusqueda">
<table class="fuente8" width="98%" cellspacing="0" cellpadding="3" border="0">
<tr>
    <td height="30">N&uacute;mero</td>
    <td width="41%" height="30" valign="middle"><input name="numero" type="text" class="cajaPequena2" id="numero" size="10" maxlength="10" readonly="readonly" value="<?php echo $numero;?>"><a href="#"></a></td>
    <td width="12%" height="30" valign="middle">Fecha</td>
    <td width="36%" height="30" valign="middle"><input NAME="fecha" type="text" class="cajaPequena" id="fecha" value="<?php echo $hoy;?>" size="10" maxlength="10" readonly="readonly">
        <a href="#"><img height="16" border="0" width="16" id="Image1" name="Image1" src="<?=$base_url;?>public/images/icons/calendario.png?=<?=IMG;?>"></a>
    </td>
</tr>
  <tr>
        <td width="11%" height="30">Proveedor </td>
        <td height="30" valign="middle">
 <input type="hidden" name="proveedor" id="proveedor" size="5" class="cajaPequena2" value="<?php echo $proveedor?>">
 <input type="text" name="ruc" class="cajaPequena2" id="ruc" size="10" maxlength="11" onBlur="obtener_proveedor();" onkeypress="return numbersonly(this,event);" value="<?php echo $ruc;?>">
 &nbsp;<input name="nombre_proveedor" type="text" class="cajaMedia" id="nombre_proveedor" size="15" maxlength="15" readonly="readonly" value="<?php echo $nombre_proveedor;?>">
 <?php echo $verproveedor;?>
</td>
    <td height="30" valign="middle">O.Pedido</td>
    <td height="30" valign="middle">
  <select name="pedido" id="pedido" class="comboMedio" onfocus="<?php echo $focus;?>"><?php echo $cboPedido;?></select>
</td>
  </tr>
  <tr>
<td height="30">Personal</td>
    <td height="30"><input NAME="nombre_usuario" type="text" class="cajaGrande" id="nombre_usuario" size="30" maxlength="30" readonly value="<?php echo $nombre_usuario;?>"></td>
    <td height="30">Condiciones de Pago</td>
    <td height="30"><select name="forma_pago" id="forma_pago" class="comboMedio">
      <?php echo $cboFormaPago;?>
      </select>
</td>
</tr>
  <tr>
    <td height="30">Lugar Entrega:</td>
    <td height="30"><select name="lugar_entrega" id="lugar_entrega" class="comboMedio">
<?php echo $cboLugarEntrega;?>
</select></td>
    <td height="30">Condicion. entrega.</td>
    <td height="30">
 <select name="condicion_entrega" id="condicion_entrega" class="comboMedio">
   <?php echo $cboCondicionEntrega;?>
 </select>
    </td>
</tr>
</table>
</div>
<div id="frmBusqueda">
<table class="fuente8" width="98%" cellspacing=0 cellpadding=3 border="0">
<tr>
<td width="9%">Art&iacute;culo</td>
<td width="43%">
    <input name="producto" type="hidden" class="cajaPequena2" id="producto" size="10" maxlength="11">
    <input name="codproducto" type="text" class="cajaPequena" id="codproducto" size="10" maxlength="50" onBlur="obtener_producto();"> &nbsp;
    <input name="nombre_producto" type="text" class="cajaGrande" id="nombre_producto" size="10" maxlength="15" readonly="readonly">
     <a href="#">
        <?php echo $verproducto;?>
       <input name="stock" type="hidden" id="stock">
       <input name="simbolo" type="hidden" id="simbolo">
       <input name="nombre_familia" type="hidden" id="nombre_familia">
     </a>
</td>
<td width="6%">Cantidad</td>
<td width="28%">
    <input NAME="cantidad" type="text" class="cajaPequena2" id="cantidad" value="0" size="5" maxlength="10" onkeypress="return numbersonly(this,event,'.');">
    <select name="unidad_medida" id="unidad_medida" class="comboMedio" onchange="escribe_nombre_unidad_medida();"><option value="0">::Seleccione::</option></select>
</td>
<td width="2%"><input type="hidden" name="nombre_unidad_medida" id="nombre_unidad_medida" class="cajaMedia"></td>
<td width="15%"><div align="right"><a href="#" onClick="agregar_producto_cotizacion();"><img src="<?=$base_url;?>public/images/icons/botonagregar.jpg?=<?=IMG;?>" class="imgBoton" align="absbottom"></a></div></td>
</tr>
</table>
</div>
<br>
<div id="frmBusqueda">
<table class="fuente8" width="100%" cellspacing="0" cellpadding="3" border="1" ID="Table1">
    <tr class="cabeceraTabla">
        <td width="3%"><div align="center">&nbsp;</div></td>
        <td width="6%"><div align="center">ITEM</div></td>
        <td width="10%"><div align="center">C&Oacute;DIGO</div></td>
        <td width="54%"><div align="center">DESCRIPCI&Oacute;N</div></td>
        <td width="14%"><div align="center">	UNIDAD</div></td>
        <td width="13%"><div align="center">CANTIDAD</div></td>
    </tr>
</table>
</div>
<div id="lineaResultado">
<table width="100%" height="250px;" border="1" cellpadding="0" cellspacing="0">
    <tr>
        <td valign="top">
            <table id="tblDetalleCotizacion" class="fuente8" width="100%" border="0">
                <?php
                if(count($detalle_cotizacion)>0){
                foreach($detalle_cotizacion as $indice=>$valor){
                $detcotiz           = $valor->COTDEP_Codigo;
                $producto           = $valor->PROD_Codigo;
                $unidad_medida      = $valor->UNDMED_Codigo;
                $codigo_interno     = $valor->PROD_CodigoInterno;
                $cantidad           = $valor->COTDEC_Cantidad;
                $nombre_producto = $valor->PROD_Nombre;
                $nombre_unidad    =  $valor->UNDMED_Simbolo;
                if(($indice+1)%2==0){$clase="itemParTabla";}else{$clase="itemImparTabla";}
                ?>
                <tr class="<?php echo $clase;?>" id="row<?php echo $indice;?>">
                    <td width="3%"><div align="center"><font color="red"><strong><a href="#" onclick="eliminar_producto_cotizacion(<?php echo $indice;?>);"><span style="border:1px solid red;background: #ffffff;">&nbsp;X&nbsp;</span></a></strong></font></div></td>
                    <td width="6%"><div align="center"><?php echo $indice+1;?></div></td>
                    <td width="10%"><div align="left"><?php echo $codigo_interno;?></div></td>
                    <td width="54%"><div align="left"><?php echo $nombre_producto;?></div></td>
                    <td width="14%"><div align="center"><?php echo $nombre_unidad;?></div></td>
                    <td width="13%"><div align="center">
                        <input type="hidden" class="cajaMinima" name="detaccion[<?php echo $indice;?>]" id="detaccion[<?php echo $indice;?>]" value="m">
                        <input type="hidden" class="cajaMinima" name="detcotiz[<?php echo $indice;?>]" id="detcotiz[<?php echo $indice;?>]" value="<?php echo $detcotiz;?>">
                        <input type="hidden" class="cajaMinima" name="prodcodigo[<?php echo $indice;?>]" id="prodcodigo[<?php echo $indice;?>]" value="<?php echo $producto;?>">
                        <input type="hidden" class="cajaMinima" name="produnidad[<?php echo $indice;?>]" id="produnidad[<?php echo $indice;?>]" value="<?php echo $unidad_medida;?>">
                        <input type="text" class="cajaPequena2" name="prodcantidad[<?php echo $indice;?>]" id="prodcantidad[<?php echo $indice;?>]" value="<?php echo $cantidad;?>"></div>
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
<div id="frmBusqueda3">
    <table width="100%" border="0" align="right" cellpadding=3 cellspacing=0 class="fuente8">
            <tr>
                    <td width="80%" rowspan="4" align="left">
                            <div style="float: left;padding-left:6px;padding-top: 0px;height:30px;width: 100px;">OBSERVACION</div>
                            <div style="float:left;margin-right: 10px;"><textarea id="observacion" name="observacion" class="fuente8" cols="130" rows="3"><?php echo $observacion;?></textarea></div>
                    </td>
                    <td width="10%" class="busqueda">&nbsp;</td>
                    <td width="10%" align="right">&nbsp;</td>
            </tr>
    </table>
</div>	
<div style="margin-top:15px;">
    <a href="#" id="grabarCotizacion"><img src="<?=$base_url;?>public/images/icons/botonaceptar.jpg?=<?=IMG;?>" width="85" height="22" class="imgBoton" ></a>
    <a href="#" id="limpiarCotizacion"><img src="<?=$base_url;?>public/images/icons/botonlimpiar.jpg?=<?=IMG;?>" width="69" height="22" class="imgBoton" ></a>
    <a href="#" id="cancelarCotizacion"><img src="<?=$base_url;?>public/images/icons/botoncancelar.jpg?=<?=IMG;?>" width="85" height="22" class="imgBoton" ></a>
    <?php echo $oculto?>
</div>		
</div>
</form>