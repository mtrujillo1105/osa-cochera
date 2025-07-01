<html>

<head>
<link href="<?php echo base_url(); ?>public/css/estilos.css?=<?=CSS;?>" type="text/css"	rel="stylesheet" />
<script type="text/javascript"	src="<?php echo base_url(); ?>public/js/jquery.js?=<?=JS;?>"></script>
         <script type="text/javascript" src="<?php echo base_url(); ?>public/js/jquery.blockUI.js?=<?=JS;?>"></script>

  
<title><?php echo TITULO; ?></title>
<meta charset="UTF-8" />
<script language="javascript">

            $(document).ready(function(){
            	

        $('#plantillaArticulo').click(function(){
            url = "<?php echo base_url(); ?>/images/plantillas/Plantilla-Producto.xlsx";
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
       elem.download = "ProductoErrores.txt";
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



.button {
    background-color: #4CAF50; /* Green */
    border: none;
    color: white;
    padding: 16px 32px;
    text-align: center;
    text-decoration: none;
    display: inline-block;
    font-size: 16px;
    margin: 4px 2px;
    -webkit-transition-duration: 0.4s; /* Safari */
    transition-duration: 0.4s;
    cursor: pointer;
}

.button2 {
    background-color: #82A7C6;
    color: white;
    
}

.button2:hover {
    background-color: #4970A5;
    color: white;
}


/* Progress Bar - Inicio*/

</style>
  <style>
  #progressbar {
    margin-top: 20px;
  }
 
  .progress-label {
    font-weight: bold;
    text-shadow: 1px 1px 0 #fff;
  }
 
  .ui-dialog-titlebar-close {
    display: none;
  }
  </style>


</head>
<body>

	<div align="center">

		<div id="tituloForm" class="header"
			style="width: 95%; padding-top: 0px; background-color: cadetblue;">
			<ul>
				<h3 style="color: white;">CARGA DE ARTICULOS</h3>
			</ul>
		</div>
		<div id="dialog-form" title="Cargar Excel">
							
	         <input  type="hidden" id="carga" value="<?php echo $aca;?>" />
                            
			<form id="frm" method="post"
				action="<?php echo base_url(); ?>index.php/basedatos/basedatos/insertararticulo"
				enctype="multipart/form-data">

				<fieldset style="margin-top:15px;">
					<table>
						<tr>
							<td>Subir Articulo:</td>
							<td><input id="archivo" type="file" name="archivo" value="Subir"  >
							</td>
						</tr>
					</table>
					
				</fieldset>
				
				<div id="frmBus" class="formulario" style="margin-left: 5%;">

					<button class="butt" id="butt">
						<a class="linea"
							href="<?php echo base_url(); ?>index.php/basedatos/basedatos/basedatos_principal"
							target="_parent">Cerrar</a>
					</button>

					<input type="submit" name="button" class="butt" id="button" value="Aceptar" />

					<ul id="plantillaArticulo" class="lista_botones">
						<li id="plantillaArticulo">Descargar Plantilla Articulo</li>
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
	$urlnueva = "images/plantillas/temporal/articulo.xls";
	if (is_uploaded_file ( $tmpEXCEL )) {
		copy ( $tmpEXCEL, $urlnueva );
	}
}
?>


    <script type="text/javascript">
                var base_url;

        $(document).ready(function () {
            base_url = "<?php echo base_url(); ?>";

            $('#cerrar').click(function () {
                parent.$.fancybox.close();
                header('http://localhost/dragotek/index.php/tesoreria/cuentas/listar/1');
            });

            $('#agregarPlantilla').click(function() {
                 $('#frmPlantilla').submit();
            });

            $('#cerrarDetalle').click(function(){
                $('#detalle-plantilla').hide('slow');
                $('#detalle-plantilla .well').html("");
            });

            $('.cerrar-alerta').click(function() {
                 $('#alerta-detalle').html("");
            });

        });

    </script>

</body>
</html>
