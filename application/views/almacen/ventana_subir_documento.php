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
   <?php echo $form_open;?>
    <div id="frmBusqueda" style="width:80%">
        
        
        
    <table class="fuente8" width="100%" cellspacing=0 cellpadding=3 border=0>					
        <tr class="cabeceraTabla" height="25px">
            <td align="center" colspan="3">IMPORTAR DOCUMENTOS</td>
        </tr>
       </table>
    </div>
    <div id="frmBusqueda" style="width:80%;">
    <table class="fuente8" width="100%" id="tabla_resultado" name="tabla_resultado"  align="center" cellspacing="1" cellpadding="3" border="0" >
       
        <tr>
               <td width="35%">Importar Documento:</td>
               <td width="65%"><?php echo $documento;?></td>
           </tr>
         
    </table>
</div>
    <br />
    <div id="divBotones" style="text-align: center; float:left;margin-left: auto;margin-right: auto;width: 98%;margin-top:15px;">
        <img id="loading" src="<?=$base_url;?>public/images/icons/loading.gif?=<?=IMG;?>"  style="visibility: hidden" />
      
        <a href="javascript:;" id="GuardarCarga"><img src="<?=$base_url;?>public/images/icons/botonaceptar.jpg?=<?=IMG;?>" width="85" height="22" class="imgBoton"></a>
        <a href="javascript:;" id="imgCancelarcarga"><img src="<?=$base_url;?>public/images/icons/botoncancelar.jpg?=<?=IMG;?>" width="85" height="22" class="imgBoton"></a>
        <?php echo $form_hidden;?>
    </div>
    <?php echo $form_close;?>
</body>
</html>
