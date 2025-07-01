	
<script type="text/javascript" src="<?=$base_url;?>public/js/almacen/recepcionproveedor.js?=<?=JS;?>"></script>
<script type="text/javascript" src="<?=$base_url;?>public/js/funciones.js?=<?=JS;?>"></script>		
<script type="text/javascript" src="<?=$base_url;?>public/js/fancybox/jquery.mousewheel-3.0.4.pack.js?=<?=JS;?>"></script>
<script type="text/javascript" src="<?=$base_url;?>public/js/fancybox/jquery.fancybox-1.3.4.pack.js?=<?=JS;?>"></script>
<link rel="stylesheet" type="text/css" href="<?=$base_url;?>public/js/fancybox/jquery.fancybox-1.3.4.css?=<?=CSS;?>" media="screen" />
<script type="text/javascript">
jQuery(document).ready(function(){
    $("a#linkVerProveedor").fancybox({
            'width'          : 700,
            'height'         : 450,
            'autoScale'	 : false,
            'transitionIn'   : 'none',
            'transitionOut'  : 'none',
            'showCloseButton': false,
            'modal'          : true,
            'type'	     : 'iframe'
    }); 
	     
    $("a#linkVerProducto").fancybox({
            'width'          : 800,
            'height'         : 600,
            'autoScale'	 : false,
            'transitionIn'   : 'none',
            'transitionOut'  : 'none',
            'showCloseButton': false,
            'modal'          : true,
            'type'	     : 'iframe'
    }); 
});

 function seleccionar_proveedor(codigo,ruc,razon_social){
                $("#proveedor").val(codigo);
                $("#ruc_proveedor").val(ruc);
                $("#nombre_proveedor").val(razon_social);
             }
function buscar_proveedor(n){
    $("#fila").val(n);
    base_url = $("#base_url").val();
    $('#linkVerProveedor').click();
}
function campos(){
    valor = document.getElementById('solucion').value;
    if(valor == "nota credito") {
        document.getElementById('nota').style.display="inherit";
        document.getElementById('producto').style.display="none"; 
        document.getElementById('codpadre').value ="";
        document.getElementById('nompadre').value ="";
    } 
    else if(valor == "otro producto"){
        document.getElementById('producto').style.display="inherit";  
        document.getElementById('nota').style.display="none";
        document.getElementById('serie').value ="";
        document.getElementById('numero').value ="";
    }
    else{
        document.getElementById('producto').style.display="none";  
        document.getElementById('nota').style.display="none";
        document.getElementById('serie').value ="";
        document.getElementById('numero').value ="";
        document.getElementById('codpadre').value ="";
        document.getElementById('nompadre').value ="";
    }
    
}

