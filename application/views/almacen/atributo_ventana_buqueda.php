<html>
<head>
   <title><?php echo TITULO;?></title>
   <link href="<?=$base_url;?>public/css/estilos.css?=<?=CSS;?>" type="text/css" rel="stylesheet">
   <link rel="stylesheet" href="<?=$base_url;?>public/css/theme.css?=<?=CSS;?>" type="text/css">
    <script type="text/javascript" src="<?=$base_url;?>public/js/jquery.js?=<?=JS;?>"></script>
   <script type="text/javascript" src="<?=$base_url;?>public/js/funciones.js?=<?=JS;?>"></script>
   <script type="text/javascript" src="<?=$base_url;?>public/js/comercialjs.js?=<?=JS;?>"></script>
   <script type="text/javascript" src="<?=$base_url;?>public/js/jquery.metadata.js?=<?=JS;?>"></script>
   <script type="text/javascript" src="<?=$base_url;?>public/js/jquery.validate.js?=<?=JS;?>"></script>
   <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
   <script type="text/javascript">
        jQuery(document).ready(function(){
            $("#buscarAtributo").click(function(){
                $("#form_busquedaAtributo").submit();
            });
        });
        function seleccionar_atributo(codigo,nombre,tipo){
             window.opener.seleccionar_atributo(codigo,nombre,tipo);
             window.close();
        }
   </script>
</head>
<body onload="document.getElementById('nombres').focus();">
<div align="center">
<form name="form_busquedaAtributo" id="form_busquedaAtributo" method="post" action="<?php echo $base_url; ?>">
    <div id="frmBusqueda" style="width:95%">
        <table class="fuente8" width="100%" cellspacing="0" cellpadding="3" border="0">
           <tr class="cabeceraTabla" height="25px">
                <td align="center" colspan="5">ATRIBUTOS</td>
           </tr>
            <tr height="35px">
                <td width="25%">Nombre </td>
                <td width="68%"><input id="txtNombre" type="text" class="cajaGrande" NAME="txtNombre" value="<?php echo $txtNombre; ?>">
                <td width="5%">&nbsp;</td>
                <td width="5%">&nbsp;</td>
                <td width="6%" align="right"></td>
            </tr>
            <tr height="25px">
                <td colspan="4" align="right"><a href="#" ><img id="buscarAtributo" src="<?=$base_url;?>public/images/icons/botonbuscar.jpg?=<?=IMG;?>" border="1" title="Buscar Atributo"></a></td>
            </tr>
        </table>
   </div>
    <div id="lineaResultado" style="width:95%;margin-top: 10px;">
        <table class="fuente8" width="100%" cellspacing=0 cellpadding=3 border=0>
            <tr>
                <td width="50%" align="left">N de atributos encontrados:&nbsp;<?php echo $registros;?></td>
                <td width="50%" align="right">&nbsp;</td>
            </tr>
        </table>
    </div>
   <div id="frmResultado" style="width:95%">
  <table class="fuente8" width="100%" id="tabla_resultado" name="tabla_resultado"  align="center" border="0" cellpadding="4">
       <tr class="cabeceraTabla">
			<td width="5%"><div align="center"><b>Item</b></div></td>
			<td><div align="center"><b>Nombre</b></div></td>
                        <td width="20%"><div align="center"><b>Tipo</b></div></td>
			<td width="7%"><div align="center"></div></td>
       </tr>
       <?php
       $indice = 0;
       foreach($lista as $valor){
            $classfila      = $indice%2==0?"itemImparTabla":"itemParTabla";
            $codigo         = $valor[4];
            $nombre         = $valor[1];
            $tipo           = $valor[2];
       ?>
         <tr class="<?php echo $classfila;?>">
            <td><div align="center"><?php echo $valor[0];?></div></td>
           <td><div align="left"><?php echo $nombre;?></div></td>
           <td><div align="left"><?php echo $tipo;?></div></td>
           <td align="center"><div align="center"><a href="#" onclick="seleccionar_atributo(<?php echo $codigo;?>,'<?php echo $nombre;?>','<?php echo $tipo;?>');"><img src="<?=$base_url;?>public/images/icons/convertir.png?=<?=IMG;?>" border="0" title="Seleccionar"></a></div></td>
       </tr>
       <?php
       $indice++;
       }
       ?>
       
</table>
</div>
<div style="margin-top: 15px;"><?php echo $paginacion;?></div>
<table width="100%" border="0">
  <tr>
    <td>
       <div align="center"><a href="#" onclick="window.close();"><img src="<?=$base_url;?>public/images/icons/botoncerrar.jpg?=<?=IMG;?>" width="70" height="22"  border="1" ></a></div>
    </td>
  </tr>
</table>
</form>
</div>
</body>
</html>