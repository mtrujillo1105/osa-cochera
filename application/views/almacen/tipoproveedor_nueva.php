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
    <link href="<?=$base_url;?>public/css/estilos.css?=<?=CSS;?>" type="text/css" rel="stylesheet">
    <script type="text/javascript" src="<?=$base_url;?>public/js/jquery.js?=<?=JS;?>"></script>
    <script type="text/javascript" src="<?=$base_url;?>public/js/almacen/tipoproveedor.js?=<?=JS;?>"></script>
</head>
<body <?php echo $onload;?>>	
        <div id="zonaContenido">
            <div id="pagina" align="center">
            <div id="tituloForm" style="width:95%" class="header"><?php echo $titulo;?></div>
            <div id="frmBusqueda" style="width:95%"  align='left'>
            <?php echo validation_errors("<div class='error'>",'</div>');?>
            <form id="<?php echo $formulario;?>" method="post" action="<?=$base_url;?>index.php/almacen/tipoproveedor/nueva_familia">
                    <?php echo $fila;?>
                    <input type="hidden" id="codfamilia" name="codfamilia" value="<?php echo $codproducto;?>">
                    <br>					
                    <div id="botonBusqueda" style="width:85%">
                            <a href="#" id="seleccionarFamilia"><img src="<?=$base_url;?>public/images/icons/botonaceptar.jpg?=<?=IMG;?>" width="85" height="22" border="1" ></a>						
                            <a href="#" id="cancelarFamilia"><img src="<?=$base_url;?>public/images/icons/botoncancelar.jpg?=<?=IMG;?>" width="85" height="22" border="1" ></a>
              </div>
            </form>
          </div>
          </div>
        </div>
</body>
</html>