function seleccionar_producto(producto,cod_interno,nombre_familia,stock,costo){
     $("#padre").val(producto);
     $("#codpadre").val(cod_interno);
     obtener_nombre_producto(producto);    
}
function obtener_nombre_producto(producto){
        url          = base_url+"index.php/almacen/producto/listar_unidad_medida_producto/"+producto;
	$.getJSON(url,function(data){
		  $.each(data, function(i,item){
                      $("#nompadre").val(item.PROD_Nombre);
		  });
                  
	});
}
</script>
<br>
<form id="frmRecepcionProveedor" name="frmRecepcionProveedor" method="post" enctype="multipart/form-data" action="<?php echo $url_action;?>">
    <div id="pagina">
    <div id="zonaContenido">
            <div align="center">
                <div id="tituloForm" class="header"><?php echo $titulo;?></div>
                <div id="divProducto">
                    <?php echo validation_errors("<div class='error'>",'</div>');?>
                    <div id="container" class="container">
                        <h4>Primero debe completar los siguientes campos antes de enviar.</h4>
                        <ol>
                            <li>
                              <label for="descripcion_producto" class="descripcion_producto">Por favor ingrese la descripcion del envio</label></li>
                        </ol>
                    </div>
                    <?php if(isset($flagGuardado) && $flagGuardado==true) echo '<div class="mensaje_grabar"><img src="'.base_url().'images/icono_aprobar.png?=<?=IMG;?>" width="18" height="15" border=0 alt="Ok" /> Los datos del artículo se guardaron correctamente</div>'; ?>

                   
                <div id="general" style="float:left;width:98%; text-align: left;">
                        <div style="width:100%">
                                  <?php 
               foreach( $garantia as $valores){
                 echo  "
                      <label  style='display:none' id='garantia' >
                     <input type='checkbox'  id='checkGarantia' name='checkGarantia[]' value='".$valores."' checked />
                         </label >";
                      
                  
                   
               }
         ?> 
                            
                            <table class="fuente8" width="98%" cellspacing="0" cellpadding="6" border="0">
                              
                              <tr>

                                <td height="30">Proveedor:</td>
                                <td>
                                <input type="hidden" name="proveedor" value="<?php echo $proveedor; ?>" id="proveedor" size="5" />
                                <input type="text" name="ruc_proveedor" value="<?php echo $ruc_proveedor; ?>" class="cajaGeneral" id="ruc_proveedor" size="10" maxlength="11" onBlur="obtener_proveedor();" onKeyPress="return numbersonly(this,event,'.');" />
                                <input type="text" name="nombre_proveedor" value="<?php echo $nombre_proveedor; ?>"  class="cajaGrande cajaSoloLectura" id="nombre_proveedor" size="40" readonly="readonly" />
                                <a href="<?=$base_url;?>index.php/empresa/proveedor/ventana_busqueda_proveedor/" id="linkVerProveedor"><img height='16' width='16' src='<?php echo base_url(); ?>/images/ver.png?=<?=IMG;?>' title='Buscar' border='0' /></a>
                                </td>
                              </tr>
                             
                              <tr>
                                <td valign="top">Tipo Solucion</td>
                                <td>
                                    <select name="solucion"  id="solucion" onChange="campos()">
                                      <option selected ="selected">::Selecione:: </option>
                                     <option  value ="nota credito">Nota de Credito </option>
                                     <option value ="otro producto" >Otro Producto </option>
                                     <option value ="mismo producto">Mismo Producto </option>
                                    </select>
                                    
                                    <label  style="display:none" id="producto" >
                                      <input type="hidden" name="padre" id="padre" value="<?php echo $padre; ?>" />
                                      Codigo:
                                      <input type="text" name="codpadre" id="codpadre" class="cajaPequena SoloLectura" readonly="readonly" value="<?php echo $codpadre; ?>" />
                                      Nombre:
                                      <input type="text" name="nompadre" id="nompadre" class="cajaMedia cajaSoloLectura" readonly="readonly" style="width:215px;"  value="<?php echo $nompadre; ?>" />
                                      <a href="<?=$base_url;?>index.php/almacen/producto/ventana_busqueda_producto/" id="linkVerProducto"><img height='16' width='16' src='<?php echo base_url(); ?>/images/ver.png?=<?=IMG;?>' title='Buscar' border='0' /></a>
                                      <br>
                                </label >
                                 <label style="display:none" id="nota"  >
                                      Serie
                                      <input type="text" name="serie" id="serie" class="cajaPequena " value="<?php echo $serie; ?>" />
                                      Numero:
                                      <input type="text" name="numero" id="numero" class="cajaMedia "  style="width:215px;"  value="<?php echo $numero; ?>" />
                                      <br>
                                </label>
                                
                                </td>
                              </tr>
                              <tr>
                                <td valign="top">Observacion:</td>
                                <td><textarea rows="8" cols="56" class="cajaTextArea"  name="descripcion" id="descripcion"><?php echo $descripcion;?> </textarea> </td>
                              </tr>
                             
                              <tr>
                                <td colspan="2" align="left" valign="top">&nbsp;</td>
                              </tr>
                             
                            </table>
</div>
                <div style="width:100%;"></div>
                    </div>
                   
                    <div id="datosOcompras" style="float:left; display:none;width:100%;"></div>
                </div>
                <div id="divBotones" style="text-align: center; float:left;margin-left: auto;margin-right: auto;width: 98%;margin-top:15px;">
                    <a href="javascript:;" id="imgGuardarRecepcionProveedor"><img src="<?=$base_url;?>public/images/icons/botonaceptar.jpg?=<?=IMG;?>" width="85" height="22" class="imgBoton"></a>
                    <a href="javascript:;" id="imgLimpiarEnvioProveedor"><img src="<?=$base_url;?>public/images/icons/botonlimpiar.jpg?=<?=IMG;?>" width="69" height="22" class="imgBoton"></a>
                    <a href="javascript:;" id="imgCancelarEnvioProveedor"><img src="<?=$base_url;?>public/images/icons/botoncancelar.jpg?=<?=IMG;?>" width="85" height="22" class="imgBoton"></a>
                    <input type="hidden" name="cod" id="cod" value="<?php echo $cod; ?>" >                   
                       <?php echo $oculto; ?>
                </div>
            </div>
        </div>
    </div>
</form>