<html>
	<head>	
        <script type="text/javascript" src="<?=$base_url;?>public/js/jquery.js?=<?=JS;?>"></script>
		<script type="text/javascript" src="<?=$base_url;?>public/js/jquery.metadata.js?=<?=JS;?>"></script>
		<script type="text/javascript" src="<?=$base_url;?>public/js/jquery.validate.js?=<?=JS;?>"></script>
		<script type="text/javascript" src="<?=$base_url;?>public/js/compras/cuadrocom.js?=<?=JS;?>"></script>
		<script type="text/javascript" src="<?=$base_url;?>public/js/funciones.js?=<?=JS;?>"></script>
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
	<div id="pagina">
			<div id="zonaContenido">
				<div align="center">
				<div id="tituloForm" class="header"><?php echo $titulo;?></div>
				<div id="frmBusqueda">
				<form id="frmCuadro" name="frmCuadro" method="post" action="">
					<div id="container" class="container">
					</div><br><br>				
					<div id="datosGenerales">
                       <div id="datosPedido" >
                        <table class="fuente8" width="98%" cellspacing="0" cellpadding="4" border="0">
                                <tr>
                                    <td width="16%">Pedido&nbsp;(*)</td>
                                    <td>
                                        <?php echo $cboPedidos; ?>
                                    </td>
                                  <td>Descripci&oacute;n de Cuadro Comparativo </td>
                                  <td><input name="cuadro_descripcion" type="text" class="cajaMedia" id="cuadro_descripcion" size="15" value="<?php echo $cuadro_descripcion;?>">
                                  </td>
                                </tr>
                        </table>
                        </div>
					</div>
				 <div id="datosCuadro">
					
				 </div>
				<div style="margin-top:20px; text-align: center">
          <a href="#" id="imgGuardarCuadro"><img src="<?=$base_url;?>public/images/icons/botonaceptar.jpg?=<?=IMG;?>" width="85" height="22" class="imgBoton" onMouseOver="style.cursor=cursor"></a>
					<a href="#" id="imgCancelarCuadro"><img src="<?=$base_url;?>public/images/icons/botoncancelar.jpg?=<?=IMG;?>" width="85" height="22" class="imgBoton" onMouseOver="style.cursor=cursor"></a>
					<input id="accion" name="accion" value="alta" type="hidden">
					<input type="hidden" name="modo" id="modo" value="<?php echo $modo; ?>">
					<input type="hidden" name="opcion" id="opcion" value="1">
					<input type="hidden" name="base_url" id="base_url" value="<?=$base_url;?>">
					<input type="hidden" id="id" name="id" value="<?php echo $id;?>">
          </div>
			  </form>
		  </div>
		  </div>
		</div></div>
	</body>
</html>