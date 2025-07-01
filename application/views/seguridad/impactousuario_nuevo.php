	
<script type="text/javascript" src="<?=$base_url;?>public/js/seguridad/impactousuario.js?=<?=JS;?>"></script>
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

</script>
<br>
<form id="frmEntregacliente" name="frmEntregacliente" method="post" enctype="multipart/form-data" action="<?php echo $url_action;?>">
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
                                <td valign="top">USUARIO:</td>
                                <td><input type ="text" class="cajaMedia"  name="usuario" id="usuario" value ="<?php echo $usuario;?>"/> </td>
                              </tr>
                             
                              <tr>
                                  <td valign="top">PASSWORD:</td>
                                <td><input type ="password" class="cajaMedia"  name="password" id="password"><?php echo $password;?> </td>
                                
                              </tr>
                             
                            </table>
</div>
                <div style="width:100%;"></div>
                    </div>
                   
                    <div id="datosOcompras" style="float:left; display:none;width:100%;"></div>
                </div>
                <div id="divBotones" style="text-align: center; float:left;margin-left: auto;margin-right: auto;width: 98%;margin-top:15px;">
                    <a href="javascript:;" id="guardarRegistro"><img src="<?=$base_url;?>public/images/icons/botonaceptar.jpg?=<?=IMG;?>" width="85" height="22" class="imgBoton"></a>
                    <a href="javascript:;" id="limpiarRegistro"><img src="<?=$base_url;?>public/images/icons/botonlimpiar.jpg?=<?=IMG;?>" width="69" height="22" class="imgBoton"></a>
                    <a href="javascript:;" id="CancelarRegistro"><img src="<?=$base_url;?>public/images/icons/botoncancelar.jpg?=<?=IMG;?>" width="85" height="22" class="imgBoton"></a>
<!--                    <input type="hidden" name="password" id="password" value="<?php echo $password; ?>" >                   -->
                       <input type="hidden" name="base_url" id="base_url" value="<?=$base_url;?>">
                </div>
            </div>
        </div>
    </div>
    


</form>
