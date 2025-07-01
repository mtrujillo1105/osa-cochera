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
		<link href="<?php echo URL_CSS;?>estilos.css?=<?=CSS;?>" type="text/css" rel="stylesheet">
		<link rel="stylesheet" href="<?php echo URL_CSS;?>theme.css?=<?=CSS;?>" type="text/css">		
		<script language="javaScript" src="<?php echo URL_JS;?>JSCookMenu.js?=<?=JS;?>"></script>
		<script language="javaScript" src="<?php echo URL_JS;?>theme.js?=<?=JS;?>"></script>		
        <script type="text/javascript" src="<?php echo URL_BASE;?>public/js/jquery.js?=<?=JS;?>"></script>
        <script type="text/javascript" src="<?php echo URL_BASE;?>public/js/compras/cotizacion.js?=<?=JS;?>"></script>
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
				<div id="tituloForm" class="header"><?php echo $titulo;?></div>
				<div id="frmBusqueda">
					<table class="fuente8" width="98%" cellspacing="0" cellpadding="3" border="0">
						<tr>
							<td width="15%">C&oacute;digo</td>
							<td width="85%" colspan="2"><?php echo $datos_cargo[0]->CARGP_Codigo;?></td>
					    </tr>
						<tr>
							<td width="15%">Nombre</td>
						    <td width="85%" colspan="2"><?php echo $datos_cargo[0]->CARGC_Descripcion;?></td>
							<?php echo $oculto;?>
					    </tr>						
					</table>
			  </div>
				<div id="botonBusqueda">
					<a href="#" onclick="atras_cargo();"><img src="<?php echo URL_IMAGE;?>botonaceptar.jpg?=<?=IMG;?>" width="85" height="22" border="1"></a>
			  </div>
			 </div>
		  </div>
		</div>
	</body>
</html>
