<html>
<head>
    <script type="text/javascript" src="<?php echo base_url(); ?>public/js/jquery.js?=<?=JS;?>"></script>
        <script type="text/javascript" src="<?php echo base_url(); ?>public/js/tesoreria/movimiento.js?=<?=JS;?>"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>public/js/jquery-ui-1.8.17.custom.min.js?=<?=JS;?>"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>public/js/funciones.js?=<?=JS;?>"></script>
     <script src="<?php echo base_url(); ?>public/js/jquery.columns.min.js?=<?=JS;?>"></script>
     

 <style>
        #mapa{
            width: 900px;
            height: 300px;
            background: green;
        }
        #infor{
            width: 400px;
            height: 400px;
            float:left;
        }
        
        #tablaG {
    		font-family: arial, sans-serif;
    		border-collapse: collapse;
    		width: 100%;
    		border-style: solid;
		}

		td, th {
    		border: 1px solid #dddddd;
    		text-align: left;
    		padding: 8px;
    		border-style: solid;
		}
		
		
	/***ESTILOS PARA EL AUTOCOMPLETE**/	
	.cajaPadding {

        padding: 2px 10px;

    }
    
    .ui-autocomplete {
        padding: 0;
        margin: 0;
        width: 500px;
        list-style: none;
    }

    .ui-autocomplete a {
        color: #000;
        font-family: Arial;
        font-size: 8pt;
        display: block;
        padding: 4px 10px;
    }

    .ui-autocomplete a:hover {
        color: #000;
        font-weight: bold;;

    }

    .ui-state-hover {
        background: black !important;
        color: #FFF !important;
        border: 0px !important;
    }

    </style>
     		<script> 
     		var base_url   = $("#base_url").val(); 
     		
				/***AUTOCOMPLETE DE CLIENTE**/
 			function busqueda_cliente(tipo,pos) {
//  				a="_"+tipo+"_"+pos;
//  				formadepago = document.getElementById(a).value;
 				 
	                $("#clienteDescripcion_"+tipo+"_"+pos).autocomplete({
	                    source: function (request, response) {
	
	                        $.ajax({
	                            url: "<?php echo base_url(); ?>index.php/tesoreria/movimiento/encuentrax_cliente",
	                            type: "POST",
	                            data: {
	                                term: $("#clienteDescripcion_"+tipo+"_"+pos).val(),
	                                codigoInventario:$("#seleccionando_"+tipo+"_"+pos).val()
	                            },
	                            dataType: "json",
	                            success: function (data) {
	                                response(data);
	                            },
	                            error: function (data) {
	                                alert("No contiene cuentas");
	                            }
	                        });
	                    },
	                    select: function (event, ui) {
	                        $("#cliente_"+tipo+"_"+pos).val(ui.item.codigo);
	                        if(formadepago == 2){
	                        	obtenerBancos(ui.item.codigo,tipo,pos);
	                        }else if(formadepago == 1){
// 	        	 				 alert("Forma De Pago Efectivo");
	        	 			}else{
	        	 				alert("Seleccionar Forma De Pago");
	       						$("#seleccionando_"+tipo+"_"+pos).val("");
	       						$("#clienteDescripcion_"+tipo+"_"+pos).val("");
		        	 		}
	                         
	                    },
	                    minLength: 3
	                });
 				
            }



 			
 			  
	 function obtenerBancos(cod,tipo,pos){	
		 $("#tipobancos_"+tipo+"_"+pos).show();
	 var url= "<?php echo base_url(); ?>index.php/tesoreria/movimiento/obtenerBancos";
	
	 $.ajax({url: url,type: "POST",
		 data:{
			 codigo:cod,
			 codigoInventario:$("#seleccionando_"+tipo+"_"+pos).val()
			 },  
		 dataType: "json", 
		 success: function(data){
			 $("#idcmbbancos-"+tipo+"_"+pos).html("");
			 fila = "";
		     fila+="<option value='00' > SELECCIONAR </option>";
		 $.each(data, function (i, item) {
			
	         fila+= "<option value='"+item.codigo+"' >" + item.nombre + "</option>";
	         
	        // obtenerCuentas(item.codigo,item.codigopersona);
	            
		 });
 	    $("#idcmbbancos_"+tipo+"_"+pos).html(fila);
 	     $("#registroMovimiento_"+tipo+"_"+pos).hide();
		  $("#tipocuentas_"+tipo+"_"+pos).hide();
		  
	 }	
	        
	    });   
	}

	 var codigoBanco,nombreBanco,codigoPeronsa;
	 
	 function mostrandoCuentas(codigo,tipo,pos) {
		 var sel1 = document.getElementById("idcmbbancos_"+tipo+"_"+pos);
		 codigoBanco = sel1.options[sel1.selectedIndex].value;
		 
		  var sel2 = document.getElementById("idcmbbancos_"+tipo+"_"+pos);
		  nombreBanco = sel2.options[sel2.selectedIndex].text;
		  
		  codigoPeronsa = document.getElementById("cliente_"+tipo+"_"+pos).value;
// 		 alert(codigoBanco +"		"+ nombreBanco +"		"+codigoPeronsa);
		
			if(codigoBanco == 00){
				$("#registroMovimiento_"+tipo+"_"+pos).hide();
				$("#tipocuentas_"+tipo+"_"+pos).hide();

			}else{
				obtenerCuentas(tipo,pos);		
			}
		 
	}

		

	 function obtenerCuentas(tipo,pos){
		 var url= "<?php echo base_url(); ?>index.php/tesoreria/movimiento/obtenerCuentas";
		 $("#tipocuentas_"+tipo+"_"+pos).show();
		 $.ajax({url: url,type: "POST",
			 data:{
				 codigo:codigoBanco,
				 codipersona:codigoPeronsa,
				 codigoInventario:$("#seleccionando_"+tipo+"_"+pos).val()
				 },
			 dataType: "json",
			 success: function(data){
				// alert(data);
				 $("#idcmbcuentas_"+tipo+"_"+pos).html("");
				 fila = "";
				 fila+="<option value='00' > SELECCIONAR </option>";
				 
				     $.each(data, function (i, item) {
					 fila+= "<option value='"+item.codigo+"'>" + item.nombre + "</option>";		    		 
					// buscarCuentas(item.nombre);
				     });
				     
			    	 $("#idcmbcuentas_"+tipo+"_"+pos).html(fila);
			    	 
			    	 $("#registroMovimiento_"+tipo+"_"+pos).hide();
		     }	
		        
		 });   

	}
	 

	var nombreCuentas ;
	function mostrandoDatosCuentas(codigo,tipo,pos) {
		  var sel2 = document.getElementById("idcmbcuentas_"+tipo+"_"+pos);
		  nombreCuentas = sel2.options[sel2.selectedIndex].text;

		  var sel1 = document.getElementById("idcmbcuentas_"+tipo+"_"+pos);
		  CodigoCuentas = sel1.options[sel1.selectedIndex].value;
			 
		  if(CodigoCuentas == "00"){
			  $("#registroMovimiento_"+tipo+"_"+pos).hide();
			  }
		  
		  
		  //alert(nombreCuentas);
		  buscarCuentas(tipo,pos);
	}
		
		
	 function buscarCuentas(tipo,pos){

		 var url= "<?php echo base_url(); ?>index.php/tesoreria/movimiento/buscarCuentas";
		 
		 $.ajax({url: url,type: "POST",
			 data:{
				 codigo:nombreCuentas
				 },
		   dataType: "json",
		    success: function(data){
			
			 $.each(data, function (i, item) {
				 $("#registroMovimiento_"+tipo+"_"+pos).show();
						fila ="<table>";
						fila+="<tr>";
						fila+="<td>BANCO";
						fila+="<input value='"+item.banco+"' id='txtbanco' disabled='true'> ";
						fila+="</td>";
						fila+="<td>CUENTA";
						fila+="<input value='"+item.cuenta+"' id='txtcuenta' disabled='true' >";
						fila+="</td>";	
						fila+="<td>MONEDA";
						fila+="<input value='"+item.moneda+"' id='txtmoneda' disabled='true' >";
						fila+="</td>";
						fila+="<td>TIPO DE CUENTA";
						fila+="<input value='"+item.tipocuenta+"' id='txtTipoCuenta' disabled='true' >";
						fila+="</td>";
						fila+="</tr>";					
						fila+="</table>";
						
							 $("#registroMovimiento_"+tipo+"_"+pos).html(fila);
		     });
		     }	
		        
		    });   

	 }

	 function canbiodeSeleccion(codigo,tipo,pos){

			a = "registroMovimiento_"+tipo+"_"+pos;
			b = "tipobancos_"+tipo+"_"+pos;
			d = "tipocuentas_"+tipo+"_"+pos;
			c = "clienteDescripcion_"+tipo+"_"+pos;
			document.getElementById(c).value="";

				  $("#"+a).hide();
			      $("#"+b).hide();
				  $("#"+d).hide();

	 }

	 
	function obtenerBancosCajaDiaria(codigo,tipo,pos){

		a = "formapago_"+1+"_"+0;
		b = "formapago_"+tipo+"_"+pos;
		formadepago = document.getElementById(b).value;
		formadepagoa = document.getElementById(a).value;
		
			if(formadepago == 2 || formadepagoa == 2){
				
				cajadiaria = $("#cajadiaria_"+tipo+"_"+pos).val();
				if(cajadiaria == ""){
					 $("#tdbancocaja_"+tipo+"_"+pos).hide();
					$("#tdcuentacaja_"+tipo+"_"+pos).hide();
					 $("#datoscuentacaja_"+tipo+"_"+pos).hide();
				}else{
					$("#tdbancocaja_"+tipo+"_"+pos).show();
				
					 cajadiaria = $("#cajadiaria_"+tipo+"_"+pos).val();
						 
					 var url= "<?php echo base_url(); ?>index.php/tesoreria/movimiento/obtenerBancosCajaDiaria";
					// $("#tipocuentas").show();
					 $.ajax({url: url,type: "POST",
						 data:{
							 codigo:cajadiaria
							 },
						 dataType: "json",
						 success: function(data){
							 $("#cmbbancocaja_2_0").html("");
							 fila = "";
							 fila+="<option value='00' > SELECCIONAR </option>";
							 
							     $.each(data, function (i, item) {					 
								 fila+= "<option value='"+item.bancocodigo+"'>" + item.banconombre + "</option>";		    		 
								// buscarCuentas(item.nombre);
							     });
							     
						    	 $("#cmbbancocaja_2_0").html(fila);
						    	 
						    	// $("#registroMovimiento").hide();
					     }	
					        
					 });   
				}
	
			}
			
		}

	 function obtenerCuentasCajaDiaria(codigo,tipo,pos){

		 cmbbancocaja = $("#cmbbancocaja_"+tipo+"_"+pos).val();

		 
		 $("#tdcuentacaja_"+tipo+"_"+pos).hide();
		 $("#datoscuentacaja_"+tipo+"_"+pos).hide();
		 if(cmbbancocaja == 00){
			 $("#tdcuentacaja_"+tipo+"_"+pos).hide();
			 $("#datoscuentacaja_"+tipo+"_"+pos).hide();
		 }else{
				$("#tdcuentacaja_2_0").show();
				 
			 cajadiaria = $("#cajadiaria_"+tipo+"_"+pos).val();
			 bancocaja = $("#cmbbancocaja_"+tipo+"_"+pos).val();

			 var url= "<?php echo base_url(); ?>index.php/tesoreria/movimiento/obtenerCuentasCajaDiaria";
			// $("#tipocuentas").show();
			 $.ajax({url: url,type: "POST",
				 data:{
					 codigocaja:cajadiaria,
					 codigobanco:bancocaja
					 },
				 dataType: "json",
				 success: function(data){
					 $("#idcuentacaja_2_0").html("");
					 fila = "";
					 fila+="<option value='00' > SELECCIONAR </option>";
					 
					     $.each(data, function (i, item) {					 
						 fila+= "<option value='"+item.codigo+"'>" + item.numerocaja + "</option>";		    		 
					     });
					     
				    	 $("#idcuentacaja_2_0").html(fila);
				    	 
				    	// $("#registroMovimiento").hide();
			     }	
			        
			 });   
		 }
	}

		function obtenerDatosCajaDiaria(codigo,tipo,pos){

			cuentacaja = $("#idcuentacaja_2_0").val();
			if(cuentacaja == 00){
				 $("#datoscuentacaja_"+tipo+"_"+pos).hide();
				}else{
					$("#datoscuentacaja_"+tipo+"_"+pos).show();
					
			var idcuentacaja = document.getElementById("idcuentacaja_2_0");
			  nombreCuentascaja = idcuentacaja.options[idcuentacaja.selectedIndex].text;
			  
			 var url= "<?php echo base_url(); ?>index.php/tesoreria/movimiento/obtenerDatosCajaDiaria";
			// $("#tipocuentas").show();
			 $.ajax({url: url,type: "POST",
				 data:{
					 nombrecuentacaja:nombreCuentascaja
					 },
				 dataType: "json",
				 success: function(data){
					 $("#datoscuentacaja_2_0").html("");
					 fila = "";					 
					     $.each(data, function (i, item) {					 
					    	 	fila+="<table>";
								fila+="<tr>";
								fila+="<td>BANCO";
								fila+="<input value='"+item.banconombre+"' id='txtbancocaja' disabled='true'>";
								fila+="</td>";
								fila+="<td>CUENTA";
								fila+="<input value='"+item.numeroempresa+"' id='txtcuentacaja' disabled='true'>";
								fila+="</td>";	
								fila+="<td>TIPO  CUENTA";
								fila+="<input value='"+item.tipocuenta+"' id='txtmonedacaja' disabled='true'>";
								fila+="</td>";
								fila+="<td>MONEDA";
								fila+="<input value='"+item.monedadescripcion+"' id='txtTipoCuentacaja' disabled='true'>";
								fila+="</td>";
								fila+="</tr>";					
								fila+="</table>";		    		 
					     });
					     
				    	 $("#datoscuentacaja_2_0").html(fila);
				    	 
				    	// $("#registroMovimiento").hide();
			     }	
			        
			 });   

		}
			}


		 function tipoFormaDePago(codigo,tipo,pos){
			 
				/**tipo-1:girador,2:beneficiario**/
				a="formapago_"+tipo+"_"+pos;
				formadepago = document.getElementById(a).value;
				
				

			if(formadepago == 1 || formadepago==2){
				$("#botonGeneral").show();
				$("#justificacionobservacion").show();
				
				if(tipo == 1){
					
					b="seleccionando_"+tipo+"_"+pos;
					c="clienteDescripcion_"+tipo+"_"+pos;
					d="tipobancos_"+tipo+"_"+pos;
					e="tipocuentas_"+tipo+"_"+pos;
					f="registroMovimiento_"+tipo+"_"+pos;
					document.getElementById(b).value="";
					document.getElementById(c).value="";
					$("#"+d).hide();
					$("#"+e).hide();
					$("#"+f).hide();
	
					
					g="tdbancocaja_2_0";
					h="tdcuentacaja_"+2+"_"+0;
					i="cajadiaria_"+2+"_"+0;
					j="datoscuentacaja_"+2+"_"+0;
				//	alert(i);
					$("#"+g).hide();
					$("#"+h).hide();
					$("#"+j).hide();
					document.getElementById(i).value="";
	
				
				}
	
				if(tipo == 2){
					a="seleccionando_1_0";
					b="clienteDescripcion_1_0";
					c="tipobancos_1_0";
					d="tipocuentas_1_0";
					e="registroMovimiento_1_0";
									
					document.getElementById(a).value="";
					document.getElementById(b).value="";
					$("#"+c).hide();
					$("#"+d).hide();
					$("#"+e).hide();
	 
					
					i="cajadiaria_2_0";
					j="tdbancocaja_2_0";
					k="tdcuentacaja_2_0";
					l="datoscuentacaja_2_0";
					
					document.getElementById(i).value="";
					$("#"+j).hide();
					$("#"+k).hide();
					$("#"+l).hide();
					
	
					}
			
			}else{
				alert("Seleccionar Forma de Pago");
				$("#botonGeneral").hide();
				$("#justificacionobservacion").hide();
				
				}
		 }
	 
  		</script>

