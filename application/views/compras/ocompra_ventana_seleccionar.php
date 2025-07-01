<link href="<?=$base_url;?>public/css/estilos.css?=<?=CSS;?>" type="text/css" rel="stylesheet">
<script type="text/javascript" src="<?=$base_url;?>public/js/jquery.js?=<?=JS;?>"></script>
<script type="text/javascript" src="<?=$base_url;?>public/js/compras/ocompra_popup.js?=<?=JS;?>"></script>
<script type="text/javascript" src="<?=$base_url;?>public/js/funciones.js?=<?=JS;?>"></script>
<script type="text/javascript">
	$(document).ready(function() {
		
		
			
	 });
</script>
<form id="frmOventa" id="" method="post" action="" onsubmit="return valida_oventa();">
    <div id="zonaContenido" align="center" style="width:750px;height:400px;">
    <div id="tituloForm" class="header" style="width:750px;"><?php echo $titulo;?></div>
<div id="frmBusqueda" style="width:750px;">
    <table class="fuente8" style="width:750px;" cellspacing="0" cellpadding="5" border="0">
		<tr>
			<td width="8%" >O. Compra</td>
			<td width="38%">
				<select name="cboOrden" id="cboOrden" class="comboGrande">
					<option value="0">::Seleccione::</option>
					<?php
					foreach($lista as $value){
						?>
						<option value="<?php echo $value->codigo; ?>"><?php echo $value->numero." - ".$value->proveedor; ?></option>
						<?php
					}
					
					?>
				</select>
			</td>
		</tr>
    </table>
    </div>
    <br>
	<form name="frmSeguimiento" id="frmSeguimiento">
	<div id="data"></div>
    <div id="frmBusqueda" style="width:750px;">
		<table class="fuente8" style="width:750px;" cellspacing="0" cellpadding="3" border="0" ID="tablaGuias">
			<tr class="cabeceraTabla">
				<td width="3%" colspan="6"><div align="center">LISTADO DE GUIAS</div></td>
			</tr>
			<tr class="cabeceraTabla">
				<td width="6%"><div align="center">&nbsp;</div></td>
				<td width="6%"><div align="center">SERIE</div></td>
				<td width="6%"><div align="center">NUMERO</div></td>
				<td><div align="center">RAZON SOCIAL</div></td>
				<td width="8%"><div align="center">TOTAL</div></td>
				<td width="6%"><div align="center">&nbsp;</div></td>
			</tr>
		</table>
		<br />
		<table class="fuente8" style="width:750px;" cellspacing="0" cellpadding="3" border="0" ID="tablaDetalleGuias">
			<tr class="cabeceraTabla">
				<td width="3%" colspan="6"><div align="center">DETALLE DE GUIA</div></td>
			</tr>
			<tr class="cabeceraTabla">
				<td width="6%"><div align="center">CODIGO</div></td>
				<td><div align="center">DESCRIPCION</div></td>
				<td width="6%"><div align="center">CANTIDAD</div></td>
				<td width="6%"><div align="center">P. U.</div></td>
				<td width="6%"><div align="center">IMPORTE</div></td>
			</tr>
		</table>
    </div>
	</form>
    <br />
    <div style="margin-top:70px; clear:both">
		<img id="loading" src="<?=$base_url;?>public/images/icons/loading.gif?=<?=IMG;?>"  style="visibility: hidden" />
		<a href="javascript:;" id="agregarGuias"><img src="<?=$base_url;?>public/images/icons/botonagregar.jpg?=<?=IMG;?>" width="72" height="22" class="imgBoton" ></a>
		<a href="javascript:;" id="limpiarOcompra"><img src="<?=$base_url;?>public/images/icons/botonlimpiar.jpg?=<?=IMG;?>" width="69" height="22" class="imgBoton" ></a>
		<a href="javascript:;" id="cerrarOcompra"><img src="<?=$base_url;?>public/images/icons/botoncerrar.jpg?=<?=IMG;?>" width="70" height="22" class="imgBoton" ></a>
		<input type="hidden" name="tipo_oper" id="tipo_oper" value="<?php echo $tipo_oper; ?>" />
		<input type="hidden" name="base_url" id="base_url" value="<?php echo base_url(); ?>" />
    </div>
</div>
</form>
