<html>
<head>
   <title><?php echo TITULO;?></title>
   <link href="<?=$base_url;?>public/css/estilos.css?=<?=CSS;?>" type="text/css" rel="stylesheet">
    <script type="text/javascript" src="<?=$base_url;?>public/js/jquery.js?=<?=JS;?>"></script>
   <script type="text/javascript" src="<?=$base_url;?>public/js/funciones.js?=<?=JS;?>"></script>
   <script type="text/javascript" src="<?=$base_url;?>public/js/almacen/producto.js?=<?=JS;?>"></script>

   <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
   
</head>
<body>
<div align="center">
<div id="frmBusqueda" style="width:80%"></div>
    <div id="frmBusqueda" style="width:80%;">
    <table class="fuente8" width="100%" id="tabla_resultado" name="tabla_resultado"  align="center" cellspacing="1" cellpadding="3" border="0" >
       
        <tr>
               <td><p>&nbsp;</p>
               <CENTER> <?php echo $mensaje;?></CENTER></td>
           </tr>
         
    </table>
</div>
    <br />
    <div id="divBotones" style="text-align: center; float:left;margin-left: auto;margin-right: auto;width: 98%;margin-top:15px;">
       
<a href="javascript:;" id="imgCancelarcarga"><img src="<?=$base_url;?>public/images/icons/botoncancelar.jpg?=<?=IMG;?>" width="85" height="22" class="imgBoton"></a>
      
    </div>
</body>
</html>
