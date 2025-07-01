<html>

<head>
<link href="<?php echo base_url(); ?>public/css/estilos.css?=<?=CSS;?>" type="text/css"	rel="stylesheet" />
<script type="text/javascript"	src="<?php echo base_url(); ?>public/js/jquery.js?=<?=JS;?>"></script>	
         <script type="text/javascript" src="<?php echo base_url(); ?>public/js/jquery.blockUI.js?=<?=JS;?>"></script>


<title><?php echo TITULO; ?></title>
<meta charset="UTF-8" />
<script>

$(document).ready(function() { 
	$('#button').click(function() { 
		 var url= "<?php echo base_url(); ?>index.php/basedatos/basedatos/eliminarTablas";
		    if(confirm("Esta Seguro de Eliminar?")){
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
	    	        
		     $.ajax({url: url,type: "POST", success: function(result){
		    	if(result == "0"){
		    		parent.RedireccionIndex();
		    		parent.$.fancybox.close();
		    	
		    	}

	    	 	
		        }
		    });   

		 }else{
				alert("Usted Cancelo la Funcion !");
		 }
			 
  
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
	width: 100%;
	height: 100%;
	float: right;
	 background-image: url('<?php echo base_url(); ?>public/images/icons/salid.png?=<?=IMG;?>');
	
}



.button {
 background-image: url('<?php echo base_url(); ?>public/images/icons/dark.jpg?=<?=IMG;?>');
	background-color: #4CAF50; /* Green */
	border: none;
	color: white;
	padding: 16px 32px;
	text-align: center;
	text-decoration: none;
	display: inline-block;
	font-size: 16px;
	-webkit-transition-duration: 0.4s; /* Safari */
	transition-duration: 0.4s;
	cursor: pointer;
}

.button2 {
	background-color: #82A7C6;
	color: white;
}

.button2:hover {
 background-image: url('<?php echo base_url(); ?>public/images/icons/danger.png?=<?=IMG;?>');
	background-color: red;
	color: white;
}

</style>

</head>
<body>

	<div align="center">
		<div id="dialog-form" title="Destroyer">
						<button name="button" class="butt button button2" id="button">	Limpiar Registros y Transaccionales </button>
						
				<button class="butt" id="butt" style="width: 4%;height: 8%; z-index: 9999999; position: absolute; left: 95%;">
						<a class="linea" href="<?php echo base_url(); ?>index.php/basedatos/basedatos/basedatos_principal" target="_parent"
						style="width: 11px;height: 8px;margin-left: -8px;margin-top: -2px;"></a>
				</button>
					
					

</body>
</html>
