<html>
<head>
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
    </style>
		<script type="text/javascript" src="<?=$base_url;?>public/js/jquery.validate.js?=<?=JS;?>"></script>
		<script type="text/javascript" src="<?=$base_url;?>public/js/maestros/terminal.js?=<?=JS;?>"></script>
		<script type="text/javascript" src="<?=$base_url;?>public/js/funciones.js?=<?=JS;?>"></script>
		<script>

  			function cargar_ubigeo(ubigeo,valor){
  			   $("#cboNacimiento").val(ubigeo);
  			   $("#cboNacimientovalue").val(valor);
  			}
  		</script>
</head>
<body>
<!-- Inicio -->
<div id="VentanaTransparente" style="display:none;">
  <div class="overlay_absolute"></div>
  <div id="cargador" style="z-index:2000">
    <table width="100%" height="100%" border="0" class="fuente8">
		<tr valign="middle">
		     <td> Por Favor Espere    </td>
		     <td><img src="<?=$base_url;?>public/images/icons/cargando.gif?=<?=IMG;?>"  border="0" title="CARGANDO" /><a href="#" id="hider2"></a>	</td>
		</tr>
    </table>
  </div>
</div>
	<div id="pagina">
			<div id="zonaContenido">
				<div align="center">
				<div id="tituloForm" class="header"><?php echo $titulo;?></div>
				
				<form id="frmProyecto" name="frmProyecto" method="post" action="<?php echo $url_action; ?>" >
				
					<div id="datosProyecto" >
                           <table class="fuente8" width="98%" cellspacing="0" cellpadding="6" border="0">
                             <tr>
                                 <td>Nombre de Proyecto  </td>
                                 <td><?php echo $nombreProyecto;?></td>
                             </tr>
                             <tr>
                                 <input type="hidden" name="direccionCodigo" 
                                 		id="direccionCodigo" value="<?php echo $direccionCodigo; ?>" />                                
                             </tr>
                             <tr>
                                 <input type="hidden" name="proyecto" 
                                 		id="proyecto" value="<?php echo $proyecto; ?>" />                                
                             </tr>
                             <tr>
                                <td>Direccion de Proyecto </td>
                                <td><?php echo $descripcionDireccion;?></td>
                             </tr>
							 <tr>
                                 <td>Nombre de Terminal </td>
                                 <td><input name="nombreTerminal" type="text" class="cajaGrande" id="nombreTerminal" maxlength="150" value="<?php echo $nombreTerminal;?>"></td>
                             </tr>
                             <tr>
                                <td>Modelo de Terminal </td>
                                <td><input name="modeloTerminal" type="text" class="cajaGrande" id="modeloTerminal" maxlength="150" value="<?php echo $modeloTerminal;?>"></td>
                             </tr>
                             <tr>
                                <td>Numero de Serie </td>
                                <td><input name="numeroSerie" type="text" class="cajaGrande" id="numeroSerie" maxlength="150" value="<?php echo $numeroSerie;?>"></td>
                             </tr>
                             <tr>
                                <td>Numero de Led </td>
                                <td><input name="numeroLed" type="text" class="cajaGrande" id="numeroLed" maxlength="150" value="<?php echo $numeroLed;?>"></td>
                             </tr>
                              <tr>
                      			<td width="10%">
                        		    <div align="right"><a href="javascript:;" onClick="agregar_terminal_direccion();"><img
                                    		 src="<?php echo base_url(); ?>public/images/icons/botonagregar.jpg?=<?=IMG;?>" border="1" align="absbottom"></a>
                        			</div>
                      			</td>
                      				
                      		 </tr>
                         	</table>
                     </div>
  				
  						<div id="tabs-2">  
  							
    						<div id="datosTerminal">
    							<div id="frmBusqueda" style="height:250px; overflow: auto">
            						<table class="fuente8" width="100%" cellspacing="0" cellpadding="3" border="1" ID="Table1">
                						<tr class="cabeceraTabla">
                    						<td width="2%">
                        						<div align="center">&nbsp;</div>
                    						</td>
                    						<td width="5%">
                        						<div align="center">ITEM</div>
                    						</td>
                    						<td width="5%">
                        						<div align="center">NOMBRE TERMINAL</div>
                    						</td>
                    						<td width="5%">
                        						<div align="center">MODELO TERMINAL</div>
                    						</td>
                    						<td width="5%">
                        						<div align="center">NUMERO SERIE</div>
                    						</td>
                    						<td width="5%">
                        						<div align="center">NUMERO LED</div>
                    						</td>
                    						
                						</tr>
            						</table>
            						<div>
            						<table id="tblDetalleTerminalDireccion" class="fuente8" width="100%" border="1">
            						       <?php
												if (count($detalle_terminal) > 0) {
												  foreach ($detalle_terminal as $indice => $valor) {
												  	$proyecto			= $valor->PROYP_Codigo;
												  	$direccionCodigo	= $valor->DIRECC_Codigo;
												  	$terminalCodigo     = $valor->TERMINAL_Codigo;
												  	$terminalNombre     = $valor->TERMINAL_Nombre;
												  	$terminalModelo     = $valor->TERMINAL_Modelo;
												  	$terminalSerie      = $valor->TERMINAL_Serie;
												  	$terminalLed        = $valor->TERMINAL_NroLed;
												  	
													if (($indice + 1) % 2 == 0) {
														$clase = "itemParTabla";
													} else {
														$clase = "itemImparTabla";
													}
											?>
												<tr>
													<td width="3%">
   														<div align="center">
   															<font color="red">
   																<strong>
   																	<a href="javascript:;" onclick="eliminar_terminal(<?php echo $indice; ?>);">
																		<span style="border:1px solid red;background: #ffffff;">&nbsp;X&nbsp;</span>
																	</a>
																</strong>
															</font>
        												</div>
                                					</td>
													<td width="2%"> <?php  echo $indice; ?></td>
															<input type="hidden" name="proyecto[<?php echo $indice; ?>]"
        												   id="proyecto[<?php echo $indice; ?>]" 
        												   value="<?php echo $proyecto; ?>" />
        												   <input type="hidden" name="direccionCodigo[<?php echo $indice; ?>]"
        												   id="direccionCodigo[<?php echo $indice; ?>]" 
        												   value="<?php echo $direccionCodigo; ?>" />	
        												   <input type="hidden" name="terminalCodigo[<?php echo $indice; ?>]"
        												   id="terminalCodigo[<?php echo $indice; ?>]" 
        												   value="<?php echo $terminalCodigo; ?>" />						
													<td>
    												  <div align="left">
    													<input type="text" class="cajaGeneral"
   										 					   maxlength="250" name="terminalNombre[<?php echo $indice; ?>]"
    									 					   id="terminalNombre[<?php echo $indice; ?>]"
   										 					   value="<?php echo $terminalNombre; ?>"/>
   													  </div>
                                				    </td>
                                				    <td>
    												  <div align="left">
    													<input type="text" class="cajaGeneral"
   										 					   maxlength="250" name="terminalModelo[<?php echo $indice; ?>]"
    									 					   id="terminalModelo[<?php echo $indice; ?>]"
   										 					   value="<?php echo $terminalModelo; ?>"/>
   													  </div>
                                				    </td>
                                				    <td>
    												  <div align="left">
    													<input type="text" class="cajaGeneral"
   										 					   maxlength="250" name="terminalSerie[<?php echo $indice; ?>]"
    									 					   id="terminalSerie[<?php echo $indice; ?>]"
   										 					   value="<?php echo $terminalSerie; ?>"/>
   													  </div>
                                				    </td>
                                				    <td>
    												  <div align="left">
    													<input type="text" class="cajaGeneral"
   										 					   maxlength="250" name="terminalLed[<?php echo $indice; ?>]"
    									 					   id="terminalLed[<?php echo $indice; ?>]"
   										 					   value="<?php echo $terminalLed; ?>"/>
   													  </div>
                                				    </td>
                                				     <input type="hidden" name="teraccion[<?php echo $indice; ?>]"
  															id="teraccion[<?php echo $indice; ?>]" value="m"/>
                                				  
												</tr>
												
												<?php }} ?>            			
                					</table>
                					</div>
    							</div>
  						</div>
					</div>			
                    
                    <div id="nuevoRegistro" style="display:none;float:right;width:150px;height:20px;border:0px solid #000;margin-top:7px;">
                    	<a href="#">Nuevo <image src="<?=$base_url;?>public/images/icons/add.png?=<?=IMG;?>" name="agregarFila" id="agregarFila" border="0" alt="Agregar"></a>
                    </div><br><br>				
      
				<div style="margin-top:20px; text-align: center">
                    <a href="#" id="imgGuardarTerminal"><img src="<?=$base_url;?>public/images/icons/botonaceptar.jpg?=<?=IMG;?>" width="85" height="22" class="imgBoton" onMouseOver="style.cursor=cursor"></a>
					<a href="#" id="imgLimpiarProyecto"><img src="<?=$base_url;?>public/images/icons/botonlimpiar.jpg?=<?=IMG;?>" width="69" height="22" class="imgBoton" onMouseOver="style.cursor=cursor"></a>
					<a href="#" id="imgCancelarProyecto"><img src="<?=$base_url;?>public/images/icons/botoncancelar.jpg?=<?=IMG;?>" width="85" height="22" class="imgBoton" onMouseOver="style.cursor=cursor"></a>
					<input type="hidden" name="base_url" id="base_url" value="<?=$base_url;?>">
                    <input id="modo" name="modo" type="hidden" value="<?php echo $modo;?>">                                       
                    <input type="hidden" name="terminal" id="terminal" value="<?php echo $datos->id; ?>" />
                </div>
			  </form>
		  </div>
		  </div>
		</div>
	</body>
</html>