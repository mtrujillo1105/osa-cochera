<html>
	<head>
        <script type="text/javascript" src="<?=$base_url;?>public/js/jquery.js?=<?=JS;?>"></script>
        <script type="text/javascript" src="<?=$base_url;?>public/js/almacen/tipoproveedor.js?=<?=JS;?>"></script>
		        <script type="text/javascript" src="<?=$base_url;?>public/js/funciones.js?=<?=JS;?>"></script>
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
	<!-- Inicio -->
	<div id="VentanaTransparente" style="display:none;">
	  <div class="overlay_absolute"></div>
	  <div id="cargador" style="z-index:2000">
		<table width="100%" height="100%" border="0" class="fuente8">
			<tr valign="middle">
				<td> Por Favor Espere    </td>
				<td><img src="<?=$base_url;?>public/images/icons/cargando.gif?=<?=IMG;?>"  border="0" title="CARGANDO" /><a href="#" id="hider2"></a>	</td>
			</tr>
		</table>
	  </div>
	</div>
	<!-- Fin -->	
	<br>
		<div id="pagina">
			<div id="zonaContenido">
			<div align="center">
				<div id="tituloForm" class="header"><?php echo $titulo_busqueda;?></div>
				<div id="frmBusqueda" >
				<form id="form_busqueda" name="form_busqueda" method="post" action="<?php echo $action;?>">
					<table class="fuente8" width="98%" cellspacing=0 cellpadding=3 border=0>					
					    <tr>
								<td width="16%">Código </td>
								<td width="68%"><input id="txtCodigo" type="text" class="cajaPequena" NAME="txtCodigo" maxlength="30" value="<?php echo $codigo; ?>">
								<td width="5%">&nbsp;</td>
								<td width="5%">&nbsp;</td>
								<td width="6%" align="right"></td>
                        </tr>
                                            <tr>
                                                    <td>Nombre</td>
                                                    <td><input id="txtNombre" name="txtNombre" type="text" class="cajaGrande" maxlength="100" value="<?php echo $nombre; ?>"></td>
                                                    <td>&nbsp;</td>
                                                    <td>&nbsp;</td>
                                            </tr>
					</table>
				</form>
			  </div>
			  <div class="acciones">
			 	<div id="botonBusqueda">
					<ul id="imprimirFamilia" class="lista_botones"><li id="imprimir">Imprimir</li></ul>
					<ul id="nuevaFamilia" class="lista_botones"><li id="nuevo">Nuevo Tipo de Proveedor</li></ul>
					<ul id="limpiarFamilia" class="lista_botones"><li id="limpiar">Limpiar</li></ul>
					<ul id="buscarFamilia" class="lista_botones"><li id="buscar">Buscar</li></ul>   
				</div>
			  <div id="lineaResultado">
			  <table class="fuente7" width="100%" cellspacing="0" cellpadding="3" border="0">
			  	<tr>
				<td width="100%" align="left">N de tipos encontrados:&nbsp;<?php echo $registros;?> </td>
				<td width="50%" align="right">
					<input type="hidden" class="cajaPequena" id="codanterior" name="codanterior" value="<?php echo $codanterior?>">
					<input type="hidden" class="cajaPequena" id="codanterior2" name="codanterior2" value="<?php echo $codanterior2?>">
				</td>
			  </table>
				</div>
				 </div>
				<div id="cabeceraResultado" class="header"><?php echo $titulo_tabla;;?></div>
				<div id="frmResultado">
				<table class="fuente8" width="100%" cellspacing="0" cellpadding="3" border="0" id="tablaFamilia">
					<tr class="cabeceraTabla">
						<td width="5%">ITEM</td>
						<td width="10%">COD. INTERNO</td>
                        <td width="10%">COD. USUARIO</td>
						<td width="50%">DESCRIPCION</td>				
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
								<td><div align="left"><?php echo $valor[3];?></div></td>
								<td><div align="center"><?php echo $valor[4];?></div></td>
								<td><div align="center"><?php echo $valor[5];?></div></td>
								<td><div align="center"><?php echo $valor[6];?></div></td>
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
				<br>
				<div align="center" <?php echo $ver_regresar;?>>
					<input type="button" name="btnVolver" id="btnVolver" value="Regresar" onclick="location.href='<?=$base_url;?>index.php/almacen/tipoproveedor/familias/<?php echo $codanterior2;?>'">
				</div>
				<div style="margin-top: 15px;"><?php echo $paginacion;?></div>
				<input type="hidden" id="iniciopagina" name="iniciopagina">
				<input type="hidden" id="cadena_busqueda" name="cadena_busqueda">
				<input type="hidden" name="base_url" id="base_url" value="<?=$base_url;?>">
			</div>
		  </div>			
		</div>
	</body>
</html>
