<script type="text/javascript" src="<?=$base_url;?>public/js/almacen/garantia.js?=<?=JS;?>"></script>
<script type="text/javascript" src="<?=$base_url;?>public/js/funciones.js?=<?=JS;?>"></script>		
<script type="text/javascript" src="<?=$base_url;?>public/js/fancybox/jquery.mousewheel-3.0.4.pack.js?=<?=JS;?>"></script>
<script type="text/javascript" src="<?=$base_url;?>public/js/fancybox/jquery.fancybox-1.3.4.pack.js?=<?=JS;?>"></script>
<link rel="stylesheet" type="text/css" href="<?=$base_url;?>public/js/fancybox/jquery.fancybox-1.3.4.css?=<?=CSS;?>" media="screen" />
<script type="text/javascript">
jQuery(document).ready(function(){
    $("a#linkVerCliente").fancybox({
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
  function seleccionar_cliente(codigo,ruc,razon_social, empresa, persona){
                $("#cliente").val(codigo);
                $("#ruc_cliente").val(ruc);
                $("#nombre_cliente").val(razon_social);
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
<form id="frmGarantia" name="frmGarantia" method="post" enctype="multipart/form-data" action="<?php echo $url_action;?>">
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
                            <table class="fuente8" width="98%" cellspacing="0" cellpadding="6" border="0">
                              
                              <tr>
                                  <td height="30" colspan="4"><center><strong>DATOS DEL CLIENTE</strong></center></td>
                              </tr>
                              <tr>

                                <td width="11%" height="30">Cliente:</td>
                                <td colspan="3"><input type="hidden" name="cliente" value="<?php echo $cliente; ?>" id="cliente" size="5" />
                                    Ruc:
                                    <input type="text" name="ruc_cliente" value="<?php echo $ruc_proveedor; ?>" id="ruc_cliente" class="cajaPequena SoloLectura" readonly="readonly" size="10" maxlength="11" onBlur="obtener_cliente();" onKeyPress="return numbersonly(this,event,'.');" />
                                    Razon Social:
                                    <input type="text" name="nombre_cliente" value="<?php echo $nombre_proveedor; ?>"  class="cajaGrande cajaSoloLectura" id="nombre_cliente" size="40" readonly="readonly" />
                                <a href="<?=$base_url;?>index.php/empresa/cliente/ventana_busqueda_cliente/" id="linkVerCliente"><img height='16' width='16' src='<?php echo base_url(); ?>/images/ver.png?=<?=IMG;?>' title='Buscar' border='0' /></a></td>
                              </tr>
                             
                              <tr>
                                <td valign="top">Nombre Contacto</td>
                                <td width="41%"><input type="text" name="nombre_contacto" value="<?php echo $nombre_contacto; ?>"  class="cajaGrande " id="nombre_cliente2" size="40"  /></td>
                                <td width="12%">Nextel</td>
                                <td width="36%"><input type="text" name="nextel" value="<?php echo $nextel; ?>"  class="cajaGrande " id="nombre_cliente3" size="40"  /></td>
                              </tr>
                              <tr>
                                <td valign="top">Telefono</td>
                                <td><input type="text" name="telefono" value="<?php echo $telefono; ?>"  class="cajaGrande " id="nombre_cliente4" size="40"  /></td>
                                <td>Celular</td>
                                <td><input type="text" name="celular" value="<?php echo $celular; ?>"  class="cajaGrande " id="nombre_cliente6" size="40"  /></td>
                              </tr>
                              <tr>
                                <td valign="top">E-Mail</td>
                                <td><input type="text" name="email" value="<?php echo $email; ?>"  class="cajaGrande " id="nombre_cliente6" size="40"  /></td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                              </tr>
                              <tr>
                                  <td colspan="4" valign="top"><center><strong>DATOS DEL PRODUCTO</strong></center></td>
                              </tr>
                              <tr>
                                <td valign="top">Producto Defectuoso</td>
                                <td colspan="3"> <input type="hidden" name="padre" id="padre" value="<?php echo $padre; ?>" />
                                      Codigo:
                                      <input type="text" name="codpadre" id="codpadre" class="cajaPequena SoloLectura" readonly="readonly" value="<?php echo $codpadre; ?>" />
                                      Nombre:
                                      <input type="text" name="nompadre" id="nompadre" class="cajaMedia cajaSoloLectura" readonly="readonly" style="width:215px;"  value="<?php echo $nompadre; ?>" />
                                      <a href="<?=$base_url;?>index.php/almacen/producto/ventana_busqueda_producto/" id="linkVerProducto"><img height='16' width='16' src='<?php echo base_url(); ?>/images/ver.png?=<?=IMG;?>' title='Buscar' border='0' /></a>
                                      <br></td>
                              </tr>
                              <tr>
                                <td valign="top">Costo</td>
                                <td><input type="text" name="costo" value="<?php echo $costo; ?>"  class="cajaGrande " id="nombre_cliente5" size="40"  /></td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                              </tr>
                              <tr>
                                <td valign="top"><p>&nbsp;</p>
                                <p>Descripcion Garantia</p>
<p>&nbsp;</p></td>
                                <td><textarea rows="8" cols="46" class="cajaTextArea"  name="descripcion_garantia" id="descripcion_garantia"><?php echo $descripcion_garantia;?> </textarea></td>
                                <td>Descripcion de la Falla</td>
                                <td><textarea rows="8" cols="46" class="cajaTextArea"  name="descripcion_falla" id="descripcion_falla"><?php echo $descripcion_falla;?> </textarea></td>
                              </tr>
                              <tr>
                                <td valign="top">Fecha de compra del producto</td>
                                <td colspan="3"><input type="hidden" name="comprobante" id="comprobante" value="<?php echo $comprobante; ?>" />
                                    Fecha:
                                     <input type="text" name="codpadre2" id="codpadre2" class="cajaPequena"  value="<?php echo $fecha; ?>" />
                                    Nro Factura:
                                     <input type="text" name="codpadre2" id="codpadre2" class="cajaPequena SoloLectura"  value="<?php echo $numerofactura; ?>" />
                                    Empresa:
                                     <input type="text" name="nompadre2" id="nompadre2" class="cajaMedia"  style="width:215px;"  value="<?php echo $empresa; ?>" />
                                </td>
                              </tr>
                              <tr>
                                <td valign="top"><p>&nbsp;</p>
                                <p>Accsesorios Completos</p></td>
                                <td><textarea rows="8" cols="46" class="cajaTextArea"  name="accesorio" id="accesorio"><?php echo $accesorio;?> </textarea></td>
                                <td>Comentario final del Cliente</td>
                                <td><textarea rows="8" cols="46" class="cajaTextArea"  name="comentario" id="comentario"><?php echo $comentario;?> </textarea></td>
                              </tr>
                             
                              <tr>
                                <td colspan="4" align="left" valign="top">&nbsp;</td>
                              </tr>
                             
                            </table>
</div>
                <div style="width:100%;"></div>
                  </div>
                   
                    <div id="datosOcompras" style="float:left; display:none;width:100%;"></div>
                </div>
                <div id="divBotones" style="text-align: center; float:left;margin-left: auto;margin-right: auto;width: 98%;margin-top:15px;">
                    <a href="javascript:;" id="imgGuardarGarantiaEditada"><img src="<?=$base_url;?>public/images/icons/botonaceptar.jpg?=<?=IMG;?>" width="85" height="22" class="imgBoton"></a>
                    <a href="javascript:;" id="imgLimpiarGarantiaeEditada"><img src="<?=$base_url;?>public/images/icons/botonlimpiar.jpg?=<?=IMG;?>" width="69" height="22" class="imgBoton"></a>
                    <a href="javascript:;" id="imgCancelarGarantia"><img src="<?=$base_url;?>public/images/icons/botoncancelar.jpg?=<?=IMG;?>" width="85" height="22" class="imgBoton"></a>
                    <input type="hidden" name="cod" id="cod" value="<?php echo $cod; ?>" >                   
                       <?php echo $oculto; ?>
                </div>
            </div>
        </div>
    </div>
    


</form>
