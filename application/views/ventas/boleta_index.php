<?php
$nombre_persona = $this->session->userdata('nombre_persona');
$persona        = $this->session->userdata('persona');
$usuario        = $this->session->userdata('usuario');
$url            = base_url()."index.php";
if(empty($persona)) header("location:$url");
?>
<html>
	<head>
		<title><?php echo TITULO;?></title>
		<link rel="stylesheet" href="<?php echo URL_CSS;?>estilos.css?=<?=CSS;?>" type="text/css">
		<link rel="stylesheet" href="<?php echo URL_CSS;?>theme.css?=<?=CSS;?>" type="text/css">		
		<script language="javaScript" src="<?php echo URL_JS;?>JSCookMenu.js?=<?=JS;?>"></script>
		<script language="javaScript" src="<?php echo URL_JS;?>theme.js?=<?=JS;?>"></script>	
        <script type="text/javascript" src="<?php echo URL_BASE;?>public/js/jquery.js?=<?=JS;?>"></script>
        <script type="text/javascript" src="<?php echo URL_BASE;?>public/js/ventas.js?=<?=JS;?>"></script>
		<script type="text/javascript" src="<?php echo URL_BASE;?>public/js/jquery.validate.min.js?=<?=JS;?>"></script>					
		<script language="javascript">
			var cursor;
			if (document.all) {
			// Está utilizando EXPLORER
			cursor='hand';
			} else {
			// Está utilizando MOZILLA/NETSCAPE
			cursor='pointer';
			}
		</script>		
	</head>
	<body>
	<div id="contenedor" align="center"><img src="<?php echo URL_IMAGE;?>2.jpg?=<?=IMG;?>" height="81"></div>
	<div id="MenuAplicacion" align="center"></div>
	<?php require_once "menu.php";?>
	<div class="fuente8" align="right" style="width:95%;"><?php echo anchor('mantenimiento/editar_cuenta/'.$usuario,$nombre_persona);?></div>	
	<br>
		<div id="pagina">
			<div id="zonaContenido">
			<div align="center">
				<div id="tituloForm" class="header"><?php echo $titulo_busqueda;?></div>
				<form id="form_busquedaCargo" name="form_busquedaCargo" method="post" action="<?=$base_url;?>index.php/mantenimiento/buscar_cargos">
                <div id="frmBusqueda" >
					<table class="fuente8" width="98%" cellspacing="0" cellpadding="3" border="0">
						<tr>
							<td align='left' width="10%">Fecha Inicial</td>
							<td align='left' width="40%"><input id="txtCargo" name="txtCargo" type="text" class="cajaPequena" maxlength="45" value=""></td>
							<td align='left' width="10%">Fecha final</td>
							<td><input id="txtCargo" name="txtCargo" type="text" class="cajaPequena" maxlength="45" value=""></td>
						</tr>
						<tr>
                             <td align='left'>N&uacute;mero</td>
							<td align='left'><input id="txtCargo" name="txtCargo" type="text" class="cajaPequena" maxlength="45" value=""></td>
							<td align='left'>Cotizaci&oacute;n</td>
							<td align='left'><input id="txtCargo" name="txtCargo" type="text" class="cajaPequena" maxlength="45" value=""></td>
						</tr>
						<tr>
                             <td align='left'>Pedido</td>
							<td align='left'><input id="txtCargo" name="txtCargo" type="text" class="cajaPequena" maxlength="45" value=""></td>
							<td align='left'>Situaci&oacute;n</td>
							<td align='left'>
                                   <select name="conGuiain" id="conGuiain" class="comboPequeno">
                                      <option value="" selected>:: :: :: :: ::</option>
                                       <option value="">Pend.</option>
                                        <option value="">Atend.</option>
                                 </select>
                            </td>
						</tr>
						<tr>
							<td align='left'>Cliente</td>
							<td align='left'><input id="txtCargo" name="txtCargo" type="text" class="cajaMedia" maxlength="45" value=""></td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
						</tr>
					</table>
			  </div>
			 	<div id="botonBusqueda">
					<a href="#" id="buscarBoleta"><img src="<?php echo URL_IMAGE;?>botonbuscar.jpg?=<?=IMG;?>" width="69" height="22" border="1" onMouseOver="style.cursor=cursor"></a>
					<a href="#" id="limpiarBoleta"><img src="<?php echo URL_IMAGE;?>botonlimpiar.jpg?=<?=IMG;?>" width="69" height="22" border="1" onClick="document.getElementById('form_busqueda').reset();" onMouseOver="style.cursor=cursor"></a>
					<a href="#" id="nuevaBoleta"><img src="<?php echo URL_IMAGE;?>botonnuevoproveedor.jpg?=<?=IMG;?>" width="130" height="22" border="1" onMouseOver="style.cursor=cursor"></a>
					<a href="#" id="imprimirBoleta"><img src="<?php echo URL_IMAGE;?>botonimprimir.jpg?=<?=IMG;?>" width="79" height="22" border="1" onClick="imprimir()" onMouseOver="style.cursor=cursor"></a>
				</div>
			  <div id="lineaResultado">
			  <table class="fuente8" width="80%" cellspacing=0 cellpadding=3 border=0>
			  	<tr>
				<td width="50%" align="left">N de cargos encontrados:&nbsp;<?php echo $registros;?> </td>
				<td width="50%" align="right">&nbsp;</td>
			  </table>
				</div>
				<div id="cabeceraResultado" class="header"><?php echo $titulo_tabla;;?></div>
				<div id="frmResultado">
				<table class="fuente8" width="100%" cellspacing="0" cellpadding="3" border="0" ID="Table1">
					<tr class="cabeceraTabla">
						<td width="5%">ITEM</td>
                        <td width="5%">FECHA</td>
                        <td width="5%">NUMERO</td>
                        <td width="5%">PRESUPUESTO</td>
						<td width="46%">RAZON SOCIAL</td>
                        <td width="7%">TOTAL</td>
                        <td width="7%">GUIA ING.</td>
						<td width="5%">&nbsp;</td>
						<td width="5%">&nbsp;</td>
						<td width="5%">&nbsp;</td>
					</tr>					
						<?php
						if(count($lista)>0){
						foreach($lista as $indice=>$valor){
							$class = $indice%2==0?'itemParTabla':'itemImparTabla';
							?>
							<tr class="<?php echo $class;?>">
								<td><div align="center"><?php echo $valor[0];?></div></td>
								<td><div align="center"><?php echo $valor[1];?></div></td>
								<td><div align="center"><?php echo $valor[2];?></div></td>
								<td><div align="center"><?php echo $valor[3];?></div></td>
								<td><div align="left"><?php echo $valor[4];?></div></td>
                                <td><div align="right"><?php echo $valor[5];?></div></td>
                                <td><div align="center"><?php echo $valor[6];?></div></td>
                                <td><div align="center"><?php echo $valor[7];?></div></td>
                                <td><div align="center"><?php echo $valor[8];?></div></td>
                                <td><div align="center"><?php echo $valor[9];?></div></td>
							</tr>
							<?php
							}
						}
						else{
						?>
						<table width="100%" cellspacing="0" cellpadding="3" border="0" class="fuente8">
							<tbody>
								<tr>
									<td width="100%" class="mensaje">No hay ning&uacute;n registro que cumpla con los criterios de b&uacute;squeda</td>
								</tr>
							</tbody>
						</table>	
						<?php
						}
						?>
				</table>
				</div>
				<?php echo $paginacion;?>
				<input type="hidden" id="iniciopagina" name="iniciopagina">
				<input type="hidden" id="cadena_busqueda" name="cadena_busqueda">
				<input type="hidden" name="base_url" id="base_url" value="<?=$base_url;?>">
			</form>
				<div id="lineaResultado" style="display:none;">
					<iframe width="100%" height="250" id="frame_rejilla" name="frame_rejilla" frameborder="0">
						<ilayer width="100%" height="250" id="frame_rejilla" name="frame_rejilla"></ilayer>
					</iframe>
					<iframe id="frame_datos" name="frame_datos" width="0" height="0" frameborder="0">
					<ilayer width="0" height="0" id="frame_datos" name="frame_datos"></ilayer>
					</iframe>
				</div>
			</div>
		  </div>			
		</div>
	</body>
</html>
