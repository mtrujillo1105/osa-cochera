<?php
$nombre = $this->session->userdata('nombre');
$persona        = $this->session->userdata('persona');
$usuario        = $this->session->userdata('usuario');
$url            = base_url()."index.php";
if(empty($persona)) header("location:$url");
?>
<html>
<head>
	<title><?php echo TITULO;?></title>
	<link href="<?php echo URL_CSS;?>estilos.css?=<?=CSS;?>" type="text/css" rel="stylesheet">
        <script type="text/javascript" src="<?php echo URL_BASE;?>public/js/jquery.js?=<?=JS;?>"></script>
        <script type="text/javascript" src="<?php echo URL_BASE;?>public/js/mantenimiento.js?=<?=JS;?>"></script>
	<script type="text/javascript" src="<?php echo URL_BASE;?>public/js/jquery.metadata.js?=<?=JS;?>"></script>
	<script type="text/javascript" src="<?php echo URL_BASE;?>public/js/jquery.validate.js?=<?=JS;?>"></script>		
</head>
	<body>
		<div id="pagina">
			<div id="zonaContenido">
				<div align="center">
				<div id="tituloForm" class="header"><?php echo $titulo;?></div>
				<div id="frmBusqueda">
					
                    <div id="datosProyecto">
                        <table class="fuente8" width="98%" cellspacing=0 cellpadding="6" border="0">
                                <tr>
                                  <td>Nombre de Caja</td>
                                  <td>
                                     <?php echo $nombres;?>
                                  </td>                                  
                                </tr>
                                <tr>
                                    <td>Tipo de Caja</td>
                                    <td><?php echo $tipoCaja;?></td>
                                 
                                  
                                </tr>
                                <tr>
                                    <td>Observaciones</td>
                                    <td><?php echo $observaciones;?></td>
                                </tr>                              
                                
                        </table>
                        </div>					   
			  </div>
				<div id="botonBusqueda">
				<input type="hidden" name="base_url" id="base_url" value="<?=$base_url;?>">
				<a href="#" onclick="atras_proyecto();"><img src="<?=$base_url;?>public/images/icons/botonaceptar.jpg?=<?=IMG;?>" width="85" height="22" border="1" onMouseOver="style.cursor=cursor"></a>
			  </div>
		  </div>
		  </div>
		</div>
	</body>
</html>
