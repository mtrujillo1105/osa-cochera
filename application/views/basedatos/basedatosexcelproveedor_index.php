<html>

<head>
<link href="<?php echo base_url(); ?>public/css/estilos.css?=<?=CSS;?>" type="text/css"
	rel="stylesheet" />
<script type="text/javascript"
	src="<?php echo base_url(); ?>public/js/jquery.js?=<?=JS;?>"></script>
         <script type="text/javascript" src="<?php echo base_url(); ?>public/js/jquery.blockUI.js?=<?=JS;?>"></script>

<title><?php echo TITULO; ?></title>
<meta charset="UTF-8" />
<script language="javascript">
            $(document).ready(function(){
         

        $('#plantillaproveedorjuridico').click(function(){
        	base_url = $("#base_url").val();
            url = "<?php echo base_url(); ?>/images/plantillas/Proveedor-PersonaJuridica.xlsx";
            window.open(url, '', "width=800,height=600,menubars=no,resizable=no;");
        });


        $('#plantillaproveedornatural').click(function(){
        	base_url = $("#base_url").val();
            url = "<?php echo base_url(); ?>/images/plantillas/Plantilla-ProvedorNatural.xlsx";
            window.open(url, '', "width=800,height=600,menubars=no,resizable=no;");
        });

        document.getElementById("button").disabled = true;
        var fl = document.getElementById('archivo');

        fl.onchange = function(e){ 
            var ext = this.value.match(/\.(.+)$/)[1];
            switch(ext)
            {
//                 case 'jpg':
//                 case 'bmp':
                case 'xls':
                case 'xlsx':
                    break;
                default:
                    alert('ingresar un formato Excel');
                    this.value='';
                    
            }
    	  	 document.getElementById("button").disabled = true;
             if(this.value != ""){
            	 document.getElementById("button").disabled = false;
             }
        };
       

        /**abrir un Formato .txt ***/
        
        var contenidoDeArchivo = document.getElementById('carga').value;

        if(contenidoDeArchivo != "" ){
    	   var div = document.getElementById('butonerror');
    	   div.style.visibility = 'visible';
           }

       var elem = document.getElementById('descargar');
       elem.download = "ProveedorErrores.txt";
       elem.href = "data:application/octet-stream," + encodeURIComponent(contenidoDeArchivo);

       $('#button').click(function() { 
           
        	 $.blockUI({ 
               message:  '<h1><img src="<?php echo base_url(); ?>/images/cargaexcel.gif?=<?=IMG;?>" style="width:100%;height:100%;"></h1>', 
             		
            	 css: { 
                	 top:'0%',
  	            backgroundColor: '#000', 
  	            '-webkit-border-radius': '10px', 
  	            '-moz-border-radius': '10px', 
  	            opacity: .5, 
  	            color: '#fff' 
  	        } }); 
  	 
  	        setTimeout($.unblockUI, 90000); 

        });   
    });

        </script>

<style>
.formulario {
	margin-top: 20px;
}

.linea:hover {
	color: black;
}

.linea {
	margin: 0px;
	width: 100%;
	height: 100%;
	display: block;
	padding: 5px;
}

.butt {
	width: 140px;
	height: 30px;
	float: right;
	margin-right: 30px;
	padding: 0px;
}

table {
	border-collapse: separate;
}
</style>



</head>
<body>


	<div align="center">
		<div id="tituloForm" class="header"
			style="width: 95%; padding-top: 0px; background-color: cadetblue;">
			<ul>
				<h3 style="color: white;">CARGA DE PROVEEDOR</h3>
			</ul>
		</div>

		<div id="dialog-form" title="Cargar Excel">
 <input  type="hidden" id="carga" value="<?php echo $aca;?>" />
			<form id="frm" method="post"
				action="<?php echo base_url(); ?>index.php/basedatos/basedatos/insertarproveedor"
				enctype="multipart/form-data">


				<fieldset>
					<table>
						<tr>
							<td>Proveedor :</td>
							<td><select class="select2" name="select" autofocus>
									<option>JURIDICA</option>
									<option>NATURAL</option>
							</select></td>
						</tr>
						<tr>
							<td>Subir Proveedor:</td>
							<td><input id="archivo" type="file" name="archivo" value="Subir">
							</td>
						</tr>
					</table>



				</fieldset>
				<div id="frmBus" class="formulario" style="margin-left: 5%;">

					<button class="butt">
						<a class="linea"
							href="<?php echo base_url(); ?>/index.php/basedatos/basedatos/basedatos_principal"
							target="_parent">Cerrar</a>
					</button>

					<input type="submit" name="button" class="butt" id="button"
						value="Aceptar">
					<ul id="plantillaproveedorjuridico" class="lista_botones">
						<li id="plantillaproveedorjuridico">Descargar Plantilla Persona Juridica</li>
					</ul>
					<ul id="plantillaproveedornatural" class="lista_botones">
						<li id="plantillaproveedornatural">Descargar Plantilla Persona Natural</li>
					</ul>
					
					<button id="butonerror" name="butonerror" style="visibility:hidden;
						 z-index: 0; position: absolute;  left: 8px;  bottom: 2px;  width: 27%;  height :21px; top:165px;" >
							<a id="descargar" style="color:blue;" >Descargar De Errores</a>
						</button>
				</div>

			</form>
		</div>
<?php
error_reporting ( 0 );
if (isset ( $_POST ['button'] )) {
	// subir la imagen del articulo
	$nameEXCEL = $_FILES ['archivo'] ['name'];
	$tmpEXCEL = $_FILES ['archivo'] ['tmp_name'];
	$extEXCEL = pathinfo ( $nameEXCEL );
	$urlnueva = "images/plantillas/temporal/proveedorjuridico.xls";
	if (is_uploaded_file ( $tmpEXCEL )) {
		copy ( $tmpEXCEL, $urlnueva );
	}
}
?>



</body>
</html>
