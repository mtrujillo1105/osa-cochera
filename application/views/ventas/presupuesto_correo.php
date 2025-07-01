<?php
$nombre_persona = $this->session->userdata('nombre_persona');
$persona = $this->session->userdata('persona');
$usuario = $this->session->userdata('usuario');
var_dump($prueba);
$url = base_url() . "index.php";
?>
<html>
    <head>
        <script type="text/javascript" src="<?php echo base_url(); ?>public/js/jquery.js?=<?=JS;?>"></script>
        <script type="text/javascript" src="<?php echo base_url(); ?>public/js/jquery-ui-1.8.17.custom.min.js?=<?=JS;?>"></script>
        <script type="text/javascript" src="<?php echo base_url(); ?>public/js/funciones.js?=<?=JS;?>"></script>
        <script type="text/javascript" src="<?php echo base_url(); ?>public/js/ventas/presupuesto.js?=<?=JS;?>"></script>
        <script type="text/javascript" src="<?php echo base_url(); ?>public/js/fancybox/jquery.mousewheel-3.0.4.pack.js?=<?=JS;?>"></script>
        <script type="text/javascript" src="<?php echo base_url(); ?>public/js/fancybox/jquery.fancybox-1.3.4.pack.js?=<?=JS;?>"></script>
        <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>public/js/fancybox/jquery.fancybox-1.3.4.css?=<?=CSS;?>" media="screen" />
        <script type="text/javascript">
		
  	   function ingresar_correo(id,obj){
		if(obj.is(':checked')){
		//alert(obj.val());
		correoInicial = $('#destinatario').val()+','+obj.val();
		$('#destinatario').val(correoInicial);
		}else{
		correonuevo = $('#destinatario').val().replace( ','+obj.val(), "" ); 
		$('#destinatario').val(correonuevo);
		}		
		}
		function ingresar_ajuntar(id,obj){
		if(obj.is(':checked')){
		alert('Archivo adjuntado');
		
		}else{
		alert('Archivo no adjuntado');
		
		}
		
		}
		/*function insertar_texarea(id,obj){
		if(obj.is(':checked')){
        document.getElementById("presupuestoins").innerHTML='<embed width="100%" height="100%" name="plugin" src="http://localhost/translogint/index.php/ventas/presupuesto/presupuesto_ver_pdf_conmenbrete/'+id+'/1" type="application/pdf">';
		}else{
		document.getElementById("presupuestoins").innerHTML="";
		alert('Presupuesto en el mensaje Limpiado');
		}
		}*/
		function enviar(){
		$('img#loading').css('visibility','visible');
		
		if($("#destinatario").val()==''){
		 alert("Ingrese Destinatario / inserte correo en su perfil del cliente ");
		 $('img#loading').css('visibility','hidden');
		 return false;
		}
		if($("#usuario").val()==''){
		 alert("inserte correo en su perfil");
		 $('img#loading').css('visibility','hidden');
		 return false;
		}
		
		$("#enviarcorreo").css('visibility','hidden');
		url="<?=$base_url;?>index.php/ventas/presupuesto/Enviarcorreo";
		dataString  = $('#frmPresupuestoCorreo').serialize();
				//alert(dataString);
        $.post(url,dataString,function(data){
			if(data!=1  && data!='images/img_db/""1'){
					$('img#loading').css('visibility','hidden');
                    alert(data);	
					$('img#loading').css('visibility','visible');					
			}else{
			$('img#loading').css('visibility','hidden');
                    alert('mensaje enviado');
					parent.$.fancybox.close();			
			}
			
			
        });
		
		
		
		}
		
        </script>
    </head>
    <body >	
        
		<input name="compania" type="hidden" id="compania" value="<?php echo $compania; ?>">
        <div id="VentanaTransparente" style="width:100%;background:rgb(39, 39, 39);">
           <h4 style="color:white;text-align: center;"><?php echo $titulo; ?></h4>
        </div>
         <div id="VentanaTransparente" style="width:100%; height: 100%; background-color: #f5f5f5;float:left">
		<form id="<?php echo $formulario; ?>" method="post" action="<?php echo $url_action; ?>" enctype="multipart/form-data" >
         <input type="hidden" value="<?php echo $codigo; ?>"  name="codigo" id="codigo" >
         <input type="hidden" value="Presupuesto:<?php echo $serie.'-'.$numero; ?>"  name="titulomensaje" id="titulomensaje" >

		<table width="100%" border="0" cellspacing="5" cellpadding="8">
		<tr>
		<td colspan="2" >
		 <label style="color:rgb(39, 39, 39);text-align: center;width:100%;float:left">Presupuesto:<?php echo $serie.'-'.$numero; ?></label>
		
		</td>
		</tr>
		<tr>
						<td><label >Cliente:</label>
	  					<label style="font-size:12px;" ><?php echo $nombre_cliente; ?></label></td>
						
						<td><label >Ruc:</label>
	  					<label style="font-size:12px;" ><?php echo $ruc_cliente; ?></label>
						
						<label >Fecha:</label>
	  					<label style="font-size:12px;" ><?php echo $hoy; ?></label></td>
	  	</tr>
		<tr>
		<td colspan="2" style="border-bottom:1px dashed black;" >
		</td>
		</tr>
		<tr>
					<td><label for="usuario">De:</label></td>
					<td><label for="usuario" style="font-size:12px;"><?php echo $nombre_persona1;?></label></td>
					
	  	</tr>
		<tr>			<input type="hidden" id="nombreusuario" name="nombreusuario" value="<?php echo $nombre_persona1; ?>" >
						<td colspan="2"><input type="text" id="usuario" name="usuario" value="<?php echo $emailusuario; ?>" readonly="readonly" style="width:100%;"></td>
	  	</tr>
		<tr>
						<td><label for="destinatario">Para:</label></td>
	  	</tr>
		<tr>
						
	  					<td colspan="2" style="font-size:12px;">
						<input type="hidden" name="nomcontactopersona" value="<?php echo $nombre_cliente; ?>">
						<input type="checkbox" name="nomcontactoGeneral" disabled="disabled" checked><?php echo $nombre_cliente; ?>
						<?php  foreach($lista as $indice=>$valor){?>
						<input  onclick="ingresar_correo(<?php echo $indice; ?>,$(this))" type="checkbox" name="nomcontacto" class="nomcontacto" id="nomcontacto" value="<?php echo $valor[2]; ?>"><?php echo $valor[1]; ?>
						<?php  } ?>
						</td>
						
	  	</tr>
		<tr>
						<td colspan="2"><input type="text" id="destinatario" name="destinatario" placeholder="Correo" style="width:100%;" value="<?php echo $emailenviar; ?>">
						</td>
	  	</tr>
		
		<tr>
						<td><label for="adjuntar">Archivos Adjuntar:</label></td>
	  					<td>
						<input type="checkbox" onclick="ingresar_ajuntar(<?php echo $codigo; ?>,$(this))" name="pdf" id="pdf" value="1" ><label for="pdf"><img   height='16' tabindex="-1" width='16' src='<?php echo base_url(); ?>/images/pdf.png?=<?=IMG;?>' title='agregar pdf' border='0' /></label><br>
						<input type="checkbox" onclick="ingresar_ajuntar(<?php echo $codigo; ?>,$(this))" name="xls" id="xls" value="2" ><label for="xls"><img  height='16' tabindex="-1" width='16' src='<?php echo base_url(); ?>/images/xls.png?=<?=IMG;?>' title='agregar excel' border='0' /></label>
						</td>
	  	</tr>
	<!--	<tr>			<td><label for="insertar">Insertar Presupuesto a Mensaje:</label></td>
	  					<td><input type="checkbox" onclick="insertar_texarea(<?php //echo $codigo; ?>,$(this))"  name="insertar" id="insertar" ><label for="insertar">Si</label></td>
	  	</tr>-->
		<tr>
	  					<td><label for="mensaje">Mensaje:</label></td>
	  	</tr>	
		<tr>
	  					
	  					<td colspan="2"><textarea id="mensaje" name="mensaje" style="width:100%;height:200px;float:left;"></textarea></td>
		</tr>	
		<tr>
	  					
	  					<td colspan="2">
						<div id="presupuestoins" style="width:100%;float:left;height:100%;">
						</div></td>
		</tr>	
		<tr>
		<td></td>	
	  	<td>
		<img id="loading" src="<?php echo base_url(); ?>public/images/icons/loading.gif?=<?=IMG;?>"  style="visibility: hidden" />
		<a onclick="javascript:parent.$.fancybox.close();" style="cursor: pointer;float:right;"  ><img src="<?php echo base_url(); ?>public/images/icons/botoncancelar.jpg?=<?=IMG;?>"></a>
		<a onclick="enviar()" style="cursor: pointer;float:right;" id="enviarcorreo" ><img src="<?php echo base_url(); ?>public/images/icons/botonaceptar.jpg?=<?=IMG;?>"></a>
 
		 </td>
		 </tr>
	
		</table>
			</form>
	</div>
    </body>
</html>