</head>
<body>
<!-- Inicio -->

	<div id="pagina">
			<div id="zonaContenido">
				<div align="center">
				<div id="tituloForm" class="header"><?php echo $titulo;?></div>
				<div id="frmBusqueda">
				<form id="frmcajamovimiento" name="frmcajamovimiento" method="post" action='<?php echo $url_action; ?>'>
				 <div id="datosResponsable" >
				
				<input type="text" id="movimientoDinero" name="movimientoDinero" value="<?php echo $movimientoDinero ?>" hidden />		
   	 			<input type="text" id="codigoCajaSeleccion" name="codigoCajaSeleccion" value="<?php echo $codigoCajaSeleccion; ?>" hidden>
   	 	
<!--    	 						/*****   INGRESO DE DINERO    *****/ -->
				                <?php if($movimientoDinero == 1 ) { ?>
   	 									<!--  		 /** GIRADOR ***/ -->
   	 						
   	 	 <fieldset >
   	 		 <legend style="font-size:20px;">GIRADOR</legend>
   	 	 			 <table style="float: left;">
	   	 	
	   	 	<tr>
	   	 		<td width="25%">Forma De Pago: 
	   	 			<select id='formapago_1_0' name='formapago_1[]' onchange="tipoFormaDePago(this,1,0)" >
	   	 			<option value="0"> Seleccionar </option>
	   	 			<option <?php if ($cmbformapago == '1') echo 'selected="selected"'; ?> value="1">Efectivo</option>
	   	 			<option <?php if ($cmbformapago == '2') echo 'selected="selected"'; ?> value="2">Deposito</option>
						
	   	 			</select></td>
				<td width="25%">Tipo De Moneda:<select id='tipomoneda_1_0' name='tipomoneda_1[]'> <?php echo $cbomonedamovmiento ;?>  </select></td>
	   	 		<td width="25%">Monto :<input type='text' id='monto_1_0' name="monto_1[]" value="<?php echo $monto; ?> " ></td>    
	   	 	</tr>
	   	 	<tr>
	   	 		<td width="25%">Cuenta Contable: <select id='cuentacontable_1_0' name='cuentacontable_1[]'> <?php echo $cboCuentaContable ; ?>  </select></td>
	   	 		<td width="25%">Fecha de Ingreso: <input type='date' id='txtfechaingreso_1_0' name="txtfechaingreso_1[]" value="<?php echo $fechaingreso; ?> " > </td>
	   	 		
	   	 		
	   	 	</tr>
	   	 	<tr>
	   	 	<td colspan="4"><hr size="3px" color="black" /></td>
	   	 	</tr>
	   	 	
	   	 	
   	 	<tr>
	   	 		<td>Girador <select id="seleccionando_1_0" name="seleccionando_1[]" onchange="canbiodeSeleccion(this,1,0)"  >
	   	 		<option <?php if ($estado == '00') echo 'selected="selected"'; ?> value="00">::Seleccione::</option>
	   	 		<option <?php if ($estado == '10') echo 'selected="selected"'; ?> value="10">Caja</option>
	   	 		<option <?php if ($estado == '20') echo 'selected="selected"'; ?> value="20">Colaboradores</option>
	   	 		<option <?php if ($estado == '30') echo 'selected="selected"'; ?> value="30">Proveedor</option>
	   	 		<option <?php if ($estado == '40') echo 'selected="selected"'; ?> value="40">Cliente</option>
	   	 		</select></td>
	
	   	 		<td>INGRESAR NOMBRE
	<!-- 				<input type="hidden" name="cod_cliente" id="cod_cliente" value="" > -->
					<input type="hidden" name="cliente_1[]" id="cliente_1_0" >
	   	 			<input type="text"  name="clienteDescripcion_1[]" 
	                                       onfinishinput="busqueda_cliente(1,0);"
	                                       value="<?php echo $nombrecliente; ?> " id="clienteDescripcion_1_0"
	                                       class="cajaGrande cajaPadding cajaBusquedaGrande">	
	                                
	   	 			</td>
	   	 			<td id="tipobancos_1_0" <?php echo $hidden ?>>Bancos
	   	 			 <select class='comboMedio' id="idcmbbancos_1_0" name="idcmbbancos_1[]"   onchange="mostrandoCuentas(this,1,0)" >
	   	 			 <?php echo $bancosmovmientos ?>
	             	 </select>
		            </td>
		            
	             <td id="tipocuentas_1_0" <?php echo $hidden ?>>N° De Cuentas
	             <select class='comboMedio' id="idcmbcuentas_1_0" name="idcmbcuentas_1[]" onchange="mostrandoDatosCuentas(this,1,0)" >
	             <?php echo $cuentamoviminetos ?>
	             </select>
	             </td>
	   	 		</tr>
	   	 	</table>
	   	 	<div id="registroMovimiento_1_0" >
   	 	    </div>
 		 </fieldset>
 		 
 		 
 		 
								<!--  		 /** BENEFICIARIO ***/ -->
 		 <fieldset>
		 	 <legend style="font-size:20px;">BENEFICIARIO</legend>
	   	 				 <table style="float: left;">
<!-- 	   	 				 /***NO USO ESAS FUNCIONES***/ -->
	   	 				 <tr>
	   	 		<td width="25%" hidden>Forma De Pago: 
	   	 			<select id='formapago_2_0' name='formapago_2[]' onchange="tipoFormaDePago(this,2,0)" >
	   	 			<option value="0"> Seleccionar </option>
	   	 				<option value="1"> Efectivo </option>
	   	 				<option value="2"> Deposito </option>							
	   	 			</select></td>
	   	 			
				<td width="25%" hidden>Tipo De Moneda: <select id='tipomoneda_2_0' name='tipomoneda_2[]'> <?php echo $cbomonedamovmiento ?>  </select></td>
	   	 		<td width="25%" hidden>Monto :<input type='text' id='monto_2_0' name="monto_2[]"></td>
	   	 	</tr>
	   	 	<tr>
	   	 		<td width="25%" hidden>Cuenta Contable: <select id='cuentacontable_2_0' name='cuentacontable_2[]'> <?php echo $cboCuentaContable ?>  </select></td>
	   	 		<td width="25%" hidden>Fecha de Ingreso: <input type='date' id='txtfechaingreso_2_0' name="txtfechaingreso_2[]"> </td>
	   	 	</tr>
<!-- 	   	 	<tr> -->
<!-- 	   	 	<td colspan="3"><hr size="3px" color="black" /></td> -->
<!-- 	   	 	</tr> -->
	   	 <!-- 	   	 				 /***NO USO ESAS FUNCIONES***/ -->
	   	 			<tr>
	   	 				<td width="25%">Caja : <select id='cajadiaria_2_0' name='cajadiaria_2[]' onchange="obtenerBancosCajaDiaria(this,2,0)"  ><?php echo $cboResponsable; ?></select></td>
	   	 				<td width="25%" id="tdbancocaja_2_0"   <?php echo $hidden ?>>Bancos : <select id='cmbbancocaja_2_0' name='cmbbancocaja_2[]' onchange="obtenerCuentasCajaDiaria(this,2,0)" > <?php echo $cboBancoCaja ?></select></td>
	   	 				<td width="25%" id="tdcuentacaja_2_0" <?php echo $hidden ?> >Cuenta Caja: <select id='idcuentacaja_2_0' name='idcuentacaja_2[]'  onchange="obtenerDatosCajaDiaria(this,2,0)" > <?php echo $cboCuentaCaja ?></select></td>
	   	 			</tr>
	   	 		</table>
	   	 		<div id="datoscuentacaja_2_0"  <?php echo $hidden ?>></div>
 		 </fieldset>
 
 			  <!--    	 						/*****   SALIDA DE DINERO ==== 2   *****/ -->
   	 	
		  <?php }else{ ?>
		     	 									<!--  		 /** GIRADOR ***/ -->
		  
		   <fieldset>
		 	 <legend style="font-size:20px;">GIRADOR</legend>
	   	 				 <table style="float: left;">
	   	 	<tr hidden>
	   	 		<td width="25%">Forma De Pago: 
	   	 			<select id='formapago_1_0' name='formapago_1[]' onchange="tipoFormaDePago(this,1,0)" >
	   	 			<option value="0"> Seleccionar </option>
	   	 				<option value="1"> Efectivo </option>
	   	 				<option value="2"> Deposito </option>							
	   	 			</select></td>
				<td width="25%">Tipo De Moneda: <select id='tipomoneda_1_0' name='tipomoneda_1[]'> <?php echo $cbomonedamovmiento ?>  </select></td>
	   	 		<td width="25%">Monto :<input type='text' id='monto_1_0' name="monto_1[]"	 ></td>
	   	 	</tr>
	   	 	<tr hidden>
	   	 		<td width="25%">Cuenta Contable: <select id='cuentacontable_1_0' name='cuentacontable_1[]'> <?php echo $cboCuentaContable ?>  </select></td>
	   	 		<td width="25%">Fecha de Ingreso: <input type='date' id='txtfechaingreso_1_0' name="txtfechaingreso_1[]"> </td>
	   	 	</tr>
<!-- 	   	 	<tr> -->
<!-- 	   	 	<td colspan="3"><hr size="3px" color="black" /></td> -->
<!-- 	   	 	</tr> -->
	   	 	
	   	 	<tr>
	   	 		<td width="25%" >Forma De Pago: 
	   	 			<select id='formapago_2_0' name='formapago_2[]' onchange="tipoFormaDePago(this,2,0)" >
	   	 			<option value="0"> Seleccionar </option>
	   	 				<option value="1"> Efectivo </option>
	   	 				<option value="2"> Deposito </option>							
	   	 			</select></td>
	   	 			
				<td width="25%" >Tipo De Moneda: <select id='tipomoneda_2_0' name='tipomoneda_2[]'> <?php echo $cbomonedamovmiento ?>  </select></td>
	   	 		<td width="25%" >Monto :<input type='text' id='monto_2_0' name="monto_2[]"></td>
	   	 	</tr>
	   	 	<tr>
	   	 		<td width="25%" >Cuenta Contable: <select id='cuentacontable_2_0' name='cuentacontable_2[]'> <?php echo $cboCuentaContable ?>  </select></td>
	   	 		<td width="25%" >Fecha de Ingreso: <input type='date' id='txtfechaingreso_2_0' name="txtfechaingreso_2[]"> </td>
	   	 	</tr>
	   	 	<tr>
	   	 	<td colspan="4"><hr size="3px" color="black" /></td>
	   	 	</tr>
	   	 			<tr>
	   	 				<td width="25%">Caja : <select id='cajadiaria_2_0' name='cajadiaria_2[]' onchange="obtenerBancosCajaDiaria(this,2,0)"  > <?php echo $cboResponsable ?>  </select></td>
	   	 				<td width="25%" id="tdbancocaja_2_0" hidden>Bancos : <select id='cmbbancocaja_2_0' name='cmbbancocaja_2[]' onchange="obtenerCuentasCajaDiaria(this,2,0)" > </select></td>
	   	 				<td width="25%" id="tdcuentacaja_2_0" hidden>Cuenta Caja: <select id='idcuentacaja_2_0' name='idcuentacaja_2[]'  onchange="obtenerDatosCajaDiaria(this,2,0)" > </select></td>
	   	 			</tr>
	   	 		</table>
	   	 		<div id="datoscuentacaja_2_0" hidden></div>
 		 </fieldset>
 		 
        
 		 
 		 
								<!--  		 /** BENEFICIARIO ***/ -->
 		
                           	 <fieldset>
   	 		 <legend style="font-size:20px;">BENEFICIARIO</legend>
   	 	 			 <table style="float: left;">
	   	 	
	   	 
	   	 	
	   	 	
   	 	<tr>
	   	 		<td>Beneficiario <select id="seleccionando_1_0" name="seleccionando_1[]" onchange="canbiodeSeleccion(this,1,0)" >
	   	 		<option value="00">::Seleccione::</option>
	   	 		<option value="10">Caja</option>
	   	 		<option value="20">Colaboradores</option>
	   	 		<option value="30">Proveedor</option>
	   	 		<option value="40">Cliente</option>
	   	 		</select></td>
	
	   	 		<td>INGRESAR NOMBRE
	<!-- 				<input type="hidden" name="cod_cliente" id="cod_cliente" value="" > -->
					<input type="hidden" name="cliente_1[]" id="cliente_1_0" >
	   	 			<input type="text"  name="clienteDescripcion_1[]" 
	                                       onfinishinput="busqueda_cliente(1,0);"
	                                       value="" id="clienteDescripcion_1_0"
	                                       class="cajaGrande cajaPadding cajaBusquedaGrande">	
	                                
	   	 			</td>
	   	 			<td id="tipobancos_1_0" hidden>Bancos
	   	 			 <select class='comboMedio' id="idcmbbancos_1_0" name="idcmbbancos_1[]"   onchange="mostrandoCuentas(this,1,0)" >
	             	 </select>
		            </td>
		            
	             <td id="tipocuentas_1_0" hidden>N° De Cuentas
	             <select class='comboMedio' id="idcmbcuentas_1_0" name="idcmbcuentas_1[]" onchange="mostrandoDatosCuentas(this,1,0)" >
	             </select>
	             </td>
	   	 		</tr>
	   	 	</table>
	   	 	<div id="registroMovimiento_1_0" hidden>
   	 	    </div>
 		 </fieldset>
 		 
                        <?php }  ?>
                         <table id="justificacionobservacion" >
					<tr>
						<td>Justificacion
							<textarea rows='4' cols='50' id='txtjustificacion' name="txtjustificacion" ><?php echo $txtjustificacion ?></textarea>
						</td>
						<td>Observacion
							<textarea rows='4' cols='50' id='txtobservacion' name="txtobservacion" ><?php echo $txtobservacion ?></textarea>
						</td>
					</tr>
		      </table>
                        </div>   
                        <div style="margin-top:8%; text-align: center" id="botonGeneral" >
                    <a href="#" id="GuardarCajaMovimiento" onclick="insertar_cajaMovimiento()"><img src="<?=$base_url;?>public/images/icons/botonaceptar.jpg?=<?=IMG;?>" width="85" height="22" class="imgBoton" onMouseOver="style.cursor=cursor"></a>
					<a href="#" id="LimpiarCajaMovimiento"><img src="<?=$base_url;?>public/images/icons/botonlimpiar.jpg?=<?=IMG;?>" width="69" height="22" class="imgBoton" onMouseOver="style.cursor=cursor"></a>
					<a href="#" id="CancelarCajaMovimiento"><img src="<?=$base_url;?>public/images/icons/botoncancelar.jpg?=<?=IMG;?>" width="85" height="22" class="imgBoton" onMouseOver="style.cursor=cursor"></a>
                </div>
        </form>
		  </div>
		  </div>
		</div>
	</div>
	</body>
</html>
